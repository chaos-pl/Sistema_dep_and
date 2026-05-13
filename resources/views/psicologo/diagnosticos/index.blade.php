@extends('layouts.app')

@section('title', 'Diagnósticos - PROMETEO')
@section('page-title', 'Diagnósticos')
@section('page-subtitle', 'Historial de diagnósticos registrados por el psicólogo')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
        .hover-elevate {
            transition: transform .2s ease, box-shadow .2s ease;
        }
        .hover-elevate:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 24px rgba(0,0,0,.08) !important;
        }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                         style="width: 50px; height: 50px;">
                        <i class="bi bi-file-earmark-medical-fill fs-4"></i>
                    </div>

                    <div>
                        <h4 class="fw-black mb-1 text-body">Diagnósticos registrados</h4>
                        <p class="text-body-secondary mb-0 small">
                            Consulta los diagnósticos elaborados a partir de alertas clínicas revisadas.
                        </p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">
                                Estudiante
                            </th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">
                                Instrumento
                            </th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">
                                Puntaje
                            </th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">
                                Riesgo
                            </th>
                            <th class="py-3 text-body-secondary fw-bold border-0 text-center">
                                Derivación
                            </th>
                            <th class="py-3 px-4 rounded-end-3 text-body-secondary fw-bold border-0 text-center">
                                Fecha
                            </th>
                        </tr>
                        </thead>

                        <tbody class="border-top-0">
                        @forelse($diagnosticos as $diagnostico)
                            @php
                                $evaluacion = $diagnostico->evaluacion;
                                $resultado = $evaluacion?->resultadoClinico;
                                $instrumento = $evaluacion?->instrumento;
                                $persona = $evaluacion?->estudiante?->persona;

                                $riesgo = $resultado?->nivel_riesgo ?? 'n/d';

                                $riesgoColor = match($riesgo) {
                                    'severo' => 'bg-danger text-white',
                                    'moderado' => 'bg-warning text-dark',
                                    'leve' => 'bg-info text-dark',
                                    default => 'bg-success text-white',
                                };
                            @endphp

                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-4 py-3 border-0">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person-badge text-primary"></i>
                                        <span class="fw-bold text-body">
                                            {{ $persona?->nombre ?? 'Sin nombre' }}
                                            {{ $persona?->apellido_paterno ?? '' }}
                                            {{ $persona?->apellido_materno ?? '' }}
                                        </span>
                                    </div>
                                </td>

                                <td class="text-center py-3 border-0">
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        {{ strtoupper($instrumento?->acronimo ?? 'N/D') }}
                                    </span>
                                </td>

                                <td class="text-center py-3 border-0 fw-black text-body">
                                    {{ $resultado?->puntaje_total ?? 'N/D' }}
                                </td>

                                <td class="text-center py-3 border-0">
                                    <span class="badge rounded-pill px-3 py-2 fw-bold shadow-sm {{ $riesgoColor }}">
                                        {{ ucfirst($riesgo) }}
                                    </span>
                                </td>

                                <td class="text-center py-3 border-0">
                                    @if($diagnostico->requiere_derivacion)
                                        <span class="badge bg-danger rounded-pill px-3 py-2 fw-bold shadow-sm">
                                            Requiere derivación
                                        </span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3 py-2 fw-bold shadow-sm">
                                            Manejo interno
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center px-4 py-3 border-0 text-body-secondary fw-medium">
                                    {{ $diagnostico->created_at?->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-body-secondary border-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-folder2-open fs-1 text-primary opacity-50 mb-3"></i>
                                        <span>No hay diagnósticos registrados todavía.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($diagnosticos, 'links'))
                    <div class="mt-4">
                        {{ $diagnosticos->links() }}
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            if (typeof anime !== 'undefined') {
                anime({
                    targets: '.anime-item',
                    translateY: [30, 0],
                    opacity: [0, 1],
                    delay: anime.stagger(100),
                    easing: 'easeOutExpo',
                    duration: 850
                });
            }
        });
    </script>
@endpush
