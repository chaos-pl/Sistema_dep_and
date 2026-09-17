<?php

namespace App\Services;

use App\Jobs\ProcesarAnalisisNlp;
use App\Models\AnalisisNlp;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AnalisisNlpQueueService
{
    public function solicitar(AnalisisNlp $analisis): bool
    {
        return DB::transaction(function () use ($analisis) {
            $entry = AnalisisNlp::lockForUpdate()->findOrFail($analisis->id);
            if (in_array($entry->estado_analisis, ['pendiente', 'procesando'], true)) {
                return false;
            }
            $run = (string) Str::uuid();
            $entry->update(['estado_analisis' => 'pendiente', 'solicitud_id' => $run, 'solicitado_at' => now(), 'error_codigo' => null]);
            // Inserción transaccional en la misma base, incluso si la cola predeterminada es sync.
            Bus::dispatch(new ProcesarAnalisisNlp($entry->id, $run));

            return true;
        });
    }
}
