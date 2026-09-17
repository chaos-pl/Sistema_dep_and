<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\AnalisisNlp;
use App\Models\Carrera;
use App\Models\CasoAtencion;
use App\Models\Dass21Evaluation;
use App\Models\Dass21Question;
use App\Models\Diagnostico;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Grupo;
use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\SeguimientoCaso;
use App\Models\Tutor;
use App\Models\User;
use App\Services\CasoAtencionService;
use App\Services\Dass21EvaluationService;
use Database\Seeders\Dass21Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AttentionCaseTest extends TestCase
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

    private function diary(Estudiante $student): AnalisisNlp
    {
        return AnalisisNlp::create(['codigo_anonimo' => $student->codigo_anonimo,
            'texto_ingresado' => 'DIARIO PRIVADO SOLO PARA PRUEBAS', 'etiqueta_roberta' => 'SIN_RIESGO',
            'score_confianza' => 0.9, 'requiere_atencion' => true, 'estado_analisis' => 'completado']);
    }

    private function action(CasoAtencion $case, string $action, array $extra = [])
    {
        return $this->post(route('psicologo.casos.update', $case),
            $extra + ['version' => $case->fresh()->version, 'accion' => $action, 'nota' => 'NOTA PRIVADA DE PRUEBA']);
    }

    public function test_case_lifecycle_requires_assignment_review_and_explicit_close(): void
    {
        $psych = $this->account('psicologo');
        $dass = $this->submit($this->student());
        $case = $dass->evaluacion->casoAtencion;
        $this->assertSame('pendiente', $case->estado);
        $this->assertSame('generada', $dass->evaluacion->alerta->fresh()->estado);
        $this->actingAs($psych)->get(route('psicologo.casos.show', $case))->assertOk();
        $this->assertNull($case->fresh()->psicologo_id);
        $this->action($case, 'cerrar')->assertSessionHasErrors('caso');
        $this->action($case, 'asignar', ['psicologo_id' => $psych->persona->psicologo->id])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('asignado', $case->fresh()->estado);
        $this->action($case, 'cerrar')->assertSessionHasErrors('caso');
        $this->action($case, 'nota', ['nota' => '<script>nota privada</script>'])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('seguimiento', $case->fresh()->estado);
        $this->get(route('psicologo.casos.show', $case))->assertSee('&lt;script&gt;nota privada&lt;/script&gt;', false)->assertDontSee('<script>nota privada</script>', false);
        $this->action($case, 'cerrar')->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('cerrado', $case->fresh()->estado);
        $this->assertNotNull($case->fresh()->cerrado_at);
        $this->assertSame('atendida', $dass->evaluacion->alerta->fresh()->estado);
        $this->action($case, 'nota')->assertSessionHasErrors('caso');
        $this->action($case, 'reabrir')->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('seguimiento', $case->fresh()->estado);
        $this->assertNull($case->fresh()->cerrado_at);
        $this->assertSame('asignada_psicologo', $dass->evaluacion->alerta->fresh()->estado);
        $this->assertSame(4, $case->fresh()->version);
        $this->assertSame(5, $case->seguimientos()->count());
    }

    public function test_assignment_is_exclusive_and_stale_changes_cannot_overwrite_it(): void
    {
        $a = $this->account('psicologo');
        $b = $this->account('psicologo');
        $case = $this->submit($this->student())->evaluacion->casoAtencion;
        $this->actingAs($a);
        $this->action($case, 'asignar', ['psicologo_id' => $a->persona->psicologo->id])->assertSessionHasNoErrors();
        $this->action($case, 'nota', ['version' => 0])->assertSessionHasErrors('version');
        $this->actingAs($b);
        $this->action($case, 'asignar', ['psicologo_id' => $b->persona->psicologo->id])->assertForbidden();
        $this->action($case, 'nota')->assertForbidden();
        $this->get(route('psicologo.casos.show', $case))->assertOk()->assertSee('Solo el responsable');
        $this->actingAs($a);
        $this->action($case, 'asignar', ['psicologo_id' => $b->persona->psicologo->id])->assertSessionHasNoErrors();
        $this->action($case, 'nota')->assertForbidden();
        $this->actingAs($b);
        $this->action($case, 'nota')->assertSessionHasNoErrors();
        $this->assertSame($b->persona->psicologo->id, $case->fresh()->psicologo_id);
    }

    public function test_valuation_keeps_followup_open_and_other_professional_cannot_value_assigned_case(): void
    {
        $a = $this->account('psicologo');
        $b = $this->account('psicologo');
        $dass = $this->submit($this->student());
        $case = $dass->evaluacion->casoAtencion;
        $this->actingAs($a);
        $this->action($case, 'asignar', ['psicologo_id' => $a->persona->psicologo->id]);
        $this->actingAs($b)->post(route('diagnosticos.store'), ['evaluacion_id' => $dass->evaluacion_id, 'impresion_diagnostica' => 'Privada'])->assertForbidden();
        $this->assertDatabaseCount('diagnosticos', 0);
        $this->actingAs($a)->post(route('diagnosticos.store'), ['evaluacion_id' => $dass->evaluacion_id, 'impresion_diagnostica' => 'Privada'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('seguimiento', $case->fresh()->estado);
        $this->assertNull($case->fresh()->cerrado_at);
        $this->assertSame('asignada_psicologo', $dass->evaluacion->alerta->fresh()->estado);
        $this->assertSame(1, $case->seguimientos()->where('tipo', 'valoracion')->count());
    }

    public function test_diary_attention_uses_same_lifecycle_without_leaking_to_tutor_or_student(): void
    {
        $tutor = $this->account('tutor');
        $student = $this->student($this->group($tutor->persona->tutor));
        $diary = $this->diary($student);
        $service = app(CasoAtencionService::class);
        $case = $service->forAnalysis($diary);
        $service->forAnalysis($diary);
        $this->assertDatabaseCount('casos_atencion', 1);
        $psych = $this->account('psicologo');
        $this->actingAs($psych);
        $this->action($case, 'asignar', ['psicologo_id' => $psych->persona->psicologo->id]);
        $this->action($case, 'nota');
        $this->actingAs($tutor)->get(route('tutor.seguimiento'))->assertOk()
            ->assertDontSee('DIARIO PRIVADO')->assertDontSee('NOTA PRIVADA')->assertDontSee('Diario IA')
            ->assertViewHas('estudiantes', fn ($rows) => $rows->total() === 1);
        $this->get(route('psicologo.casos.show', $case))->assertForbidden();
        $this->actingAs($student->persona->user)->get(route('diario.index'))->assertOk()->assertDontSee('NOTA PRIVADA');
        $this->get(route('psicologo.casos.show', $case))->assertForbidden();
        $this->actingAs($psych);
        $this->action($case, 'cerrar')->assertSessionHasNoErrors();
        $diary->update(['requiere_atencion' => false]);
        $service->forAnalysis($diary);
        $diary->update(['requiere_atencion' => true]);
        $service->forAnalysis($diary);
        $this->assertSame('cerrado', $case->fresh()->estado);
        $this->assertTrue($diary->fresh()->requiere_atencion);
        $this->actingAs($tutor)->get(route('tutor.seguimiento'))->assertViewHas('estudiantes', fn ($rows) => $rows->total() === 0);
    }

    public function test_origin_permissions_filter_list_and_block_direct_reads_and_writes(): void
    {
        $psych = $this->account('psicologo');
        $evaluationCase = $this->submit($this->student())->evaluacion->casoAtencion;
        $diaryCase = app(CasoAtencionService::class)->forAnalysis($this->diary($this->student()));
        $role = Role::findByName('psicologo');
        $role->revokePermissionTo('resultados_ia.ver');
        $this->actingAs($psych->fresh())->get(route('psicologo.casos.index'))->assertOk()
            ->assertViewHas('casos', fn ($rows) => $rows->total() === 1);
        $this->get(route('psicologo.casos.show', $diaryCase))->assertForbidden();
        $this->action($diaryCase, 'nota')->assertForbidden();
        $role->revokePermissionTo('evaluaciones.respuestas.detalle');
        $this->actingAs($psych->fresh())->get(route('psicologo.casos.show', $evaluationCase))->assertForbidden();
        $this->get(route('psicologo.casos.index'))->assertOk()->assertViewHas('casos', fn ($rows) => $rows->total() === 0);
    }

    public function test_sync_is_explicit_repeatable_and_never_invents_historical_closure_dates(): void
    {
        $student = $this->student();
        $psych = $this->account('psicologo');
        $evaluation = Evaluacion::create(['codigo_anonimo' => $student->codigo_anonimo, 'instrumento_id' => Dass21Question::first()->instrument_id, 'estado' => 'completada']);
        Alerta::withoutEvents(fn () => Alerta::create(['evaluacion_id' => $evaluation->id, 'estado' => 'atendida']));
        Diagnostico::create(['evaluacion_id' => $evaluation->id, 'psicologo_id' => $psych->persona->psicologo->id, 'impresion_diagnostica' => 'Historia privada']);
        $diary = $this->diary($student);
        $this->artisan('casos:sincronizar', ['--dry-run' => true])->assertExitCode(0);
        $this->assertDatabaseCount('casos_atencion', 0);
        $this->artisan('casos:sincronizar')->assertExitCode(0);
        $this->artisan('casos:sincronizar')->assertExitCode(0);
        $this->assertDatabaseCount('casos_atencion', 2);
        $this->assertDatabaseCount('seguimientos_caso', 2);
        $case = $evaluation->fresh()->casoAtencion;
        $this->assertSame('seguimiento', $case->estado);
        $this->assertNull($case->cerrado_at);
        $this->assertNull($case->asignado_at);
        $this->assertSame('asignada_psicologo', $evaluation->alerta->fresh()->estado);
        $this->assertSame('pendiente', $diary->fresh()->casoAtencion->estado);
    }

    public function test_failed_event_write_rolls_back_state_and_assignment(): void
    {
        $case = $this->submit($this->student())->evaluacion->casoAtencion;
        $psych = $this->account('psicologo');
        SeguimientoCaso::creating(function () {
            throw new \RuntimeException('Fallo simulado');
        });
        try {
            app(CasoAtencionService::class)->change($case, $psych, 0, 'asignar', 'Prueba', $psych->persona->psicologo->id);
            $this->fail('La escritura debió fallar');
        } catch (\RuntimeException $e) {
            $this->assertSame('Fallo simulado', $e->getMessage());
        } finally {
            SeguimientoCaso::flushEventListeners();
        }
        $this->assertSame('pendiente', $case->fresh()->estado);
        $this->assertNull($case->fresh()->psicologo_id);
        $this->assertSame(0, $case->fresh()->version);
        $this->assertSame('generada', $case->evaluacion->alerta->fresh()->estado);
    }

    public function test_case_mutations_require_reason_permission_and_professional_profile(): void
    {
        $case = $this->submit($this->student())->evaluacion->casoAtencion;
        foreach (['admin', 'control_escolar', 'estudiante', 'tutor'] as $role) {
            $this->actingAs($this->account($role))->post(route('psicologo.casos.update', $case), [])->assertForbidden();
        }
        $psych = $this->account('psicologo');
        $this->actingAs($psych);
        $this->action($case, 'asignar', ['psicologo_id' => $psych->persona->psicologo->id, 'nota' => ''])->assertSessionHasErrors('nota');
        $this->action($case, 'asignar', ['psicologo_id' => 999999])->assertSessionHasErrors('psicologo_id');
        Role::findByName('psicologo')->revokePermissionTo('diagnosticos.crear');
        $this->actingAs($psych->fresh());
        $this->action($case, 'nota')->assertForbidden();
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole('psicologo');
        $this->actingAs($user)->get(route('psicologo.casos.index'))->assertForbidden();
    }

    public function test_case_filters_and_source_deletion_cascades(): void
    {
        $student = $this->student();
        $case = $this->submit($student)->evaluacion->casoAtencion;
        app(CasoAtencionService::class)->forAnalysis($this->diary($student));
        $psych = $this->account('psicologo');
        $this->actingAs($psych);
        $this->action($case, 'asignar', ['psicologo_id' => $psych->persona->psicologo->id]);
        $this->get(route('psicologo.casos.index', ['origen' => 'diario', 'responsable' => 'sin_asignar']))->assertOk()->assertViewHas('casos', fn ($rows) => $rows->total() === 1);
        $this->get(route('psicologo.casos.index',['responsable' => 'mios']))->assertOk()->assertViewHas('casos',fn ($rows) => $rows->total() === 1);
        $student->persona->user->delete();
        $this->assertDatabaseCount('casos_atencion',0);
        $this->assertDatabaseCount('seguimientos_caso',0);
    }
}
