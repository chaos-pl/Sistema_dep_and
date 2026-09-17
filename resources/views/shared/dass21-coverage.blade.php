@php
    $total = $cobertura->sum('participantes');
    $completos = $cobertura->sum('completadas');
    $porcentaje = $total ? round(100 * $completos / $total, 1) : 0;
@endphp
<section class="tm-panel tm-enter">
    <div class="tm-heading"><div class="d-flex align-items-center gap-3"><span class="tm-icon"><i class="bi bi-bar-chart-line"></i></span><div><h2>Cobertura DASS-21</h2><p>Participación de estudiantes activos en sus grupos actuales.</p></div></div><span class="tm-badge tm-badge--neutral"><i class="bi bi-calendar3"></i> {{ $periodo[0]->format('d/m/Y') }} — {{ $periodo[1]->format('d/m/Y') }}</span></div>
    <div class="tm-stats"><div class="tm-stat"><span class="tm-eyebrow">Con aplicación</span><strong>{{ $completos }}</strong><span>de {{ $total }} estudiantes</span></div><div class="tm-stat"><span class="tm-eyebrow">Pendientes</span><strong>{{ $cobertura->sum('pendientes') }}</strong><span>sin aplicación en el periodo</span></div><div class="tm-stat"><span class="tm-eyebrow">Participación</span><strong>{{ $porcentaje }} %</strong><progress class="tm-progress" value="{{ $porcentaje }}" max="100" aria-label="Porcentaje de participación">{{ $porcentaje }} %</progress></div></div>
    <form method="GET" class="row g-3 tm-toolbar m-0">
        <div class="col-md-4"><label for="desde">Desde</label><input id="desde" class="form-control" type="date" name="desde" value="{{ $periodo[0]->toDateString() }}"></div>
        <div class="col-md-4"><label for="hasta">Hasta</label><input id="hasta" class="form-control" type="date" name="hasta" value="{{ $periodo[1]->toDateString() }}"></div>
        <div class="col-md-4 align-self-end"><button class="btn btn-primary tm-action"><i class="bi bi-funnel"></i> Consultar periodo</button></div>
        @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
    </form>
    <div class="table-responsive"><table class="table tm-table"><thead><tr><th>Carrera / Grupo</th><th>Ciclo actual</th><th>Estudiantes</th><th>Con aplicación</th><th>Pendientes</th><th>Cobertura</th></tr></thead>
        <tbody>@forelse($cobertura as $fila)<tr><td><strong>{{ $fila->nombre }}</strong><small class="d-block text-body-secondary">{{ $fila->carrera?->nombre }}</small></td><td>{{ $fila->cicloEscolar?->nombre ?? 'Sin ciclo' }}</td><td>{{ $fila->participantes }}</td><td><span class="tm-badge tm-badge--success">{{ $fila->completadas }}</span></td><td>{{ $fila->pendientes }}</td><td><strong>{{ $fila->porcentaje }} %</strong><progress class="tm-progress" value="{{ $fila->porcentaje }}" max="100" aria-label="Cobertura de {{ $fila->nombre }}">{{ $fila->porcentaje }} %</progress></td></tr>@empty<tr><td colspan="6"><div class="tm-empty"><i class="bi bi-people"></i><strong>No hay grupos disponibles.</strong><p>La cobertura aparecerá cuando existan grupos asignados.</p></div></td></tr>@endforelse</tbody>
    </table></div><div class="tm-footer small text-body-secondary"><i class="bi bi-info-circle me-1"></i> Cada estudiante cuenta una vez. Sin aplicación no significa abandono ni un resultado de riesgo.</div>
</section>
