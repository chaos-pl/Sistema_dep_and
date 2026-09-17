<?php

namespace Tests\Feature;

use App\Models\Alerta;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Dass21Evaluation;
use App\Models\Dass21EvaluationAnswer;
use App\Models\Dass21Question;
use App\Models\Estudiante;
use App\Models\Evaluacion;
use App\Models\Grupo;
use App\Models\GrupoTutor;
use App\Models\Instrumento;
use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\Tutor;
use App\Models\User;
use App\Services\Dass21CoverageService;
use App\Services\Dass21EvaluationService;
use Database\Seeders\Dass21Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class Dass21IntegrationTest extends TestCase
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

    public function test_submission_links_details_answers_and_one_alert_atomically(): void
    {
        $student = $this->student();
        $this->actingAs($student->persona->user)->post(route('dass21.store'), ['answers' => $this->answers()])->assertSessionHasNoErrors()->assertRedirect();
        $dass = Dass21Evaluation::firstOrFail();
        $this->assertSame(21, $dass->answers()->count());
        $this->assertSame(42, $dass->depression_score);
        $this->assertSame(42, $dass->anxiety_score);
        $this->assertSame(42, $dass->stress_score);
        $this->assertSame('completada', $dass->evaluacion->estado);
        $this->assertSame(1, Alerta::where('evaluacion_id', $dass->evaluacion_id)->count());
        $this->assertDatabaseCount('resultados_clinicos', 0);
        $this->get(route('estudiante.dashboard'))->assertOk()->assertViewHas('totalCompletadas', 1);
        $this->get(route('evaluaciones.index'))->assertOk()->assertSee('Historial DASS-21');
        $this->get(route('dass21.history'))->assertOk()->assertSee('Mis aplicaciones');
        $this->get(route('dass21.create'))->assertOk();
    }

    public function test_invalid_question_keys_and_scores_leave_no_partial_records(): void
    {
        $student = $this->student();
        $bad = $this->answers();
        unset($bad[array_key_first($bad)]);
        $bad[99999] = 3;
        $this->actingAs($student->persona->user)->post(route('dass21.store'), ['answers' => $bad])->assertSessionHasErrors('answers');
        $this->assertDatabaseCount('evaluaciones', 0);
        $this->assertDatabaseCount('dass21_evaluations', 0);
        $bad = $this->answers();
        $bad[array_key_first($bad)] = 4;
        $this->post(route('dass21.store'), ['answers' => $bad])->assertSessionHasErrors();
        $this->assertDatabaseCount('dass21_evaluation_answers', 0);
    }

    public function test_low_scores_do_not_create_alerts_and_repeated_applications_are_allowed(): void
    {
        $student = $this->student();
        $this->submit($student, 0);
        $this->submit($student, 0);
        $this->assertDatabaseCount('evaluaciones', 2);
        $this->assertDatabaseCount('alertas', 0);
    }

    public function test_historical_conversion_is_repeatable_preserves_dates_and_does_not_alert(): void
    {
        $student = $this->student();
        $dass = Dass21Evaluation::create([
            'codigo_anonimo' => $student->codigo_anonimo, 'instrument_id' => Dass21Question::first()->instrument_id,
            'completed_at' => '2026-01-01 10:00:00', 'depression_score' => 42, 'max_severity_level' => 'Extremadamente severo',
        ]);
        $updated = $dass->updated_at->toDateTimeString();
        $this->artisan('dass21:link-evaluations', ['--dry-run' => true])->assertSuccessful();
        $this->assertDatabaseCount('evaluaciones', 0);
        $this->artisan('dass21:link-evaluations')->assertSuccessful();
        $this->artisan('dass21:link-evaluations')->assertSuccessful();
        $this->assertDatabaseCount('evaluaciones', 1);
        $this->assertDatabaseCount('alertas', 0);
        $dass->refresh();
        $this->assertSame('2026-01-01 10:00:00', $dass->evaluacion->created_at->toDateTimeString());
        $this->assertSame($updated, $dass->updated_at->toDateTimeString());
        $this->assertSame(42, $dass->depression_score);
    }

    public function test_psychologist_can_review_and_student_only_sees_shared_feedback(): void
    {
        $student = $this->student();
        $dass = $this->submit($student);
        $psych = $this->account('psicologo');
        $this->actingAs($psych)->get(route('psicologo.dashboard'))->assertOk()->assertSee('DASS-21 recientes');
        $this->get(route('psicologo.tamizajes.index'))->assertOk()->assertSee('DASS21');
        $this->get(route('psicologo.tamizajes.show', $dass->evaluacion_id))->assertOk()->assertSee('Estrés');
        $this->get(route('alertas.index'))->assertOk()->assertSee('Extremadamente severo', false);
        $this->post(route('diagnosticos.store'), [
            'evaluacion_id' => $dass->evaluacion_id, 'impresion_diagnostica' => 'NOTA PRIVADA DE PRUEBA',
            'retroalimentacion_estudiante' => 'MENSAJE COMPARTIDO DE PRUEBA',
        ])->assertSessionHasNoErrors()->assertRedirect();
        $this->assertSame('asignada_psicologo', $dass->evaluacion->alerta->fresh()->estado);
        $this->get(route('diagnosticos.index'))->assertOk();
        $this->actingAs($student->persona->user)->get(route('dass21.show', $dass))->assertOk()->assertSee('MENSAJE COMPARTIDO DE PRUEBA')->assertDontSee('NOTA PRIVADA DE PRUEBA');
        $other = $this->student();
        $this->actingAs($other->persona->user)->get(route('dass21.show', $dass))->assertForbidden();
        $this->get(route('psicologo.tamizajes.show', $dass->evaluacion_id))->assertForbidden();
    }

    public function test_tutor_coverage_counts_people_and_never_exposes_clinical_details(): void
    {
        $tutorUser = $this->account('tutor');
        $group = $this->group($tutorUser->persona->tutor);
        $student = $this->student($group);
        $this->student($group);
        $this->submit($student);
        $dass = $this->submit($student);
        $this->actingAs($tutorUser)->get(route('tutor.dashboard'))->assertOk()
            ->assertViewHas('completadas', 1)->assertViewHas('pendientes', 1)->assertDontSee('Extremadamente severo');
        $this->get(route('tutor.grupos.show', $group))->assertOk()->assertSee('Seguimiento pendiente')->assertDontSee('Extremadamente severo');
        $this->get(route('psicologo.tamizajes.show', $dass->evaluacion_id))->assertForbidden();
        $this->get(route('tutor.grupos.show', $this->group()))->assertForbidden();
    }

    public function test_cycle_assignments_take_precedence_and_revocation_removes_access(): void
    {
        $legacy = $this->account('tutor');
        $assigned = $this->account('tutor');
        $group = $this->group($legacy->persona->tutor);
        $cycle = CicloEscolar::create(['nombre' => 'Actual', 'fecha_inicio' => today()->subMonth(), 'fecha_fin' => today()->addMonth(), 'estado' => 'activo']);
        $group->update(['ciclo_escolar_id' => $cycle->id]);
        $pivot = GrupoTutor::create(['grupo_id' => $group->id, 'tutor_id' => $assigned->persona->tutor->id, 'ciclo_escolar_id' => $cycle->id, 'estado' => 'activo']);
        $this->actingAs($assigned)->get(route('tutor.grupos.show', $group))->assertOk();
        $this->actingAs($legacy)->get(route('tutor.grupos.show', $group))->assertForbidden();
        $pivot->delete();
        $this->actingAs($assigned)->get(route('tutor.grupos.show', $group))->assertForbidden();
        $this->actingAs($legacy)->get(route('tutor.grupos.show', $group))->assertForbidden();
    }

    public function test_period_excludes_old_applications_and_administrative_views_only_show_coverage(): void
    {
        $student = $this->student();
        $dass = $this->submit($student);
        $dass->update(['completed_at' => now()->subYear()]);
        $rows = app(Dass21CoverageService::class)->groups(Grupo::query(), [now()->startOfMonth(), now()->endOfDay()]);
        $this->assertSame(0, $rows->sum('completadas'));
        foreach (['admin' => 'admin.dashboard', 'control_escolar' => 'control_escolar.dashboard'] as $role => $route) {
            $this->actingAs($this->account($role))->get(route($route))->assertOk()->assertSee('Cobertura DASS-21')->assertDontSee('Extremadamente severo');
        }
    }

    public function test_existing_phq9_results_remain_visible_without_dass_details(): void
    {
        $student = $this->student();
        $instrument = Instrumento::create(['acronimo' => 'PHQ9', 'nombre' => 'PHQ-9']);
        $eval = Evaluacion::create(['codigo_anonimo' => $student->codigo_anonimo, 'instrumento_id' => $instrument->id, 'estado' => 'completada']);
        $eval->resultadoClinico()->create(['puntaje_total' => 12, 'nivel_riesgo' => 'moderado']);
        $this->actingAs($this->account('psicologo'))->get(route('psicologo.tamizajes.show', $eval))->assertOk()->assertSee('12')->assertSee('moderado');
    }

    public function test_failure_during_answer_storage_rolls_back_the_whole_submission(): void
    {
        $student = $this->student();
        $event = 'eloquent.created: '.Dass21EvaluationAnswer::class;
        Event::listen($event, function () {
            throw new \RuntimeException('Fallo sintético de persistencia');
        });
        try {
            $this->submit($student);
            $this->fail('Debió fallar la persistencia.');
        } catch (\RuntimeException $exception) {
            $this->assertSame('Fallo sintético de persistencia', $exception->getMessage());
        } finally {
            Event::forget($event);
        }
        foreach (['evaluaciones', 'dass21_evaluations', 'dass21_evaluation_answers', 'alertas'] as $table) {
            $this->assertDatabaseCount($table, 0);
        }
    }

    public function test_detail_permission_is_required_even_for_psychologists(): void
    {
        $dass = $this->submit($this->student());
        $psych = $this->account('psicologo');
        Role::findByName('psicologo')->revokePermissionTo('evaluaciones.respuestas.detalle');
        $this->actingAs($psych->fresh())->get(route('psicologo.tamizajes.show', $dass->evaluacion_id))->assertForbidden();
        $this->post(route('diagnosticos.store'), ['evaluacion_id' => $dass->evaluacion_id, 'impresion_diagnostica' => 'Prueba'])->assertForbidden();
        $this->assertDatabaseCount('diagnosticos', 0);
    }

    public function test_expired_cycle_and_inactive_assignment_do_not_grant_tutor_access(): void
    {
        $user = $this->account('tutor');
        $group = $this->group();
        $cycle = CicloEscolar::create(['nombre' => 'Cerrado', 'fecha_inicio' => today()->subYear(), 'fecha_fin' => today()->subMonth(), 'estado' => 'activo']);
        $group->update(['ciclo_escolar_id' => $cycle->id]);
        $pivot = GrupoTutor::create(['grupo_id' => $group->id, 'tutor_id' => $user->persona->tutor->id, 'ciclo_escolar_id' => $cycle->id, 'estado' => 'activo']);
        $this->actingAs($user)->get(route('tutor.grupos.show', $group))->assertForbidden();
        $cycle->update(['fecha_fin' => today()->addMonth()]);
        $pivot->update(['estado' => 'inactivo']);
        $this->get(route('tutor.grupos.show', $group))->assertForbidden();
        $this->get(route('tutor.dashboard'))->assertOk()->assertViewHas('totalGrupos', 0);
    }

    public function test_alert_policy_preserves_the_existing_dass_severity_criterion(): void
    {
        $student = $this->student();
        $moderate = $this->submit($student, 1);
        $this->assertSame('Moderado', $moderate->max_severity_level);
        $this->assertNull($moderate->evaluacion->alerta);
        $critical = $this->submit($student, 2);
        $this->assertSame('Extremadamente severo', $critical->max_severity_level);
        $this->assertNotNull($critical->evaluacion->alerta);
    }

    public function test_invalid_periods_are_rejected_and_filters_select_the_expected_instrument(): void
    {
        $this->submit($this->student());
        $this->actingAs($this->account('psicologo'));
        $this->get(route('psicologo.tamizajes.index', ['desde' => '2026-09-02', 'hasta' => '2026-09-01']))->assertSessionHasErrors('hasta');
        $this->get(route('psicologo.tamizajes.index', ['instrumento' => 'PHQ9']))->assertOk()->assertViewHas('evaluaciones', fn ($rows) => $rows->total() === 0);
        $this->get(route('psicologo.tamizajes.index', ['instrumento' => 'DASS21']))->assertOk()->assertViewHas('evaluaciones', fn ($rows) => $rows->total() === 1);
    }

    public function test_linked_details_follow_existing_account_deletion_cascades(): void
    {
        $student = $this->student();
        $this->submit($student);
        $student->persona->user->delete();
        $this->assertDatabaseCount('evaluaciones', 0);
        $this->assertDatabaseCount('dass21_evaluations', 0);
        $this->assertDatabaseCount('dass21_evaluation_answers', 0);
        $this->assertDatabaseCount('alertas', 0);
    }

    public function test_tutor_followup_counts_people_until_all_their_alerts_are_attended(): void
    {
        $tutor = $this->account('tutor');
        $group = $this->group($tutor->persona->tutor);
        $student = $this->student($group);
        $first = $this->submit($student);
        $second = $this->submit($student);
        $this->submit($student, 0); // Un resultado nuevo normal no cierra alertas anteriores.
        $this->submit($this->student()); // Otro grupo no debe entrar al contador.
        $this->actingAs($tutor)->get(route('tutor.dashboard', ['desde' => '2025-01-01', 'hasta' => '2025-01-02']))
            ->assertOk()->assertViewHas('alumnosRiesgo', 1)->assertSee(route('tutor.seguimiento'), false)->assertDontSee('Posible Riesgo');
        $this->get(route('tutor.seguimiento'))->assertOk()
            ->assertViewHas('estudiantes', fn ($rows) => $rows->total() === 1)
            ->assertDontSee('Extremadamente severo');
        $this->get(route('tutor.grupos.show', $group))->assertOk()->assertSee('Seguimiento pendiente');
        $psych = $this->account('psicologo');
        foreach ([$first, $second] as $index => $dass) {
            $this->actingAs($psych)->post(route('diagnosticos.store'), ['evaluacion_id' => $dass->evaluacion_id, 'impresion_diagnostica' => 'Valoración de prueba'])->assertSessionHasNoErrors();
            $this->actingAs($tutor)->get(route('tutor.dashboard'))->assertOk()->assertViewHas('alumnosRiesgo', 1);
            $case = $dass->evaluacion->casoAtencion;
            $this->actingAs($psych)->post(route('psicologo.casos.update', $case), [
                'version' => $case->version, 'accion' => 'cerrar', 'nota' => 'Cierre explícito de prueba',
            ])->assertSessionHasNoErrors()->assertRedirect();
            $this->actingAs($tutor)->get(route('tutor.dashboard'))->assertOk()->assertViewHas('alumnosRiesgo', $index === 0 ? 1 : 0);
        }
        $this->get(route('tutor.seguimiento'))->assertOk()->assertSee('Sin seguimiento pendiente registrado');
    }

    public function test_tutor_followup_requires_permission_and_excludes_inactive_students(): void
    {
        $tutor = $this->account('tutor');
        $student = $this->student($this->group($tutor->persona->tutor));
        $this->submit($student);
        $student->update(['estado' => 'inactivo']);
        $this->actingAs($tutor)->get(route('tutor.seguimiento'))->assertOk()->assertViewHas('estudiantes', fn ($rows) => $rows->total() === 0);
        Role::findByName('tutor')->revokePermissionTo('alertas.ver.general');
        $this->actingAs($tutor->fresh())->get(route('tutor.seguimiento'))->assertForbidden();
        $this->get(route('tutor.dashboard'))->assertOk()->assertDontSee('Estudiantes con casos por atender');
    }
}
