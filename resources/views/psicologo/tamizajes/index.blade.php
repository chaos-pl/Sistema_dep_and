@extends('layouts.app')
@section('page-title', 'Tamizajes e historial')
@section('content')
<div class="tm-panel tm-enter">
    <div class="tm-heading"><div class="d-flex gap-3 align-items-center"><span class="tm-icon"><i class="bi bi-clipboard2-pulse"></i></span><div><h2>Tamizajes e historial</h2><p>Consulta resultados, revisa cada dimensión y registra el seguimiento.</p></div></div><span class="tm-badge tm-badge--neutral">{{ $evaluaciones->total() }} resultados</span></div>
    <form method="GET" class="row g-3 tm-toolbar m-0">
        <div class="col-md-2"><label for="desde">Desde</label><input id="desde" type="date" name="desde" class="form-control" value="{{ $periodo[0]->toDateString() }}"></div>
        <div class="col-md-2"><label for="hasta">Hasta</label><input id="hasta" type="date" name="hasta" class="form-control" value="{{ $periodo[1]->toDateString() }}"></div>
        <div class="col-md-2"><label for="instrumento">Instrumento</label><select id="instrumento" name="instrumento" class="form-select"><option value="">Todos</option>@foreach(['DASS21', 'PHQ9', 'GAD7'] as $item)<option @selected(request('instrumento') === $item)>{{ $item }}</option>@endforeach</select></div>
        <div class="col-md-2"><label for="estado">Atención</label><select id="estado" name="estado" class="form-select"><option value="">Todas</option>@foreach(['pendiente' => 'Pendiente', 'atendida' => 'Con valoración', 'sin_alerta' => 'Sin alerta', 'seguimiento' => 'En seguimiento', 'cerrado' => 'Caso cerrado'] as $key => $label)<option value="{{ $key }}" @selected(request('estado') === $key)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-2"><label for="grupo_id">Grupo</label><select id="grupo_id" name="grupo_id" class="form-select"><option value="">Todos</option>@foreach($grupos as $grupo)<option value="{{ $grupo->id }}" @selected((string) request('grupo_id') === (string) $grupo->id)>{{ $grupo->nombre }}</option>@endforeach</select></div>
        <div class="col-md-2 align-self-end"><button class="btn btn-primary tm-action"><i class="bi bi-funnel"></i> Filtrar</button><a class="d-inline-block small mt-2" href="{{ route('psicologo.tamizajes.index') }}">Restablecer filtros</a></div>
    </form>
    @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
    <div class="table-responsive"><table class="table tm-table">
        <thead><tr><th>Estudiante</th><th>Fecha</th><th>Instrumento</th><th>Resultado</th><th>Atención</th><th></th></tr></thead>
        <tbody>@forelse($evaluaciones as $evaluacion)
            <tr><td>{{ $evaluacion->estudiante?->persona?->nombre ?? 'Expediente no disponible' }} {{ $evaluacion->estudiante?->persona?->apellido_paterno }}</td>
                <td>{{ $evaluacion->created_at?->format('d/m/Y H:i') }}</td><td>{{ $evaluacion->instrumento?->acronimo }}</td>
                <td><div class="fw-bold mb-2">{{ $evaluacion->puntaje_resumen }}</div><x-tamizaje-status :label="$evaluacion->nivel_resumen" /></td>
                <td><x-tamizaje-status :label="$evaluacion->casoAtencion ? ucfirst($evaluacion->casoAtencion->estado) : ($evaluacion->diagnostico ? 'Valorado' : ($evaluacion->alerta?->estado ?? 'Sin alerta'))" /></td>
                <td>@can('evaluaciones.respuestas.detalle')<a class="btn btn-outline-primary tm-action" href="{{ route('psicologo.tamizajes.show', $evaluacion) }}">Ver detalle <i class="bi bi-arrow-up-right"></i></a>@endcan</td></tr>
        @empty<tr><td colspan="6"><div class="tm-empty"><i class="bi bi-search"></i><strong>No hay tamizajes en el periodo seleccionado.</strong><p>Prueba con otras fechas o restablece los filtros.</p></div></td></tr>@endforelse</tbody>
    </table></div><div class="tm-footer">{{ $evaluaciones->links() }}</div>
</div>
@endsection
