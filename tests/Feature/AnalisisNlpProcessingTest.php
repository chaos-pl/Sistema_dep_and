<?php

namespace Tests\Feature;

use App\Exceptions\PrometeoIaException;
use App\Jobs\ProcesarAnalisisNlp;
use App\Models\AnalisisNlp;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\Tutor;
use App\Models\User;
use App\Services\AnalisisNlpQueueService;
use App\Services\PrometeoIaService;
use Database\Seeders\Dass21Seeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class AnalisisNlpProcessingTest extends TestCase
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
        config(['services.prometeo_ia.url' => 'https://ia.test/api/evaluar', 'queue.failed.database' => 'dass21_testing']);
        Http::preventStrayRequests();
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

    private function entry(array $attributes = []): AnalisisNlp
    {
        return AnalisisNlp::create($attributes + [
            'codigo_anonimo' => $this->student()->codigo_anonimo,
            'texto_ingresado' => 'Entrada ficticia exclusivamente para pruebas automatizadas.',
            'etiqueta_roberta' => 'pendiente', 'score_confianza' => 0, 'requiere_atencion' => false,
            'estado_analisis' => 'fallido',
        ]);
    }

    private function response(): array
    {
        return [
            'status' => 'ok',
            'entrada' => ['texto_original' => 'NO_PERSISTIR_CUERPO'],
            'beto' => ['prob_riesgo_depresivo' => 0.8, 'prob_sin_riesgo' => 0.2],
            'hibrido' => ['codigo' => 0, 'etiqueta_final' => 'SIN_RIESGO', 'confianza' => 0.95],
            'requiere_atencion' => true,
        ];
    }

    private function work(): void
    {
        app('queue.worker')->runNextJob('prometeo', 'prometeo-ia', new WorkerOptions(sleep: 0, maxTries: 3));
    }

    public function test_diary_save_is_async_atomic_and_job_payload_has_no_diary_text(): void
    {
        $student = $this->student();
        $text = 'Contenido privado sintético que nunca debe estar en el payload de la cola.';
        $this->actingAs($student->persona->user)->post(route('diario.store'), ['texto_ingresado' => $text])
            ->assertSessionHasNoErrors()->assertRedirect(route('diario.index'));
        Http::assertNothingSent();
        $entry = AnalisisNlp::firstOrFail();
        $this->assertSame('pendiente', $entry->estado_analisis);
        $payload = DB::table('jobs')->sole()->payload;
        $this->assertStringNotContainsString($text, $payload);
        $this->assertStringNotContainsString($student->codigo_anonimo, $payload);
        $this->assertStringNotContainsString('texto_ingresado', $payload);
        $this->get(route('diario.index'))->assertOk()->assertSee('En cola')
            ->assertSee('no indica ausencia de riesgo')->assertSee($text);
        $this->actingAs($this->student()->persona->user)->get(route('diario.index'))->assertDontSee($text);
    }

    public function test_queue_failure_rolls_back_the_entry_and_request(): void
    {
        $student = $this->student();
        Bus::shouldReceive('dispatch')->once()->andThrow(new \RuntimeException('Cola no disponible'));
        $this->actingAs($student->persona->user)->post(route('diario.store'), [
            'texto_ingresado' => 'Registro sintético para comprobar la transacción completa.',
        ])->assertRedirect(route('diario.index'));
        $this->assertDatabaseCount('analisis_nlp', 0);
        $this->assertDatabaseCount('jobs', 0);
    }

    public function test_worker_keeps_hybrid_class_confidence_and_attention_independent(): void
    {
        Http::fake(['*' => Http::response($this->response())]);
        $entry = $this->entry();
        app(AnalisisNlpQueueService::class)->solicitar($entry);
        $run = $entry->fresh()->solicitud_id;
        $this->work();
        $entry->refresh();
        $this->assertSame('completado', $entry->estado_analisis);
        $this->assertSame('pendiente', $entry->casoAtencion->estado);
        $this->assertSame('SIN_RIESGO', $entry->etiqueta_hibrida);
        $this->assertSame('SIN_RIESGO', $entry->etiqueta_roberta);
        $this->assertSame('0.9500', $entry->confianza_hibrida);
        $this->assertSame('0.8000', $entry->probabilidad_beto);
        $this->assertTrue($entry->requiere_atencion);
        $this->assertStringNotContainsString('NO_PERSISTIR_CUERPO', $entry->toJson());
        $this->assertDatabaseCount('jobs', 0);
        (new ProcesarAnalisisNlp($entry->id, $run))->handle(app(PrometeoIaService::class));
        Http::assertSentCount(1);
        Http::assertSent(fn ($request) => $request['phq9'] === 0 && $request['gad7'] === 0);
        $this->actingAs($this->account('psicologo'))->get(route('analisis.show', $entry))->assertOk()
            ->assertSee('SIN RIESGO')->assertSee('95.00')->assertSee('80.00')->assertSee('Señal de atención');
        $this->get(route('analisis.index'))->assertOk()->assertViewHas('totalRiesgo', 1)->assertViewHas('totalSinRiesgo', 0);
    }

    public function test_duplicate_and_stale_requests_cannot_overwrite_results(): void
    {
        Http::fake(['*' => Http::response($this->response())]);
        $entry = $this->entry();
        $queue = app(AnalisisNlpQueueService::class);
        $this->assertTrue($queue->solicitar($entry));
        $oldRun = $entry->fresh()->solicitud_id;
        $this->assertFalse($queue->solicitar($entry));
        $this->assertDatabaseCount('jobs', 1);
        $this->work();
        $queue->solicitar($entry);
        (new ProcesarAnalisisNlp($entry->id, $oldRun))->handle(app(PrometeoIaService::class));
        (new ProcesarAnalisisNlp($entry->id, $oldRun))->failed(new PrometeoIaException('http_500', true));
        $this->assertSame('pendiente', $entry->fresh()->estado_analisis);
        Http::assertSentCount(1);
        $this->work();
        Http::assertSentCount(2);
        $this->assertSame('completado', $entry->fresh()->estado_analisis);
    }

    public function test_transient_errors_retry_three_times_and_only_safe_metadata_is_logged(): void
    {
        Http::fake(['*' => Http::response('CUERPO_SECRETO_NO_REGISTRAR', 503)]);
        Log::spy();
        $entry = $this->entry();
        app(AnalisisNlpQueueService::class)->solicitar($entry);
        $this->work();
        $this->assertSame('pendiente', $entry->fresh()->estado_analisis);
        $this->assertSame(1, DB::table('jobs')->sole()->attempts);
        $this->travel(31)->seconds();
        $this->work();
        $this->travel(121)->seconds();
        $this->work();
        $this->assertSame('fallido', $entry->fresh()->estado_analisis);
        $this->assertSame('http_503', $entry->fresh()->error_codigo);
        $this->assertDatabaseCount('jobs', 0);
        Http::assertSentCount(3);
        Log::shouldHaveReceived('warning')->once()->with('Procesamiento IA fallido', ['analisis_id' => $entry->id, 'codigo' => 'http_503']);
        Log::shouldNotHaveReceived('error', fn ($message, $context = []) => str_contains($message.json_encode($context), 'CUERPO_SECRETO_NO_REGISTRAR'));
    }

    public function test_invalid_contract_fails_without_retry_or_erasing_previous_attention(): void
    {
        Http::fake(['*' => Http::response(['requiere_atencion' => 'false'])]);
        $entry = $this->entry([
            'estado_analisis' => 'completado', 'requiere_atencion' => true,
            'etiqueta_hibrida' => 'SIN_RIESGO', 'confianza_hibrida' => 0.95, 'probabilidad_beto' => 0.8, 'procesado_at' => now(),
        ]);
        app(AnalisisNlpQueueService::class)->solicitar($entry);
        $this->work();
        $entry->refresh();
        $this->assertSame('fallido', $entry->estado_analisis);
        $this->assertSame('contrato_invalido', $entry->error_codigo);
        $this->assertSame('0.9500', $entry->confianza_hibrida);
        $this->assertTrue($entry->requiere_atencion);
        $this->assertDatabaseCount('jobs', 0);
        Http::assertSentCount(1);
        $this->actingAs($this->account('psicologo'))->get(route('analisis.show', $entry))->assertOk()
            ->assertSee('Último resultado disponible')->assertSee('Análisis no disponible')->assertSee('Señal de atención');
    }

    public function test_contract_rejects_missing_invalid_or_inconsistent_fields(): void
    {
        $cases = [
            ['hibrido.confianza', 1.1], ['hibrido.confianza', '0.95'], ['hibrido.codigo', 1],
            ['beto.prob_riesgo_depresivo', -0.1], ['beto.prob_sin_riesgo', 0.8],
            ['requiere_atencion', null], ['requiere_atencion', 'false'], ['status', 'error'],
        ];
        foreach ($cases as [$key, $value]) {
            $data = $this->response();
            data_set($data, $key, $value);
            Http::fake(['*' => Http::response($data)]);
            try {
                app(PrometeoIaService::class)->evaluar('Texto sintético', 0);
                $this->fail('Se aceptó un contrato inválido: '.$key);
            } catch (PrometeoIaException $exception) {
                $this->assertSame('contrato_invalido', $exception->reason);
                $this->assertNull($exception->getPrevious());
            }
        }
    }

    public function test_network_exception_and_http_body_do_not_escape_service(): void
    {
        Http::fake(fn () => throw new ConnectionException('TOKEN_PRIVADO cuerpo texto'));
        try {
            app(PrometeoIaService::class)->evaluar('DIARIO_PRIVADO', 0);
            $this->fail('Debió fallar la conexión');
        } catch (PrometeoIaException $exception) {
            $this->assertSame('conexion', $exception->reason);
            $this->assertTrue($exception->retryable);
            $this->assertNull($exception->getPrevious());
            $this->assertStringNotContainsString('TOKEN_PRIVADO', (string) $exception);
            $this->assertStringNotContainsString('DIARIO_PRIVADO', (string) $exception);
        }
    }

    public function test_reanalysis_requires_psychologist_and_does_not_duplicate_queue(): void
    {
        $entry = $this->entry();
        $this->actingAs($this->account('tutor'))->post(route('analisis.reanalizar', $entry))->assertForbidden();
        $this->actingAs($this->student()->persona->user)->get(route('analisis.show', $entry))->assertForbidden();
        $this->actingAs($this->account('psicologo'))->post(route('analisis.reanalizar', $entry))->assertRedirect();
        $this->post(route('analisis.reanalizar', $entry))->assertRedirect();
        $this->post(route('analisis.reanalizar-pendientes'))->assertRedirect();
        $this->assertDatabaseCount('jobs', 1);
        Http::assertNothingSent();
        $this->get(route('analisis.index'))->assertOk()->assertSee('En cola')->assertViewHas('totalSinRiesgo', 0);
    }

    public function test_legacy_records_are_not_presented_as_verified_or_safe(): void
    {
        $entry = $this->entry(['estado_analisis' => 'legacy', 'etiqueta_roberta' => 'SIN_RIESGO', 'score_confianza' => 0.99]);
        $this->actingAs($this->account('psicologo'))->get(route('analisis.show', $entry))->assertOk()
            ->assertSee('Resultado anterior no verificado')->assertDontSee('99.00')
            ->assertSee('no indica ausencia de riesgo');
        $this->get(route('analisis.index'))->assertOk()->assertViewHas('totalSinRiesgo', 0);
    }

    public function test_worker_command_stores_only_sanitized_failure_and_payload(): void
    {
        Http::fake(['*' => Http::response('CUERPO_PRIVADO_API', 400)]);
        $entry = $this->entry();
        app(AnalisisNlpQueueService::class)->solicitar($entry);
        $this->artisan('queue:work', [
            'connection' => 'prometeo', '--queue' => 'prometeo-ia', '--once' => true, '--sleep' => 0,
        ])->assertExitCode(0);
        $failed = DB::table('failed_jobs')->sole();
        $this->assertSame('fallido', $entry->fresh()->estado_analisis);
        $this->assertStringContainsString('http_400', $failed->exception);
        $this->assertStringNotContainsString('CUERPO_PRIVADO_API', $failed->exception.$failed->payload);
        $this->assertStringNotContainsString($entry->texto_ingresado, $failed->exception.$failed->payload);
        $this->assertStringNotContainsString($entry->codigo_anonimo, $failed->payload);
    }

    public function test_pending_becomes_processing_then_completes_on_a_successful_retry(): void
    {
        $entry = $this->entry();
        $calls = 0;
        Http::fake(function () use ($entry, &$calls) {
            $this->assertSame('procesando', $entry->fresh()->estado_analisis);
            $calls++;
            if ($calls === 1) {
                return Http::response('', 429);
            }
            $data = $this->response();
            $data['requiere_atencion'] = false;

            return Http::response($data);
        });
        app(AnalisisNlpQueueService::class)->solicitar($entry);
        $this->work();
        $this->assertSame('pendiente', $entry->fresh()->estado_analisis);
        $this->travel(31)->seconds();
        $this->work();
        $this->assertSame('completado', $entry->fresh()->estado_analisis);
        $this->assertNull($entry->fresh()->error_codigo);
        $this->actingAs($this->account('psicologo'))->get(route('analisis.index'))->assertOk()
            ->assertViewHas('totalSinRiesgo', 1)->assertViewHas('totalPendientes', 0);
    }

    public function test_migration_preserves_historical_rows_without_inventing_model_results(): void
    {
        $migration = require database_path('migrations/2026_09_10_000001_add_processing_to_analisis_nlp.php');
        $legacy = $this->entry(['etiqueta_roberta' => 'RIESGO_DEPRESIVO', 'requiere_atencion' => true, 'score_confianza' => 0.9]);
        $pending = $this->entry();
        $migration->down();
        $migration->up();
        $this->assertSame('legacy', $legacy->fresh()->estado_analisis);
        $this->assertTrue($legacy->fresh()->requiere_atencion);
        $this->assertNull($legacy->fresh()->confianza_hibrida);
        $this->assertSame('fallido', $pending->fresh()->estado_analisis);
        $this->assertDatabaseCount('jobs', 0);
        $this->assertDatabaseCount('analisis_nlp', 2);
    }
}
