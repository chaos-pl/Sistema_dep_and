@extends('layouts.app')

@section('title', 'Detalle de Alerta - PROMETEO')
@section('page-title', 'Detalle de Alerta Clínica')
@section('page-subtitle', 'Revisión clínica del caso y registro de diagnóstico')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
    </style>
@endpush

@section('content')
    @php
        $evaluacion = $alerta->evaluacion;
        $estudiante = $evaluacion->estudiante;
        $persona = $estudiante?->persona;
        $resultado = $evaluacion->resultadoClinico;
        $diagnostico = $evaluacion->diagnostico;

        $riesgo = $resultado->nivel_riesgo ?? 'n/d';
        $riesgoColor = match($riesgo) {
            'severo' => 'bg-danger text-white border-danger',
            'moderado' => 'bg-warning text-dark border-warning',
            'leve' => 'bg-info text-dark border-info',
            default => 'bg-success text-white border-success',
        };
    @endphp

    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 bg-body-tertiary">
                <div class="d-flex flex-wrap justify-content-between gap-4 align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 60px; height: 60px;">
                            <i class="bi bi-person-bounding-box fs-3"></i>
                        </div>
                        <div>
                            <p class="text-body-secondary mb-1 fw-bold text-uppercase" style="font-size: 0.75rem; letter-spacing: 1px;">Expediente Estudiantil</p>
                            <h3 class="fw-black mb-1 text-body">
                                {{ $persona->nombre ?? 'Sin nombre' }} {{ $persona->apellido_paterno ?? '' }} {{ $persona->apellido_materno ?? '' }}
                            </h3>
                            <div class="d-flex align-items-center gap-2 text-body-secondary fw-medium">
                                <i class="bi bi-clipboard2-check text-primary"></i>
                                Instrumento aplicado: <strong class="text-body">{{ strtoupper($evaluacion->instrumento->acronimo ?? 'N/D') }}</strong>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-body text-body border border-secondary border-opacity-25 rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2" style="font-size: 0.95rem;">
                            Puntaje Obtenido: <strong class="fs-5">{{ $resultado->puntaje_total ?? 'N/D' }}</strong>
                        </span>
                        <span class="badge border rounded-pill px-4 py-2 shadow-sm d-flex align-items-center gap-2 {{ $riesgoColor }}" style="font-size: 0.95rem;">
                            Nivel de Riesgo: <strong>{{ ucfirst($riesgo) }}</strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-3 mb-4">
                    <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-list-check fs-5"></i>
                    </div>
                    <h4 class="fw-black mb-0 text-body">Desglose de Respuestas</h4>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle table-prometeo border-top border-secondary border-opacity-10">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-2 px-3 rounded-start-3 text-body-secondary fw-bold border-0 text-center">Nº Pregunta</th>
                            <th class="py-2 px-3 rounded-end-3 text-body-secondary fw-bold border-0 text-center">Valor Elegido</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($evaluacion->respuestas->sortBy('numero_pregunta') as $respuesta)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="text-center py-2 border-0 fw-bold text-body-secondary">
                                    Pregunta {{ $respuesta->numero_pregunta }}
                                </td>
                                <td class="text-center py-2 border-0">
                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-circle fs-6 d-inline-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                        {{ $respuesta->valor }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="2" class="text-center text-body-secondary py-4 border-0">
                                    No hay respuestas registradas.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-7 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4 h-100">
                <div class="d-flex align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-3 mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-file-earmark-medical-fill fs-5"></i>
                    </div>
                    <h4 class="fw-black mb-0 text-body">
                        {{ $diagnostico ? 'Expediente Diagnóstico' : 'Registrar Diagnóstico Inicial' }}
                    </h4>
                </div>

                @if($diagnostico)
                    <div class="mb-4">
                        <label class="form-label fw-bold text-body-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Impresión Diagnóstica (Privado)</label>
                        <div class="p-3 bg-body-tertiary border border-secondary border-opacity-10 rounded-4 text-body" style="min-height: 110px;">
                            {{ $diagnostico->impresion_diagnostica }}
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-body-secondary text-uppercase" style="font-size: 0.75rem; letter-spacing: 0.5px;">Retroalimentación al estudiante (Visible)</label>
                        <div class="p-3 bg-body-tertiary border border-secondary border-opacity-10 rounded-4 text-body" style="min-height: 110px;">
                            {{ $diagnostico->retroalimentacion_estudiante ?: 'Sin retroalimentación enviada al estudiante.' }}
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-5 pt-3 border-top border-secondary border-opacity-10">
                        <span class="badge rounded-pill px-4 py-2 fs-6 shadow-sm {{ $diagnostico->requiere_derivacion ? 'bg-danger text-white' : 'bg-success text-white' }}">
                            <i class="bi {{ $diagnostico->requiere_derivacion ? 'bi-hospital' : 'bi-check-circle' }} me-2"></i>
                            {{ $diagnostico->requiere_derivacion ? 'Requiere Derivación Externa' : 'Manejo Interno / Sin Derivación' }}
                        </span>
                        <a href="{{ route('alertas.index') }}" class="btn btn-light border rounded-pill fw-bold px-4 shadow-sm">Volver al Panel</a>
                    </div>
                @else
                    <form action="{{ route('diagnosticos.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="evaluacion_id" value="{{ $evaluacion->id }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-body-secondary">Impresión diagnóstica <span class="text-danger">*</span></label>
                            <textarea name="impresion_diagnostica" rows="5" class="form-control form-control-lg bg-body-tertiary @error('impresion_diagnostica') is-invalid @enderror" placeholder="Anota aquí las observaciones clínicas confidenciales..." required>{{ old('impresion_diagnostica') }}</textarea>
                            @error('impresion_diagnostica') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-body-secondary">Retroalimentación (Visible para el estudiante)</label>
                            <textarea name="retroalimentacion_estudiante" rows="4" class="form-control form-control-lg bg-body-tertiary @error('retroalimentacion_estudiante') is-invalid @enderror" placeholder="Mensaje de apoyo u orientación que el alumno podrá leer en su panel...">{{ old('retroalimentacion_estudiante') }}</textarea>
                            @error('retroalimentacion_estudiante') <div class="invalid-feedback fw-bold">{{ $message }}</div> @enderror
                        </div>

                        <div class="form-check form-switch mb-5 mt-2">
                            <input type="checkbox" name="requiere_derivacion" value="1" class="form-check-input" id="requiere_derivacion" style="width: 3em; height: 1.5em;" role="switch">
                            <label class="form-check-label fw-bold ms-2 pt-1 text-danger" for="requiere_derivacion">¿El caso requiere derivación clínica externa?</label>
                        </div>

                        <div class="d-flex justify-content-between align-items-center pt-4 border-top border-secondary border-opacity-10">
                            <a href="{{ route('alertas.index') }}" class="btn btn-light border text-body-secondary rounded-pill fw-bold px-4 shadow-sm">Cancelar</a>
                            <button type="submit" class="btn btn-primary rounded-pill fw-bold px-5 shadow-sm">
                                Guardar Diagnóstico <i class="bi bi-save ms-1"></i>
                            </button>
                        </div>
                    </form>
                @endif
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
