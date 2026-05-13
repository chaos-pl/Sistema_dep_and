@extends('layouts.app')

@section('title', 'Análisis NLP - PROMETEO')
@section('page-title', 'Análisis NLP')
@section('page-subtitle', 'Diarios emocionales detectados con posible riesgo por el modelo BETO')

@push('styles')
    <style>
        .anime-item {
            opacity: 0;
            transform: translateY(20px);
        }

        .hover-elevate {
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .hover-elevate:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.08) !important;
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
            background-color: rgba(255, 193, 7, .20) !important;
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
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-md-6 col-xl anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size:.8rem; letter-spacing:.5px;">
                        Requieren Atención
                    </h6>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalRiesgo ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Diarios marcados por el modelo NLP.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size:.8rem; letter-spacing:.5px;">
                        Total Analizados
                    </h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-cpu-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalAnalisis ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Entradas procesadas por la IA.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size:.8rem; letter-spacing:.5px;">
                        Sin Riesgo
                    </h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-shield-check fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalSinRiesgo ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Registros sin alerta prioritaria.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size:.8rem; letter-spacing:.5px;">
                        Pendientes
                    </h6>
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-hourglass-split fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalPendientes ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Entradas guardadas sin analizar.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size:.8rem; letter-spacing:.5px;">
                        Confianza Prom.
                    </h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-graph-up-arrow fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">
                    {{ $promedioConfianza ? number_format($promedioConfianza * 100, 2) . '%' : '0%' }}
                </h2>
                <p class="text-body-secondary mb-0 small">Promedio en casos de atención.</p>
            </div>
        </div>

        @if(isset($pendientes) && $pendientes->count() > 0)
            <div class="col-12 anime-item">
                <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center"
                                 style="width:50px;height:50px;">
                                <i class="bi bi-hourglass-split fs-4"></i>
                            </div>

                            <div>
                                <h4 class="fw-black mb-1 text-body">Entradas pendientes de análisis</h4>
                                <p class="text-body-secondary mb-0 small">
                                    Registros guardados cuando la API de IA no estaba disponible.
                                </p>
                            </div>
                        </div>

                        <form action="{{ route('analisis.reanalizar-pendientes') }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-warning rounded-pill fw-bold px-4 shadow-sm hover-elevate">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Reanalizar pendientes
                            </button>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table align-middle table-prometeo">
                            <thead class="bg-body-tertiary">
                            <tr>
                                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                                <th class="py-3 text-body-secondary fw-bold border-0">Texto</th>
                                <th class="py-3 text-body-secondary fw-bold border-0 text-center">Estado</th>
                                <th class="py-3 text-body-secondary fw-bold border-0 text-center">Fecha</th>
                                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acción</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($pendientes as $pendiente)
                                @php
                                    $persona = $pendiente->estudiante?->persona;
                                @endphp

                                <tr class="border-bottom border-secondary border-opacity-10">
                                    <td class="px-4 py-3 border-0 fw-bold text-body">
                                        <i class="bi bi-person-badge text-primary me-1"></i>
                                        {{ $persona?->nombre ?? 'Sin nombre' }}
                                        {{ $persona?->apellido_paterno ?? '' }}
                                        {{ $persona?->apellido_materno ?? '' }}
                                    </td>

                                    <td class="py-3 border-0 text-body-secondary">
                                        {{ \Illuminate\Support\Str::limit($pendiente->texto_ingresado, 100) }}
                                    </td>

                                    <td class="text-center py-3 border-0">
                                        <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm nlp-badge-warning">
                                            <i class="bi bi-hourglass-split me-1"></i>
                                            Pendiente
                                        </span>
                                    </td>

                                    <td class="text-center py-3 border-0 text-body-secondary">
                                        {{ $pendiente->created_at?->format('d/m/Y H:i') }}
                                    </td>

                                    <td class="text-end px-4 py-3 border-0">
                                        <form action="{{ route('analisis.reanalizar', $pendiente->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm hover-elevate">
                                                <i class="bi bi-arrow-repeat me-1"></i>
                                                Reanalizar
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="bi bi-journal-medical fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-1 text-body">Diarios emocionales con posible riesgo</h4>
                            <p class="text-body-secondary mb-0 small">
                                Entradas donde el modelo BETO + XGBoost marcó atención prioritaria.
                            </p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <form action="{{ route('analisis.reanalizar-pendientes') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-warning rounded-pill fw-bold px-4 shadow-sm hover-elevate">
                                <i class="bi bi-arrow-repeat me-1"></i>
                                Reanalizar pendientes
                            </button>
                        </form>

                        <a href="{{ route('psicologo.dashboard') }}" class="btn btn-light border rounded-pill fw-bold px-4 shadow-sm">
                            <i class="bi bi-arrow-left me-1"></i> Volver al panel
                        </a>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Código</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Resultado IA</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Confianza</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Fecha</th>
                            <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                        </tr>
                        </thead>

                        <tbody class="border-top-0">
                        @forelse($analisisRiesgo as $analisis)
                            @php
                                $persona = $analisis->estudiante?->persona;
                                $confianza = (float) $analisis->score_confianza;

                                $etiquetaOriginal = $analisis->etiqueta_roberta ?? 'pendiente';
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
                            @endphp

                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <span class="fw-bold text-body">
                                            {{ $persona?->nombre ?? 'Sin nombre' }}
                                            {{ $persona?->apellido_paterno ?? '' }}
                                            {{ $persona?->apellido_materno ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="text-center py-3 border-0">
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        {{ $analisis->codigo_anonimo }}
                                    </span>
                                </td>

                                <td class="text-center py-3 border-0">
                                    <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm {{ $etiquetaClase }}">
                                        {{ $etiquetaTexto }}
                                    </span>
                                </td>

                                <td class="text-center py-3 border-0 fw-black text-body">
                                    {{ number_format($confianza * 100, 2) }}%
                                </td>

                                <td class="text-center py-3 border-0 text-body-secondary fw-medium">
                                    {{ $analisis->created_at?->format('d/m/Y H:i') }}
                                </td>

                                <td class="text-end px-4 py-3 border-0">
                                    <a href="{{ route('analisis.show', $analisis->id) }}" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm hover-elevate">
                                        Revisar texto <i class="bi bi-arrow-right-circle ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-body-secondary border-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-shield-check fs-1 text-success opacity-50 mb-3"></i>
                                        <span>No hay análisis NLP con atención prioritaria.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $analisisRiesgo->links() }}
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
                    delay: anime.stagger(100),
                    easing: 'easeOutExpo',
                    duration: 850
                });
            }
        });
    </script>
@endpush
