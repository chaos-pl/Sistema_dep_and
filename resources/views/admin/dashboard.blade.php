@extends('layouts.app')

@section('title', 'Dashboard Admin - PROMETEO')
@section('page-title', 'Panel de Administración')
@section('page-subtitle', 'Resumen general e indicadores del sistema')

@php
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    $granimPalettes = match($userAccentColor) {
        'blue' => "
            [ { color: '#1e3a8a', pos: 0 }, { color: '#2563eb', pos: .5 }, { color: '#93c5fd', pos: 1 } ],
            [ { color: '#2563eb', pos: 0 }, { color: '#0284c7', pos: .5 }, { color: '#38bdf8', pos: 1 } ],
            [ { color: '#0f172a', pos: 0 }, { color: '#1d4ed8', pos: .5 }, { color: '#3b82f6', pos: 1 } ]
        ",
        'green' => "
            [ { color: '#064e3b', pos: 0 }, { color: '#059669', pos: .5 }, { color: '#6ee7b7', pos: 1 } ],
            [ { color: '#059669', pos: 0 }, { color: '#0d9488', pos: .5 }, { color: '#2dd4bf', pos: 1 } ],
            [ { color: '#022c22', pos: 0 }, { color: '#047857', pos: .5 }, { color: '#10b981', pos: 1 } ]
        ",
        'pink' => "
            [ { color: '#831843', pos: 0 }, { color: '#db2777', pos: .5 }, { color: '#f9a8d4', pos: 1 } ],
            [ { color: '#db2777', pos: 0 }, { color: '#e11d48', pos: .5 }, { color: '#f43f5e', pos: 1 } ],
            [ { color: '#4c0519', pos: 0 }, { color: '#be185d', pos: .5 }, { color: '#ec4899', pos: 1 } ]
        ",
        default => "
            [ { color: '#4c1d95', pos: 0 }, { color: '#7c3aed', pos: .5 }, { color: '#a78bfa', pos: 1 } ],
            [ { color: '#7c3aed', pos: 0 }, { color: '#c026d3', pos: .5 }, { color: '#db2777', pos: 1 } ],
            [ { color: '#1e1b4b', pos: 0 }, { color: '#6d28d9', pos: .5 }, { color: '#8b5cf6', pos: 1 } ]
        "
    };
@endphp

