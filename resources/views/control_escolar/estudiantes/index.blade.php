@extends('layouts.app')
@section('title', 'Estudiantes - Control Escolar')
@section('page-title', 'Gestión de Estudiantes')
@section('page-subtitle', 'Asignación de grupos y estado escolar')
@push('styles')
<style>
.search-input-prometeo{padding-left:2.8rem!important}.hover-elevate{transition:transform .2s ease,box-shadow .2s ease}.hover-elevate:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08)!important}
.modal-content{border-radius:1.5rem;overflow:hidden;border:0;box-shadow:0 25px 50px -12px rgba(0,0,0,.5)}.modal-header-custom{background-color:var(--app-primary);color:#fff;border-bottom:0;padding:1.5rem 2rem}.modal-body-custom{padding:2rem;background-color:var(--app-surface)}.modal-footer-custom{padding:1.5rem 2rem;border-top:1px solid rgba(148,163,184,.15);background-color:rgba(148,163,184,.05)}
body.theme-dark .modal-content,body.theme-system .modal-content{background-color:#1e293b!important;border:1px solid rgba(255,255,255,.1)}body.theme-dark .modal-body-custom,body.theme-system .modal-body-custom{background-color:#0f172a!important}body.theme-dark .modal-footer-custom,body.theme-system .modal-footer-custom{background-color:#1e293b!important;border-color:rgba(255,255,255,.1)!important}
.modal.fade{perspective:2000px}.modal.fade .modal-dialog{opacity:0;transform:translateZ(-500px) rotateY(90deg) scale(.5);transition:transform .7s cubic-bezier(.165,.84,.44,1),opacity .4s ease-in-out}.modal.show .modal-dialog{opacity:1;transform:translateZ(0) rotateY(0) scale(1)}.modal-backdrop.show{opacity:.75;backdrop-filter:blur(8px) brightness(.4);background:#000}
</style>
@endpush
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="row align-items-center justify-content-between mb-5 gy-3">
        <div class="col-lg-5"><h4 class="fw-black text-body mb-1"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Estudiantes</h4><p class="text-body-secondary mb-0">Gestiona la asignación de grupos y estado escolar.</p></div>
        <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
            <div class="position-relative w-100" style="max-width:280px"><div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2"><i class="bi bi-search"></i></div><input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar..." onkeyup="filterRows()"></div>
            @can('estudiantes.crear')<a href="{{ route('control_escolar.estudiantes.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4"><i class="bi bi-plus-lg me-2"></i>Nuevo Estudiante</a>@endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Matrícula</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Grupo</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Estado</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($estudiantes as $est)
                <tr class="data-row border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3" style="transition:transform .2s"><div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-mortarboard-fill"></i></div><div><h6 class="fw-bold mb-0 text-body row-name">{{ $est->persona?->nombre }} {{ $est->persona?->apellido_paterno }}</h6><small class="text-body-secondary">{{ $est->persona?->user?->email ?? '—' }}</small></div></div></td>
                    <td class="text-center py-3 border-0 text-body fw-bold">{{ $est->matricula }}</td>
                    <td class="py-3 border-0 text-body">
                        @if($est->grupo)
                            <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold"><i class="bi bi-collection-fill me-1"></i>{{ $est->grupo->nombre }}</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-2"><i class="bi bi-exclamation-triangle me-1"></i>Sin grupo</span>
                        @endif
                    </td>
                    <td class="text-center py-3 border-0"><span class="badge {{ $est->estado==='activo'?'bg-success':($est->estado==='baja_definitiva'?'bg-danger':'bg-secondary') }} rounded-pill px-3 py-2">{{ ucfirst(str_replace('_',' ',$est->estado ?? 'activo')) }}</span></td>
                    <td class="text-end px-4 py-3 border-0"><div class="d-flex justify-content-end gap-2">
                        @can('estudiantes.editar')<a href="{{ route('control_escolar.estudiantes.edit', $est) }}" class="btn btn-sm btn-light border text-info rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Editar"><i class="bi bi-pencil-fill"></i></a>@endcan
                        @can('estudiantes.cambiar_grupo')<button data-bs-toggle="modal" data-bs-target="#modalCambio{{ $est->id }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Cambiar grupo"><i class="bi bi-arrow-left-right"></i></button>@endcan
                        @if($est->grupo_id)@can('estudiantes.quitar_grupo')<button data-bs-toggle="modal" data-bs-target="#modalQuitar{{ $est->id }}" class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Quitar de grupo"><i class="bi bi-x-circle"></i></button>@endcan @endif
                        @can('estudiantes.ver_historial')<a href="{{ route('control_escolar.estudiantes.historial', $est) }}" class="btn btn-sm btn-light border text-secondary rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Historial"><i class="bi bi-clock-history"></i></a>@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-body-secondary border-0"><i class="bi bi-person-x fs-1 opacity-25 mb-3 d-block"></i>No hay estudiantes registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $estudiantes->links() }}</div>
</div>

{{-- Modales Cambiar grupo --}}
@foreach($estudiantes as $est)
@can('estudiantes.cambiar_grupo')
<div class="modal fade" id="modalCambio{{ $est->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header modal-header-custom"><h5 class="modal-title fw-black mb-0"><i class="bi bi-arrow-left-right me-2"></i>Cambiar Grupo</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('control_escolar.estudiantes.updateGrupo', $est) }}" method="POST">@csrf @method('PUT')
        <div class="modal-body modal-body-custom">
            <div class="alert bg-body-tertiary border border-secondary border-opacity-10 rounded-4 mb-4">
                <div class="fw-bold text-body">{{ $est->persona?->nombre }} {{ $est->persona?->apellido_paterno }} {{ $est->persona?->apellido_materno }}</div>
                <small class="text-body-secondary">Matrícula: {{ $est->matricula }} · Grupo actual: {{ $est->grupo?->nombre ?? 'Sin grupo' }}</small>
            </div>
            <div class="row g-4">
                <div class="col-12"><label class="form-label fw-bold text-body-secondary">Nuevo grupo <span class="text-danger">*</span></label><select name="grupo_id" class="form-select form-select-lg bg-body-tertiary" required><option value="">Seleccionar grupo...</option>@foreach($grupos as $g)<option value="{{ $g->id }}" {{ $est->grupo_id==$g->id?'disabled':'' }}>{{ $g->nombre }} — {{ $g->carrera?->nombre }}{{ $est->grupo_id==$g->id?' (actual)':'' }}</option>@endforeach</select></div>
                <div class="col-12"><label class="form-label fw-bold text-body-secondary">Motivo <span class="text-danger">*</span></label><textarea name="motivo" rows="2" class="form-control bg-body-tertiary" placeholder="¿Por qué se cambia de grupo?" required></textarea></div>
                <div class="col-12"><label class="form-label fw-bold text-body-secondary">Observaciones</label><textarea name="observaciones" rows="2" class="form-control bg-body-tertiary" placeholder="Observaciones opcionales..."></textarea></div>
            </div>
        </div>
        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-check-lg me-2"></i>Confirmar Cambio</button></div>
    </form>
</div></div></div>
@endcan

{{-- Modal Quitar de grupo --}}
@if($est->grupo_id)
@can('estudiantes.quitar_grupo')
<div class="modal fade" id="modalQuitar{{ $est->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header modal-header-custom" style="background-color:#dc3545"><h5 class="modal-title fw-black mb-0"><i class="bi bi-x-circle me-2"></i>Quitar del Grupo</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('control_escolar.estudiantes.quitarGrupo', $est) }}" method="POST">@csrf @method('DELETE')
        <div class="modal-body modal-body-custom">
            <div class="alert bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 mb-4">
                <div class="fw-bold text-body"><i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>Esta acción quitará al estudiante de su grupo actual sin eliminarlo del sistema.</div>
                <small class="text-body-secondary">El estudiante quedará como "pendiente de asignación".</small>
            </div>
            <div class="alert bg-body-tertiary border border-secondary border-opacity-10 rounded-4 mb-4">
                <div class="fw-bold text-body">{{ $est->persona?->nombre }} {{ $est->persona?->apellido_paterno }}</div>
                <small class="text-body-secondary">Grupo actual: {{ $est->grupo?->nombre }}</small>
            </div>
            <div class="row g-4">
                <div class="col-12"><label class="form-label fw-bold text-body-secondary">Motivo <span class="text-danger">*</span></label><textarea name="motivo" rows="2" class="form-control bg-body-tertiary" placeholder="¿Por qué se quita del grupo?" required></textarea></div>
                <div class="col-12"><label class="form-label fw-bold text-body-secondary">Observaciones</label><textarea name="observaciones" rows="2" class="form-control bg-body-tertiary" placeholder="Observaciones opcionales..."></textarea></div>
            </div>
        </div>
        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-x-circle me-2"></i>Confirmar</button></div>
    </form>
</div></div></div>
@endcan
@endif
@endforeach
@endsection
@push('scripts')
<script>
function filterRows(){let f=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('.data-row').forEach(r=>{r.style.display=r.querySelector('.row-name').textContent.toLowerCase().includes(f)?'':'none'})}
document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.modal').forEach(m=>document.body.appendChild(m))});
</script>
@endpush
