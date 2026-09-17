<?php

namespace App\Services;

use App\Models\Estudiante;
use App\Models\Evaluacion;

class EvaluationContextService
{
    /** Solo se llama al responder; nunca al vincular aplicaciones históricas. */
    public function capture(Evaluacion $evaluation, Estudiante $student): void
    {
        if ($evaluation->contexto_registrado_at !== null) {
            return;
        }
        $student = Estudiante::whereKey($student->id)->lockForUpdate()->firstOrFail();
        $group = $student->grupo()->with(['carrera', 'cicloEscolar'])->first();
        $evaluation->forceFill([
            'contexto_registrado_at' => now(),
            'grupo_aplicacion_id' => $group?->id,
            'grupo_aplicacion_nombre' => $group?->nombre,
            'carrera_aplicacion_id' => $group?->carrera?->id,
            'carrera_aplicacion_nombre' => $group?->carrera?->nombre,
            'ciclo_aplicacion_id' => $group?->cicloEscolar?->id,
            'ciclo_aplicacion_nombre' => $group?->cicloEscolar?->nombre,
        ])->save();
    }
}
