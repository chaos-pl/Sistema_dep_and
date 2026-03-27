@extends('layouts.app')

@section('title', 'Evaluaciones')
@section('page-title', 'Evaluaciones')
@section('page-subtitle', 'Instrumentos disponibles y estado actual')

@section('content')
    <div class="row g-4">
        <div class="col-md-6">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="soft-badge soft-warning">Disponible</span>
                        <h4 class="fw-bold mt-3 mb-1">PHQ-9</h4>
                        <p class="text-muted mb-0">Cuestionario de salud del paciente</p>
                    </div>
                    <div class="metric-icon"><i class="bi bi-clipboard-pulse"></i></div>
                </div>
                <p class="text-muted">Tiempo estimado: 3 a 5 minutos.</p>
                <a href="{{ route('evaluaciones.aplicar', 'phq9') }}" class="btn btn-primary w-100">Responder PHQ-9</a>
            </div>
        </div>

        <div class="col-md-6">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <span class="soft-badge soft-warning">Disponible</span>
                        <h4 class="fw-bold mt-3 mb-1">GAD-7</h4>
                        <p class="text-muted mb-0">Escala para ansiedad generalizada</p>
                    </div>
                    <div class="metric-icon"><i class="bi bi-clipboard-heart"></i></div>
                </div>
                <p class="text-muted">Tiempo estimado: 3 a 5 minutos.</p>
                <a href="{{ route('evaluaciones.aplicar', 'gad7') }}" class="btn btn-primary w-100">Responder GAD-7</a>
            </div>
        </div>
    </div>
@endsection
