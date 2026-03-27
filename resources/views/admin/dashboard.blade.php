@extends('layouts.app')

@section('title', 'Dashboard admin')
@section('page-title', 'Panel de administración')
@section('page-subtitle', 'Resumen general del sistema')

@section('content')
    <div class="row g-4">

        <div class="col-12">
            <div class="app-card welcome-card p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="soft-badge soft-primary mb-3">Administración general</span>
                        <h2 class="fw-bold mb-2">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="text-muted mb-0">
                            Desde aquí puedes supervisar usuarios, personas, roles, permisos y la estructura general del sistema.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                    <span class="soft-badge soft-info">
                        <i class="bi bi-shield-lock"></i> Administrador
                    </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Usuarios</h6>
                    <div class="metric-icon"><i class="bi bi-people-fill"></i></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalUsuarios ?? 0 }}</h3>
                <p class="text-muted mb-0">Cuentas registradas.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Personas</h6>
                    <div class="metric-icon"><i class="bi bi-person-vcard"></i></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalPersonas ?? 0 }}</h3>
                <p class="text-muted mb-0">Perfiles personales registrados.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Roles</h6>
                    <div class="metric-icon"><i class="bi bi-shield-check"></i></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalRoles ?? 0 }}</h3>
                <p class="text-muted mb-0">Roles disponibles en Spatie.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Permisos</h6>
                    <div class="metric-icon"><i class="bi bi-key-fill"></i></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalPermisos ?? 0 }}</h3>
                <p class="text-muted mb-0">Permisos configurados.</p>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="app-card p-4 h-100">
                <div class="mb-4">
                    <h4 class="fw-bold mb-1">Accesos administrativos</h4>
                    <p class="text-muted mb-0">Opciones centrales para control y configuración.</p>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.usuarios.index') }}" class="text-decoration-none">
                            <div class="app-card p-4 h-100 border-0" style="background:#faf7ff;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="metric-icon"><i class="bi bi-people"></i></div>
                                    <h6 class="fw-bold mb-0 text-dark">Gestión de usuarios</h6>
                                </div>
                                <p class="text-muted mb-0">Administrar cuentas, roles y control de acceso.</p>
                            </div>
                        </a>

                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.personas.index') }}" class="text-decoration-none">
                            <div class="app-card p-4 h-100 border-0" style="background:#faf7ff;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="metric-icon"><i class="bi bi-person-vcard"></i></div>
                                    <h6 class="fw-bold mb-0 text-dark">Gestión de personas</h6>
                                </div>
                                <p class="text-muted mb-0">Consultar perfiles personales vinculados a usuarios.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.roles.index') }}" class="text-decoration-none">
                            <div class="app-card p-4 h-100 border-0" style="background:#faf7ff;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="metric-icon"><i class="bi bi-shield-check"></i></div>
                                    <h6 class="fw-bold mb-0 text-dark">Gestión de roles</h6>
                                </div>
                                <p class="text-muted mb-0">Asignar roles según funciones institucionales.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.permisos.index') }}" class="text-decoration-none">
                            <div class="app-card p-4 h-100 border-0" style="background:#faf7ff;">
                                <div class="d-flex align-items-center gap-3 mb-2">
                                    <div class="metric-icon"><i class="bi bi-key"></i></div>
                                    <h6 class="fw-bold mb-0 text-dark">Permisos</h6>
                                </div>
                                <p class="text-muted mb-0">Control fino sobre accesos y módulos del sistema.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6">
            <div class="app-card p-4 h-100">
                <div class="mb-4">
                    <h4 class="fw-bold mb-1">Estado general</h4>
                    <p class="text-muted mb-0">Indicadores globales del sistema.</p>
                </div>

                <div class="d-flex flex-column gap-3">
                    <div class="d-flex justify-content-between align-items-center p-3 rounded-4" style="background:#faf7ff;">
                        <div>
                            <div class="fw-semibold">Usuarios registrados</div>
                            <small class="text-muted">Total de cuentas</small>
                        </div>
                        <span class="soft-badge soft-primary">{{ $totalUsuarios ?? 0 }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 rounded-4" style="background:#faf7ff;">
                        <div>
                            <div class="fw-semibold">Personas registradas</div>
                            <small class="text-muted">Perfiles personales</small>
                        </div>
                        <span class="soft-badge soft-success">{{ $totalPersonas ?? 0 }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 rounded-4" style="background:#faf7ff;">
                        <div>
                            <div class="fw-semibold">Roles disponibles</div>
                            <small class="text-muted">Spatie Permission</small>
                        </div>
                        <span class="soft-badge soft-info">{{ $totalRoles ?? 0 }}</span>
                    </div>

                    <div class="d-flex justify-content-between align-items-center p-3 rounded-4" style="background:#faf7ff;">
                        <div>
                            <div class="fw-semibold">Permisos configurados</div>
                            <small class="text-muted">Control de acceso</small>
                        </div>
                        <span class="soft-badge soft-warning">{{ $totalPermisos ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
