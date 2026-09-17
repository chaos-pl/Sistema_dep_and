@can('evaluaciones.historial.global')
<section class="tm-panel tm-enter">
    <div class="tm-heading"><h2>DASS-21 recientes</h2><a class="btn btn-primary tm-action" href="{{ route('psicologo.tamizajes.index') }}">Todos los tamizajes</a></div>
    <div class="table-responsive"><table class="table tm-table"><thead><tr><th>Estudiante</th><th>Fecha</th><th>Depresión</th><th>Ansiedad</th><th>Estrés</th><th></th></tr></thead>
        <tbody>@forelse($dass21Recientes as $item)<tr>
            <td>{{ $item->estudiante?->persona?->nombre ?? 'Expediente no disponible' }} {{ $item->estudiante?->persona?->apellido_paterno }}</td><td>{{ $item->completed_at?->format('d/m/Y') }}</td>
            <td><strong class="d-block mb-2">{{ $item->depression_score }} / 42</strong><x-tamizaje-status :label="$item->depression_level" /></td><td><strong class="d-block mb-2">{{ $item->anxiety_score }} / 42</strong><x-tamizaje-status :label="$item->anxiety_level" /></td><td><strong class="d-block mb-2">{{ $item->stress_score }} / 42</strong><x-tamizaje-status :label="$item->stress_level" /></td>
            <td>@if($item->evaluacion_id)@can('evaluaciones.respuestas.detalle')<a class="btn btn-outline-primary tm-action" href="{{ route('psicologo.tamizajes.show', $item->evaluacion_id) }}">Revisar <i class="bi bi-arrow-up-right"></i></a>@endcan @else Histórico pendiente de vincular @endif</td>
        </tr>@empty<tr><td colspan="6"><div class="tm-empty"><i class="bi bi-clipboard-check"></i><strong>Todavía no hay resultados DASS-21.</strong><p>Las nuevas aplicaciones aparecerán aquí.</p></div></td></tr>@endforelse</tbody>
    </table></div>
</section>
@endcan
