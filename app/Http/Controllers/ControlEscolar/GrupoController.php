<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\Tutor;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with(['carrera', 'tutor.persona', 'cicloEscolar'])
            ->withCount('estudiantes')
            ->latest()
            ->paginate(10);

        $carreras = Carrera::where('estado', 'activo')->orderBy('nombre')->get();
        $tutores = Tutor::with('persona')->where('estado', 'activo')->orderBy('id', 'desc')->get();
        $ciclos = CicloEscolar::where('estado', 'activo')->orderBy('nombre', 'desc')->get();

        return view('control_escolar.grupos.index', compact('grupos', 'carreras', 'tutores', 'ciclos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'carrera_id' => 'required|exists:carreras,id',
            'tutor_id' => 'nullable|exists:tutores,id',
            'ciclo_escolar_id' => 'nullable|exists:ciclos_escolares,id',
            'periodo' => 'required|string|max:50',
        ]);

        Grupo::create($request->only('nombre', 'carrera_id', 'tutor_id', 'ciclo_escolar_id', 'periodo'));

        Alert::success('Grupo creado', 'El grupo fue registrado correctamente.');
        return redirect()->route('control_escolar.grupos.index');
    }

    public function show(Grupo $grupo)
    {
        $grupo->load([
            'carrera',
            'tutor.persona',
            'cicloEscolar',
            'estudiantes.persona.user',
        ]);

        return view('control_escolar.grupos.show', compact('grupo'));
    }

    public function update(Request $request, Grupo $grupo)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'carrera_id' => 'required|exists:carreras,id',
            'tutor_id' => 'nullable|exists:tutores,id',
            'ciclo_escolar_id' => 'nullable|exists:ciclos_escolares,id',
            'periodo' => 'required|string|max:50',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $grupo->update($request->only('nombre', 'carrera_id', 'tutor_id', 'ciclo_escolar_id', 'periodo', 'estado'));

        Alert::success('Grupo actualizado', 'Los datos del grupo fueron actualizados.');
        return redirect()->route('control_escolar.grupos.index');
    }

    public function destroy(Grupo $grupo)
    {
        $grupo->loadCount('estudiantes');

        if ($grupo->estudiantes_count > 0) {
            Alert::warning('No permitido', 'No puedes eliminar un grupo que tiene estudiantes registrados.');
            return redirect()->route('control_escolar.grupos.index');
        }

        $grupo->delete();

        Alert::success('Grupo eliminado', 'El grupo fue eliminado correctamente.');
        return redirect()->route('control_escolar.grupos.index');
    }
}
