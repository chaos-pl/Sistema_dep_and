@extends('layouts.app')

@section('title', 'Dashboard Admin - PROMETEO')
@section('page-title', 'Panel de Administración')
@section('page-subtitle', 'Métricas, gráficas y monitoreo en tiempo real del sistema')

@php
    $metricas = $metricas ?? [];

    $resumen = $metricas['resumen'] ?? [];
    $evaluaciones = $metricas['evaluaciones'] ?? [];
    $alertas = $metricas['alertas'] ?? [];
    $salud = $metricas['salud'] ?? [];
    $actividad = $metricas['actividadMensual'] ?? [];

    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    $granimPalettes = match($userAccentColor) {
        'blue' => "
            ['#1e3a8a', '#2563eb'],
            ['#2563eb', '#38bdf8'],
            ['#0f172a', '#3b82f6']
        ",
        'green' => "
            ['#064e3b', '#059669'],
            ['#059669', '#2dd4bf'],
            ['#022c22', '#10b981']
        ",
        'pink' => "
            ['#831843', '#db2777'],
            ['#db2777', '#f43f5e'],
            ['#4c0519', '#ec4899']
        ",
        default => "
            ['#4c1d95', '#7c3aed'],
            ['#7c3aed', '#db2777'],
            ['#1e1b4b', '#8b5cf6']
        "
    };
@endphp

