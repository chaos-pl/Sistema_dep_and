@extends('layouts.app')
@section('page-title', 'Detalle del tamizaje')
@section('content')
@include('components.evolution-link', ['student' => $evaluacion->estudiante])
<x-case-link :source="$evaluacion" />
<div class="tm-panel tm-enter tm-body">
    <a class="btn btn-outline-primary tm-action mb-3" href="{{ route('psicologo.tamizajes.index') }}"><i class="bi bi-arrow-left"></i> Volver a tamizajes</a>
    <span class="tm-eyebrow">Expediente · Resultado del tamizaje</span><h2 class="fw-black">{{ $evaluacion->instrumento?->nombre }}</h2>
    <p>{{ $evaluacion->estudiante?->persona?->nombre ?? 'Expediente no disponible' }} {{ $evaluacion->estudiante?->persona?->apellido_paterno }} · {{ $evaluacion->created_at?->format('d/m/Y H:i') }}</p>
    @if($evaluacion->dass21)
        <x-dass21-result :resultado="$evaluacion->dass21" />
        <details class="tm-details"><summary>Ver las 21 respuestas</summary><ol>@foreach($evaluacion->dass21->answers->sortBy('question.item_number') as $answer)<li>{{ $answer->question?->statement }} — {{ $answer->score }}</li>@endforeach</ol></details>
    @else
        <p>Puntaje: {{ $evaluacion->puntaje_resumen }} · Nivel: {{ $evaluacion->nivel_resumen }}</p>
        <details class="tm-details"><summary>Ver respuestas</summary><ul>@foreach($evaluacion->respuestas->sortBy('numero_pregunta') as $answer)<li>Pregunta {{ $answer->numero_pregunta }}: {{ $answer->valor }}</li>@endforeach</ul></details>
    @endif
</div>
<div class="tm-panel tm-enter tm-body">
    <div class="d-flex align-items-center gap-3 mb-4"><span class="tm-icon"><i class="bi bi-journal-medical"></i></span><h3 class="fw-bold mb-0">Valoración profesional</h3></div>
    @if($evaluacion->diagnostico)
        <p class="tm-note"><strong>Impresión diagnóstica privada:</strong> {{ $evaluacion->diagnostico->impresion_diagnostica }}</p>
        <p class="tm-note"><strong>Retroalimentación compartida:</strong> {{ $evaluacion->diagnostico->retroalimentacion_estudiante ?: 'Sin retroalimentación.' }}</p>
        <p>{{ $evaluacion->diagnostico->requiere_derivacion ? 'Requiere derivación' : 'Sin derivación registrada' }}</p>
    @else
        @can('diagnosticos.crear')
        <form class="tm-form" method="POST" action="{{ route('diagnosticos.store') }}">@csrf
            <input type="hidden" name="evaluacion_id" value="{{ $evaluacion->id }}">
            <label for="impresion">Impresión diagnóstica privada</label><textarea id="impresion" name="impresion_diagnostica" class="form-control mb-3" required>{{ old('impresion_diagnostica') }}</textarea>
            <label for="retroalimentacion">Retroalimentación visible para el estudiante</label><textarea id="retroalimentacion" name="retroalimentacion_estudiante" class="form-control mb-3">{{ old('retroalimentacion_estudiante') }}</textarea>
            <label><input type="checkbox" name="requiere_derivacion" value="1" @checked(old('requiere_derivacion'))> Requiere derivación</label>
            @foreach($errors->all() as $error)<p class="text-danger">{{ $error }}</p>@endforeach
            <button class="btn btn-primary tm-action d-block mt-3">Guardar valoración</button>
        </form>
        @endcan
    @endif
</div>
<div class="tm-panel tm-enter tm-body"><h3>Historial del estudiante</h3>
    <ul class="tm-timeline mt-4">@foreach($historial as $item)<li><a href="{{ route('psicologo.tamizajes.show', $item) }}">{{ $item->created_at?->format('d/m/Y H:i') }} · {{ $item->instrumento?->acronimo }}</a> — {{ $item->puntaje_resumen }} · {{ $item->nivel_resumen }}</li>@endforeach</ul>
    <div class="tm-footer">{{ $historial->links() }}</div>
</div>
@endsection
