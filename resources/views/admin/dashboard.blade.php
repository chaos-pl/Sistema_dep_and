@extends('layouts.app')

@section('title', 'Dashboard Admin - PROMETEO')
@section('page-title', 'Panel de Administración')
@section('page-subtitle', 'Resumen general e indicadores del sistema')

@php
    // Obtenemos el color de acento del usuario. Si no tiene, usamos primary por defecto.
    $userAccentColor = auth()->user()->appearance['accent_color'] ?? 'primary';

    // Mapeamos el color de acento a las clases de Bootstrap para la tarjeta principal
    $bannerClasses = match($userAccentColor) {
        'red' => 'bg-danger bg-gradient',
        'green' => 'bg-success bg-gradient',
        'blue' => 'bg-info bg-gradient text-dark', // El info suele ser claro
        'orange' => 'bg-warning bg-gradient text-dark',
        'pink' => 'bg-pink bg-gradient', // Si definiste este color en tu CSS
        default => 'bg-primary bg-gradient' // Morado por defecto
    };

    // Ajustamos el color del texto del banner dependiendo de si el fondo es claro u oscuro
    $bannerTextClass = in_array($userAccentColor, ['blue', 'orange']) ? 'text-dark' : 'text-white';
@endphp

@push('styles')
    <style>
        /* Efecto de elevación para las tarjetas interactivas */
        .hover-elevate {
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease !important;
            border: 1px solid transparent;
        }
        .hover-elevate:hover {
            transform: translateY(-6px);
            /* Usamos variables CSS para que la sombra se adapte al tema */
            box-shadow: 0 15px 35px var(--bs-primary-rgb) !important;
            border-color: rgba(var(--bs-primary-rgb), 0.2);
        }

        /* Efecto de zoom en el ícono al hacer hover en la tarjeta */
        .hover-elevate:hover .metric-icon i {
            transform: scale(1.25);
        }
        .metric-icon i {
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        /* Fondo decorativo para la tarjeta de bienvenida adaptativo */
        .bg-welcome {
            position: relative;
            overflow: hidden;
        }
        .bg-welcome::after {
            content: '\F52F'; /* Ícono de escudo de Bootstrap Icons */
            font-family: "bootstrap-icons";
            position: absolute;
            top: -10%;
            right: -5%;
            font-size: 15rem;
            color: inherit;
            opacity: 0.1;
            transform: rotate(-15deg);
            pointer-events: none;
        }

        /* Elementos dinámicos para modo oscuro */
        .dynamic-bg {
            background-color: var(--bs-tertiary-bg);
        }

        .anime-item {
            opacity: 0;
            transform: translateY(20px);
        }
        .cursor-pointer {
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-12 anime-item">
            <div class="app-card bg-welcome p-4 p-md-5 rounded-4 border-0 shadow-lg {{ $bannerClasses }} {{ $bannerTextClass }}">
                <div class="row align-items-center position-relative z-1">
                    <div class="col-lg-8">
                        <span class="badge bg-body text-body border rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-shield-lock-fill me-1"></i> Administración General
                        </span>
                        <h2 class="fw-black mb-2" style="font-size: 2.2rem;">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="mb-0 opacity-75 fs-5">
                            Desde aquí puedes supervisar usuarios, personas, roles, permisos y la estructura general del sistema.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <div class="bg-body bg-opacity-10 rounded-4 p-3 d-inline-block text-start border border-body border-opacity-10 backdrop-blur">
                            <small class="opacity-75 d-block text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Estado del Servidor</small>
                            <div class="d-flex align-items-center gap-2">
                                <div class="spinner-grow spinner-grow-sm text-success" role="status"></div>
                                <span class="fw-bold">En línea y operando</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Usuarios</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up" data-value="{{ $totalUsuarios ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Cuentas registradas</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Personas</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-person-vcard-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up" data-value="{{ $totalPersonas ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Perfiles personales</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Roles</h6>
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up" data-value="{{ $totalRoles ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Perfiles de acceso</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Permisos</h6>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-key-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up" data-value="{{ $totalPermisos ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Reglas configuradas</p>
            </div>
        </div>

        <div class="col-12 col-xl-7 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="mb-4 d-flex align-items-center gap-3">
                    <div class="dynamic-bg text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-grid-1x2-fill fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0">Accesos Administrativos</h4>
                        <p class="text-body-secondary mb-0">Atajos rápidos a los módulos de control principales.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.usuarios.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 dynamic-bg rounded-4 border-0">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon bg-body text-primary shadow-sm"><i class="bi bi-people-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Gestión de Usuarios</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Administra cuentas de acceso, asignación de roles y bloqueos.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.personas.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 dynamic-bg rounded-4 border-0">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon bg-body text-info shadow-sm"><i class="bi bi-person-vcard-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Directorio Personas</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Consulta y vincula expedientes y perfiles demográficos.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.roles.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 dynamic-bg rounded-4 border-0">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon bg-body text-warning shadow-sm"><i class="bi bi-shield-check"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Control de Roles</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Define perfiles de acceso (Estudiante, Tutor, Psicólogo).</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.permisos.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 dynamic-bg rounded-4 border-0">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon bg-body text-danger shadow-sm"><i class="bi bi-key-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Matriz de Permisos</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Ajuste fino de permisos a nivel de controlador y vista.</p>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="mb-4 d-flex align-items-center gap-3">
                    <div class="dynamic-bg text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-activity fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0">Salud del Sistema</h4>
                        <p class="text-body-secondary mb-0">Alertas de configuración y demografía.</p>
                    </div>
                </div>

                <h6 class="fw-bold text-body-secondary text-uppercase mb-3" style="font-size: 0.75rem; letter-spacing: 1px;">Atención Requerida</h6>
                <div class="d-flex flex-column gap-2 mb-4">

                    <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 d-flex align-items-center justify-content-between hover-elevate cursor-pointer" onclick="window.location='{{ route('admin.usuarios.index') }}'">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-person-exclamation text-danger fs-4"></i>
                            <div>
                                <span class="fw-bold text-danger d-block" style="line-height: 1;">Cuentas sin expediente</span>
                                <small class="text-danger text-opacity-75">Usuarios sin persona vinculada</small>
                            </div>
                        </div>
                        <span class="badge bg-danger rounded-pill fs-6 shadow-sm">{{ $usuariosSinPersona ?? 0 }}</span>
                    </div>

                    <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 d-flex align-items-center justify-content-between hover-elevate cursor-pointer" onclick="window.location='{{ route('admin.roles.index') }}'">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-shield-exclamation text-warning fs-4"></i>
                            <div>
                                <span class="fw-bold text-warning d-block" style="line-height: 1;">Roles vacíos</span>
                                <small class="text-warning text-opacity-75">Perfiles sin permisos asignados</small>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill fs-6 shadow-sm">{{ $rolesSinPermisos ?? 0 }}</span>
                    </div>
                </div>

                <h6 class="fw-bold text-body-secondary text-uppercase mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Distribución de Población</h6>
                <div class="d-flex flex-column gap-3 p-3 dynamic-bg rounded-4 border-0">

                    @php
                        $total = ($totalUsuarios ?? 0) > 0 ? $totalUsuarios : 1;
                        $estCount = $estudiantesCount ?? 0;
                        $psiCount = $psicologosCount ?? 0;
                        $tutCount = $tutoresCount ?? 0;

                        $pctEstudiantes = round(($estCount / $total) * 100);
                        $pctPsicologos = round(($psiCount / $total) * 100);
                        $pctTutores = round(($tutCount / $total) * 100);
                    @endphp

                    <div>
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <span class="fw-bold fs-6"><i class="bi bi-mortarboard-fill text-primary me-1"></i> Estudiantes</span>
                            <span class="text-body-secondary fw-bold" style="font-size: 0.85rem;">{{ $estCount }} ({{ $pctEstudiantes }}%)</span>
                        </div>
                        <div class="progress bg-body border-0 shadow-sm" style="height: 10px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pctEstudiantes }}%"></div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <span class="fw-bold fs-6"><i class="bi bi-heart-pulse-fill text-info me-1"></i> Psicólogos Clínicos</span>
                            <span class="text-body-secondary fw-bold" style="font-size: 0.85rem;">{{ $psiCount }} ({{ $pctPsicologos }}%)</span>
                        </div>
                        <div class="progress bg-body border-0 shadow-sm" style="height: 10px;">
                            <div class="progress-bar bg-info rounded-pill" style="width: {{ $pctPsicologos }}%"></div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-end mb-1">
                            <span class="fw-bold fs-6"><i class="bi bi-person-video3 text-success me-1"></i> Tutores</span>
                            <span class="text-body-secondary fw-bold" style="font-size: 0.85rem;">{{ $tutCount }} ({{ $pctTutores }}%)</span>
                        </div>
                        <div class="progress bg-body border-0 shadow-sm" style="height: 10px;">
                            <div class="progress-bar bg-success rounded-pill" style="width: {{ $pctTutores }}%"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({
                targets: '.anime-item',
                translateY: [30, 0],
                opacity: [0, 1],
                delay: anime.stagger(150),
                easing: 'easeOutExpo',
                duration: 1000
            });

            const counters = document.querySelectorAll('.count-up');
            counters.forEach(counter => {
                const endValue = parseInt(counter.getAttribute('data-value'), 10);
                anime({
                    targets: counter,
                    innerHTML: [0, endValue],
                    easing: 'easeOutExpo',
                    round: 1,
                    duration: 2500,
                    delay: 500
                });
            });
        });
    </script>
@endpush
