<?php

namespace App\Console\Commands;

use App\Models\AnalisisNlp;
use App\Models\Evaluacion;
use App\Services\CasoAtencionService;
use Illuminate\Console\Command;

class SyncAttentionCases extends Command
{
    protected $signature = 'casos:sincronizar {--dry-run : Mostrar únicamente cantidades}';

    protected $description = 'Vincula alertas, valoraciones y señales IA históricas con casos de atención';

    public function handle(CasoAtencionService $service): int
    {
        $evaluations = Evaluacion::where(fn ($q) => $q->has('alerta')->orHas('diagnostico'))->doesntHave('casoAtencion');
        $analyses = AnalisisNlp::where('requiere_atencion', true)->doesntHave('casoAtencion');
        $this->info('Evaluaciones sin caso: '.$evaluations->count().'. Diarios sin caso: '.$analyses->count().'.');
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }
        $evaluations->chunkById(100, fn ($rows) => $rows->each(fn ($row) => $service->forEvaluation($row, false)));
        $analyses->chunkById(100, fn ($rows) => $rows->each(fn ($row) => $service->forAnalysis($row, false)));
        $this->info('Sincronización terminada. Los históricos valorados quedan en seguimiento, no cerrados.');

        return self::SUCCESS;
    }
}
