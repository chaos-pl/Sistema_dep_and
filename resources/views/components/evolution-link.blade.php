@if($student && auth()->user()->hasRole('psicologo'))
@can('evaluaciones.historial.global') @can('evaluaciones.respuestas.detalle')
<a class="btn btn-outline-primary tm-action my-2" href="{{ route('evolucion.show', $student) }}"><i class="bi bi-graph-up"></i> Evolución del estudiante</a>
@endcan @endcan
@endif
