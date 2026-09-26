<?php

namespace Tests\Feature;

use App\Services\ProductionReadinessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProductionReadinessTest extends TestCase
{
    use RefreshDatabase;

    public function test_local_settings_are_flagged_without_network_or_secret_output(): void
    {
        config(['app.debug' => true, 'app.url' => 'http://private-url.test',
            'services.prometeo_ia.url' => 'http://secret-api.test/token', 'app.key' => 'SECRET-DO-NOT-PRINT']);
        Http::fake();
        $this->artisan('prometeo:comprobar-produccion')->expectsOutput('PENDIENTE | APP_DEBUG desactivado')
            ->expectsOutput('PENDIENTE | API IA configurada con HTTPS')->doesntExpectOutputToContain('SECRET-DO-NOT-PRINT')
            ->doesntExpectOutputToContain('secret-api.test')->assertExitCode(1);
        Http::assertNothingSent();
    }

    public function test_configuration_checks_reject_sync_queue_and_insecure_cookies(): void
    {
        config(['queue.connections.prometeo.driver' => 'sync', 'session.secure' => false]);
        $checks = app(ProductionReadinessService::class)->checks();
        $this->assertFalse($checks['Cola IA en la misma base y reserva mayor que timeout']);
        $this->assertFalse($checks['Cookie de sesión segura y HttpOnly']);
        config(['queue.connections.prometeo.driver' => 'database', 'session.secure' => true, 'session.http_only' => true]);
        $checks = app(ProductionReadinessService::class)->checks();
        $this->assertTrue($checks['Cola IA en la misma base y reserva mayor que timeout']);
        $this->assertTrue($checks['Cookie de sesión segura y HttpOnly']);
    }

    public function test_database_option_reads_metadata_without_writing_and_detects_pending_migrations(): void
    {
        $before = DB::table('migrations')->count();
        $this->artisan('prometeo:comprobar-produccion', ['--database' => true])
            ->expectsOutput('OK | Migraciones del repositorio aplicadas')->assertExitCode(1);
        $this->assertSame($before, DB::table('migrations')->count());
        DB::table('migrations')->where('migration', '2026_09_15_000001_add_evolution_reports_and_notices')->delete();
        $this->artisan('prometeo:comprobar-produccion', ['--database' => true])
            ->expectsOutput('PENDIENTE | Migraciones del repositorio aplicadas')->assertExitCode(1);
        $this->assertSame($before - 1, DB::table('migrations')->count());
    }

    public function test_success_exit_requires_all_checks_to_pass(): void
    {
        $this->mock(ProductionReadinessService::class)->shouldReceive('checks')->once()->andReturn(['Configuración validada' => true]);
        $this->artisan('prometeo:comprobar-produccion')->expectsOutput('OK | Configuración validada')->assertExitCode(0);
    }
}
