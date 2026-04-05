@extends('layouts.app')

@section('title', 'Evaluaciones - PROMETEO')
@section('page-title', 'Evaluaciones')
@section('page-subtitle', 'Instrumentos de tamizaje disponibles para ti')

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
        .anime-item { opacity: 0; transform: translateY(20px); }

        .eval-hero {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary);
        }
        .eval-hero::after {
            content: '\F26A'; font-family: "bootstrap-icons"; position: absolute;
            top: -10%; right: -4%; font-size: 14rem; color: #ffffff;
            opacity: 0.08; transform: rotate(-12deg); pointer-events: none;
            z-index: 2;
        }

        #granim-canvas-eval {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .banner-content { position: relative; z-index: 3; }

        .instrument-card { transition: all .3s cubic-bezier(0.25, 0.8, 0.25, 1); border: 1px solid transparent; }
        .instrument-card:hover {
            transform: translateY(-6px);
            border-color: var(--app-primary-soft);
            box-shadow: 0 18px 36px rgba(0, 0, 0, 0.1);
        }

        .instrument-icon {
            width: 58px; height: 58px; border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
        }

        .metric-pill {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .45rem .95rem; border-radius: 999px;
            font-size: .85rem; font-weight: 700;
        }
        .metric-soft-primary { background: var(--app-primary-soft); color: var(--app-primary-dark); }

        .risk-chip { font-size: .8rem; font-weight: 700; padding: .42rem .85rem; border-radius: 999px; }
        .risk-nulo { background: rgba(34, 197, 94, 0.12); color: #15803d; }
        .risk-leve { background: rgba(234, 179, 8, 0.15); color: #a16207; }
        .risk-moderado { background: rgba(249, 115, 22, 0.14); color: #c2410c; }
        .risk-severo { background: rgba(239, 68, 68, 0.14); color: #b91c1c; }

        body.theme-dark .risk-nulo, body.theme-system .risk-nulo { color: #4ade80; }
        body.theme-dark .risk-leve, body.theme-system .risk-leve { color: #fde047; }
        body.theme-dark .risk-moderado, body.theme-system .risk-moderado { color: #fb923c; }
        body.theme-dark .risk-severo, body.theme-system .risk-severo { color: #f87171; }

        .estado-badge { font-size: .78rem; padding: .45rem .85rem; border-radius: 999px; font-weight: 700; }
    </style>
@endpush

@section('content')
    @php
        $totalInstrumentos = $instrumentosDashboard->count();
        $totalCompletadas = $instrumentosDashboard->where('estado', 'completada')->count();
        $totalPendientes = $instrumentosDashboard->where('estado', '!=', 'completada')->count();
    @endphp

    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card eval-hero p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-eval"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm" style="color: var(--app-primary) !important;">
                            <i class="bi bi-clipboard2-pulse-fill me-1"></i> Tamizaje Emocional
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">Hola, {{ auth()->user()->name }}</h2>
                        <p class="mb-3 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            Responde tus instrumentos disponibles. Tus resultados ayudan a construir tu seguimiento emocional dentro de PROMETEO.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 rounded-pill px-3 py-2 shadow-sm" style="backdrop-filter: blur(8px);">
                                <i class="bi bi-collection-fill me-1 text-white"></i> Instrumentos: {{ $totalInstrumentos }}
                            </span>
                            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 rounded-pill px-3 py-2 shadow-sm" style="backdrop-filter: blur(8px);">
                                <i class="bi bi-check-circle-fill me-1 text-white"></i> Completadas: {{ $totalCompletadas }}
                            </span>
                            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 rounded-pill px-3 py-2 shadow-sm" style="backdrop-filter: blur(8px);">
                                <i class="bi bi-hourglass-split me-1 text-white"></i> Pendientes: {{ $totalPendientes }}
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('estudiante.dashboard') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm" style="color: var(--app-primary) !important;">
                            <i class="bi bi-arrow-left me-1"></i> Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @forelse($instrumentosDashboard as $item)
            @php
                $nivel = $item->nivel_riesgo;
                $riskClass = match($nivel) {
                    'nulo' => 'risk-nulo',
                    'leve' => 'risk-leve',
                    'moderado' => 'risk-moderado',
                    'severo' => 'risk-severo',
                    default => 'risk-nulo',
                };

                $estadoClass = match($item->estado) {
                    'completada' => 'bg-success text-white',
                    'abandonada' => 'bg-danger text-white',
                    default => 'bg-warning text-dark',
                };
            @endphp

            <div class="col-lg-6 anime-item">
                <div class="app-card instrument-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="instrument-icon bg-primary bg-opacity-10 text-primary shadow-sm">
                                <i class="bi bi-clipboard2-heart-fill fs-4"></i>
                            </div>
                            <div>
                                <h4 class="fw-black mb-1 text-body">{{ $item->instrumento->nombre }}</h4>
                                <div class="text-body-secondary fw-bold">{{ strtoupper($item->instrumento->acronimo) }}</div>
                            </div>
                        </div>
                        <span class="estado-badge {{ $estadoClass }} shadow-sm">
                            {{ ucfirst($item->estado) }}
                        </span>
                    </div>

                    <div class="mb-4">
                        @if($item->puntaje_total !== null)
                            <div class="d-flex flex-wrap gap-2">
                                <span class="metric-pill metric-soft-primary shadow-sm">
                                    <i class="bi bi-bar-chart-fill"></i> Puntaje: {{ $item->puntaje_total }}
                                </span>
                                <span class="risk-chip {{ $riskClass }} shadow-sm">
                                    <i class="bi bi-activity me-1"></i> Riesgo {{ ucfirst($item->nivel_riesgo) }}
                                </span>
                            </div>
                        @else
                            <p class="text-body-secondary mb-0 fw-medium">
                                <i class="bi bi-info-circle me-1"></i> Aún no has respondido este instrumento.
                            </p>
                        @endif
                    </div>

                    <div class="mt-auto pt-3 border-top border-secondary border-opacity-10">
                        <a href="{{ route('evaluaciones.aplicar', strtolower($item->instrumento->acronimo)) }}"
                           class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm w-100">
                            <i class="bi {{ $item->estado === 'completada' ? 'bi-arrow-clockwise' : 'bi-pencil-square' }} me-2"></i>
                            {{ $item->estado === 'completada' ? 'Responder de nuevo' : 'Iniciar evaluación' }}
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 anime-item">
                <div class="app-card bg-body-tertiary p-5 border border-secondary border-opacity-10 shadow-sm rounded-4 text-center">
                    <i class="bi bi-clipboard-x fs-1 text-body-secondary opacity-50"></i>
                    <h4 class="fw-black mt-3 text-body">No hay instrumentos disponibles</h4>
                    <p class="text-body-secondary mb-0">Primero se deben registrar instrumentos en el sistema.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/granim.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof anime !== 'undefined') {
                anime({
                    targets: '.anime-item',
                    translateY: [28, 0],
                    opacity: [0, 1],
                    delay: anime.stagger(110),
                    easing: 'easeOutExpo',
                    duration: 900
                });
            }

            if (document.getElementById('granim-canvas-eval') && typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-eval',
                    direction: 'left-right',
                    isPausedWhenNotInView: true,
                    states : {
                        "default-state": {
                            gradients: [ {!! $granimPalettes !!} ],
                            transitionSpeed: 7000
                        }
                    }
                });
            }
        });
    </script>
@endpush
