@extends('layouts.app')

@section('title', 'Editar Psicólogo - PROMETEO')
@section('page-title', 'Editar Psicólogo')
@section('page-subtitle', 'Actualización de expediente y credenciales clínicas')

@section('content')
    <div class="row justify-content-center anime-item">
        <div class="col-lg-10">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Modificando Psicólogo</h4>
                        <p class="text-body-secondary mb-0 small">Actualiza la información personal, cédula profesional o cambia la contraseña.</p>
                    </div>
                </div>

                <form action="{{ route('admin.psicologos.update', $psicologo->id) }}" method="POST">
                    @include('admin.psicologos.partials.form', [
                        'psicologo' => $psicologo,
                        'submitText' => 'Actualizar Expediente',
                        'method' => 'PUT'
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], easing: 'easeOutExpo', duration: 800 });
        });
    </script>
@endpush
