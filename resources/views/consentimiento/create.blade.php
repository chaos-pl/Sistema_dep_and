@extends('layouts.app')

@section('title', 'Consentimiento informado')
@section('page-title', 'Consentimiento informado')
@section('page-subtitle', 'Autorización para el uso clínico y confidencial de la información')

@section('content')
    <div class="row justify-content-center">
        <div class="col-xl-8">
            <div class="app-card p-4 p-md-5">
                <div class="text-center mb-4">
                    <div class="bg-light text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width:76px;height:76px;">
                        <i class="bi bi-shield-check fs-2"></i>
                    </div>
                    <h2 class="fw-bold">Consentimiento informado</h2>
                    <p class="text-muted mb-0">Antes de continuar, confirma que comprendes el uso de tus datos</p>
                </div>

                <div class="app-card p-4 mb-4 welcome-card">
                    <h5 class="fw-bold mb-3">Información importante</h5>
                    <p>
                        Tus respuestas en las evaluaciones y los textos que ingreses podrán ser utilizados para análisis de riesgo
                        y seguimiento psicológico dentro de la institución.
                    </p>
                    <p>
                        El sistema utiliza un código anónimo para proteger tu identidad y restringe la visualización de datos
                        a usuarios autorizados.
                    </p>
                    <p class="mb-0">
                        El objetivo es apoyar la detección oportuna y el acompañamiento responsable.
                    </p>
                </div>

                <form action="{{ route('consentimiento.store') }}" method="POST" id="formConsentimiento">
                    @csrf

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" name="acepta" id="acepta" required>
                        <label class="form-check-label" for="acepta">
                            Acepto los términos y el aviso de privacidad
                        </label>
                    </div>

                    @error('acepta')
                    <small class="text-danger d-block mb-3">{{ $message }}</small>
                    @enderror

                    <div class="d-flex flex-column flex-md-row justify-content-between gap-3">
                        <div class="d-flex flex-column flex-md-row gap-3">
                            <a href="{{ route('aviso.privacidad') }}" class="btn btn-light border">
                                <i class="bi bi-file-earmark-text me-2"></i>Leer aviso de privacidad
                            </a>

                            <button type="submit" form="formRechazar" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle me-2"></i>No acepto y salir
                            </button>
                        </div>

                        <button type="submit" class="btn btn-primary" id="btnAceptar" disabled>
                            <i class="bi bi-check-circle me-2"></i>Acepto y continuar
                        </button>
                    </div>
                </form>

                <form action="{{ route('consentimiento.rechazar') }}" method="POST" id="formRechazar" class="d-none">
                    @csrf
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        const acepta = document.getElementById('acepta');
        const btnAceptar = document.getElementById('btnAceptar');

        acepta.addEventListener('change', function () {
            btnAceptar.disabled = !this.checked;
        });
    </script>
@endpush
