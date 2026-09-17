<?php

namespace Tests\Feature;

use App\Models\AnalisisNlp;
use App\Models\Aviso;
use App\Models\Carrera;
use App\Models\CasoAtencion;
use App\Models\CicloEscolar;
use App\Models\Dass21Evaluation;
use App\Models\Dass21Question;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Grupo;
use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\Tutor;
use App\Models\User;
use App\Services\AvisoService;
use App\Services\CasoAtencionService;
use App\Services\Dass21EvaluationService;
use Database\Seeders\Dass21Seeder;
use Database\Seeders\InstrumentosBaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class EvolutionReportsNoticesTest extends TestCase
{
    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();
        // Migraciones reales, exclusivamente en una conexión SQLite aislada en memoria.
        config([
            'database.default' => 'dass21_testing',
            'database.connections.dass21_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array',
            'mail.default' => 'array', 'queue.default' => 'sync',
        ]);
        DB::purge('dass21_testing');
        $this->artisan('migrate', ['--database' => 'dass21_testing', '--force' => true])->assertExitCode(0);
        $this->seed([RolesAndPermissionsSeeder::class, Dass21Seeder::class]);
        $this->withoutVite();
    }

    private function account(string $role): User
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole($role);
        $persona = Persona::create([
            'user_id' => $user->id, 'nombre' => 'Persona de prueba', 'apellido_paterno' => 'Sintética',
            'fecha_nacimiento' => '2000-01-01', 'genero' => 'otro',
        ]);
        if ($role === 'tutor') {
            Tutor::create(['persona_id' => $persona->id, 'numero_empleado' => 'T-'.$user->id]);
        }
        if ($role === 'psicologo') {
            Psicologo::create(['persona_id' => $persona->id, 'cedula_profesional' => 'P-'.$user->id]);
        }

        return $user->fresh();
    }

    private function group(?Tutor $tutor = null): Grupo
    {
        $tutor ??= $this->account('tutor')->persona->tutor;
        $carrera = Carrera::create(['nombre' => 'Carrera de prueba', 'estado' => 'activo']);

        return Grupo::create(['nombre' => 'Grupo '.++$this->sequence, 'carrera_id' => $carrera->id, 'tutor_id' => $tutor->id, 'periodo' => 'Prueba', 'estado' => 'activo']);
    }

    private function student(?Grupo $grupo = null): Estudiante
    {
        $user = $this->account('estudiante');

        return Estudiante::create([
            'persona_id' => $user->persona->id, 'matricula' => 'M-'.$user->id,
            'grupo_id' => ($grupo ?? $this->group())->id, 'codigo_anonimo' => 'TEST-'.$user->id, 'estado' => 'activo',
        ]);
    }

    private function answers(int $score = 3): array
    {
        return Dass21Question::pluck('id')->mapWithKeys(fn ($id) => [$id => $score])->all();
    }

    private function submit(Estudiante $student, int $score = 3): Dass21Evaluation
    {
        return app(Dass21EvaluationService::class)->submit($student, $this->answers($score));
    }

    public function test_new_submissions_preserve_assignment_while_historical_linking_does_not_invent_it(): void
    {
        $student = $this->student();
        $group = $student->grupo;
        $detail = $this->submit($student, 0);
        $evaluation = $detail->evaluacion->fresh();
        $this->assertEquals($group->id, $evaluation->grupo_aplicacion_id);
        $this->assertEquals($group->nombre, $evaluation->grupo_aplicacion_nombre);
        $student->update(['grupo_id' => $this->group()->id]);
        $this->assertEquals($group->id, $evaluation->fresh()->grupo_aplicacion_id);
        $detail->update(['evaluacion_id' => null]);
        $historical = app(Dass21EvaluationService::class)->link($detail);
        $this->assertNull($historical->contexto_registrado_at);
        $student->update(['grupo_id' => null]);
        $withoutGroup = $this->submit($student, 0)->evaluacion;
        $this->assertNotNull($withoutGroup->contexto_registrado_at);
        $this->assertNull($withoutGroup->grupo_aplicacion_id);
        $this->seed(InstrumentosBaseSeeder::class);
        $this->actingAs($student->persona->user)->post(route('evaluaciones.responder', 'phq9'), [
            'respuestas' => array_fill(1, 9, 0),
        ])->assertRedirect();
        $this->assertNotNull(Evaluacion::latest('id')->first()->contexto_registrado_at);
    }

    public function test_evolution_separates_scales_and_restricts_access(): void
    {
        $student = $this->student();
        $this->submit($student, 1);
        $this->seed(InstrumentosBaseSeeder::class);
        $own = $student->persona->user;
        $this->actingAs($own)->post(route('evaluaciones.responder', 'phq9'), ['respuestas' => array_fill(1, 9, 1)])->assertRedirect();
        $this->actingAs($own)->get(route('evolucion.own'))->assertOk()->assertViewHas('series', function ($series) {
            return count($series) === 5 && $series[0]['max'] === 42 && $series[3]['max'] === 27
                && $series[4]['max'] === 21 && $series[0]['points'][0]['score'] === 14
                && $series[3]['points'][0]['score'] === 9 && count($series[4]['points']) === 0;
        });
        $psych = $this->account('psicologo');
        $this->actingAs($psych)->get(route('evolucion.show', $student))->assertOk();
        foreach (['admin', 'tutor', 'estudiante', 'control_escolar'] as $role) {
            $this->actingAs($this->account($role))->get(route('evolucion.show', $student))->assertForbidden();
        }
        Role::findByName('psicologo')->revokePermissionTo('evaluaciones.respuestas.detalle');
        $this->actingAs($psych->fresh())->get(route('evolucion.show', $student))->assertForbidden();
        $this->actingAs($own)->get(route('evolucion.own', ['desde' => '2026-09-10', 'hasta' => '2026-09-01']))
            ->assertSessionHasErrors('hasta');
    }

    public function test_notices_are_private_idempotent_and_permissions_are_checked_again(): void
    {
        $psych = $this->account('psicologo');
        $other = $this->account('psicologo');
        $student = $this->student();
        $evaluation = $this->submit($student)->evaluacion;
        $case = $evaluation->casoAtencion;
        app(AvisoService::class)->newCase($case);
        $this->assertSame(2, Aviso::count());
        $notice = Aviso::where('user_id', $psych->id)->firstOrFail();
        $this->actingAs($other)->post(route('avisos.open', $notice))->assertNotFound();
        $this->actingAs($other)->patch(route('avisos.update', $notice), ['leido' => 1])->assertNotFound();
        $this->actingAs($psych)->get(route('avisos.index'))->assertOk()->assertSee('nuevo caso')->assertDontSee($student->codigo_anonimo);
        $this->assertNull($notice->fresh()->leido_at);
        $this->actingAs($psych)->post(route('avisos.open', $notice))->assertRedirect(route('psicologo.casos.show', $case));
        $this->assertNotNull($notice->fresh()->leido_at);
        $this->actingAs($psych)->patch(route('avisos.update', $notice), ['leido' => 0])->assertRedirect();
        $this->assertNull($notice->fresh()->leido_at);
        Role::findByName('psicologo')->revokePermissionTo('evaluaciones.respuestas.detalle');
        $this->actingAs($psych->fresh())->post(route('avisos.open', $notice))->assertForbidden();
    }

    public function test_shared_feedback_notifies_only_its_student_without_private_notes(): void
    {
        $psych = $this->account('psicologo');
        $student = $this->student();
        $evaluation = $this->submit($student, 0)->evaluacion;
        $this->actingAs($psych)->post(route('diagnosticos.store'), [
            'evaluacion_id' => $evaluation->id, 'impresion_diagnostica' => 'PRIVATE-DIAGNOSIS-ONLY',
            'retroalimentacion_estudiante' => 'SHARED-FEEDBACK-ONLY', 'requiere_derivacion' => false,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $notice = Aviso::where('user_id', $student->persona->user_id)->firstOrFail();
        $this->actingAs($student->persona->user)->get(route('avisos.index'))->assertOk()
            ->assertDontSee('PRIVATE-DIAGNOSIS-ONLY')->assertDontSee('SHARED-FEEDBACK-ONLY');
        $this->post(route('avisos.open', $notice))->assertRedirect(route('retroalimentacion.show', $evaluation));
        $this->get(route('retroalimentacion.show', $evaluation))->assertOk()->assertSee('SHARED-FEEDBACK-ONLY')->assertDontSee('PRIVATE-DIAGNOSIS-ONLY');
        $this->actingAs($this->student()->persona->user)->get(route('retroalimentacion.show', $evaluation))->assertForbidden();
        app(AvisoService::class)->feedback($evaluation->fresh());
        $this->assertSame(1, Aviso::where('tipo', 'retroalimentacion')->count());
    }

    public function test_historical_case_sync_is_silent_and_notices_rollback_with_case(): void
    {
        $this->account('psicologo');
        $student = $this->student();
        $evaluation = $this->submit($student, 0)->evaluacion;
        DB::table('alertas')->insert(['evaluacion_id' => $evaluation->id, 'estado' => 'generada', 'created_at' => now(), 'updated_at' => now()]);
        $this->artisan('casos:sincronizar')->assertExitCode(0);
        $this->assertSame(0, Aviso::count());
        try {
            DB::transaction(function () use ($student) {
                $this->submit($student);
                throw new \RuntimeException('rollback');
            });
        } catch (\RuntimeException $e) {
            $this->assertSame('rollback', $e->getMessage());
        }
        $this->assertSame(0, Aviso::count());
        $this->assertSame(1, CasoAtencion::count());
    }

    public function test_reports_count_distinct_participants_filter_snapshots_and_export_no_clinical_details(): void
    {
        $student = $this->student();
        $group = $student->grupo;
        $this->submit($student);
        $this->submit($student);
        $student->update(['grupo_id' => $this->group()->id]);
        $legacy = $this->submit($student, 0)->evaluacion;
        $legacy->forceFill(['contexto_registrado_at' => null, 'grupo_aplicacion_id' => null,
            'grupo_aplicacion_nombre' => null, 'carrera_aplicacion_id' => null, 'carrera_aplicacion_nombre' => null,
            'ciclo_aplicacion_id' => null, 'ciclo_aplicacion_nombre' => null])->save();
        $admin = $this->account('admin');
        $this->actingAs($admin)->get(route('reportes.index', ['grupo' => $group->id]))->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->total() === 1 && $rows->first()->aplicaciones === 2 && $rows->first()->participantes === 1)
            ->assertDontSee($student->codigo_anonimo);
        $this->get(route('reportes.index', ['contexto' => 'historico']))->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->first()->aplicaciones === 1);
        $response = $this->get(route('reportes.export', ['grupo' => $group->id]))->assertOk()->assertDownload();
        $csv = $response->streamedContent();
        $this->assertStringContainsString('Participantes', $csv);
        $this->assertStringNotContainsString($student->codigo_anonimo, $csv);
        $this->assertStringNotContainsString('depression_score', $csv);
        $this->get(route('reportes.index', ['grupo' => $student->grupo_id]))->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->total() === 0);
        foreach (['tutor', 'estudiante', 'control_escolar'] as $role) {
            $this->actingAs($this->account($role))->get(route('reportes.export'))->assertForbidden();
        }
        $this->actingAs($admin)->get(route('reportes.export', ['desde' => 'bad']))->assertSessionHasErrors('desde');
    }

    public function test_report_exports_neutralize_spreadsheet_formulas_and_use_career_cycle_filters(): void
    {
        $student = $this->student();
        $cycle = CicloEscolar::create(['nombre' => '2026', 'fecha_inicio' => '2026-01-01', 'fecha_fin' => '2026-12-31', 'estado' => 'activo']);
        $student->grupo->update(['nombre' => '=FORMULA()', 'ciclo_escolar_id' => $cycle->id]);
        $this->submit($student, 0);
        $this->actingAs($this->account('admin'));
        $params = ['ciclo' => $cycle->id, 'carrera' => $student->grupo->carrera_id];
        $this->get(route('reportes.index', $params))->assertOk()->assertViewHas('rows', fn ($rows) => $rows->total() === 1);
        $csv = $this->get(route('reportes.export', $params))->assertOk()->streamedContent();
        $this->assertStringContainsString("'=FORMULA()", $csv);
        $this->get(route('reportes.index', ['ciclo' => $cycle->id + 1]))->assertOk()->assertViewHas('rows', fn ($rows) => $rows->total() === 0);
    }

    public function test_diary_notices_respect_source_permissions_and_do_not_repeat_on_reanalysis(): void
    {
        $psych = $this->account('psicologo');
        $student = $this->student();
        $analysis = AnalisisNlp::create(['codigo_anonimo' => $student->codigo_anonimo,
            'texto_ingresado' => 'PRIVATE-DIARY', 'etiqueta_roberta' => 'SIN_RIESGO',
            'score_confianza' => 0.9, 'requiere_atencion' => true, 'estado_analisis' => 'completado']);
        $service = app(CasoAtencionService::class);
        $case = $service->forAnalysis($analysis);
        $service->forAnalysis($analysis);
        $this->assertSame(1, Aviso::count());
        $notice = Aviso::first();
        $this->actingAs($psych)->get(route('avisos.index'))->assertOk()->assertDontSee('PRIVATE-DIARY');
        $this->post(route('avisos.open', $notice))->assertRedirect(route('psicologo.casos.show', $case));
        Role::findByName('psicologo')->revokePermissionTo('resultados_ia.ver');
        $this->actingAs($psych->fresh())->post(route('avisos.open', $notice))->assertForbidden();
        $analysis2 = $analysis->replicate();
        $analysis2->save();
        $service->forAnalysis($analysis2);
        $this->assertSame(1, Aviso::count());
    }

    public function test_unlinked_dass_history_is_visible_and_empty_feedback_is_not_notified(): void
    {
        $psych = $this->account('psicologo');
        $student = $this->student();
        $detail = $this->submit($student, 0);
        $evaluation = $detail->evaluacion;
        $detail->update(['evaluacion_id' => null]);
        $this->actingAs($student->persona->user)->get(route('evolucion.own'))->assertOk()
            ->assertViewHas('series', fn ($series) => count($series[0]['points']) === 1);
        $this->actingAs($psych)->post(route('diagnosticos.store'), [
            'evaluacion_id' => $evaluation->id, 'impresion_diagnostica' => 'PRIVATE-ONLY',
            'requiere_derivacion' => false,
        ])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame(0, Aviso::where('tipo', 'retroalimentacion')->count());
        $this->actingAs($student->persona->user)->get(route('retroalimentacion.show', $evaluation))->assertNotFound();
    }

    public function test_reports_use_application_date_and_instrument_and_preserve_deleted_catalog_labels(): void
    {
        $student = $this->student();
        $detail = $this->submit($student, 0);
        $evaluation = $detail->evaluacion;
        $evaluation->forceFill(['created_at' => '2026-01-05 10:00:00'])->save();
        $student->grupo->delete();
        $this->actingAs($this->account('admin'));
        $params = ['desde' => '2026-01-01', 'hasta' => '2026-01-31', 'instrumento' => $evaluation->instrumento_id];
        $this->get(route('reportes.index', $params))->assertOk()
            ->assertViewHas('rows', fn ($rows) => $rows->total() === 1 && $rows->first()->grupo_aplicacion_nombre !== null);
        $params['instrumento'] = $evaluation->instrumento_id + 100;
        $this->get(route('reportes.index', $params))->assertOk()->assertViewHas('rows', fn ($rows) => $rows->total() === 0);
        unset($params['instrumento']);
        $params['desde'] = '2026-01-06';
        $this->get(route('reportes.index', $params))->assertOk()->assertViewHas('rows', fn ($rows) => $rows->total() === 0);
    }
}
