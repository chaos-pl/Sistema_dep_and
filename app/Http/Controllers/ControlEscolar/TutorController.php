<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\Persona;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use RealRashid\SweetAlert\Facades\Alert;

class TutorController extends Controller
{
    public function index()
    {
        $tutores = Tutor::with(['persona.user'])
            ->withCount('grupos')
            ->latest()
            ->paginate(10);

        return view('control_escolar.tutores.index', compact('tutores'));
    }

    public function create()
    {
        return view('control_escolar.tutores.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:masculino,femenino,otro,prefiero_no_decirlo',
            'telefono' => 'nullable|string|max:20',
            'numero_empleado' => 'required|string|max:50|unique:tutores,numero_empleado',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        DB::transaction(function () use ($request) {
            $nombreCompleto = trim($request->nombre . ' ' . $request->apellido_paterno . ' ' . ($request->apellido_materno ?? ''));

            $user = User::create([
                'name' => $nombreCompleto,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('tutor');

            $persona = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            Tutor::create([
                'persona_id' => $persona->id,
                'numero_empleado' => $request->numero_empleado,
            ]);
        });

        Alert::success('Tutor registrado', 'El tutor fue dado de alta correctamente.');
        return redirect()->route('control_escolar.tutores.index');
    }

    public function edit(Tutor $tutor)
    {
        $tutor->load('persona.user');

        return view('control_escolar.tutores.edit', compact('tutor'));
    }

    public function update(Request $request, Tutor $tutor)
    {
        $tutor->load('persona.user');

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'numero_empleado' => 'required|string|max:50|unique:tutores,numero_empleado,' . $tutor->id,
            'email' => 'required|email|max:255|unique:users,email,' . $tutor->persona->user->id,
        ]);

        DB::transaction(function () use ($request, $tutor) {
            $nombreCompleto = trim($request->nombre . ' ' . $request->apellido_paterno . ' ' . ($request->apellido_materno ?? ''));

            $tutor->persona->user->update([
                'name' => $nombreCompleto,
                'email' => $request->email,
            ]);

            $tutor->persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
            ]);

            $tutor->update([
                'numero_empleado' => $request->numero_empleado,
            ]);
        });

        Alert::success('Tutor actualizado', 'Los datos del tutor fueron actualizados.');
        return redirect()->route('control_escolar.tutores.index');
    }

    /**
     * Soft-delete: control_escolar NO puede eliminar definitivamente.
     */
    public function destroy(Tutor $tutor)
    {
        $tutor->loadCount('grupos');

        if ($tutor->grupos_count > 0) {
            Alert::warning('No permitido', 'No puedes dar de baja un tutor que tiene grupos asignados.');
            return redirect()->route('control_escolar.tutores.index');
        }

        $tutor->delete(); // soft-delete

        Alert::success('Tutor dado de baja', 'El tutor fue dado de baja del sistema.');
        return redirect()->route('control_escolar.tutores.index');
    }
}
