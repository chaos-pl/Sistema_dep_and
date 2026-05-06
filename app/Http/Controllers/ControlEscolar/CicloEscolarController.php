<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\CicloEscolar;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CicloEscolarController extends Controller
{
    public function index()
    {
        $ciclos = CicloEscolar::withCount('grupos')->latest()->paginate(10);
        return view('control_escolar.ciclos_escolares.index', compact('ciclos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:ciclos_escolares,nombre',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
        ]);

        CicloEscolar::create($request->only('nombre', 'fecha_inicio', 'fecha_fin'));

        Alert::success('Ciclo creado', 'El ciclo escolar fue registrado correctamente.');
        return redirect()->route('control_escolar.ciclos-escolares.index');
    }

    public function update(Request $request, CicloEscolar $ciclos_escolare)
    {
        $request->validate([
            'nombre' => 'required|string|max:100|unique:ciclos_escolares,nombre,' . $ciclos_escolare->id,
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date|after:fecha_inicio',
            'estado' => 'required|in:activo,inactivo,cerrado',
        ]);

        $ciclos_escolare->update($request->only('nombre', 'fecha_inicio', 'fecha_fin', 'estado'));

        Alert::success('Ciclo actualizado', 'El ciclo escolar fue actualizado correctamente.');
        return redirect()->route('control_escolar.ciclos-escolares.index');
    }

    public function destroy(CicloEscolar $ciclos_escolare)
    {
        $ciclos_escolare->loadCount('grupos');

        if ($ciclos_escolare->grupos_count > 0) {
            Alert::warning('No permitido', 'No puedes eliminar un ciclo que tiene grupos asociados.');
            return redirect()->route('control_escolar.ciclos-escolares.index');
        }

        $ciclos_escolare->delete();

        Alert::success('Ciclo eliminado', 'El ciclo escolar fue eliminado correctamente.');
        return redirect()->route('control_escolar.ciclos-escolares.index');
    }
}
