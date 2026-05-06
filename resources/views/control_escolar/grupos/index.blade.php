@extends('layouts.app')
@section('title', 'Grupos - Control Escolar')
@section('page-title', 'Grupos Escolares')
@section('page-subtitle', 'Gestión de grupos académicos')
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
        <div class="col-lg-5"><h4 class="fw-black text-body mb-1"><i class="bi bi-collection-fill text-primary me-2"></i>Grupos</h4><p class="text-body-secondary mb-0">Organiza la estructura académica.</p></div>
        <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
            <div class="position-relative w-100" style="max-width:320px"><div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2"><i class="bi bi-search"></i></div><input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar grupo..." onkeyup="filterRows()"></div>
            @can('grupos.crear')<button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalCreate"><i class="bi bi-plus-lg me-2"></i>Nuevo Grupo</button>@endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Grupo</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Carrera</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Periodo</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Ciclo</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Estudiantes</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Estado</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($grupos as $grupo)
                <tr class="data-row border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3" style="transition:transform .2s"><div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-collection-fill"></i></div><div><h6 class="fw-bold mb-0 text-body row-name">{{ $grupo->nombre }}</h6><small class="text-body-secondary">Tutor: {{ $grupo->tutor?->persona?->nombre ?? 'Sin asignar' }}</small></div></div></td>
                    <td class="py-3 border-0 text-body">{{ $grupo->carrera?->nombre ?? '—' }}</td>
                    <td class="text-center py-3 border-0 text-body">{{ $grupo->periodo }}</td>
                    <td class="text-center py-3 border-0 text-body">{{ $grupo->cicloEscolar?->nombre ?? '—' }}</td>
                    <td class="text-center py-3 border-0"><span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold shadow-sm">{{ $grupo->estudiantes_count }}</span></td>
                    <td class="text-center py-3 border-0"><span class="badge {{ $grupo->estado==='activo'?'bg-success':'bg-secondary' }} rounded-pill px-3 py-2">{{ ucfirst($grupo->estado) }}</span></td>
                    <td class="text-end px-4 py-3 border-0"><div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('control_escolar.grupos.show', $grupo) }}" class="btn btn-sm btn-light border text-info rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Ver estudiantes"><i class="bi bi-eye-fill"></i></a>
                        @can('grupos.editar')<button data-bs-toggle="modal" data-bs-target="#modalEdit{{ $grupo->id }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center"><i class="bi bi-pencil-fill"></i></button>@endcan
                        @can('grupos.eliminar')<form action="{{ route('control_escolar.grupos.destroy', $grupo) }}" method="POST" class="d-inline m-0 form-delete">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center"><i class="bi bi-trash-fill"></i></button></form>@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center py-5 text-body-secondary border-0"><i class="bi bi-collection fs-1 opacity-25 mb-3 d-block"></i>No hay grupos registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $grupos->links() }}</div>
</div>

