@extends('layouts.modal')

@section('title', 'Aviso de privacidad - PROMETEO')

@push('styles')
    <style>
        .anime-icon { opacity: 0; transform: scale(0.5); }
        .anime-content { opacity: 0; }
    </style>
@endpush

@section('content')
    <div class="modal-header-prometeo d-flex justify-content-between align-items-center">
        <div class="anime-content">
            <h4 class="fw-bold mb-1 text-dark">Aviso de privacidad</h4>
            <div class="small text-muted">Tratamiento responsable y confidencial de la información</div>
        </div>

        <a href="{{ url()->previous() }}" class="btn btn-light btn-sm border rounded-pill shadow-sm anime-content">
            <i class="bi bi-arrow-left me-1"></i>Regresar
        </a>
    </div>

    <div class="modal-body-prometeo">
        <div class="text-center mb-5">
            <div class="modal-badge mb-3 anime-icon shadow-sm">
                <i class="bi bi-file-earmark-lock fs-2"></i>
            </div>
            <h2 class="fw-bold anime-content">Privacidad y protección de datos</h2>
            <p class="text-muted mb-0 anime-content">Conoce cómo PROMETEO resguarda tu información.</p>
        </div>

        <div class="text-secondary">
            <p class="anime-content">
                La plataforma <strong class="text-primary">PROMETEO</strong> recaba información relacionada con evaluaciones emocionales, respuestas clínicas
                y registros textuales con la finalidad de apoyar la detección oportuna y el acompañamiento institucional.
            </p>

            <p class="anime-content">
                La identidad del estudiante se protege mediante el uso de un código anónimo durante los procesos de
                evaluación, análisis y visualización clínica.
            </p>

            <p class="anime-content">
                La información será visualizada únicamente por perfiles autorizados conforme a las funciones del sistema,
                y será utilizada con fines de seguimiento, atención y análisis.
            </p>
        </div>

        <div class="app-card p-4 mt-4 border-0 shadow-sm anime-content" style="background: linear-gradient(135deg, #f7f4fb 0%, #ffffff 100%);">
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-shield-check me-2"></i>Compromisos del sistema</h5>
            <ul class="mb-0 text-secondary ps-3">
                <li class="mb-2">Resguardar la confidencialidad de las respuestas.</li>
                <li class="mb-2">Reducir la exposición innecesaria de datos personales.</li>
                <li class="mb-2">Permitir atención profesional cuando exista riesgo detectado.</li>
                <li>Favorecer un entorno digital respetuoso y seguro.</li>
            </ul>
        </div>

        <div class="text-end mt-5 anime-content">
            <a href="{{ url()->previous() }}" class="btn btn-primary px-4 fw-bold shadow-sm rounded-pill">
                <i class="bi bi-check-lg me-1"></i> Entendido
            </a>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Icono central con efecto rebote
            anime({
                targets: '.anime-icon',
                scale: [0, 1],
                opacity: [0, 1],
                delay: 200,
                easing: 'spring(1, 80, 10, 0)',
                duration: 1200
            });

            // Párrafos y tarjetas en cascada
            anime({
                targets: '.anime-content',
                translateY: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(100, {start: 350}),
                easing: 'easeOutQuad',
                duration: 800
            });
        });
    </script>
@endpush
