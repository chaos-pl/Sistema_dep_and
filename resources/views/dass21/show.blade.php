@extends('layouts.app')

@section('title', 'Resultados DASS-21 - PROMETEO')
@section('page-title', 'Resultados del Tamizaje')
@section('page-subtitle', 'Tus resultados detallados del DASS-21')

@php
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    $granimPalettes = match($userAccentColor) {
        'blue' => "[ { color: '#1e3a8a', pos: 0 }, { color: '#2563eb', pos: .5 }, { color: '#93c5fd', pos: 1 } ], [ { color: '#2563eb', pos: 0 }, { color: '#0284c7', pos: .5 }, { color: '#38bdf8', pos: 1 } ]",
        'green' => "[ { color: '#064e3b', pos: 0 }, { color: '#059669', pos: .5 }, { color: '#6ee7b7', pos: 1 } ], [ { color: '#059669', pos: 0 }, { color: '#0d9488', pos: .5 }, { color: '#2dd4bf', pos: 1 } ]",
        'pink' => "[ { color: '#831843', pos: 0 }, { color: '#db2777', pos: .5 }, { color: '#f9a8d4', pos: 1 } ], [ { color: '#db2777', pos: 0 }, { color: '#e11d48', pos: .5 }, { color: '#f43f5e', pos: 1 } ]",
        default => "[ { color: '#4c1d95', pos: 0 }, { color: '#7c3aed', pos: .5 }, { color: '#a78bfa', pos: 1 } ], [ { color: '#7c3aed', pos: 0 }, { color: '#c026d3', pos: .5 }, { color: '#db2777', pos: 1 } ]"
    };

    // Helper para mapear niveles de riesgo a las clases CSS de tu proyecto
    $getRiskClass = function($level) {
        return match($level) {
            'Normal' => 'risk-nulo',
            'Leve' => 'risk-leve',
            'Moderado' => 'risk-moderado',
            'Severo' => 'risk-severo',
            'Extremadamente severo' => 'bg-danger text-white border-0',
            default => 'risk-nulo',
        };
    };
