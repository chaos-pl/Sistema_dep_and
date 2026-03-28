<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Validation\Rule;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles.ver', only: ['index', 'show']),
            new Middleware('permission:roles.crear', only: ['create', 'store']),
            new Middleware('permission:roles.editar', only: ['edit', 'update']),
            new Middleware('permission:roles.eliminar', only: ['destroy']),
        ];
    }

    public function index()
    {
        $roles = Role::with('permissions')
            ->withCount('users')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return view('admin.roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($request->permissions ?? []);

        Alert::success('Rol creado', 'El rol se registró correctamente.');

        return redirect()->route('admin.roles.index');
    }

    public function show(Role $role)
    {
        $role->load('permissions', 'users');

        return view('admin.roles.show', compact('role'));
    }

    public function edit(Role $role)
    {
        $permissions = Permission::orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->ignore($role->id)],
            'permissions' => ['nullable', 'array'],
            'permissions.*' => ['exists:permissions,name'],
        ]);

        $role->update([
            'name' => $request->name,
        ]);

        $role->syncPermissions($request->permissions ?? []);

        Alert::success('Rol actualizado', 'El rol se actualizó correctamente.');

        return redirect()->route('admin.roles.index');
    }

    public function destroy(Role $role)
    {
        if ($role->name === 'admin') {
            Alert::error('Acción no permitida', 'El rol admin no se puede eliminar.');
            return redirect()->route('admin.roles.index');
        }

        if ($role->users()->count() > 0) {
            Alert::warning('No se puede eliminar', 'Este rol tiene usuarios asignados.');
            return redirect()->route('admin.roles.index');
        }

        $role->delete();

        Alert::success('Rol eliminado', 'El rol se eliminó correctamente.');

        return redirect()->route('admin.roles.index');
    }
}
