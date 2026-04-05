@extends('layouts.app')

@section('title', 'Detalle de Psicólogo - PROMETEO')
@section('page-title', 'Expediente Clínico')
@section('page-subtitle', 'Información detallada del personal de psicología')

@section('content')
    <div class="row justify-content-center g-4 anime-item">

        <div class="col-xl-4 col-lg-5">
            <div class="app-card p-4 border-0 shadow-sm rounded-4 h-100 text-center bg-body-tertiary">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 100px; height: 100px;">
                    <i class="bi bi-heart-pulse-fill" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-black text-body mb-1">
                    {{ $psicologo->persona->nombre ?? 'N/A' }} {{ $psicologo->persona->apellido_paterno ?? '' }}
                </h4>
                <p class="text-body-secondary mb-3 fw-medium">Psicólogo Institucional</p>

                <span class="badge bg-body text-primary border border-primary border-opacity-25 rounded-pill px-4 py-2 mb-4 fw-bold shadow-sm" style="font-size: 0.9rem;">
                    <i class="bi bi-award-fill me-1"></i> Cédula: {{ $psicologo->cedula_profesional }}
                </span>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.psicologos.edit', $psicologo->id) }}" class="btn btn-warning fw-bold rounded-pill text-dark shadow-sm">
                        <i class="bi bi-pencil-fill me-2"></i>Editar Psicólogo
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <h5 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-4">
                    <i class="bi bi-person-vcard-fill me-2"></i>Información Personal y Contacto
                </h5>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Correo Electrónico</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-envelope-at text-info"></i> {{ $psicologo->persona->user->email ?? 'No asignado' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Teléfono</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-telephone text-success"></i> {{ $psicologo->persona->telefono ?? 'No especificado' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Fecha de Nacimiento</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-calendar-event text-warning"></i>
                            @if(isset($psicologo->persona->fecha_nacimiento))
                                {{ \Carbon\Carbon::parse($psicologo->persona->fecha_nacimiento)->format('d/m/Y') }}
                            @else
                                No especificada
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Género</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-gender-ambiguous text-primary"></i> {{ ucfirst($psicologo->persona->genero ?? 'No especificado') }}
                        </div>
                    </div>
                </div>

                <div class="mt-5 text-start">
                    <a href="{{ route('admin.psicologos.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
                        <i class="bi bi-arrow-left me-2"></i>Volver al Directorio
                    </a>
                </div>
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
