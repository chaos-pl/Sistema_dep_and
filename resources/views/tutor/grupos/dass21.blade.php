@can('usuarios.ver.grupo')
<section class="tm-panel tm-enter" id="seguimiento-dass21">
    <div class="tm-heading"><div class="d-flex align-items-center gap-3"><span class="tm-icon"><i class="bi bi-clipboard2-check"></i></span><div><h2>Participación DASS-21</h2><p>Última aplicación registrada. El estado general incluye los casos de atención abiertos.</p></div></div>@can('alertas.ver.general')<a class="btn btn-outline-primary tm-action" href="{{ route('tutor.seguimiento') }}">Ver seguimiento <i class="bi bi-arrow-right"></i></a>@endcan</div>
    <div class="table-responsive"><table class="table tm-table"><thead><tr><th>Estudiante</th><th>Última aplicación</th><th>Participación</th>@can('alertas.ver.general')<th>Seguimiento</th>@endcan</tr></thead>
        <tbody>@forelse($grupo->estudiantes as $estudiante)
            @php($ultimo = $estudiante->latestDass21)
            <tr><td class="fw-bold">{{ $estudiante->persona?->nombre }} {{ $estudiante->persona?->apellido_paterno }}</td><td>{{ $ultimo?->completed_at?->format('d/m/Y') ?? 'Sin aplicación' }}</td><td><x-tamizaje-status :label="$ultimo ? 'Completado' : 'Pendiente'" /></td>
            @can('alertas.ver.general')<td><x-tamizaje-status :label="$estudiante->seguimientos_pendientes && $estudiante->estado === 'activo' ? 'Seguimiento pendiente' : 'Sin seguimiento pendiente registrado'" /></td>@endcan</tr>
        @empty<tr><td colspan="{{ auth()->user()->can('alertas.ver.general') ? 4 : 3 }}"><div class="tm-empty"><i class="bi bi-people"></i><p>No hay estudiantes registrados en este grupo.</p></div></td></tr>@endforelse</tbody>
    </table></div>
</section>
@endcan
