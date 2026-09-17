@extends('layouts.app')
@section('title', 'Seguimiento del caso - PROMETEO')
@section('page-title', 'Seguimiento del caso')
@section('content')
@include('components.evolution-link', ['student' => $caso->estudiante])
@php($canManage = auth()->user()->can('diagnosticos.crear') && (!$caso->psicologo_id || (int) $caso->psicologo_id === auth()->user()->persona->psicologo->id))
<section class="tm-panel tm-body">
    <a class="btn btn-outline-primary tm-action mb-3" href="{{ route('psicologo.casos.index') }}"><i class="bi bi-arrow-left"></i> Volver a casos</a>
    <span class="tm-eyebrow">Caso #{{ $caso->id }} · {{ $caso->origen_texto }}</span>
    <h2>{{ $caso->estudiante?->persona?->nombre ?? 'Expediente no disponible' }} {{ $caso->estudiante?->persona?->apellido_paterno }}</h2>
    <div class="d-flex flex-wrap gap-3 mb-3"><span class="badge text-bg-primary align-self-start">{{ ucfirst($caso->estado) }}</span><span>Responsable: <strong>{{ $caso->psicologo?->persona?->nombre ?? 'Sin asignar' }} {{ $caso->psicologo?->persona?->apellido_paterno }}</strong></span></div>
    <p class="text-body-secondary small">Asignación: {{ $caso->asignado_at?->format('d/m/Y H:i') ?? 'Fecha no registrada' }} · Cierre: {{ $caso->cerrado_at?->format('d/m/Y H:i') ?? 'Sin cierre' }}</p>
    <a class="btn btn-primary tm-action" href="{{ $caso->evaluacion_id ? route('psicologo.tamizajes.show', $caso->evaluacion_id) : route('analisis.show', $caso->analisis_nlp_id) }}">Consultar resultado de origen <i class="bi bi-arrow-up-right"></i></a>
    <p class="tm-note mt-3 mb-0">Las notas de este historial son privadas de psicología. El cierre no cambia el resultado del instrumento ni la clasificación de IA.</p>
</section>
@if(session('status'))<div class="alert alert-success" role="status">{{ session('status') }}</div>@endif
@if($errors->any())<div class="alert alert-danger" role="alert">@foreach($errors->all() as $error)<p class="mb-1">{{ $error }}</p>@endforeach</div>@endif
<div class="row g-4">
<div class="col-lg-5">
    <section class="tm-panel tm-body">
    @if($canManage)
        <h3 class="h5">{{ $caso->estado === 'cerrado' ? 'Reabrir caso' : 'Actualizar atención' }}</h3>
        <form method="POST" action="{{ route('psicologo.casos.update', $caso) }}" class="tm-form">
            @csrf<input type="hidden" name="version" value="{{ $caso->version }}">
            <label for="accion">Acción</label>
            <select class="form-select mb-3" name="accion" id="accion">
                @if($caso->estado === 'cerrado')<option value="reabrir">Reabrir seguimiento</option>
                @else
                    <option value="asignar">{{ $caso->psicologo_id ? 'Transferir responsabilidad' : 'Asignar responsable' }}</option>
                    @if($caso->psicologo_id)<option value="nota" @selected(old('accion', 'nota') === 'nota')>Registrar seguimiento</option>@endif
                    @if($caso->estado === 'seguimiento')<option value="cerrar" @selected(old('accion') === 'cerrar')>Cerrar caso</option>@endif
                @endif
            </select>
            @if($caso->estado !== 'cerrado')
                <label for="psicologo_id">Responsable nuevo (solo al asignar o transferir)</label>
                <select class="form-select mb-3" id="psicologo_id" name="psicologo_id"><option value="">Selecciona un profesional</option>@foreach($psicologos as $psych)<option value="{{ $psych->id }}" @selected(old('psicologo_id', !$caso->psicologo_id ? auth()->user()->persona->psicologo->id : '') == $psych->id)>{{ $psych->persona?->nombre }} {{ $psych->persona?->apellido_paterno }}</option>@endforeach</select>
            @endif
            <label for="nota">Nota privada o motivo de la acción</label><textarea class="form-control mb-3" name="nota" id="nota" rows="5" maxlength="3000" required>{{ old('nota') }}</textarea>
            <p class="small text-body-secondary">Para cerrar, registra primero una valoración o un seguimiento y explica el motivo del cierre.</p>
            <button class="btn btn-primary tm-action">Guardar acción</button>
        </form>
    @else<p class="text-body-secondary mb-0">Solo el responsable puede agregar seguimiento, transferir o cerrar este caso.</p>@endif
    </section>
</div>
<div class="col-lg-7"><section class="tm-panel tm-body"><h3 class="h5 mb-4">Historial de atención</h3>
    <ol class="tm-timeline">@forelse($seguimientos as $item)<li><strong>{{ ucfirst($item->tipo) }}</strong><span class="d-block small text-body-secondary">{{ $item->created_at?->format('d/m/Y H:i') }} · {{ $item->autor?->name ?? 'Registro del sistema / autor no disponible' }}</span>
    @if($item->responsable)<span class="d-block small">Responsable: {{ $item->responsable->persona?->nombre }} {{ $item->responsable->persona?->apellido_paterno }}</span>@endif
    @if($item->nota)<p class="mt-2" style="white-space: pre-wrap; overflow-wrap: anywhere">{{ $item->nota }}</p>@endif
    @if($item->tipo === 'valoracion')<p class="small">Valoración registrada en el resultado de origen.</p>@endif
    </li>@empty<li>Sin movimientos registrados.</li>@endforelse</ol>{{ $seguimientos->links() }}
</section></div>
</div>
@endsection
