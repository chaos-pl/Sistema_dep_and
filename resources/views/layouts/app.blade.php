<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'PROMETEO'))</title>

    @php
        $authUser = auth()->user();
        $unreadNotices = $authUser ? \App\Models\Aviso::where('user_id', $authUser->id)->whereNull('leido_at')->count() : 0;

        $defaultAppearance = [
            'theme' => 'light',
            'accent_color' => 'purple',
            'density' => 'comfortable',
            'reduced_motion' => false,
        ];

        $appearance = $authUser
            ? array_merge($defaultAppearance, $authUser->appearance_settings ?? [])
            : $defaultAppearance;

        $accentMap = config('appearance.palettes');

        $accent = $accentMap[$appearance['accent_color'] ?? 'purple'] ?? $accentMap['purple'];

        // PALETAS DE COLORES PROFUNDOS PARA LA ANIMACIÓN DEL SIDEBAR


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
        }

        body{ background: var(--app-bg); color: var(--app-text); font-family: var(--app-font-base); transition: background-color .25s ease, color .25s ease; }
        h1, h2, h3, h4, h5, h6, .fw-bold, .offcanvas-title { font-family: var(--app-font-title); }
        .fw-extrabold { font-weight: 800 !important; }
        .fw-black { font-weight: 900 !important; }
        .app-shell{ min-height: 100vh; display: flex; }

        /* 3. TEMA OSCURO Y SISTEMA */
        body.theme-dark, body.theme-system.system-dark {
            --app-bg: #0f172a; --app-surface: #111827; --app-text: #e5e7eb; --app-muted: #94a3b8; --app-border: #1f2937;
        }
        body.theme-dark .topbar, body.theme-system.system-dark .topbar { background: rgba(17, 24, 39, 0.88); border-bottom: 1px solid rgba(31, 41, 55, 0.9); }
        body.theme-dark .app-card, body.theme-system.system-dark .app-card { background: var(--app-surface); color: var(--app-text); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.20); }

        /* Ajuste de contraste para textos globales en modo oscuro */
        body.theme-dark .text-body-secondary, body.theme-system.system-dark .text-body-secondary { color: #94a3b8 !important; }
        body.theme-dark .text-body, body.theme-system.system-dark .text-body { color: #f8fafc !important; }

        /* Ajuste de contraste para centros de alertas y avisos en modo oscuro */
        body.theme-dark .alert-warning, body.theme-system.system-dark .alert-warning { background-color: rgba(245, 158, 11, 0.15) !important; color: #fcd34d !important; border-color: rgba(245, 158, 11, 0.3) !important; }
        body.theme-dark .alert-info, body.theme-system.system-dark .alert-info { background-color: rgba(56, 189, 248, 0.15) !important; color: #7dd3fc !important; border-color: rgba(56, 189, 248, 0.3) !important; }
        body.theme-dark .alert-danger, body.theme-system.system-dark .alert-danger { background-color: rgba(239, 68, 68, 0.15) !important; color: #fca5a5 !important; border-color: rgba(239, 68, 68, 0.3) !important; }
        body.theme-dark .alert-success, body.theme-system.system-dark .alert-success { background-color: rgba(34, 197, 94, 0.15) !important; color: #86efac !important; border-color: rgba(34, 197, 94, 0.3) !important; }

        /* Ajuste de íconos/widgets con opacidad en los dashboards para no verse opacados */
        body.theme-dark .bg-primary.bg-opacity-10, body.theme-system.system-dark .bg-primary.bg-opacity-10 { background-color: rgba(96, 165, 250, 0.15) !important; }
        body.theme-dark .bg-success.bg-opacity-10, body.theme-system.system-dark .bg-success.bg-opacity-10 { background-color: rgba(52, 211, 153, 0.15) !important; }
        body.theme-dark .bg-info.bg-opacity-10, body.theme-system.system-dark .bg-info.bg-opacity-10 { background-color: rgba(56, 189, 248, 0.15) !important; }
        body.theme-dark .bg-warning.bg-opacity-10, body.theme-system.system-dark .bg-warning.bg-opacity-10 { background-color: rgba(251, 191, 36, 0.15) !important; }
        body.theme-dark .bg-danger.bg-opacity-10, body.theme-system.system-dark .bg-danger.bg-opacity-10 { background-color: rgba(248, 113, 113, 0.15) !important; }

        body.theme-dark .text-primary, body.theme-system.system-dark .text-primary { color: #60a5fa !important; }
        body.theme-dark .text-success, body.theme-system.system-dark .text-success { color: #34d399 !important; }
        body.theme-dark .text-info, body.theme-system.system-dark .text-info { color: #38bdf8 !important; }
        body.theme-dark .text-warning, body.theme-system.system-dark .text-warning { color: #fbbf24 !important; }
        body.theme-dark .text-danger, body.theme-system.system-dark .text-danger { color: #f87171 !important; }
        body.theme-dark .text-dark, body.theme-dark .text-body, body.theme-system.system-dark .text-dark, body.theme-system.system-dark .text-body { color: #f8fafc !important; }
        body.theme-dark .text-secondary, body.theme-system.system-dark .text-secondary { color: #cbd5e1 !important; }
        body.theme-dark .text-muted, body.theme-dark .text-body-secondary, body.theme-system.system-dark .text-muted, body.theme-system.system-dark .text-body-secondary { color: #94a3b8 !important; }
        body.theme-dark .bg-light, body.theme-dark .bg-body, body.theme-system.system-dark .bg-light, body.theme-system.system-dark .bg-body { background-color: rgba(255, 255, 255, 0.03) !important; }
        body.theme-dark .bg-white, body.theme-system.system-dark .bg-white { background-color: rgba(255, 255, 255, 0.05) !important; }
        body.theme-dark .bg-body-tertiary, body.theme-system.system-dark .bg-body-tertiary { background-color: rgba(0, 0, 0, 0.2) !important; }
        body.theme-dark .border-light, body.theme-dark .border-secondary, body.theme-system.system-dark .border-light, body.theme-system.system-dark .border-secondary { border-color: rgba(255, 255, 255, 0.1) !important; }
        body.theme-dark .form-control, body.theme-dark .form-select, body.theme-dark .input-group-text, body.theme-dark textarea, body.theme-system.system-dark .form-control, body.theme-system.system-dark .form-select, body.theme-system.system-dark .input-group-text, body.theme-system.system-dark textarea { background-color: rgba(0, 0, 0, 0.2) !important; color: #f8fafc !important; border-color: rgba(255, 255, 255, 0.1) !important; }
        body.theme-dark .form-control::placeholder, body.theme-dark textarea::placeholder, body.theme-system.system-dark .form-control::placeholder, body.theme-system.system-dark textarea::placeholder { color: rgba(255, 255, 255, 0.4) !important; }
        body.theme-dark .btn-light, body.theme-system.system-dark .btn-light { background: #1f2937 !important; border-color: #374151 !important; color: #e5e7eb; }
        body.theme-dark .table, body.theme-system.system-dark .table { --bs-table-bg: transparent !important; --bs-table-color: #f8fafc !important; }
        body.theme-dark .table > :not(caption) > * > *, body.theme-system.system-dark .table > :not(caption) > * > * { background-color: transparent !important; color: #f8fafc !important; border-bottom-color: rgba(255, 255, 255, 0.08) !important; }

        /* ==============================================
           DISEÑO PREMIUM DEL LOGO Y HEADER SIDEBAR
           ============================================== */
        .sidebar {
            width: 280px; height: 100vh;
            background: transparent;
            color: var(--app-sidebar-text);
            position: relative; top: 0; left: 0; z-index: 0; isolation: isolate;
            display: flex; flex-direction: column;
            box-shadow: 4px 0 24px rgba(46, 16, 101, 0.15);
            overflow: hidden; /* Necesario para que el canvas no sobresalga */
        }

        /* Conserva position: fixed de Bootstrap; con relative el menú oculto ocupa espacio al final de la página. */
        .offcanvas-sidebar {
            background: transparent;
            color: #fff;
            overflow: hidden;
        }

        /* Configuración del Canvas para Granim.js en el Sidebar */
        #granim-sidebar, #granim-sidebar-mobile {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        /* Aseguramos que todo el contenido del sidebar esté por encima del canvas */
        .sidebar-content-wrapper {
            position: relative;
            z-index: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            width: 100%;
        }

        .offcanvas-header, .offcanvas-body, .offcanvas-sidebar .mt-auto {
            position: relative;
            z-index: 1;
        }

        .sidebar-brand-wrapper {
            display: flex; align-items: center; gap: 1rem;
            padding: 0.85rem; border-radius: 20px;
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            position: relative;
            overflow: hidden;
            text-decoration: none;
            color: inherit;
        }

        .sidebar-brand-wrapper::before {
            content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 60%);
            opacity: 0; transition: opacity 0.4s ease; transform: scale(0.5); pointer-events: none;
        }

        .sidebar-brand-wrapper:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(255, 255, 255, 0.25);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .sidebar-brand-wrapper:hover::before {
            opacity: 1; transform: scale(1);
        }

        .brand-logo-img {
            width: 48px; height: 48px;
            object-fit: contain;
            border-radius: 14px;
            background: #ffffff;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            padding: 4px;
        }

        .sidebar-brand-wrapper:hover .brand-logo-img {
            transform: scale(1.1) rotate(5deg);
        }

        .sidebar-brand { font-size: 1.3rem; font-weight: 900; letter-spacing: 1px; line-height: 1.1; text-shadow: 0 2px 4px rgba(0,0,0,0.2); }
        .sidebar-sub { font-size: 0.78rem; opacity: 0.85; font-weight: 500; }

        /* ==============================================================
           ACORDEÓN NOVEDOSO Y PERSISTENTE
           ============================================================== */
        .sidebar-nav-container { flex-grow: 1; overflow-y: auto; padding: 0 1rem; scrollbar-width: none; -ms-overflow-style: none; }
        .sidebar-nav-container::-webkit-scrollbar { display: none; }

        .nav-section { margin-bottom: 0.2rem; }
        .nav-section-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 0.6rem 1rem; cursor: pointer; border-radius: 12px;
            transition: background-color 0.2s ease, transform 0.2s ease;
            margin-top: 0.5rem;
        }
        .nav-section-header:hover { background-color: rgba(255, 255, 255, 0.1); }
        .nav-section-header:active { transform: scale(0.98); }
        .nav-section-title {
            font-size: 0.72rem; font-weight: 800; text-transform: uppercase;
            letter-spacing: 1.5px; color: rgba(255, 255, 255, 0.5);
            transition: color 0.3s ease;
        }
        .nav-section-header:hover .nav-section-title { color: rgba(255, 255, 255, 0.9); }
        .nav-section-icon {
            font-size: 0.75rem; color: rgba(255, 255, 255, 0.4);
            transition: transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), color 0.3s ease;
        }
        .nav-section-header:hover .nav-section-icon { color: rgba(255, 255, 255, 0.9); }

        .nav-section.is-collapsed .nav-section-icon { transform: rotate(-90deg); }

        .nav-section-body { perspective: 1000px; overflow: hidden; }
        .nav-item-wrapper { transform-origin: top center; }

        /* Estilos de los Links */
        .sidebar .nav-link, .offcanvas-sidebar .nav-link {
            color: rgba(255,255,255,.80); border-radius: 12px; padding: .85rem 1rem; margin-bottom: .2rem;
            display: flex; align-items: center; gap: .85rem; font-weight: 500;
            border-left: 4px solid transparent; transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .sidebar .nav-link:hover, .sidebar .nav-link.active,
        .offcanvas-sidebar .nav-link:hover, .offcanvas-sidebar .nav-link.active {
            background: linear-gradient(90deg, rgba(255, 255, 255, 0.15) 0%, rgba(255, 255, 255, 0) 100%);
            color: #ffffff; border-left: 4px solid #ffffff; padding-left: 1.5rem;
        }
        .sidebar .nav-link i, .offcanvas-sidebar .nav-link i { font-size: 1.15rem; transition: transform 0.3s ease; }
        .sidebar .nav-link:hover i, .sidebar .nav-link.active i,
        .offcanvas-sidebar .nav-link:hover i, .offcanvas-sidebar .nav-link.active i { transform: scale(1.15); }

        /* LAYOUT GENERAL */
        .main-panel{ flex: 1; margin-left: 280px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar{ position: sticky; top: 0; z-index: 1020; background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); border-bottom: 1px solid rgba(226, 232, 240, 0.8); padding: 1rem 1.5rem; box-shadow: 0 4px 20px rgba(0,0,0,0.02); }
        .content-wrapper{ padding: 2rem; flex-grow: 1; }
        .sidebar-header { padding: 1.5rem 1rem 0.5rem 1rem; }
        .sidebar-footer { padding: 1rem; border-top: 1px solid rgba(255, 255, 255, 0.1); margin-top: auto; }

        .soft-badge{ display: inline-flex; align-items: center; gap: .4rem; border-radius: 999px; padding: .4rem .85rem; font-size: .85rem; font-weight: 700; }
        .soft-primary{ background: var(--app-primary-soft); color: var(--app-primary-dark); }
        .btn{ border-radius: 12px; font-weight: 600; padding: .6rem 1.2rem; transition: .25s ease; }
        .sidebar-user-icon{ width: 52px; height: 52px; border-radius: 16px; display: flex; align-items: center; justify-content: center; background: rgba(255,255,255,.18); color: #fff; font-size: 1.35rem; flex-shrink: 0; }

        body.density-compact .content-wrapper{ padding: 1.2rem; }
        body.density-compact .app-card{ border-radius: 16px; }
        body.density-compact .btn{ padding: .45rem .9rem; font-size: .92rem; }
        body.density-compact .sidebar .nav-link{ padding: .7rem .9rem; }

        @media (max-width: 991.98px){
            .sidebar{ display: none; }
            .main-panel{ margin-left: 0; }
            .content-wrapper{ padding: 1.25rem; }
        }

        .anime-topbar, .anime-sidebar-item, .anime-content { opacity: 1; }
        body.reduced-motion *, body.reduced-motion *::before, body.reduced-motion *::after{ animation: none !important; transition: none !important; scroll-behavior: auto !important; }
    </style>

    @stack('styles')
    <noscript><style>.question-step{position:relative!important;visibility:visible!important;opacity:1!important;pointer-events:auto!important;margin-bottom:2rem}.btn-next,.btn-prev,[data-save-draft],.btn-cancelar-eval{display:none!important}#btn-submit{display:inline-block!important}</style></noscript>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/interface.css') }}?v={{ filemtime(public_path('css/interface.css')) }}">
    <link rel="stylesheet" href="{{ asset('css/appearance.css') }}?v={{ filemtime(public_path('css/appearance.css')) }}">
    <script type="application/json" id="appearance-palettes">@json(config('appearance.palettes'))</script>



</head>
<body data-accent="{{ $appearance['accent_color'] }}" class="{{ $themeClass }} {{ $densityClass }} {{ $motionClass }}">
<div class="app-shell">

    <div class="sidebar-anchor d-none d-lg-block">
    <aside class="sidebar d-none d-lg-flex">
        <canvas id="granim-sidebar" class="granim-canvas-sidebar" data-prometeo-granim aria-hidden="true" style="position:absolute;top:0;left:0;width:100%;height:100%;z-index:0;"></canvas>

        <div class="sidebar-content-wrapper">
            <div class="sidebar-header anime-sidebar-item">
                <a href="{{ route('home') }}" class="sidebar-brand-wrapper">
                    <img src="{{ asset('img/logo_prometeo.png') }}" alt="PROMETEO Logo" class="brand-logo-img">
                    <div class="brand-text-container">
                        <div class="sidebar-brand font-title text-truncate">PROMETEO</div>
                        <div class="sidebar-sub text-truncate">Monitoreo emocional</div>
                    </div>
                </a>
                <div class="my-3 border-top border-light border-opacity-25"></div>
            </div>

            <div class="sidebar-nav-container pb-4">
                @auth
                    @if(auth()->user()->hasRole('admin'))

                        <div class="nav-section anime-sidebar-item" data-section-name="admin_gestion">
                            <div class="nav-section-header">
                                <span class="nav-section-title">Gestión</span>
                                <i class="bi bi-chevron-down nav-section-icon"></i>
                            </div>
                            <div class="nav-section-body">
                                <div class="nav-item-wrapper"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Panel admin</a></div>
                                @can('usuarios.ver')
                                    <div class="nav-item-wrapper"><a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Usuarios</a></div>
                                @endcan
                                @can('personas.ver')
                                    <div class="nav-item-wrapper"><a href="{{ route('admin.personas.index') }}" class="nav-link {{ request()->routeIs('admin.personas.*') ? 'active' : '' }}"><i class="bi bi-person-vcard-fill"></i> Personas</a></div>
                                @endcan
                                <div class="nav-item-wrapper"><a href="{{ route('admin.usuarios-sin-persona.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios-sin-persona.*') ? 'active' : '' }}"><i class="bi bi-person-dash-fill"></i> Sin persona</a></div>
                                <div class="nav-item-wrapper"><a href="{{ route('admin.expedientes-pendientes.index') }}" class="nav-link {{ request()->routeIs('admin.expedientes-pendientes.*') ? 'active' : '' }}"><i class="bi bi-person-exclamation"></i> Sin expediente</a></div>
                            </div>
                        </div>


                        <div class="nav-section anime-sidebar-item" data-section-name="admin_clinica">
                            <div class="nav-section-header">
                                <span class="nav-section-title">Clínica</span>
                                <i class="bi bi-chevron-down nav-section-icon"></i>
                            </div>
                            <div class="nav-section-body">
                                @can('usuarios.ver')
                                    <div class="nav-item-wrapper">
                                        <a href="{{ route('admin.psicologos.index') }}" class="nav-link {{ request()->routeIs('admin.psicologos.*') ? 'active' : '' }}">
                                            <i class="bi bi-heart-pulse-fill"></i> Psicólogos
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        </div>

                        <div class="nav-section anime-sidebar-item" data-section-name="admin_seguridad">
                            <div class="nav-section-header">
                                <span class="nav-section-title">Seguridad</span>
                                <i class="bi bi-chevron-down nav-section-icon"></i>
                            </div>
                            <div class="nav-section-body">
                                @can('roles.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"><i class="bi bi-shield-lock-fill"></i> Roles</a></div>@endcan
                                @can('permisos.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.permisos.index') }}" class="nav-link {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}"><i class="bi bi-key-fill"></i> Permisos</a></div>@endcan
                            </div>
                        </div>

                        <div class="nav-section anime-sidebar-item" data-section-name="admin_control_escolar">
                            <div class="nav-section-header">
                                <span class="nav-section-title">Control Escolar</span>
                                <i class="bi bi-chevron-down nav-section-icon"></i>
                            </div>
                            <div class="nav-section-body">
                                @can('control_escolar.dashboard')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.dashboard') }}" class="nav-link {{ request()->routeIs('control_escolar.dashboard') ? 'active' : '' }}"><i class="bi bi-building"></i> Panel Escolar</a></div>@endcan
                                @can('estudiantes.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.estudiantes.index') }}" class="nav-link {{ request()->routeIs('control_escolar.estudiantes.*') ? 'active' : '' }}"><i class="bi bi-mortarboard-fill"></i> Estudiantes</a></div>@endcan
                                @can('tutores.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.tutores.index') }}" class="nav-link {{ request()->routeIs('control_escolar.tutores.*') ? 'active' : '' }}"><i class="bi bi-person-video3"></i> Tutores</a></div>@endcan
                                @can('grupos.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.grupos.index') }}" class="nav-link {{ request()->routeIs('control_escolar.grupos.*') ? 'active' : '' }}"><i class="bi bi-collection-fill"></i> Grupos</a></div>@endcan
                                @can('estudiantes.ver_pendientes')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.pendientes.index') }}" class="nav-link {{ request()->routeIs('control_escolar.pendientes.*') ? 'active' : '' }}"><i class="bi bi-person-exclamation"></i> Pendientes por asignar</a></div>@endcan
                                @can('carreras.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.carreras.index') }}" class="nav-link {{ request()->routeIs('control_escolar.carreras.*') ? 'active' : '' }}"><i class="bi bi-book-fill"></i> Carreras</a></div>@endcan
                                @can('ciclos_escolares.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.ciclos-escolares.index') }}" class="nav-link {{ request()->routeIs('control_escolar.ciclos-escolares.*') ? 'active' : '' }}"><i class="bi bi-calendar-event-fill"></i> Ciclos Escolares</a></div>@endcan
                                @can('tutores.asignar_grupo')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.asignaciones.index') }}" class="nav-link {{ request()->routeIs('control_escolar.asignaciones.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Asignaciones</a></div>@endcan
                            </div>
                        </div>

                    @elseif(auth()->user()->hasRole('control_escolar'))
                        <div class="nav-section anime-sidebar-item" data-section-name="ce_panel">
                            <div class="nav-section-header"><span class="nav-section-title">Control Escolar</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                            <div class="nav-section-body">
                                <div class="nav-item-wrapper"><a href="{{ route('control_escolar.dashboard') }}" class="nav-link {{ request()->routeIs('control_escolar.dashboard') ? 'active' : '' }}"><i class="bi bi-building"></i> Panel Escolar</a></div>
                                @can('estudiantes.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.estudiantes.index') }}" class="nav-link {{ request()->routeIs('control_escolar.estudiantes.*') ? 'active' : '' }}"><i class="bi bi-mortarboard-fill"></i> Estudiantes</a></div>@endcan
                                @can('tutores.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.tutores.index') }}" class="nav-link {{ request()->routeIs('control_escolar.tutores.*') ? 'active' : '' }}"><i class="bi bi-person-video3"></i> Tutores</a></div>@endcan
                                @can('grupos.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.grupos.index') }}" class="nav-link {{ request()->routeIs('control_escolar.grupos.*') ? 'active' : '' }}"><i class="bi bi-collection-fill"></i> Grupos</a></div>@endcan
                                @can('estudiantes.ver_pendientes')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.pendientes.index') }}" class="nav-link {{ request()->routeIs('control_escolar.pendientes.*') ? 'active' : '' }}"><i class="bi bi-person-exclamation"></i> Pendientes por asignar</a></div>@endcan
                                @can('carreras.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.carreras.index') }}" class="nav-link {{ request()->routeIs('control_escolar.carreras.*') ? 'active' : '' }}"><i class="bi bi-book-fill"></i> Carreras</a></div>@endcan
                                @can('ciclos_escolares.ver')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.ciclos-escolares.index') }}" class="nav-link {{ request()->routeIs('control_escolar.ciclos-escolares.*') ? 'active' : '' }}"><i class="bi bi-calendar-event-fill"></i> Ciclos Escolares</a></div>@endcan
                                @can('tutores.asignar_grupo')<div class="nav-item-wrapper"><a href="{{ route('control_escolar.asignaciones.index') }}" class="nav-link {{ request()->routeIs('control_escolar.asignaciones.*') ? 'active' : '' }}"><i class="bi bi-arrow-left-right"></i> Asignaciones</a></div>@endcan
                            </div>
                        </div>

                    @elseif(auth()->user()->hasRole('estudiante'))
                        <div class="nav-section anime-sidebar-item" data-section-name="estud_cuenta">
                            <div class="nav-section-header"><span class="nav-section-title">Mi Cuenta</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                            <div class="nav-section-body">
                                <div class="nav-item-wrapper"><a href="{{ route('estudiante.dashboard') }}" class="nav-link {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}"><i class="bi bi-house-door-fill"></i> Inicio</a></div>
                                @can('evaluaciones.realizar')<div class="nav-item-wrapper"><a href="{{ route('evaluaciones.index') }}" class="nav-link {{ request()->routeIs('evaluaciones.*') ? 'active' : '' }}"><i class="bi bi-clipboard2-check-fill"></i> Evaluaciones</a></div>@endcan
                                @can('diario_ia.ver.propio')<div class="nav-item-wrapper"><a href="{{ route('diario.index') }}" class="nav-link {{ request()->routeIs('diario.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Diario IA</a></div>@endcan
                            </div>
                        </div>

                    @elseif(auth()->user()->hasRole('tutor'))
                        <div class="nav-section anime-sidebar-item" data-section-name="tutor_gestion">
                            <div class="nav-section-header"><span class="nav-section-title">Gestión</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                            <div class="nav-section-body">
                                <div class="nav-item-wrapper"><a href="{{ route('tutor.dashboard') }}" class="nav-link {{ request()->routeIs('tutor.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Panel tutor</a></div>
                                @can('grupos.ver.asignados')<div class="nav-item-wrapper"><a href="{{ route('tutor.grupos.index') }}" class="nav-link {{ request()->routeIs('tutor.grupos.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Mis grupos</a></div>@endcan
                        @can('alertas.ver.general')@can('grupos.ver.asignados')@can('usuarios.ver.grupo')<div class="nav-item-wrapper"><a href="{{ route('tutor.seguimiento') }}" class="nav-link {{ request()->routeIs('tutor.seguimiento') ? 'active' : '' }}"><i class="bi bi-heart-pulse"></i> Seguimiento</a></div>@endcan
                        @endcan
                        @endcan
                            </div>
                        </div>

                    @elseif(auth()->user()->hasRole('psicologo'))
                        <div class="nav-section anime-sidebar-item" data-section-name="psico_clinica">
                            <div class="nav-section-header"><span class="nav-section-title">Clínica</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                            <div class="nav-section-body">
                                <div class="nav-item-wrapper"><a href="{{ route('psicologo.dashboard') }}" class="nav-link {{ request()->routeIs('psicologo.dashboard') ? 'active' : '' }}"><i class="bi bi-activity"></i> Panel clínico</a></div>
                                @can('evaluaciones.historial.global')<div class="nav-item-wrapper"><a href="{{ route('psicologo.tamizajes.index') }}" class="nav-link {{ request()->routeIs('psicologo.tamizajes.*') ? 'active' : '' }}"><i class="bi bi-clipboard2-pulse"></i> Tamizajes</a></div>@endcan
                        @canany(['evaluaciones.historial.global', 'resultados_ia.ver'])<div class="nav-item-wrapper"><a href="{{ route('psicologo.casos.index') }}" class="nav-link {{ request()->routeIs('psicologo.casos.*') ? 'active' : '' }}"><i class="bi bi-journal-medical"></i> Casos de atención</a></div>@endcanany
                        @can('alertas.ver.clinicas')<div class="nav-item-wrapper"><a href="{{ route('alertas.index') }}" class="nav-link {{ request()->routeIs('alertas.*') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle-fill"></i> Alertas</a></div>@endcan
                                @can('diagnosticos.ver')<div class="nav-item-wrapper"><a href="{{ route('diagnosticos.index') }}" class="nav-link {{ request()->routeIs('diagnosticos.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-medical-fill"></i> Diagnósticos</a></div>@endcan
                                @can('resultados_ia.ver')<div class="nav-item-wrapper"><a href="{{ route('analisis.index') }}" class="nav-link {{ request()->routeIs('analisis.*') ? 'active' : '' }}"><i class="bi bi-robot"></i> Resultados IA</a></div>@endcan
                            </div>
                        </div>
                    @endif

                    <div class="nav-section anime-sidebar-item" data-section-name="global_ajustes">
                        <div class="nav-section-header"><span class="nav-section-title">Ajustes</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                        <div class="nav-section-body">
                            @include('components.delivery-four-nav')
                            @can('perfil.ver')<div class="nav-item-wrapper"><a href="{{ route('perfil.index') }}" class="nav-link {{ request()->routeIs('perfil.*') || request()->routeIs('profile.*') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Mi Perfil</a></div>@endcan
                            <div class="nav-item-wrapper"><a href="{{ route('aviso.privacidad') }}" class="nav-link {{ request()->routeIs('aviso.privacidad') ? 'active' : '' }}"><i class="bi bi-shield-check"></i> Aviso legal</a></div>
                        </div>
                    </div>
                @endauth
            </div>

            <div class="sidebar-footer anime-sidebar-item">
                @auth
                    <div class="bg-white bg-opacity-10 rounded-4 p-3 mb-3 border border-white border-opacity-10">
                        <div class="d-flex align-items-center gap-3">
                            <div class="sidebar-user-icon"><x-profile-avatar :user="$authUser" /></div>
                            <div class="min-w-0" style="flex: 1;">
                                <div class="fw-bold fs-6 text-truncate" style="line-height: 1.2;">{{ auth()->user()->name }}</div>
                                <small class="opacity-75 d-block text-truncate mt-1" style="font-size: 0.75rem;">
                                    {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                                </small>
                            </div>
                        </div>
                    </div>
                @endauth
                <a href="#" data-bs-toggle="modal" data-bs-target="#modalLogout" class="btn btn-light w-100 fw-bold shadow-sm d-flex justify-content-center align-items-center">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                </a>
            </div>
        </div>
    </aside>
    </div>

    <a href="#main-content" class="skip-link">Saltar al contenido</a>
    <div class="main-panel">
        <header class="topbar anime-topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-light d-lg-none border-0 shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-label="Abrir menú de navegación">
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
                        <a href="{{ route('avisos.index') }}" class="notification-bell {{ request()->routeIs('avisos.*') ? 'is-active' : '' }}" aria-label="Centro de notificaciones: {{ $unreadNotices }} sin leer" title="Centro de notificaciones">
                            <i class="bi bi-bell" aria-hidden="true"></i>
                            @if($unreadNotices)<span class="notification-count" aria-hidden="true">{{ $unreadNotices > 99 ? '99+' : $unreadNotices }}</span>@endif
                        </a>
                        <a href="#" data-bs-toggle="modal" data-bs-target="#modalLogout" class="btn btn-outline-danger d-none d-lg-inline-flex align-items-center rounded-pill px-3">
                            <i class="bi bi-power me-2"></i>Salir
                        </a>
                    </div>
                @endauth
            </div>
        </header>

        <main id="main-content" class="content-wrapper anime-content" tabindex="-1">
            <div class="gradient-notice" data-gradient-notice hidden role="status">
                <span data-gradient-message></span>
                <a href="{{ route('perfil.index') }}">Revisar apariencia</a>
            </div>
            @yield('content')
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="mobileSidebar">
    <canvas id="granim-sidebar-mobile" class="granim-canvas-sidebar" data-prometeo-granim aria-hidden="true"></canvas>

    <div class="offcanvas-header border-bottom border-light border-opacity-10 p-4">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ asset('img/logo_prometeo.png') }}" alt="PROMETEO Logo" style="width: 38px; height: 38px; object-fit: contain; background: white; padding: 2px; border-radius: 8px;">
            <h5 class="offcanvas-title fw-black mb-0 font-title">PROMETEO</h5>
        </div>
        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column p-4 overflow-y-auto" style="scrollbar-width: none; -ms-overflow-style: none;">
        @auth
            <div class="d-flex align-items-center gap-3 mb-4">
                <div class="sidebar-user-icon" style="width:40px; height:40px; font-size:1rem;"><x-profile-avatar :user="$authUser" /></div>
                <div class="min-w-0">
                    <div class="fw-bold text-truncate" style="line-height: 1;">{{ auth()->user()->name }}</div>
                    <small class="opacity-75 text-truncate d-block mt-1" style="font-size: 0.75rem;">
                        {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                    </small>
                </div>
            </div>

            @if(auth()->user()->hasRole('admin'))
                <div class="nav-section" data-section-name="mobile_admin_gestion">
                    <div class="nav-section-header"><span class="nav-section-title">Gestión</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                    <div class="nav-section-body">
                        <div class="nav-item-wrapper"><a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Panel admin</a></div>
                        @can('usuarios.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Usuarios</a></div><div class="nav-item-wrapper"><a href="{{ route('admin.expedientes-pendientes.index') }}" class="nav-link {{ request()->routeIs('admin.expedientes-pendientes.*') ? 'active' : '' }}"><i class="bi bi-person-exclamation"></i> Pendientes</a></div>@endcan
                        @can('personas.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.personas.index') }}" class="nav-link {{ request()->routeIs('admin.personas.*') ? 'active' : '' }}"><i class="bi bi-person-vcard-fill"></i> Personas</a></div>@endcan
                    </div>
                </div>
                <div class="nav-section" data-section-name="mobile_admin_escolar">
                    <div class="nav-section-header"><span class="nav-section-title">Escolar</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                    <div class="nav-section-body">
                        @can('usuarios.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.estudiantes.index') }}" class="nav-link {{ request()->routeIs('admin.estudiantes.*') ? 'active' : '' }}"><i class="bi bi-person-badge"></i> Estudiantes</a></div>@endcan
                        @can('usuarios.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.tutores.index') }}" class="nav-link {{ request()->routeIs('admin.tutores.*') ? 'active' : '' }}"><i class="bi bi-person-video3"></i> Tutores</a></div>@endcan
                        @can('grupos.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.grupos.index') }}" class="nav-link {{ request()->routeIs('admin.grupos.*') ? 'active' : '' }}"><i class="bi bi-collection-fill"></i> Grupos</a></div>@endcan
                        @can('carreras.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.carreras.index') }}" class="nav-link {{ request()->routeIs('admin.carreras.*') ? 'active' : '' }}"><i class="bi bi-mortarboard-fill"></i> Carreras</a></div>@endcan
                    </div>
                </div>

                <div class="nav-section" data-section-name="mobile_admin_clinica">
                    <div class="nav-section-header"><span class="nav-section-title">Clínica</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                    <div class="nav-section-body">
                        @can('usuarios.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.psicologos.index') }}" class="nav-link {{ request()->routeIs('admin.psicologos.*') ? 'active' : '' }}"><i class="bi bi-heart-pulse-fill"></i> Psicólogos</a></div>@endcan
                    </div>
                </div>

                <div class="nav-section" data-section-name="mobile_admin_seguridad">
                    <div class="nav-section-header"><span class="nav-section-title">Seguridad</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                    <div class="nav-section-body">
                        @can('roles.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}"><i class="bi bi-shield-lock-fill"></i> Roles</a></div>@endcan
                        @can('permisos.ver')<div class="nav-item-wrapper"><a href="{{ route('admin.permisos.index') }}" class="nav-link {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}"><i class="bi bi-key-fill"></i> Permisos</a></div>@endcan
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('estudiante'))
                <div class="nav-section" data-section-name="mobile_estud_cuenta">
                    <div class="nav-section-header"><span class="nav-section-title">Mi Cuenta</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                    <div class="nav-section-body">
                        <div class="nav-item-wrapper"><a href="{{ route('estudiante.dashboard') }}" class="nav-link {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}"><i class="bi bi-house-door-fill"></i> Inicio</a></div>
                        @can('evaluaciones.realizar')<div class="nav-item-wrapper"><a href="{{ route('evaluaciones.index') }}" class="nav-link {{ request()->routeIs('evaluaciones.*') ? 'active' : '' }}"><i class="bi bi-clipboard2-check-fill"></i> Evaluaciones</a></div>@endcan
                        @can('diario_ia.ver.propio')<div class="nav-item-wrapper"><a href="{{ route('diario.index') }}" class="nav-link {{ request()->routeIs('diario.*') ? 'active' : '' }}"><i class="bi bi-journal-text"></i> Diario IA</a></div>@endcan
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('tutor'))
                <div class="nav-section" data-section-name="mobile_tutor_gestion">
                    <div class="nav-section-header"><span class="nav-section-title">Gestión</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                    <div class="nav-section-body">
                        <div class="nav-item-wrapper"><a href="{{ route('tutor.dashboard') }}" class="nav-link {{ request()->routeIs('tutor.dashboard') ? 'active' : '' }}"><i class="bi bi-grid-1x2-fill"></i> Panel tutor</a></div>
                        @can('grupos.ver.asignados')<div class="nav-item-wrapper"><a href="{{ route('tutor.grupos.index') }}" class="nav-link {{ request()->routeIs('tutor.grupos.*') ? 'active' : '' }}"><i class="bi bi-people-fill"></i> Mis grupos</a></div>@endcan
                        @can('alertas.ver.general')@can('grupos.ver.asignados')@can('usuarios.ver.grupo')<div class="nav-item-wrapper"><a href="{{ route('tutor.seguimiento') }}" class="nav-link {{ request()->routeIs('tutor.seguimiento') ? 'active' : '' }}"><i class="bi bi-heart-pulse"></i> Seguimiento</a></div>@endcan
                        @endcan
                        @endcan
                    </div>
                </div>
            @elseif(auth()->user()->hasRole('psicologo'))
                <div class="nav-section" data-section-name="mobile_psico_clinica">
                    <div class="nav-section-header"><span class="nav-section-title">Clínica</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                    <div class="nav-section-body">
                        <div class="nav-item-wrapper"><a href="{{ route('psicologo.dashboard') }}" class="nav-link {{ request()->routeIs('psicologo.dashboard') ? 'active' : '' }}"><i class="bi bi-activity"></i> Panel clínico</a></div>
                        @can('evaluaciones.historial.global')<div class="nav-item-wrapper"><a href="{{ route('psicologo.tamizajes.index') }}" class="nav-link {{ request()->routeIs('psicologo.tamizajes.*') ? 'active' : '' }}"><i class="bi bi-clipboard2-pulse"></i> Tamizajes</a></div>@endcan
                        @canany(['evaluaciones.historial.global', 'resultados_ia.ver'])<div class="nav-item-wrapper"><a href="{{ route('psicologo.casos.index') }}" class="nav-link {{ request()->routeIs('psicologo.casos.*') ? 'active' : '' }}"><i class="bi bi-journal-medical"></i> Casos de atención</a></div>@endcanany
                        @can('alertas.ver.clinicas')<div class="nav-item-wrapper"><a href="{{ route('alertas.index') }}" class="nav-link {{ request()->routeIs('alertas.*') ? 'active' : '' }}"><i class="bi bi-exclamation-triangle-fill"></i> Alertas</a></div>@endcan
                        @can('diagnosticos.ver')<div class="nav-item-wrapper"><a href="{{ route('diagnosticos.index') }}" class="nav-link {{ request()->routeIs('diagnosticos.*') ? 'active' : '' }}"><i class="bi bi-file-earmark-medical-fill"></i> Diagnósticos</a></div>@endcan
                        @can('resultados_ia.ver')<div class="nav-item-wrapper"><a href="{{ route('analisis.index') }}" class="nav-link {{ request()->routeIs('analisis.*') ? 'active' : '' }}"><i class="bi bi-robot"></i> Resultados IA</a></div>@endcan
                    </div>
                </div>
            @endif

            <div class="nav-section" data-section-name="mobile_global_ajustes">
                <div class="nav-section-header"><span class="nav-section-title">Ajustes</span><i class="bi bi-chevron-down nav-section-icon"></i></div>
                <div class="nav-section-body">
                    @include('components.delivery-four-nav')
                            @can('perfil.ver')<div class="nav-item-wrapper"><a href="{{ route('perfil.index') }}" class="nav-link {{ request()->routeIs('perfil.*') || request()->routeIs('profile.*') ? 'active' : '' }}"><i class="bi bi-person-circle"></i> Mi Perfil</a></div>@endcan
                    <div class="nav-item-wrapper"><a href="{{ route('aviso.privacidad') }}" class="nav-link {{ request()->routeIs('aviso.privacidad') ? 'active' : '' }}"><i class="bi bi-shield-check"></i> Aviso legal</a></div>
                </div>
            </div>
        @endauth
    </div>

    <div class="mt-auto p-4 border-top border-light border-opacity-10">
        <a href="#" data-bs-toggle="modal" data-bs-target="#modalLogout" class="btn btn-light w-100 fw-bold d-flex justify-content-center align-items-center">
            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
        </a>
    </div>
