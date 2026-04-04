@extends('layouts.app')

@section('title', 'Evaluaciones - PROMETEO')
@section('page-title', 'Evaluaciones')
@section('page-subtitle', 'Instrumentos de tamizaje disponibles para ti')

@php
    $userAccentColor = auth()->user()->appearance['accent_color'] ?? 'primary';
    $bannerClasses = match($userAccentColor) {
        'red' => 'bg-danger bg-gradient text-white',
        'green' => 'bg-success bg-gradient text-white',
        'blue' => 'bg-info bg-gradient text-dark',
        'orange' => 'bg-warning bg-gradient text-dark',
        'pink' => 'bg-pink bg-gradient text-white',
        default => 'bg-primary bg-gradient text-white'
    };
@endphp

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }

        .eval-hero { position: relative; overflow: hidden; }
        .eval-hero::after {
            content: '\F26A'; font-family: "bootstrap-icons"; position: absolute;
            top: -10%; right: -4%; font-size: 14rem; color: inherit;
            opacity: 0.1; transform: rotate(-12deg); pointer-events: none;
        }

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
            <div class="app-card eval-hero p-4 p-md-5 rounded-4 border-0 shadow-lg {{ $bannerClasses }}">
                <div class="row align-items-center position-relative z-1">
                    <div class="col-lg-8">
                        <span class="badge bg-body text-body border border-secondary border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-clipboard2-pulse-fill me-1 text-primary"></i> Tamizaje Emocional
                        </span>
                        <h2 class="fw-black mb-2" style="font-size: 2.2rem;">Hola, {{ auth()->user()->name }}</h2>
                        <p class="mb-3 opacity-75 fs-5">
                            Responde tus instrumentos disponibles. Tus resultados ayudan a construir tu seguimiento emocional dentro de PROMETEO.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-body-tertiary text-body border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                <i class="bi bi-collection-fill me-1 text-primary"></i> Instrumentos: {{ $totalInstrumentos }}
                            </span>
                            <span class="badge bg-body-tertiary text-body border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                <i class="bi bi-check-circle-fill me-1 text-success"></i> Completadas: {{ $totalCompletadas }}
                            </span>
                            <span class="badge bg-body-tertiary text-body border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                <i class="bi bi-hourglass-split me-1 text-warning"></i> Pendientes: {{ $totalPendientes }}
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('estudiante.dashboard') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm">
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({
                targets: '.anime-item',
                translateY: [28, 0],
                opacity: [0, 1],
                delay: anime.stagger(110),
                easing: 'easeOutExpo',
                duration: 900
            });
        });
    </script>
@endpush
