<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'carrera_id',
        'tutor_id',
        'ciclo_escolar_id',
        'nombre',
        'periodo',
        'estado',
    ];

    public function carrera()
    {
        return $this->belongsTo(Carrera::class);
    }

    /** La asignación por ciclo tiene precedencia sobre la relación antigua. */
    public function scopeVisibleToTutor($query, Tutor $tutor)
    {
        return $query->where('grupos.estado', 'activo')->where(function ($q) use ($tutor) {
            $q->whereExists(function ($pivot) use ($tutor) {
                $pivot->selectRaw('1')->from('grupo_tutor')
                    ->whereColumn('grupo_tutor.grupo_id', 'grupos.id')
                    ->where('grupo_tutor.tutor_id', $tutor->id)
                    ->where('grupo_tutor.estado', 'activo')->whereNull('grupo_tutor.deleted_at')
                    ->where(function ($cycle) {
                        $cycle->whereNull('grupo_tutor.ciclo_escolar_id')->orWhereExists(function ($active) {
                            $active->selectRaw('1')->from('ciclos_escolares')
                                ->whereColumn('ciclos_escolares.id', 'grupo_tutor.ciclo_escolar_id')
                                ->whereColumn('ciclos_escolares.id', 'grupos.ciclo_escolar_id')
                                ->where('ciclos_escolares.estado', 'activo')->whereNull('ciclos_escolares.deleted_at')
                                ->whereDate('fecha_inicio', '<=', today())->whereDate('fecha_fin', '>=', today());
                        });
                    });
            })->orWhere(function ($legacy) use ($tutor) {
                $legacy->where('grupos.tutor_id', $tutor->id)->whereNotExists(function ($pivot) {
                    // No reactivar el acceso antiguo si una asignación fue revocada.
                    $pivot->selectRaw('1')->from('grupo_tutor')->whereColumn('grupo_tutor.grupo_id', 'grupos.id');
                });
            });
        });
    }

    // Relación directa legacy (retrocompatibilidad con módulo Tutor existente)
    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function cicloEscolar()
    {
        return $this->belongsTo(CicloEscolar::class, 'ciclo_escolar_id');
    }

    public function estudiantes()
    {
        return $this->hasMany(Estudiante::class);
    }

    // Relación pivote: tutores asignados por ciclo escolar
    public function tutoresAsignados()
    {
        return $this->belongsToMany(Tutor::class, 'grupo_tutor')
            ->withPivot('ciclo_escolar_id', 'estado')
            ->withTimestamps();
    }
}
