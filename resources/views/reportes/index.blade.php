@extends('layouts.app')
@section('page-title', 'Reportes institucionales')
@section('content')
<section class="tm-hero mb-4"><span class="tm-eyebrow">Participación institucional</span><h2>Aplicaciones y participantes</h2><p>Asignación académica al responder. Los registros anteriores sin esa información se muestran por separado.</p></section>
@if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $error)<p class="mb-0">{{ $error }}</p>@endforeach</div>@endif
<form method="GET" class="tm-panel tm-body mb-4">
<div class="row g-3">
    <div class="col-sm-6 col-lg-3"><label for="desde" class="form-label">Desde</label><input class="form-control" type="date" id="desde" name="desde" value="{{ $from->toDateString() }}" required></div>
    <div class="col-sm-6 col-lg-3"><label for="hasta" class="form-label">Hasta</label><input class="form-control" type="date" id="hasta" name="hasta" value="{{ $to->toDateString() }}" required></div>
    @foreach(['carrera' => 'Carrera', 'grupo' => 'Grupo', 'ciclo' => 'Ciclo'] as $key => $label)
    <div class="col-sm-6 col-lg-3"><label for="{{ $key }}" class="form-label">{{ $label }}</label><select class="form-select" name="{{ $key }}" id="{{ $key }}"><option value="">Todos</option>@foreach($options[$key] as $option)<option value="{{ $option->id }}" @selected(request($key) == $option->id)>{{ $option->nombre }} (#{{ $option->id }})</option>@endforeach</select></div>
    @endforeach
    <div class="col-sm-6 col-lg-3"><label for="instrumento" class="form-label">Instrumento</label><select class="form-select" id="instrumento" name="instrumento"><option value="">Todos</option>@foreach($instrumentos as $instrumento)<option value="{{ $instrumento->id }}" @selected(request('instrumento') == $instrumento->id)>{{ $instrumento->acronimo }}</option>@endforeach</select></div>
    <div class="col-sm-6 col-lg-3"><label for="contexto" class="form-label">Asignación</label><select class="form-select" name="contexto" id="contexto"><option value="">Todas</option>@foreach(['registrado' => 'Registrada al responder', 'historico' => 'Histórica no registrada', 'sin_grupo' => 'Sin grupo al responder'] as $key => $label)<option value="{{ $key }}" @selected(request('contexto') === $key)>{{ $label }}</option>@endforeach</select></div>
    <div class="col-sm-6 col-lg-3 d-flex align-items-end"><button class="btn btn-primary tm-action">Aplicar filtros</button></div>
</div>
</form>
<section class="tm-panel tm-body">
    <div class="d-flex flex-wrap justify-content-between gap-3 mb-3"><h3 class="h5">Participación por asignación e instrumento</h3><a class="btn btn-outline-primary" href="{{ route('reportes.export', array_merge(request()->except('page'), ['desde' => $from->toDateString(), 'hasta' => $to->toDateString()])) }}">Descargar CSV</a></div>
    <p class="tm-note">Participantes cuenta estudiantes distintos dentro de cada fila. No sumes las filas como personas únicas: alguien puede responder varios instrumentos o cambiar de grupo. No se calcula cobertura histórica porque no existe un padrón fechado.</p>
    <div class="table-responsive"><table class="table align-middle"><thead><tr>
        @foreach(['Instrumento', 'Carrera', 'Grupo', 'Ciclo', 'Asignación', 'Aplicaciones', 'Participantes'] as $label)<th scope="col">{{ $label }}</th>@endforeach
    </tr></thead><tbody>
        @forelse($rows as $row)<tr><td>{{ $row->acronimo }}</td><td>{{ $row->carrera_aplicacion_nombre ?? 'Sin registro' }}</td><td>{{ $row->grupo_aplicacion_nombre ?? 'Sin registro' }}</td><td>{{ $row->ciclo_aplicacion_nombre ?? 'Sin registro' }}</td><td>{{ $row->contexto }}</td><td>{{ $row->aplicaciones }}</td><td>{{ $row->participantes }}</td></tr>
        @empty<tr><td colspan="7">Sin aplicaciones con estos filtros.</td></tr>@endforelse
    </tbody></table></div>
    {{ $rows->links() }}
    <p class="small text-body-secondary mb-0">Incluye evaluaciones completadas vinculadas al historial general. Completa la vinculación de DASS-21 históricos antes de usar este reporte. La cobertura actual continúa en los paneles existentes.</p>
</section>
@endsection
