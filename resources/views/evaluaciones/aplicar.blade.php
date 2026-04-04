@extends('layouts.app')

@section('title', 'Aplicar evaluación - PROMETEO')
@section('page-title', 'Aplicar evaluación')
@section('page-subtitle', 'Responde todas las preguntas del instrumento')

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
            content: '\F4F7'; font-family: "bootstrap-icons"; position: absolute;
            top: -10%; right: -4%; font-size: 14rem; color: inherit;
            opacity: 0.1; transform: rotate(-12deg); pointer-events: none;
        }

        /* ESTILOS DEL WIZARD (Paso a Paso) */
        .wizard-container {
            position: relative;
            min-height: 400px;
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

        /* Barra de Progreso */
        .progress-bar-container {
            height: 8px;
            background: rgba(124, 58, 237, 0.1);
            border-radius: 4px;
            overflow: hidden;
            margin-bottom: 2rem;
        }
        .progress-bar-fill {
            height: 100%;
            background: var(--app-primary);
            width: 0%;
            transition: width 0.4s ease-in-out;
        }

        .question-card { border: 1px solid rgba(148, 163, 184, 0.16); }

        .option-tile {
            border: 2px solid var(--app-border);
            border-radius: 18px;
            padding: 1.25rem 1rem;
            cursor: pointer;
            transition: all .2s ease;
            background: var(--app-surface);
            min-height: 100%;
            text-align: center;
        }
        body.theme-dark .option-tile, body.theme-system .option-tile { background: rgba(0,0,0,0.2); border-color: rgba(255,255,255,0.1); }

        .option-tile:hover {
            border-color: rgba(124, 58, 237, 0.4);
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        }

        .option-tile.active {
            border-color: var(--app-primary);
            background: var(--app-primary-soft);
            box-shadow: 0 10px 24px rgba(124, 58, 237, 0.15);
            transform: scale(1.02);
        }
        body.theme-dark .option-tile.active, body.theme-system .option-tile.active { color: var(--app-primary-dark); }

        .option-input { display: none; }

        .question-number {
            width: 50px; height: 50px; border-radius: 14px;
            display: inline-flex; align-items: center; justify-content: center;
            background: var(--app-primary); color: white; font-weight: 900; font-size: 1.2rem;
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
            <div class="app-card eval-hero p-4 p-md-5 rounded-4 border-0 shadow-lg {{ $bannerClasses }}">
                <div class="row align-items-center position-relative z-1">
                    <div class="col-lg-8">
                        <span class="badge bg-body text-body border border-secondary border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-clipboard2-check-fill me-1 text-primary"></i> Evaluación en progreso
                        </span>
                        <h2 class="fw-black mb-2" style="font-size: 2.15rem;">Cuestionario {{ strtoupper($instrumento->acronimo) }}</h2>
                        <p class="mb-3 opacity-75 fs-5">{{ $descripcion }}</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm" onclick="return confirm('¿Seguro que deseas salir? Perderás el progreso de esta evaluación.');">
                            <i class="bi bi-x-circle me-1"></i> Cancelar Evaluación
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4">

                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold text-body m-0">Progreso</h6>
                    <span class="badge bg-primary rounded-pill"><span id="progress-text">1</span> / {{ $totalPreguntas }}</span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" id="progress-bar"></div>
                </div>

                <form action="{{ route('evaluaciones.responder', strtolower($instrumento->acronimo)) }}" method="POST" id="form-evaluacion">
                    @csrf

                    <div class="wizard-container" id="wizard-container">
                        @foreach($preguntas as $numero => $pregunta)
                            <div class="question-step {{ $loop->first ? 'active' : '' }}" data-step="{{ $loop->iteration }}">
                                <div class="app-card question-card p-4 p-md-5 border-0 shadow-sm rounded-4 text-center">

                                    <div class="d-flex flex-column align-items-center mb-5">
                                        <span class="question-number mb-3 shadow-sm">{{ $numero }}</span>
                                        <h4 class="fw-black text-body" style="line-height: 1.4;">{{ $pregunta }}</h4>
                                    </div>

                                    <div class="row justify-content-center g-3">
                                        @foreach([
                                            0 => 'Ningún día',
                                            1 => 'Varios días',
                                            2 => 'Más de la mitad de los días',
                                            3 => 'Casi todos los días'
                                        ] as $valor => $texto)
                                            <div class="col-sm-6 col-lg-3">
                                                <label class="option-tile w-100" for="p{{ $numero }}_{{ $valor }}">
                                                    <input class="option-input step-radio"
                                                           type="radio"
                                                           name="respuestas[{{ $numero }}]"
                                                           id="p{{ $numero }}_{{ $valor }}"
                                                           value="{{ $valor }}"
                                                           data-step="{{ $loop->parent->iteration }}">

                                                    <div class="display-6 fw-black mb-2 text-body">{{ $valor }}</div>
                                                    <div class="small fw-bold text-body-secondary">{{ $texto }}</div>
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>

                                </div>

                                <div class="d-flex justify-content-between mt-4">
                                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold btn-prev" {{ $loop->first ? 'disabled' : '' }}>
                                        <i class="bi bi-arrow-left me-1"></i> Anterior
                                    </button>
                                    @if($loop->last)
                                        <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-none" id="btn-submit">
                                            Enviar Evaluación <i class="bi bi-send-check-fill ms-1"></i>
                                        </button>
                                    @else
                                        <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold btn-next" style="opacity: 0.5; pointer-events: none;">
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
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Animación de entrada inicial
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 1000 });

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
                const translateOut = direction === 'forward' ? -50 : 50;
                const translateIn = direction === 'forward' ? 50 : -50;

                // Animación de salida
                anime({
                    targets: fromEl,
                    translateX: [0, translateOut],
                    opacity: [1, 0],
                    duration: 400,
                    easing: 'easeInQuad',
                    complete: function() {
                        fromEl.classList.remove('active');
                        toEl.classList.add('active');

                        // Animación de entrada
                        anime({
                            targets: toEl,
                            translateX: [translateIn, 0],
                            opacity: [0, 1],
                            duration: 500,
                            easing: 'easeOutExpo'
                        });
                    }
                });

                currentStep = toStep;
                updateProgress();
            }

            // Manejo de selección de opciones (SOLO ESTILO, NO AUTO-AVANCE)
            document.querySelectorAll('.step-radio').forEach(radio => {
                radio.addEventListener('change', function() {
                    const step = parseInt(this.getAttribute('data-step'));

                    // Estilo visual de la opción seleccionada
                    const allTiles = this.closest('.row').querySelectorAll('.option-tile');
                    allTiles.forEach(t => t.classList.remove('active'));
                    this.closest('.option-tile').classList.add('active');

                    // Habilitar botón "Siguiente" manual para esta pregunta
                    const nextBtn = this.closest('.question-step').querySelector('.btn-next');
                    if(nextBtn) {
                        nextBtn.style.opacity = '1';
                        nextBtn.style.pointerEvents = 'auto';
                        // Pequeño rebote para indicar que ya puede avanzar
                        anime({ targets: nextBtn, scale: [0.95, 1], duration: 400, easing: 'easeOutQuad' });
                    }

                    // Si es la última pregunta, mostramos el botón de enviar
                    if (step === totalSteps) {
                        submitBtn.classList.remove('d-none');
                        anime({ targets: submitBtn, scale: [0.9, 1], duration: 800, easing: 'easeOutElastic(1, .6)' });
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
        });
    </script>
@endpush
