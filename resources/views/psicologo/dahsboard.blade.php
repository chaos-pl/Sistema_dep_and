@extends('layouts.app')

@section('title', 'Dashboard psicólogo')
@section('page-title', 'Panel clínico')
@section('page-subtitle', 'Alertas, resultados clínicos y análisis NLP')

@section('content')
    <div class="row g-4">
        <div class="col-12 col-xl-5">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Alertas prioritarias</h4>
                        <p class="text-muted mb-0">Casos detectados por evaluación.</p>
                    </div>
                    <span class="soft-badge soft-danger"><i class="bi bi-exclamation-triangle"></i> Atención</span>
                </div>

                <div class="d-flex flex-column gap-3">
                    @forelse($alertas ?? [] as $alerta)
                        <div class="border rounded-4 p-3" style="background:#fffafb;">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold mb-1">Evaluación #{{ $alerta->evaluacion_id }}</h6>
                                    <small class="text-muted">{{ $alerta->created_at }}</small>
                                </div>
                                <span class="soft-badge {{ $alerta->estado === 'atendida' ? 'soft-success' : ($alerta->estado === 'asignada_psicologo' ? 'soft-warning' : 'soft-danger') }}">
                                {{ $alerta->estado }}
                            </span>
                            </div>
                            <a href="{{ route('alertas.show', $alerta->id) }}" class="btn btn-sm btn-primary">Revisar</a>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">No hay alertas registradas.</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-12 col-xl-7">
            <div class="app-card p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h4 class="fw-bold mb-1">Resultados clínicos</h4>
                        <p class="text-muted mb-0">Riesgo clínico y hallazgos de NLP.</p>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                        <tr>
                            <th>Evaluación</th>
                            <th>Puntaje</th>
                            <th>Nivel de riesgo</th>
                            <th>RoBERTa</th>
                            <th>Confianza</th>
                            <th>Atención</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($resultados ?? [] as $item)
                            <tr>
                                <td>{{ $item->evaluacion_id ?? '-' }}</td>
                                <td>{{ $item->puntaje_total ?? '-' }}</td>
                                <td>
                                    <span class="soft-badge
                                        {{ ($item->nivel_riesgo ?? '') === 'severo' ? 'soft-danger' :
                                           (($item->nivel_riesgo ?? '') === 'moderado' ? 'soft-warning' :
                                           'soft-success') }}">
                                        {{ $item->nivel_riesgo ?? 'sin dato' }}
                                    </span>
                                </td>
                                <td>{{ $item->etiqueta_roberta ?? '-' }}</td>
                                <td>{{ $item->score_confianza ?? '-' }}</td>
                                <td>
                                    @if(($item->requiere_atencion ?? 0) == 1)
                                        <span class="soft-badge soft-danger">Sí</span>
                                    @else
                                        <span class="soft-badge soft-success">No</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No hay resultados disponibles.</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="app-card p-4">
                <div class="mb-3">
                    <h4 class="fw-bold mb-1">Registrar diagnóstico</h4>
                    <p class="text-muted mb-0">Impresión diagnóstica y retroalimentación del caso.</p>
                </div>

                <form action="{{ route('diagnosticos.store') }}" method="POST">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Evaluación</label>
                            <input type="number" name="evaluacion_id" class="form-control" placeholder="ID de evaluación">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Psicólogo</label>
                            <input type="number" name="psicologo_id" class="form-control" placeholder="ID del psicólogo">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label fw-semibold">¿Requiere derivación?</label>
                            <select name="requiere_derivacion" class="form-select">
                                <option value="0">No</option>
                                <option value="1">Sí</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Impresión diagnóstica</label>
                            <textarea name="impresion_diagnostica" rows="5" class="form-control" placeholder="Describe los hallazgos clínicos del caso..."></textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Retroalimentación para el estudiante</label>
                            <textarea name="retroalimentacion_estudiante" rows="4" class="form-control" placeholder="Escribe un mensaje empático y orientador..."></textarea>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end mt-4">
                        <button class="btn btn-primary">
                            <i class="bi bi-save me-2"></i>Guardar diagnóstico
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
