@extends('layouts.app')

@section('title', 'Control Escolar - PROMETEO')
@section('page-title', 'Control Escolar')
@section('page-subtitle', 'Panel de gestión académica e institucional')

@push('styles')
    <style>
        .hover-elevate { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease !important; border: 1px solid transparent; }
        .hover-elevate:hover { transform: translateY(-6px); box-shadow: 0 15px 35px rgba(0,0,0,0.08) !important; border-color: var(--app-primary-soft) !important; }
        .hover-elevate:hover .metric-icon i { transform: scale(1.25); }
        .metric-icon i { transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
        .bg-welcome-ce { position: relative; overflow: hidden; background-color: var(--app-primary); }
        .bg-welcome-ce::after { content: '\F59C'; font-family: "bootstrap-icons"; position: absolute; top: -10%; right: -5%; font-size: 15rem; color: #ffffff; opacity: 0.08; transform: rotate(-15deg); pointer-events: none; z-index: 2; }
        #granim-canvas-ce { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; border-radius: inherit; }
        .banner-content { position: relative; z-index: 3; }
        .anime-item { opacity: 0; transform: translateY(20px); }
        .glass-badge { background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important; backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3); }
        .cursor-pointer { cursor: pointer; }
        .alert-card { border-radius: 1rem; border: 0; transition: all .3s ease; }
        .alert-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,.1)!important; }
        .grupo-bar { height: 8px; border-radius: 4px; transition: width .8s ease; }
        .timeline-dot { width: 10px; height: 10px; border-radius: 50%; min-width: 10px; }
        .timeline-line { width: 2px; background: var(--bs-border-color); }
    </style>
@endpush

@php
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';
    $granimPalettes = match($userAccentColor) {
        'blue' => "[ { color: '#1e3a8a', pos: 0 }, { color: '#2563eb', pos: .5 }, { color: '#93c5fd', pos: 1 } ], [ { color: '#2563eb', pos: 0 }, { color: '#0284c7', pos: .5 }, { color: '#38bdf8', pos: 1 } ]",
        'green' => "[ { color: '#064e3b', pos: 0 }, { color: '#059669', pos: .5 }, { color: '#6ee7b7', pos: 1 } ], [ { color: '#059669', pos: 0 }, { color: '#0d9488', pos: .5 }, { color: '#2dd4bf', pos: 1 } ]",
        'pink' => "[ { color: '#831843', pos: 0 }, { color: '#db2777', pos: .5 }, { color: '#f9a8d4', pos: 1 } ], [ { color: '#db2777', pos: 0 }, { color: '#e11d48', pos: .5 }, { color: '#f43f5e', pos: 1 } ]",
        default => "[ { color: '#4c1d95', pos: 0 }, { color: '#7c3aed', pos: .5 }, { color: '#a78bfa', pos: 1 } ], [ { color: '#7c3aed', pos: 0 }, { color: '#c026d3', pos: .5 }, { color: '#db2777', pos: 1 } ]"
    };
@endphp

