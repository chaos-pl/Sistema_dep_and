<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Sistema de Tamizaje')</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('layouts.partials.theme')
    @stack('styles')
</head>
<body>
    <div class="app-shell">
        @auth
            <aside class="app-sidebar d-none d-lg-flex flex-column">
                @include('layouts.partials.sidebar')
            </aside>
        @endauth

        <div class="app-main">
            <header class="app-topbar py-3 px-3 px-lg-4">
                <div class="d-flex align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        @auth
                            <button class="btn btn-light d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                                <i class="bi bi-list fs-5"></i>
                            </button>
                        @endauth
                        <div>
                            <span class="soft-badge soft-primary mb-2">Bienestar universitario</span>
                            <h1 class="h4 fw-bold mb-1">@yield('page-title', 'Dashboard principal')</h1>
                            <p class="text-muted-soft mb-0">@yield('page-subtitle', 'Interfaz diseñada para ofrecer claridad, contención y seguimiento oportuno.')</p>
                        </div>
                    </div>

                    @auth
                        <div class="d-flex align-items-center gap-3">
                            <div class="text-end d-none d-md-block">
                                <div class="fw-semibold">{{ auth()->user()->name }}</div>
                                <div class="small text-muted-soft text-capitalize">Rol: {{ auth()->user()->rol }}</div>
                            </div>
                            <div class="rounded-circle bg-white border d-inline-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-color: var(--app-border) !important;">
                                <i class="bi bi-person fs-5 text-primary"></i>
                            </div>
                        </div>
                    @endauth
                </div>
            </header>

            <main class="page-content">
                @if (session('status'))
                    <div class="alert border-0 soft-success rounded-4 mb-4">{{ session('status') }}</div>
                @endif

                @yield('content')
                {{ $slot ?? '' }}
            </main>
        </div>
    </div>

    @auth
        <div class="offcanvas offcanvas-start offcanvas-soft" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <div class="offcanvas-header border-bottom border-light border-opacity-25">
                <div class="d-flex align-items-center gap-3">
                    <div class="mobile-brand-mark fs-4 text-white">
                        <i class="bi bi-heart-pulse-fill"></i>
                    </div>
                    <div>
                        <h5 class="offcanvas-title mb-0" id="mobileSidebarLabel">Tamizaje Mental</h5>
                        <small class="text-white text-opacity-75">Navegación por rol</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>
            <div class="offcanvas-body d-flex flex-column">
                @include('layouts.partials.sidebar')
            </div>
        </div>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>
