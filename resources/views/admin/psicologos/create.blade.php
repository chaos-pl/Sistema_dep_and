@extends('layouts.app')

@section('title', 'Nuevo Psicólogo - PROMETEO')
@section('page-title', 'Registrar Psicólogo')
@section('page-subtitle', 'Alta administrativa de personal clínico')

@section('content')
    <div class="row justify-content-center anime-item">
        <div class="col-lg-10">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-person-plus-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Nuevo Psicólogo</h4>
                        <p class="text-body-secondary mb-0 small">Completa el expediente y genera sus credenciales de acceso institucional.</p>
                    </div>
                </div>

                <form action="{{ route('admin.psicologos.store') }}" method="POST">
                    @include('admin.psicologos.partials.form', [
                        'submitText' => 'Guardar Psicólogo',
                        'method' => 'POST'
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
                icon.style.color = 'var(--app-primary)';
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                icon.style.color = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], easing: 'easeOutExpo', duration: 800 });
        });
    </script>
@endpush
