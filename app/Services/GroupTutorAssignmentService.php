<?php

namespace App\Services;

use App\Models\Grupo;
use App\Models\GrupoTutor;
use Illuminate\Support\Facades\DB;

class GroupTutorAssignmentService
{
    /** Un responsable actual por grupo; conservar las asignaciones anteriores como historial. */
    public function save(Grupo $grupo, array $attributes): Grupo
    {
        return DB::transaction(function () use ($grupo, $attributes) {
            $locked = $grupo->exists ? Grupo::whereKey($grupo->id)->lockForUpdate()->firstOrFail() : $grupo;
            $locked->fill($attributes)->save();

            GrupoTutor::where('grupo_id', $locked->id)->where('estado', 'activo')->update(['estado' => 'inactivo']);
            if ($locked->tutor_id) {
                $assignment = GrupoTutor::withTrashed()->firstOrNew([
                    'grupo_id' => $locked->id, 'tutor_id' => $locked->tutor_id,
                    'ciclo_escolar_id' => $locked->ciclo_escolar_id,
                ]);
                $assignment->estado = 'activo';
                $assignment->deleted_at = null;
                $assignment->save();
            }

            return $locked;
        });
    }

    public function remove(GrupoTutor $assignment): void
    {
        DB::transaction(function () use ($assignment) {
            $grupo = Grupo::whereKey($assignment->grupo_id)->lockForUpdate()->firstOrFail();
            $assignment->refresh();
            if ($assignment->estado === 'activo' && $grupo->tutor_id === $assignment->tutor_id
                && $grupo->ciclo_escolar_id === $assignment->ciclo_escolar_id) {
                $grupo->update(['tutor_id' => null]);
            }
            $assignment->delete();
        });
    }
}
