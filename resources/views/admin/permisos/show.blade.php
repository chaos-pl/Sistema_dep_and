@extends('layouts.app')

@section('title', 'Detalle del Permiso - PROMETEO')
@section('page-title', 'Información del Permiso')
@section('page-subtitle', 'Resumen de la capacidad y roles que la poseen')

@section('content')
    <div class="row g-4">
        <div class="col-lg-4">
            <div class="app-card p-4 p-md-5 h-100 bg-primary bg-gradient text-white text-center rounded-4 border-0 shadow-lg">
                <div class="bg-white bg-opacity-25 rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                    <i class="bi bi-key-fill fs-1 text-white"></i>
                </div>

                <h3 class="fw-black mb-1 text-uppercase text-break">{{ $permiso->name }}</h3>
                <p class="opacity-75 mb-4">Permiso de Sistema</p>

                <div class="d-flex flex-column gap-3 mt-4 text-start bg-white bg-opacity-10 p-4 rounded-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-medium opacity-75"><i class="bi bi-shield-lock-fill me-2"></i> Roles que lo poseen</span>
                        <span class="badge bg-white text-primary rounded-pill px-3">{{ $permiso->roles->count() }} roles</span>
                    </div>
                </div>

                <div class="mt-5 text-center">
                    <a href="{{ route('admin.permisos.index') }}" class="btn btn-light text-primary rounded-pill px-4 py-2 fw-bold w-100 shadow">
                        <i class="bi bi-arrow-left me-2"></i>Volver al listado
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="app-card p-4 p-md-5 h-100 border-0 shadow-sm rounded-4">
                <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-shield-check text-primary me-2"></i>Roles Autorizados</h4>
                <p class="text-muted mb-4">Los siguientes roles de sistema tienen este permiso concedido:</p>

                @if($permiso->roles->count() > 0)
                    <div class="row g-3">
                        @foreach($permiso->roles as $role)
                            <div class="col-md-6 col-xl-4">
                                <div class="d-flex align-items-center gap-3 p-3 bg-light border border-secondary border-opacity-10 rounded-4 shadow-sm">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person-badge"></i>
                                    </div>
                                    <span class="fw-bold text-dark text-capitalize">{{ $role->name }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                            <i class="bi bi-shield-slash fs-1 text-muted"></i>
                        </div>
                        <h5 class="fw-bold text-muted">Permiso Huérfano</h5>
                        <p class="text-muted">Este permiso no está asignado a ningún rol en la base de datos.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
