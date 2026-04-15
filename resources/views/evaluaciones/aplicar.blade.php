@extends('layouts.app')

@section('title', 'Aplicar evaluación - PROMETEO')
@section('page-title', 'Aplicar evaluación')
@section('page-subtitle', 'Responde todas las preguntas del instrumento')

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

        /* --- HERO BANNER --- */
        .eval-hero {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary);
        }
        .eval-hero::after {
            content: '\F4F7'; font-family: "bootstrap-icons"; position: absolute;
            top: -10%; right: -4%; font-size: 14rem; color: #ffffff;
            opacity: 0.08; transform: rotate(-12deg); pointer-events: none;
            z-index: 2;
        }

        #granim-canvas-eval-show {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .banner-content { position: relative; z-index: 3; }

        .glass-badge {
            background-color: rgba(255, 255, 255, 0.2) !important;
            color: #ffffff !important;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .glass-btn {
            background-color: rgba(255, 255, 255, 0.95) !important;
            color: var(--app-primary-dark) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.5);
            transition: all 0.3s ease;
        }
        .glass-btn:hover {
            background-color: #ffffff !important;
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15) !important;
        }

        /* --- WIZARD (Paso a Paso) --- */
        .wizard-container {
            position: relative;
            min-height: 500px;
            overflow: hidden;
        }

        .question-step {
            position: absolute;
            top: 0; left: 0; width: 100%;
            visibility: hidden;
            opacity: 0;
            pointer-events: none;
        }

        .question-step.active {
            position: relative;
            visibility: visible;
            opacity: 1;
            pointer-events: auto;
        }

        /* --- BARRA DE PROGRESO --- */
        .progress-bar-container {
            height: 10px;
            background: rgba(124, 58, 237, 0.1);
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 2.5rem;
        }
        .progress-bar-fill {
            height: 100%;
            background: var(--app-primary);
            width: 0%;
            transition: width 0.5s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .question-card {
            border: 1px solid rgba(148, 163, 184, 0.16);
        }

        /* --- DISEÑO MEJORADO DE PREGUNTAS Y REACTIVOS --- */
        .question-number {
            width: 60px; height: 60px; border-radius: 18px;
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--app-primary); color: white;
            font-weight: 900; font-size: 1.5rem;
            box-shadow: 0 8px 20px rgba(124, 58, 237, 0.3);
        }

        .question-text {
            font-size: clamp(1.5rem, 3vw, 2.2rem);
            line-height: 1.35;
            letter-spacing: -0.5px;
            color: var(--app-text);
        }

        /* Cajas de opciones (Tiles) */
        .option-tile {
            border: 2px solid rgba(148, 163, 184, 0.25);
            border-radius: 24px;
            padding: 2rem 1rem;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            background: var(--app-surface);
            min-height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .reactivo-valor {
            font-size: 3.5rem;
            font-weight: 900;
            line-height: 1;
            margin-bottom: 0.75rem;
            color: var(--app-primary);
            transition: transform 0.3s ease;
        }

        .reactivo-texto {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--app-muted);
            transition: color 0.3s ease;
        }

        /* Efectos Hover */
        .option-tile:hover {
            border-color: var(--app-primary);
            transform: translateY(-4px);
            box-shadow: 0 12px 25px rgba(0,0,0,0.08);
            background-color: var(--app-primary-soft);
        }
        .option-tile:hover .reactivo-valor {
            transform: scale(1.1);
        }

        /* Efecto Seleccionado (Active) */
        .option-tile.active {
            border-width: 3px;
            border-color: var(--app-primary);
            background: var(--app-primary-soft);
            box-shadow: 0 12px 30px rgba(124, 58, 237, 0.2);
            transform: scale(1.03);
        }
        .option-tile.active .reactivo-valor {
            color: var(--app-primary-dark);
            transform: scale(1.15);
        }
        .option-tile.active .reactivo-texto {
            color: var(--app-primary-dark);
        }

        /* Input oculto */
        .option-input { display: none; }

        /* Ajustes Modo Oscuro */
        body.theme-dark .question-text, body.theme-system .question-text { color: #f8fafc; }
        body.theme-dark .option-tile, body.theme-system .option-tile {
            background: rgba(255,255,255,0.03);
            border-color: rgba(255,255,255,0.1);
        }
        body.theme-dark .reactivo-texto, body.theme-system .reactivo-texto { color: #cbd5e1; }

        body.theme-dark .option-tile:hover, body.theme-system .option-tile:hover,
        body.theme-dark .option-tile.active, body.theme-system .option-tile.active {
            background: rgba(255,255,255,0.1);
            border-color: var(--app-primary);
        }
        body.theme-dark .option-tile.active .reactivo-texto, body.theme-system .option-tile.active .reactivo-texto {
            color: #ffffff;
        }

    </style>
@endpush

@section('content')
    @php
        $totalPreguntas = count($preguntas);
        $descripcion = strtoupper($instrumento->acronimo) === 'PHQ9'
            ? 'Responde según cómo te has sentido durante las últimas 2 semanas.'
            : 'Responde según cómo te has sentido durante las últimas 2 semanas.';
    @endphp

    <div class="row g-4">

        <div class="col-12 anime-item">
            <div class="app-card eval-hero p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-eval-show"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge glass-badge rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-clipboard2-check-fill me-1"></i> Evaluación en progreso
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.15rem; text-shadow: 0 2px 4px rgba(0,0,0,0.2);">
                            Cuestionario {{ strtoupper($instrumento->acronimo) }}
                        </h2>
                        <p class="mb-3 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.2);">{{ $descripcion }}</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}" class="btn glass-btn rounded-pill fw-bold px-4 py-2 shadow-sm btn-cancelar-eval">
                            <i class="bi bi-x-circle me-1 text-danger"></i> Cancelar Evaluación
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card bg-body-tertiary p-4 p-md-5 border border-secondary border-opacity-10 shadow-sm rounded-4">

                <div class="d-flex justify-content-between align-items-end mb-2">
                    <h6 class="fw-black text-body-secondary text-uppercase m-0" style="letter-spacing: 1px; font-size: 0.85rem;">Progreso del test</h6>
                    <span class="badge bg-primary rounded-pill fs-6 px-3 shadow-sm"><span id="progress-text">1</span> de {{ $totalPreguntas }}</span>
                </div>
                <div class="progress-bar-container shadow-sm">
                    <div class="progress-bar-fill" id="progress-bar"></div>
                </div>

                <form action="{{ route('evaluaciones.responder', strtolower($instrumento->acronimo)) }}" method="POST" id="form-evaluacion">
                    @csrf

                    <div class="wizard-container" id="wizard-container">
                        @foreach($preguntas as $numero => $pregunta)
                            <div class="question-step {{ $loop->first ? 'active' : '' }}" data-step="{{ $loop->iteration }}">

                                <div class="app-card question-card p-4 p-md-5 border-0 shadow-sm rounded-4">

                                    <div class="d-flex flex-column align-items-center text-center mb-5">
                                        <span class="question-number mb-4">{{ $numero }}</span>
                                        <h2 class="fw-black question-text">{{ $pregunta }}</h2>
                                    </div>

                                    <div class="row justify-content-center g-3 g-md-4">
                                        @foreach([
                                            0 => 'Ningún día',
                                            1 => 'Varios días',
                                            2 => 'Más de la mitad de los días',
                                            3 => 'Casi todos los días'
                                        ] as $valor => $texto)
                                            <div class="col-6 col-lg-3">
                                                <label class="option-tile w-100" for="p{{ $numero }}_{{ $valor }}">
                                                    <input class="option-input step-radio"
                                                           type="radio"
                                                           name="respuestas[{{ $numero }}]"
                                                           id="p{{ $numero }}_{{ $valor }}"
                                                           value="{{ $valor }}"
                                                           data-step="{{ $loop->parent->iteration }}">

                                                    <div class="reactivo-valor">{{ $valor }}</div>
                                                    <div class="reactivo-texto">{{ $texto }}</div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>

                                <div class="d-flex flex-column flex-sm-row justify-content-between gap-3 mt-4">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold btn-prev" style="min-width: 140px;" {{ $loop->first ? 'disabled' : '' }}>
                                        <i class="bi bi-arrow-left me-1"></i> Anterior
                                    </button>
                                    @if($loop->last)
                                        <button type="submit" class="btn btn-success rounded-pill px-5 py-2 fw-black shadow-sm d-none fs-5" id="btn-submit" style="min-width: 200px;">
                                            Enviar Evaluación <i class="bi bi-send-check-fill ms-2"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold btn-next" style="min-width: 140px; opacity: 0.4; pointer-events: none;">
                                            Siguiente <i class="bi bi-arrow-right ms-1"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>

                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/granim.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animación de entrada inicial
            if(typeof anime !== 'undefined') {
                anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 1000 });
            }

            // Inicialización de Granim para el Banner
            if (document.getElementById('granim-canvas-eval-show') && typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-eval-show',
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

            // Lógica del Wizard (Paso a Paso)
            const totalSteps = {{ $totalPreguntas }};
            let currentStep = 1;

            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');
            const submitBtn = document.getElementById('btn-submit');

            function updateProgress() {
                const percentage = ((currentStep - 1) / totalSteps) * 100;
                progressBar.style.width = percentage + '%';
                progressText.textContent = currentStep;

                if(currentStep > totalSteps) {
                    progressBar.style.width = '100%';
                    progressText.textContent = totalSteps;
                }
            }

            function goToStep(fromStep, toStep, direction = 'forward') {
                const fromEl = document.querySelector(`.question-step[data-step="${fromStep}"]`);
                const toEl = document.querySelector(`.question-step[data-step="${toStep}"]`);

                if (!fromEl || !toEl) return;

                // Dirección de la animación
                const translateOut = direction === 'forward' ? -80 : 80;
                const translateIn = direction === 'forward' ? 80 : -80;

                // Animación de salida más rápida
                anime({
                    targets: fromEl,
                    translateX: [0, translateOut],
                    opacity: [1, 0],
                    duration: 350,
                    easing: 'easeInQuad',
                    complete: function() {
                        fromEl.classList.remove('active');
                        toEl.classList.add('active');

                        // Animación de entrada con un leve rebote
                        anime({
                            targets: toEl,
                            translateX: [translateIn, 0],
                            opacity: [0, 1],
                            duration: 600,
                            easing: 'easeOutElastic(1, .8)'
                        });
                    }
                });

                currentStep = toStep;
                updateProgress();
            }

            // Manejo de selección de opciones
            document.querySelectorAll('.step-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const step = parseInt(this.getAttribute('data-step'));

                    // Estilo visual de la opción seleccionada
                    const allTiles = this.closest('.row').querySelectorAll('.option-tile');
                    allTiles.forEach(t => t.classList.remove('active'));
                    this.closest('.option-tile').classList.add('active');

                    // Pequeña animación a la tarjeta de la pregunta al responder
                    anime({
                        targets: this.closest('.question-card'),
                        scale: [1.02, 1],
                        duration: 400,
                        easing: 'easeOutExpo'
                    });

                    // Habilitar botón "Siguiente" manual para esta pregunta
                    const nextBtn = this.closest('.question-step').querySelector('.btn-next');
                    if(nextBtn) {
                        nextBtn.style.opacity = '1';
                        nextBtn.style.pointerEvents = 'auto';
                        // Destello para indicar que ya puede avanzar
                        anime({ targets: nextBtn, scale: [0.9, 1.05, 1], duration: 500, easing: 'easeOutQuad' });
                    }

                    // Si es la última pregunta, mostramos el botón de enviar
                    if (step === totalSteps) {
                        submitBtn.classList.remove('d-none');
                        anime({ targets: submitBtn, scale: [0.8, 1.1, 1], opacity: [0, 1], duration: 800, easing: 'easeOutElastic(1, .6)' });
                    }
                });
            });

            // Botones manuales Anterior
            document.querySelectorAll('.btn-prev').forEach(btn => {
                btn.addEventListener('click', function() {
                    const stepEl = this.closest('.question-step');
                    const step = parseInt(stepEl.getAttribute('data-step'));
                    if (step > 1) {
                        goToStep(step, step - 1, 'backward');
                    }
                });
            });

            // Botones manuales Siguiente
            document.querySelectorAll('.btn-next').forEach(btn => {
                btn.addEventListener('click', function() {
                    const stepEl = this.closest('.question-step');
                    const step = parseInt(stepEl.getAttribute('data-step'));
                    if (step < totalSteps) {
                        goToStep(step, step + 1, 'forward');
                    }
                });
            });

            // Inicializar progreso
            updateProgress();

            // --- ALERTA MODERNA DE SWEETALERT2 PARA CANCELAR EVALUACIÓN ---
            document.querySelectorAll('.btn-cancelar-eval').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    const urlDestino = this.getAttribute('href');

                    const isDark = document.body.classList.contains('theme-dark') || document.body.classList.contains('theme-system') && window.matchMedia('(prefers-color-scheme: dark)').matches;

                    Swal.fire({
                        title: '¿Abandonar evaluación?',
                        text: "Perderás todo el progreso actual de este cuestionario.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: '<i class="bi bi-box-arrow-right me-1"></i> Sí, salir',
                        cancelButtonText: 'Continuar respondiendo',
                        background: isDark ? '#1e293b' : '#ffffff',
                        color: isDark ? '#f8fafc' : '#1e293b',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border border-secondary border-opacity-10',
                            confirmButton: 'btn btn-danger rounded-pill px-4 fw-bold shadow-sm',
                            cancelButton: 'btn rounded-pill px-4 fw-bold ms-2 ' + (isDark ? 'btn-outline-light' : 'btn-outline-secondary')
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = urlDestino;
                        }
                    });
                });
            });
        });
    </script>
@endpush
