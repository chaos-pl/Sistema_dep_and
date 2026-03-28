<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'PROMETEO'))</title>

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
            --app-primary: #7c3aed;
            --app-primary-dark: #6d28d9;
            --app-primary-soft: #ede9fe;
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

        /* 3. MENÚ LATERAL (SIDEBAR) */
        .sidebar{
            width: 280px;
            min-height: 100vh;
            /* Gradiente oscuro alineado con el login */
            background: linear-gradient(180deg, #2e1065 0%, #7c3aed 100%);
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
            color: rgba(255,255,255,.85);
            border-radius: 12px;
            padding: .85rem 1.15rem;
            margin-bottom: .4rem;
            display: flex;
            align-items: center;
            gap: .85rem;
            font-weight: 500;
            transition: all .3s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active{
            background: rgba(255,255,255,.15);
            color: #fff;
            transform: translateX(4px); /* Pequeño salto al hacer hover */
        }

        .sidebar .nav-link i { font-size: 1.1rem; }

        /* 4. PANEL PRINCIPAL Y TOPBAR */
        .main-panel{
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        .topbar{
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(12px); /* Efecto cristal */
            -webkit-backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(226, 232, 240, 0.8);
            padding: 1rem 1.5rem;
            box-shadow: 0 4px 20px rgba(0,0,0,0.02);
        }

        .content-wrapper{
            padding: 2rem;
            flex-grow: 1;
        }

        /* 5. TARJETAS Y COMPONENTES */
        .app-card{
            background: var(--app-surface);
            border: 1px solid var(--app-border);
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(148, 163, 184, 0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .metric-icon{
            width: 56px;
            height: 56px;
            border-radius: 16px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--app-primary-soft);
            color: var(--app-primary-dark);
            font-size: 1.5rem;
        }

        .soft-badge{
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            border-radius: 999px;
            padding: .4rem .85rem;
            font-size: .85rem;
            font-weight: 700;
        }

        .soft-primary{ background: var(--app-primary-soft); color: var(--app-primary-dark); }

        .btn{ border-radius: 12px; font-weight: 600; padding: .6rem 1.2rem; transition: .25s ease; }
        .btn-primary{ background: var(--app-primary); border-color: var(--app-primary); }
        .btn-primary:hover{ background: var(--app-primary-dark); border-color: var(--app-primary-dark); transform: translateY(-1px); box-shadow: 0 4px 12px rgba(124, 58, 237, 0.2); }

        .form-control, .form-select, textarea{
            border-radius: 12px !important;
            border: 2px solid var(--app-border) !important;
            padding: .75rem 1rem !important;
            box-shadow: none !important;
            transition: border-color 0.2s;
        }

        .form-control:focus, .form-select:focus, textarea:focus{
            border-color: var(--app-primary) !important;
            box-shadow: 0 0 0 .25rem rgba(124, 58, 237, .1) !important;
        }

        /* 6. RESPONSIVE Y MOBILE SIDEBAR */
        .offcanvas-sidebar{
            background: linear-gradient(180deg, #2e1065 0%, #7c3aed 100%);
            color: #fff;
        }

        @media (max-width: 991.98px){
            .sidebar{ display: none; }
            .main-panel{ margin-left: 0; }
            .content-wrapper{ padding: 1.25rem; }
        }

        /* Ocultar elementos para la animación inicial */
        .anime-topbar, .anime-sidebar-item, .anime-content {
            opacity: 0;
        }
    </style>

    @stack('styles')
</head>
<body>
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
                    <a href="{{ route('perfil.index') }}" class="nav-link anime-sidebar-item {{ request()->routeIs('perfil.*') ? 'active' : '' }}">
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
                    <div class="fw-bold fs-6">{{ auth()->user()->name }}</div>
                    <small class="opacity-75 d-block text-truncate">
                        {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                    </small>
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
                        <h1 class="h4 fw-black mb-0 text-dark">@yield('page-title', 'Panel de Control')</h1>
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
        <nav class="nav flex-column gap-2">
            @auth
                @if(auth()->user()->hasRole('admin'))
                    <a href="{{ route('admin.dashboard') }}" class="nav-link text-white bg-white bg-opacity-10 rounded-3 px-3 py-2"><i class="bi bi-grid-1x2-fill me-2"></i> Panel admin</a>
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
            <a href="{{ route('logout.view') }}" class="btn btn-light w-100 fw-bold">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<script>
    /* global anime */
    document.addEventListener('DOMContentLoaded', () => {
        const tl = anime.timeline({ easing: 'easeOutExpo' });

        // 1. La barra superior (Topbar) cae desde arriba
        tl.add({
            targets: '.anime-topbar',
            translateY: [-20, 0],
            opacity: [0, 1],
            duration: 800
        })

            // 2. Los elementos del menú lateral (Sidebar) se deslizan en cascada
            .add({
                targets: '.anime-sidebar-item',
                translateX: [-20, 0],
                opacity: [0, 1],
                delay: anime.stagger(60), // Cascada rápida entre los links
                duration: 600
            }, '-=600') // Comienza un poco antes de que termine el Topbar

            // 3. El contenido dinámico principal sube suavemente
            .add({
                targets: '.anime-content',
                translateY: [20, 0],
                opacity: [0, 1],
                duration: 800
            }, '-=400'); // Comienza casi al mismo tiempo que el Sidebar
    });
</script>

@include('sweetalert::alert')
@stack('scripts')
</body>
</html>
