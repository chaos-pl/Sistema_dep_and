<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Tutor;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ExistingTutorRegistrationService
{
    public function availablePeople()
    {
        return Persona::with('user.roles')->whereHas('user')
            ->whereDoesntHave('tutor', fn ($query) => $query->withTrashed())
            ->orderBy('nombre')->orderBy('apellido_paterno')->get();
    }

    public function register(array $data): Tutor
    {
        return DB::transaction(function () use ($data) {
            $persona = Persona::whereKey($data['persona_id'])->lockForUpdate()->firstOrFail();
            if (! $persona->user || $persona->tutor()->withTrashed()->exists()) {
                throw ValidationException::withMessages([
                    'persona_id' => 'La persona ya tiene expediente de tutor (incluso dado de baja) o no tiene cuenta.',
                ]);
            }
            if (! $persona->telefono && ! empty($data['telefono'])) {
                $persona->update(['telefono' => $data['telefono']]);
            }
            $tutor = Tutor::create([
                'persona_id' => $persona->id,
                'numero_empleado' => $data['numero_empleado'],
                'estado' => 'activo',
            ]);
            $persona->user->assignRole('tutor');

            return $tutor;
        });
    }
}