</div>

<!-- Modal de Cerrar Sesión Global -->
<div class="modal fade" id="modalLogout" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center overflow-hidden border-0" style="border-radius: 1.5rem; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);">
            <div class="modal-body p-5">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow-sm" style="width: 80px; height: 80px;">
                    <i class="bi bi-power" style="font-size: 2.5rem;"></i>
                </div>
                <h4 class="fw-black text-body mb-3 font-title">¿Deseas cerrar sesión?</h4>
                <p class="text-body-secondary mb-0">Tu sesión actual será finalizada y tendrás que volver a ingresar para acceder a PROMETEO.</p>
            </div>
            <div class="modal-footer d-flex justify-content-center gap-2 p-4 border-top border-secondary border-opacity-10 bg-body-tertiary">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                    Cancelar
                </button>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-power me-2"></i>Sí, cerrar sesión
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos rápidos y sutiles solo para el modal de Logout */
#modalLogout.fade .modal-dialog {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
    transition: transform 0.2s cubic-bezier(0.2, 0, 0, 1), opacity 0.2s cubic-bezier(0.2, 0, 0, 1) !important;
}
#modalLogout.show .modal-dialog {
    opacity: 1;
    transform: scale(1) translateY(0);
}

/* Soporte para modo oscuro exclusivo del modal de logout */
body.theme-dark #modalLogout .modal-content,
body.theme-system.system-dark #modalLogout .modal-content {
    background-color: #1e293b !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
}
body.theme-dark #modalLogout .modal-body,
body.theme-system.system-dark #modalLogout .modal-body {
    background-color: #0f172a !important;
}
body.theme-dark #modalLogout .modal-footer,
body.theme-system.system-dark #modalLogout .modal-footer {
    background-color: #1e293b !important;
    border-top: 1px solid rgba(255,255,255,0.1) !important;
}
body.theme-dark #modalLogout h4,
body.theme-system.system-dark #modalLogout h4 {
    color: #f8fafc !important;
}
body.theme-dark #modalLogout .btn-light,
body.theme-system.system-dark #modalLogout .btn-light {
    background-color: #334155 !important;
    color: #f8fafc !important;
    border-color: rgba(255,255,255,0.2) !important;
}
body.theme-dark #modalLogout .btn-light:hover,
body.theme-system.system-dark #modalLogout .btn-light:hover {
    background-color: #475569 !important;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const reducedMotion = document.body.classList.contains('reduced-motion');
        if (reducedMotion) {
            document.querySelectorAll('.anime-topbar, .anime-sidebar-item, .anime-content').forEach(el => {
                el.style.opacity = '1'; el.style.transform = 'none';
            });
        } else {
            const tl = anime.timeline({ easing: 'easeOutExpo' });
            tl.add({ targets: '.anime-topbar', translateY: [-20, 0], opacity: [0, 1], duration: 800 })
                .add({ targets: '.anime-sidebar-item', translateX: [-20, 0], opacity: [0, 1], delay: anime.stagger(60), duration: 600 }, '-=600')
                .add({ targets: '.anime-content', translateY: [20, 0], opacity: [0, 1], duration: 800 }, '-=400');
        }


    });
