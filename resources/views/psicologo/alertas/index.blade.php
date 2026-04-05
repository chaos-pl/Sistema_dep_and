@extends('layouts.app')

@section('title', 'Alertas Clínicas - PROMETEO')
@section('page-title', 'Alertas Clínicas')
@section('page-subtitle', 'Seguimiento de alertas generadas por evaluaciones')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
        .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-elevate:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,0.08) !important; }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size: .8rem; letter-spacing: 0.5px;">Generadas</h6>
                    <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-bell-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalGeneradas }}</h2>
                <p class="text-body-secondary mb-0 small">Alertas nuevas pendientes de revisión.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size: .8rem; letter-spacing: 0.5px;">Asignadas</h6>
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-person-lines-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalAsignadas }}</h2>
                <p class="text-body-secondary mb-0 small">Casos abiertos en seguimiento clínico.</p>
            </div>
        </div>

        <div class="col-md-4 anime-item">
            <div class="app-card bg-body-tertiary p-4 border border-secondary border-opacity-10 shadow-sm rounded-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="text-body-secondary fw-bold text-uppercase mb-0" style="font-size: .8rem; letter-spacing: 0.5px;">Atendidas</h6>
                    <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 45px; height: 45px;">
                        <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>
                </div>
                <h2 class="fw-black mb-1 text-body">{{ $totalAtendidas }}</h2>
                <p class="text-body-secondary mb-0 small">Alertas cerradas con intervención clínica.</p>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Listado de Alertas</h4>
                        <p class="text-body-secondary mb-0 small">Consulta el detalle de cada alerta clínica y sus resultados asociados.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Instrumento</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Puntaje</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Nivel de Riesgo</th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">Estado Clínico</th>
                            <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($alertas as $alerta)
                            @php
                                $riesgo = $alerta->evaluacion->resultadoClinico->nivel_riesgo ?? 'n/d';
                                $riesgoColor = match($riesgo) {
                                    'severo' => 'bg-danger text-danger',
                                    'moderado' => 'bg-warning text-warning-emphasis',
                                    'leve' => 'bg-info text-info',
                                    default => 'bg-success text-success',
                                };
                            @endphp
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <span class="fw-bold text-body">
                                            {{ $alerta->evaluacion->estudiante->persona->nombre ?? 'Sin nombre' }}
                                            {{ $alerta->evaluacion->estudiante->persona->apellido_paterno ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="text-center py-3 border-0">
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm">
                                        {{ strtoupper($alerta->evaluacion->instrumento->acronimo ?? 'N/D') }}
                                    </span>
                                </td>

                                <td class="text-center py-3 border-0 fw-black fs-5 text-body">
                                    {{ $alerta->evaluacion->resultadoClinico->puntaje_total ?? 'N/D' }}
                                </td>

                                <td class="text-center py-3 border-0">
                                    <span class="badge bg-opacity-10 border border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm {{ $riesgoColor }}">
                                        {{ ucfirst($riesgo) }}
                                    </span>
                                </td>

                                <td class="text-center py-3 border-0">
                                    <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm
                                        @if($alerta->estado === 'atendida') bg-success
                                        @elseif($alerta->estado === 'asignada_psicologo') bg-info text-dark
                                        @else bg-warning text-dark
                                        @endif">
                                        {{ str_replace('_', ' ', ucfirst($alerta->estado)) }}
                                    </span>
                                </td>

                                <td class="text-end px-4 py-3 border-0">
                                    <a href="{{ route('alertas.show', $alerta->id) }}" class="btn btn-sm btn-primary rounded-pill fw-bold px-3 shadow-sm hover-elevate">
                                        Revisar Caso <i class="bi bi-arrow-right-circle ms-1"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-body-secondary border-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-check2-circle fs-1 text-success opacity-50 mb-3"></i>
                                        <span>No hay alertas clínicas registradas. El sistema está limpio.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($alertas, 'links'))
                    <div class="mt-4">
                        {{ $alertas->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(100), easing: 'easeOutExpo', duration: 850 });
        });
    </script>
@endpush
