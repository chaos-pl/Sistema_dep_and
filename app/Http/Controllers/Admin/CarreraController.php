<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCarreraRequest;
use App\Http\Requests\Admin\UpdateCarreraRequest;
use App\Models\Carrera;
use RealRashid\SweetAlert\Facades\Alert;

class CarreraController extends Controller
{
    public function index()
    {
        $carreras = Carrera::withCount('grupos')
            ->latest()
            ->paginate(10);

        return view('admin.carreras.index', compact('carreras'));
    }

    public function create()
    {
        return view('admin.carreras.create');
    }

    public function store(StoreCarreraRequest $request)
    {
        Carrera::create([
            'nombre' => $request->nombre,
        ]);

        Alert::success('Carrera registrada', 'La carrera fue creada correctamente.');

        return redirect()->route('admin.carreras.index');
    }

    public function edit(Carrera $carrera)
    {
        $carrera->loadCount('grupos');

        return view('admin.carreras.edit', compact('carrera'));
    }

    public function update(UpdateCarreraRequest $request, Carrera $carrera)
    {
        $carrera->update([
            'nombre' => $request->nombre,
        ]);

        Alert::success('Carrera actualizada', 'La carrera fue actualizada correctamente.');

        return redirect()->route('admin.carreras.index');
    }

    public function destroy(Carrera $carrera)
    {
        $carrera->loadCount('grupos');

        if ($carrera->grupos_count > 0) {
            Alert::warning('No permitido', 'No puedes eliminar una carrera que ya tiene grupos registrados.');
            return redirect()->route('admin.carreras.index');
        }

        $carrera->delete();

        Alert::success('Carrera eliminada', 'La carrera fue eliminada correctamente.');

        return redirect()->route('admin.carreras.index');
    }
}
