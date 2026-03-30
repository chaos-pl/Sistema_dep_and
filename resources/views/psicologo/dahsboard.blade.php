@extends('layouts.app')

@section('title', 'Dashboard Clínico - PROMETEO')
@section('page-title', 'Panel Clínico')
@section('page-subtitle', 'Monitoreo de Alertas y Análisis NLP')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
        .alert-card { transition: all 0.2s; border-left: 4px solid transparent; }
        .alert-card:hover { transform: translateX(5px); background-color: #f8fafc !important; border-left-color: #ef4444; }
    </style>
@endpush

@section('content')
    <div class="row g-4">

        <div class="col-12 col-xl-5 anime-item">
            <div class="app-card p-4 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-bell-fill fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-0 text-dark">Alertas Prioritarias</h4>
                            <p class="text-muted mb-0 small">Casos detectados por evaluación.</p>
                        </div>
                    </div>
                    <span class="badge bg-danger rounded-pill shadow-sm">{{ count($alertas ?? []) }}</span>
                </div>

                <div class="d-flex flex-column gap-2">
                    @forelse($alertas ?? [] as $alerta)
                        <div class="alert-card border rounded-4 p-3 bg-light shadow-sm d-flex justify-content-between align-items-center cursor-pointer" onclick="window.location='{{ route('alertas.show', $alerta->id) }}'">
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <i class="bi bi-exclamation-circle-fill text-danger"></i>
                                    <h6 class="fw-bold mb-0 text-dark">Evaluación #{{ $alerta->evaluacion_id }}</h6>
                                </div>
                                <small class="text-muted d-block ms-4"><i class="bi bi-clock me-1"></i>{{ $alerta->created_at }}</small>
                            </div>
                            <div class="text-end">
                                @php
                                    $estadoClass = match($alerta->estado) {
                                        'atendida' => 'bg-success text-white',
                                        'asignada_psicologo' => 'bg-warning text-dark',
                                        default => 'bg-danger text-white'
                                    };
                                @endphp
                                <span class="badge rounded-pill {{ $estadoClass }} shadow-sm mb-2 d-block">{{ ucfirst(str_replace('_', ' ', $alerta->estado)) }}</span>
                                <span class="text-primary fw-bold small">Revisar <i class="bi bi-arrow-right"></i></span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 60px; height: 60px;">
                                <i class="bi bi-check2-circle fs-2 text-success opacity-50"></i>
                            </div>
                            <p class="text-muted fw-medium mb-0">Sin alertas pendientes</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7 anime-item">
            <div class="app-card p-4 p-md-5 h-100 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-clipboard2-pulse fs-5"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-0 text-dark">Resultados y Análisis NLP</h4>
                            <p class="text-muted mb-0 small">Riesgo clínico y hallazgos procesados por IA.</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover">
                        <thead class="bg-light">
                        <tr>
                            <th class="rounded-start-3 py-3 text-muted">Eval. ID</th>
                            <th class="py-3 text-muted">Puntaje</th>
                            <th class="py-3 text-muted">Riesgo Clínico</th>
                            <th class="py-3 text-muted">NLP (RoBERTa)</th>
                            <th class="py-3 text-center text-muted">Confianza</th>
                            <th class="rounded-end-3 py-3 text-center text-muted"><i class="bi bi-exclamation-triangle-fill text-danger"></i></th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($resultados ?? [] as $item)
                            <tr>
                                <td class="fw-bold text-secondary">#{{ $item->evaluacion_id ?? '-' }}</td>
                                <td class="fw-bold text-dark">{{ $item->puntaje_total ?? '-' }} pts</td>
                                <td>
                                    @php
                                        $riesgoClass = match($item->nivel_riesgo ?? '') {
                                            'severo' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                            'moderado' => 'bg-warning bg-opacity-10 text-warning-emphasis border-warning',
                                            default => 'bg-success bg-opacity-10 text-success border-success'
                                        };
                                    @endphp
                                    <span class="badge border border-opacity-25 rounded-pill px-3 py-1 fw-bold {{ $riesgoClass }}">
                                        {{ ucfirst($item->nivel_riesgo ?? 'sin dato') }}
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-primary border rounded-pill">{{ $item->etiqueta_roberta ?? '-' }}</span></td>
                                <td class="text-center fw-medium text-secondary">{{ $item->score_confianza ?? '-' }}</td>
                                <td class="text-center">
                                    @if(($item->requiere_atencion ?? 0) == 1)
                                        <i class="bi bi-circle-fill text-danger" title="Requiere Atención"></i>
                                    @else
                                        <i class="bi bi-circle-fill text-success opacity-50"></i>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted border-bottom-0">
                                    <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i> No hay resultados analizados disponibles.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-4">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-file-earmark-medical fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-dark">Registrar Impresión Diagnóstica</h4>
                        <p class="text-muted mb-0 small">Emite un juicio clínico y envía retroalimentación al paciente.</p>
                    </div>
                </div>

                <form action="{{ route('diagnosticos.store') }}" method="POST">
                    @csrf
                    <div class="row g-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary">ID Evaluación</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-hash"></i></span>
                                <input type="number" name="evaluacion_id" class="form-control form-control-lg bg-light border-start-0 ps-0" placeholder="Ej. 1024">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary">ID Psicólogo</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-end-0"><i class="bi bi-person-badge"></i></span>
                                <input type="number" name="psicologo_id" class="form-control form-control-lg bg-light border-start-0 ps-0" value="{{ auth()->id() }}" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-secondary">¿Requiere derivación?</label>
                            <select name="requiere_derivacion" class="form-select form-select-lg bg-light">
                                <option value="0">No, manejo interno</option>
                                <option value="1">Sí, derivación externa especializada</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Impresión Diagnóstica Clínica (Uso Interno)</label>
                            <textarea name="impresion_diagnostica" rows="5" class="form-control bg-light rounded-4 p-3 border" placeholder="Describe los hallazgos clínicos del caso..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-secondary">Retroalimentación (Visible para el paciente)</label>
                            <textarea name="retroalimentacion_estudiante" rows="5" class="form-control bg-light rounded-4 p-3 border" placeholder="Escribe un mensaje empático y orientador..."></textarea>
                        </div>
                    </div>
                    <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                            <i class="bi bi-save-fill me-2"></i>Guardar Expediente Clínico
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({ targets: '.anime-item', translateY: [30, 0], opacity: [0, 1], delay: anime.stagger(150), easing: 'easeOutExpo', duration: 1000 });
        });
    </script>
@endpush
