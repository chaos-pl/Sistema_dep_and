@extends('layouts.app')

@section('title', 'Personas')
@section('page-title', 'Personas')
@section('page-subtitle', 'Listado general de perfiles personales')

@section('content')
    <div class="app-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Personas registradas</h4>
                <p class="text-muted mb-0">Consulta la información personal vinculada a usuarios.</p>
            </div>
            <div class="d-flex gap-2">
                <span class="soft-badge soft-primary">{{ $personas->total() }} registros</span>
                <a href="{{ route('admin.personas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Nueva persona
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Usuario</th>
                    <th>Nombre completo</th>
                    <th>Fecha nacimiento</th>
                    <th>Género</th>
                    <th>Teléfono</th>
                    <th class="text-end">Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($personas as $persona)
                    <tr>
                        <td>{{ $persona->id }}</td>
                        <td>{{ $persona->user->email ?? 'Sin usuario' }}</td>
                        <td>
                            {{ $persona->nombre }}
                            {{ $persona->apellido_paterno }}
                            {{ $persona->apellido_materno }}
                        </td>
                        <td>{{ $persona->fecha_nacimiento }}</td>
                        <td>{{ $persona->genero }}</td>
                        <td>{{ $persona->telefono ?? '—' }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.personas.edit', $persona) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil-square"></i>
                            </a>

                            <form action="{{ route('admin.personas.destroy', $persona) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('¿Deseas eliminar esta persona?')">
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
                        <td colspan="7" class="text-center text-muted py-4">
                            No hay personas registradas.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $personas->links() }}
        </div>
    </div>
@endsection
