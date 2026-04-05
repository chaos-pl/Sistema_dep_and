@extends('layouts.app')

@section('title', 'Dashboard Estudiante - PROMETEO')
@section('page-title', 'Inicio del Estudiante')
@section('page-subtitle', 'Evaluaciones pendientes y registro emocional')

@php
    // Obtenemos el color elegido por el usuario
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    // Paletas complejas de Granim adaptadas al sistema
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
        .hover-elevate { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease; border: 1px solid transparent; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; border-color: var(--app-primary-soft) !important; }

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
            color: white;
            opacity: 0.05;
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 2;
        }

        #granim-canvas-student {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .banner-content {
            position: relative;
            z-index: 3;
        }

        .anime-item { opacity: 0; transform: translateY(20px); }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-student p-4 p-md-5 text-white rounded-4 border-0 shadow-lg">

                <canvas id="granim-canvas-student"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm" style="color: var(--app-primary) !important;">
                            <i class="bi bi-shield-check me-1"></i> Entorno Seguro y Confidencial
                        </span>

                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                            Hola, {{ auth()->user()->name }}
                        </h2>

                        <p class="mb-0 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            @if($estudiante)
                                Matrícula: {{ $estudiante->matricula }} · Grupo: {{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}
                            @else
                                Tu cuenta aún no tiene expediente de estudiante vinculado.
                            @endif
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm hover-elevate" style="color: var(--app-primary) !important;">
                            <i class="bi bi-clipboard2-check-fill me-2"></i>Ver Evaluaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($estudiante)
            <div class="col-md-4 anime-item">
                <div class="app-card p-4 border-0 shadow-sm rounded-4 bg-body-tertiary h-100 hover-elevate">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 52px; height: 52px;">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-black text-body mb-0">{{ $totalCompletadas }}</h5>
                            <small class="text-body-secondary">Evaluaciones completadas</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 anime-item">
                <div class="app-card p-4 border-0 shadow-sm rounded-4 bg-body-tertiary h-100 hover-elevate">
                    <h5 class="fw-black text-body mb-3">Resumen académico</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-body-secondary fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Código anónimo</small>
                            <span class="fw-bold text-body fs-5">{{ $estudiante->codigo_anonimo }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-body-secondary fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Matrícula</small>
                            <span class="fw-bold text-body fs-5">{{ $estudiante->matricula }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-body-secondary fw-bold text-uppercase d-block" style="letter-spacing: 0.5px;">Grupo</small>
                            <span class="fw-bold text-body fs-5">{{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @forelse($instrumentosDashboard as $item)
            @php
                $acronimo = strtoupper($item->instrumento->acronimo);
                $esPhq = str_contains($acronimo, 'PHQ');
                $btnClass = $esPhq ? 'btn-primary' : 'btn-info text-white';
                $iconBg = $esPhq ? 'bg-primary bg-opacity-10 text-primary' : 'bg-info bg-opacity-10 text-info';
                $icon = $esPhq ? 'bi-cloud-rain-fill' : 'bi-lightning-fill';

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
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 fw-bold shadow-sm mb-2">
                                    {{ ucfirst(str_replace('_', ' ', $item->estado)) }}
                                </span>
                                <h4 class="fw-black text-body mb-0">{{ strtoupper($item->instrumento->acronimo) }}</h4>
                            </div>

                            <div class="{{ $iconBg }} rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                                <i class="bi {{ $icon }} fs-3"></i>
                            </div>
                        </div>

                        <p class="text-body-secondary fw-medium mb-3">{{ $item->instrumento->nombre }}</p>

                        @if($item->evaluacion && $item->puntaje_total !== null)
                            <p class="mb-0 text-body">
                                <span class="fw-bold">Puntaje:</span> {{ $item->puntaje_total }}
                                ·
                                <span class="fw-bold">Riesgo:</span> {{ ucfirst($item->nivel_riesgo) }}
                            </p>
                        @endif
                    </div>

                    <a href="{{ route('evaluaciones.aplicar', strtolower($item->instrumento->acronimo)) }}"
                       class="btn {{ $btnClass }} rounded-pill w-100 fw-bold shadow-sm py-2 mt-4">
                        {{ $item->estado === 'completada' ? 'Responder de nuevo' : 'Iniciar evaluación' }}
                    </a>
                </div>
            </div>
        @empty
            <div class="col-12 anime-item">
                <div class="alert alert-warning rounded-4 border-0 shadow-sm">
                    No hay instrumentos cargados o tu expediente de estudiante no está completo.
                </div>
            </div>
        @endforelse

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-4 mb-4 gap-3 border-secondary border-opacity-10">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                            <i class="bi bi-journal-text fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-1 text-body">Diario Emocional</h4>
                            <p class="text-body-secondary mb-0 small">Escribe una entrada libre. Nuestra IA te ayudará a entender tus emociones.</p>
                        </div>
                    </div>
                    <span class="badge bg-body-tertiary text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-lock-fill me-1"></i> 100% Privado
                    </span>
                </div>

                @if($ultimaEntrada)
                    <div class="alert bg-body-tertiary border border-secondary border-opacity-10 rounded-4 mb-4 shadow-sm">
                        <div class="fw-bold text-body mb-1">Última entrada registrada</div>
                        <div class="text-body-secondary small mb-2">{{ $ultimaEntrada->created_at?->format('d/m/Y H:i') }}</div>
                        <div class="text-body">{{ \Illuminate\Support\Str::limit($ultimaEntrada->texto_ingresado, 180) }}</div>
                    </div>
                @endif

                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold text-body-secondary">¿Cómo te sientes el día de hoy?</label>
                        <textarea name="texto_ingresado" rows="6" class="form-control form-control-lg bg-body-tertiary border-0 shadow-sm rounded-4 p-4 text-body" placeholder="Hoy me he sentido un poco abrumado por las clases, pero también emocionado por..." style="resize: none;"></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                            <i class="bi bi-send-fill me-2"></i>Guardar en mi Diario
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
            // Animación de entrada
            if(typeof anime !== 'undefined') {
                anime({
                    targets: '.anime-item',
                    translateY: [30, 0],
                    opacity: [0, 1],
                    delay: anime.stagger(150),
                    easing: 'easeOutExpo',
                    duration: 1000
                });
            }

            // Granim.js para el Banner
            if (document.getElementById('granim-canvas-student') && typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-student',
                    direction: 'left-right',
                    isPausedWhenNotInView: true,
                    states : {
                        "default-state": {
                            gradients: [
                                {!! $granimPalettes !!}
                            ],
                            transitionSpeed: 7000
                        }
                    }
                });
            }
        });
    </script>
@endpush
