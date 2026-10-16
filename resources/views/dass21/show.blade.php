
@extends('layouts.app')

@section('title', 'Resultados DASS-21 - PROMETEO')
@section('page-title', 'Resultados del Tamizaje')
@section('page-subtitle', 'Tus resultados detallados del DASS-21')

@php
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    $granimPalettes = match($userAccentColor) {
        'blue' => "
            [ { color: '#1e3a8a', pos: 0 }, { color: '#2563eb', pos: .5 }, { color: '#93c5fd', pos: 1 } ],
            [ { color: '#2563eb', pos: 0 }, { color: '#0284c7', pos: .5 }, { color: '#38bdf8', pos: 1 } ]
        ",
        'green' => "
            [ { color: '#064e3b', pos: 0 }, { color: '#059669', pos: .5 }, { color: '#6ee7b7', pos: 1 } ],
            [ { color: '#059669', pos: 0 }, { color: '#0d9488', pos: .5 }, { color: '#2dd4bf', pos: 1 } ]
        ",
        'pink' => "
            [ { color: '#831843', pos: 0 }, { color: '#db2777', pos: .5 }, { color: '#f9a8d4', pos: 1 } ],
            [ { color: '#db2777', pos: 0 }, { color: '#e11d48', pos: .5 }, { color: '#f43f5e', pos: 1 } ]
        ",
        default => "
            [ { color: '#4c1d95', pos: 0 }, { color: '#7c3aed', pos: .5 }, { color: '#a78bfa', pos: 1 } ],
            [ { color: '#7c3aed', pos: 0 }, { color: '#c026d3', pos: .5 }, { color: '#db2777', pos: 1 } ]
        "
    };

    // Clasificación visual. No modifica los puntajes.
    $getRiskClass = function (?string $nivel): string {
        return match ($nivel) {
            'Normal' => 'risk-nulo',
            'Leve' => 'risk-leve',
            'Moderado' => 'risk-moderado',
            'Severo' => 'risk-severo',
            'Extremadamente severo' => 'risk-extremo',
            default => 'risk-desconocido',
        };
    };

    $dimensiones = [
        [
            'nombre' => 'Depresión',
            'puntaje' => $evaluation->depression_score,
            'nivel' => $evaluation->depression_level,
            'icono' => 'bi-cloud-rain-fill',
            'color' => 'primary',
            'descripcion' => 'Explora experiencias relacionadas con el estado de ánimo durante la última semana.',
        ],
        [
            'nombre' => 'Ansiedad',
            'puntaje' => $evaluation->anxiety_score,
            'nivel' => $evaluation->anxiety_level,
            'icono' => 'bi-lightning-charge-fill',
            'color' => 'warning',
            'descripcion' => 'Explora manifestaciones de ansiedad reportadas durante la última semana.',
        ],
        [
            'nombre' => 'Estrés',
            'puntaje' => $evaluation->stress_score,
            'nivel' => $evaluation->stress_level,
            'icono' => 'bi-activity',
            'color' => 'danger',
            'descripcion' => 'Explora experiencias de tensión y dificultad para relajarse.',
        ],
    ];
@endphp

