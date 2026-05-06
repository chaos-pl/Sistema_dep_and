@extends('layouts.app')
@section('title', 'Asignaciones Tutor-Grupo - Control Escolar')
@section('page-title', 'Asignaciones Tutor-Grupo')
@section('page-subtitle', 'Gestión de asignaciones por ciclo escolar')
@push('styles')
<style>
.hover-elevate{transition:transform .2s ease,box-shadow .2s ease}.hover-elevate:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08)!important}
.modal-content{border-radius:1.5rem;overflow:hidden;border:0;box-shadow:0 25px 50px -12px rgba(0,0,0,.5)}.modal-header-custom{background-color:var(--app-primary);color:#fff;border-bottom:0;padding:1.5rem 2rem}.modal-body-custom{padding:2rem;background-color:var(--app-surface)}.modal-footer-custom{padding:1.5rem 2rem;border-top:1px solid rgba(148,163,184,.15);background-color:rgba(148,163,184,.05)}
body.theme-dark .modal-content,body.theme-system .modal-content{background-color:#1e293b!important;border:1px solid rgba(255,255,255,.1)}body.theme-dark .modal-body-custom,body.theme-system .modal-body-custom{background-color:#0f172a!important}body.theme-dark .modal-footer-custom,body.theme-system .modal-footer-custom{background-color:#1e293b!important;border-color:rgba(255,255,255,.1)!important}
.modal.fade{perspective:2000px}.modal.fade .modal-dialog{opacity:0;transform:translateZ(-500px) rotateY(90deg) scale(.5);transition:transform .7s cubic-bezier(.165,.84,.44,1),opacity .4s ease-in-out}.modal.show .modal-dialog{opacity:1;transform:translateZ(0) rotateY(0) scale(1)}.modal-backdrop.show{opacity:.75;backdrop-filter:blur(8px) brightness(.4);background:#000}
</style>
@endpush
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="row align-items-center justify-content-between mb-5 gy-3">
        <div class="col-lg-6"><h4 class="fw-black text-body mb-1"><i class="bi bi-arrow-left-right text-primary me-2"></i>Asignaciones Tutor-Grupo</h4><p class="text-body-secondary mb-0">Vincula tutores con grupos por ciclo escolar.</p></div>
        <div class="col-lg-6 d-flex justify-content-lg-end">
            @can('tutores.asignar_grupo')<button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#modalCreate"><i class="bi bi-plus-lg me-2"></i>Nueva Asignación</button>@endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Tutor</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Grupo</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Carrera</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Ciclo</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Estado</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($asignaciones as $asig)
                <tr class="border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3" style="transition:transform .2s"><div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-person-video3"></i></div><div><h6 class="fw-bold mb-0 text-body">{{ $asig->tutor?->persona?->nombre }} {{ $asig->tutor?->persona?->apellido_paterno }}</h6></div></div></td>
                    <td class="py-3 border-0 text-body fw-bold">{{ $asig->grupo?->nombre ?? '—' }}</td>
                    <td class="py-3 border-0 text-body">{{ $asig->grupo?->carrera?->nombre ?? '—' }}</td>
                    <td class="text-center py-3 border-0 text-body">{{ $asig->cicloEscolar?->nombre ?? '—' }}</td>
                    <td class="text-center py-3 border-0"><span class="badge {{ $asig->estado==='activo'?'bg-success':'bg-secondary' }} rounded-pill px-3 py-2">{{ ucfirst($asig->estado) }}</span></td>
                    <td class="text-end px-4 py-3 border-0">
                        <form action="{{ route('control_escolar.asignaciones.destroy', $asig) }}" method="POST" class="d-inline m-0 form-delete">@csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Eliminar"><i class="bi bi-trash-fill"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center py-5 text-body-secondary border-0"><i class="bi bi-arrow-left-right fs-1 opacity-25 mb-3 d-block"></i>No hay asignaciones registradas.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $asignaciones->links() }}</div>
</div>

@can('tutores.asignar_grupo')
<div class="modal fade" id="modalCreate" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header modal-header-custom"><h5 class="modal-title fw-black mb-0"><i class="bi bi-plus-lg me-2"></i>Nueva Asignación</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('control_escolar.asignaciones.store') }}" method="POST">@csrf
        <div class="modal-body modal-body-custom"><div class="row g-4">
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Tutor</label><select name="tutor_id" class="form-select form-select-lg bg-body-tertiary" required><option value="">Seleccionar...</option>@foreach($tutores as $t)<option value="{{ $t->id }}">{{ $t->persona?->nombre }} {{ $t->persona?->apellido_paterno }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Grupo</label><select name="grupo_id" class="form-select form-select-lg bg-body-tertiary" required><option value="">Seleccionar...</option>@foreach($grupos as $g)<option value="{{ $g->id }}">{{ $g->nombre }}</option>@endforeach</select></div>
            <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Ciclo Escolar</label><select name="ciclo_escolar_id" class="form-select form-select-lg bg-body-tertiary"><option value="">Sin ciclo</option>@foreach($ciclos as $ci)<option value="{{ $ci->id }}">{{ $ci->nombre }}</option>@endforeach</select></div>
        </div></div>
        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-floppy-fill me-2"></i>Guardar</button></div>
    </form>
</div></div></div>
@endcan
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.modal').forEach(m=>document.body.appendChild(m));document.querySelectorAll('.form-delete').forEach(form=>{form.addEventListener('submit',function(e){e.preventDefault();const f=this;const dk=document.body.classList.contains('theme-dark')||(document.body.classList.contains('theme-system')&&window.matchMedia('(prefers-color-scheme:dark)').matches);Swal.fire({title:'¿Eliminar asignación?',text:'Se desvinculará al tutor del grupo.',icon:'error',showCancelButton:true,confirmButtonColor:'#ef4444',cancelButtonColor:dk?'#334155':'#e2e8f0',confirmButtonText:'<i class="bi bi-trash-fill me-1"></i> Eliminar',cancelButtonText:'Cancelar',background:dk?'#1e293b':'#fff',color:dk?'#f8fafc':'#1e293b',customClass:{popup:'rounded-4 shadow-lg',confirmButton:'btn btn-danger rounded-pill px-4 fw-bold',cancelButton:'btn rounded-pill px-4 fw-bold ms-2 '+(dk?'text-white btn-outline-secondary':'text-dark btn-light')},buttonsStyling:false}).then(r=>{if(r.isConfirmed)f.submit()})})})});
</script>
@endpush
