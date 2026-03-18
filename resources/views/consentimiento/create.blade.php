@extends('layouts.app')

@section('title', 'Consentimiento informado')
@section('page-title', 'Consentimiento informado y uso de datos')
@section('page-subtitle', 'Vista obligatoria tras el primer acceso para explicar privacidad, anonimato y tratamiento clínico de las respuestas.')

@section('content')
    <div class="row justify-content-center">
        <div class="col-12 col-xl-9">
            <div class="app-card p-4 p-md-5">
                <div class="row g-4 align-items-center mb-4">
                    <div class="col-lg-8">
                        <span class="soft-badge soft-primary mb-3">Acción requerida antes de continuar</span>
                        <h2 class="fw-bold mb-3">Tu participación está protegida mediante anonimato y acceso clínico restringido.</h2>
                        <p class="text-muted-soft mb-0">Esta pantalla debe mostrarse inmediatamente después del primer login para informar con claridad cómo se usa el <strong>codigo_anonimo</strong>, cómo se resguardan respuestas sensibles y quién puede acceder a cada módulo según el rol.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end">
                        <div class="metric-icon mx-lg-auto ms-0 me-0 ms-lg-auto" style="width: 76px; height: 76px; font-size: 2rem;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                    </div>
                </div>

                <div class="row g-4 mb-4">
                    <div class="col-md-4">
                        <div class="app-card-soft p-4 h-100">
                            <div class="metric-icon mb-3"><i class="bi bi-incognito"></i></div>
                            <h5 class="fw-semibold">Código anónimo</h5>
                            <p class="text-muted-soft mb-0">Las evaluaciones y el diario emocional pueden vincularse con un identificador interno para minimizar exposición de datos personales en reportes y seguimiento.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="app-card-soft p-4 h-100">
                            <div class="metric-icon mb-3"><i class="bi bi-file-earmark-lock"></i></div>
                            <h5 class="fw-semibold">Respuestas clínicas protegidas</h5>
                            <p class="text-muted-soft mb-0">Los resultados de PHQ-9, GAD-7, diagnósticos y análisis NLP solo deben ser visibles para perfiles autorizados y con fines de apoyo institucional.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="app-card-soft p-4 h-100">
                            <div class="metric-icon mb-3"><i class="bi bi-heart-pulse"></i></div>
                            <h5 class="fw-semibold">Detección oportuna</h5>
                            <p class="text-muted-soft mb-0">La finalidad del sistema es identificar señales tempranas de ansiedad o depresión y canalizar acompañamiento cuando exista riesgo.</p>
                        </div>
                    </div>
                </div>

                <div class="welcome-gradient rounded-4 border p-4 mb-4">
                    <h5 class="fw-bold mb-3">¿Qué acepta el estudiante al continuar?</h5>
                    <ul class="mb-0 text-muted-soft">
                        <li>Que sus respuestas a evaluaciones y textos de <code>texto_ingresado</code> serán analizadas para tamizaje y orientación.</li>
                        <li>Que el sistema puede generar alertas tempranas y resultados de <code>analisis_nlp</code> para atención profesional.</li>
                        <li>Que el tratamiento de información se realiza bajo confidencialidad y segmentación por roles: estudiante, tutor y psicólogo.</li>
                        <li>Que puede consultar el aviso de privacidad institucional antes de aceptar.</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('consentimiento.store') }}">
                    @csrf

                    <div class="app-card-soft p-4 mb-4">
                        <div class="form-check d-flex gap-3 align-items-start m-0">
                            <input class="form-check-input mt-1" type="checkbox" value="1" id="acepta" name="acepta" required>
                            <label class="form-check-label" for="acepta">
                                <span class="fw-semibold d-block mb-1">Acepto los términos y el aviso de privacidad.</span>
                                <span class="text-muted-soft">Confirmo que comprendo el uso del código anónimo, la protección de mis respuestas clínicas y la finalidad preventiva del sistema.</span>
                            </label>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between">
                        <a href="{{ route('aviso.privacidad') }}" class="btn btn-light border rounded-4 px-4">
                            <i class="bi bi-file-earmark-text me-2"></i>Leer aviso de privacidad
                        </a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-2"></i>Acepto los términos y deseo continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
