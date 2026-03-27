@extends('layouts.app')

@section('title', 'Cerrar sesión - PROMETEO')
@section('page-title', 'Cerrar sesión')
@section('page-subtitle', 'Confirma que deseas salir del sistema')

@push('styles')
    <style>
        .anime-icon { opacity: 0; transform: scale(0); }
        .anime-text { opacity: 0; }
        .anime-action-btn { opacity: 0; }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center mt-4">
        <div class="col-lg-5 col-md-8">
            <div class="app-card p-5 text-center shadow-sm rounded-4 border-0">
                <div class="mb-4 d-flex justify-content-center">
                    <div class="metric-icon anime-icon bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width:80px;height:80px;">
                        <i class="bi bi-door-open fs-1"></i>
                    </div>
                </div>

                <h2 class="fw-bold mb-3 anime-text text-dark">¿Deseas cerrar sesión?</h2>
                <p class="text-muted mb-5 anime-text fs-5">
                    Estás a punto de salir de <strong class="text-primary">PROMETEO</strong>. Asegúrate de haber guardado tus cambios antes de continuar.
                </p>

                <div class="d-flex flex-column flex-md-row justify-content-center gap-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-light border px-4 py-2 fw-bold anime-action-btn rounded-pill" data-direction="left">
                        <i class="bi bi-arrow-left me-2"></i>Volver al panel
                    </a>

                    <form action="{{ route('logout') }}" method="POST" class="m-0 anime-action-btn" data-direction="right">
                        @csrf
                        <button type="submit" class="btn btn-danger px-4 py-2 fw-bold w-100 rounded-pill shadow-sm">
                            <i class="bi bi-box-arrow-right me-2"></i>Sí, cerrar sesión
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Efecto de rebote "spring" para el icono
            anime({
                targets: '.anime-icon',
                scale: [0, 1],
                opacity: [0, 1],
                easing: 'spring(1, 80, 10, 0)', // masa, rigidez, amortiguación, velocidad inicial
                duration: 1500
            });

            // Textos apareciendo
            anime({
                targets: '.anime-text',
                translateY: [15, 0],
                opacity: [0, 1],
                delay: anime.stagger(200, {start: 200}),
                easing: 'easeOutExpo',
                duration: 1000
            });

            // Botones separándose desde el centro
            anime({
                targets: '.anime-action-btn',
                translateX: function(el) {
                    return el.getAttribute('data-direction') === 'left' ? [30, 0] : [-30, 0];
                },
                opacity: [0, 1],
                delay: 600,
                easing: 'easeOutExpo',
                duration: 1200
            });
        });
    </script>
@endpush
