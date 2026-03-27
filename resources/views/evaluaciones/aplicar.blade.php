@extends('layouts.app')

@section('title', 'Aplicar evaluación')
@section('page-title', 'Aplicación del tamizaje')
@section('page-subtitle', 'Responde con sinceridad')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-9">
            <div class="app-card p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <span class="soft-badge soft-primary text-uppercase">{{ $tipo ?? 'instrumento' }}</span>
                        <h3 class="fw-bold mt-3 mb-1">Formulario paso a paso</h3>
                        <p class="text-muted mb-0">Selecciona la opción que más se acerque a tu experiencia reciente.</p>
                    </div>
                    <div class="text-end">
                        <small class="text-muted d-block">Progreso</small>
                        <strong class="text-primary">Pregunta 1 de 9</strong>
                    </div>
                </div>

                <div class="progress rounded-pill mb-4" style="height: 12px; background:#efe7fb;">
                    <div class="progress-bar rounded-pill" style="width:11%; background:#9b72cf;"></div>
                </div>

                <form action="{{ route('evaluaciones.responder', $tipo) }}" method="POST">
                    @csrf

                    <div class="app-card border-0 p-4 mb-4" style="background:#fbf8ff;">
                        <small class="text-muted d-block mb-2">Pregunta 1</small>
                        <h5 class="fw-bold mb-0">Durante las últimas dos semanas, ¿con qué frecuencia te has sentido desanimado o sin interés?</h5>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <input class="btn-check" type="radio" name="respuesta_1" id="op1" value="0">
                            <label class="btn btn-light border text-start w-100 p-3" for="op1">
                                <strong>Nunca</strong><br>
                                <small class="text-muted">No ocurrió en este periodo</small>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <input class="btn-check" type="radio" name="respuesta_1" id="op2" value="1">
                            <label class="btn btn-light border text-start w-100 p-3" for="op2">
                                <strong>Varios días</strong><br>
                                <small class="text-muted">Ocurrió ocasionalmente</small>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <input class="btn-check" type="radio" name="respuesta_1" id="op3" value="2">
                            <label class="btn btn-light border text-start w-100 p-3" for="op3">
                                <strong>Más de la mitad de los días</strong><br>
                                <small class="text-muted">Ocurrió con frecuencia</small>
                            </label>
                        </div>

                        <div class="col-md-6">
                            <input class="btn-check" type="radio" name="respuesta_1" id="op4" value="3">
                            <label class="btn btn-light border text-start w-100 p-3" for="op4">
                                <strong>Casi todos los días</strong><br>
                                <small class="text-muted">Fue persistente</small>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-light border" disabled>
                            <i class="bi bi-arrow-left me-2"></i>Anterior
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Siguiente <i class="bi bi-arrow-right ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
