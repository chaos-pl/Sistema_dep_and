@extends('layouts.app')

@section('title', 'Dashboard Tutor - PROMETEO')
@section('page-title', 'Panel Académico')
@section('page-subtitle', 'Seguimiento general de grupos asignados')

@push('styles')
    <style>
        .bg-welcome-tutor { background: linear-gradient(135deg, #059669 0%, #10b981 100%); position: relative; overflow: hidden; }
        .bg-welcome-tutor::after {
            content: '\F4DA'; /* Icono de personas */
            font-family: "bootstrap-icons"; position: absolute; top: -10%; right: -5%; font-size: 15rem; color: white; opacity: 0.05; transform: rotate(-15deg); pointer-events: none;
        }
        .anime-item { opacity: 0; transform: translateY(20px); }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card bg-welcome-tutor p-4 p-md-5 text-white rounded-4 border-0 shadow-lg">
                <div class="row align-items-center position-relative z-1">
                    <div class="col-lg-8">
                        <span class="badge bg-white text-success border border-white border-opacity-25 rounded-pill px-3 py-2 mb-3 fw-bold shadow-sm">
                            <i class="bi bi-building me-1"></i> Vista Académica
                        </span>
                        <h2 class="fw-black mb-2 text-white" style="font-size: 2.2rem;">Bienvenido, {{ auth()->user()->name }}</h2>
                        <p class="mb-0 text-white text-opacity-75 fs-5">
                            Consulta el avance general de evaluaciones completadas y abandonadas por tus grupos asignados.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-4 mt-lg-0">
                        <a href="{{ route('grupos.index') }}" class="btn btn-light text-success rounded-pill fw-bold px-4 py-2 shadow-sm">
                            <i class="bi bi-people-fill me-2"></i>Ver Mis Grupos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Grupos Asignados</h6>
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-people-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-dark count-up" data-value="{{ $totalGrupos ?? 0 }}">0</h2>
                <p class="text-muted mb-0 small">Total de grupos a tu cargo.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Completadas</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-dark count-up" data-value="{{ $completadas ?? 0 }}">0</h2>
                <p class="text-muted mb-0 small">Evaluaciones finalizadas con éxito.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted fw-bold mb-0 text-uppercase" style="font-size: 0.8rem; letter-spacing: 0.5px;">Abandonadas</h6>
                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                        <i class="bi bi-hourglass-bottom fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-dark count-up" data-value="{{ $abandonadas ?? 0 }}">0</h2>
                <p class="text-muted mb-0 small">Evaluaciones no concluidas.</p>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-4">
                    <div class="bg-secondary bg-opacity-10 text-secondary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-table fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-dark">Resumen por Grupo</h4>
                        <p class="text-muted mb-0 small">Vista general agregada por grupo y periodo escolar.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="bg-light">
                        <tr>
                            <th class="rounded-start-3 py-3 text-muted">Grupo</th>
                            <th class="py-3 text-muted">Periodo Escolar</th>
                            <th class="py-3 text-center text-success"><i class="bi bi-check-circle-fill me-1"></i> Completadas</th>
                            <th class="py-3 text-center text-danger"><i class="bi bi-x-circle-fill me-1"></i> Abandonadas</th>
                            <th class="rounded-end-3 py-3 text-end text-muted">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($grupos ?? [] as $grupo)
                            <tr>
                                <td class="fw-bold text-dark">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-folder-fill text-warning"></i> {{ $grupo->nombre }}
                                    </div>
                                </td>
                                <td><span class="badge bg-light text-secondary border rounded-pill">{{ $grupo->periodo }}</span></td>
                                <td class="text-center fw-bold text-success">{{ $grupo->completadas ?? 0 }}</td>
                                <td class="text-center fw-bold text-danger">{{ $grupo->abandonadas ?? 0 }}</td>
                                <td class="text-end">
                                    <a href="{{ route('grupos.show', $grupo->id) }}" class="btn btn-sm btn-light border text-primary rounded-pill shadow-sm fw-bold px-3">
                                        Detalle <i class="bi bi-arrow-right ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted border-bottom-0">
                                    <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i> No tienes grupos asignados en este momento.
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
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 1000 });

            const counters = document.querySelectorAll('.count-up');
            counters.forEach(counter => {
                const endValue = parseInt(counter.getAttribute('data-value'), 10) || 0;
                anime({ targets: counter, innerHTML: [0, endValue], easing: 'easeOutExpo', round: 1, duration: 2500, delay: 500 });
            });
        });
    </script>
@endpush
