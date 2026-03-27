<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PROMETEO')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root{
            --app-bg: #f7f4fb;
            --app-surface: #ffffff;
            --app-primary: #9b72cf;
            --app-primary-dark: #8258bb;
            --app-primary-soft: #efe6fb;
            --app-text: #374151;
            --app-muted: #6b7280;
            --app-border: #eadff7;
            --app-sidebar-text: #f8f5ff;
            --app-success-soft: #eaf7ee;
            --app-warning-soft: #fff5db;
            --app-danger-soft: #fdecef;
            --app-info-soft: #edf2ff;
        }

        body{
            background: var(--app-bg);
            color: var(--app-text);
            font-family: "Inter", "Segoe UI", sans-serif;
        }

        .app-shell{
            min-height: 100vh;
            display: flex;
        }

        .sidebar{
            width: 280px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--app-primary) 0%, var(--app-primary-dark) 100%);
            color: var(--app-sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            padding: 1.25rem 1rem;
            box-shadow: 0 12px 30px rgba(107, 70, 193, .15);
            overflow-y: auto;
        }

        .sidebar-brand{
            font-size: 1.1rem;
            font-weight: 700;
        }

        .sidebar-sub{
            font-size: .9rem;
            opacity: .9;
        }

        .sidebar .nav-link{
            color: rgba(255,255,255,.95);
            border-radius: 16px;
            padding: .85rem 1rem;
            margin-bottom: .35rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            font-weight: 500;
            transition: .2s ease;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active{
            background: rgba(255,255,255,.16);
            color: #fff;
        }

        .main-panel{
            flex: 1;
            margin-left: 280px;
            min-height: 100vh;
        }

        .topbar{
            position: sticky;
            top: 0;
            z-index: 1020;
            background: rgba(255,255,255,.85);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--app-border);
            padding: 1rem 1.25rem;
        }

        .content-wrapper{
            padding: 1.5rem;
        }

        .app-card{
            background: var(--app-surface);
            border: 1px solid var(--app-border);
            border-radius: 24px;
            box-shadow: 0 10px 24px rgba(155,114,207,.06);
        }

        .welcome-card{
            background: linear-gradient(135deg, #f6f0ff 0%, #ffffff 100%);
        }

        .metric-icon{
            width: 52px;
            height: 52px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: var(--app-primary-soft);
            color: var(--app-primary-dark);
            font-size: 1.25rem;
        }

        .soft-badge{
            display: inline-flex;
            align-items: center;
            gap: .35rem;
            border-radius: 999px;
            padding: .45rem .8rem;
            font-size: .8rem;
            font-weight: 700;
        }

        .soft-primary{ background: var(--app-primary-soft); color: var(--app-primary-dark); }
        .soft-success{ background: var(--app-success-soft); color: #18794e; }
        .soft-warning{ background: var(--app-warning-soft); color: #946200; }
        .soft-danger{ background: var(--app-danger-soft); color: #b42318; }
        .soft-info{ background: var(--app-info-soft); color: #3b5bcc; }

        .btn{
            border-radius: 16px;
            font-weight: 600;
            padding: .72rem 1rem;
        }

        .btn-primary{
            background: var(--app-primary);
            border-color: var(--app-primary);
        }

        .btn-primary:hover{
            background: var(--app-primary-dark);
            border-color: var(--app-primary-dark);
        }

        .form-control,
        .form-select,
        textarea{
            border-radius: 16px !important;
            border: 1px solid var(--app-border) !important;
            padding: .85rem 1rem !important;
            box-shadow: none !important;
        }

        .form-control:focus,
        .form-select:focus,
        textarea:focus{
            border-color: #bea1e7 !important;
            box-shadow: 0 0 0 .2rem rgba(155,114,207,.14) !important;
        }

        .table thead th{
            color: var(--app-muted);
            font-weight: 700;
            border-bottom: 1px solid var(--app-border);
        }

        .table tbody td{
            vertical-align: middle;
            border-color: #f2ebfb;
        }

        .offcanvas-sidebar{
            background: linear-gradient(180deg, var(--app-primary) 0%, var(--app-primary-dark) 100%);
            color: #fff;
        }

        @media (max-width: 991.98px){
            .sidebar{
                display: none;
            }

            .main-panel{
                margin-left: 0;
            }

            .content-wrapper{
                padding: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-shell">
    <aside class="sidebar d-none d-lg-flex flex-column">
        <div class="mb-4">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-white text-primary rounded-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-heart-pulse-fill fs-4"></i>
                </div>
                <div>
                    <div class="sidebar-brand">PROMETEO</div>
                    <div class="sidebar-sub">Monitoreo emocional estudiantil</div>
                </div>
            </div>
        </div>

        <nav class="nav flex-column">
            @auth
                @role('admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Panel admin
                </a>

                @can('usuarios.ver')
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link {{ request()->routeIs('admin.usuarios.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Usuarios
                    </a>
                @endcan

                @can('personas.ver')
                    <a href="{{ route('admin.personas.index') }}" class="nav-link {{ request()->routeIs('admin.personas.*') ? 'active' : '' }}">
                        <i class="bi bi-person-vcard"></i> Personas
                    </a>
                @endcan

                @can('roles.ver')
                    <a href="{{ route('admin.roles.index') }}" class="nav-link {{ request()->routeIs('admin.roles.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i> Roles
                    </a>
                @endcan

                @can('permisos.ver')
                    <a href="{{ route('admin.permisos.index') }}" class="nav-link {{ request()->routeIs('admin.permisos.*') ? 'active' : '' }}">
                        <i class="bi bi-key"></i> Permisos
                    </a>
                @endcan
                @elserole('estudiante')
                <a href="{{ route('estudiante.dashboard') }}" class="nav-link {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-house-door"></i> Inicio
                </a>

                @can('evaluaciones.realizar')
                    <a href="{{ route('evaluaciones.index') }}" class="nav-link {{ request()->routeIs('evaluaciones.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i> Evaluaciones
                    </a>
                @endcan

                @can('diario_ia.ver.propio')
                    <a href="{{ route('diario.index') }}" class="nav-link {{ request()->routeIs('diario.*') ? 'active' : '' }}">
                        <i class="bi bi-journal-text"></i> Diario IA
                    </a>
                @endcan

                @can('consentimiento.ver')
                    <a href="{{ route('consentimiento.create') }}" class="nav-link {{ request()->routeIs('consentimiento.*') ? 'active' : '' }}">
                        <i class="bi bi-shield-check"></i> Consentimiento
                    </a>
                @endcan
                @elserole('tutor')
                <a href="{{ route('tutor.dashboard') }}" class="nav-link {{ request()->routeIs('tutor.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i> Panel tutor
                </a>

                @can('grupos.ver.asignados')
                    <a href="{{ route('grupos.index') }}" class="nav-link {{ request()->routeIs('grupos.*') ? 'active' : '' }}">
                        <i class="bi bi-people"></i> Mis grupos
                    </a>
                @endcan
                @elserole('psicologo')
                <a href="{{ route('psicologo.dashboard') }}" class="nav-link {{ request()->routeIs('psicologo.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-activity"></i> Panel clínico
                </a>

                @can('alertas.ver.clinicas')
                    <a href="{{ route('alertas.index') }}" class="nav-link {{ request()->routeIs('alertas.*') ? 'active' : '' }}">
                        <i class="bi bi-exclamation-triangle"></i> Alertas
                    </a>
                @endcan

                @can('diagnosticos.ver')
                    <a href="{{ route('diagnosticos.index') }}" class="nav-link {{ request()->routeIs('diagnosticos.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-medical"></i> Diagnósticos
                    </a>
                @endcan

                @can('resultados_ia.ver')
                    <a href="{{ route('analisis.index') }}" class="nav-link {{ request()->routeIs('analisis.*') ? 'active' : '' }}">
                        <i class="bi bi-robot"></i> Resultados IA
                    </a>
                @endcan
                @endrole

                @can('perfil.ver')
                    <a href="{{ route('perfil.index') }}" class="nav-link {{ request()->routeIs('perfil.*') ? 'active' : '' }}">
                        <i class="bi bi-person-circle"></i> Perfil
                    </a>
                @endcan
            @endauth

            <a href="{{ route('aviso.privacidad') }}" class="nav-link {{ request()->routeIs('aviso.privacidad') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-lock"></i> Aviso de privacidad
            </a>
        </nav>

        <div class="mt-auto pt-4">
            @auth
                <div class="bg-white bg-opacity-10 rounded-4 p-3 mb-3">
                    <div class="fw-semibold">{{ auth()->user()->name }}</div>
                    <small class="opacity-75">
                        {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                    </small>
                </div>
            @endauth

                <a href="{{ route('logout.view') }}" class="btn btn-light w-100">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                </a>
        </div>
    </aside>

    <div class="main-panel">
        <header class="topbar">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light d-lg-none" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h1 class="h5 fw-bold mb-0">@yield('page-title', 'Panel')</h1>
                        <small class="text-muted">@yield('page-subtitle', 'Bienvenido')</small>
                    </div>
                </div>

                @auth
                    <div class="d-flex align-items-center gap-2">
                        <span class="soft-badge soft-primary d-none d-md-inline-flex">
                            <i class="bi bi-person-badge"></i>
                            {{ auth()->user()->getRoleNames()->map(fn($role) => ucfirst($role))->implode(', ') ?: 'Sin rol' }}
                        </span>

                        <a href="{{ route('logout.view') }}" class="btn btn-light w-100">
                            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                        </a>
                    </div>
                @endauth
            </div>
        </header>

        <main class="content-wrapper">
            @if(session('success'))
                <div class="alert alert-success border-0 rounded-4 mb-4">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger border-0 rounded-4 mb-4">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<div class="offcanvas offcanvas-start offcanvas-sidebar" tabindex="-1" id="mobileSidebar">
    <div class="offcanvas-header border-bottom border-light border-opacity-25">
        <h5 class="offcanvas-title fw-bold">PROMETEO</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
    </div>

    <div class="offcanvas-body d-flex flex-column">
        <nav class="nav flex-column">
            @auth
                @role('admin')
                <a href="{{ route('admin.dashboard') }}" class="nav-link text-white">
                    <i class="bi bi-speedometer2"></i> Panel admin
                </a>

                @can('usuarios.ver')
                    <a href="{{ route('admin.usuarios.index') }}" class="nav-link text-white">
                        <i class="bi bi-people"></i> Usuarios
                    </a>
                @endcan

                @can('personas.ver')
                    <a href="{{ route('admin.personas.index') }}" class="nav-link text-white">
                        <i class="bi bi-person-vcard"></i> Personas
                    </a>
                @endcan

                @can('roles.ver')
                    <a href="{{ route('admin.roles.index') }}" class="nav-link text-white">
                        <i class="bi bi-shield-check"></i> Roles
                    </a>
                @endcan

                @can('permisos.ver')
                    <a href="{{ route('admin.permisos.index') }}" class="nav-link text-white">
                        <i class="bi bi-key"></i> Permisos
                    </a>
                @endcan
                @elserole('estudiante')
                <a href="{{ route('estudiante.dashboard') }}" class="nav-link text-white">
                    <i class="bi bi-house-door"></i> Inicio
                </a>

                @can('evaluaciones.realizar')
                    <a href="{{ route('evaluaciones.index') }}" class="nav-link text-white">
                        <i class="bi bi-clipboard-check"></i> Evaluaciones
                    </a>
                @endcan

                @can('diario_ia.ver.propio')
                    <a href="{{ route('diario.index') }}" class="nav-link text-white">
                        <i class="bi bi-journal-text"></i> Diario IA
                    </a>
                @endcan

                @can('consentimiento.ver')
                    <a href="{{ route('consentimiento.create') }}" class="nav-link text-white">
                        <i class="bi bi-shield-check"></i> Consentimiento
                    </a>
                @endcan
                @elserole('tutor')
                <a href="{{ route('tutor.dashboard') }}" class="nav-link text-white">
                    <i class="bi bi-speedometer2"></i> Panel tutor
                </a>

                @can('grupos.ver.asignados')
                    <a href="{{ route('grupos.index') }}" class="nav-link text-white">
                        <i class="bi bi-people"></i> Mis grupos
                    </a>
                @endcan
                @elserole('psicologo')
                <a href="{{ route('psicologo.dashboard') }}" class="nav-link text-white">
                    <i class="bi bi-activity"></i> Panel clínico
                </a>

                @can('alertas.ver.clinicas')
                    <a href="{{ route('alertas.index') }}" class="nav-link text-white">
                        <i class="bi bi-exclamation-triangle"></i> Alertas
                    </a>
                @endcan

                @can('diagnosticos.ver')
                    <a href="{{ route('diagnosticos.index') }}" class="nav-link text-white">
                        <i class="bi bi-file-earmark-medical"></i> Diagnósticos
                    </a>
                @endcan

                @can('resultados_ia.ver')
                    <a href="{{ route('analisis.index') }}" class="nav-link text-white">
                        <i class="bi bi-robot"></i> Resultados IA
                    </a>
                @endcan
                @endrole

                @can('perfil.ver')
                    <a href="{{ route('perfil.index') }}" class="nav-link text-white">
                        <i class="bi bi-person-circle"></i> Perfil
                    </a>
                @endcan
            @endauth

            <a href="{{ route('aviso.privacidad') }}" class="nav-link text-white">
                <i class="bi bi-file-earmark-lock"></i> Aviso de privacidad
            </a>
        </nav>

        <div class="mt-auto">
            <a href="{{ route('logout.view') }}" class="btn btn-light w-100">
                <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
            </a>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@include('sweetalert::alert')
@stack('scripts')
</body>
</html>
