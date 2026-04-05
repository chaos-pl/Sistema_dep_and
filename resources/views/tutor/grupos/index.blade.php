@extends('layouts.app')

@section('title', 'Mis Grupos - PROMETEO')
@section('page-title', 'Mis Grupos')
@section('page-subtitle', 'Consulta y administra los grupos asignados a tu cargo')

@php
    // Obtenemos el color elegido por el usuario (Morado por defecto)
    $userAccentColor = auth()->user()->appearance_settings['accent_color'] ?? 'purple';

    // Definimos las paletas complejas de 3 colores
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
        .anime-item { opacity: 0; transform: translateY(20px); }

        .hover-elevate { transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1), box-shadow 0.3s ease; border: 1px solid transparent; }
        .hover-elevate:hover { transform: translateY(-5px); box-shadow: 0 10px 24px rgba(0,0,0,0.08) !important; border-color: var(--app-primary-soft) !important; }

        .grupos-hero {
            position: relative;
            overflow: hidden;
            background-color: var(--app-primary);
        }
        .grupos-hero::after {
            content: '\F4DA';
            font-family: "bootstrap-icons";
            position: absolute;
            top: -10%;
            right: -5%;
            font-size: 14rem;
            color: #ffffff;
            opacity: 0.08;
            transform: rotate(-15deg);
            pointer-events: none;
            z-index: 2;
        }

        #granim-canvas-grupos {
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            z-index: 0;
            border-radius: inherit;
        }

        .banner-content { position: relative; z-index: 3; }

        .grupo-card { transition: all 0.25s ease; }
        .grupo-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 35px rgba(0, 0, 0, 0.08);
            border-color: var(--app-primary-soft) !important;
        }

        .grupo-icon {
            width: 54px; height: 54px; border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
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
            <div class="app-card grupos-hero p-4 p-md-5 rounded-4 border-0 shadow-lg text-white">

                <canvas id="granim-canvas-grupos"></canvas>

                <div class="row align-items-center banner-content">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-primary border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm" style="color: var(--app-primary) !important;">
                            <i class="bi bi-people-fill me-1"></i> Gestión Académica
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.15rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">Mis Grupos Asignados</h2>
                        <p class="mb-3 text-white text-opacity-90 fs-5" style="text-shadow: 0 1px 2px rgba(0,0,0,0.1);">
                            Desde aquí puedes consultar el detalle de cada grupo y registrar estudiantes vinculados a tu carga académica.
                        </p>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge bg-white bg-opacity-25 text-white border border-white border-opacity-25 rounded-pill px-3 py-2 shadow-sm" style="backdrop-filter: blur(8px);">
                                <i class="bi bi-collection-fill me-1 text-white"></i> Total de grupos: {{ method_exists($grupos, 'total') ? $grupos->total() : $grupos->count() }}
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('tutor.dashboard') }}" class="btn btn-light rounded-pill fw-bold px-4 py-2 shadow-sm hover-elevate" style="color: var(--app-primary) !important;">
                            <i class="bi bi-arrow-left me-1"></i> Volver al panel
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card grupo-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: .5px;">Grupos</h6>
                    <div class="grupo-icon bg-primary bg-opacity-10 text-primary shadow-sm">
                        <i class="bi bi-folder-fill fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-1 text-body">{{ method_exists($grupos, 'total') ? $grupos->total() : $grupos->count() }}</h3>
                <p class="text-body-secondary mb-0 small">Grupos en tu expediente.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card grupo-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: .5px;">Estudiantes</h6>
                    <div class="grupo-icon bg-success bg-opacity-10 text-success shadow-sm">
                        <i class="bi bi-person-lines-fill fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-1 text-body">{{ $grupos->sum('estudiantes_count') }}</h3>
                <p class="text-body-secondary mb-0 small">Estudiantes en tus grupos.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card grupo-card bg-body-tertiary p-4 h-100 border border-secondary border-opacity-10 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: .5px;">Estatus</h6>
                    <div class="grupo-icon bg-info bg-opacity-10 text-info shadow-sm">
                        <i class="bi bi-clipboard-data-fill fs-4"></i>
                    </div>
                </div>
                <h3 class="fw-black mb-1 text-body">Activo</h3>
                <p class="text-body-secondary mb-0 small">Accede al detalle del grupo.</p>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-secondary bg-opacity-10 text-body-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-table fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-1 text-body">Listado de Grupos</h4>
                            <p class="text-body-secondary mb-0 small">Selecciona un grupo para ver sus estudiantes o registrar expedientes.</p>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="info-chip border border-primary border-opacity-25 shadow-sm">
                            <i class="bi bi-collection-fill"></i>
                            {{ method_exists($grupos, 'total') ? $grupos->total() : $grupos->count() }} Grupos
                        </span>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="rounded-start-3 py-3 text-body-secondary border-0">Grupo</th>
                            <th class="py-3 text-body-secondary border-0">Periodo</th>
                            <th class="py-3 text-center text-body-secondary border-0">Estudiantes</th>
                            <th class="rounded-end-3 py-3 text-end text-body-secondary border-0">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($grupos as $grupo)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="fw-bold text-body border-0">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-folder2-open text-primary"></i>
                                        <span>{{ $grupo->nombre ?? 'Sin nombre' }}</span>
                                    </div>
                                </td>
                                <td class="border-0">
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2">
                                        {{ $grupo->periodo ?? 'Sin periodo' }}
                                    </span>
                                </td>
                                <td class="text-center border-0">
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-person-lines-fill me-1"></i> {{ $grupo->estudiantes_count }}
                                    </span>
                                </td>
                                <td class="text-end border-0">
                                    <a href="{{ route('tutor.grupos.show', $grupo->id) }}" class="btn btn-sm btn-light border border-secondary border-opacity-10 text-primary rounded-pill fw-bold px-3 shadow-sm hover-elevate">
                                        <i class="bi bi-arrow-right-circle me-1"></i> Ver detalle
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-body-secondary border-0">
                                    <i class="bi bi-folder-x fs-2 d-block mb-2 opacity-25"></i>
                                    No tienes grupos asignados en este momento.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($grupos, 'links'))
                    <div class="mt-4">
                        {{ $grupos->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/granim.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if(typeof anime !== 'undefined') {
                anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(120), easing: 'easeOutExpo', duration: 900 });
            }

            if (document.getElementById('granim-canvas-grupos') && typeof Granim !== 'undefined') {
                new Granim({
                    element: '#granim-canvas-grupos',
                    direction: 'left-right',
                    isPausedWhenNotInView: true,
                    states : {
                        "default-state": {
                            gradients: [ {!! $granimPalettes !!} ],
                            transitionSpeed: 7000
                        }
                    }
                });
            }
        });
    </script>
@endpush
