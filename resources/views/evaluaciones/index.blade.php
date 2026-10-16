
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

    $dassNivelClase = function (?string $nivel): string {
        return match ($nivel) {
            'Normal' => 'risk-nulo',
            'Leve' => 'risk-leve',
            'Moderado' => 'risk-moderado',
            'Severo' => 'risk-severo',
            'Extremadamente severo' => 'risk-extremo',
            default => 'risk-desconocido',
        };
    };
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
            background-color: var(--app-primary);
        }

        .eval-hero::after {
            content: '\F26A';
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

        #granim-canvas-eval {
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
            border-radius: 999px;
            transition: all .3s ease;
        }

        .glass-btn:hover {
            background: #fff !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,.15);
        }

        .instrument-card {
            transition: all .3s cubic-bezier(.25,.8,.25,1);
            border: 1px solid transparent;
        }

        .instrument-card:hover {
            transform: translateY(-5px);
            border-color: var(--app-primary-soft) !important;
            box-shadow: 0 18px 36px rgba(0,0,0,.1);
        }

        .instrument-icon {
            width: 58px;
            height: 58px;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .metric-pill {
            display: inline-flex;
            align-items: center;
            gap: .45rem;
            padding: .45rem .95rem;
            border-radius: 999px;
            font-size: .85rem;
            font-weight: 700;
        }

        .metric-soft-primary {
            background: var(--app-primary-soft);
            color: var(--app-primary-dark);
        }

        .risk-chip {
            display: inline-flex;
            align-items: center;
            gap: .3rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            font-size: .8rem;
            font-weight: 700;
            max-width: 100%;
            white-space: normal;
            overflow-wrap: anywhere;
            line-height: 1.4;
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

        .estado-badge {
            font-size: .78rem;
            padding: .45rem .85rem;
            border-radius: 999px;
            font-weight: 700;
        }

        /* Diseño de las tres dimensiones del DASS-21 */
        .dass-results {
            padding: 1rem;
            border-radius: 18px;
            border: 1px solid rgba(100,116,139,.16);
            background: rgba(100,116,139,.04);
        }

        .dass-dimension-box {
            height: 100%;
            padding: .85rem;
            border: 1px solid rgba(100,116,139,.16);
            border-radius: 14px;
            background: var(--bs-body-bg);
        }

        .dass-dimension-title {
            display: block;
            font-size: .78rem;
            font-weight: 700;
            color: var(--bs-secondary-color);
            margin-bottom: .65rem;
        }

        .dass-description {
            font-size: .8rem;
            line-height: 1.5;
        }

        /* Accesibilidad en modo oscuro */
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

            body.theme-system .text-body-secondary {
                color: #94a3b8 !important;
            }
        }
    </style>
@endpush

@section('content')

    {{-- CUESTIONARIOS EN BORRADOR --}}
    @if($borradores->isNotEmpty())
        <section class="tm-panel tm-body mb-4"
                 aria-label="Cuestionarios en borrador">

            <h2 class="h5">Continúa tus cuestionarios</h2>

            <p class="text-body-secondary">
                Los borradores no cuentan como evaluaciones completadas.
                Revisa tus respuestas antes de enviarlas.
            </p>

            <div class="d-flex flex-wrap gap-2">
                @foreach($borradores as $borrador)
                    <a class="btn btn-outline-primary"
                       href="{{ $borrador->instrument === 'DASS21'
                           ? route('dass21.create')
                           : route('evaluaciones.aplicar', strtolower($borrador->instrument)) }}">

                        Continuar {{ $borrador->instrument }}
                        · {{ count($borrador->answers) }} respuestas
                    </a>
                @endforeach
            </div>
        </section>
    @endif

    @php
        $totalInstrumentos = $instrumentosDashboard->count();
        $totalCompletadas = $instrumentosDashboard
            ->where('estado', 'completada')->count();
        $totalPendientes = $instrumentosDashboard
            ->where('estado', '!=', 'completada')->count();
    @endphp

    <div class="row g-4">

        {{-- BANNER PRINCIPAL --}}
        <div class="col-12 anime-item">
            <div class="app-card eval-hero p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-eval"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">

                        <span class="badge glass-badge rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-clipboard2-pulse-fill me-1"></i>
                            Tamizaje Emocional
                        </span>

                        <h2 class="fw-black mb-2 text-white"
                            style="font-size:2.2rem;text-shadow:0 2px 4px rgba(0,0,0,.2);">
                            Hola, {{ auth()->user()->name }}
                        </h2>

                        <p class="mb-3 text-white text-opacity-90 fs-5">
                            Responde tus instrumentos disponibles.
                            Tus resultados ayudan a construir tu seguimiento
                            emocional dentro de PROMETEO.
                        </p>

                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge glass-badge rounded-pill px-3 py-2">
                                <i class="bi bi-collection-fill me-1"></i>
                                Instrumentos: {{ $totalInstrumentos + 1 }}
                            </span>

                            <span class="badge glass-badge rounded-pill px-3 py-2">
                                <i class="bi bi-check-circle-fill me-1"></i>
                                Completadas: {{ $totalCompletadas }}
                            </span>

                            <span class="badge glass-badge rounded-pill px-3 py-2">
                                <i class="bi bi-hourglass-split me-1"></i>
                                Pendientes: {{ $totalPendientes }}
                            </span>
                        </div>
                    </div>

                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('estudiante.dashboard') }}"
                           class="btn glass-btn fw-bold px-4 py-2 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i>
                            Volver al inicio
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- TARJETAS PHQ-9 Y GAD-7 --}}
        @forelse($instrumentosDashboard as $item)

            @php
                $interpretacion = \App\Support\InterpretacionCuestionario::interpretar(
                    $item->instrumento->acronimo,
                    $item->puntaje_total === null
                        ? null
                        : (int) $item->puntaje_total
                );

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
                                <h4 class="fw-black mb-1 text-body">
                                    {{ $item->instrumento->nombre }}
                                </h4>

                                <div class="text-body-secondary fw-bold">
                                    {{ strtoupper($item->instrumento->acronimo) }}
                                </div>
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
                                    <i class="bi bi-bar-chart-fill"></i>
                                    Puntaje: {{ $item->puntaje_total }}
                                </span>

                                @if($interpretacion)
                                    <span class="risk-chip {{ $interpretacion['clase'] }} shadow-sm">
                                        <i class="bi bi-activity"></i>
                                        {{ $interpretacion['etiqueta'] }}
                                    </span>
                                @endif
                            </div>

                            <small class="d-block text-body-secondary mt-2">
                                Resultado orientativo.
                                No constituye un diagnóstico clínico.
                            </small>

                        @else
                            <p class="text-body-secondary mb-0 fw-medium">
                                <i class="bi bi-info-circle me-1"></i>
                                Aún no has respondido este instrumento.
                            </p>
                        @endif
                    </div>

                    <div class="mt-auto pt-3 border-top border-secondary border-opacity-10">
                        <a href="{{ route('evaluaciones.aplicar', strtolower($item->instrumento->acronimo)) }}"
                           class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm w-100">

                            <i class="bi {{ $item->estado === 'completada'
                                ? 'bi-arrow-clockwise'
                                : 'bi-pencil-square' }} me-2"></i>

                            {{ $item->estado === 'completada'
                                ? 'Responder de nuevo'
                                : 'Iniciar evaluación' }}
                        </a>
                    </div>
                </div>
            </div>

        @empty

            <div class="col-12 anime-item">
                <div class="app-card bg-body-tertiary p-5 border border-secondary border-opacity-10 shadow-sm rounded-4 text-center">
                    <i class="bi bi-clipboard-x fs-1 text-body-secondary"></i>
                    <h4 class="fw-black mt-3 text-body">
                        No hay instrumentos disponibles
                    </h4>
                    <p class="text-body-secondary mb-0">
                        Primero se deben registrar instrumentos en el sistema.
                    </p>
                </div>
            </div>

        @endforelse

        {{-- =========================================== --}}
        {{-- NUEVA TARJETA DE RESULTADOS DASS-21         --}}
        {{-- =========================================== --}}

        @php
            $dassEstado = $dass21Eval ? 'completada' : 'pendiente';

            $dassEstadoClass = $dass21Eval
                ? 'bg-success text-white'
                : 'bg-warning text-dark';

            $dassDimensiones = $dass21Eval ? [
                [
                    'nombre' => 'Depresión',
                    'nivel' => $dass21Eval->depression_level,
                    'icono' => 'bi-cloud-rain',
                ],
                [
                    'nombre' => 'Ansiedad',
                    'nivel' => $dass21Eval->anxiety_level,
                    'icono' => 'bi-activity',
                ],
                [
                    'nombre' => 'Estrés',
                    'nivel' => $dass21Eval->stress_level,
                    'icono' => 'bi-lightning-charge',
                ],
            ] : [];
        @endphp

        <div class="col-lg-6 anime-item">
            <div class="app-card instrument-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 d-flex flex-column">

                {{-- Encabezado DASS-21 --}}
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">

                    <div class="d-flex align-items-center gap-3">
                        <div class="instrument-icon bg-info bg-opacity-10 text-info shadow-sm">
                            <i class="bi bi-layers-fill fs-4"></i>
                        </div>

                        <div>
                            <h4 class="fw-black mb-1 text-body">
                                Depresión, Ansiedad y Estrés
                            </h4>
                            <div class="text-body-secondary fw-bold">
                                DASS-21
                            </div>
                        </div>
                    </div>

                    <span class="estado-badge {{ $dassEstadoClass }} shadow-sm">
                        {{ ucfirst($dassEstado) }}
                    </span>
                </div>

                {{-- Resultados DASS-21 --}}
                <div class="mb-4">

                    @if($dass21Eval)

                        <div class="dass-results">

                            <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
                                <span class="metric-pill metric-soft-primary">
                                    <i class="bi bi-bar-chart-fill"></i>
                                    Nivel más elevado
                                </span>

                                <span class="risk-chip {{ $dassNivelClase($dass21Eval->max_severity_level) }}">
                                    {{ $dass21Eval->max_severity_level ?? 'Sin resultado' }}
                                </span>
                            </div>

                            <div class="row g-2">

                                @foreach($dassDimensiones as $dimension)
                                    <div class="col-12 col-sm-4">
                                        <div class="dass-dimension-box">

                                            <span class="dass-dimension-title">
                                                <i class="bi {{ $dimension['icono'] }} me-1"></i>
                                                {{ $dimension['nombre'] }}
                                            </span>

                                            <span class="risk-chip {{ $dassNivelClase($dimension['nivel']) }}">
                                                {{ $dimension['nivel'] ?? 'Sin resultado' }}
                                            </span>

                                        </div>
                                    </div>
                                @endforeach

                            </div>

                            <p class="dass-description text-body-secondary mt-3 mb-0">
                                Cada dimensión tiene su propia clasificación.
                                Este resultado es orientativo y no constituye
                                un diagnóstico clínico.
                            </p>
                        </div>

                    @else

                        <p class="text-body-secondary mb-0 fw-medium">
                            <i class="bi bi-info-circle me-1"></i>
                            Este cuestionario evalúa tres dimensiones:
                            depresión, ansiedad y estrés.
                        </p>

                    @endif
                </div>

                {{-- Acciones DASS-21 --}}
                <div class="mt-auto pt-3 border-top border-secondary border-opacity-10 d-flex gap-2">

                    <a href="{{ route('dass21.create') }}"
                       class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm flex-grow-1">

                        <i class="bi {{ $dass21Eval
                            ? 'bi-arrow-clockwise'
                            : 'bi-pencil-square' }} me-2"></i>

                        {{ $dass21Eval
                            ? 'Responder de nuevo'
                            : 'Iniciar evaluación' }}
                    </a>

                    @if($dass21Eval)
                        <a href="{{ route('dass21.show', $dass21Eval->id) }}"
                           class="btn btn-outline-secondary rounded-pill px-3 fw-bold shadow-sm"
                           title="Ver resultados detallados"
                           aria-label="Ver resultados detallados DASS-21">
                            <i class="bi bi-eye-fill"></i>
                        </a>
                    @endif

                </div>
            </div>
        </div>

        {{-- Historial DASS-21 --}}
        <div class="col-12 anime-item">
            <a class="btn btn-outline-primary rounded-pill px-4 fw-bold"
               href="{{ route('dass21.history') }}">
                <i class="bi bi-clock-history me-2"></i>
                Historial DASS-21
            </a>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            if (typeof anime !== 'undefined') {
                anime({
                    targets: '.anime-item',
                    translateY: [28, 0],
                    opacity: [0, 1],
                    delay: anime.stagger(110),
                    easing: 'easeOutExpo',
                    duration: 900
                });
            }

            if (
                document.getElementById('granim-canvas-eval') &&
                typeof Granim !== 'undefined'
            ) {
                new Granim({
                    element: '#granim-canvas-eval',
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
