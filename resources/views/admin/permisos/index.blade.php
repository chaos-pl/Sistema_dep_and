@extends('layouts.app')

@section('title', 'Permisos')
@section('page-title', 'Permisos')
@section('page-subtitle', 'Listado de permisos configurados')

@section('content')
    <div class="app-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Permisos del sistema</h4>
                <p class="text-muted mb-0">Catálogo general de permisos registrados.</p>
            </div>
            <span class="soft-badge soft-primary">{{ $permisos->count() }} permisos</span>
        </div>

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Permiso</th>
                    <th>Guard</th>
                    <th>Fecha de creación</th>
                </tr>
                </thead>
                <tbody>
                @forelse($permisos as $permiso)
                    <tr>
                        <td>{{ $permiso->id }}</td>
                        <td>{{ $permiso->name }}</td>
                        <td>{{ $permiso->guard_name }}</td>
                        <td>{{ optional($permiso->created_at)->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">
                            No hay permisos registrados.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
