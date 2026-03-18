<div class="row g-4">
    <div class="col-12">
        <div class="app-card p-4 p-lg-5 welcome-gradient">
            <div class="d-flex flex-column flex-lg-row justify-content-between gap-4 align-items-lg-center">
                <div>
                    <span class="soft-badge soft-danger mb-3">Rol: Psicólogo</span>
                    <h2 class="fw-bold mb-3">Prioriza alertas clínicas y resultados combinados de tamizaje + NLP.</h2>
                    <p class="text-muted-soft mb-0">Este panel reúne registros de <code>alertas</code>, <code>diagnosticos</code>, <code>evaluaciones</code> y <code>analisis_nlp</code> para apoyar la intervención temprana.</p>
                </div>
                <div class="d-flex flex-wrap gap-3">
                    <div class="app-card p-3 text-center" style="min-width: 150px;">
                        <div class="small text-muted-soft">Alertas urgentes</div>
                        <div class="display-6 fw-bold text-primary">7</div>
                    </div>
                    <div class="app-card p-3 text-center" style="min-width: 150px;">
                        <div class="small text-muted-soft">Casos en seguimiento</div>
                        <div class="display-6 fw-bold">23</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="app-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Alertas prioritarias</h4>
                    <p class="text-muted-soft mb-0">Listado inspirado en la tabla <code>alertas</code>.</p>
                </div>
                <span class="soft-badge soft-danger">Urgentes</span>
            </div>

            <div class="vstack gap-3">
                @foreach ([
                    ['codigo' => 'ANON-014', 'motivo' => 'Ideación autolesiva detectada en PHQ-9', 'nivel' => 'Alta', 'fecha' => '18 Mar 2026'],
                    ['codigo' => 'ANON-029', 'motivo' => 'Polaridad negativa persistente en diario emocional', 'nivel' => 'Media', 'fecha' => '17 Mar 2026'],
                    ['codigo' => 'ANON-061', 'motivo' => 'Ansiedad severa + abandono académico', 'nivel' => 'Alta', 'fecha' => '17 Mar 2026'],
                ] as $alerta)
                    <div class="app-card-soft timeline-alert p-3">
                        <div class="d-flex justify-content-between align-items-start gap-3 mb-2">
                            <div>
                                <h6 class="fw-semibold mb-1">{{ $alerta['codigo'] }}</h6>
                                <p class="text-muted-soft mb-0">{{ $alerta['motivo'] }}</p>
                            </div>
                            <span class="soft-badge {{ $alerta['nivel'] === 'Alta' ? 'soft-danger' : 'soft-warning' }}">{{ $alerta['nivel'] }}</span>
                        </div>
                        <small class="text-muted-soft"><i class="bi bi-clock me-1"></i>{{ $alerta['fecha'] }}</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="app-card p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Resultados clínicos integrados</h4>
                    <p class="text-muted-soft mb-0">Tabla con <code>nivel_riesgo</code> y salida de <code>analisis_nlp</code> (score + etiqueta RoBERTa).</p>
                </div>
                <a href="{{ route('analisis.index') }}" class="btn btn-soft-primary">Ver todos los análisis</a>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Nivel de riesgo</th>
                            <th>PHQ-9 / GAD-7</th>
                            <th>Score NLP</th>
                            <th>Etiqueta RoBERTa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>ANON-014</td>
                            <td><span class="soft-badge soft-danger">Alto</span></td>
                            <td>21 / 16</td>
                            <td>0.93</td>
                            <td>NEGATIVE</td>
                        </tr>
                        <tr>
                            <td>ANON-029</td>
                            <td><span class="soft-badge soft-warning">Moderado</span></td>
                            <td>14 / 10</td>
                            <td>0.78</td>
                            <td>NEGATIVE</td>
                        </tr>
                        <tr>
                            <td>ANON-044</td>
                            <td><span class="soft-badge soft-info">Bajo vigilancia</span></td>
                            <td>9 / 12</td>
                            <td>0.55</td>
                            <td>NEUTRAL</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="app-card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Gestión de diagnóstico</h4>
                    <p class="text-muted-soft mb-0">Formulario para registrar <code>impresion_diagnostica</code> y <code>retroalimentacion_estudiante</code>.</p>
                </div>
                <span class="soft-badge soft-success">Registro clínico</span>
            </div>

            <form method="POST" action="#">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Código anónimo</label>
                        <input type="text" class="form-control" value="ANON-014">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nivel de depresión</label>
                        <select class="form-select">
                            <option>Moderado</option>
                            <option selected>Moderadamente severo</option>
                            <option>Severo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nivel de ansiedad</label>
                        <select class="form-select">
                            <option>Leve</option>
                            <option>Moderado</option>
                            <option selected>Severo</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Impresión diagnóstica</label>
                        <textarea class="form-control" rows="4" placeholder="Describe hallazgos clínicos, factores asociados y recomendación inicial.">Sintomatología compatible con episodio depresivo moderadamente severo y ansiedad significativa. Se recomienda entrevista clínica inmediata y coordinación con red de apoyo institucional.</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Retroalimentación para el estudiante</label>
                        <textarea class="form-control" rows="3" placeholder="Redacta un mensaje empático y claro para el estudiante.">Hemos identificado señales que sugieren que sería valioso brindarte acompañamiento profesional. Te contactaremos de forma confidencial para ofrecerte apoyo.</textarea>
                    </div>
                </div>
                <div class="d-flex justify-content-end gap-3 mt-4">
                    <button type="button" class="btn btn-soft-primary">Guardar borrador</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-save me-2"></i>Registrar diagnóstico</button>
                </div>
            </form>
        </div>
    </div>
</div>
