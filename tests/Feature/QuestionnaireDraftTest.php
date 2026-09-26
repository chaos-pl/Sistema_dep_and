<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\Dass21Evaluation;
use App\Models\Dass21Question;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\QuestionnaireDraft;
use App\Models\Tutor;
use App\Models\User;
use App\Services\Dass21EvaluationService;
use Database\Seeders\Dass21Seeder;
use Database\Seeders\InstrumentosBaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class QuestionnaireDraftTest extends TestCase
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

    public function test_drafts_resume_privately_without_creating_results_or_alerts(): void
    {
        $student = $this->student();
        $question = Dass21Question::firstOrFail()->id;
        $this->actingAs($student->persona->user)->putJson(route('questionnaires.draft', 'DASS21'),
            ['answers' => [$question => 2], 'draft_version' => 0])->assertOk()->assertJsonPath('version', 1);
        $this->assertDatabaseCount('evaluaciones', 0);
        $this->assertDatabaseCount('alertas', 0);
        $this->get(route('dass21.create'))->assertOk()->assertViewHas('draftAnswers', fn ($a) => $a[$question] === 2)->assertSee('Borrador recuperado');
        $other = $this->student();
        $this->actingAs($other->persona->user)->get(route('dass21.create'))->assertViewHas('draftAnswers', []);
        foreach (['psicologo', 'tutor', 'admin'] as $role) {
            $this->actingAs($this->account($role))->putJson(route('questionnaires.draft', 'DASS21'), ['answers' => [], 'draft_version' => 0])->assertForbidden();
        }
    }

    public function test_drafts_reject_invalid_values_questions_and_stale_tabs(): void
    {
        $student = $this->student();
        $this->actingAs($student->persona->user);
        foreach ([[10 => 2], [1 => 4], [1 => '2'], [1 => true]] as $answers) {
            $this->putJson(route('questionnaires.draft', 'PHQ9'), ['answers' => $answers, 'draft_version' => 0])->assertUnprocessable();
        }
        $this->putJson(route('questionnaires.draft', 'PHQ9'), ['answers' => [1 => 0], 'draft_version' => 0])->assertOk();
        $this->putJson(route('questionnaires.draft', 'PHQ9'), ['answers' => [1 => 3], 'draft_version' => 0])->assertUnprocessable()->assertJsonValidationErrors('draft_version');
        $this->assertSame([1 => 0], QuestionnaireDraft::first()->answers);
        $this->putJson(route('questionnaires.draft', 'OTHER'), ['answers' => [], 'draft_version' => 0])->assertNotFound();
    }

    public function test_final_submission_clears_draft_and_late_autosave_cannot_restore_it(): void
    {
        $student = $this->student();
        $answers = $this->answers(0);
        $this->actingAs($student->persona->user)->putJson(route('questionnaires.draft', 'DASS21'), ['answers' => $answers, 'draft_version' => 0])->assertOk();
        $this->post(route('dass21.store'), ['answers' => $answers, 'draft_version' => 0])->assertSessionHasErrors('draft_version');
        $this->assertDatabaseCount('evaluaciones', 0);
        $this->post(route('dass21.store'), ['answers' => $answers, 'draft_version' => 1])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertDatabaseCount('evaluaciones', 1);
        $this->assertSame([], QuestionnaireDraft::first()->answers);
        $this->putJson(route('questionnaires.draft', 'DASS21'), ['answers' => $answers, 'draft_version' => 1])->assertUnprocessable();
        $this->get(route('dass21.create'))->assertViewHas('draftVersion', 2)->assertViewHas('draftAnswers', []);
    }

    public function test_both_short_questionnaires_resume_and_only_clear_after_valid_submission(): void
    {
        $this->seed(InstrumentosBaseSeeder::class);
        $student = $this->student();
        $this->actingAs($student->persona->user);
        foreach (['PHQ9' => 9, 'GAD7' => 7] as $instrument => $count) {
            $this->putJson(route('questionnaires.draft', $instrument), ['answers' => [1 => 1], 'draft_version' => 0])->assertOk();
            $this->get(route('evaluaciones.aplicar', strtolower($instrument)))->assertOk()->assertViewHas('draftAnswers', [1 => 1]);
            $this->post(route('evaluaciones.responder', strtolower($instrument)), ['respuestas' => [1 => 1], 'draft_version' => 1])->assertRedirect();
            $this->assertFalse(QuestionnaireDraft::where('instrument', $instrument)->first()->completed);
            $this->post(route('evaluaciones.responder', strtolower($instrument)), ['respuestas' => array_fill(1, $count, 0), 'draft_version' => 1])->assertSessionHasNoErrors();
            $this->assertTrue(QuestionnaireDraft::where('instrument', $instrument)->first()->completed);
        }
        $this->assertDatabaseCount('evaluaciones', 2);
    }

    public function test_dashboard_order_and_notification_navigation(): void
    {
        $student = $this->student();
        $this->submit($student, 0);
        $psych = $this->account('psicologo');
        $response = $this->actingAs($psych)->get(route('psicologo.dashboard'))->assertOk();
        $response->assertSeeInOrder(['Bienvenido,', 'DASS-21 recientes']);
        $response->assertSee('notification-bell')->assertSee('Centro de notificaciones:');
        $html = $response->getContent();
        $this->assertSame(1, substr_count($html, 'class="notification-bell'));
        $this->assertStringNotContainsString('bi bi-bell"></i> Notificaciones', $html);
        $this->actingAs($student->persona->user)->get(route('estudiante.dashboard'))->assertOk()->assertSeeInOrder(['Monitoreo DASS-21', 'Resumen académico']);
    }
}
