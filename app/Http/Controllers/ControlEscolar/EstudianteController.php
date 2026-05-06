<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\MovimientoEstudiante;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RealRashid\SweetAlert\Facades\Alert;

class EstudianteController extends Controller
{
    /**
     * Lista general de estudiantes.
     */
    public function index()
    {
        $estudiantes = Estudiante::with(['persona.user', 'grupo.carrera'])
            ->leftJoin('personas', 'estudiantes.persona_id', '=', 'personas.id')
            ->orderBy('personas.nombre')
            ->select('estudiantes.*')
            ->paginate(15);

        $grupos = Grupo::with('carrera')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('control_escolar.estudiantes.index', compact('estudiantes', 'grupos'));
    }

    /**
     * Estudiantes pendientes de asignación de grupo.
     */
    public function pendientes()
    {
        // Tipo 1: Estudiantes con expediente pero sin grupo asignado
        $sinGrupo = Estudiante::with(['persona.user', 'grupo'])
            ->whereNull('grupo_id')
            ->leftJoin('personas', 'estudiantes.persona_id', '=', 'personas.id')
            ->orderBy('personas.nombre')
            ->select('estudiantes.*')
            ->paginate(15, ['*'], 'sin_grupo');

        // Tipo 2: Users con rol estudiante que no tienen expediente (Persona → Estudiante)
        $sinExpediente = User::role('estudiante')
            ->with('persona')
            ->where(function ($q) {
                // No tiene persona
                $q->whereDoesntHave('persona')
                // O tiene persona pero no tiene estudiante
                ->orWhereDoesntHave('persona.estudiante');
            })
            ->latest()
            ->paginate(15, ['*'], 'sin_expediente');

        $grupos = Grupo::with('carrera')
            ->where('estado', 'activo')
            ->orderBy('nombre')
            ->get();

        return view('control_escolar.estudiantes.pendientes', compact('sinGrupo', 'sinExpediente', 'grupos'));
    }

