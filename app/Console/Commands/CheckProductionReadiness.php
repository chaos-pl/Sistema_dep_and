<?php

namespace App\Console\Commands;

use App\Services\ProductionReadinessService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Throwable;

class CheckProductionReadiness extends Command
{
    protected $signature = 'prometeo:comprobar-produccion {--database : Consultar esquema y migraciones, sin modificar datos}';

    protected $description = 'Comprueba preparación de producción sin mostrar configuración sensible ni llamar a la API IA';

    public function handle(ProductionReadinessService $service): int
    {
        $checks = $service->checks();
        if ($this->option('database')) {
            try {
                DB::select('SELECT 1');
                $checks['Conexión a base de datos'] = true;
                $applied = Schema::hasTable('migrations') ? DB::table('migrations')->pluck('migration')->all() : [];
                $expected = array_map(fn ($path) => basename($path, '.php'), glob(database_path('migrations/*.php')));
                $checks['Migraciones del repositorio aplicadas'] = count(array_diff($expected, $applied)) === 0;
                $checks['Tablas operativas presentes'] = collect(['jobs', 'failed_jobs', 'cache', 'cache_locks', 'avisos', 'casos_atencion'])
                    ->every(fn ($table) => Schema::hasTable($table));
            } catch (Throwable) {
                $checks['Consulta de base de datos y esquema'] = false;
            }
        }
        foreach ($checks as $label => $passed) {
            $this->line(($passed ? 'OK' : 'PENDIENTE').' | '.$label);
        }
        $this->line('No verifica disponibilidad de IA/correo, proceso trabajador, TLS externo ni restauración de respaldos.');

        return in_array(false, $checks, true) ? self::FAILURE : self::SUCCESS;
    }
}
