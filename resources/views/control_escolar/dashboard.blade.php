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

        {{-- Métricas --}}
        @php
            $metrics = [
                ['label' => 'Estudiantes', 'value' => $totalEstudiantes, 'icon' => 'bi-mortarboard-fill', 'color' => 'primary', 'sub' => 'Total registrados'],
                ['label' => 'Sin Grupo', 'value' => $estudiantesSinGrupo, 'icon' => 'bi-person-exclamation', 'color' => 'danger', 'sub' => 'Requieren asignación'],
                ['label' => 'Grupos', 'value' => $totalGrupos, 'icon' => 'bi-collection-fill', 'color' => 'info', 'sub' => 'Grupos registrados'],
                ['label' => 'Carreras', 'value' => $totalCarreras, 'icon' => 'bi-book-fill', 'color' => 'success', 'sub' => 'Carreras activas'],
                ['label' => 'Tutores', 'value' => $totalTutores, 'icon' => 'bi-person-video3', 'color' => 'warning', 'sub' => 'Personal de tutoría'],
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

        {{-- Accesos rápidos --}}
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
                            'route' => 'control_escolar.grupos.index',
                            'icon' => 'bi-collection-fill',
                            'color' => 'info',
                            'title' => 'Grupos',
                            'desc' => 'Crea y organiza los grupos escolares.'
                        ],
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
                            'title' => 'Pendientes de Asignación',
                            'desc' => 'Estudiantes registrados que todavía no tienen grupo.'
                        ],
                        [
                            'route' => 'control_escolar.tutores.index',
                            'icon' => 'bi-person-video3',
                            'color' => 'warning',
                            'title' => 'Tutores',
                            'desc' => 'Registra y actualiza tutores académicos.'
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
