@extends('layouts.app')

@section('title', 'Estado Pendiente - PROMETEO')
@section('page-title', 'Aviso del Sistema')
@section('page-subtitle', 'Estado de tu expediente en la plataforma')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: scale(0.95); }
    </style>
@endpush

@section('content')
    <div class="row justify-content-center align-items-center" style="min-height: 60vh;">
        <div class="col-lg-6 anime-item">
            <div class="app-card p-5 border-0 shadow-lg rounded-4 text-center bg-body-tertiary">
                <div class="mb-4 position-relative">
                    <div class="mx-auto d-flex align-items-center justify-content-center rounded-circle bg-warning bg-opacity-10 text-warning-emphasis shadow-sm"
                         style="width: 100px; height: 100px; position: relative; z-index: 2;">
                        <i class="bi bi-hourglass-split" style="font-size: 3.5rem;"></i>
                    </div>
                </div>

                <h2 class="fw-black mb-3 text-body" style="font-size: 2rem;">{{ $titulo }}</h2>
                <p class="text-body-secondary fs-5 mb-5 px-md-4">
                    {{ $mensaje }}
                </p>

                <div class="d-flex justify-content-center gap-3 flex-wrap pt-4 border-top border-secondary border-opacity-10">
                    <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 rounded-pill px-4 py-2 fs-6 shadow-sm">
                        <i class="bi bi-info-circle-fill me-1"></i> Estado: Pendiente
                    </span>

                    <span class="badge bg-body text-body-secondary border border-secondary border-opacity-25 rounded-pill px-4 py-2 fs-6 shadow-sm">
                        <i class="bi bi-person-badge-fill me-1 text-primary"></i> Contacta a tu Administrador
                    </span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({
                targets: '.anime-item',
                scale: [0.95, 1],
                opacity: [0, 1],
                easing: 'easeOutElastic(1, .8)',
                duration: 1000
            });
        });
    </script>
@endpush
