<?php

namespace App\Console\Commands;

use App\Models\Dass21Evaluation;
use App\Services\Dass21EvaluationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class LinkDass21Evaluations extends Command
{
    protected $signature = 'dass21:link-evaluations {--dry-run : Mostrar únicamente cuántos registros faltan}';

    protected $description = 'Vincula resultados DASS-21 históricos sin generar alertas ni recalcular puntuaciones';

    public function handle(Dass21EvaluationService $service): int
    {
        if (! Schema::hasColumn('dass21_evaluations', 'evaluacion_id')) {
            $this->error('Primero aplica la migración link_dass21_to_evaluaciones.');

            return self::FAILURE;
        }
        $query = Dass21Evaluation::whereNull('evaluacion_id');
        $this->info('Resultados sin vínculo: '.$query->count());
        $invalid = (clone $query)->where(function ($q) {
            $q->whereDoesntHave('estudiante', fn ($s) => $s->withTrashed())
                ->orWhereDoesntHave('instrumento');
        })->count();
        if ($invalid) {
            $this->error("Hay {$invalid} resultados sin estudiante o instrumento válido. Revisa su integridad antes de convertir.");

            return self::FAILURE;
        }
        if ($this->option('dry-run')) {
            return self::SUCCESS;
        }
        $count = 0;
        $query->chunkById(100, function ($rows) use ($service, &$count) {
            foreach ($rows as $row) {
                $service->link($row);
                $count++;
            }
        });
        $this->info("Vinculados: {$count}. No se generaron alertas históricas.");

        return self::SUCCESS;
    }
}
