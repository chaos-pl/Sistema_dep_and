@extends('layouts.app')

@section('title', 'Diario emocional')
@section('page-title', 'Diario emocional')
@section('page-subtitle', 'Registro textual para análisis NLP')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="app-card p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h3 class="fw-bold mb-1">Nueva entrada</h3>
                        <p class="text-muted mb-0">Comparte cómo te has sentido recientemente.</p>
                    </div>
                    <span class="soft-badge soft-info"><i class="bi bi-robot"></i> Análisis NLP</span>
                </div>

                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Texto ingresado</label>
                        <textarea name="texto_ingresado" rows="10" class="form-control" placeholder="Escribe aquí tu reflexión o cómo te has sentido..."></textarea>
                    </div>

                    <div class="d-flex justify-content-between align-items-center flex-column flex-md-row gap-3">
                        <small class="text-muted">
                            Tu entrada podrá ser analizada para identificar necesidad de atención.
                        </small>
                        <button class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
