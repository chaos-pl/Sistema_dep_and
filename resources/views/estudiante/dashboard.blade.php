
@extends('layouts.app')
@section('page-class', 'student-page')

@section('title', 'Dashboard Estudiante - PROMETEO')
@section('page-title', 'Inicio del Estudiante')
@section('page-subtitle', 'Evaluaciones pendientes y registro emocional')

@php
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    // Evaluación DASS-21 más reciente.
    $dass21EvalShortcut = $dass21Eval;

    $dassRequiereAtencion = $dass21EvalShortcut &&
        $dass21EvalShortcut->completed_at->diffInDays(now()) >= 14;

    // Clases visuales según la clasificación almacenada.
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

    // Dimensiones DASS-21 para el dashboard.
    $dassDimensionesDashboard = $dass21EvalShortcut ? [
        [
            'nombre' => 'Depresión',
            'nivel' => $dass21EvalShortcut->depression_level,
            'icono' => 'bi-cloud-rain',
        ],
        [
            'nombre' => 'Ansiedad',
            'nivel' => $dass21EvalShortcut->anxiety_level,
            'icono' => 'bi-activity',
        ],
        [
            'nombre' => 'Estrés',
            'nivel' => $dass21EvalShortcut->stress_level,
            'icono' => 'bi-lightning-charge',
        ],
    ] : [];
@endphp

@push('styles')
    <style>
        /* Diseño general */
        .hover-elevate {
            transition: transform .3s cubic-bezier(.25,.8,.25,1),
            box-shadow .3s ease;
            border: 1px solid transparent;
        }

        .hover-elevate:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,.08) !important;
            border-color: var(--app-primary-soft) !important;
        }

        .bg-welcome-student {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary);
        }

        .bg-welcome-student::after {
            content: '\F431';
            font-family: "bootstrap-icons";
            position: absolute;
            top: -20%;
            right: -5%;
            font-size: 14rem;
            color: #fff;
            opacity: .08;
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 2;
        }

        #granim-dashboard {
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

        .anime-item {
            opacity: 1;
            transform: translateY(20px);
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
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.5);
            transition: all .3s ease;
        }

        .glass-btn:hover {
            background: #fff !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,.15) !important;
        }

        /* Etiquetas de resultados */
        .student-result-box {
            padding: 1rem;
            border: 1px solid rgba(100,116,139,.16);
            border-radius: 16px;
            background: rgba(100,116,139,.04);
        }

        .student-result-pill {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            padding: .5rem .9rem;
            border-radius: 999px;
            font-size: .84rem;
            font-weight: 700;
            line-height: 1.35;
            max-width: 100%;
            white-space: normal;
            overflow-wrap: anywhere;
        }

        .student-result-score {
            color: #1d4ed8;
            background: rgba(37,99,235,.11);
        }

        .student-result-level.risk-nulo {
            color: #166534;
            background: rgba(34,197,94,.12);
        }

        .student-result-level.risk-leve {
            color: #92400e;
            background: rgba(234,179,8,.15);
        }

        .student-result-level.risk-moderado {
            color: #9a3412;
            background: rgba(249,115,22,.14);
        }

        .student-result-level.risk-severo {
            color: #991b1b;
            background: rgba(239,68,68,.12);
        }

        .student-result-level.risk-extremo {
            color: #991b1b;
            background: rgba(185,28,28,.18);
        }

        .student-result-level.risk-desconocido {
            color: #475569;
            background: rgba(100,116,139,.13);
        }

        .student-result-note {
            font-size: .78rem;
            line-height: 1.5;
        }

        /* Diseño DASS-21 */
        .dass-dashboard-results {
            margin-top: 1.2rem;
        }

        .dass-dashboard-dimension {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            gap: .55rem;
            padding: .8rem;
            height: 100%;
            background: var(--bs-body-bg);
            border: 1px solid rgba(100,116,139,.15);
            border-radius: 14px;
            min-width: 0;
        }

        .dass-dashboard-dimension-name {
            display: inline-flex;
            align-items: center;
            gap: .4rem;
            font-size: .8rem;
            font-weight: 700;
            color: var(--bs-secondary-color);
        }

        .dass-dashboard-dimension .student-result-pill {
            font-size: .76rem;
            padding: .4rem .7rem;
        }

        /* Modo oscuro */
        body.theme-dark .text-body-secondary {
            color: #94a3b8 !important;
        }

        body.theme-dark .text-body {
            color: #f8fafc !important;
        }

        body.theme-dark .student-result-score {
            color: #93c5fd;
        }

        body.theme-dark .student-result-level.risk-nulo {
            color: #86efac;
        }

        body.theme-dark .student-result-level.risk-leve {
            color: #fde68a;
        }

        body.theme-dark .student-result-level.risk-moderado {
            color: #fdba74;
        }

        body.theme-dark .student-result-level.risk-severo,
        body.theme-dark .student-result-level.risk-extremo {
            color: #fca5a5;
        }

        body.theme-dark .student-result-level.risk-desconocido {
            color: #cbd5e1;
        }

        @media (prefers-color-scheme: dark) {
            body.theme-system .text-body-secondary {
                color: #94a3b8 !important;
            }

            body.theme-system .text-body {
                color: #f8fafc !important;
            }

            body.theme-system .student-result-score {
                color: #93c5fd;
            }

            body.theme-system .student-result-level.risk-nulo {
                color: #86efac;
            }

            body.theme-system .student-result-level.risk-leve {
                color: #fde68a;
            }

            body.theme-system .student-result-level.risk-moderado {
                color: #fdba74;
            }

            body.theme-system .student-result-level.risk-severo,
            body.theme-system .student-result-level.risk-extremo {
                color: #fca5a5;
            }

            body.theme-system .student-result-level.risk-desconocido {
                color: #cbd5e1;
            }
        }
    </style>
