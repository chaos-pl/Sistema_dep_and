@extends('layouts.app')
@section('title', 'Último DASS-21 por estudiante')
@section('page-title', 'Estudiantes con último DASS-21 severo')
@section('content')
<section class="tm-panel tm-body">
    <a href="{{ route('psicologo.dashboard') }}" class="btn btn-outline-primary mb-3">Volver al panel clínico</a>
    <p>{{ $resultados->total() }} estudiantes con un resultado severo o extremadamente severo en su última aplicación completada.</p>
    <div class="table-responsive"><table class="table tm-table">
        <thead><tr><th>Estudiante</th><th>Última aplicación</th><th>Nivel</th><th>Caso de esta aplicación</th><th></th></tr></thead>
        <tbody>
        @forelse($resultados as $resultado)
            <tr>
                <td>{{ $resultado->estudiante?->persona?->nombre }} {{ $resultado->estudiante?->persona?->apellido_paterno }}</td>
                <td>{{ $resultado->completed_at->format('d/m/Y H:i') }}</td>
                <td><x-tamizaje-status :label="$resultado->max_severity_level" /></td>
                <td>{{ ucfirst($resultado->evaluacion?->casoAtencion?->estado ?? 'sin caso vinculado') }}</td>
                <td>@can('evaluaciones.respuestas.detalle')
                    @if($resultado->evaluacion?->estado === 'completada')
                        <a href="{{ route('psicologo.tamizajes.show', $resultado->evaluacion_id) }}">Revisar tamizaje</a>
                    @endif
                @endcan</td>
            </tr>
        @empty
            <tr><td colspan="5">Ningún estudiante cumple este criterio.</td></tr>
        @endforelse
        </tbody>
    </table></div>
    {{ $resultados->links() }}
</section>
@endsection
