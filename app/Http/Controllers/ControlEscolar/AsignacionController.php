<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use App\Models\Grupo;
use App\Models\GrupoTutor;
use App\Models\Tutor;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class AsignacionController extends Controller
{
    public function index()
    {
        $asignaciones = GrupoTutor::with(['grupo.carrera', 'tutor.persona', 'cicloEscolar'])
            ->latest()
            ->paginate(15);

        $grupos = Grupo::where('estado', 'activo')->orderBy('nombre')->get();
        $tutores = Tutor::with('persona')->where('estado', 'activo')->get();
        $ciclos = CicloEscolar::where('estado', 'activo')->orderBy('nombre', 'desc')->get();

        return view('control_escolar.asignaciones.index', compact('asignaciones', 'grupos', 'tutores', 'ciclos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'grupo_id' => 'required|exists:grupos,id',
            'tutor_id' => 'required|exists:tutores,id',
            'ciclo_escolar_id' => 'nullable|exists:ciclos_escolares,id',
        ]);

        $exists = GrupoTutor::where('grupo_id', $request->grupo_id)
            ->where('tutor_id', $request->tutor_id)
            ->where('ciclo_escolar_id', $request->ciclo_escolar_id)
            ->whereNull('deleted_at')
            ->exists();

        if ($exists) {
            Alert::warning('Ya existe', 'Esta asignación de tutor-grupo ya está registrada.');
            return redirect()->route('control_escolar.asignaciones.index');
        }

        GrupoTutor::create($request->only('grupo_id', 'tutor_id', 'ciclo_escolar_id'));

        Alert::success('Asignación creada', 'El tutor fue asignado al grupo correctamente.');
        return redirect()->route('control_escolar.asignaciones.index');
    }

    public function destroy(GrupoTutor $asignacione)
    {
        $asignacione->delete();

        Alert::success('Asignación eliminada', 'La asignación fue removida correctamente.');
        return redirect()->route('control_escolar.asignaciones.index');
    }
}
