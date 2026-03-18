<div class="row g-4">
    <div class="col-12">
        <div class="app-card welcome-gradient p-4 p-lg-5">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <span class="soft-badge soft-primary mb-3">Rol: Estudiante</span>
                    <h2 class="fw-bold mb-3">Hola, {{ auth()->user()->name ?? 'Estudiante' }}. Este espacio fue diseñado para acompañarte sin saturarte.</h2>
                    <p class="text-muted-soft mb-4">Encuentra aquí tus evaluaciones pendientes, un espacio de expresión emocional y recordatorios visuales simples para completar el tamizaje con calma.</p>
                    <div class="d-flex flex-wrap gap-3">
                        <a href="{{ route('tamizaje.show') }}" class="btn btn-primary"><i class="bi bi-play-circle me-2"></i>Iniciar tamizaje</a>
                        <a href="{{ route('diario.index') }}" class="btn btn-soft-primary"><i class="bi bi-journal-text me-2"></i>Escribir en mi diario</a>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="app-card p-4 bg-white">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="fw-semibold">Estado actual</span>
                            <span class="soft-badge soft-info">Seguimiento activo</span>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted-soft">Código anónimo</span>
                            <strong>ANON-2026-014</strong>
                        </div>
                        <div class="d-flex justify-content-between py-2 border-bottom">
                            <span class="text-muted-soft">Última evaluación</span>
                            <strong>Hace 14 días</strong>
                        </div>
                        <div class="d-flex justify-content-between pt-2">
                            <span class="text-muted-soft">Riesgo actual</span>
                            <strong class="text-primary">En observación</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="app-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Evaluaciones pendientes</h4>
                    <p class="text-muted-soft mb-0">Tarjetas breves para instrumentos PHQ-9 y GAD-7.</p>
                </div>
                <span class="soft-badge soft-warning">2 pendientes</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="app-card-soft p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon"><i class="bi bi-clipboard2-heart"></i></div>
                            <span class="soft-badge soft-warning">PHQ-9</span>
                        </div>
                        <h5 class="fw-semibold">Tamizaje de síntomas depresivos</h5>
                        <p class="text-muted-soft">9 preguntas con escala clara para detectar intensidad de síntomas en las últimas dos semanas.</p>
                        <div class="progress rounded-pill mb-3" role="progressbar" aria-label="Progreso PHQ-9" aria-valuenow="35" aria-valuemin="0" aria-valuemax="100" style="height: 10px; background: #EFE7FA;">
                            <div class="progress-bar" style="width: 35%; background: var(--bs-primary);"></div>
                        </div>
                        <a href="{{ route('tamizaje.show') }}" class="btn btn-primary w-100">Continuar PHQ-9</a>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="app-card-soft p-4 h-100">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="metric-icon"><i class="bi bi-shield-plus"></i></div>
                            <span class="soft-badge soft-info">GAD-7</span>
                        </div>
                        <h5 class="fw-semibold">Tamizaje de ansiedad</h5>
                        <p class="text-muted-soft">7 preguntas con botones grandes para facilitar concentración y respuesta rápida.</p>
                        <div class="progress rounded-pill mb-3" role="progressbar" aria-label="Progreso GAD-7" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="height: 10px; background: #EFE7FA;">
                            <div class="progress-bar" style="width: 0%; background: var(--bs-primary);"></div>
                        </div>
                        <a href="{{ route('tamizaje.show') }}" class="btn btn-soft-primary w-100">Comenzar GAD-7</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="app-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h4 class="fw-bold mb-1">Diario / Expresión para NLP</h4>
                    <p class="text-muted-soft mb-0">Área asociada a <code>texto_ingresado</code>.</p>
                </div>
                <span class="soft-badge soft-success">Privado</span>
            </div>

            <label for="texto_ingresado" class="form-label fw-semibold">¿Cómo te has sentido esta semana?</label>
            <textarea id="texto_ingresado" class="form-control journal-box mb-3" placeholder="Escribe con libertad. El sistema puede analizar lenguaje emocional para detectar señales tempranas y priorizar apoyo profesional.">Últimamente me he sentido con más presión académica, me cuesta dormir y a veces pierdo la motivación para asistir a clases.</textarea>
            <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                <small class="text-muted-soft">Se recomienda un tono conversacional y espontáneo para mejorar el análisis NLP.</small>
                <button class="btn btn-primary"><i class="bi bi-send me-2"></i>Guardar entrada</button>
            </div>
        </div>
    </div>
</div>
