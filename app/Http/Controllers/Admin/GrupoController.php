<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreGrupoRequest;
use App\Http\Requests\Admin\UpdateGrupoRequest;
use App\Models\Carrera;
use App\Models\Grupo;
use App\Models\Tutor;
use RealRashid\SweetAlert\Facades\Alert;

class GrupoController extends Controller
{
    public function index()
    {
        $grupos = Grupo::with([
            'carrera',
            'tutor.persona',
            'estudiantes.persona.user'
        ])->withCount('estudiantes')->paginate(10);

        $carreras = Carrera::orderBy('nombre')->get();

        $tutores = Tutor::with('persona')
            ->orderBy('id', 'desc')
            ->get();

        return view('admin.grupos.index', compact('grupos', 'carreras', 'tutores'));
    }

    public function create()
    {
        $carreras = Carrera::orderBy('nombre')->get();
        $tutores = Tutor::with('persona')->orderBy('id')->get();

        return view('admin.grupos.create', compact('carreras', 'tutores'));
    }

    public function store(StoreGrupoRequest $request)
    {
        Grupo::create([
            'carrera_id' => $request->carrera_id,
            'tutor_id' => $request->tutor_id,
            'nombre' => $request->nombre,
            'periodo' => $request->periodo,
        ]);

        Alert::success('Grupo registrado', 'El grupo fue creado correctamente.');

        return redirect()->route('admin.grupos.index');
    }

    public function edit(Grupo $grupo)
    {
        $carreras = Carrera::orderBy('nombre')->get();
        $tutores = Tutor::with('persona')->orderBy('id')->get();

        $grupo->load(['carrera', 'tutor.persona']);

        return view('admin.grupos.edit', compact('grupo', 'carreras', 'tutores'));
    }

    public function update(UpdateGrupoRequest $request, Grupo $grupo)
    {
        $grupo->update([
            'carrera_id' => $request->carrera_id,
            'tutor_id' => $request->tutor_id,
            'nombre' => $request->nombre,
            'periodo' => $request->periodo,
        ]);

        Alert::success('Grupo actualizado', 'Los datos del grupo fueron actualizados correctamente.');

        return redirect()->route('admin.grupos.index');
    }
    public function show(Grupo $grupo)
    {
        $grupo->load([
            'carrera',
            'tutor.persona',
            'estudiantes.persona.user',
        ]);

        return view('admin.grupos.show', compact('grupo'));
    }

    public function destroy(Grupo $grupo)
    {
        $grupo->loadCount('estudiantes');

        if ($grupo->estudiantes_count > 0) {
            Alert::warning('No permitido', 'No puedes eliminar un grupo que ya tiene estudiantes registrados.');
            return redirect()->route('admin.grupos.index');
        }

        $grupo->delete();

        Alert::success('Grupo eliminado', 'El grupo fue eliminado correctamente.');

        return redirect()->route('admin.grupos.index');
    }
}