</script>

@include('sweetalert::alert')
@stack('scripts')
{{-- ======================================================= --}}
{{-- BOTIQUÍN EMOCIONAL (Solo visible para Estudiantes)      --}}
{{-- ======================================================= --}}
@if(auth()->check() && auth()->user()->hasRole('estudiante'))

    {{-- 1. Botón Flotante (FAB) --}}
    <button class="wellbeing-trigger btn btn-primary shadow-lg d-flex align-items-center justify-content-center position-fixed" aria-label="Abrir botiquín emocional"
            type="button"
            data-bs-toggle="offcanvas"
            data-bs-target="#offcanvasBienestar"
            aria-controls="offcanvasBienestar"
            style="bottom: 30px; right: 30px; width: 65px; height: 65px; border-radius: 50%; z-index: 1040; transition: transform 0.3s ease;"
            >
        <i class="bi bi-stars fs-3"></i>
    </button>

    {{-- 2. Menú Lateral (Offcanvas) --}}
    <div class="offcanvas offcanvas-end shadow" tabindex="-1" id="offcanvasBienestar" aria-labelledby="offcanvasBienestarLabel" style="width: 400px; border-left: 1px solid rgba(148, 163, 184, 0.2);">
        <div class="offcanvas-header bg-primary bg-opacity-10 border-bottom border-primary border-opacity-10">
            <h5 class="offcanvas-title fw-black text-primary d-flex align-items-center gap-2" id="offcanvasBienestarLabel">
                <i class="bi bi-box2-heart-fill"></i> Botiquín Emocional
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-4 bg-body">
            <p class="text-body-secondary small mb-4">Herramientas rápidas para ayudarte a regular el estrés y la ansiedad dondequiera que estés en el sistema.</p>

            {{-- Widget 1: Box Breathing --}}
            <div class="app-card bg-body-tertiary border border-secondary border-opacity-10 rounded-4 text-center p-4 mb-4 shadow-sm">
                <h6 class="fw-bold text-body mb-1"><i class="bi bi-wind text-info me-2"></i>Respiro Rápido</h6>
                <div class="d-flex align-items-center justify-content-center position-relative my-4" style="height: 100px;">
                    <div id="fab-breathe-circle" class="bg-info bg-opacity-25 position-absolute z-1" style="width: 50px; height: 50px; border-radius: 50%;"></div>
                    <div id="fab-breathe-text" class="fw-bold text-info position-absolute z-2 small">Listo</div>
                </div>
                <button id="fab-btn-breathe" class="btn btn-outline-info rounded-pill w-100 fw-bold btn-sm">Iniciar ejercicio</button>
            </div>

            {{-- Widget 2: Frasco de Gratitud --}}
            <div class="app-card bg-body-tertiary border border-secondary border-opacity-10 rounded-4 text-center p-4 mb-4 shadow-sm">
                <h6 class="fw-bold text-body mb-2"><i class="bi bi-jar-fill text-warning me-2"></i>Micro-Gratitud</h6>
                <input type="text" id="fab-gratitude-input" class="form-control text-center bg-transparent rounded-pill border-secondary border-opacity-25 mb-3 text-body" placeholder="¿Qué fue lo mejor de hoy?">
                <button id="fab-btn-gratitude" class="btn btn-warning text-dark rounded-pill w-100 fw-bold shadow-sm btn-sm">Guardar recuerdo</button>
            </div>

            {{-- Widget 3: Reseteo Mental --}}
            <div class="app-card bg-body-tertiary border border-secondary border-opacity-10 rounded-4 text-center p-4 mb-4 shadow-sm">
                <h6 class="fw-bold text-body mb-2"><i class="bi bi-shuffle text-success me-2"></i>Pausa Activa</h6>
                <div class="bg-success bg-opacity-10 rounded-3 p-2 mb-3" style="min-height: 60px; display: flex; align-items: center; justify-content: center;">
                    <span id="fab-reset-text" class="text-success fw-medium small">Presiona el botón para recibir tu pausa.</span>
                </div>
                <button id="fab-btn-reset" class="btn btn-outline-success rounded-pill w-100 fw-bold btn-sm">Dame un respiro</button>
            </div>
        </div>
    </div>

    {{-- Dependencias Globales para el Botiquín --}}
    {{-- (Nos aseguramos de cargar anime.js y confetti solo si es estudiante) --}}

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {

            // --- BOX BREATHING ---
            const btnBreathe = document.getElementById('fab-btn-breathe');
            const circleBreathe = document.getElementById('fab-breathe-circle');
            const textBreathe = document.getElementById('fab-breathe-text');
            let isBreathing = false;
            let breatheAnim;

            btnBreathe.addEventListener('click', () => {
                if (!isBreathing) {
                    isBreathing = true;
                    btnBreathe.innerText = 'Detener ejercicio';
                    btnBreathe.classList.replace('btn-outline-info', 'btn-danger');
                    circleBreathe.classList.replace('bg-opacity-25', 'bg-opacity-50');

                    breatheAnim = anime.timeline({ loop: true, autoplay: true })
                        .add({ targets: circleBreathe, scale: 2.2, duration: 4000, easing: 'easeInOutSine', begin: () => { textBreathe.innerText = "Inhala..."; }})
                        .add({ targets: circleBreathe, duration: 4000, begin: () => { textBreathe.innerText = "Sostén..."; }})
                        .add({ targets: circleBreathe, scale: 1, duration: 4000, easing: 'easeInOutSine', begin: () => { textBreathe.innerText = "Exhala..."; }})
                        .add({ targets: circleBreathe, duration: 4000, begin: () => { textBreathe.innerText = "Sostén..."; }});
                } else {
                    isBreathing = false;
                    if(breatheAnim) breatheAnim.pause();
                    anime({ targets: circleBreathe, scale: 1, duration: 500, easing: 'easeOutQuad' });
                    circleBreathe.classList.replace('bg-opacity-50', 'bg-opacity-25');
                    textBreathe.innerText = "Listo";
                    btnBreathe.innerText = 'Iniciar ejercicio';
                    btnBreathe.classList.replace('btn-danger', 'btn-outline-info');
                }
            });

            // --- FRASCO DE GRATITUD ---
            const btnGrat = document.getElementById('fab-btn-gratitude');
            const inputGrat = document.getElementById('fab-gratitude-input');

            btnGrat.addEventListener('click', () => {
                if(inputGrat.value.trim() !== "") {
                    // La posición 'x' ajusta el confeti al lado derecho donde está el Offcanvas
                    if (!window.prometeoReducedMotion?.()) confetti({ particleCount: 100, spread: 70, origin: { y: 0.6, x: 0.8 }, colors: ['#ffc107', '#ff9800', '#ffffff'], zIndex: 1055 });
                    inputGrat.value = "";
                    inputGrat.placeholder = "¡Recuerdo guardado!";
                    setTimeout(() => { inputGrat.placeholder = "¿Qué fue lo mejor de hoy?"; }, 3000);
                } else {
                    anime({ targets: inputGrat, translateX: [0, -10, 10, -10, 10, 0], duration: 400, easing: 'easeInOutSine' });
                }
            });

            // --- PAUSAS ACTIVAS ---
            const pausas = [
                "Toma un vaso completo de agua ahora mismo.",
                "Mira por la ventana hacia el punto más lejano por 30 seg.",
                "Estira los brazos hacia el techo y respira hondo 3 veces.",
                "Levántate, da 10 pasos por tu habitación y vuelve.",
                "Cierra los ojos y nombra mentalmente 3 cosas que escuchas.",
                "Sonríe forzadamente por 10 segundos (¡engaña a tu cerebro!)."
            ];
            const btnReset = document.getElementById('fab-btn-reset');
            const textReset = document.getElementById('fab-reset-text');

            btnReset.addEventListener('click', () => {
                const randomPausa = pausas[Math.floor(Math.random() * pausas.length)];
                anime({
                    targets: textReset, opacity: [1, 0], duration: 200, easing: 'easeInQuad',
                    complete: function() {
                        textReset.innerText = randomPausa;
                        anime({ targets: textReset, opacity: [0, 1], duration: 300, easing: 'easeOutQuad' });
                    }
                });
            });
        });
    </script>
@endif
{{-- ======================================================= --}}
<script src="{{ asset('js/granim.min.js') }}?v={{ filemtime(public_path('js/granim.min.js')) }}"></script>
<script src="{{ asset('js/prometeo-sidebar.js') }}?v={{ filemtime(public_path('js/prometeo-sidebar.js')) }}"></script>
<script src="{{ asset('js/prometeo-gradients.js') }}?v={{ filemtime(public_path('js/prometeo-gradients.js')) }}"></script>
</body>
</html>
