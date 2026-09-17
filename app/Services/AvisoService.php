<?php

namespace App\Services;

use App\Models\Aviso;
use App\Models\CasoAtencion;
use App\Models\Evaluacion;
use App\Models\User;

class AvisoService
{
    public function newCase(CasoAtencion $case): void
    {
        User::role('psicologo')->whereHas('persona.psicologo')->chunkById(100, function ($users) use ($case) {
            foreach ($users as $user) {
                if (CasoAtencion::visibleTo($user)->whereKey($case->id)->exists()) {
                    Aviso::firstOrCreate(['evento' => "caso:{$case->id}:usuario:{$user->id}"], [
                        'user_id' => $user->id, 'tipo' => 'caso', 'caso_atencion_id' => $case->id,
                    ]);
                }
            }
        });
    }

    public function feedback(Evaluacion $evaluation): void
    {
        $user = $evaluation->estudiante?->persona?->user;
        if ($user && $user->hasRole('estudiante') && $user->can('retroalimentacion.ver.propia')
            && trim((string) $evaluation->diagnostico?->retroalimentacion_estudiante) !== '') {
            Aviso::firstOrCreate(['evento' => "retroalimentacion:{$evaluation->id}:usuario:{$user->id}"], [
                'user_id' => $user->id, 'tipo' => 'retroalimentacion', 'evaluacion_id' => $evaluation->id,
            ]);
        }
    }
}
