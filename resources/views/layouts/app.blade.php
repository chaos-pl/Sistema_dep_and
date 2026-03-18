<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Tamizaje')</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Bootstrap Icons --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root{
            --bs-primary: #9B72CF;
            --bs-primary-rgb: 155, 114, 207;
            --bs-secondary: #A388D4;
            --bs-secondary-rgb: 163, 136, 212;
            --bs-body-bg: #F4F4F8;
            --bs-body-color: #374151;
            --bs-border-color: #E9DFF7;
            --bs-light: #FAF5FF;
            --bs-light-rgb: 250, 245, 255;
            --app-bg: #F4F4F8;
            --app-surface: #FFFFFF;
            --app-sidebar: #9B72CF;
            --app-sidebar-dark: #8C66BC;
            --app-sidebar-text: #F9F7FD;
            --app-text: #374151;
            --app-muted: #6B7280;
            --app-border: #E9DFF7;
            --app-hover: #F3ECFB;
            --app-card: #FFFFFF;
            --app-warning-soft: #FFF4D6;
            --app-success-soft: #EAF7EE;
            --app-danger-soft: #FCEDEE;
            --app-info-soft: #EEF2FF;
        }

        body{
            background: var(--app-bg);
            color: var(--app-text);
            font-family: "Inter", "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        }

        .app-wrapper{
            min-height: 100vh;
            display: flex;
            background: var(--app-bg);
        }

        .sidebar-desktop{
            width: 270px;
            min-height: 100vh;
            background: linear-gradient(180deg, var(--app-sidebar) 0%, var(--app-sidebar-dark) 100%);
            color: var(--app-sidebar-text);
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1030;
            padding: 1.25rem 1rem;
            box-shadow: 0 10px 30px rgba(76, 29, 149, .12);
        }

        .sidebar-brand{
            font-weight: 700;
            font-size: 1.1rem;
            letter-spacing: .3px;
        }

        .sidebar-subtitle{
            font-size: .88rem;
            opacity: .9;
        }

        .sidebar-nav .nav-link{
            color: rgba(255,255,255,.92);
            border-radius: 1rem;
            padding: .85rem 1rem;
            margin-bottom: .35rem;
            display: flex;
            align-items: center;
            gap: .75rem;
            transition: .25s ease;
            font-weight: 500;
        }

        .sidebar-nav .nav-link:hover,
        .sidebar-nav .nav-link.active{
            background: rgba(255,255,255,.16);
            color: #fff;
        }

        .main-content{
            flex: 1;
            margin-left: 270px;
            min-height: 100vh;
        }

        .topbar{
            background: rgba(255,255,255,.78);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--app-border);
            padding: 1rem 1.25rem;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .content-area{
            padding: 1.5rem;
        }

        .app-card{
            background: var(--app-card);
            border: 1px solid var(--app-border);
            border-radius: 1.25rem;
            box-shadow: 0 10px 30px rgba(155,114,207,.08);
        }

        .soft-badge{
            border-radius: 999px;
            padding: .45rem .8rem;
            font-size: .8rem;
            font-weight: 600;
        }

        .soft-primary{
            background: #F2EAFE;
            color: #7C4DB6;
        }

        .soft-warning{
            background: var(--app-warning-soft);
            color: #8A6300;
        }

        .soft-danger{
            background: var(--app-danger-soft);
            color: #9E2F3E;
        }

        .soft-success{
            background: var(--app-success-soft);
            color: #217A43;
        }

        .soft-info{
            background: var(--app-info-soft);
            color: #4B59B7;
        }

        .btn{
            border-radius: 1rem;
            padding: .72rem 1rem;
            font-weight: 600;
        }

        .btn-primary{
            background-color: var(--bs-primary);
            border-color: var(--bs-primary);
        }

        .btn-primary:hover{
            background-color: #875dc0;
            border-color: #875dc0;
        }

        .form-control,
        .form-select{
            border-radius: 1rem;
            padding: .85rem 1rem;
            border: 1px solid var(--app-border);
            box-shadow: none !important;
        }

        .form-control:focus,
        .form-select:focus{
            border-color: #BEA1E7;
            box-shadow: 0 0 0 .2rem rgba(155, 114, 207, .15) !important;
        }

        .table{
            --bs-table-bg: transparent;
        }

        .table thead th{
            color: var(--app-muted);
            font-weight: 700;
            border-bottom: 1px solid var(--app-border);
        }

        .table tbody td{
            vertical-align: middle;
            border-color: #F1E9FB;
        }

        .welcome-panel{
            background: linear-gradient(135deg, #F7F0FF 0%, #FFFFFF 100%);
        }

        .metric-icon{
            width: 52px;
            height: 52px;
            border-radius: 1rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            background: #F2EAFE;
            color: #7C4DB6;
        }

        .journal-box{
            min-height: 180px;
            resize: none;
        }

        .offcanvas-custom{
            background: linear-gradient(180deg, var(--app-sidebar) 0%, var(--app-sidebar-dark) 100%);
            color: #fff;
        }

        .privacy-link{
            color: #6D4BAE;
            text-decoration: none;
            font-weight: 600;
        }

        .privacy-link:hover{
            text-decoration: underline;
        }

        @media (max-width: 991.98px){
            .sidebar-desktop{
                display: none;
            }

            .main-content{
                margin-left: 0;
            }

            .content-area{
                padding: 1rem;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
<div class="app-wrapper">
    {{-- Sidebar escritorio --}}
    <aside class="sidebar-desktop d-none d-lg-flex flex-column">
        <div class="mb-4">
            <div class="d-flex align-items-center gap-3 mb-2">
                <div class="bg-white text-primary rounded-4 d-flex align-items-center justify-content-center" style="width:48px;height:48px;">
                    <i class="bi bi-heart-pulse-fill fs-4"></i>
                </div>
                <div>
                    <div class="sidebar-brand">Tamizaje Mental</div>
                    <div class="sidebar-subtitle">Entorno seguro y confidencial</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav nav flex-column">
            @auth
                @if(auth()->user()->rol === 'estudiante')
                    <a href="{{ route('estudiante.dashboard') }}" class="nav-link {{ request()->routeIs('estudiante.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-house-door"></i> Inicio
                    </a>
                    <a href="{{ route('evaluaciones.index') }}" class="nav-link">
                        <i class="bi bi-clipboard-check"></i> Evaluaciones
                    </a>
                    <a href="{{ route('diario.index') }}" class="nav-link">
                        <i class="bi bi-journal-text"></i> Diario emocional
                    </a>
                    <a href="{{ route('perfil.index') }}" class="nav-link">
                        <i class="bi bi-person-circle"></i> Mi perfil
                    </a>
                @endif

                @if(auth()->user()->rol === 'tutor')
                    <a href="{{ route('tutor.dashboard') }}" class="nav-link {{ request()->routeIs('tutor.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i> Panel general
                    </a>
                    <a href="{{ route('grupos.index') }}" class="nav-link">
                        <i class="bi bi-people"></i> Grupos
                    </a>
                    <a href="{{ route('reportes.tutor') }}" class="nav-link">
                        <i class="bi bi-bar-chart-line"></i> Métricas
                    </a>
                @endif

                @if(auth()->user()->rol === 'psicologo')
                    <a href="{{ route('psicologo.dashboard') }}" class="nav-link {{ request()->routeIs('psicologo.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-activity"></i> Panel clínico
                    </a>
                    <a href="{{ route('alertas.index') }}" class="nav-link">
                        <i class="bi bi-exclamation-triangle"></i> Alertas
                    </a>
                    <a href="{{ route('diagnosticos.index') }}" class="nav-link">
                        <i class="bi bi-file-medical"></i> Diagnósticos
                    </a>
                    <a href="{{ route('analisis.index') }}" class="nav-link">
                        <i class="bi bi-robot"></i> Análisis NLP
                    </a>
                @endif
            @endauth
        </nav>

        <div class="mt-auto pt-4">
            <div class="small opacity-75 mb-2">Privacidad y consentimiento</div>
            <a href="{{ route('aviso.privacidad') }}" class="nav-link">
                <i class="bi bi-shield-lock"></i> Aviso de privacidad
            </a>
            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button class="btn btn-light w-100 rounded-4 fw-semibold">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    <div class="main-content">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                        <i class="bi bi-list fs-5"></i>
                    </button>
                    <div>
                        <h1 class="h5 mb-0 fw-bold">@yield('page-title', 'Panel principal')</h1>
                        <small class="text-muted">@yield('page-subtitle', 'Bienvenido al sistema')</small>
                    </div>
                </div>

                @auth
                    <div class="d-flex align-items-center gap-3">
                        <span class="soft-badge soft-primary text-capitalize">
                            <i class="bi bi-person-badge me-1"></i>{{ auth()->user()->rol }}
                        </span>
                        <div class="text-end d-none d-sm-block">
                            <div class="fw-semibold">{{ auth()->user()->name }}</div>
                            <small class="text-muted">{{ auth()->user()->email }}</small>
                        </div>
                    </div>
                @endauth
            </div>
        </header>

        <main class="content-area">
            @if(session('success'))
                <div class="alert border-0 rounded-4 app-card mb-4" style="background:#ECFDF3; color:#166534;">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert border-0 rounded-4 app-card mb-4" style="background:#FEF2F2; color:#991B1B;">
                    {{ session('error') }}
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Sidebar móvil --}}
<div class="offcanvas offcanvas-start offcanvas-custom" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
    <div class="offcanvas-header border-bottom border-light border-opacity-25">
        <h5 class="offcanvas-title fw-bold" id="mobileSidebarLabel">Tamizaje Mental</h5>
        <button type="button" class="btn-close btn-close-white text-reset" data-bs-dismiss="offcanvas" aria-label="Cerrar"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <nav class="nav flex-column sidebar-nav">
            @auth
                @if(auth()->user()->rol === 'estudiante')
                    <a href="{{ route('estudiante.dashboard') }}" class="nav-link"><i class="bi bi-house-door"></i> Inicio</a>
                    <a href="{{ route('evaluaciones.index') }}" class="nav-link"><i class="bi bi-clipboard-check"></i> Evaluaciones</a>
                    <a href="{{ route('diario.index') }}" class="nav-link"><i class="bi bi-journal-text"></i> Diario emocional</a>
                    <a href="{{ route('perfil.index') }}" class="nav-link"><i class="bi bi-person-circle"></i> Mi perfil</a>
                @endif

                @if(auth()->user()->rol === 'tutor')
                    <a href="{{ route('tutor.dashboard') }}" class="nav-link"><i class="bi bi-speedometer2"></i> Panel general</a>
                    <a href="{{ route('grupos.index') }}" class="nav-link"><i class="bi bi-people"></i> Grupos</a>
                    <a href="{{ route('reportes.tutor') }}" class="nav-link"><i class="bi bi-bar-chart-line"></i> Métricas</a>
                @endif

                @if(auth()->user()->rol === 'psicologo')
                    <a href="{{ route('psicologo.dashboard') }}" class="nav-link"><i class="bi bi-activity"></i> Panel clínico</a>
                    <a href="{{ route('alertas.index') }}" class="nav-link"><i class="bi bi-exclamation-triangle"></i> Alertas</a>
                    <a href="{{ route('diagnosticos.index') }}" class="nav-link"><i class="bi bi-file-medical"></i> Diagnósticos</a>
                    <a href="{{ route('analisis.index') }}" class="nav-link"><i class="bi bi-robot"></i> Análisis NLP</a>
                @endif
            @endauth
        </nav>

        <div class="mt-auto">
            <a href="{{ route('aviso.privacidad') }}" class="nav-link">
                <i class="bi bi-shield-lock"></i> Aviso de privacidad
            </a>

            <form action="{{ route('logout') }}" method="POST" class="mt-3">
                @csrf
                <button class="btn btn-light w-100 rounded-4 fw-semibold">
                    <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