@endphp

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
        .eval-hero { position: relative; overflow: hidden; background-color: var(--app-primary); }
        .eval-hero::after {
            content: '\F52A'; font-family: "bootstrap-icons"; position: absolute;
            top: -10%; right: -4%; font-size: 14rem; color: #ffffff;
            opacity: 0.08; transform: rotate(-12deg); pointer-events: none; z-index: 2;
        }
        #granim-canvas-eval-show { position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 0; border-radius: inherit; }
        .banner-content { position: relative; z-index: 3; }

        .glass-badge {
            background-color: rgba(255, 255, 255, 0.2) !important; color: #ffffff !important;
            backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.3);
        }
        .glass-btn {
            background-color: rgba(255, 255, 255, 0.95) !important; color: var(--app-primary-dark) !important;
            backdrop-filter: blur(10px); border: 1px solid rgba(255, 255, 255, 0.5); transition: all 0.3s ease;
        }
        .glass-btn:hover { background-color: #ffffff !important; transform: translateY(-3px); box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important; }

        /* Clases de Riesgo de PROMETEO */
        .risk-chip { font-size: .85rem; font-weight: 700; padding: .5rem 1rem; border-radius: 999px; display: inline-block; }
        .risk-nulo { background: rgba(34, 197, 94, 0.12); color: #15803d; border: 1px solid rgba(34,197,94,0.3); }
        .risk-leve { background: rgba(234, 179, 8, 0.15); color: #a16207; border: 1px solid rgba(234,179,8,0.3); }
        .risk-moderado { background: rgba(249, 115, 22, 0.14); color: #c2410c; border: 1px solid rgba(249,115,22,0.3); }
        .risk-severo { background: rgba(239, 68, 68, 0.14); color: #b91c1c; border: 1px solid rgba(239,68,68,0.3); }

        body.theme-dark .risk-nulo, body.theme-system .risk-nulo { color: #4ade80; }
        body.theme-dark .risk-leve, body.theme-system .risk-leve { color: #fde047; }
        body.theme-dark .risk-moderado, body.theme-system .risk-moderado { color: #fb923c; }
        body.theme-dark .risk-severo, body.theme-system .risk-severo { color: #f87171; }
        body.theme-dark .text-body-secondary, body.theme-system .text-body-secondary { color: #94a3b8 !important; }
        body.theme-dark .text-body, body.theme-system .text-body { color: #f8fafc !important; }

        .score-display { font-size: 4rem; font-weight: 900; line-height: 1; margin: 1rem 0; color: var(--app-text); }
        .dim-icon { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-12 anime-item">
            <div class="app-card eval-hero p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">
                <canvas id="granim-canvas-eval-show"></canvas>
                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge glass-badge rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> Completado el {{ $evaluation->completed_at->format('d/m/Y') }}
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.15rem; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            Resultados del DASS-21
                        </h2>
                        <p class="mb-0 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">
                            Aquí tienes el desglose de tus resultados por dimensión emocional.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}" class="btn glass-btn rounded-pill fw-bold px-4 py-2 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i> Volver a Evaluaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alerta Crítica (Asistencia Inmediata) --}}
        @if($evaluation->hasCriticalSeverity())
            <div class="col-12 anime-item">
                <div class="alert alert-danger border-0 border-start border-5 border-danger shadow-sm rounded-4 p-4 d-flex align-items-start gap-3 bg-danger bg-opacity-10">
                    <i class="bi bi-exclamation-triangle-fill fs-2 text-danger"></i>
                    <div>
                        <h5 class="fw-bold text-danger mb-1">Sugerencia de Asistencia Inmediata</h5>
                        <p class="mb-3 text-danger text-opacity-75">Tus resultados indican niveles elevados. Te recomendamos encarecidamente contactar al departamento de orientación psicopedagógica.</p>
                        <div class="d-flex flex-wrap gap-2">
                            <a href="tel:8009112000" class="btn btn-danger btn-sm rounded-pill px-3 fw-bold shadow-sm">
                                <i class="bi bi-telephone-fill me-1"></i> Línea de la Vida (800-911-2000)
                            </a>
                            <a href="tel:5552598121" class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold bg-white bg-opacity-50">
                                <i class="bi bi-telephone me-1"></i> SAPTEL (55-5259-8121)
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Tarjetas de Resultados por Dimensión --}}
        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 p-md-5 border border-secondary border-opacity-10 shadow-sm rounded-4 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                <div class="dim-icon bg-primary bg-opacity-10 text-primary mb-3 shadow-sm"><i class="bi bi-cloud-rain-fill"></i></div>
                <h4 class="fw-black text-body-secondary text-uppercase" style="letter-spacing: 1px;">Depresión</h4>
                <div class="score-display">{{ $evaluation->depression_score }}</div>
                <span class="risk-chip {{ $getRiskClass($evaluation->depression_level) }} shadow-sm">
                    {{ $evaluation->depression_level }}
                </span>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 p-md-5 border border-secondary border-opacity-10 shadow-sm rounded-4 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                <div class="dim-icon bg-warning bg-opacity-10 text-warning mb-3 shadow-sm"><i class="bi bi-lightning-fill"></i></div>
                <h4 class="fw-black text-body-secondary text-uppercase" style="letter-spacing: 1px;">Ansiedad</h4>
                <div class="score-display">{{ $evaluation->anxiety_score }}</div>
                <span class="risk-chip {{ $getRiskClass($evaluation->anxiety_level) }} shadow-sm">
                    {{ $evaluation->anxiety_level }}
                </span>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 p-md-5 border border-secondary border-opacity-10 shadow-sm rounded-4 text-center h-100 d-flex flex-column justify-content-center align-items-center">
                <div class="dim-icon bg-danger bg-opacity-10 text-danger mb-3 shadow-sm"><i class="bi bi-activity"></i></div>
                <h4 class="fw-black text-body-secondary text-uppercase" style="letter-spacing: 1px;">Estrés</h4>
                <div class="score-display">{{ $evaluation->stress_score }}</div>
                <span class="risk-chip {{ $getRiskClass($evaluation->stress_level) }} shadow-sm">
                    {{ $evaluation->stress_level }}
                </span>
            </div>
        </div>

        {{-- Disclaimer Legal/Médico --}}
        <div class="col-12 anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 d-flex align-items-start gap-3">
                <i class="bi bi-info-circle-fill text-body-secondary fs-4"></i>
                <p class="text-body-secondary small mb-0 mt-1 text-justify">
                    <strong>Nota importante:</strong> Este instrumento (DASS-21) es una herramienta de tamizaje inicial diseñada para medir estados emocionales durante la última semana.
                    <span class="text-decoration-underline">No constituye un diagnóstico clínico ni psiquiátrico definitivo</span>. Si experimentas malestar significativo, te recomendamos buscar la valoración de un profesional de la salud.
                </p>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/granim.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof anime !== 'undefined') {
                anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 1000 });
            }

            if (document.getElementById('granim-canvas-eval-show') && typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-eval-show',
                    direction: 'left-right',
                    isPausedWhenNotInView: true,
                    states : { "default-state": { gradients: [ {!! $granimPalettes !!} ], transitionSpeed: 7000 } }
                });
            }
        });
    </script>
@endpush
