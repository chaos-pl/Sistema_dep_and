@extends('layouts.app')

@section('title', 'Dashboard Tutor - PROMETEO')
@section('page-title', 'Panel Académico')
@section('page-subtitle', 'Seguimiento general de grupos asignados')

@php
    // Obtenemos el color de acento del usuario. Si no tiene, usamos primary (morado).
    $userAccentColor = auth()->user()->appearance['accent_color'] ?? 'primary';

    // Mapeamos el color de acento a las clases de Bootstrap para la tarjeta principal
    $bannerClasses = match($userAccentColor) {
        'red' => 'bg-danger bg-gradient',
        'green' => 'bg-success bg-gradient',
        'blue' => 'bg-info bg-gradient text-dark', // El info suele ser claro
        'orange' => 'bg-warning bg-gradient text-dark',
        'pink' => 'bg-pink bg-gradient', // Si lo definiste en CSS
        default => 'bg-primary bg-gradient'
    };

    // Ajustamos el color del texto del banner dependiendo de si el fondo es claro u oscuro
    $bannerTextClass = in_array($userAccentColor, ['blue', 'orange']) ? 'text-dark' : 'text-white';
@endphp

@push('styles')
    <style>
        /* Efecto de elevación para tarjetas interactivas */
        .hover-elevate {
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease !important;
            border: 1px solid transparent;
        }
        .hover-elevate:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important;
            border-color: var(--app-primary-soft);
        }

        /* Fondo decorativo de la tarjeta de bienvenida adaptativo */
        .bg-welcome-tutor {
            position: relative;
            overflow: hidden;
        }
        .bg-welcome-tutor::after {
            content: '\F4DA'; /* Icono de personas */
            font-family: "bootstrap-icons";
            position: absolute;
            top: -10%;
            right: -5%;
            font-size: 15rem;
            color: inherit;
            opacity: 0.1;
            transform: rotate(-15deg);
            pointer-events: none;
        }

        .anime-item { opacity: 0; transform: translateY(20px); }
        .cursor-pointer { cursor: pointer; }
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-tutor p-4 p-md-5 rounded-4 border-0 shadow-lg {{ $bannerClasses }} {{ $bannerTextClass }}">
                <div class="row align-items-center position-relative z-1">
                    <div class="col-lg-8">
                        <span class="badge bg-body text-body border rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-building me-1"></i> Vista Académica
                        </span>
                        <h2 class="fw-black mb-2" style="font-size: 2.2rem;">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="mb-0 opacity-75 fs-5">
                            Consulta el avance general de evaluaciones completadas y alumnos en riesgo de tus grupos asignados.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('tutor.grupos.index') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm">
                            <i class="bi bi-people-fill me-2"></i>Ver Mis Grupos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Grupos Asignados</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body count-up" data-value="{{ $totalGrupos ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Total de grupos a tu cargo.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Evaluaciones Completas</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body count-up" data-value="{{ $completadas ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Instrumentos finalizados con éxito.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4 bg-body-tertiary">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Evaluaciones Pendientes</h6>
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-hourglass-bottom fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body count-up" data-value="{{ $abandonadas ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Alumnos que no han concluido.</p>
            </div>
        </div>

        <div class="col-12 col-xl-7 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="bg-secondary bg-opacity-10 text-body-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-table fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Resumen por Grupo</h4>
                        <p class="text-body-secondary mb-0 small">Vista general agregada por grupo y periodo escolar.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="rounded-start-3 py-3 text-body-secondary border-0">Grupo</th>
                            <th class="py-3 text-body-secondary border-0">Periodo</th>
                            <th class="py-3 text-center text-success border-0"><i class="bi bi-check-circle-fill me-1"></i> Fin.</th>
                            <th class="py-3 text-center text-warning-emphasis border-0"><i class="bi bi-x-circle-fill me-1"></i> Pend.</th>
                            <th class="rounded-end-3 py-3 text-end text-body-secondary border-0">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($grupos ?? [] as $grupo)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="fw-bold text-body border-0">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-folder-fill text-primary"></i> {{ $grupo->nombre }}
                                    </div>
                                </td>
                                <td class="border-0"><span class="badge bg-body-tertiary text-body-secondary border rounded-pill">{{ $grupo->periodo }}</span></td>
                                <td class="text-center fw-bold text-success border-0">{{ $grupo->completadas ?? 0 }}</td>
                                <td class="text-center fw-bold text-warning-emphasis border-0">{{ $grupo->abandonadas ?? 0 }}</td>
                                <td class="text-end border-0">
                                    <a href="{{ route('tutor.grupos.show', $grupo->id) }}" class="btn btn-sm btn-light border text-primary rounded-pill shadow-sm fw-bold px-3">
                                        Entrar <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-body-secondary border-0">
                                    <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i> No tienes grupos asignados en este momento.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-5 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4 bg-body-tertiary">
                <div class="mb-4 d-flex align-items-center gap-3">
                    <div class="bg-body text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-bell-fill fs-5"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-0 text-body">Centro de Alertas</h4>
                        <p class="text-body-secondary mb-0 small">Notificaciones de tus grupos.</p>
                    </div>
                </div>

                <div class="d-flex flex-column gap-3 mb-4">

                    <div class="p-3 bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 d-flex align-items-center justify-content-between hover-elevate cursor-pointer" onclick="window.location='{{ route('tutor.grupos.index') }}'">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-exclamation-triangle text-warning-emphasis fs-3"></i>
                            <div>
                                <span class="fw-bold text-warning-emphasis d-block" style="line-height: 1;">Tamizajes Pendientes</span>
                                <small class="text-warning-emphasis text-opacity-75">Alumnos faltantes de evaluación</small>
                            </div>
                        </div>
                        <span class="badge bg-warning text-dark rounded-pill fs-6 shadow-sm">{{ $abandonadas ?? 0 }}</span>
                    </div>

                    <div class="p-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 d-flex align-items-center justify-content-between hover-elevate cursor-pointer" onclick="window.location='{{ route('tutor.grupos.index') }}'">
                        <div class="d-flex align-items-center gap-3">
                            <i class="bi bi-heart-pulse text-danger fs-3"></i>
                            <div>
                                <span class="fw-bold text-danger d-block" style="line-height: 1;">Posible Riesgo</span>
                                <small class="text-danger text-opacity-75">Casos canalizados a psicología</small>
                            </div>
                        </div>
                        <span class="badge bg-danger rounded-pill fs-6 shadow-sm">{{ $alumnosRiesgo ?? 0 }}</span>
                    </div>

                </div>

                <h6 class="fw-bold text-body-secondary text-uppercase mb-3 mt-2" style="font-size: 0.75rem; letter-spacing: 1px;">Acciones Rápidas</h6>
                <div class="row g-2">
                    <div class="col-6">
                        <a href="{{ route('tutor.grupos.index') }}" class="btn btn-light w-100 border text-start d-flex flex-column gap-2 p-3 hover-elevate bg-body rounded-4">
                            <i class="bi bi-journal-check text-primary fs-4"></i>
                            <span class="fw-bold text-body" style="font-size: 0.85rem;">Ver Expedientes</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('perfil.index') }}" class="btn btn-light w-100 border text-start d-flex flex-column gap-2 p-3 hover-elevate bg-body rounded-4">
                            <i class="bi bi-person-gear text-success fs-4"></i>
                            <span class="fw-bold text-body" style="font-size: 0.85rem;">Mi Cuenta</span>
                        </a>
                    </div>
                </div>

            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 1000 });

            const counters = document.querySelectorAll('.count-up');
            counters.forEach(counter => {
                const endValue = parseInt(counter.getAttribute('data-value'), 10) || 0;
                anime({ targets: counter, innerHTML: [0, endValue], easing: 'easeOutExpo', round: 1, duration: 2500, delay: 500 });
            });
        });
    </script>
@endpush
