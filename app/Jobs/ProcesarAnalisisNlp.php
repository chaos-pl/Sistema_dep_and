<?php

namespace App\Jobs;

use App\Exceptions\PrometeoIaException;
use App\Models\AnalisisNlp;
use App\Services\CasoAtencionService;
use App\Services\PrometeoIaService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcesarAnalisisNlp implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    public int $timeout = 75;

    public bool $failOnTimeout = true;

    public function __construct(public int $analisisId, public string $solicitudId)
    {
        $this->onConnection('prometeo')->onQueue('prometeo-ia');
    }

    public function backoff(): array
    {
        return [30, 120];
    }

    public function middleware(): array
    {
        return [(new WithoutOverlapping('analisis-nlp:'.$this->analisisId))->releaseAfter(30)->expireAfter(90)];
    }

    private function current()
    {
        return AnalisisNlp::whereKey($this->analisisId)->where('solicitud_id', $this->solicitudId)
            ->whereIn('estado_analisis', ['pendiente', 'procesando']);
    }

    public function handle(PrometeoIaService $service): void
    {
        try {
            $entry = $this->current()->first();
            if (! $entry) {
                return;
            }
            $this->current()->update(['estado_analisis' => 'procesando']);
            // Compatibilidad con la API actual: ceros son placeholders, no pruebas aplicadas.
            $result = $service->evaluar($entry->texto_ingresado, 0, 0);
            DB::transaction(function () use ($result) {
                $current = $this->current()->lockForUpdate()->first();
                if (! $current) {
                    return;
                }
                $current->update($result + [
                    'etiqueta_roberta' => $result['etiqueta_hibrida'],
                    'score_confianza' => $result['confianza_hibrida'],
                    'estado_analisis' => 'completado', 'procesado_at' => now(), 'error_codigo' => null,
                ]);
                app(CasoAtencionService::class)->forAnalysis($current);
            });
        } catch (Throwable $exception) {
            $safe = $exception instanceof PrometeoIaException ? $exception : new PrometeoIaException('error_interno');
            $this->current()->update(['estado_analisis' => 'pendiente', 'error_codigo' => $safe->reason]);
            if (! $safe->retryable) {
                $this->fail($safe);

                return;
            }
            throw $safe;
        }
    }

    public function failed(?Throwable $exception): void
    {
        $reason = $exception instanceof PrometeoIaException ? $exception->reason : 'intentos_agotados';
        if ($this->current()->update(['estado_analisis' => 'fallido', 'error_codigo' => $reason])) {
            Log::warning('Procesamiento IA fallido', ['analisis_id' => $this->analisisId, 'codigo' => $reason]);
        }
    }
}
