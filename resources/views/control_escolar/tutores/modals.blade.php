@foreach($tutores as $tutor)
    @can('usuarios.eliminar')
    <div class="modal fade" id="modalDeleteTutor{{ $tutor->id }}" tabindex="-1" aria-label="Dar de baja tutor" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Dar de baja tutor</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <div class="modal-body">¿Deseas dar de baja a {{ $tutor->persona?->nombre }} {{ $tutor->persona?->apellido_paterno }}? Solo se permite si no tiene grupos asignados.</div>
            <form action="{{ route('control_escolar.tutores.destroy', $tutor) }}" method="POST">
                @csrf @method('DELETE')
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger">Dar de baja</button></div>
            </form>
        </div></div>
    </div>
    @endcan
    <div class="modal fade" id="modalShowTutor{{ $tutor->id }}" tabindex="-1" aria-label="Detalle del tutor" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Detalle del tutor</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <div class="modal-body">
                <h5>{{ $tutor->persona?->nombre }} {{ $tutor->persona?->apellido_paterno }} {{ $tutor->persona?->apellido_materno }}</h5>
                <dl class="row">
                    <dt class="col-sm-4">Número de empleado</dt><dd class="col-sm-8">{{ $tutor->numero_empleado }}</dd>
                    <dt class="col-sm-4">Correo</dt><dd class="col-sm-8">{{ $tutor->persona?->user?->email }}</dd>
                    <dt class="col-sm-4">Teléfono</dt><dd class="col-sm-8">{{ $tutor->persona?->telefono ?: 'Sin registrar' }}</dd>
                </dl>
                @can('grupos.ver')
                    <h6>Grupos asignados</h6>
                    <ul class="list-group">
                    @forelse($tutor->grupos as $grupo)
                        <li class="list-group-item"><a href="{{ route('control_escolar.grupos.show', $grupo) }}">{{ $grupo->nombre }}</a> — {{ $grupo->carrera?->nombre }} · {{ $grupo->periodo }}</li>
                    @empty
                        <li class="list-group-item">Sin grupos asignados.</li>
                    @endforelse
                    </ul>
                @endcan
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button></div>
        </div></div>
    </div>
    @can('tutores.editar')
    <div class="modal fade" id="modalEditTutor{{ $tutor->id }}" tabindex="-1" aria-label="Editar tutor" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable"><div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Editar tutor</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button></div>
            <form action="{{ route('control_escolar.tutores.update', $tutor) }}" method="POST" class="d-flex flex-column overflow-hidden">
                @include('admin.tutores.partials.form', ['tutor' => $tutor, 'submitText' => 'Actualizar Tutor', 'method' => 'PUT', 'isModal' => true, 'modalType' => 'edit', 'modalId' => $tutor->id])
            </form>
        </div></div>
    </div>
    @endcan
@endforeach
