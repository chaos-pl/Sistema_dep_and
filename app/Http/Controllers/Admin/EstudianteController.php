<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Services\StudentGroupAssignmentService;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use RealRashid\SweetAlert\Facades\Alert;

class EstudianteController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:usuarios.ver', only: ['index']),
        ];
    }

    public function index()
    {
        // Traer todos los estudiantes con su persona, usuario y grupo actual
        $estudiantes = Estudiante::with(['persona.user', 'grupo.carrera'])
            ->leftJoin('personas', 'estudiantes.persona_id', '=', 'personas.id')
            ->orderBy('personas.nombre')
            ->select('estudiantes.*') // Evitar sobreescribir id con el de persona
            ->paginate(10);

        // Grupos disponibles para el select, ordenados por nombre y periodo
        $grupos = Grupo::with('carrera')->orderBy('nombre')->get();

        return view('admin.estudiantes.index', compact('estudiantes', 'grupos'));
    }

    public function updateGrupo(Request $request, Estudiante $estudiante, StudentGroupAssignmentService $assignments)
    {
        $request->validate([
            'grupo_id' => 'nullable|exists:grupos,id',
        ], [
            'grupo_id.exists' => 'El grupo seleccionado no es válido.',
        ]);

        $assignments->assign($estudiante, $request->filled('grupo_id') ? (int) $request->grupo_id : null,
            auth()->id(), 'Actualización de grupo desde administración');

        if ($request->grupo_id) {
            $grupoName = Grupo::find($request->grupo_id)->nombre;
            Alert::success('Grupo asignado', "El estudiante fue asignado al grupo {$grupoName}.");
        } else {
            Alert::success('Grupo removido', 'El estudiante ha sido removido de su grupo.');
        }

        return redirect()->route('admin.estudiantes.index');
    }
}
