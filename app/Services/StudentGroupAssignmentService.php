<?php

namespace App\Services;

use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\MovimientoEstudiante;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StudentGroupAssignmentService
{
    public function assign(Estudiante $student, ?int $groupId, int $actorId, string $reason, ?string $notes = null): bool
    {
        return DB::transaction(function () use ($student, $groupId, $actorId, $reason, $notes) {
            $current = Estudiante::lockForUpdate()->findOrFail($student->id);
            if ($groupId !== null && ! Grupo::whereKey($groupId)->where('estado', 'activo')->lockForUpdate()->first()) {
                throw ValidationException::withMessages(['grupo_id' => 'Selecciona un grupo activo y disponible.']);
            }
            $origin = $current->grupo_id === null ? null : (int) $current->grupo_id;
            if ($origin === $groupId) {
                return false;
            }
            $current->update(['grupo_id' => $groupId]);
            MovimientoEstudiante::create([
                'estudiante_id' => $current->id,
                'grupo_origen_id' => $origin,
                'grupo_destino_id' => $groupId,
                'accion' => $groupId === null ? 'quitado' : ($origin === null ? 'asignado' : 'cambiado'),
                'motivo' => $reason,
                'observaciones' => $notes,
                'realizado_por' => $actorId,
            ]);

            return true;
        });
    }
}
