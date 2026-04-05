@extends('layouts.app')

@section('title', 'Dashboard Psicólogo - PROMETEO')
@section('page-title', 'Panel Clínico')
@section('page-subtitle', 'Seguimiento de alertas, riesgos y actividad diagnóstica')

@php
    // 1. Obtenemos el color acento del usuario
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    // 2. Definimos las paletas COMPLEJAS (3 paradas de color) según el acento elegido
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
        " // Morado (Purple) por defecto
    };
@endphp

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }

        .hover-elevate { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 12px 24px rgba(0,0,0,0.08) !important; border-color: var(--app-primary-soft) !important; }

        .bg-welcome-psicologo {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary); /* Usamos el color base del sistema como fallback */
        }

        .bg-welcome-psicologo::after {
            content: '\F4B8'; font-family: "bootstrap-icons"; position: absolute;
            top: -10%; right: -5%; font-size: 15rem; color: #ffffff;
            opacity: 0.08; transform: rotate(-15deg); pointer-events: none;
            z-index: 2;
        }

        #granim-canvas {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .banner-content {
            position: relative;
            z-index: 3;
        }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-psicologo p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm" style="color: var(--app-primary) !important;">
                            <i class="bi bi-heart-pulse-fill me-1"></i> Vista Clínica Integral
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem;">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="mb-0 text-white text-opacity-75 fs-5">Supervisa alertas generadas, resultados clínicos de riesgo y el avance de diagnósticos.</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('alertas.index') }}" class="btn btn-light rounded-pill text-primary fw-bold px-4 py-2 shadow-sm hover-elevate" style="color: var(--app-primary) !important;">
                            <i class="bi bi-exclamation-triangle-fill me-2 text-warning"></i>Ver Bandeja de Alertas
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Alertas Pendientes</h6>
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-bell-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalAlertasPendientes ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Casos nuevos en espera de revisión.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Atendidas</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalAlertasAtendidas ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Alertas cerradas exitosamente.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Tus Diagnósticos</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-file-earmark-medical-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalDiagnosticosPropios ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Registrados bajo tu cuenta clínica.</p>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 anime-item">
            <div class="app-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4 hover-elevate">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size: 0.8rem; letter-spacing: 0.5px;">Casos Severos Totales</h6>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:48px;height:48px;">
                        <i class="bi bi-activity fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalCasosSeveros ?? 0 }}</h2>
                <p class="text-body-secondary mb-0 small">Resultados de alto riesgo global.</p>
            </div>
        </div>

        <div class="col-12 col-xl-6 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:50px;height:50px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Alertas Recientes</h4>
                        <p class="text-body-secondary mb-0 small">Últimos casos clínicos reportados por el sistema.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Test</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Riesgo</th>
                            <th class="py-3 px-4 rounded-end-3 text-body-secondary fw-bold border-0 text-center">Estado</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($alertas as $alerta)
                            @php
                                $riesgo = $alerta->evaluacion->resultadoClinico->nivel_riesgo ?? 'n/d';
                                $riesgoColor = match($riesgo) { 'severo' => 'bg-danger text-danger', 'moderado' => 'bg-warning text-warning-emphasis', 'leve' => 'bg-info text-info', default => 'bg-success text-success' };
                            @endphp
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-4 py-3 border-0 fw-bold text-body">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <span>{{ $alerta->evaluacion->estudiante->persona->nombre ?? 'Sin nombre' }}</span>
                                    </div>
                                </td>
                                <td class="text-center py-3 border-0">
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm">
                                        {{ strtoupper($alerta->evaluacion->instrumento->acronimo ?? 'N/D') }}
                                    </span>
                                </td>
                                <td class="text-center py-3 border-0">
                                    <span class="badge bg-opacity-10 border border-opacity-25 rounded-pill px-3 py-1 shadow-sm {{ $riesgoColor }}">{{ ucfirst($riesgo) }}</span>
                                </td>
                                <td class="text-center px-4 py-3 border-0">
                                    <span class="badge rounded-pill px-3 py-1 shadow-sm fw-bold @if($alerta->estado === 'atendida') bg-success @elseif($alerta->estado === 'asignada_psicologo') bg-info text-dark @else bg-warning text-dark @endif">
                                        {{ str_replace('_', ' ', ucfirst($alerta->estado)) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-body-secondary py-5 border-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-check2-circle fs-1 text-success opacity-50 mb-3"></i>
                                        <span>Bandeja limpia. No hay alertas registradas.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-6 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:50px;height:50px;">
                        <i class="bi bi-clipboard2-pulse-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Casos de Alto Riesgo</h4>
                        <p class="text-body-secondary mb-0 small">Resultados severos o moderados que exigen atención.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Test</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Puntaje</th>
                            <th class="py-3 px-4 rounded-end-3 text-body-secondary fw-bold border-0 text-center">Nivel</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($resultados as $resultado)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-4 py-3 border-0 fw-bold text-body">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-exclamation-circle text-danger"></i>
                                        <span>{{ $resultado->evaluacion->estudiante->persona->nombre ?? 'Sin nombre' }}</span>
                                    </div>
                                </td>
                                <td class="text-center py-3 border-0">
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm">
                                        {{ strtoupper($resultado->evaluacion->instrumento->acronimo ?? 'N/D') }}
                                    </span>
                                </td>
                                <td class="text-center py-3 border-0 fs-5 fw-black text-body">{{ $resultado->puntaje_total }}</td>
                                <td class="text-center px-4 py-3 border-0">
                                    <span class="badge rounded-pill px-3 py-1 fw-bold shadow-sm @if($resultado->nivel_riesgo === 'severo') bg-danger @else bg-warning text-dark @endif">
                                        {{ ucfirst($resultado->nivel_riesgo) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-body-secondary py-5 border-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-shield-check fs-1 text-success opacity-50 mb-3"></i>
                                        <span>No hay casos moderados o severos.</span>
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
    <script type="module">
        document.addEventListener('DOMContentLoaded', () => {
            // Animación de entrada
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(120), easing: 'easeOutExpo', duration: 900 });

            // Granim.js: Insertamos el String dinámico de PHP con las paletas de 3 colores
            if (document.getElementById('granim-canvas') && window.Granim) {
                var granimInstance = new window.Granim({
                    element: '#granim-canvas',
                    direction: 'left-right', // Mantenemos el flujo horizontal de tu imagen
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
