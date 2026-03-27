@extends('layouts.app')

@section('title', 'Dashboard estudiante')
@section('page-title', 'Inicio del estudiante')
@section('page-subtitle', 'Evaluaciones pendientes y registro emocional')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="app-card welcome-card p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="soft-badge soft-primary mb-3">Entorno confidencial</span>
                        <h2 class="fw-bold mb-2">Hola, {{ auth()->user()->name }}</h2>
                        <p class="text-muted mb-0">
                            Aquí puedes responder tus instrumentos de tamizaje y registrar cómo te has sentido.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="{{ route('evaluaciones.index') }}" class="btn btn-primary">
                            <i class="bi bi-clipboard-check me-2"></i>Ver evaluaciones
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="soft-badge soft-warning">Pendiente</span>
                        <h5 class="fw-bold mt-3">PHQ-9</h5>
                    </div>
                    <div class="metric-icon"><i class="bi bi-emoji-frown"></i></div>
                </div>
                <p class="text-muted">Instrumento de tamizaje para sintomatología depresiva.</p>
                <a href="{{ route('evaluaciones.aplicar', 'phq9') }}" class="btn btn-primary w-100">Iniciar PHQ-9</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="soft-badge soft-warning">Pendiente</span>
                        <h5 class="fw-bold mt-3">GAD-7</h5>
                    </div>
                    <div class="metric-icon"><i class="bi bi-emoji-dizzy"></i></div>
                </div>
                <p class="text-muted">Instrumento de tamizaje para sintomatología ansiosa.</p>
                <a href="{{ route('evaluaciones.aplicar', 'gad7') }}" class="btn btn-primary w-100">Iniciar GAD-7</a>
            </div>
        </div>

        <div class="col-12">
            <div class="app-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Diario emocional</h4>
                        <p class="text-muted mb-0">Escribe una entrada breve para análisis emocional.</p>
                    </div>
                    <span class="soft-badge soft-info"><i class="bi bi-lock"></i> Protegido</span>
                </div>

                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Texto ingresado</label>
                        <textarea name="texto_ingresado" rows="7" class="form-control" placeholder="Hoy me he sentido..."></textarea>
                    </div>
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-primary">
                            <i class="bi bi-send me-2"></i>Guardar entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
