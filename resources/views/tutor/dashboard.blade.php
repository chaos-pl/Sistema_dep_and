@extends('layouts.app')

@section('title', 'Dashboard Tutor - PROMETEO')
@section('page-title', 'Panel Académico')
@section('page-subtitle', 'Seguimiento general de grupos asignados')

@php
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    $granimPalettes = match($userAccentColor) {
        'blue' => "
            [ { color: '#1e3a8a', pos: 0 }, { color: '#2563eb', pos: .5 }, { color: '#93c5fd', pos: 1 } ],
            [ { color: '#2563eb', pos: 0 }, { color: '#0284c7', pos: .5 }, { color: '#38bdf8', pos: 1 } ],
            [ { color: '#0f172a', pos: 0 }, { color: '#1d4ed8', pos: .5 }, { color: '#3b82f6', pos: 1 } ]
        ",
        'green' => "
            [ { color: '#064e3b', pos: 0 }, { color: '#059669', pos: .5 }, { color: '#6ee7b7', pos: 1 } ],
            [ { color: '#059669', pos: 0 }, { color: '#0d9488', pos: .5 }, { color: '#2dd4bf', pos: 1 } ],
            [ { color: '#022c22', pos: 0 }, { color: '#047857', pos: .5 }, { color: '#10b981', pos: 1 } ]
        ",
        'pink' => "
            [ { color: '#831843', pos: 0 }, { color: '#db2777', pos: .5 }, { color: '#f9a8d4', pos: 1 } ],
            [ { color: '#db2777', pos: 0 }, { color: '#e11d48', pos: .5 }, { color: '#f43f5e', pos: 1 } ],
            [ { color: '#4c0519', pos: 0 }, { color: '#be185d', pos: .5 }, { color: '#ec4899', pos: 1 } ]
        ",
        default => "
            [ { color: '#4c1d95', pos: 0 }, { color: '#7c3aed', pos: .5 }, { color: '#a78bfa', pos: 1 } ],
            [ { color: '#7c3aed', pos: 0 }, { color: '#c026d3', pos: .5 }, { color: '#db2777', pos: 1 } ],
            [ { color: '#1e1b4b', pos: 0 }, { color: '#6d28d9', pos: .5 }, { color: '#8b5cf6', pos: 1 } ]
        "
    };
@endphp

@push('styles')
    <style>
        .hover-elevate { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease !important; border: 1px solid transparent; }
        .hover-elevate:hover { transform: translateY(-6px); box-shadow: 0 15px 35px rgba(0,0,0,0.1) !important; border-color: var(--app-primary-soft); }

        .bg-welcome-tutor {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary);
        }
        .bg-welcome-tutor::after {
            content: '\F4DA';
            font-family: "bootstrap-icons";
            position: absolute;
            top: -10%;
            right: -5%;
            font-size: 15rem;
            color: #ffffff;
            opacity: 0.08;
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 2;
        }

        #granim-canvas-tutor {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .banner-content { position: relative; z-index: 3; }
        .anime-item { opacity: 0; transform: translateY(20px); }
        .cursor-pointer { cursor: pointer; }
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-tutor p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-tutor"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm" style="color: var(--app-primary) !important;">
                            <i class="bi bi-building me-1"></i> Vista Académica
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="mb-0 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            Consulta el avance general de evaluaciones completadas y alumnos en riesgo de tus grupos asignados.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('tutor.grupos.index') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm hover-elevate" style="color: var(--app-primary) !important;">
                            <i class="bi bi-people-fill me-2"></i>Ver Mis Grupos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 bg-body-tertiary hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Grupos Asignados</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body count-up" data-value="{{ $totalGrupos ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Total de grupos a tu cargo.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 bg-body-tertiary hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Evaluaciones Completas</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body count-up" data-value="{{ $completadas ?? 0 }}">0</h2>
                <p class="text-body-secondary mb-0 small">Instrumentos finalizados con éxito.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 bg-body-tertiary hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Evaluaciones Pendientes</h6>
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
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
                    <div class="bg-secondary bg-opacity-10 text-body-secondary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
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
                            <th class="rounded-start-3 py-3 px-3 text-body-secondary border-0">Grupo</th>
                            <th class="py-3 text-body-secondary border-0 text-center">Periodo</th>
                            <th class="py-3 text-center text-success border-0"><i class="bi bi-check-circle-fill me-1"></i> Fin.</th>
                            <th class="py-3 text-center text-warning-emphasis border-0"><i class="bi bi-x-circle-fill me-1"></i> Pend.</th>
                            <th class="rounded-end-3 py-3 px-3 text-end text-body-secondary border-0">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($grupos ?? [] as $grupo)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="fw-bold text-body border-0 px-3">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-folder-fill text-primary"></i> {{ $grupo->nombre }}
                                    </div>
                                </td>
                                <td class="border-0 text-center"><span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 shadow-sm rounded-pill px-3">{{ $grupo->periodo }}</span></td>
                                <td class="text-center fw-black text-success border-0 fs-5">{{ $grupo->completadas ?? 0 }}</td>
                                <td class="text-center fw-black text-warning-emphasis border-0 fs-5">{{ $grupo->abandonadas ?? 0 }}</td>
                                <td class="text-end border-0 px-3">
                                    <a href="{{ route('tutor.grupos.show', $grupo->id) }}" class="btn btn-sm btn-light border text-primary rounded-pill shadow-sm fw-bold px-3 hover-elevate">
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
            <div class="app-card p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 bg-body-tertiary">
                <div class="mb-4 d-flex align-items-center gap-3">
                    <div class="bg-body text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
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

                <h6 class="fw-bold text-body-secondary text-uppercase mb-3 mt-4" style="font-size: 0.75rem; letter-spacing: 1px;">Acciones Rápidas</h6>
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('tutor.grupos.index') }}" class="btn btn-light w-100 border border-secondary border-opacity-10 text-start d-flex flex-column gap-2 p-3 hover-elevate bg-body rounded-4 shadow-sm">
                            <i class="bi bi-journal-check text-primary fs-4"></i>
                            <span class="fw-bold text-body" style="font-size: 0.85rem;">Ver Expedientes</span>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('perfil.index') }}" class="btn btn-light w-100 border border-secondary border-opacity-10 text-start d-flex flex-column gap-2 p-3 hover-elevate bg-body rounded-4 shadow-sm">
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
            // Animaciones de entrada
            if(typeof anime !== 'undefined') {
                anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 1000 });

                const counters = document.querySelectorAll('.count-up');
                counters.forEach(counter => {
                    const endValue = parseInt(counter.getAttribute('data-value'), 10) || 0;
                    anime({ targets: counter, innerHTML: [0, endValue], easing: 'easeOutExpo', round: 1, duration: 2500, delay: 500 });
                });
            }

            // Granim.js para el Banner
            if (document.getElementById('granim-canvas-tutor') && typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-tutor',
                    direction: 'left-right',
                    isPausedWhenNotInView: true,
                    states : {
                        "default-state": {
                            gradients: [
                                {!! $granimPalettes !!}
                            ],
                            transitionSpeed: 7000
                        }
                    }
                });
            }
        });
    </script>
@endpush