    /**
     * Asignar grupo desde la vista de pendientes.
     */
    public function asignarGrupo(Request $request, Estudiante $estudiante)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'grupo_id.required' => 'Debes seleccionar un grupo.',
        ]);

        // Evitar asignación duplicada
        if ($estudiante->grupo_id == $request->grupo_id) {
            Alert::warning('Sin cambios', 'El estudiante ya pertenece a ese grupo.');
            return redirect()->route('control_escolar.pendientes.index');
        }

        MovimientoEstudiante::create([
            'estudiante_id' => $estudiante->id,
            'grupo_origen_id' => null,
            'grupo_destino_id' => $request->grupo_id,
            'accion' => 'asignado',
            'motivo' => 'Asignación inicial de grupo',
            'observaciones' => $request->observaciones,
            'realizado_por' => auth()->id(),
        ]);

        $estudiante->update(['grupo_id' => $request->grupo_id]);

        $grupoName = Grupo::find($request->grupo_id)->nombre;
        Alert::success('Grupo asignado', "El estudiante fue asignado al grupo {$grupoName}.");
        return redirect()->route('control_escolar.pendientes.index');
    }

    /**
     * Formulario para crear un estudiante.
     */
    public function create()
    {
        $grupos = Grupo::with('carrera')->where('estado', 'activo')->orderBy('nombre')->get();
        $carreras = Carrera::where('estado', 'activo')->orderBy('nombre')->get();

        return view('control_escolar.estudiantes.create', compact('grupos', 'carreras'));
    }

    /**
     * Guardar nuevo estudiante.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'fecha_nacimiento' => 'required|date',
            'genero' => 'required|in:masculino,femenino,otro,prefiero_no_decirlo',
            'telefono' => 'nullable|string|max:20',
            'matricula' => 'required|string|max:50|unique:estudiantes,matricula',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'grupo_id' => 'nullable|exists:grupos,id',
        ]);

        DB::transaction(function () use ($request) {
            $nombreCompleto = trim($request->nombre . ' ' . $request->apellido_paterno . ' ' . ($request->apellido_materno ?? ''));

            $user = User::create([
                'name' => $nombreCompleto,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('estudiante');

            $persona = Persona::create([
                'user_id' => $user->id,
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'genero' => $request->genero,
                'telefono' => $request->telefono,
            ]);

            Estudiante::create([
                'persona_id' => $persona->id,
                'matricula' => $request->matricula,
                'grupo_id' => $request->grupo_id,
                'codigo_anonimo' => 'EST-' . Str::random(8),
                'estado' => 'activo',
            ]);
        });

        Alert::success('Estudiante registrado', 'El estudiante fue dado de alta correctamente.');
        return redirect()->route('control_escolar.estudiantes.index');
    }

    /**
     * Formulario para editar datos escolares de un estudiante.
     */
    public function edit(Estudiante $estudiante)
    {
        $estudiante->load('persona.user');
        $grupos = Grupo::with('carrera')->where('estado', 'activo')->orderBy('nombre')->get();

        return view('control_escolar.estudiantes.edit', compact('estudiante', 'grupos'));
    }

    /**
     * Actualizar datos escolares (solo académicos, no clínicos).
     */
    public function update(Request $request, Estudiante $estudiante)
    {
        $estudiante->load('persona.user');

        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido_paterno' => 'required|string|max:100',
            'apellido_materno' => 'nullable|string|max:100',
            'matricula' => 'required|string|max:50|unique:estudiantes,matricula,' . $estudiante->id,
            'estado' => 'required|in:activo,baja_temporal,baja_definitiva',
        ]);

        DB::transaction(function () use ($request, $estudiante) {
            $estudiante->persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
            ]);

            $nombreCompleto = trim($request->nombre . ' ' . $request->apellido_paterno . ' ' . ($request->apellido_materno ?? ''));
            $estudiante->persona->user->update(['name' => $nombreCompleto]);

            $estudiante->update([
                'matricula' => $request->matricula,
                'estado' => $request->estado,
            ]);
        });

        Alert::success('Estudiante actualizado', 'Los datos fueron actualizados correctamente.');
        return redirect()->route('control_escolar.estudiantes.index');
    }

    /**
     * Soft-delete de un estudiante (solo control_escolar no puede eliminar definitivamente).
     */
    public function destroy(Estudiante $estudiante)
    {
        // Solo soft-delete, nunca eliminación definitiva
        $estudiante->delete();

        Alert::success('Estudiante dado de baja', 'El estudiante fue dado de baja del sistema.');
        return redirect()->route('control_escolar.estudiantes.index');
    }

    /**
     * Cambiar grupo de un estudiante.
     */
    public function updateGrupo(Request $request, Estudiante $estudiante)
    {
        $request->validate([
            'grupo_id' => 'nullable|exists:grupos,id',
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'motivo.required' => 'Debes registrar un motivo para el movimiento.',
        ]);

        // No permitir cambiar al mismo grupo
        if ($request->grupo_id && $estudiante->grupo_id == $request->grupo_id) {
            Alert::warning('Sin cambios', 'El estudiante ya pertenece a ese grupo.');
            return redirect()->route('control_escolar.estudiantes.index');
        }

        $grupoOrigenId = $estudiante->grupo_id;
        $accion = $request->grupo_id ? 'cambiado' : 'quitado';

        MovimientoEstudiante::create([
            'estudiante_id' => $estudiante->id,
            'grupo_origen_id' => $grupoOrigenId,
            'grupo_destino_id' => $request->grupo_id,
            'accion' => $accion,
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones,
            'realizado_por' => auth()->id(),
        ]);

        $estudiante->update(['grupo_id' => $request->grupo_id]);

        if ($request->grupo_id) {
            $grupoName = Grupo::find($request->grupo_id)->nombre;
            Alert::success('Grupo cambiado', "El estudiante fue asignado al grupo {$grupoName}.");
        } else {
            Alert::success('Grupo removido', 'El estudiante ha sido removido de su grupo.');
        }

        return redirect()->route('control_escolar.estudiantes.index');
    }

    /**
     * Quitar estudiante de su grupo sin eliminarlo.
     */
    public function quitarGrupo(Request $request, Estudiante $estudiante)
    {
        $request->validate([
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ], [
            'motivo.required' => 'Debes registrar un motivo para quitar al estudiante del grupo.',
        ]);

        if (!$estudiante->grupo_id) {
            Alert::info('Sin grupo', 'El estudiante ya no pertenece a ningún grupo.');
            return redirect()->route('control_escolar.estudiantes.index');
        }

        MovimientoEstudiante::create([
            'estudiante_id' => $estudiante->id,
            'grupo_origen_id' => $estudiante->grupo_id,
            'grupo_destino_id' => null,
            'accion' => 'quitado',
            'motivo' => $request->motivo,
            'observaciones' => $request->observaciones,
            'realizado_por' => auth()->id(),
        ]);

        $estudiante->update(['grupo_id' => null]);

        Alert::success('Grupo removido', 'El estudiante fue removido de su grupo y queda pendiente de asignación.');
        return redirect()->route('control_escolar.estudiantes.index');
    }

    /**
     * Historial de movimientos de un estudiante.
     */
    public function historial(Estudiante $estudiante)
    {
        $estudiante->load('persona.user');

        $movimientos = MovimientoEstudiante::where('estudiante_id', $estudiante->id)
            ->with(['grupoOrigen', 'grupoDestino', 'realizadoPor'])
            ->latest()
            ->paginate(15);

        return view('control_escolar.estudiantes.historial', compact('estudiante', 'movimientos'));
    }
}