@can('grupos.crear')
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header modal-header-custom"><h5 class="modal-title fw-black mb-0"><i class="bi bi-plus-lg me-2"></i>Nuevo Grupo</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('control_escolar.grupos.store') }}" method="POST">@csrf
        <div class="modal-body modal-body-custom"><div class="row g-4">
            <div class="col-md-6"><label class="form-label fw-bold text-body-secondary">Nombre del grupo</label><input type="text" name="nombre" class="form-control form-control-lg bg-body-tertiary" required></div>
            <div class="col-md-6"><label class="form-label fw-bold text-body-secondary">Periodo</label><input type="text" name="periodo" class="form-control form-control-lg bg-body-tertiary" placeholder="Ej. 2025-A" required></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Carrera</label><select name="carrera_id" class="form-select form-select-lg bg-body-tertiary" required><option value="">Seleccionar...</option>@foreach($carreras as $c)<option value="{{ $c->id }}">{{ $c->nombre }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Tutor</label><select name="tutor_id" class="form-select form-select-lg bg-body-tertiary"><option value="">Sin asignar</option>@foreach($tutores as $t)<option value="{{ $t->id }}">{{ $t->persona?->nombre }} {{ $t->persona?->apellido_paterno }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Ciclo Escolar</label><select name="ciclo_escolar_id" class="form-select form-select-lg bg-body-tertiary"><option value="">Sin asignar</option>@foreach($ciclos as $ci)<option value="{{ $ci->id }}">{{ $ci->nombre }}</option>@endforeach</select></div>
        </div></div>
        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-floppy-fill me-2"></i>Guardar</button></div>
    </form>
</div></div></div>
@endcan

@foreach($grupos as $grupo)
@can('grupos.editar')
<div class="modal fade" id="modalEdit{{ $grupo->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header modal-header-custom" style="background-color:var(--app-primary-dark)"><h5 class="modal-title fw-black mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Editar Grupo #{{ $grupo->id }}</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('control_escolar.grupos.update', $grupo) }}" method="POST">@csrf @method('PUT')
        <div class="modal-body modal-body-custom"><div class="row g-4">
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Nombre</label><input type="text" name="nombre" value="{{ $grupo->nombre }}" class="form-control form-control-lg bg-body-tertiary" required></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Periodo</label><input type="text" name="periodo" value="{{ $grupo->periodo }}" class="form-control form-control-lg bg-body-tertiary" required></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Estado</label><select name="estado" class="form-select form-select-lg bg-body-tertiary"><option value="activo" {{ $grupo->estado==='activo'?'selected':'' }}>Activo</option><option value="inactivo" {{ $grupo->estado==='inactivo'?'selected':'' }}>Inactivo</option></select></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Carrera</label><select name="carrera_id" class="form-select form-select-lg bg-body-tertiary" required>@foreach($carreras as $c)<option value="{{ $c->id }}" {{ $grupo->carrera_id==$c->id?'selected':'' }}>{{ $c->nombre }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Tutor</label><select name="tutor_id" class="form-select form-select-lg bg-body-tertiary"><option value="">Sin asignar</option>@foreach($tutores as $t)<option value="{{ $t->id }}" {{ $grupo->tutor_id==$t->id?'selected':'' }}>{{ $t->persona?->nombre }} {{ $t->persona?->apellido_paterno }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Ciclo</label><select name="ciclo_escolar_id" class="form-select form-select-lg bg-body-tertiary"><option value="">Sin asignar</option>@foreach($ciclos as $ci)<option value="{{ $ci->id }}" {{ $grupo->ciclo_escolar_id==$ci->id?'selected':'' }}>{{ $ci->nombre }}</option>@endforeach</select></div>
        </div></div>
        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-arrow-repeat me-2"></i>Actualizar</button></div>
    </form>
</div></div></div>
@endcan
@endforeach
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function filterRows(){let f=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('.data-row').forEach(r=>{r.style.display=r.querySelector('.row-name').textContent.toLowerCase().includes(f)?'':'none'})}
document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.modal').forEach(m=>document.body.appendChild(m));document.querySelectorAll('.form-delete').forEach(form=>{form.addEventListener('submit',function(e){e.preventDefault();const f=this;const dk=document.body.classList.contains('theme-dark')||(document.body.classList.contains('theme-system')&&window.matchMedia('(prefers-color-scheme:dark)').matches);Swal.fire({title:'¿Eliminar grupo?',text:'Esta acción no se puede deshacer.',icon:'error',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonColor:dk?'#334155':'#e2e8f0',confirmButtonText:'<i class="bi bi-trash-fill me-1"></i> Eliminar',cancelButtonText:'Cancelar',background:dk?'#1e293b':'#fff',color:dk?'#f8fafc':'#1e293b',customClass:{popup:'rounded-4 shadow-lg',confirmButton:'btn btn-danger rounded-pill px-4 fw-bold',cancelButton:'btn rounded-pill px-4 fw-bold ms-2 '+(dk?'text-white btn-outline-secondary':'text-dark btn-light')},buttonsStyling:false}).then(r=>{if(r.isConfirmed)f.submit()})})})});
</script>
@endpush
