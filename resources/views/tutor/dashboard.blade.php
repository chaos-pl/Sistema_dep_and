<div class="row g-4">
    <div class="col-12">
        <div class="app-card p-4 p-lg-5 welcome-gradient">
            <div class="row g-4 align-items-center">
                <div class="col-lg-8">
                    <span class="soft-badge soft-primary mb-3">Rol: Tutor</span>
                    <h2 class="fw-bold mb-3">Seguimiento grupal con métricas agregadas y anonimato preservado.</h2>
                    <p class="text-muted-soft mb-0">El dashboard del tutor evita exponer respuestas individuales y prioriza indicadores por cohorte, abandono de tamizajes y grupos con menor participación.</p>
                </div>
                <div class="col-lg-4">
                    <div class="row g-3">
                        <div class="col-6">
                            <div class="app-card p-3 text-center h-100">
                                <div class="metric-icon mx-auto mb-2"><i class="bi bi-people"></i></div>
                                <div class="small text-muted-soft">Grupos</div>
                                <div class="h3 fw-bold mb-0">4</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="app-card p-3 text-center h-100">
                                <div class="metric-icon mx-auto mb-2"><i class="bi bi-mortarboard"></i></div>
                                <div class="small text-muted-soft">Estudiantes</div>
                                <div class="h3 fw-bold mb-0">126</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="app-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Resumen de grupos</h4>
                    <p class="text-muted-soft mb-0">Vista compacta para tutorías asignadas.</p>
                </div>
                <span class="soft-badge soft-info">Anonimizado</span>
            </div>

            <div class="vstack gap-3">
                @foreach ([
                    ['grupo' => 'Ingeniería de Sistemas - 3A', 'progreso' => 78, 'alertas' => 2],
                    ['grupo' => 'Psicología - 2B', 'progreso' => 64, 'alertas' => 1],
                    ['grupo' => 'Contabilidad - 1C', 'progreso' => 81, 'alertas' => 0],
                ] as $grupo)
                    <div class="app-card-soft p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <h6 class="fw-semibold mb-0">{{ $grupo['grupo'] }}</h6>
                            <span class="soft-badge {{ $grupo['alertas'] ? 'soft-warning' : 'soft-success' }}">{{ $grupo['alertas'] }} alertas</span>
                        </div>
                        <div class="progress rounded-pill mb-2" style="height: 10px; background: #EFE7FA;">
                            <div class="progress-bar" style="width: {{ $grupo['progreso'] }}%; background: var(--bs-primary);"></div>
                        </div>
                        <small class="text-muted-soft">{{ $grupo['progreso'] }}% de estudiantes completó el tamizaje.</small>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="app-card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h4 class="fw-bold mb-1">Métricas generales</h4>
                    <p class="text-muted-soft mb-0">Evaluaciones completadas vs. abandonadas, sin exponer identidad.</p>
                </div>
                <a href="{{ route('reportes.tutor') }}" class="btn btn-soft-primary">Ver reporte completo</a>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="app-card-soft p-4 text-center h-100">
                        <div class="metric-icon mx-auto mb-3"><i class="bi bi-check2-circle"></i></div>
                        <div class="small text-muted-soft">Completadas</div>
                        <div class="display-6 fw-bold">92</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="app-card-soft p-4 text-center h-100">
                        <div class="metric-icon mx-auto mb-3"><i class="bi bi-pause-circle"></i></div>
                        <div class="small text-muted-soft">Abandonadas</div>
                        <div class="display-6 fw-bold">18</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="app-card-soft p-4 text-center h-100">
                        <div class="metric-icon mx-auto mb-3"><i class="bi bi-hourglass-split"></i></div>
                        <div class="small text-muted-soft">Pendientes</div>
                        <div class="display-6 fw-bold">16</div>
                    </div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Programa</th>
                            <th>Tamizajes</th>
                            <th>Completados</th>
                            <th>Abandonados</th>
                            <th>Participación</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Ingeniería</td>
                            <td>46</td>
                            <td>35</td>
                            <td>6</td>
                            <td><span class="soft-badge soft-success">76%</span></td>
                        </tr>
                        <tr>
                            <td>Psicología</td>
                            <td>31</td>
                            <td>22</td>
                            <td>5</td>
                            <td><span class="soft-badge soft-warning">71%</span></td>
                        </tr>
                        <tr>
                            <td>Contabilidad</td>
                            <td>49</td>
                            <td>35</td>
                            <td>7</td>
                            <td><span class="soft-badge soft-info">72%</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
