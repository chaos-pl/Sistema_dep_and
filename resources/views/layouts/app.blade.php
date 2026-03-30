<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'PROMETEO'))</title>

    @php
        $authUser = auth()->user();

        $defaultAppearance = [
            'theme' => 'light',
            'accent_color' => 'purple',
            'density' => 'comfortable',
            'reduced_motion' => false,
        ];

        $appearance = $authUser
            ? array_merge($defaultAppearance, $authUser->appearance_settings ?? [])
            : $defaultAppearance;

        $accentMap = [
            'purple' => [
                'primary' => '#7c3aed',
                'primary_dark' => '#6d28d9',
                'primary_soft' => '#ede9fe',
                'sidebar_start' => '#2e1065',
                'sidebar_end' => '#7c3aed',
            ],
            'blue' => [
                'primary' => '#2563eb',
                'primary_dark' => '#1d4ed8',
                'primary_soft' => '#dbeafe',
                'sidebar_start' => '#1e3a8a',
                'sidebar_end' => '#2563eb',
            ],
            'green' => [
                'primary' => '#059669',
                'primary_dark' => '#047857',
                'primary_soft' => '#d1fae5',
                'sidebar_start' => '#064e3b',
                'sidebar_end' => '#059669',
            ],
            'pink' => [
                'primary' => '#db2777',
                'primary_dark' => '#be185d',
                'primary_soft' => '#fce7f3',
                'sidebar_start' => '#831843',
                'sidebar_end' => '#db2777',
            ],
        ];

        $accent = $accentMap[$appearance['accent_color'] ?? 'purple'] ?? $accentMap['purple'];

        $themeClass = match($appearance['theme'] ?? 'light') {
            'dark' => 'theme-dark',
            'system' => 'theme-system',
            default => 'theme-light',
        };

        $densityClass = ($appearance['density'] ?? 'comfortable') === 'compact'
            ? 'density-compact'
            : 'density-comfortable';

        $motionClass = !empty($appearance['reduced_motion']) ? 'reduced-motion' : '';

        $avatarIcons = config('appearance.avatar_icons', [
            'person-circle' => [
                'label' => 'Clásico',
                'class' => 'bi bi-person-circle',
            ],
        ]);

        $userAvatarKey = $authUser->avatar_icon ?? 'person-circle';
        $userAvatarClass = $avatarIcons[$userAvatarKey]['class'] ?? 'bi bi-person-circle';
    @endphp

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* 1. FUENTES LOCALES */
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter_18pt-Regular.ttf') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter_18pt-SemiBold.ttf') format('truetype'); font-weight: 600; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter_18pt-Bold.ttf') format('truetype'); font-weight: 700; font-style: normal; font-display: swap; }

        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-Regular.ttf') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-SemiBold.ttf') format('truetype'); font-weight: 600; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-Bold.ttf') format('truetype'); font-weight: 700; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-ExtraBold.ttf') format('truetype'); font-weight: 800; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-Black.ttf') format('truetype'); font-weight: 900; font-style: normal; font-display: swap; }

        /* 2. VARIABLES Y ESTILOS BASE */
        :root{
            --app-font-base: 'Inter', sans-serif;
            --app-font-title: 'Montserrat', sans-serif;

            --app-bg: #f8fafc;
            --app-surface: #ffffff;
            --app-primary: {{ $accent['primary'] }};
            --app-primary-dark: {{ $accent['primary_dark'] }};
            --app-primary-soft: {{ $accent['primary_soft'] }};
            --app-text: #1e293b;
            --app-muted: #64748b;
            --app-border: #e2e8f0;
            --app-sidebar-text: #f8f5ff;

            --app-success-soft: #dcfce7;
            --app-warning-soft: #fef3c7;
            --app-danger-soft: #fee2e2;
            --app-info-soft: #dbeafe;
        }

        body{
            background: var(--app-bg);
            color: var(--app-text);
            font-family: var(--app-font-base);
            transition: background-color .25s ease, color .25s ease;
        }

        h1, h2, h3, h4, h5, h6, .fw-bold, .offcanvas-title {
            font-family: var(--app-font-title);
        }

        .fw-extrabold { font-weight: 800 !important; }
        .fw-black { font-weight: 900 !important; }

        .app-shell{
            min-height: 100vh;
            display: flex;
        }

        /* -----------------------------------------------------
           3. TEMA OSCURO (Corrección Total de Tablas y Fondos)
           ----------------------------------------------------- */
        body.theme-dark{
            --app-bg: #0f172a;
            --app-surface: #111827;
            --app-text: #e5e7eb;
            --app-muted: #94a3b8;
            --app-border: #1f2937;
        }

        body.theme-dark .topbar{ background: rgba(17, 24, 39, 0.88); border-bottom: 1px solid rgba(31, 41, 55, 0.9); }
        body.theme-dark .app-card{ background: var(--app-surface); color: var(--app-text); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.20); }

        /* Textos y fondos utilitarios */
        body.theme-dark .text-dark, body.theme-dark .text-body { color: #f8fafc !important; }
        body.theme-dark .text-secondary { color: #cbd5e1 !important; }
        body.theme-dark .text-muted, body.theme-dark .text-body-secondary { color: #94a3b8 !important; }
        body.theme-dark .bg-light, body.theme-dark .bg-body { background-color: rgba(255, 255, 255, 0.03) !important; }
        body.theme-dark .bg-white { background-color: rgba(255, 255, 255, 0.05) !important; }
        body.theme-dark .bg-body-tertiary { background-color: rgba(0, 0, 0, 0.2) !important; }
        body.theme-dark .border-light, body.theme-dark .border-secondary { border-color: rgba(255, 255, 255, 0.1) !important; }

        /* Corrección de Inputs */
        body.theme-dark .form-control, body.theme-dark .form-select, body.theme-dark .input-group-text, body.theme-dark textarea {
            background-color: rgba(0, 0, 0, 0.2) !important; color: #f8fafc !important; border-color: rgba(255, 255, 255, 0.1) !important;
        }
        body.theme-dark .form-control::placeholder, body.theme-dark textarea::placeholder { color: rgba(255, 255, 255, 0.4) !important; }

        /* Corrección Botones - AQUÍ QUITAMOS EL !important AL COLOR */
        body.theme-dark .btn-light { background: #1f2937 !important; border-color: #374151 !important; color: #e5e7eb; }
        body.theme-dark .btn-outline-danger{ color: #fca5a5; border-color: #7f1d1d; }
        body.theme-dark .btn-outline-danger:hover{ background: #7f1d1d; color: #fff; }

        /* ====================================================
           CORRECCIÓN ESTRICTA DE TABLAS BOOTSTRAP MODO OSCURO
           ==================================================== */
        body.theme-dark .table {
            --bs-table-bg: transparent !important;
            --bs-table-color: #f8fafc !important;
        }
        body.theme-dark .table > :not(caption) > * > * {
            background-color: transparent !important;
            color: #f8fafc !important;
            border-bottom-color: rgba(255, 255, 255, 0.08) !important;
        }

        /* -----------------------------------------------------
           4. TEMA SISTEMA AUTOMÁTICO
           ----------------------------------------------------- */
        @media (prefers-color-scheme: dark) {
            body.theme-system{
                --app-bg: #0f172a;
                --app-surface: #111827;
                --app-text: #e5e7eb;
                --app-muted: #94a3b8;
                --app-border: #1f2937;
            }

            body.theme-system .topbar{ background: rgba(17, 24, 39, 0.88); border-bottom: 1px solid rgba(31, 41, 55, 0.9); }
            body.theme-system .app-card{ background: var(--app-surface); color: var(--app-text); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.20); }

            body.theme-system .text-dark, body.theme-system .text-body { color: #f8fafc !important; }
            body.theme-system .text-secondary { color: #cbd5e1 !important; }
            body.theme-system .text-muted, body.theme-system .text-body-secondary { color: #94a3b8 !important; }
            body.theme-system .bg-light, body.theme-system .bg-body { background-color: rgba(255, 255, 255, 0.03) !important; }
            body.theme-system .bg-white { background-color: rgba(255, 255, 255, 0.05) !important; }
            body.theme-system .bg-body-tertiary { background-color: rgba(0, 0, 0, 0.2) !important; }
            body.theme-system .border-light, body.theme-system .border-secondary { border-color: rgba(255, 255, 255, 0.1) !important; }

            body.theme-system .form-control, body.theme-system .form-select, body.theme-system .input-group-text, body.theme-system textarea {
                background-color: rgba(0, 0, 0, 0.2) !important; color: #e5e7eb !important; border-color: rgba(255, 255, 255, 0.1) !important;
            }
            body.theme-system .form-control::placeholder, body.theme-system textarea::placeholder { color: rgba(255, 255, 255, 0.4) !important; }

            /* Corrección Botones Sistema Automático - AQUÍ QUITAMOS EL !important AL COLOR */
            body.theme-system .btn-light{ background: #1f2937 !important; border-color: #374151 !important; color: #e5e7eb; }

            /* Corrección Estricta Tablas Theme System */
            body.theme-system .table {
                --bs-table-bg: transparent !important;
                --bs-table-color: #f8fafc !important;
            }
            body.theme-system .table > :not(caption) > * > * {
                background-color: transparent !important;
                color: #f8fafc !important;
                border-bottom-color: rgba(255, 255, 255, 0.08) !important;
            }
        }

        /* -----------------------------------------------------
           EFECTO HOVER PREMIUM PARA TABLAS
           ----------------------------------------------------- */
        .table-prometeo tbody tr {
            transition: all 0.2s ease-in-out;
        }

        .table-prometeo tbody tr td {
            transition: background-color 0.2s ease, transform 0.2s ease;
        }

        /* Iluminación en Modo Claro */
        .table-prometeo tbody tr:hover td {
            background-color: rgba(124, 58, 237, 0.05) !important;
        }

        /* Iluminación en Modo Oscuro */
        body.theme-dark .table-prometeo tbody tr:hover td,
        body.theme-system .table-prometeo tbody tr:hover td {
            background-color: rgba(124, 58, 237, 0.15) !important;
        }

        /* Deslizamiento del primer elemento (nombre/avatar) al hacer hover */
        .table-prometeo tbody tr:hover td:first-child div {
            transform: translateX(8px);
        }

        /* -----------------------------------------------------
           5. MENÚ LATERAL Y DEMÁS ESTILOS
           ----------------------------------------------------- */
        .sidebar{
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(180deg, {{ $accent['sidebar_start'] }} 0%, {{ $accent['sidebar_end'] }} 100%);
            color: var(--app-sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            padding: 1.5rem 1rem;
            box-shadow: 4px 0 24px rgba(46, 16, 101, 0.15);
            overflow-y: auto;
        }

        .sidebar-brand{ font-size: 1.25rem; font-weight: 900; letter-spacing: 1px; }
        .sidebar-sub{ font-size: .85rem; opacity: .75; }

        .sidebar .nav-link{
            color: rgba(255,255,255,.80);
            border-radius: 12px;
            padding: .85rem 1rem;
            margin-bottom: .4rem;
            display: flex;
            align-items: center;
            gap: .85rem;
            font-weight: 500;
            border-left: 4px solid transparent;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active{
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 100%);
            color: #ffffff;
            border-left: 4px solid #ffffff;
            padding-left: 1.5rem;
        }

        .sidebar .nav-link i { font-size: 1.15rem; transition: transform 0.3s ease; }
        .sidebar .nav-link:hover i, .sidebar .nav-link.active i { transform: scale(1.15); }

        .main-panel{ flex: 1; margin-left: 280px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar{ position: sticky; top: 0; z-index: 1020; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(226, 232, 240, 0.8); padding: 1rem 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .content-wrapper{ padding: 2rem; flex-grow: 1; }

        .metric-icon{ width: 56px; height: 56px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; background: var(--app-primary-soft); color: var(--app-primary-dark); font-size: 1.5rem; }
        .soft-badge{ display: inline-flex; align-items: center; gap: .4rem; border-radius: 999px; padding: .4rem .85rem; font-size: .85rem; font-weight: 700; }
        .soft-primary{ background: var(--app-primary-soft); color: var(--app-primary-dark); }

        .btn{ border-radius: 12px; font-weight: 600; padding: .6rem 1.2rem; transition: .25s ease; }
        .btn-primary{ background: var(--app-primary); border-color: var(--app-primary); }
        .btn-primary:hover{ background: var(--app-primary-dark); border-color: var(--app-primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2); }

        .profile-avatar-circle{ width: 92px; height: 92px; border-radius: 50%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, var(--app-primary), var(--app-primary-dark)); color: #fff; font-size: 2rem; box-shadow: 0 14px 30px rgba(124, 58, 237, .22); }
        .sidebar-user-icon{ width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); color: #fff; font-size: 1.35rem; flex-shrink: 0; }

        .appearance-icon-option{ transition: .2s ease; cursor: pointer; background: #fff; }
        .appearance-icon-option:hover{ transform: translateY(-2px); border-color: #b89ae6 !important; box-shadow: 0 8px 24px rgba(155,114,207,.10); }
        .appearance-icon-input:checked + .appearance-icon-option{ border-color: var(--app-primary) !important; background: var(--app-primary-soft); box-shadow: 0 0 0 .2rem rgba(155,114,207,.12); }
        .appearance-icon-preview{ width: 52px; height: 52px; border-radius: 16px; display: inline-flex; align-items: center; justify-content: center; background: var(--app-primary-soft); color: var(--app-primary-dark); font-size: 1.35rem; }

        body.density-compact .content-wrapper{ padding: 1.2rem; }
        body.density-compact .app-card{ border-radius: 16px; }
        body.density-compact .btn{ padding: .45rem .9rem; font-size: .92rem; }
        body.density-compact .form-control, body.density-compact .form-select, body.density-compact textarea{ padding: .6rem .85rem !important; }
        body.density-compact .sidebar .nav-link{ padding: .7rem .9rem; }

        .offcanvas-sidebar{ background: linear-gradient(180deg, {{ $accent['sidebar_start'] }} 0%, {{ $accent['sidebar_end'] }} 100%); color: #fff; }

        @media (max-width: 991.98px){
            .sidebar{ display: none; }
            .main-panel{ margin-left: 0; }
            .content-wrapper{ padding: 1.25rem; }
        }

        .anime-topbar, .anime-sidebar-item, .anime-content { opacity: 0; }

        body.reduced-motion *, body.reduced-motion *::before, body.reduced-motion *::after{
            animation: none !important; transition: none !important; scroll-behavior: auto !important;
        }
    </style>

    @stack('styles')
</head>
<body class="{{ $themeClass }} {{ $densityClass }} {{ $motionClass }}">
<div class="app-shell">

    <aside class="sidebar d-none d-lg-flex flex-column">
        <div class="mb-4 anime-sidebar-item">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-white text-primary rounded-4 d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                    <i class="bi bi-heart-pulse-fill fs-4"></i>
                </div>
                <div>
                    <div class="sidebar-brand font-title">PROMETEO</div>
                    <div class="sidebar-sub">Monitoreo emocional</div>
                </div>
            </div>
        </div>

        <nav class="nav flex-column mt-2">
            @auth
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Panel admin
                    </a>

                    @can('usuarios.ver')
                        <a href="{{ route('admin.usuarios.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i> Usuarios
                        </a>
                    @endcan

                    @can('personas.ver')
                        <a href="{{ route('admin.personas.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('admin.personas.*') ? 'active' : '' }}">
                            <i class="bi bi-person-vcard-fill"></i> Personas
                        </a>
                    @endcan

                    @can('roles.ver')
                        <a href="{{ route('admin.roles.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                            <i class="bi bi-shield-lock-fill"></i> Roles
                        </a>
                    @endcan

                    @can('permisos.ver')
                        <a href="{{ route('admin.permisos.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}">
                            <i class="bi bi-key-fill"></i> Permisos
                        </a>
                    @endcan

                @elseif(auth()->user()->hasRole('estudiante'))
                    <a href="{{ route('estudiante.dashboard') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door-fill"></i> Inicio
                    </a>

                    @can('evaluaciones.realizar')
                        <a href="{{ route('evaluaciones.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('evaluaciones.*') ? 'active' : '' }}">
                            <i class="bi bi-clipboard2-check-fill"></i> Evaluaciones
                        </a>
                    @endcan

                    @can('diario_ia.ver.propio')
                        <a href="{{ route('diario.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('diario.*') ? 'active' : '' }}">
                            <i class="bi bi-journal-text"></i> Diario IA
                        </a>
                    @endcan

                @elseif(auth()->user()->hasRole('tutor'))
                    <a href="{{ route('tutor.dashboard') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('tutor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-grid-1x2-fill"></i> Panel tutor
                    </a>

                    @can('grupos.ver.asignados')
                        <a href="{{ route('grupos.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('grupos.*') ? 'active' : '' }}">
                            <i class="bi bi-people-fill"></i> Mis grupos
                        </a>
                    @endcan

                @elseif(auth()->user()->hasRole('psicologo'))
                    <a href="{{ route('psicologo.dashboard') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('psicologo.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-activity"></i> Panel clínico
                    </a>

                    @can('alertas.ver.clinicas')
                        <a href="{{ route('alertas.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('alertas.*') ? 'active' : '' }}">
                            <i class="bi bi-exclamation-triangle-fill"></i> Alertas
                        </a>
                    @endcan

                    @can('diagnosticos.ver')
                        <a href="{{ route('diagnosticos.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('diagnosticos.*') ? 'active' : '' }}">
                            <i class="bi bi-file-earmark-medical-fill"></i> Diagnósticos
                        </a>
                    @endcan

                    @can('resultados_ia.ver')
                        <a href="{{ route('analisis.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('analisis.*') ? 'active' : '' }}">
                            <i class="bi bi-robot"></i> Resultados IA
                        </a>
                    @endcan
                @endif

                <div class="my-2 border-top border-light border-opacity-25 anime-sidebar-item"></div>

                @can('perfil.ver')
                    <a href="{{ route('perfil.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('perfil.*') || request()->routeIs('profile.*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i> Mi Perfil
                    </a>
                @endcan
            @endauth

            <a href="{{ route('aviso.privacidad') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('aviso.privacidad') ? 'active' : '' }}">
                <i class="bi bi-shield-check"></i> Aviso de privacidad
            </a>
        </nav>

        <div class="mt-auto pt-4 anime-sidebar-item">
            @auth
                <div class="bg-white bg-opacity-10 rounded-4 p-3 mb-3 border border-white border-opacity-10">
                    <div class="d-flex align-items-center gap-3">
                        <div class="sidebar-user-icon">
                            <i class="{{ $userAvatarClass }}"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="fw-bold fs-6 text-truncate">{{ auth()->user()->name }}</div>
                            <small class="opacity-75 d-block text-truncate">
                                {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                            </small>
                        </div>
                    </div>
                </div>
            @endauth

            <a href="{{ route('logout.view') }}" class="btn btn-light w-100 fw-bold shadow-sm">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
            </a>
        </div>
    </aside>

    <div class="main-panel">

        <header class="topbar anime-topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none border-0 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h1 class="h4 fw-black mb-0">@yield('page-title', 'Panel de Control')</h1>
                        <small class="text-muted fw-medium">@yield('page-subtitle', 'Bienvenido a PROMETEO')</small>
                    </div>
                </div>

                @auth
                    <div class="d-flex align-items-center gap-3">
                        <span class="soft-badge soft-primary d-none d-md-inline-flex border border-primary border-opacity-10">
                            <i class="bi bi-person-badge-fill"></i>
                            {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                        </span>

                        <a href="{{ route('logout.view') }}" class="btn btn-outline-danger d-none d-lg-inline-flex align-items-center rounded-pill px-3">
                            <i class="bi bi-power me-2"></i>Salir
                        </a>
                    </div>
                @endauth
            </div>
        </header>

        <main class="content-wrapper anime-content">
            @yield('content')
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header border-bottom border-light border-opacity-10 p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-white text-primary rounded-3 d-flex align-items-center justify-content-center" style="width:40px;height:40px;">
                <i class="bi bi-heart-pulse-fill fs-5"></i>
            </div>
            <h5 class="offcanvas-title fw-black mb-0 font-title">PROMETEO</h5>
        </div>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-4">
        @auth
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="sidebar-user-icon">
                    <i class="{{ $userAvatarClass }}"></i>
                </div>
                <div>
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                    <small class="opacity-75">
                        {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                    </small>
                </div>
            </div>
        @endauth

        <nav class="nav flex-column gap-2">
            @auth
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="nav-link text-white bg-white bg-opacity-10 rounded-3 px-3 py-2">
                        <i class="bi bi-grid-1x2-fill me-2"></i> Panel admin
                    </a>
                    @can('usuarios.ver')<a href="{{ route('admin.usuarios.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-people-fill me-2"></i> Usuarios</a>@endcan
                    @can('personas.ver')<a href="{{ route('admin.personas.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-person-vcard-fill me-2"></i> Personas</a>@endcan
                    @can('roles.ver')<a href="{{ route('admin.roles.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-shield-lock-fill me-2"></i> Roles</a>@endcan
                    @can('permisos.ver')<a href="{{ route('admin.permisos.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-key-fill me-2"></i> Permisos</a>@endcan
                @elseif(auth()->user()->hasRole('estudiante'))
                    <a href="{{ route('estudiante.dashboard') }}" class="nav-link text-white bg-white bg-opacity-10 rounded-3 px-3 py-2"><i class="bi bi-house-door-fill me-2"></i> Inicio</a>
                    @can('evaluaciones.realizar')<a href="{{ route('evaluaciones.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-clipboard2-check-fill me-2"></i> Evaluaciones</a>@endcan
                    @can('diario_ia.ver.propio')<a href="{{ route('diario.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-journal-text me-2"></i> Diario IA</a>@endcan
                @elseif(auth()->user()->hasRole('tutor'))
                    <a href="{{ route('tutor.dashboard') }}" class="nav-link text-white bg-white bg-opacity-10 rounded-3 px-3 py-2"><i class="bi bi-grid-1x2-fill me-2"></i> Panel tutor</a>
                    @can('grupos.ver.asignados')<a href="{{ route('grupos.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-people-fill me-2"></i> Mis grupos</a>@endcan
                @elseif(auth()->user()->hasRole('psicologo'))
                    <a href="{{ route('psicologo.dashboard') }}" class="nav-link text-white bg-white bg-opacity-10 rounded-3 px-3 py-2"><i class="bi bi-activity me-2"></i> Panel clínico</a>
                    @can('alertas.ver.clinicas')<a href="{{ route('alertas.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-exclamation-triangle-fill me-2"></i> Alertas</a>@endcan
                    @can('diagnosticos.ver')<a href="{{ route('diagnosticos.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-file-earmark-medical-fill me-2"></i> Diagnósticos</a>@endcan
                    @can('resultados_ia.ver')<a href="{{ route('analisis.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-robot me-2"></i> Resultados IA</a>@endcan
                @endif
                <div class="my-2 border-top border-light border-opacity-25"></div>
                @can('perfil.ver')<a href="{{ route('perfil.index') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-person-circle me-2"></i> Perfil</a>@endcan
            @endauth
            <a href="{{ route('aviso.privacidad') }}" class="nav-link text-white opacity-75 px-3 py-2"><i class="bi bi-shield-check me-2"></i> Aviso de privacidad</a>
        </nav>

        <div class="mt-auto pt-4">
            <a href="{{ route('logout.view') }}" class="btn btn-light w-100 fw-bold"><i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión</a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reducedMotion = document.body.classList.contains('reduced-motion');
        if (reducedMotion) {
            document.querySelectorAll('.anime-topbar, .anime-sidebar-item, .anime-content').forEach(el => {
                el.style.opacity = '1'; el.style.transform = 'none';
            });
            return;
        }

        const tl = anime.timeline({ easing: 'easeOutExpo' });
        tl.add({ targets: '.anime-topbar', translateY: [-20, 0], opacity: [0, 1], duration: 800 })
            .add({ targets: '.anime-sidebar-item', translateX: [-20, 0], opacity: [0, 1], delay: anime.stagger(60), duration: 600 }, '-=600')
            .add({ targets: '.anime-content', translateY: [20, 0], opacity: [0, 1], duration: 800 }, '-=400');
    });
</script>

@include('sweetalert::alert')
@stack('scripts')
</body>
</html>
