<?php

namespace App\Services;

use App\Models\Grupo;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Dass21CoverageService
{
    public function period(Request $request): array
    {
        $data = $request->validate([
            'desde' => ['nullable', 'date_format:Y-m-d'],
            'hasta' => ['nullable', 'date_format:Y-m-d'],
        ]);
        $from = Carbon::parse($data['desde'] ?? now()->startOfMonth()->toDateString())->startOfDay();
        $to = Carbon::parse($data['hasta'] ?? now()->toDateString())->endOfDay();
        if ($to->lt($from)) {
            throw ValidationException::withMessages(['hasta' => 'La fecha final debe ser igual o posterior a la inicial.']);
        }

        return [$from, $to];
    }

    public function groups(Builder $query, array $period)
    {
        return $query->with(['carrera', 'cicloEscolar'])->withCount([
            'estudiantes as participantes' => fn ($q) => $q->where('estado', 'activo'),
            'estudiantes as completadas' => fn ($q) => $q->where('estado', 'activo')
                ->whereHas('dass21Evaluations', fn ($d) => $d->whereBetween('completed_at', $period)),
        ])->orderBy('nombre')->get()->each(function (Grupo $grupo) {
            $grupo->pendientes = max(0, $grupo->participantes - $grupo->completadas);
            $grupo->porcentaje = $grupo->participantes ? round(100 * $grupo->completadas / $grupo->participantes, 1) : 0;
        });
    }
}