@push('styles')
    <style>
        .hover-elevate {
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease !important;
            border: 1px solid transparent;
        }
        .hover-elevate:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.08) !important;
            border-color: var(--app-primary-soft) !important;
        }

        .hover-elevate:hover .metric-icon i { transform: scale(1.25); }
        .metric-icon i { transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }

        .bg-welcome-admin {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary);
        }
        .bg-welcome-admin::after {
            content: '\F52F';
            font-family: "bootstrap-icons";
            position: absolute;
            top: -10%; right: -5%;
            font-size: 15rem; color: #ffffff;
            opacity: 0.08; transform: rotate(-15deg);
            pointer-events: none; z-index: 2;
        }

        #granim-canvas-admin {
            position: absolute; top: 0; left: 0;
            width: 100%; height: 100%; z-index: 0;
            border-radius: inherit;
        }

        .banner-content { position: relative; z-index: 3; }

        .anime-item { opacity: 0; transform: translateY(20px); }
        .cursor-pointer { cursor: pointer; }

        /* Clase protectora para asegurar que los elementos del banner sean visibles en modo oscuro */
        .glass-badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .glass-panel {
            background-color: rgba(255, 255, 255, 0.15) !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-admin p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-admin"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge glass-badge rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-shield-lock-fill me-1"></i> Administración General
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="mb-0 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                            Desde aquí puedes supervisar usuarios, personas, roles, permisos y la estructura general del sistema.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <div class="glass-panel rounded-4 p-3 d-inline-block text-start shadow-sm">
                            <small class="text-white text-opacity-90 d-block text-uppercase fw-bold mb-1" style="font-size: 0.75rem; letter-spacing: 1px;">Estado del Servidor</small>
                            <div class="d-flex align-items-center gap-2">
                                <div class="spinner-grow spinner-grow-sm text-white" role="status"></div>
                                <span class="fw-bold text-white">En línea y operando</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Usuarios</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up text-body" data-value="{{ $totalUsuarios ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Cuentas registradas</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Personas</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-vcard-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up text-body" data-value="{{ $totalPersonas ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Perfiles personales</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Roles</h6>
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-shield-lock-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up text-body" data-value="{{ $totalRoles ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Perfiles de acceso</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Permisos</h6>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-key-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 count-up text-body" data-value="{{ $totalPermisos ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Reglas configuradas</p>
            </div>
        </div>

        <div class="col-12 col-xl-7 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="mb-4 d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-grid-1x2-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0 text-body">Accesos Administrativos</h4>
                        <p class="text-body-secondary mb-0">Atajos rápidos a los módulos de control principales.</p>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <a href="{{ route('admin.usuarios.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-primary shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-people-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Gestión de Usuarios</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Administra cuentas de acceso, asignación de roles y bloqueos.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.psicologos.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-danger shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-heart-pulse-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Gestión de Psicólogos</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Da de alta psicólogos, actualiza su expediente y prepara el módulo clínico.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.personas.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-info shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-person-vcard-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Directorio Personas</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Consulta y vincula expedientes y perfiles demográficos.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.tutores.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-success shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-person-video3"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Gestión de Tutores</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Da de alta tutores, actualiza sus datos y administra su expediente institucional.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.grupos.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-info shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-folder-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Gestión de Grupos</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Crea grupos, asigna tutores y organiza la estructura académica.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.carreras.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-primary shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-mortarboard-fill"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Gestión de Carreras</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Crea carreras académicas y organízalas antes de registrar grupos.</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.roles.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-warning shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-shield-check text-warning-emphasis"></i></div>
                                    <h6 class="fw-bold mb-0 text-body">Control de Roles</h6>
                                </div>
                                <p class="text-body-secondary mb-0 small">Define perfiles de acceso (Estudiante, Tutor, Psicólogo).</p>
                            </div>
                        </a>
                    </div>

                    <div class="col-md-6">
                        <a href="{{ route('admin.permisos.index') }}" class="text-decoration-none">
                            <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                <div class="d-flex align-items-center gap-3 mb-3">
                                    <div class="metric-icon rounded-circle bg-body text-danger shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi bi-key-fill"></i></div>
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
                <div class="mb-4 d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                        <i class="bi bi-activity fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0 text-body">Salud del Sistema</h4>
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

                    <div class="p-3 bg-info bg-opacity-10 border border-info border-opacity-25 rounded-4 d-flex align-items-center justify-content-between hover-elevate cursor-pointer"
                         onclick="window.location='{{ route('admin.expedientes-pendientes.index') }}'">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-mortarboard text-info fs-4"></i>
                            <div>
                                <span class="fw-bold text-info d-block" style="line-height: 1;">Estudiantes sin expediente</span>
                                <small class="text-info text-opacity-75">Usuarios con rol estudiante sin grupo</small>
                            </div>
                        </div>
                        <span class="badge bg-info text-dark rounded-pill fs-6 shadow-sm">{{ $estudiantesSinExpediente ?? 0 }}</span>
                    </div>

                    <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 d-flex align-items-center justify-content-between hover-elevate cursor-pointer" onclick="window.location='{{ route('admin.roles.index') }}'">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-shield-exclamation text-warning-emphasis fs-4"></i>
                            <div>
                                <span class="fw-bold text-warning-emphasis d-block" style="line-height: 1;">Roles vacíos</span>
                                <small class="text-warning-emphasis text-opacity-75">Perfiles sin permisos asignados</small>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill fs-6 shadow-sm">{{ $rolesSinPermisos ?? 0 }}</span>
                    </div>

                    <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 rounded-4 d-flex align-items-center justify-content-between hover-elevate cursor-pointer" onclick="window.location='{{ route('admin.tutores.index') }}'">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-person-video3 text-primary fs-4"></i>
                            <div>
                                <span class="fw-bold text-primary d-block" style="line-height: 1;">Tutores sin grupos</span>
                                <small class="text-primary text-opacity-75">Expedientes sin asignación académica</small>
                            </div>
                        </div>
                        <span class="badge bg-primary rounded-pill fs-6 shadow-sm">{{ $tutoresSinGrupos ?? 0 }}</span>
                    </div>
                </div>

                <h6 class="fw-bold text-body-secondary text-uppercase mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Distribución de Población</h6>
                <div class="d-flex flex-column gap-3 p-4 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">

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
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="fw-bold text-body"><i class="bi bi-mortarboard-fill text-primary me-2"></i> Estudiantes</span>
                            <span class="text-body-secondary fw-bold">{{ $estCount }} ({{ $pctEstudiantes }}%)</span>
                        </div>
                        <div class="progress bg-body border border-secondary border-opacity-10" style="height: 12px;">
                            <div class="progress-bar bg-primary rounded-pill" style="width: {{ $pctEstudiantes }}%"></div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="fw-bold text-body"><i class="bi bi-heart-pulse-fill text-info me-2"></i> Psicólogos</span>
                            <span class="text-body-secondary fw-bold">{{ $psiCount }} ({{ $pctPsicologos }}%)</span>
                        </div>
                        <div class="progress bg-body border border-secondary border-opacity-10" style="height: 12px;">
                            <div class="progress-bar bg-info rounded-pill" style="width: {{ $pctPsicologos }}%"></div>
                        </div>
                    </div>

                    <div class="mt-2">
                        <div class="d-flex justify-content-between align-items-end mb-2">
                            <span class="fw-bold text-body"><i class="bi bi-person-video3 text-success me-2"></i> Tutores</span>
                            <span class="text-body-secondary fw-bold">{{ $tutCount }} ({{ $pctTutores }}%)</span>
                        </div>
                        <div class="progress bg-body border border-secondary border-opacity-10" style="height: 12px;">
                            <div class="progress-bar bg-success rounded-pill" style="width: {{ $pctTutores }}%"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/granim.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof anime !== 'undefined') {
                anime({
                    targets: '.anime-item',
                    translateY: [30, 0],
                    opacity: [0, 1],
                    delay: anime.stagger(100),
                    easing: 'easeOutExpo',
                    duration: 900
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
            }

            if (document.getElementById('granim-canvas-admin') && typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-admin',
                    direction: 'left-right',
                    isPausedWhenNotInView: true,
                    states : {
                        "default-state": {
                            gradients: [
                                {!! $granimPalettes !!}
                            ],
                            transitionSpeed: 7000
                        }
                    }
                });
            }
        });
    </script>
@endpush