@section('content')
    <div class="row g-4">
        {{-- Banner --}}
        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-ce p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">
                <canvas id="granim-canvas-ce"></canvas>
                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge glass-badge rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-building me-1"></i> Control Escolar
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            Gestión Académica
                        </h2>
                        <p class="mb-0 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                            Administra la estructura escolar: carreras, ciclos, grupos, estudiantes y tutores.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- ============================= --}}
        {{-- CENTRO DE ALERTAS --}}
        {{-- ============================= --}}
        @if($totalPendientes > 0 || $sinExpediente > 0 || $estudiantesSinGrupo > 0 || $tutoresSinGrupo > 0)
        <div class="col-12 anime-item">
            <div class="app-card p-4 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px"><i class="bi bi-bell-fill fs-5"></i></div>
                    <div>
                        <h5 class="fw-black mb-0 text-body">Centro de Alertas</h5>
                        <p class="text-body-secondary mb-0 small">Situaciones que requieren tu atención.</p>
                    </div>
                </div>
                <div class="row g-3">
                    @if($sinExpediente > 0)
                    <div class="col-md-6 col-xl-4">
                        <a href="{{ route('control_escolar.pendientes.index') }}" class="text-decoration-none">
                            <div class="alert-card bg-danger bg-opacity-10 p-4 shadow-sm d-flex align-items-start gap-3">
                                <div class="bg-danger bg-opacity-25 text-danger rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:45px;height:45px"><i class="bi bi-person-exclamation fs-5"></i></div>
                                <div>
                                    <h6 class="fw-bold text-body mb-1">{{ $sinExpediente }} sin expediente</h6>
                                    <p class="text-body-secondary mb-0 small">Usuarios con rol estudiante que no tienen matrícula ni datos académicos.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if($estudiantesSinGrupo > 0)
                    <div class="col-md-6 col-xl-4">
                        <a href="{{ route('control_escolar.pendientes.index') }}" class="text-decoration-none">
                            <div class="alert-card bg-warning bg-opacity-10 p-4 shadow-sm d-flex align-items-start gap-3">
                                <div class="bg-warning bg-opacity-25 text-warning rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:45px;height:45px"><i class="bi bi-collection fs-5"></i></div>
                                <div>
                                    <h6 class="fw-bold text-body mb-1">{{ $estudiantesSinGrupo }} sin grupo</h6>
                                    <p class="text-body-secondary mb-0 small">Estudiantes con expediente completo pero sin grupo asignado.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif

                    @if($tutoresSinGrupo > 0)
                    <div class="col-md-6 col-xl-4">
                        <a href="{{ route('control_escolar.tutores.index') }}" class="text-decoration-none">
                            <div class="alert-card bg-info bg-opacity-10 p-4 shadow-sm d-flex align-items-start gap-3">
                                <div class="bg-info bg-opacity-25 text-info rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width:45px;height:45px"><i class="bi bi-person-video3 fs-5"></i></div>
                                <div>
                                    <h6 class="fw-bold text-body mb-1">{{ $tutoresSinGrupo }} tutores sin grupo</h6>
                                    <p class="text-body-secondary mb-0 small">Tutores registrados que no tienen un grupo asignado.</p>
                                </div>
                            </div>
                        </a>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endif

        {{-- ============================= --}}
        {{-- MÉTRICAS --}}
        {{-- ============================= --}}
        @php
            $metrics = [
                ['label' => 'Estudiantes', 'value' => $totalEstudiantes, 'icon' => 'bi-mortarboard-fill', 'color' => 'primary', 'sub' => $estudiantesActivos . ' activos'],
                ['label' => 'Pendientes', 'value' => $totalPendientes, 'icon' => 'bi-person-exclamation', 'color' => $totalPendientes > 0 ? 'danger' : 'success', 'sub' => $sinExpediente . ' sin expediente · ' . $estudiantesSinGrupo . ' sin grupo'],
                ['label' => 'Grupos', 'value' => $totalGrupos, 'icon' => 'bi-collection-fill', 'color' => 'info', 'sub' => $gruposActivos . ' activos'],
                ['label' => 'Carreras', 'value' => $totalCarreras, 'icon' => 'bi-book-fill', 'color' => 'success', 'sub' => $carrerasActivas . ' activas'],
                ['label' => 'Tutores', 'value' => $totalTutores, 'icon' => 'bi-person-video3', 'color' => 'warning', 'sub' => $tutoresSinGrupo . ' sin grupo'],
                ['label' => 'Ciclos Activos', 'value' => $ciclosActivos, 'icon' => 'bi-calendar-event-fill', 'color' => 'primary', 'sub' => 'Periodos vigentes'],
            ];
        @endphp

        @foreach($metrics as $m)
            <div class="col-md-6 col-xl-4 anime-item">
                <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">{{ $m['label'] }}</h6>
                        <div class="metric-icon bg-{{ $m['color'] }} bg-opacity-10 text-{{ $m['color'] }} rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi {{ $m['icon'] }} fs-5"></i>
                        </div>
                    </div>
                    <h2 class="fw-black mb-1 text-body">{{ $m['value'] }}</h2>
                    <p class="text-body-secondary mb-0 small">{{ $m['sub'] }}</p>
                </div>
            </div>
        @endforeach

        {{-- ============================= --}}
        {{-- DISTRIBUCIÓN POR GRUPOS --}}
        {{-- ============================= --}}
        @if($gruposConEstudiantes->count() > 0)
        <div class="col-lg-7 anime-item">
            <div class="app-card p-4 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px"><i class="bi bi-bar-chart-fill fs-5"></i></div>
                    <div>
                        <h5 class="fw-black mb-0 text-body">Distribución por Grupo</h5>
                        <p class="text-body-secondary mb-0 small">Estudiantes por grupo activo.</p>
                    </div>
                </div>
                @php $maxEst = $gruposConEstudiantes->max('estudiantes_count') ?: 1; @endphp
                @foreach($gruposConEstudiantes as $g)
                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold text-body small">{{ $g->nombre }}</span>
                            <span class="text-body-secondary small">{{ $g->carrera?->nombre }}</span>
                        </div>
                        <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 fw-bold small">{{ $g->estudiantes_count }}</span>
                    </div>
                    <div class="bg-body-tertiary rounded-pill overflow-hidden" style="height:8px">
                        <div class="grupo-bar bg-primary bg-opacity-75" style="width: {{ ($g->estudiantes_count / $maxEst) * 100 }}%; height:100%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ============================= --}}
        {{-- ACTIVIDAD RECIENTE --}}
        {{-- ============================= --}}
        <div class="col-lg-5 anime-item">
            <div class="app-card p-4 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 mb-4 pb-3 border-bottom border-secondary border-opacity-10">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px"><i class="bi bi-clock-history fs-5"></i></div>
                    <div>
                        <h5 class="fw-black mb-0 text-body">Actividad Reciente</h5>
                        <p class="text-body-secondary mb-0 small">Últimos movimientos de grupo.</p>
                    </div>
                </div>
                @forelse($ultimosMovimientos as $mov)
                @php
                    $badgeColor = match($mov->accion) {
                        'asignado' => 'success',
                        'cambiado' => 'warning',
                        'quitado' => 'danger',
                        default => 'secondary'
                    };
                @endphp
                <div class="d-flex gap-3 mb-3 pb-3 {{ !$loop->last ? 'border-bottom border-secondary border-opacity-10' : '' }}">
                    <div class="timeline-dot bg-{{ $badgeColor }} mt-1 flex-shrink-0"></div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start">
                            <h6 class="fw-bold text-body mb-0 small">{{ $mov->estudiante?->persona?->nombre }} {{ $mov->estudiante?->persona?->apellido_paterno }}</h6>
                            <span class="badge bg-{{ $badgeColor }} bg-opacity-10 text-{{ $badgeColor }} rounded-pill px-2 py-1 small">{{ ucfirst($mov->accion ?? 'movimiento') }}</span>
                        </div>
                        <p class="text-body-secondary mb-0 small mt-1">
                            {{ $mov->grupoOrigen?->nombre ?? '—' }} → {{ $mov->grupoDestino?->nombre ?? 'Sin grupo' }}
                        </p>
                        <small class="text-body-secondary opacity-75">{{ $mov->created_at->diffForHumans() }} · {{ $mov->realizadoPor?->name }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-body-secondary opacity-25 mb-3 d-block"></i>
                    <p class="text-body-secondary mb-0 small">No hay movimientos registrados aún.</p>
                </div>
                @endforelse
            </div>
        </div>

        {{-- ============================= --}}
        {{-- ACCESOS RÁPIDOS --}}
        {{-- ============================= --}}
        <div class="col-12 anime-item">
            <div class="app-card p-4 border-0 shadow-sm rounded-4">
                <div class="mb-4 d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-grid-1x2-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0 text-body">Accesos Rápidos</h4>
                        <p class="text-body-secondary mb-0">Navega a los módulos de control escolar.</p>
                    </div>
                </div>
                <div class="row g-3">
                    @php
                        $shortcuts = [
                        [
                            'route' => 'control_escolar.estudiantes.index',
                            'icon' => 'bi-mortarboard-fill',
                            'color' => 'primary',
                            'title' => 'Estudiantes',
                            'desc' => 'Gestiona altas, edición y cambios de grupo.'
                        ],
                        [
                            'route' => 'control_escolar.pendientes.index',
                            'icon' => 'bi-person-exclamation',
                            'color' => 'danger',
                            'title' => 'Pendientes',
                            'desc' => $totalPendientes . ' pendientes de asignación.'
                        ],
                        [
                            'route' => 'control_escolar.tutores.index',
                            'icon' => 'bi-person-video3',
                            'color' => 'warning',
                            'title' => 'Tutores',
                            'desc' => 'Registra y actualiza tutores académicos.'
                        ],
                        [
                            'route' => 'control_escolar.grupos.index',
                            'icon' => 'bi-collection-fill',
                            'color' => 'info',
                            'title' => 'Grupos',
                            'desc' => 'Crea y organiza los grupos escolares.'
                        ],
                        [
                            'route' => 'control_escolar.carreras.index',
                            'icon' => 'bi-book-fill',
                            'color' => 'success',
                            'title' => 'Carreras',
                            'desc' => 'Administra las carreras académicas.'
                        ],
                        [
                            'route' => 'control_escolar.ciclos-escolares.index',
                            'icon' => 'bi-calendar-event-fill',
                            'color' => 'primary',
                            'title' => 'Ciclos Escolares',
                            'desc' => 'Gestiona los periodos académicos.'
                        ],
                        [
                            'route' => 'control_escolar.asignaciones.index',
                            'icon' => 'bi-arrow-left-right',
                            'color' => 'danger',
                            'title' => 'Asignaciones',
                            'desc' => 'Asigna tutores a grupos por ciclo escolar.'
                        ],
                    ];
                    @endphp

                    @foreach($shortcuts as $s)
                        <div class="col-md-6 col-xl-4">
                            <a href="{{ route($s['route']) }}" class="text-decoration-none">
                                <div class="app-card hover-elevate p-4 h-100 bg-body-tertiary rounded-4 border border-secondary border-opacity-10">
                                    <div class="d-flex align-items-center gap-3 mb-3">
                                        <div class="metric-icon rounded-circle bg-body text-{{ $s['color'] }} shadow-sm d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="bi {{ $s['icon'] }}"></i></div>
                                        <h6 class="fw-bold mb-0 text-body">{{ $s['title'] }}</h6>
                                    </div>
                                    <p class="text-body-secondary mb-0 small">{{ $s['desc'] }}</p>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof anime !== 'undefined') {
                anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(100), easing: 'easeOutExpo', duration: 900 });
            }
            if (document.getElementById('granim-canvas-ce') && typeof Granim !== 'undefined') {
                new Granim({ element: '#granim-canvas-ce', direction: 'left-right', isPausedWhenNotInView: true, states: { "default-state": { gradients: [{!! $granimPalettes !!}], transitionSpeed: 7000 } } });
            }
        });
    </script>
@endpush
