@extends('layouts.app')

@section('title', 'Usuarios')
@section('page-title', 'Usuarios')
@section('page-subtitle', 'Listado general de cuentas del sistema')

@section('content')
    <div class="app-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Usuarios registrados</h4>
                <p class="text-muted mb-0">Consulta correo, roles y persona vinculada.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="soft-badge soft-primary">{{ $usuarios->total() }} registros</span>
                <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nuevo usuario
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Roles</th>
                    <th>Persona vinculada</th>
                    <th>Fecha alta</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($usuarios as $usuario)
                    <tr>
                        <td>{{ $usuario->id }}</td>
                        <td>{{ $usuario->name }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            @forelse($usuario->roles as $role)
                                <span class="soft-badge soft-info me-1">{{ $role->name }}</span>
                            @empty
                                <span class="soft-badge soft-warning">Sin rol</span>
                            @endforelse
                        </td>
                        <td>
                            @if($usuario->persona)
                                {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido_paterno }}
                            @else
                                <span class="text-muted">Sin vincular</span>
                            @endif
                        </td>
                        <td>{{ optional($usuario->created_at)->format('d/m/Y H:i') }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Deseas eliminar este usuario?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">No hay usuarios registrados.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    </div>
@endsection
