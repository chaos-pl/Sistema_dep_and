@extends('layouts.app')

@section('title', 'Detalle Análisis NLP - PROMETEO')
@section('page-title', 'Detalle del Análisis NLP')
@section('page-subtitle', 'Revisión del diario emocional detectado por el modelo BETO')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
    </style>
@endpush

@section('content')
    @php
        $estudiante = $analisisNlp->estudiante;
        $persona = $estudiante?->persona;
        $confianza = (float) $analisisNlp->score_confianza;
    @endphp

    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 bg-body-tertiary">
                <div class="d-flex flex-wrap justify-content-between gap-4 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:60px;height:60px;">
                            <i class="bi bi-journal-medical fs-3"></i>
                        </div>

                        <div>
                            <p class="text-body-secondary mb-1 fw-bold text-uppercase" style="font-size:.75rem; letter-spacing:1px;">
                                Expediente estudiantil
                            </p>

                            <h3 class="fw-black mb-1 text-body">
                                {{ $persona?->nombre ?? 'Sin nombre' }}
                                {{ $persona?->apellido_paterno ?? '' }}
                                {{ $persona?->apellido_materno ?? '' }}
                            </h3>

                            <div class="d-flex align-items-center gap-2 text-body-secondary fw-medium">
                                <i class="bi bi-person-vcard text-primary"></i>
                                Código anónimo:
                                <strong class="text-body">{{ $analisisNlp->codigo_anonimo }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-4 py-2 shadow-sm">
                            {{ str_replace('_', ' ', $analisisNlp->etiqueta_roberta) }}
                        </span>

                        <span class="badge bg-body text-body border border-secondary border-opacity-25 rounded-pill px-4 py-2 shadow-sm">
                            Confianza: <strong>{{ number_format($confianza * 100, 2) }}%</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-7 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                        <i class="bi bi-chat-left-text-fill fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Texto ingresado por el estudiante</h4>
                        <p class="text-body-secondary mb-0 small">Contenido registrado en el diario emocional.</p>
                    </div>
                </div>

                <div class="p-4 bg-body-tertiary border border-secondary border-opacity-10 rounded-4 text-body" style="min-height:220px; line-height:1.8;">
                    {{ $analisisNlp->texto_ingresado }}
                </div>

                <div class="mt-4 text-body-secondary small">
                    <i class="bi bi-clock-history me-1"></i>
                    Registrado el {{ $analisisNlp->created_at?->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>

        <div class="col-lg-5 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-3 mb-4">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:45px;height:45px;">
                        <i class="bi bi-cpu-fill fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Resultado del modelo</h4>
                        <p class="text-body-secondary mb-0 small">Salida generada por BETO + XGBoost.</p>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-body-secondary text-uppercase" style="font-size:.75rem; letter-spacing:.5px;">
                        Etiqueta
                    </label>
                    <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 fw-bold text-danger">
                        {{ str_replace('_', ' ', $analisisNlp->etiqueta_roberta) }}
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-body-secondary text-uppercase" style="font-size:.75rem; letter-spacing:.5px;">
                        Confianza
                    </label>
                    <div class="p-3 bg-body-tertiary border border-secondary border-opacity-10 rounded-4 text-body fw-black fs-4">
                        {{ number_format($confianza * 100, 2) }}%
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-bold text-body-secondary text-uppercase" style="font-size:.75rem; letter-spacing:.5px;">
                        Atención prioritaria
                    </label>
                    <div class="p-3 rounded-4 fw-bold {{ $analisisNlp->requiere_atencion ? 'bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25' : 'bg-success bg-opacity-10 text-success border border-success border-opacity-25' }}">
                        <i class="bi {{ $analisisNlp->requiere_atencion ? 'bi-exclamation-triangle-fill' : 'bi-check-circle-fill' }} me-2"></i>
                        {{ $analisisNlp->requiere_atencion ? 'Sí requiere revisión profesional' : 'No requiere atención prioritaria' }}
                    </div>
                </div>

                <div class="alert alert-info border-0 rounded-4 shadow-sm small mb-4">
                    <i class="bi bi-info-circle-fill me-1"></i>
                    Este resultado es un apoyo de tamizaje. No sustituye la valoración clínica del psicólogo.
                </div>

                <div class="d-flex justify-content-between align-items-center pt-4 border-top border-secondary border-opacity-10">
                    <a href="{{ route('analisis.index') }}" class="btn btn-light border text-body-secondary rounded-pill fw-bold px-4 shadow-sm">
                        <i class="bi bi-arrow-left me-1"></i> Volver
                    </a>

                    <a href="{{ route('diagnosticos.index') }}" class="btn btn-primary rounded-pill fw-bold px-4 shadow-sm">
                        Ir a diagnósticos <i class="bi bi-arrow-right-circle ms-1"></i>
                    </a>
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
                    delay: anime.stagger(130),
                    easing: 'easeOutExpo',
                    duration: 900
                });
            }
        });
    </script>
@endpush
