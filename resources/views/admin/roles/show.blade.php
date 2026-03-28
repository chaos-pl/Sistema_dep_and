@extends('layouts.app')

@section('title', 'Detalle del Rol - PROMETEO')
@section('page-title', 'Información del Rol')
@section('page-subtitle', 'Resumen de accesos y usuarios vinculados')

@php
    $iconMap = [
        'usuarios' => 'bi-people-fill text-primary', 'personas' => 'bi-person-vcard-fill text-info', 'roles' => 'bi-shield-lock-fill text-warning',
        'permisos' => 'bi-key-fill text-danger', 'evaluaciones' => 'bi-clipboard2-check-fill text-success', 'diario_ia' => 'bi-journal-text text-primary',
        'alertas' => 'bi-exclamation-triangle-fill text-danger', 'diagnosticos' => 'bi-file-earmark-medical-fill text-info', 'resultados_ia' => 'bi-robot text-primary',
    ];
@endphp

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="app-card p-4 p-md-5 h-100 bg-primary bg-gradient text-white text-center rounded-4 border-0 shadow-lg">
                <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="bi bi-shield-check fs-1 text-white"></i>
                </div>

                <h3 class="fw-black mb-1 text-uppercase">{{ $role->name }}</h3>
                <p class="opacity-75 mb-4">Rol del Sistema</p>

                <div class="d-flex flex-column gap-3 mt-4 text-start bg-white bg-opacity-10 p-4 rounded-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium opacity-75"><i class="bi bi-people-fill me-2"></i> Usuarios</span>
                        <span class="badge bg-white text-primary rounded-pill px-3">{{ $role->users->count() }} asignados</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium opacity-75"><i class="bi bi-key-fill me-2"></i> Permisos</span>
                        <span class="badge bg-white text-primary rounded-pill px-3">{{ $role->permissions->count() }} totales</span>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold w-100 shadow">
                        <i class="bi bi-arrow-left me-2"></i>Volver al listado
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="app-card p-4 p-md-5 h-100 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-grid-1x2-fill text-primary me-2"></i>Matriz de Accesos Autorizados</h4>

                @if($groupedPermissions->count() > 0)
                    <div class="row g-3">
                        @foreach($groupedPermissions as $grupo => $permisos)
                            @php $icon = $iconMap[$grupo] ?? 'bi-folder-fill text-secondary'; @endphp
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-4 border border-light h-100">
                                    <h6 class="fw-bold text-dark text-uppercase mb-3 d-flex align-items-center gap-2">
                                        <i class="bi {{ $icon }} fs-5"></i>
                                        {{ str_replace('_', ' ', $grupo) }}
                                    </h6>

                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($permisos as $permiso)
                                            <span class="badge bg-white text-secondary border border-secondary border-opacity-25 px-3 py-2 shadow-sm fw-medium">
                                                <i class="bi bi-check-circle-fill text-success me-1"></i>
                                                {{ ucfirst(str_replace($grupo . '.', '', $permiso->name)) }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-slash fs-1 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-muted">Sin permisos</h5>
                        <p class="text-muted">Este rol no tiene capacidades asignadas en el sistema.</p>

                        @can('roles.editar')
                            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-primary rounded-pill mt-2">
                                <i class="bi bi-pencil-fill me-2"></i>Asignar permisos
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
