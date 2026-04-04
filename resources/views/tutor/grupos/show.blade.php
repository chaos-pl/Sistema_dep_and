@extends('layouts.app')

@section('title', 'Detalle del Grupo - PROMETEO')
@section('page-title', 'Detalle del Grupo')
@section('page-subtitle', 'Consulta y administra a los estudiantes del grupo')

@php
    $userAccentColor = auth()->user()->appearance['accent_color'] ?? 'primary';
    $bannerClasses = match($userAccentColor) {
        'red' => 'bg-danger bg-gradient text-white',
        'green' => 'bg-success bg-gradient text-white',
        'blue' => 'bg-info bg-gradient text-dark',
        'orange' => 'bg-warning bg-gradient text-dark',
        'pink' => 'bg-pink bg-gradient text-white',
        default => 'bg-primary bg-gradient text-white'
    };
@endphp

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }

        .grupo-hero {
            position: relative;
            overflow: hidden;
        }
        .grupo-hero::after {
            content: '\F4DA';
            font-family: "bootstrap-icons";
            position: absolute;
            top: -10%;
            right: -5%;
            font-size: 14rem;
            color: inherit;
            opacity: 0.1;
            transform: rotate(-15deg);
            pointer-events: none;
        }

        .info-chip {
            display: inline-flex; align-items: center; gap: .45rem;
            padding: .45rem .9rem; border-radius: 999px;
            font-size: .85rem; font-weight: 600;
            background: var(--app-primary-soft); color: var(--app-primary-dark);
        }
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-12 anime-item">
            <div class="app-card grupo-hero p-4 p-md-5 rounded-4 border-0 shadow-lg {{ $bannerClasses }}">
                <div class="row align-items-center position-relative z-1">
                    <div class="col-lg-8">
                        <span class="badge bg-body text-body border rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-people-fill me-1 text-primary"></i> Grupo Asignado
                        </span>
                        <h2 class="fw-black mb-2" style="font-size: 2.15rem;">
                            {{ $grupo->nombre ?? 'Grupo sin nombre' }}
                        </h2>
                        <p class="mb-3 opacity-75 fs-5">
                            Administra los estudiantes vinculados a este grupo y mantén actualizado su expediente académico.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-body-tertiary text-body border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                <i class="bi bi-calendar-event me-1 text-info"></i> Periodo: {{ $grupo->periodo ?? 'Sin periodo' }}
                            </span>
                            <span class="badge bg-body-tertiary text-body border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                <i class="bi bi-mortarboard-fill me-1 text-warning"></i> Estudiantes: {{ $grupo->estudiantes->count() }}
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <div class="d-flex flex-column flex-lg-row justify-content-lg-end gap-2">
                            <a href="{{ route('tutor.grupos.index') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm">
                                <i class="bi bi-arrow-left me-1"></i> Volver
                            </a>
                            <a href="{{ route('tutor.grupos.estudiantes.create', $grupo->id) }}" class="btn btn-warning rounded-pill text-dark fw-bold px-4 py-2 shadow-sm">
                                <i class="bi bi-person-plus-fill me-1"></i> Registrar Estudiante
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: .5px;">Nombre del Grupo</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:45px;height:45px;">
                        <i class="bi bi-folder-fill fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-black mb-1 text-body">{{ $grupo->nombre ?? 'Sin nombre' }}</h4>
                <p class="text-body-secondary mb-0 small">Identificador académico.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: .5px;">Periodo</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:45px;height:45px;">
                        <i class="bi bi-calendar-range-fill fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-black mb-1 text-body">{{ $grupo->periodo ?? 'Sin periodo' }}</h4>
                <p class="text-body-secondary mb-0 small">Ciclo escolar activo.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: .5px;">Estudiantes</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:45px;height:45px;">
                        <i class="bi bi-person-lines-fill fs-5"></i>
                    </div>
                </div>
                <h4 class="fw-black mb-1 text-body">{{ $grupo->estudiantes->count() }}</h4>
                <p class="text-body-secondary mb-0 small">Total de expedientes en el grupo.</p>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-secondary bg-opacity-10 text-body-secondary rounded-circle d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="bi bi-table fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-1 text-body">Lista de Estudiantes</h4>
                            <p class="text-body-secondary mb-0 small">Consulta, edita y supervisa los estudiantes vinculados a este grupo.</p>
                        </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="info-chip border border-primary border-opacity-25 shadow-sm">
                            <i class="bi bi-collection-fill"></i>
                            Total: {{ $grupo->estudiantes->count() }}
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="rounded-start-3 py-3 text-body-secondary border-0">Nombre Completo</th>
                            <th class="py-3 text-body-secondary border-0">Matrícula</th>
                            <th class="py-3 text-body-secondary border-0">Correo Institucional</th>
                            <th class="py-3 text-center text-body-secondary border-0">Código Anónimo</th>
                            <th class="rounded-end-3 py-3 text-end text-body-secondary border-0">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($grupo->estudiantes as $estudiante)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="fw-bold text-body border-0">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <span>
                                            {{ $estudiante->persona->nombre ?? '' }}
                                            {{ $estudiante->persona->apellido_paterno ?? '' }}
                                            {{ $estudiante->persona->apellido_materno ?? '' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="border-0">
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                        {{ $estudiante->matricula }}
                                    </span>
                                </td>
                                <td class="text-body-secondary fw-medium border-0">
                                    <i class="bi bi-envelope-at me-1 opacity-50"></i> {{ $estudiante->persona->user->email ?? 'Sin correo' }}
                                </td>
                                <td class="text-center border-0">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold">
                                        <i class="bi bi-incognito me-1"></i> {{ $estudiante->codigo_anonimo }}
                                    </span>
                                </td>
                                <td class="text-end border-0">
                                    <div class="d-inline-flex gap-2">
                                        <a href="{{ route('tutor.estudiantes.edit', $estudiante->id) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar Expediente">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-body-secondary border-0">
                                    <i class="bi bi-person-x fs-2 d-block mb-2 opacity-25"></i>
                                    Este grupo todavía no tiene estudiantes registrados.
                                    <div class="mt-3">
                                        <a href="{{ route('tutor.grupos.estudiantes.create', $grupo->id) }}" class="btn btn-primary rounded-pill px-4 shadow-sm">
                                            <i class="bi bi-person-plus-fill me-1"></i> Registrar primer estudiante
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
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
                translateY: [30, 0],
                opacity: [0, 1],
                delay: anime.stagger(120),
                easing: 'easeOutExpo',
                duration: 900
            });
        });
    </script>
@endpush
