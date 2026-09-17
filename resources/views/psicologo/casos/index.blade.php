@extends('layouts.app')
@section('title', 'Casos de atención - PROMETEO')
@section('page-title', 'Casos de atención')
@section('page-subtitle', 'Asignación, seguimiento y cierre profesional')
@section('content')
<div class="tm-hero tm-enter">
    <span class="tm-eyebrow">Psicología · Atención</span>
    <h2>Una bandeja para dar continuidad</h2>
    <p>Cuestionarios y diarios conservan su origen. Registrar una valoración no cierra el caso: el cierre es una decisión explícita del responsable.</p>
</div>
<div class="row g-3 mb-4">
@foreach(['pendiente' => 'Por asignar', 'asignado' => 'Asignados', 'seguimiento' => 'En seguimiento', 'cerrado' => 'Cerrados'] as $state => $label)
    <div class="col-6 col-xl-3"><a class="app-card d-block p-4 rounded-4 text-decoration-none" href="{{ route('psicologo.casos.index', ['estado' => $state]) }}"><span class="text-body-secondary">{{ $label }}</span><strong class="d-block fs-2 text-body">{{ $counts[$state] ?? 0 }}</strong></a></div>
@endforeach
</div>
<section class="tm-panel tm-body">
    <form method="GET" class="row g-3 align-items-end mb-4">
        <div class="col-md-3"><label class="form-label" for="estado">Estado</label><select class="form-select" name="estado" id="estado"><option value="">Todos</option>@foreach(['pendiente'=>'Por asignar','asignado'=>'Asignado','seguimiento'=>'En seguimiento','cerrado'=>'Cerrado'] as $value=>$label)<option value="{{ $value }}" @selected(request('estado') === $value)>{{ $label }}</option>@endforeach</select></div>
        <div class="col-md-3"><label class="form-label" for="origen">Origen</label><select class="form-select" name="origen" id="origen"><option value="">Todos</option><option value="evaluacion" @selected(request('origen') === 'evaluacion')>Cuestionarios</option><option value="diario" @selected(request('origen') === 'diario')>Diarios IA</option></select></div>
        <div class="col-md-3"><label class="form-label" for="responsable">Responsable</label><select class="form-select" name="responsable" id="responsable"><option value="">Todos</option><option value="mios" @selected(request('responsable') === 'mios')>Mis casos</option><option value="sin_asignar" @selected(request('responsable') === 'sin_asignar')>Sin asignar</option></select></div>
        <div class="col-md-3 d-flex gap-2"><button class="btn btn-primary tm-action">Filtrar</button><a href="{{ route('psicologo.casos.index') }}" class="btn btn-outline-secondary tm-action">Limpiar</a></div>
    </form>
    @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
    <div class="table-responsive"><table class="table tm-table"><thead><tr><th>Estudiante</th><th>Origen</th><th>Estado</th><th>Responsable</th><th>Actualizado</th><th>Acción</th></tr></thead><tbody>
    @forelse($casos as $caso)
        <tr><td>{{ $caso->estudiante?->persona?->nombre ?? 'Expediente no disponible' }} {{ $caso->estudiante?->persona?->apellido_paterno }}</td><td>{{ $caso->origen_texto }}</td><td><span class="badge {{ $caso->estado === 'cerrado' ? 'text-bg-secondary' : 'text-bg-primary' }}">{{ ucfirst($caso->estado) }}</span></td><td>{{ $caso->psicologo?->persona?->nombre ?? 'Sin asignar' }} {{ $caso->psicologo?->persona?->apellido_paterno }}</td><td>{{ $caso->updated_at?->format('d/m/Y H:i') }}</td><td><a class="btn btn-outline-primary tm-action" href="{{ route('psicologo.casos.show', $caso) }}">Abrir caso</a></td></tr>
    @empty<tr><td colspan="6"><div class="tm-empty"><i class="bi bi-journal-check"></i><p>No hay casos que coincidan con estos filtros.</p></div></td></tr>@endforelse
    </tbody></table></div>{{ $casos->links() }}
</section>
@endsection