@push('styles')
    <style>
        .anime-item {
            opacity: 1;
            transform: translateY(20px);
        }

        .eval-hero {
            position: relative;
            overflow: hidden;
            background: var(--app-primary);
        }

        .eval-hero::after {
            content: '\F52A';
            font-family: "bootstrap-icons";
            position: absolute;
            top: -10%;
            right: -4%;
            font-size: 14rem;
            color: #fff;
            opacity: .08;
            transform: rotate(-12deg);
            pointer-events: none;
            z-index: 2;
        }

        #granim-canvas-eval-show {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .banner-content {
            position: relative;
            z-index: 3;
        }

        .glass-badge {
            background: rgba(255,255,255,.2) !important;
            color: #fff !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.3);
        }

        .glass-btn {
            background: rgba(255,255,255,.95) !important;
            color: var(--app-primary-dark) !important;
            border: 1px solid rgba(255,255,255,.5);
            transition: all .3s ease;
        }

        .glass-btn:hover {
            background: #fff !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,.15);
        }

        /* Resultados */
        .result-card {
            transition: transform .3s ease, box-shadow .3s ease;
        }

        .result-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 14px 30px rgba(0,0,0,.09) !important;
        }

        .dim-icon {
            width: 58px;
            height: 58px;
            border-radius: 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .score-display {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1;
            color: var(--bs-body-color);
        }

        .score-maximum {
            font-size: 1rem;
            font-weight: 600;
            color: var(--bs-secondary-color);
        }

        .score-caption {
            font-size: .8rem;
            color: var(--bs-secondary-color);
        }

        .risk-chip {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: .5rem 1rem;
            border-radius: 999px;
            font-size: .82rem;
            font-weight: 700;
            line-height: 1.4;
            text-align: center;
            max-width: 100%;
            white-space: normal;
        }

        .risk-nulo {
            background: rgba(34,197,94,.12);
            color: #15803d;
        }

        .risk-leve {
            background: rgba(234,179,8,.15);
            color: #a16207;
        }

        .risk-moderado {
            background: rgba(249,115,22,.14);
            color: #c2410c;
        }

        .risk-severo {
            background: rgba(239,68,68,.14);
            color: #b91c1c;
        }

        .risk-extremo {
            background: rgba(185,28,28,.18);
            color: #991b1b;
        }

        .risk-desconocido {
            background: rgba(100,116,139,.13);
            color: #475569;
        }

        .result-description {
            font-size: .87rem;
            line-height: 1.6;
            color: var(--bs-secondary-color);
        }

        .summary-panel {
            padding: 1.25rem;
            border: 1px solid rgba(100,116,139,.16);
            border-radius: 18px;
            background: rgba(100,116,139,.04);
        }

        /* Modo oscuro */
        body.theme-dark .risk-nulo { color: #86efac; }
        body.theme-dark .risk-leve { color: #fde68a; }
        body.theme-dark .risk-moderado { color: #fdba74; }
        body.theme-dark .risk-severo,
        body.theme-dark .risk-extremo { color: #fca5a5; }
        body.theme-dark .risk-desconocido { color: #cbd5e1; }

        body.theme-dark .text-body-secondary {
            color: #94a3b8 !important;
        }

        @media (prefers-color-scheme: dark) {
            body.theme-system .risk-nulo { color: #86efac; }
            body.theme-system .risk-leve { color: #fde68a; }
            body.theme-system .risk-moderado { color: #fdba74; }
            body.theme-system .risk-severo,
            body.theme-system .risk-extremo { color: #fca5a5; }
            body.theme-system .risk-desconocido { color: #cbd5e1; }
        }
    </style>
@endpush

@section('content')

    {{-- Navegación --}}
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('evaluaciones.index') }}"
           class="btn btn-outline-primary rounded-pill px-4 fw-bold">

            <i class="bi bi-arrow-left me-2"></i>
            Evaluaciones
        </a>

        <a href="{{ route('dass21.history') }}"
           class="btn btn-outline-secondary rounded-pill px-4 fw-bold">

            <i class="bi bi-clock-history me-2"></i>
            Mi historial DASS-21
        </a>
    </div>

    <div class="row g-4">

        {{-- Encabezado --}}
        <div class="col-12 anime-item">
            <div class="app-card eval-hero p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-eval-show"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">

                        <span class="badge glass-badge rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">

                            <i class="bi bi-check-circle-fill me-1"></i>

                            Completado el
                            {{ $evaluation->completed_at->format('d/m/Y') }}
                        </span>

                        <h2 class="fw-black mb-2 text-white"
                            style="font-size:2.15rem;text-shadow:0 2px 4px rgba(0,0,0,.2);">

                            Resultados del DASS-21
                        </h2>

                        <p class="mb-0 text-white text-opacity-90 fs-5">
                            Conoce tus resultados de depresión,
                            ansiedad y estrés de forma individual.
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}"
                           class="btn glass-btn rounded-pill fw-bold px-4 py-2 shadow-sm">

                            <i class="bi bi-arrow-left me-1"></i>
                            Volver a Evaluaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen general --}}
        <div class="col-12 anime-item">
            <div class="app-card bg-body-tertiary rounded-4 p-4 border border-secondary border-opacity-10 shadow-sm">

                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">

                    <div>
                        <h4 class="fw-black text-body mb-2">
                            Resumen de tu evaluación
                        </h4>

                        <p class="text-body-secondary mb-0">
                            El DASS-21 evalúa tres dimensiones independientes.
                            El nivel mostrado a continuación es el más elevado
                            entre las tres.
                        </p>
                    </div>

                    <div>
                        <span class="risk-chip {{ $getRiskClass($evaluation->max_severity_level) }}">
                            <i class="bi bi-bar-chart-fill me-1"></i>
                            {{ $evaluation->max_severity_level ?? 'Sin resultado' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resultados independientes --}}
        @foreach($dimensiones as $dimension)

            <div class="col-md-4 anime-item">
                <div class="app-card result-card bg-body-tertiary p-4 p-md-5 border border-secondary border-opacity-10 shadow-sm rounded-4 text-center h-100 d-flex flex-column align-items-center">

                    {{-- Icono --}}
                    <div class="dim-icon bg-{{ $dimension['color'] }} bg-opacity-10 text-{{ $dimension['color'] }} mb-3 shadow-sm">

                        <i class="bi {{ $dimension['icono'] }}"></i>

                    </div>

                    {{-- Nombre --}}
                    <h4 class="fw-black text-body mb-3">
                        {{ $dimension['nombre'] }}
                    </h4>

                    {{-- Puntaje --}}
                    <div class="d-flex align-items-baseline justify-content-center gap-2 mb-1">

                        <span class="score-display">
                            {{ $dimension['puntaje'] }}
                        </span>

                        <span class="score-maximum">
                            / 42
                        </span>
                    </div>

                    <span class="score-caption mb-3">
                        Puntuación de la dimensión
                    </span>

                    {{-- Nivel --}}
                    <span class="risk-chip {{ $getRiskClass($dimension['nivel']) }} mb-3">

                        {{ $dimension['nivel'] ?? 'Sin resultado' }}

                    </span>

                    {{-- Descripción --}}
                    <p class="result-description mb-0 mt-auto">
                        {{ $dimension['descripcion'] }}
                    </p>

                </div>
            </div>

        @endforeach

        {{-- Interpretación general --}}
        <div class="col-12 anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4">

                <div class="d-flex align-items-start gap-3">

                    <i class="bi bi-info-circle-fill text-primary fs-4"></i>

                    <div>
                        <h5 class="fw-bold text-body mb-2">
                            ¿Cómo interpretar estos resultados?
                        </h5>

                        <p class="text-body-secondary mb-2">
                            Cada dimensión tiene su propio sistema de
                            clasificación. Los puntajes se muestran
                            en la escala ajustada de 0 a 42 puntos.
                        </p>

                        <p class="text-body-secondary mb-0">
                            Los resultados describen síntomas reportados
                            durante la última semana. No permiten establecer
                            por sí solos un diagnóstico ni determinar
                            una urgencia clínica.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Orientación para niveles elevados --}}
        @if($evaluation->hasCriticalSeverity())

            <div class="col-12 anime-item">
                <div class="app-card bg-body-tertiary p-4 border border-warning border-opacity-25 shadow-sm rounded-4">

                    <div class="d-flex align-items-start gap-3">

                        <i class="bi bi-heart-pulse-fill fs-3 text-warning"></i>

                        <div>
                            <h5 class="fw-bold text-body mb-2">
                                Orientación psicológica recomendada
                            </h5>

                            <p class="text-body-secondary mb-3">
                                Una o más dimensiones presentan niveles
                                elevados de síntomas. Puede ser útil
                                conversar con un profesional de orientación
                                psicológica para comprender mejor
                                tus resultados.
                            </p>

                            <div class="d-flex flex-wrap gap-2">

                                <a href="tel:8009112000"
                                   class="btn btn-outline-primary btn-sm rounded-pill px-3 fw-bold">

                                    <i class="bi bi-telephone-fill me-1"></i>
                                    Línea de la Vida
                                </a>

                                <a href="tel:5552598121"
                                   class="btn btn-outline-secondary btn-sm rounded-pill px-3 fw-bold">

                                    <i class="bi bi-telephone me-1"></i>
                                    SAPTEL
                                </a>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

        @endif

        {{-- Retroalimentación profesional --}}
        @if($evaluation->evaluacion?->diagnostico?->retroalimentacion_estudiante)

            <div class="col-12 anime-item">
                <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4">

                    <div class="d-flex align-items-start gap-3">

                        <i class="bi bi-chat-heart-fill fs-3 text-primary"></i>

                        <div>
                            <h5 class="fw-bold text-body mb-2">
                                Retroalimentación del psicólogo
                            </h5>

                            <p class="text-body-secondary mb-0"
                               style="white-space:pre-line;">
                                {{ $evaluation->evaluacion->diagnostico->retroalimentacion_estudiante }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        @endif

        {{-- Nota informativa --}}
        <div class="col-12 anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4">

                <div class="d-flex align-items-start gap-3">

                    <i class="bi bi-shield-check text-body-secondary fs-4"></i>

                    <p class="text-body-secondary small mb-0">
                        <strong>Nota importante:</strong>
                        El DASS-21 es un instrumento de tamizaje
                        que evalúa síntomas de depresión, ansiedad
                        y estrés. Sus resultados son orientativos
                        y no sustituyen una valoración profesional.

                        Si tienes inquietudes sobre tus resultados
                        o cómo te sientes, puedes solicitar
                        orientación psicológica.
                    </p>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            if (typeof anime !== 'undefined') {
                anime({
                    targets: '.anime-item',
                    translateY: [30, 0],
                    opacity: [0, 1],
                    delay: anime.stagger(150),
                    easing: 'easeOutExpo',
                    duration: 1000
                });
            }

            if (
                document.getElementById('granim-canvas-eval-show') &&
                typeof Granim !== 'undefined'
            ) {
                new Granim({
                    element: '#granim-canvas-eval-show',
                    direction: 'left-right',
                    isPausedWhenNotInView: true,
                    states: {
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
