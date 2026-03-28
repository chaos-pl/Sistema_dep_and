<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use App\Http\Requests\Admin\UpdatePermissionRequest;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use RealRashid\SweetAlert\Facades\Alert;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:permisos.ver', only: ['index', 'show']),
            new Middleware('permission:permisos.crear', only: ['create', 'store']),
            new Middleware('permission:permisos.editar', only: ['edit', 'update']),
            new Middleware('permission:permisos.eliminar', only: ['destroy']),
        ];
    }

    public function index()
    {
        // Obtenemos todos los permisos, contamos sus roles y los agrupamos por prefijo
        $groupedPermissions = Permission::with('roles')
            ->orderBy('name')
            ->get()
            ->groupBy(function ($permission) {
                return explode('.', $permission->name)[0];
            });

        return view('admin.permisos.index', compact('groupedPermissions'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.permisos.create', compact('roles'));
    }

    public function store(StorePermissionRequest $request)
    {
        $permiso = Permission::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        $permiso->syncRoles($request->roles ?? []);

        Alert::success('Permiso creado', 'El permiso se registró correctamente.');

        return redirect()->route('admin.permisos.index');
    }

    public function show(Permission $permiso)
    {
        $permiso->load('roles');

        return view('admin.permisos.show', compact('permiso'));
    }

    public function edit(Permission $permiso)
    {
        $roles = Role::orderBy('name')->get();
        $permiso->load('roles');

        return view('admin.permisos.edit', compact('permiso', 'roles'));
    }

    public function update(UpdatePermissionRequest $request, Permission $permiso)
    {
        $permiso->update([
            'name' => $request->name,
        ]);

        $permiso->syncRoles($request->roles ?? []);

        Alert::success('Permiso actualizado', 'El permiso se actualizó correctamente.');

        return redirect()->route('admin.permisos.index');
    }

    public function destroy(Permission $permiso)
    {
        $protegidos = [
            'usuarios.ver',
            'roles.ver',
            'permisos.ver',
        ];

        if (in_array($permiso->name, $protegidos)) {
            Alert::error('Acción no permitida', 'Ese permiso está protegido y no se puede eliminar.');
            return redirect()->route('admin.permisos.index');
        }

        $permiso->delete();

        Alert::success('Permiso eliminado', 'El permiso se eliminó correctamente.');

        return redirect()->route('admin.permisos.index');
    }
}
