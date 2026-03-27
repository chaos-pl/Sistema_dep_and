@extends('layouts.app')

@section('title', 'Roles')
@section('page-title', 'Roles')
@section('page-subtitle', 'Roles configurados con Spatie Permission')

@section('content')
    <div class="app-card p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1">Roles del sistema</h4>
                <p class="text-muted mb-0">Visualiza cada rol y la cantidad de permisos asociados.</p>
            </div>
            <span class="soft-badge soft-primary">{{ $roles->count() }} roles</span>
        </div>

        <div class="row g-4">
            @forelse($roles as $role)
                <div class="col-md-6 col-xl-4">
                    <div class="app-card p-4 h-100">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="fw-bold mb-0">{{ ucfirst($role->name) }}</h5>
                            <span class="soft-badge soft-info">{{ $role->permissions->count() }} permisos</span>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            @forelse($role->permissions as $permission)
                                <span class="soft-badge soft-primary">{{ $permission->name }}</span>
                            @empty
                                <span class="text-muted">Sin permisos asignados.</span>
                            @endforelse
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center text-muted py-4">No hay roles registrados.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
