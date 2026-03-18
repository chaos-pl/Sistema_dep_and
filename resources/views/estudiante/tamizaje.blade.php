@extends('layouts.app')

@section('title', 'Aplicación de tamizaje')
@section('page-title', 'Aplicación del tamizaje paso a paso')
@section('page-subtitle', 'Formulario responsive para insertar respuestas en la tabla respuestas, con progreso visible y opciones amplias.')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-10">
            <div class="app-card p-4 p-lg-5">
                <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 mb-4">
                    <div>
                        <span class="soft-badge soft-primary mb-3">Cuestionario activo: PHQ-9</span>
                        <h2 class="fw-bold mb-2">Responde pensando en las últimas dos semanas.</h2>
                        <p class="text-muted-soft mb-0">El estudiante ve una sola pregunta por bloque visual para reducir fatiga. Cada respuesta se almacena en <code>respuestas</code> asociada a <code>usuario_id</code>, <code>cuestionario_id</code> y <code>pregunta_id</code>.</p>
                    </div>
                    <div class="app-card-soft p-3 align-self-start">
                        <div class="small text-uppercase text-muted-soft fw-semibold mb-2">Progreso</div>
                        <div class="d-flex align-items-center gap-3">
                            <span class="step-pill">4</span>
                            <div>
                                <div class="fw-semibold">Pregunta 4 de 9</div>
                                <div class="small text-muted-soft">Tiempo estimado restante: 3 minutos</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="progress rounded-pill mb-4" role="progressbar" aria-label="Progreso del cuestionario" aria-valuenow="44" aria-valuemin="0" aria-valuemax="100" style="height: 14px; background: #EFE7FA;">
                    <div class="progress-bar" style="width: 44%; background: linear-gradient(90deg, #A388D4, #8F6AC7);"></div>
                </div>

                <form method="POST" action="#">
                    @csrf
                    <div class="app-card-soft p-4 p-lg-5 mb-4">
                        <div class="small text-uppercase text-muted-soft fw-semibold mb-2">Pregunta actual</div>
                        <h3 class="fw-bold mb-3">Poco interés o placer en hacer las cosas.</h3>
                        <p class="text-muted-soft mb-0">Selecciona la opción que mejor describa tu experiencia reciente.</p>
                    </div>

                    <div class="row g-3 mb-4">
                        @foreach ([
                            ['value' => 0, 'label' => 'Nunca', 'helper' => 'No ha ocurrido en este periodo.'],
                            ['value' => 1, 'label' => 'Varios días', 'helper' => 'Apareció de forma ocasional.'],
                            ['value' => 2, 'label' => 'Más de la mitad de los días', 'helper' => 'Fue una experiencia frecuente.'],
                            ['value' => 3, 'label' => 'Casi todos los días', 'helper' => 'Se presentó de manera persistente.'],
                        ] as $option)
                            <div class="col-md-6">
                                <label class="question-option d-block h-100 {{ $loop->first ? 'active' : '' }}">
                                    <input class="form-check-input d-none" type="radio" name="respuesta" value="{{ $option['value'] }}" {{ $loop->first ? 'checked' : '' }}>
                                    <div class="d-flex justify-content-between align-items-start gap-3">
                                        <div>
                                            <span class="step-pill mb-3" style="width: 40px; height: 40px;">{{ $option['value'] }}</span>
                                            <h5 class="fw-semibold mb-1">{{ $option['label'] }}</h5>
                                            <p class="text-muted-soft mb-0">{{ $option['helper'] }}</p>
                                        </div>
                                        <i class="bi bi-check-circle-fill text-primary fs-4"></i>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>

                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <button type="button" class="btn btn-light border"><i class="bi bi-arrow-left me-2"></i>Pregunta anterior</button>
                        <div class="d-flex gap-3">
                            <button type="button" class="btn btn-soft-primary">Guardar y salir</button>
                            <button type="submit" class="btn btn-primary">Siguiente pregunta<i class="bi bi-arrow-right ms-2"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
