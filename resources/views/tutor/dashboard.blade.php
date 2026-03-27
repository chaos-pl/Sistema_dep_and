@extends('layouts.app')

@section('title', 'Dashboard tutor')
@section('page-title', 'Panel del tutor')
@section('page-subtitle', 'Seguimiento general de grupos y evaluaciones')

@section('content')
    <div class="row g-4">
        <div class="col-12">
            <div class="app-card welcome-card p-4 p-md-5">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <span class="soft-badge soft-info mb-3">Vista institucional</span>
                        <h2 class="fw-bold mb-2">Seguimiento de grupos</h2>
                        <p class="text-muted mb-0">
                            Consulta el avance general de evaluaciones completadas y abandonadas por grupo.
                        </p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="{{ route('grupos.index') }}" class="btn btn-primary">
                            <i class="bi bi-people me-2"></i>Ver grupos
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Grupos asignados</h6>
                    <div class="metric-icon"><i class="bi bi-people"></i></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $totalGrupos ?? 0 }}</h3>
                <p class="text-muted mb-0">Total de grupos a tu cargo.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Completadas</h6>
                    <div class="metric-icon"><i class="bi bi-check2-circle"></i></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $completadas ?? 0 }}</h3>
                <p class="text-muted mb-0">Evaluaciones finalizadas.</p>
            </div>
        </div>

        <div class="col-md-4">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-muted mb-0">Abandonadas</h6>
                    <div class="metric-icon"><i class="bi bi-hourglass-split"></i></div>
                </div>
                <h3 class="fw-bold mb-1">{{ $abandonadas ?? 0 }}</h3>
                <p class="text-muted mb-0">Evaluaciones no concluidas.</p>
            </div>
        </div>

        <div class="col-12">
            <div class="app-card p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Resumen por grupo</h4>
                        <p class="text-muted mb-0">Vista agregada por grupo y periodo.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Grupo</th>
                            <th>Periodo</th>
                            <th>Completadas</th>
                            <th>Abandonadas</th>
                            <th>Acción</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($grupos ?? [] as $grupo)
                            <tr>
                                <td>{{ $grupo->nombre }}</td>
                                <td>{{ $grupo->periodo }}</td>
                                <td>{{ $grupo->completadas ?? 0 }}</td>
                                <td>{{ $grupo->abandonadas ?? 0 }}</td>
                                <td>
                                    <a href="{{ route('grupos.show', $grupo->id) }}" class="btn btn-sm btn-primary">Ver detalle</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No hay grupos para mostrar.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
