@extends('layouts.modal')

@section('title', 'Consentimiento informado - PROMETEO')

@push('styles')
    <style>
        .anime-badge { opacity: 0; transform: scale(0.5); }
        .anime-item { opacity: 0; }
    </style>
@endpush

@section('content')
    <div class="modal-header-prometeo d-flex justify-content-between align-items-center">
        <div class="anime-item">
            <h4 class="fw-bold mb-1 text-dark">Consentimiento informado</h4>
            <div class="small text-muted">Autorización para el uso clínico y confidencial de la información</div>
        </div>

        <button type="submit" form="formRechazar" class="btn btn-light btn-sm anime-item border shadow-sm rounded-pill">
            <i class="bi bi-x-lg me-1"></i> Cerrar
        </button>
    </div>

    <div class="modal-body-prometeo">
        <div class="text-center mb-4">
            <div class="modal-badge mb-3 anime-badge shadow-sm">
                <i class="bi bi-shield-check fs-2"></i>
            </div>
            <h2 class="fw-bold anime-item">Antes de continuar</h2>
            <p class="text-muted mb-0 anime-item">Confirma que comprendes el uso de tu información dentro de PROMETEO.</p>
        </div>

        <div class="app-card p-4 mb-4 anime-item border-0 shadow-sm" style="background: linear-gradient(135deg, #f7f4fb 0%, #ffffff 100%);">
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-info-circle me-2"></i>Información importante</h5>
            <p class="text-secondary">Tus respuestas en las evaluaciones y los textos que ingreses podrán ser utilizados para análisis de riesgo y seguimiento psicológico dentro de la institución.</p>
            <p class="text-secondary">El sistema utiliza un código anónimo para proteger tu identidad y restringe la visualización de datos a usuarios autorizados.</p>
            <p class="mb-0 text-secondary fw-medium">El objetivo es apoyar la detección oportuna y el acompañamiento responsable.</p>
        </div>

        <form action="{{ route('consentimiento.store') }}" method="POST" id="formConsentimiento">
            @csrf

            <div class="form-check mb-4 anime-item bg-light p-3 rounded-3 border">
                <input class="form-check-input ms-1 shadow-none" type="checkbox" value="1" name="acepta" id="acepta" required style="transform: scale(1.2); margin-top: 0.3rem;">
                <label class="form-check-label ms-2 fw-medium text-dark" for="acepta">
                    He leído y acepto los términos del sistema y el aviso de privacidad.
                </label>
            </div>

            @error('acepta')
            <small class="text-danger d-block mb-3 fw-bold anime-item"><i class="bi bi-exclamation-triangle me-1"></i>{{ $message }}</small>
            @enderror

            <div class="d-flex flex-column flex-md-row justify-content-between gap-3 anime-item">
                <div class="d-flex flex-column flex-md-row gap-2 w-100">
                    <button type="button" class="btn btn-light border text-secondary fw-bold" data-bs-toggle="modal" data-bs-target="#modalAvisoPrivacidad">
                        <i class="bi bi-file-earmark-text me-1"></i>Aviso de privacidad
                    </button>

                    <button type="submit" form="formRechazar" class="btn btn-outline-danger fw-bold">
                        <i class="bi bi-x-circle me-1"></i>No acepto
                    </button>
                </div>

                <button type="submit" class="btn btn-primary fw-bold px-4 shadow-sm w-100 w-md-auto" id="btnAceptar" disabled>
                    <i class="bi bi-check-circle me-1"></i>Acepto y continuar
                </button>
            </div>
        </form>

        <form action="{{ route('consentimiento.rechazar') }}" method="POST" id="formRechazar" class="d-none">
            @csrf
        </form>
    </div>
@endsection

@push('modals')
    <div class="modal fade" id="modalAvisoPrivacidad" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header bg-light border-bottom-0 p-4 pb-0">
                    <div>
                        <h5 class="fw-bold mb-1 text-primary"><i class="bi bi-file-earmark-lock me-2"></i>Aviso de privacidad</h5>
                        <div class="small text-muted">Tratamiento responsable de la información</div>
                    </div>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body p-4 text-secondary" id="modalPrivacidadContenido">
                    <p>La plataforma <strong>PROMETEO</strong> recaba información relacionada con evaluaciones emocionales, respuestas clínicas y registros textuales con la finalidad de apoyar la detección oportuna y el acompañamiento institucional.</p>
                    <p>La identidad del estudiante se protege mediante el uso de un código anónimo durante los procesos de evaluación, análisis y visualización clínica.</p>
                    <p>La información será visualizada únicamente por perfiles autorizados conforme a las funciones del sistema, y será utilizada con fines de seguimiento, atención y análisis.</p>

                    <div class="bg-primary bg-opacity-10 p-4 rounded-4 mt-4 border border-primary border-opacity-25">
                        <h6 class="fw-bold text-primary mb-3">Compromisos del sistema</h6>
                        <ul class="mb-0 ps-3">
                            <li class="mb-1">Resguardar la confidencialidad de las respuestas.</li>
                            <li class="mb-1">Reducir la exposición innecesaria de datos personales.</li>
                            <li class="mb-1">Permitir atención profesional cuando exista riesgo detectado.</li>
                            <li>Favorecer un entorno digital respetuoso y seguro.</li>
                        </ul>
                    </div>
                </div>

                <div class="modal-footer border-top-0 p-4 pt-0">
                    <button type="button" class="btn btn-primary px-4 rounded-pill" data-bs-dismiss="modal">Entendido</button>
                </div>
            </div>
        </div>
    </div>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const acepta = document.getElementById('acepta');
            const btnAceptar = document.getElementById('btnAceptar');

            if (acepta && btnAceptar) {
                acepta.addEventListener('change', function () {
                    btnAceptar.disabled = !this.checked;
                });
            }

            // Animación de entrada de los elementos
            anime({
                targets: '.anime-badge',
                scale: [0, 1],
                opacity: [0, 1],
                delay: 200,
                easing: 'spring(1, 80, 10, 0)',
                duration: 1200
            });

            anime({
                targets: '.anime-item',
                translateY: [15, 0],
                opacity: [0, 1],
                delay: anime.stagger(100, {start: 400}),
                easing: 'easeOutQuad',
                duration: 800
            });

            // Animar el contenido del modal de Bootstrap cuando se abre
            const modalAviso = document.getElementById('modalAvisoPrivacidad');
            if (modalAviso) {
                modalAviso.addEventListener('show.bs.modal', function () {
                    anime({
                        targets: '#modalPrivacidadContenido p, #modalPrivacidadContenido div',
                        translateY: [20, 0],
                        opacity: [0, 1],
                        delay: anime.stagger(100),
                        easing: 'easeOutExpo',
                        duration: 800
                    });
                });
            }
        });
    </script>
@endpush
