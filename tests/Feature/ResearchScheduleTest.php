<?php

namespace Tests\Feature;

use App\Models\Dass21Question;
use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\ResultadoClinico;
use App\Models\User;
use App\Services\Dass21ScoringService;
use Database\Seeders\Dass21Seeder;
use Database\Seeders\InstrumentosBaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResearchScheduleTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        $this->seed([RolesAndPermissionsSeeder::class, InstrumentosBaseSeeder::class]);
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole('estudiante');
        $persona = Persona::create([
            'user_id' => $user->id, 'nombre' => 'Prueba', 'apellido_paterno' => 'Sintetica',
            'fecha_nacimiento' => '2000-01-01', 'genero' => 'otro',
        ]);
        Estudiante::create([
            'persona_id' => $persona->id, 'matricula' => 'TEST-'.$user->id,
            'codigo_anonimo' => 'TEST-'.$user->id, 'estado' => 'activo',
        ]);

        return $user;
    }

    public function test_phq9_and_gad7_totals_and_current_application_thresholds(): void
    {
        $this->actingAs($this->student());
        // Characterizes the application's four levels; this is not clinical validation.
        foreach (['phq9' => 9, 'gad7' => 7] as $instrument => $count) {
            foreach ([0 => 'nulo', 4 => 'nulo', 5 => 'leve', 9 => 'leve', 10 => 'moderado', 14 => 'moderado', 15 => 'severo', $count * 3 => 'severo'] as $total => $level) {
                $remaining = $total;
                $answers = [];
                for ($i = 1; $i <= $count; $i++) {
                    $answers[$i] = min(3, $remaining);
                    $remaining -= $answers[$i];
                }
                $this->post(route('evaluaciones.responder', $instrument), ['respuestas' => $answers])
                    ->assertSessionHasNoErrors()->assertRedirect(route('evaluaciones.index'));
                $result = ResultadoClinico::latest('id')->firstOrFail();
                $this->assertSame($total, (int) $result->puntaje_total);
                $this->assertSame($level, $result->nivel_riesgo);
                $this->assertSame($count, $this->app['db']->table('respuestas')->where('evaluacion_id', $result->evaluacion_id)->count());
                $this->assertSame(in_array($level, ['moderado', 'severo']), $result->evaluacion->alerta()->exists());
            }
        }
    }

    public function test_dass21_dimension_boundaries_multiplier_and_global_maximum(): void
    {
        $this->seed(Dass21Seeder::class);
        $boundaries = [
            'depression' => [0 => 'Normal', 4 => 'Normal', 5 => 'Leve', 6 => 'Leve', 7 => 'Moderado', 10 => 'Moderado', 11 => 'Severo', 13 => 'Severo', 14 => 'Extremadamente severo', 21 => 'Extremadamente severo'],
            'anxiety' => [0 => 'Normal', 3 => 'Normal', 4 => 'Leve', 5 => 'Moderado', 7 => 'Moderado', 8 => 'Severo', 9 => 'Severo', 10 => 'Extremadamente severo', 21 => 'Extremadamente severo'],
            'stress' => [0 => 'Normal', 7 => 'Normal', 8 => 'Leve', 9 => 'Leve', 10 => 'Moderado', 12 => 'Moderado', 13 => 'Severo', 16 => 'Severo', 17 => 'Extremadamente severo', 21 => 'Extremadamente severo'],
        ];
        foreach ($boundaries as $dimension => $cases) {
            foreach ($cases as $raw => $level) {
                $answers = Dass21Question::pluck('id')->mapWithKeys(fn ($id) => [$id => 0])->all();
                $remaining = $raw;
                foreach (Dass21Question::where('dimension', $dimension)->pluck('id') as $id) {
                    $answers[$id] = min(3, $remaining);
                    $remaining -= $answers[$id];
                }
                $result = app(Dass21ScoringService::class)->score($answers);
                $this->assertSame($raw, $result[$dimension.'_raw']);
                $this->assertSame($raw * 2, $result[$dimension.'_score']);
                $this->assertSame($level, $result[$dimension.'_level']);
                $this->assertSame($level, $result['max_severity_level']);
                foreach (array_diff(array_keys($boundaries), [$dimension]) as $other) {
                    $this->assertSame(0, $result[$other.'_score']);
                    $this->assertSame('Normal', $result[$other.'_level']);
                }
            }
        }
    }

    public function test_sql_payload_cannot_bypass_login(): void
    {
        User::factory()->create();
        $this->post('/login', ['email' => "' OR 1=1 --", 'password' => 'wrong-password'])
            ->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 1);
    }

    public function test_sql_payload_in_instrument_lookup_returns_no_record(): void
    {
        $this->actingAs($this->student());
        $this->get(route('evaluaciones.aplicar', "phq9' OR '1'='1"))->assertNotFound();
        $this->assertDatabaseCount('instrumentos', 2);
        $this->assertDatabaseCount('evaluaciones', 0);
    }

    public function test_password_is_hashed_and_hidden_from_serialization(): void
    {
        $user = User::factory()->create(['password' => 'Synthetic-password-2026']);
        $stored = $this->app['db']->table('users')->where('id', $user->id)->value('password');
        $this->assertNotSame('Synthetic-password-2026', $stored);
        $this->assertTrue(Hash::check('Synthetic-password-2026', $stored));
        $this->assertArrayNotHasKey('password', $user->toArray());
    }

    public function test_real_csrf_validation_rejects_missing_and_wrong_tokens_and_accepts_matching_token(): void
    {
        // Laravel skips CSRF in tests; override only that bypass, keeping the web stack.
        $this->app->bind(ValidateCsrfToken::class, fn ($app) => new class($app, $app['encrypter']) extends ValidateCsrfToken
        {
            protected function runningUnitTests()
            {
                return false;
            }
        });
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $credentials = ['email' => $user->email, 'password' => 'password'];
        $this->withSession(['_token' => 'synthetic-token'])->post('/login', $credentials)->assertStatus(419);
        $this->assertGuest();
        $this->withSession(['_token' => 'synthetic-token'])->post('/login', $credentials + ['_token' => 'incorrect'])->assertStatus(419);
        $this->assertGuest();
        $this->withSession(['_token' => 'synthetic-token'])->post('/login', $credentials + ['_token' => 'synthetic-token'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }
}
