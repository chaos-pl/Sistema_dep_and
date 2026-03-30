<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\Persona;
use App\Models\User;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:usuarios.ver', only: ['index']),
            new Middleware('permission:usuarios.crear', only: ['create', 'store']),
            new Middleware('permission:usuarios.editar', only: ['edit', 'update']),
            new Middleware('permission:usuarios.eliminar', only: ['destroy']),
        ];
    }

    public function index()
    {
        $usuarios = User::with(['roles', 'persona'])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        $personas = Persona::whereNull('user_id')
            ->orderBy('nombre')
            ->get();

        return view('admin.usuarios.create', compact('roles', 'personas'));
    }

    public function store(StoreUserRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        $user->syncRoles($data['roles'] ?? []);

        if (!empty($data['persona_id'])) {
            $persona = Persona::whereNull('user_id')->findOrFail($data['persona_id']);
            $persona->update([
                'user_id' => $user->id,
            ]);
        }

        Alert::success('Usuario creado', 'El usuario se registró y sus roles fueron asignados correctamente.');

        return redirect()
            ->route('admin.usuarios.index');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        $personas = Persona::whereNull('user_id')
            ->orWhere('user_id', $user->id)
            ->orderBy('nombre')
            ->get();

        return view('admin.usuarios.edit', compact('user', 'roles', 'personas'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $data = $request->validated();

        $updateData = [
            'name' => $data['name'],
            'email' => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);
        $user->syncRoles($data['roles'] ?? []);

        $currentPersonaId = $user->persona?->id;
        $newPersonaId = $data['persona_id'] ?? null;

        // Solo tocar la persona si realmente cambió
        if (!empty($newPersonaId) && (string) $newPersonaId !== (string) $currentPersonaId) {
            $persona = Persona::where(function ($query) use ($user) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', $user->id);
            })
                ->findOrFail($newPersonaId);

            // Si tu base NO permite user_id null, no desvincules la anterior aquí
            // Solo vincula la nueva si está libre o ya pertenece al usuario
            $persona->update([
                'user_id' => $user->id,
            ]);
        }

        Alert::success('Usuario actualizado', 'Los cambios del usuario fueron guardados correctamente.');

        return redirect()->route('admin.usuarios.index');
    }

    public function destroy(User $user)
    {
        Persona::where('user_id', $user->id)->update([
            'user_id' => null,
        ]);

        $user->syncRoles([]);
        $user->delete();

        Alert::warning('Usuario eliminado', 'El usuario fue eliminado correctamente.');

        return redirect()
            ->route('admin.usuarios.index');
    }
}
