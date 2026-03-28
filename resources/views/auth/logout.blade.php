@extends('layouts.modal')

@section('title', 'Cerrar sesión - PROMETEO')

@push('styles')
    <style>
        /* Estados iniciales para Anime.js (ocultos) */
        .anime-icon { opacity: 0; transform: scale(0); }
        .anime-text { opacity: 0; transform: translateY(15px); }
        .anime-action-btn { opacity: 0; }

        /* Ajuste para que el icono se vea súper moderno */
        .logout-icon-container {
            width: 80px;
            height: 80px;
            margin: 0 auto;
            background: rgba(239, 68, 68, 0.1); /* Rojo suave */
            color: #ef4444; /* Rojo vibrante */
        }
    </style>
@endpush

@section('content')
    <div class="modal-body-prometeo text-center p-5">

        <div class="mb-4 d-flex justify-content-center">
            <div class="logout-icon-container rounded-circle d-flex align-items-center justify-content-center anime-icon shadow-sm">
                <i class="bi bi-door-open fs-1"></i>
            </div>
        </div>

        <h2 class="fw-black mb-3 anime-text text-dark">¿Deseas cerrar sesión?</h2>
        <p class="text-muted mb-5 anime-text fs-5">
            Estás a punto de salir de <strong class="text-primary fw-bold">PROMETEO</strong>. Asegúrate de haber guardado tus cambios antes de continuar.
        </p>

        <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
            <a href="{{ route('dashboard') }}" class="btn btn-light border px-4 py-2 fw-bold anime-action-btn rounded-pill shadow-sm text-secondary" data-direction="left">
                <i class="bi bi-arrow-left me-2"></i>Volver al panel
            </a>

            <form action="{{ route('logout') }}" method="POST" class="m-0 anime-action-btn" data-direction="right">
                @csrf
                <button type="submit" class="btn btn-danger px-4 py-2 fw-bold w-100 rounded-pill shadow-sm">
                    <i class="bi bi-power me-2"></i>Sí, cerrar sesión
                </button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const tl = anime.timeline({
                easing: 'easeOutExpo'
            });

            // 1. El icono da un rebote (efecto spring)
            tl.add({
                targets: '.anime-icon',
                scale: [0, 1],
                opacity: [0, 1],
                easing: 'spring(1, 80, 10, 0)', // Efecto elástico
                duration: 1200,
                delay: 200 // Esperamos a que la tarjeta modal suba primero
            })

                // 2. Los textos suben y aparecen en cascada
                .add({
                    targets: '.anime-text',
                    translateY: [15, 0],
                    opacity: [0, 1],
                    delay: anime.stagger(150),
                    duration: 800
                }, '-=800') // Comienza antes de que termine el rebote del icono

                // 3. Los botones se separan desde el centro hacia los lados
                .add({
                    targets: '.anime-action-btn',
                    translateX: function(el) {
                        // Si el botón tiene data-direction="left", viene desde la derecha (+30px), y viceversa
                        return el.getAttribute('data-direction') === 'left' ? [30, 0] : [-30, 0];
                    },
                    opacity: [0, 1],
                    duration: 1000
                }, '-=600');
        });
    </script>
@endpush