@endpush

@section('content')

    <div class="row g-4">

        {{-- BANNER PRINCIPAL --}}
        <div class="col-12 anime-item">
            <div class="app-card accent-gradient granim-surface bg-welcome-student p-4 p-md-5 text-white rounded-4 border-0 shadow-lg">

                <canvas id="granim-dashboard"
                        class="granim-canvas"
                        data-prometeo-granim
                        aria-hidden="true"
                        style="position:absolute;inset:0;width:100%;height:100%;z-index:0;">
                </canvas>

                <div class="row align-items-center banner-content">

                    <div class="col-lg-8">
                    <span class="badge glass-badge rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                        <i class="bi bi-shield-check me-1"></i>
                        Entorno Seguro y Confidencial
                    </span>

                        <h2 class="fw-black mb-2 text-white"
                            style="font-size:2.2rem;text-shadow:0 2px 4px rgba(0,0,0,.2);">
                            Hola, {{ auth()->user()->name }}
                        </h2>

                        <p class="mb-0 text-white text-opacity-90 fs-5">
                            @if($estudiante)
                                Matrícula: {{ $estudiante->matricula }}
                                · Grupo: {{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}
                            @else
                                Tu cuenta aún no tiene expediente de estudiante vinculado.
                            @endif
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}"
                           class="btn glass-btn rounded-pill fw-bold px-4 py-2 shadow-sm hover-elevate">

                            <i class="bi bi-clipboard2-check-fill me-2"></i>
                            Ver Evaluaciones
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- ============================================ --}}
        {{-- MONITOREO DASS-21 CON ETIQUETAS DE SÍNTOMAS --}}
        {{-- ============================================ --}}

        @if($estudiante)

            <div class="col-12 anime-item">
                <div class="app-card bg-primary bg-opacity-10 border border-primary border-opacity-25 shadow-sm rounded-4 p-4 hover-elevate">

                    <div class="d-flex flex-column flex-lg-row justify-content-between align-items-lg-start gap-4">

                        {{-- Información principal --}}
                        <div class="d-flex align-items-start gap-3 flex-grow-1">

                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow flex-shrink-0"
                                 style="width:60px;height:60px;">

                                <i class="bi bi-heart-pulse-fill fs-3"></i>
                            </div>

                            <div class="flex-grow-1" style="min-width:0;">

                                <h4 class="fw-black text-primary mb-1">
                                    Monitoreo DASS-21
                                </h4>

                                <p class="text-body-secondary mb-0 small">

                                    @if(!$dass21EvalShortcut)

                                        Aún no has realizado tu primer
                                        tamizaje emocional.
                                        ¡Toma 5 minutos para conocerte mejor!

                                    @elseif($dassRequiereAtencion)

                                        Han pasado más de 14 días desde tu
                                        última evaluación. Es un buen momento
                                        para actualizar tu estado.

                                    @else

                                        <i class="bi bi-check-circle-fill text-success me-1"></i>

                                        Tamizaje al día. Última evaluación:
                                        {{ $dass21EvalShortcut->completed_at->format('d/m/Y') }}

                                    @endif

                                </p>

                                {{-- Resultados DASS-21 --}}
                                @if($dass21EvalShortcut)

                                    <div class="student-result-box dass-dashboard-results">

                                        {{-- Nivel más elevado --}}
                                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">

                                        <span class="fw-bold text-body small">
                                            Nivel más elevado:
                                        </span>

                                            <span class="student-result-pill student-result-level {{ $dassNivelClase($dass21EvalShortcut->max_severity_level) }}">

                                            <i class="bi bi-bar-chart-fill"
                                               aria-hidden="true"></i>

                                            {{ $dass21EvalShortcut->max_severity_level ?? 'Sin resultado' }}

                                        </span>
                                        </div>

                                        {{-- Tres dimensiones --}}
                                        <div class="row g-2">

                                            @foreach($dassDimensionesDashboard as $dimension)

                                                <div class="col-12 col-sm-4">

                                                    <div class="dass-dashboard-dimension">

                                                    <span class="dass-dashboard-dimension-name">

                                                        <i class="bi {{ $dimension['icono'] }}"
                                                           aria-hidden="true"></i>

                                                        {{ $dimension['nombre'] }}

                                                    </span>

                                                        <span class="student-result-pill student-result-level {{ $dassNivelClase($dimension['nivel']) }}">

                                                        {{ $dimension['nivel'] ?? 'Sin resultado' }}

                                                    </span>

                                                    </div>
                                                </div>

                                            @endforeach

                                        </div>

                                        <p class="student-result-note text-body-secondary mb-0 mt-3">
                                            Cada dimensión tiene su propia clasificación.
                                            Estos resultados son orientativos
                                            y no constituyen un diagnóstico clínico.
                                        </p>

                                    </div>

                                @endif

                            </div>
                        </div>

                        {{-- Acciones --}}
                        <div class="d-flex flex-column flex-sm-row flex-lg-column gap-2 flex-shrink-0 student-cta">

                            <a href="{{ route('dass21.create') }}"
                               class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm">

                                <i class="bi bi-pencil-square me-2"></i>

                                {{ !$dass21EvalShortcut
                                    ? 'Comenzar Tamizaje'
                                    : 'Actualizar Estado' }}
                            </a>

                            @if($dass21EvalShortcut)

                                <a href="{{ route('dass21.show', $dass21EvalShortcut->id) }}"
                                   class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold"
                                   aria-label="Ver resultados detallados DASS-21">

                                    <i class="bi bi-eye-fill me-2"></i>
                                    Ver resultados

                                </a>

                            @endif

                        </div>

                    </div>
                </div>
            </div>

        @endif

        {{-- ============================================ --}}
        {{-- ESTADÍSTICAS Y RESUMEN ACADÉMICO             --}}
        {{-- ============================================ --}}

        @if($estudiante)

            <div class="col-md-4 anime-item">
                <div class="app-card p-4 border-0 shadow-sm rounded-4 bg-body-tertiary h-100 hover-elevate">

                    <div class="d-flex align-items-center gap-3 mb-3">

                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                             style="width:52px;height:52px;">

                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>

                        <div>
                            <h5 class="fw-black text-body mb-0">
                                {{ $totalCompletadas ?? 0 }}
                            </h5>

                            <small class="text-body-secondary">
                                Evaluaciones completadas
                            </small>
                        </div>

                    </div>
                </div>
            </div>

            <div class="col-md-8 anime-item">
                <div class="app-card p-4 border-0 shadow-sm rounded-4 bg-body-tertiary h-100 hover-elevate">

                    <h5 class="fw-black text-body mb-3">
                        Resumen académico
                    </h5>

                    <div class="row g-3 academic-summary">

                        <div class="col-md-4">
                            <small class="text-body-secondary fw-bold text-uppercase d-block"
                                   style="letter-spacing:.5px;">
                                Código anónimo
                            </small>

                            <span class="fw-bold text-body fs-5">
                            {{ $estudiante->codigo_anonimo }}
                        </span>
                        </div>

                        <div class="col-md-4">
                            <small class="text-body-secondary fw-bold text-uppercase d-block"
                                   style="letter-spacing:.5px;">
                                Matrícula
                            </small>

                            <span class="fw-bold text-body fs-5">
                            {{ $estudiante->matricula }}
                        </span>
                        </div>

                        <div class="col-md-4">
                            <small class="text-body-secondary fw-bold text-uppercase d-block"
                                   style="letter-spacing:.5px;">
                                Grupo
                            </small>

                            <span class="fw-bold text-body fs-5">
                            {{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}
                        </span>
                        </div>

                    </div>
                </div>
            </div>

        @endif

        {{-- ============================================ --}}
        {{-- CUESTIONARIOS PHQ-9 Y GAD-7                 --}}
        {{-- ============================================ --}}

        @forelse($instrumentosDashboard as $item)

            @php
                $acronimo = strtoupper($item->instrumento->acronimo);
                $esPhq = str_contains($acronimo, 'PHQ');

                $btnClass = $esPhq
                    ? 'btn-primary'
                    : 'btn-info text-white';

                $iconBg = $esPhq
                    ? 'bg-primary bg-opacity-10 text-primary'
                    : 'bg-info bg-opacity-10 text-info';

                $icon = $esPhq
                    ? 'bi-cloud-rain-fill'
                    : 'bi-lightning-fill';

                $interpretacion = \App\Support\InterpretacionCuestionario::interpretar(
                    $item->instrumento->acronimo,
                    $item->puntaje_total === null
                        ? null
                        : (int) $item->puntaje_total
                );

                $badgeClass = match($item->estado) {
                    'completada' => 'bg-success text-white',
                    'en_proceso' => 'bg-warning text-dark',
                    'abandonada' => 'bg-danger text-white',
                    default => 'bg-warning text-dark',
                };
            @endphp

            <div class="col-md-6 anime-item">

                <div class="app-card hover-elevate p-4 p-md-5 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 d-flex flex-column justify-content-between bg-body-tertiary">

                    <div>

                        {{-- Encabezado --}}
                        <div class="d-flex justify-content-between align-items-start mb-4">

                            <div>

                            <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 fw-bold shadow-sm mb-2">

                                {{ ucfirst(str_replace('_', ' ', $item->estado)) }}

                            </span>

                                <h4 class="fw-black text-body mb-0">
                                    {{ strtoupper($item->instrumento->acronimo) }}
                                </h4>

                            </div>

                            <div class="{{ $iconBg }} rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                 style="width:55px;height:55px;">

                                <i class="bi {{ $icon }} fs-3"></i>

                            </div>

                        </div>

                        <p class="text-body-secondary fw-medium mb-3">
                            {{ $item->instrumento->nombre }}
                        </p>

                        {{-- Etiquetas de resultados --}}
                        @if($item->evaluacion && $item->puntaje_total !== null)

                            <div class="student-result-box">

                                <div class="d-flex flex-wrap gap-2 align-items-center">

                                <span class="student-result-pill student-result-score">

                                    <i class="bi bi-bar-chart-fill"
                                       aria-hidden="true"></i>

                                    Puntaje: {{ $item->puntaje_total }}

                                </span>

                                    @if($interpretacion)

                                        <span class="student-result-pill student-result-level {{ $interpretacion['clase'] }}">

                                        <i class="bi bi-activity"
                                           aria-hidden="true"></i>

                                        {{ $interpretacion['etiqueta'] }}

                                    </span>

                                    @endif

                                </div>

                                <p class="student-result-note text-body-secondary mb-0 mt-2">
                                    Resultado orientativo.
                                    No constituye un diagnóstico clínico.
                                </p>

                            </div>

                        @endif

                    </div>

                    {{-- Botón de evaluación --}}
                    <a href="{{ route('evaluaciones.aplicar', strtolower($item->instrumento->acronimo)) }}"
                       class="btn {{ $btnClass }} rounded-pill w-100 fw-bold shadow-sm py-2 mt-4">

                        {{ $item->estado === 'completada'
                            ? 'Responder de nuevo'
                            : 'Iniciar evaluación' }}

                    </a>

                </div>
            </div>

        @empty

            <div class="col-12 anime-item">
                <div class="alert alert-warning rounded-4 border-0 shadow-sm">
                    No hay otros instrumentos cargados o tu expediente
                    de estudiante no está completo.
                </div>
            </div>

        @endforelse

        {{-- ============================================ --}}
        {{-- DIARIO EMOCIONAL                            --}}
        {{-- ============================================ --}}

        <div class="col-12 mt-4 anime-item">

            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-4 mb-4 gap-3 border-secondary border-opacity-10">

                    <div class="d-flex align-items-center gap-3">

                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                             style="width:50px;height:50px;">

                            <i class="bi bi-journal-text fs-4"></i>

                        </div>

                        <div>
                            <h4 class="fw-black mb-1 text-body">
                                Diario Emocional
                            </h4>

                            <p class="text-body-secondary mb-0 small">
                                Escribe una entrada libre. Nuestra IA
                                te ayudará a entender tus emociones.
                            </p>
                        </div>

                    </div>

                    <span class="badge bg-body-tertiary text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">

                    <i class="bi bi-lock-fill me-1"></i>
                    100% Privado

                </span>

                </div>

                {{-- Última entrada --}}
                @if(isset($ultimaEntrada) && $ultimaEntrada)

                    <div class="alert bg-body-tertiary border border-secondary border-opacity-10 rounded-4 mb-4 shadow-sm">

                        <div class="fw-bold text-body mb-1">
                            Última entrada registrada
                        </div>

                        <div class="text-body-secondary small mb-2">
                            {{ $ultimaEntrada->created_at?->format('d/m/Y H:i') }}
                        </div>

                        <div class="text-body diario-texto">
                            {{ \Illuminate\Support\Str::limit($ultimaEntrada->texto_ingresado, 180) }}
                        </div>

                    </div>

                @endif

                {{-- Formulario de diario emocional --}}
                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">

                        <label class="form-label fw-bold text-body-secondary">
                            ¿Cómo te sientes el día de hoy?
                        </label>

                        <textarea name="texto_ingresado"
                                  rows="6"
                                  class="form-control form-control-lg bg-body-tertiary border-0 shadow-sm rounded-4 p-4 text-body"
                                  placeholder="Hoy me he sentido un poco abrumado por las clases, pero también emocionado por..."
                                  style="resize:none;"
                                  required></textarea>

                    </div>

                    <div class="d-flex justify-content-end">

                        <button type="submit"
                                class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">

                            <i class="bi bi-send-fill me-2"></i>
                            Guardar en mi Diario

                        </button>

                    </div>

                </form>

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

        });
    </script>
@endpush
