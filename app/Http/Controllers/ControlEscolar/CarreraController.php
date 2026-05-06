<?php

namespace App\Http\Controllers\ControlEscolar;

use App\Http\Controllers\Controller;
use App\Models\Carrera;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::withCount('grupos')->latest()->paginate(10);
        return view('control_escolar.carreras.index', compact('carreras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'clave' => 'nullable|string|max:50|unique:carreras,clave',
        ]);

        Carrera::create($request->only('nombre', 'clave'));

        Alert::success('Carrera registrada', 'La carrera fue creada correctamente.');
        return redirect()->route('control_escolar.carreras.index');
    }

    public function update(Request $request, Carrera $carrera)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'clave' => 'nullable|string|max:50|unique:carreras,clave,' . $carrera->id,
            'estado' => 'required|in:activo,inactivo',
        ]);

        $carrera->update($request->only('nombre', 'clave', 'estado'));

        Alert::success('Carrera actualizada', 'Los datos fueron actualizados correctamente.');
        return redirect()->route('control_escolar.carreras.index');
    }

    public function destroy(Carrera $carrera)
    {
        $carrera->loadCount('grupos');

        if ($carrera->grupos_count > 0) {
            Alert::warning('No permitido', 'No puedes eliminar una carrera que ya tiene grupos registrados.');
            return redirect()->route('control_escolar.carreras.index');
        }

        $carrera->delete();

        Alert::success('Carrera eliminada', 'La carrera fue eliminada correctamente.');
        return redirect()->route('control_escolar.carreras.index');
    }
}
