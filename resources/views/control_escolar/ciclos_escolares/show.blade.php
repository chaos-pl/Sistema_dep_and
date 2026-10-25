@extends('layouts.app')
@section('title', 'Detalle del ciclo escolar')
@section('page-title', 'Detalle del ciclo escolar')
@section('content')
<div class="app-card p-4 mb-4">
    <div class="d-flex justify-content-between flex-wrap gap-3">
        <div><h3>{{ $cicloEscolar->nombre }}</h3><p>{{ $cicloEscolar->fecha_inicio->format('d/m/Y') }} — {{ $cicloEscolar->fecha_fin->format('d/m/Y') }} · {{ ucfirst($cicloEscolar->estado) }}</p></div>
        <a href="{{ route('control_escolar.ciclos-escolares.index') }}" class="btn btn-light align-self-start">Volver a ciclos</a>
    </div>
    @can('grupos.ver')
    <h5>Grupos del ciclo ({{ $grupos->total() }})</h5>
    <div class="table-responsive"><table class="table align-middle">
        <thead><tr><th>Grupo</th><th>Carrera</th><th>Tutor</th><th>Estudiantes</th><th>Detalle</th></tr></thead>
        <tbody>@forelse($grupos as $grupo)
        <tr><td>{{ $grupo->nombre }}</td><td>{{ $grupo->carrera?->nombre }}</td>
            <td>@if($grupo->tutor)
                @can('tutores.ver')<a href="{{ route('control_escolar.tutores.show', $grupo->tutor) }}">@endcan
                {{ $grupo->tutor->persona?->nombre }} {{ $grupo->tutor->persona?->apellido_paterno }}
                @can('tutores.ver')</a>@endcan
            @else Sin tutor @endif</td>
            <td>{{ $grupo->estudiantes_count }}</td><td><a href="{{ route('control_escolar.grupos.show', $grupo) }}" class="btn btn-sm btn-outline-primary">Ver grupo</a></td></tr>
        @empty<tr><td colspan="5">Este ciclo aún no tiene grupos.</td></tr>@endforelse</tbody>
    </table></div>
    {{ $grupos->links() }}
    @endcan
</div>
@endsection
