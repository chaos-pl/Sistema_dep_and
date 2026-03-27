<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePersonaRequest;
use App\Http\Requests\UpdatePersonaRequest;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use RealRashid\SweetAlert\Facades\Alert;

class PersonaController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:personas.ver', only: ['index']),
            new Middleware('permission:personas.crear', only: ['create', 'store']),
            new Middleware('permission:personas.editar', only: ['edit', 'update']),
            new Middleware('permission:personas.eliminar', only: ['destroy']),
        ];
    }

    public function index()
    {
        $personas = Persona::with('user')
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.personas.index', compact('personas'));
    }

    public function create()
    {
        $users = User::doesntHave('persona')
            ->orderBy('name')
            ->get();

        return view('admin.personas.create', compact('users'));
    }

    public function store(StorePersonaRequest $request)
    {
        Persona::create($request->validated());

        Alert::success('Persona registrada', 'La persona se guardó correctamente.');

        return redirect()->route('admin.personas.index');
    }

    public function edit(Persona $persona)
    {
        $users = User::whereDoesntHave('persona')
            ->orWhere('id', $persona->user_id)
            ->orderBy('name')
            ->get();

        return view('admin.personas.edit', compact('persona', 'users'));
    }

    public function update(UpdatePersonaRequest $request, Persona $persona)
    {
        $persona->update($request->validated());

        Alert::success('Persona actualizada', 'Los datos de la persona se actualizaron correctamente.');

        return redirect()->route('admin.personas.index');
    }

    public function destroy(Persona $persona)
    {
        $persona->delete();

        Alert::warning('Persona eliminada', 'El registro de la persona fue eliminado.');

        return redirect()->route('admin.personas.index');
    }
}
