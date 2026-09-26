<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\MovimientoEstudiante;
use App\Models\Persona;
use App\Models\User;
use App\Services\StudentGroupAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;

class EstudianteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:estudiantes.ver', only: ['index']),
            new Middleware('permission:estudiantes.ver_pendientes', only: ['pendientes']),
            new Middleware('permission:estudiantes.crear', only: ['create']),
            new Middleware('permission:estudiantes.crear', only: ['store']),
            new Middleware('permission:estudiantes.editar', only: ['edit']),
            new Middleware('permission:estudiantes.editar', only: ['update']),
            new Middleware('permission:usuarios.eliminar', only: ['destroy']),
            new Middleware('permission:estudiantes.asignar_grupo', only: ['asignarGrupo']),
            new Middleware('permission:estudiantes.cambiar_grupo', only: ['updateGrupo']),
            new Middleware('permission:estudiantes.quitar_grupo', only: ['quitarGrupo']),
            new Middleware('permission:estudiantes.ver_historial', only: ['historial']),
        ];
    }

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
    public function asignarGrupo(Request $request, Estudiante $estudiante, StudentGroupAssignmentService $assignments)
    {
        $data = $request->validate([
            'grupo_id' => 'required|integer|exists:grupos,id',
            'observaciones' => 'nullable|string|max:1000',
        ]);
        $changed = $assignments->assign($estudiante, (int) $data['grupo_id'], auth()->id(),
            'Asignación de grupo desde pendientes', $data['observaciones'] ?? null);
        Alert::info($changed ? 'Grupo asignado' : 'Sin cambios', $changed
            ? 'La asignación y su movimiento se guardaron correctamente.'
            : 'El estudiante ya pertenece a ese grupo.');

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
            'grupo_id' => ['nullable', Rule::exists('grupos', 'id')->where('estado', 'activo')->whereNull('deleted_at')],
        ]);

        DB::transaction(function () use ($request) {
            $nombreCompleto = trim($request->nombre.' '.$request->apellido_paterno.' '.($request->apellido_materno ?? ''));

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
                'codigo_anonimo' => 'EST-'.Str::random(8),
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
            'matricula' => 'required|string|max:50|unique:estudiantes,matricula,'.$estudiante->id,
            'estado' => 'required|in:activo,baja_temporal,baja_definitiva',
        ]);

        DB::transaction(function () use ($request, $estudiante) {
            $estudiante->persona->update([
                'nombre' => $request->nombre,
                'apellido_paterno' => $request->apellido_paterno,
                'apellido_materno' => $request->apellido_materno,
            ]);

            $nombreCompleto = trim($request->nombre.' '.$request->apellido_paterno.' '.($request->apellido_materno ?? ''));
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
    public function updateGrupo(Request $request, Estudiante $estudiante, StudentGroupAssignmentService $assignments)
    {
        $data = $request->validate([
            'grupo_id' => 'nullable|integer|exists:grupos,id',
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ]);
        $groupId = isset($data['grupo_id']) ? (int) $data['grupo_id'] : null;
        $changed = $assignments->assign($estudiante, $groupId, auth()->id(), $data['motivo'], $data['observaciones'] ?? null);
        Alert::info($changed ? 'Asignación actualizada' : 'Sin cambios', $changed
            ? 'El grupo y su movimiento se guardaron correctamente.'
            : 'El estudiante ya tiene esa asignación.');

        return redirect()->route('control_escolar.estudiantes.index');
    }

    /**
     * Quitar estudiante de su grupo sin eliminarlo.
     */
    public function quitarGrupo(Request $request, Estudiante $estudiante, StudentGroupAssignmentService $assignments)
    {
        $data = $request->validate([
            'motivo' => 'required|string|max:500',
            'observaciones' => 'nullable|string|max:1000',
        ]);
        $changed = $assignments->assign($estudiante, null, auth()->id(), $data['motivo'], $data['observaciones'] ?? null);
        Alert::info($changed ? 'Grupo removido' : 'Sin grupo', $changed
            ? 'El estudiante queda pendiente de asignación.'
            : 'El estudiante ya no pertenece a ningún grupo.');

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
