@extends('layouts.app')

@section('title', 'Dashboard Estudiante - PROMETEO')
@section('page-title', 'Inicio del Estudiante')
@section('page-subtitle', 'Evaluaciones pendientes y registro emocional')

@push('styles')
    <style>
        .hover-elevate { transition: transform 0.3s ease, box-shadow 0.3s ease; border: 1px solid transparent; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important; border-color: rgba(124, 58, 237, 0.2); }
        .bg-welcome-student { background: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%); position: relative; overflow: hidden; }
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
        }
        .anime-item { opacity: 0; transform: translateY(20px); }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-student p-4 p-md-5 text-white rounded-4 border-0 shadow-lg">
                <div class="row align-items-center position-relative z-1">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-shield-check me-1"></i> Entorno Seguro y Confidencial
                        </span>

                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem;">
                            Hola, {{ auth()->user()->name }}
                        </h2>

                        <p class="mb-0 text-white text-opacity-75 fs-5">
                            @if($estudiante)
                                Matrícula: {{ $estudiante->matricula }} · Grupo: {{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}
                            @else
                                Tu cuenta aún no tiene expediente de estudiante vinculado.
                            @endif
                        </p>
                    </div>

                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}" class="btn btn-light text-primary rounded-pill fw-bold px-4 py-2 shadow-sm">
                            <i class="bi bi-clipboard2-check-fill me-2"></i>Ver Evaluaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if($estudiante)
            <div class="col-md-4 anime-item">
                <div class="app-card p-4 border-0 shadow-sm rounded-4 bg-light h-100">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                            <i class="bi bi-check2-circle fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-black mb-0">{{ $totalCompletadas }}</h5>
                            <small class="text-muted">Evaluaciones completadas</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-8 anime-item">
                <div class="app-card p-4 border-0 shadow-sm rounded-4 bg-light h-100">
                    <h5 class="fw-black mb-3">Resumen académico</h5>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block">Código anónimo</small>
                            <span class="fw-bold">{{ $estudiante->codigo_anonimo }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Matrícula</small>
                            <span class="fw-bold">{{ $estudiante->matricula }}</span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block">Grupo</small>
                            <span class="fw-bold">{{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}</span>
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
                <div class="app-card hover-elevate p-4 p-md-5 h-100 border-0 shadow-sm rounded-4 d-flex flex-column justify-content-between bg-light">
                    <div>
                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <span class="badge {{ $badgeClass }} rounded-pill px-3 py-1 fw-bold shadow-sm mb-2">
                                    {{ ucfirst(str_replace('_', ' ', $item->estado)) }}
                                </span>
                                <h4 class="fw-black text-dark mb-0">{{ strtoupper($item->instrumento->acronimo) }}</h4>
                            </div>

                            <div class="{{ $iconBg }} rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 55px; height: 55px;">
                                <i class="bi {{ $icon }} fs-3"></i>
                            </div>
                        </div>

                        <p class="text-muted fw-medium mb-3">{{ $item->instrumento->nombre }}</p>

                        @if($item->evaluacion && $item->puntaje_total !== null)
                            <p class="mb-0">
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
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-4 mb-4 gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-journal-text fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-1 text-dark">Diario Emocional</h4>
                            <p class="text-muted mb-0 small">Escribe una entrada libre. Nuestra IA te ayudará a entender tus emociones.</p>
                        </div>
                    </div>
                    <span class="badge bg-light text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-lock-fill me-1"></i> 100% Privado
                    </span>
                </div>

                @if($ultimaEntrada)
                    <div class="alert alert-light border rounded-4 mb-4">
                        <div class="fw-bold mb-1">Última entrada registrada</div>
                        <div class="text-muted small mb-2">{{ $ultimaEntrada->created_at?->format('d/m/Y H:i') }}</div>
                        <div>{{ \Illuminate\Support\Str::limit($ultimaEntrada->texto_ingresado, 180) }}</div>
                    </div>
                @endif

                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="form-label fw-bold text-secondary">¿Cómo te sientes el día de hoy?</label>
                        <textarea name="texto_ingresado" rows="6" class="form-control form-control-lg bg-light border-0 shadow-sm rounded-4 p-4" placeholder="Hoy me he sentido un poco abrumado por las clases, pero también emocionado por..." style="resize: none;"></textarea>
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
            anime({
                targets: '.anime-item',
                translateY: [30, 0],
                opacity: [0, 1],
                delay: anime.stagger(150),
                easing: 'easeOutExpo',
                duration: 1000
            });
        });
    </script>
@endpush