@push('styles')
    <style>
        .admin-realtime {
            --prometeo-primary: #6d28d9;
            --prometeo-primary-dark: #4c1d95;
            --prometeo-soft: rgba(109, 40, 217, .12);
            --prometeo-danger: #dc2626;
            --prometeo-warning: #d97706;
            --prometeo-success: #16a34a;
            --prometeo-info: #0284c7;
            --prometeo-muted: #64748b;
        }

        .admin-hero-realtime {
            position: relative;
            overflow: hidden;
            min-height: 250px;
            border-radius: 30px;
            background: var(--prometeo-primary);
            color: #fff;
            box-shadow: 0 22px 55px rgba(76, 29, 149, .28);
        }

        #granim-canvas-admin {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 3;
            padding: 2.5rem;
        }

        .hero-glass {
            background: rgba(255,255,255,.17);
            border: 1px solid rgba(255,255,255,.28);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border-radius: 24px;
        }

        .metric-card {
            position: relative;
            overflow: hidden;
            border-radius: 24px;
            background: var(--bs-body-bg);
            border: 1px solid rgba(148, 163, 184, .22);
            box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
            transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        }

        .metric-card:hover {
            transform: translateY(-7px);
            box-shadow: 0 20px 45px rgba(15, 23, 42, .10);
            border-color: rgba(109, 40, 217, .26);
        }

        .metric-card::after {
            content: "";
            position: absolute;
            width: 120px;
            height: 120px;
            right: -55px;
            top: -55px;
            border-radius: 50%;
            background: var(--metric-soft, rgba(109,40,217,.10));
        }

        .metric-icon {
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--metric-soft, rgba(109,40,217,.10));
            color: var(--metric-color, #6d28d9);
            font-size: 1.35rem;
            position: relative;
            z-index: 2;
            transition: transform .25s ease;
        }

        .metric-card:hover .metric-icon {
            transform: rotate(-6deg) scale(1.08);
        }

        .metric-value {
            font-size: 2.1rem;
            font-weight: 900;
            letter-spacing: -.04em;
            line-height: 1;
        }

        .metric-label {
            color: var(--bs-secondary-color);
            font-size: .82rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .05em;
        }

        .metric-desc {
            color: var(--bs-secondary-color);
            font-size: .88rem;
            margin: 0;
        }

        .chart-card {
            border-radius: 26px;
            background: var(--bs-body-bg);
            border: 1px solid rgba(148, 163, 184, .22);
            box-shadow: 0 14px 36px rgba(15, 23, 42, .06);
        }

        .chart-box {
            position: relative;
            min-height: 310px;
        }

        .chart-box canvas {
            width: 100% !important;
            height: 300px !important;
        }

        .status-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .55rem .85rem;
            border-radius: 999px;
            background: rgba(22, 163, 74, .14);
            color: #16a34a;
            font-weight: 800;
            font-size: .82rem;
        }

        .pulse-dot {
            width: 10px;
            height: 10px;
            background: #22c55e;
            border-radius: 999px;
            position: relative;
        }

        .pulse-dot::after {
            content: "";
            position: absolute;
            inset: -6px;
            border-radius: 999px;
            border: 2px solid rgba(34,197,94,.45);
            animation: prometeoPulse 1.4s infinite;
        }

        @keyframes prometeoPulse {
            0% { transform: scale(.65); opacity: 1; }
            100% { transform: scale(1.55); opacity: 0; }
        }

        .health-item {
            border-radius: 20px;
            padding: 1rem;
            border: 1px solid rgba(148, 163, 184, .20);
            background: var(--bs-tertiary-bg);
            cursor: pointer;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .health-item:hover {
            transform: translateX(6px);
            box-shadow: 0 12px 28px rgba(15,23,42,.08);
        }

        .soft-danger { --metric-color: #dc2626; --metric-soft: rgba(220,38,38,.13); }
        .soft-warning { --metric-color: #d97706; --metric-soft: rgba(217,119,6,.15); }
        .soft-success { --metric-color: #16a34a; --metric-soft: rgba(22,163,74,.13); }
        .soft-info { --metric-color: #0284c7; --metric-soft: rgba(2,132,199,.13); }
        .soft-primary { --metric-color: #6d28d9; --metric-soft: rgba(109,40,217,.13); }
        .soft-dark { --metric-color: #334155; --metric-soft: rgba(51,65,85,.12); }

        .progress-prometeo {
            height: 13px;
            background: var(--bs-tertiary-bg);
            border-radius: 999px;
            overflow: hidden;
            border: 1px solid rgba(148,163,184,.18);
        }

        .progress-prometeo span {
            display: block;
            height: 100%;
            border-radius: 999px;
            transition: width .8s ease;
        }

        .admin-link-card {
            border-radius: 20px;
            background: var(--bs-tertiary-bg);
            border: 1px solid rgba(148, 163, 184, .18);
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .admin-link-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 16px 34px rgba(15,23,42,.08);
            border-color: rgba(109,40,217,.25);
        }

        .anime-item {
            opacity: 0;
            transform: translateY(22px);
        }

        .last-update {
            font-size: .82rem;
            color: rgba(255,255,255,.82);
        }
    </style>
@endpush

@section('content')
    <div class="admin-realtime">

        <div class="row g-4">

            {{-- HERO --}}
            <div class="col-12 anime-item">
                <div class="admin-hero-realtime">
                    <canvas id="granim-canvas-admin"></canvas>

                    <div class="hero-content">
                        <div class="row align-items-center g-4">
                            <div class="col-lg-8">
                            <span class="badge hero-glass px-3 py-2 rounded-pill mb-3">
                                <i class="bi bi-shield-lock-fill me-1"></i>
                                Administración General
                            </span>

                                <h2 class="fw-black text-white mb-2" style="font-size: 2.35rem;">
                                    Bienvenido, {{ auth()->user()->name }}
                                </h2>

                                <p class="fs-5 text-white text-opacity-90 mb-0">
                                    Monitoreo general de usuarios, evaluaciones, alertas, expedientes y actividad reciente del sistema PROMETEO.
                                </p>
                            </div>

                            <div class="col-lg-4 text-lg-end">
                                <div class="hero-glass p-3 d-inline-block text-start">
                                    <div class="status-pill mb-2">
                                        <span class="pulse-dot"></span>
                                        Sistema en línea
                                    </div>

                                    <div class="last-update">
                                        Última actualización:
                                        <strong id="last-update-label">cargando...</strong>
                                    </div>

                                    <small class="d-block mt-2 text-white text-opacity-75">
                                        Actualización automática cada 10 segundos.
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- MÉTRICAS PRINCIPALES --}}
            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-primary p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">Usuarios</div>
                            <div class="metric-value text-body mt-2" data-counter="totalUsuarios">
                                {{ $resumen['totalUsuarios'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Cuentas registradas en el sistema.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-info p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">Estudiantes</div>
                            <div class="metric-value text-body mt-2" data-counter="totalEstudiantes">
                                {{ $resumen['totalEstudiantes'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Población estudiantil vinculada.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-success p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">Psicólogos</div>
                            <div class="metric-value text-body mt-2" data-counter="totalPsicologos">
                                {{ $resumen['totalPsicologos'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Personal disponible para seguimiento.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-danger p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">Alertas activas</div>
                            <div class="metric-value text-body mt-2" data-counter="alertasActivas">
                                {{ $alertas['activas'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-exclamation-triangle-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Casos que requieren revisión.</p>
                </div>
            </div>

            {{-- MÉTRICAS SECUNDARIAS --}}
            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-dark p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">Personas</div>
                            <div class="metric-value text-body mt-2" data-counter="totalPersonas">
                                {{ $resumen['totalPersonas'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-person-vcard-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Perfiles personales registrados.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-warning p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">Grupos</div>
                            <div class="metric-value text-body mt-2" data-counter="totalGrupos">
                                {{ $resumen['totalGrupos'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Grupos académicos configurados.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-info p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">PHQ-9</div>
                            <div class="metric-value text-body mt-2" data-counter="phq9">
                                {{ $evaluaciones['phq9'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-clipboard2-pulse-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Evaluaciones de depresión registradas.</p>
                </div>
            </div>

            <div class="col-md-6 col-xl-3 anime-item">
                <div class="metric-card soft-primary p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <div class="metric-label">GAD-7</div>
                            <div class="metric-value text-body mt-2" data-counter="gad7">
                                {{ $evaluaciones['gad7'] ?? 0 }}
                            </div>
                        </div>
                        <div class="metric-icon">
                            <i class="bi bi-clipboard2-check-fill"></i>
                        </div>
                    </div>
                    <p class="metric-desc">Evaluaciones de ansiedad registradas.</p>
                </div>
            </div>

            {{-- GRÁFICAS --}}
            <div class="col-12 col-xl-8 anime-item">
                <div class="chart-card p-4 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h4 class="fw-black text-body mb-1">Actividad del sistema</h4>
                            <p class="text-body-secondary mb-0">
                                Evolución de usuarios, evaluaciones y alertas por periodo.
                            </p>
                        </div>
                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                        Tiempo real
                    </span>
                    </div>

                    <div class="chart-box">
                        <canvas id="actividadChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-4 anime-item">
                <div class="chart-card p-4 h-100">
                    <div class="mb-4">
                        <h4 class="fw-black text-body mb-1">Distribución de población</h4>
                        <p class="text-body-secondary mb-0">
                            Relación entre estudiantes, tutores y psicólogos.
                        </p>
                    </div>

                    <div class="chart-box">
                        <canvas id="poblacionChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6 anime-item">
                <div class="chart-card p-4 h-100">
                    <div class="mb-4">
                        <h4 class="fw-black text-body mb-1">Evaluaciones y diario emocional</h4>
                        <p class="text-body-secondary mb-0">
                            Comparación general entre instrumentos y entradas analizadas.
                        </p>
                    </div>

                    <div class="chart-box">
                        <canvas id="evaluacionesChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-12 col-xl-6 anime-item">
                <div class="chart-card p-4 h-100">
                    <div class="mb-4">
                        <h4 class="fw-black text-body mb-1">Estado de alertas</h4>
                        <p class="text-body-secondary mb-0">
                            Seguimiento general de alertas pendientes, activas y atendidas.
                        </p>
                    </div>

                    <div class="chart-box">
                        <canvas id="alertasChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- SALUD DEL SISTEMA --}}
            <div class="col-12 col-xl-5 anime-item">
                <div class="chart-card p-4 h-100">
                    <div class="mb-4 d-flex align-items-center gap-3">
                        <div class="metric-icon soft-success">
                            <i class="bi bi-activity"></i>
                        </div>
                        <div>
                            <h4 class="fw-black text-body mb-0">Salud del sistema</h4>
                            <p class="text-body-secondary mb-0">Pendientes administrativos importantes.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-column gap-3">
                        <div class="health-item" onclick="window.location='{{ route('admin.usuarios.index') }}'">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-danger d-block">Cuentas sin expediente</strong>
                                    <small class="text-body-secondary">Usuarios sin persona vinculada</small>
                                </div>
                                <span class="badge bg-danger rounded-pill fs-6" data-counter="usuariosSinPersona">
                                {{ $salud['usuariosSinPersona'] ?? 0 }}
                            </span>
                            </div>
                        </div>

                        <div class="health-item" onclick="window.location='{{ route('admin.expedientes-pendientes.index') }}'">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-info d-block">Estudiantes sin expediente</strong>
                                    <small class="text-body-secondary">Estudiantes sin vinculación completa</small>
                                </div>
                                <span class="badge bg-info text-dark rounded-pill fs-6" data-counter="estudiantesSinExpediente">
                                {{ $salud['estudiantesSinExpediente'] ?? 0 }}
                            </span>
                            </div>
                        </div>

                        <div class="health-item" onclick="window.location='{{ route('admin.roles.index') }}'">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-warning d-block">Roles sin permisos</strong>
                                    <small class="text-body-secondary">Perfiles de acceso incompletos</small>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill fs-6" data-counter="rolesSinPermisos">
                                {{ $salud['rolesSinPermisos'] ?? 0 }}
                            </span>
                            </div>
                        </div>

                        <div class="health-item" onclick="window.location='{{ route('admin.tutores.index') }}'">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong class="text-primary d-block">Tutores sin grupos</strong>
                                    <small class="text-body-secondary">Tutores pendientes de asignación</small>
                                </div>
                                <span class="badge bg-primary rounded-pill fs-6" data-counter="tutoresSinGrupos">
                                {{ $salud['tutoresSinGrupos'] ?? 0 }}
                            </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ACCESOS --}}
            <div class="col-12 col-xl-7 anime-item">
                <div class="chart-card p-4 h-100">
                    <div class="mb-4">
                        <h4 class="fw-black text-body mb-1">Accesos administrativos</h4>
                        <p class="text-body-secondary mb-0">
                            Atajos rápidos a módulos principales del sistema.
                        </p>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <a href="{{ route('admin.usuarios.index') }}" class="text-decoration-none">
                                <div class="admin-link-card p-4 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="metric-icon soft-primary">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <strong class="text-body">Gestión de usuarios</strong>
                                    </div>
                                    <p class="text-body-secondary small mb-0">
                                        Administra cuentas, roles y accesos.
                                    </p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('admin.personas.index') }}" class="text-decoration-none">
                                <div class="admin-link-card p-4 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="metric-icon soft-info">
                                            <i class="bi bi-person-vcard-fill"></i>
                                        </div>
                                        <strong class="text-body">Directorio personas</strong>
                                    </div>
                                    <p class="text-body-secondary small mb-0">
                                        Consulta perfiles personales.
                                    </p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('admin.psicologos.index') }}" class="text-decoration-none">
                                <div class="admin-link-card p-4 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="metric-icon soft-success">
                                            <i class="bi bi-heart-pulse-fill"></i>
                                        </div>
                                        <strong class="text-body">Psicólogos</strong>
                                    </div>
                                    <p class="text-body-secondary small mb-0">
                                        Administra personal clínico.
                                    </p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('admin.tutores.index') }}" class="text-decoration-none">
                                <div class="admin-link-card p-4 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="metric-icon soft-warning">
                                            <i class="bi bi-person-video3"></i>
                                        </div>
                                        <strong class="text-body">Tutores</strong>
                                    </div>
                                    <p class="text-body-secondary small mb-0">
                                        Controla tutores y asignaciones.
                                    </p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('admin.grupos.index') }}" class="text-decoration-none">
                                <div class="admin-link-card p-4 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="metric-icon soft-primary">
                                            <i class="bi bi-folder-fill"></i>
                                        </div>
                                        <strong class="text-body">Grupos</strong>
                                    </div>
                                    <p class="text-body-secondary small mb-0">
                                        Organiza estructura académica.
                                    </p>
                                </div>
                            </a>
                        </div>

                        <div class="col-md-6">
                            <a href="{{ route('admin.carreras.index') }}" class="text-decoration-none">
                                <div class="admin-link-card p-4 h-100">
                                    <div class="d-flex align-items-center gap-3 mb-2">
                                        <div class="metric-icon soft-info">
                                            <i class="bi bi-mortarboard-fill"></i>
                                        </div>
                                        <strong class="text-body">Carreras</strong>
                                    </div>
                                    <p class="text-body-secondary small mb-0">
                                        Gestiona programas académicos.
                                    </p>
                                </div>
                            </a>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/animejs@3.2.1/lib/anime.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/granim@2.0.0/dist/granim.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const metricasIniciales = @json($metricas);

            const metricasUrl = "{{ route('admin.dashboard.metricas') }}";

            let actividadChart;
            let poblacionChart;
            let evaluacionesChart;
            let alertasChart;

            const chartColors = {
                primary: '#6d28d9',
                primarySoft: 'rgba(109, 40, 217, .16)',
                info: '#0284c7',
                infoSoft: 'rgba(2, 132, 199, .16)',
                success: '#16a34a',
                successSoft: 'rgba(22, 163, 74, .16)',
                warning: '#d97706',
                warningSoft: 'rgba(217, 119, 6, .16)',
                danger: '#dc2626',
                dangerSoft: 'rgba(220, 38, 38, .16)',
                dark: '#334155',
                darkSoft: 'rgba(51, 65, 85, .16)'
            };

            function numero(valor) {
                valor = Number(valor || 0);
                return new Intl.NumberFormat('es-MX').format(valor);
            }

            function obtenerValorActual(elemento) {
                const texto = elemento.textContent.replaceAll(',', '').replaceAll('.', '').trim();
                return parseInt(texto, 10) || 0;
            }

            function animarContador(nombre, nuevoValor) {
                const elementos = document.querySelectorAll(`[data-counter="${nombre}"]`);

                elementos.forEach(elemento => {
                    const valorActual = obtenerValorActual(elemento);
                    const valorFinal = Number(nuevoValor || 0);

                    anime({
                        targets: { value: valorActual },
                        value: valorFinal,
                        round: 1,
                        duration: 850,
                        easing: 'easeOutExpo',
                        update: function(anim) {
                            elemento.textContent = numero(anim.animatables[0].target.value);
                        }
                    });
                });
            }

            function actualizarTextoMetricas(data) {
                const resumen = data.resumen || {};
                const evaluaciones = data.evaluaciones || {};
                const alertas = data.alertas || {};
                const salud = data.salud || {};

                animarContador('totalUsuarios', resumen.totalUsuarios);
                animarContador('totalPersonas', resumen.totalPersonas);
                animarContador('totalEstudiantes', resumen.totalEstudiantes);
                animarContador('totalPsicologos', resumen.totalPsicologos);
                animarContador('totalGrupos', resumen.totalGrupos);

                animarContador('phq9', evaluaciones.phq9);
                animarContador('gad7', evaluaciones.gad7);

                animarContador('alertasActivas', alertas.activas);
                animarContador('usuariosSinPersona', salud.usuariosSinPersona);
                animarContador('estudiantesSinExpediente', salud.estudiantesSinExpediente);
                animarContador('rolesSinPermisos', salud.rolesSinPermisos);
                animarContador('tutoresSinGrupos', salud.tutoresSinGrupos);

                const label = document.getElementById('last-update-label');

                if (label) {
                    const ahora = new Date();
                    label.textContent = ahora.toLocaleTimeString('es-MX', {
                        hour: '2-digit',
                        minute: '2-digit',
                        second: '2-digit'
                    });
                }
            }

            function crearGraficas(data) {
                const resumen = data.resumen || {};
                const evaluaciones = data.evaluaciones || {};
                const alertas = data.alertas || {};
                const actividad = data.actividadMensual || {};

                const labelsActividad = actividad.labels || ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'];
                const usuariosActividad = actividad.usuarios || [0, 0, 0, 0, 0, resumen.totalUsuarios || 0];
                const evaluacionesActividad = actividad.evaluaciones || [0, 0, 0, 0, 0, (evaluaciones.phq9 || 0) + (evaluaciones.gad7 || 0)];
                const alertasActividad = actividad.alertas || [0, 0, 0, 0, 0, alertas.activas || 0];

                const actividadCtx = document.getElementById('actividadChart');

                actividadChart = new Chart(actividadCtx, {
                    type: 'line',
                    data: {
                        labels: labelsActividad,
                        datasets: [
                            {
                                label: 'Usuarios',
                                data: usuariosActividad,
                                borderColor: chartColors.primary,
                                backgroundColor: chartColors.primarySoft,
                                fill: true,
                                tension: .42,
                                pointRadius: 4,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Evaluaciones',
                                data: evaluacionesActividad,
                                borderColor: chartColors.info,
                                backgroundColor: chartColors.infoSoft,
                                fill: true,
                                tension: .42,
                                pointRadius: 4,
                                pointHoverRadius: 7
                            },
                            {
                                label: 'Alertas',
                                data: alertasActividad,
                                borderColor: chartColors.danger,
                                backgroundColor: chartColors.dangerSoft,
                                fill: true,
                                tension: .42,
                                pointRadius: 4,
                                pointHoverRadius: 7
                            }
                        ]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 900,
                            easing: 'easeOutQuart'
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                const poblacionCtx = document.getElementById('poblacionChart');

                poblacionChart = new Chart(poblacionCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Estudiantes', 'Tutores', 'Psicólogos'],
                        datasets: [{
                            data: [
                                resumen.totalEstudiantes || 0,
                                resumen.totalTutores || 0,
                                resumen.totalPsicologos || 0
                            ],
                            backgroundColor: [
                                chartColors.primary,
                                chartColors.warning,
                                chartColors.success
                            ],
                            borderWidth: 0,
                            hoverOffset: 12
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '68%',
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1000
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            }
                        }
                    }
                });

                const evaluacionesCtx = document.getElementById('evaluacionesChart');

                evaluacionesChart = new Chart(evaluacionesCtx, {
                    type: 'bar',
                    data: {
                        labels: ['PHQ-9', 'GAD-7', 'Diario emocional'],
                        datasets: [{
                            label: 'Registros',
                            data: [
                                evaluaciones.phq9 || 0,
                                evaluaciones.gad7 || 0,
                                evaluaciones.diarios || 0
                            ],
                            backgroundColor: [
                                chartColors.info,
                                chartColors.primary,
                                chartColors.success
                            ],
                            borderRadius: 14,
                            maxBarThickness: 70
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 900,
                            easing: 'easeOutBounce'
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });

                const alertasCtx = document.getElementById('alertasChart');

                alertasChart = new Chart(alertasCtx, {
                    type: 'polarArea',
                    data: {
                        labels: ['Pendientes', 'Activas', 'Atendidas', 'Prioritarias'],
                        datasets: [{
                            data: [
                                alertas.pendientes || 0,
                                alertas.activas || 0,
                                alertas.atendidas || 0,
                                alertas.prioritarias || 0
                            ],
                            backgroundColor: [
                                chartColors.warningSoft,
                                chartColors.dangerSoft,
                                chartColors.successSoft,
                                'rgba(124, 58, 237, .20)'
                            ],
                            borderColor: [
                                chartColors.warning,
                                chartColors.danger,
                                chartColors.success,
                                chartColors.primary
                            ],
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            animateRotate: true,
                            animateScale: true,
                            duration: 1000
                        },
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    boxWidth: 8
                                }
                            }
                        },
                        scales: {
                            r: {
                                ticks: {
                                    precision: 0
                                }
                            }
                        }
                    }
                });
            }

            function actualizarGraficas(data) {
                const resumen = data.resumen || {};
                const evaluaciones = data.evaluaciones || {};
                const alertas = data.alertas || {};
                const actividad = data.actividadMensual || {};

                if (actividadChart) {
                    actividadChart.data.labels = actividad.labels || actividadChart.data.labels;
                    actividadChart.data.datasets[0].data = actividad.usuarios || actividadChart.data.datasets[0].data;
                    actividadChart.data.datasets[1].data = actividad.evaluaciones || actividadChart.data.datasets[1].data;
                    actividadChart.data.datasets[2].data = actividad.alertas || actividadChart.data.datasets[2].data;
                    actividadChart.update();
                }

                if (poblacionChart) {
                    poblacionChart.data.datasets[0].data = [
                        resumen.totalEstudiantes || 0,
                        resumen.totalTutores || 0,
                        resumen.totalPsicologos || 0
                    ];
                    poblacionChart.update();
                }

                if (evaluacionesChart) {
                    evaluacionesChart.data.datasets[0].data = [
                        evaluaciones.phq9 || 0,
                        evaluaciones.gad7 || 0,
                        evaluaciones.diarios || 0
                    ];
                    evaluacionesChart.update();
                }

                if (alertasChart) {
                    alertasChart.data.datasets[0].data = [
                        alertas.pendientes || 0,
                        alertas.activas || 0,
                        alertas.atendidas || 0,
                        alertas.prioritarias || 0
                    ];
                    alertasChart.update();
                }
            }

            function actualizarDashboard(data) {
                actualizarTextoMetricas(data);
                actualizarGraficas(data);
            }

            async function cargarMetricasTiempoReal() {
                try {
                    const response = await fetch(metricasUrl, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) {
                        throw new Error('No se pudieron cargar las métricas.');
                    }

                    const data = await response.json();
                    actualizarDashboard(data);

                } catch (error) {
                    console.error(error);

                    const label = document.getElementById('last-update-label');

                    if (label) {
                        label.textContent = 'sin conexión';
                    }
                }
            }

            if (typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-admin',
                    direction: 'diagonal',
                    isPausedWhenNotInView: true,
                    states: {
                        'default-state': {
                            gradients: [
                                {!! $granimPalettes !!}
                            ],
                            transitionSpeed: 5000
                        }
                    }
                });
            }

            if (typeof anime !== 'undefined') {
                anime({
                    targets: '.anime-item',
                    opacity: [0, 1],
                    translateY: [28, 0],
                    delay: anime.stagger(95),
                    duration: 900,
                    easing: 'easeOutExpo'
                });

                anime({
                    targets: '.metric-icon',
                    scale: [0.88, 1],
                    rotate: [-6, 0],
                    delay: anime.stagger(70),
                    duration: 850,
                    easing: 'easeOutElastic(1, .7)'
                });
            }

            crearGraficas(metricasIniciales);
            actualizarTextoMetricas(metricasIniciales);

            setInterval(cargarMetricasTiempoReal, 10000);
        });
    </script>
@endpush
