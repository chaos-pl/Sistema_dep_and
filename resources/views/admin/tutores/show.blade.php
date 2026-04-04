@extends('layouts.app')

@section('title', 'Detalle de Tutor - PROMETEO')
@section('page-title', 'Expediente del Tutor')
@section('page-subtitle', 'Información detallada y grupos asignados')

@section('content')
    <div class="row justify-content-center g-4">

        <div class="col-xl-4 col-lg-5">
            <div class="app-card p-4 border-0 shadow-sm rounded-4 h-100 text-center">
                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 100px; height: 100px;">
                    <i class="bi bi-person-video3" style="font-size: 3rem;"></i>
                </div>
                <h4 class="fw-black text-body mb-1">
                    {{ $tutor->persona->nombre ?? 'N/A' }} {{ $tutor->persona->apellido_paterno ?? '' }}
                </h4>
                <p class="text-body-secondary mb-3">Tutor Institucional</p>

                <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 mb-4 fw-bold shadow-sm">
                    Nº Empleado: {{ $tutor->numero_empleado }}
                </span>

                <div class="d-grid gap-2">
                    <a href="{{ route('admin.tutores.edit', $tutor->id) }}" class="btn btn-warning fw-bold rounded-pill text-dark shadow-sm">
                        <i class="bi bi-pencil-fill me-2"></i>Editar Tutor
                    </a>
                </div>
            </div>
        </div>

        <div class="col-xl-8 col-lg-7">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <h5 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-4">
                    <i class="bi bi-card-heading me-2"></i>Información Personal y Contacto
                </h5>

                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Correo Electrónico</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-envelope-at text-primary"></i> {{ $tutor->persona->user->email ?? 'No asignado' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Teléfono</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-telephone text-success"></i> {{ $tutor->persona->telefono ?? 'No especificado' }}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Fecha de Nacimiento</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-calendar-event text-warning"></i>
                            @if(isset($tutor->persona->fecha_nacimiento))
                                {{ \Carbon\Carbon::parse($tutor->persona->fecha_nacimiento)->format('d/m/Y') }}
                            @else
                                No especificada
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <small class="text-body-secondary d-block fw-bold mb-1 text-uppercase" style="letter-spacing: 0.5px;">Género</small>
                        <div class="d-flex align-items-center gap-2 text-body fw-medium">
                            <i class="bi bi-gender-ambiguous text-info"></i> {{ ucfirst($tutor->persona->genero ?? 'No especificado') }}
                        </div>
                    </div>
                </div>

                <h5 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-4 mt-5">
                    <i class="bi bi-people-fill me-2"></i>Grupos Asignados
                </h5>

                <div class="bg-body-tertiary rounded-4 p-4 text-center border border-secondary border-opacity-10">
                    <div class="display-4 fw-black text-primary mb-2">{{ $tutor->grupos_count ?? 0 }}</div>
                    <p class="text-body-secondary mb-0 fw-medium">Grupos actualmente gestionados por este tutor.</p>
                </div>
            </div>
        </div>

        <div class="col-12 mt-4 text-start">
            <a href="{{ route('admin.tutores.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
                <i class="bi bi-arrow-left me-2"></i>Volver al Directorio
            </a>
        </div>
    </div>
@endsection
