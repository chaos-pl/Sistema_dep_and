@extends('layouts.app')

@section('title', 'Diario Emocional - PROMETEO')
@section('page-title', 'Diario Emocional')
@section('page-subtitle', 'Registro privado de tu estado emocional')

@push('styles')
    <style>
        .anime-item {
            opacity: 0;
            transform: translateY(20px);
        }

        .diario-card {
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .diario-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 24px rgba(0, 0, 0, .08) !important;
        }

        .nlp-badge-success {
            background-color: rgba(25, 135, 84, .12) !important;
            color: #198754 !important;
            border: 1px solid rgba(25, 135, 84, .35) !important;
        }

        .nlp-badge-danger {
            background-color: rgba(220, 53, 69, .12) !important;
            color: #dc3545 !important;
            border: 1px solid rgba(220, 53, 69, .35) !important;
        }

        .nlp-badge-warning {
            background-color: rgba(255, 193, 7, .18) !important;
            color: #997404 !important;
            border: 1px solid rgba(255, 193, 7, .45) !important;
        }

        .nlp-badge-neutral {
            background-color: rgba(108, 117, 125, .12) !important;
            color: #6c757d !important;
            border: 1px solid rgba(108, 117, 125, .35) !important;
        }

        body.theme-dark .nlp-badge-success,
        body.theme-system .nlp-badge-success {
            color: #75b798 !important;
        }

        body.theme-dark .nlp-badge-danger,
        body.theme-system .nlp-badge-danger {
            color: #ea868f !important;
        }

        body.theme-dark .nlp-badge-warning,
        body.theme-system .nlp-badge-warning {
            color: #ffda6a !important;
        }

        .diario-texto {
            white-space: pre-line;
            line-height: 1.7;
        }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-4">
                    <div>
                        <h3 class="fw-black mb-1 text-body">Nuevo registro emocional</h3>
                        <p class="text-body-secondary mb-0">
                            Escribe cómo te sientes. Este espacio está vinculado a tu código anónimo:
                            <span class="fw-bold text-body">{{ $estudiante->codigo_anonimo }}</span>
                        </p>
                    </div>

                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-lock-fill me-1"></i> 100% Privado
                    </span>
                </div>

                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="texto_ingresado" class="form-label fw-bold text-body-secondary">
                            ¿Cómo te sientes hoy?
                        </label>

                        <textarea
                            name="texto_ingresado"
                            id="texto_ingresado"
                            rows="6"
                            class="form-control form-control-lg bg-body-tertiary border border-secondary border-opacity-10 shadow-sm rounded-4 p-4 @error('texto_ingresado') is-invalid @enderror"
                            placeholder="Hoy me he sentido..."
                            style="resize: none;"
                        >{{ old('texto_ingresado') }}</textarea>

                        @error('texto_ingresado')
                        <small class="text-danger fw-bold mt-2 d-block">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </small>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                            <i class="bi bi-send-fill me-2"></i>Guardar entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div>
                        <h4 class="fw-black mb-1 text-body">Historial de entradas</h4>
                        <p class="text-body-secondary mb-0 small">Solo puedes ver tus propios registros.</p>
                    </div>

                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold">
                        {{ $entradas->total() }} registros
                    </span>
                </div>

                @forelse($entradas as $entrada)
                    @php
                        $etiquetaOriginal = $entrada->etiqueta_roberta ?? 'pendiente';
                        $etiqueta = strtolower(trim($etiquetaOriginal));

                        $etiquetaTexto = match($etiqueta) {
                            'sin_riesgo' => 'SIN RIESGO',
                            'riesgo_depresivo' => 'RIESGO DEPRESIVO',
                            'pendiente' => 'PENDIENTE',
                            default => strtoupper(str_replace('_', ' ', $etiquetaOriginal)),
                        };

                        $etiquetaClase = match($etiqueta) {
                            'sin_riesgo' => 'nlp-badge-success',
                            'riesgo_depresivo' => 'nlp-badge-danger',
                            'pendiente' => 'nlp-badge-warning',
                            default => 'nlp-badge-neutral',
                        };

                        $estadoTexto = match(true) {
                            $etiqueta === 'pendiente' => 'Análisis pendiente',
                            $entrada->requiere_atencion => 'Requiere atención',
                            default => 'Sin atención inmediata',
                        };

                        $estadoClase = match(true) {
                            $etiqueta === 'pendiente' => 'nlp-badge-warning',
                            $entrada->requiere_atencion => 'nlp-badge-danger',
                            default => 'nlp-badge-success',
                        };

                        $estadoIcono = match(true) {
                            $etiqueta === 'pendiente' => 'bi-hourglass-split',
                            $entrada->requiere_atencion => 'bi-exclamation-triangle-fill',
                            default => 'bi-check-circle-fill',
                        };
                    @endphp

                    <div class="diario-card bg-body-tertiary rounded-4 p-4 mb-3 shadow-sm border border-secondary border-opacity-10">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                            <div class="fw-bold text-body">
                                <i class="bi bi-calendar-event me-2 text-primary"></i>
                                {{ $entrada->created_at?->format('d/m/Y H:i') }}
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm {{ $etiquetaClase }}">
                                    <i class="bi bi-cpu-fill me-1"></i>
                                    Etiqueta: {{ $etiquetaTexto }}
                                </span>

                                <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm {{ $estadoClase }}">
                                    <i class="bi {{ $estadoIcono }} me-1"></i>
                                    {{ $estadoTexto }}
                                </span>
                            </div>
                        </div>

                        @if($etiqueta === 'pendiente')
                            <div class="alert alert-warning bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 py-2 px-3 small fw-semibold mb-3">
                                <i class="bi bi-info-circle-fill me-1"></i>
                                Esta entrada fue guardada, pero aún no ha sido procesada por la IA.
                            </div>
                        @endif

                        <p class="text-body-secondary diario-texto mb-0">{{ $entrada->texto_ingresado }}</p>
                    </div>
                @empty
                    <div class="alert alert-light border rounded-4 mb-0 text-body-secondary">
                        Aún no has registrado entradas en tu diario emocional.
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $entradas->links() }}
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
                    delay: anime.stagger(120),
                    easing: 'easeOutExpo',
                    duration: 900
                });
            }
        });
    </script>
@endpush
