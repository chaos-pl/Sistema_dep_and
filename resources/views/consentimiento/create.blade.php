@extends('layouts.app')

@section('title', 'Consentimiento Informado')
@section('page-title', 'Consentimiento y aviso de privacidad')
@section('page-subtitle', 'Antes de continuar, necesitamos tu autorización informada')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="app-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-light text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:70px;height:70px;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                    <h2 class="fw-bold">Consentimiento informado</h2>
                    <p class="text-muted mb-0">
                        Tu bienestar y privacidad son prioridad dentro de este sistema.
                    </p>
                </div>

                <div class="welcome-panel rounded-4 border p-4 mb-4">
                    <h5 class="fw-bold mb-3">Uso de tus datos</h5>
                    <p>
                        Este sistema utiliza un <strong>código anónimo</strong> para proteger tu identidad dentro de los procesos de evaluación, seguimiento y análisis clínico.
                    </p>
                    <p>
                        Tus respuestas a instrumentos como <strong>PHQ-9</strong> y <strong>GAD-7</strong>, así como los textos ingresados para análisis NLP, serán tratados con confidencialidad y únicamente con fines de orientación, detección oportuna y atención institucional.
                    </p>
                    <p class="mb-0">
                        La información solo podrá ser visualizada por personal autorizado conforme a su rol y bajo medidas de resguardo.
                    </p>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="app-card p-3 h-100">
                            <div class="metric-icon mb-3"><i class="bi bi-incognito"></i></div>
                            <h6 class="fw-bold">Anonimato</h6>
                            <p class="text-muted mb-0">Se prioriza el uso de un identificador anónimo en lugar de datos visibles.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="app-card p-3 h-100">
                            <div class="metric-icon mb-3"><i class="bi bi-file-earmark-lock"></i></div>
                            <h6 class="fw-bold">Confidencialidad</h6>
                            <p class="text-muted mb-0">Tus respuestas clínicas se resguardan bajo acceso restringido.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="app-card p-3 h-100">
                            <div class="metric-icon mb-3"><i class="bi bi-heart"></i></div>
                            <h6 class="fw-bold">Acompañamiento</h6>
                            <p class="text-muted mb-0">El propósito es favorecer la detección y atención oportuna.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-light rounded-4 p-4 mb-4">
                    <h6 class="fw-bold mb-3">Declaro que:</h6>
                    <ul class="mb-0">
                        <li>He leído y comprendido la finalidad del sistema.</li>
                        <li>Entiendo que mis respuestas serán utilizadas con fines de tamizaje y seguimiento.</li>
                        <li>Conozco el tratamiento confidencial de la información.</li>
                        <li>He revisado el aviso de privacidad institucional.</li>
                    </ul>
                </div>

                <form method="POST" action="{{ route('consentimiento.store') }}">
                    @csrf

                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" value="1" id="acepta" name="acepta" required>
                        <label class="form-check-label" for="acepta">
                            Acepto los términos y el aviso de privacidad
                        </label>
                    </div>

                    <div class="d-flex flex-column flex-md-row gap-3 justify-content-between">
                        <a href="{{ route('aviso.privacidad') }}" class="btn btn-light border rounded-4 px-4">
                            <i class="bi bi-file-earmark-text me-2"></i>Leer aviso de privacidad
                        </a>

                        <button type="submit" class="btn btn-primary rounded-4 px-4">
                            <i class="bi bi-check-circle me-2"></i>Acepto y continuar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
