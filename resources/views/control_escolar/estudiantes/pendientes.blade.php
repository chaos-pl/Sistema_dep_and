@extends('layouts.app')
@section('title', 'Pendientes de Asignación - Control Escolar')
@section('page-title', 'Pendientes de Asignación')
@section('page-subtitle', 'Estudiantes que requieren asignación de grupo o expediente')
@push('styles')
<style>
.search-input-prometeo{padding-left:2.8rem!important}.hover-elevate{transition:transform .2s ease,box-shadow .2s ease}.hover-elevate:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08)!important}
.modal-content{border-radius:1.5rem;overflow:hidden;border:0;box-shadow:0 25px 50px -12px rgba(0,0,0,.5)}.modal-header-custom{background-color:var(--app-primary);color:#fff;border-bottom:0;padding:1.5rem 2rem}.modal-body-custom{padding:2rem;background-color:var(--app-surface)}.modal-footer-custom{padding:1.5rem 2rem;border-top:1px solid rgba(148,163,184,.15);background-color:rgba(148,163,184,.05)}
body.theme-dark .modal-content,body.theme-system .modal-content{background-color:#1e293b!important;border:1px solid rgba(255,255,255,.1)}body.theme-dark .modal-body-custom,body.theme-system .modal-body-custom{background-color:#0f172a!important}body.theme-dark .modal-footer-custom,body.theme-system .modal-footer-custom{background-color:#1e293b!important;border-color:rgba(255,255,255,.1)!important}
.modal.fade{perspective:2000px}.modal.fade .modal-dialog{opacity:0;transform:translateZ(-500px) rotateY(90deg) scale(.5);transition:transform .7s cubic-bezier(.165,.84,.44,1),opacity .4s ease-in-out}.modal.show .modal-dialog{opacity:1;transform:translateZ(0) rotateY(0) scale(1)}.modal-backdrop.show{opacity:.75;backdrop-filter:blur(8px) brightness(.4);background:#000}
</style>
@endpush
@section('content')

{{-- =============================================== --}}
{{-- SECCIÓN 1: Estudiantes SIN EXPEDIENTE (Users con rol estudiante sin registro en tabla estudiantes) --}}
{{-- =============================================== --}}
<div class="app-card p-4 p-md-5 mb-4">
    <div class="row align-items-center justify-content-between mb-4 gy-3">
        <div class="col-lg-8">
            <h4 class="fw-black text-body mb-1"><i class="bi bi-person-exclamation text-danger me-2"></i>Estudiantes sin Expediente</h4>
            <p class="text-body-secondary mb-0">Usuarios con rol de estudiante que aún no tienen matrícula ni asignación académica.</p>
        </div>
    </div>

    @if($sinExpediente->total() > 0)
    <div class="alert bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 mb-4 d-flex align-items-center gap-3">
        <div class="bg-danger bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;min-width:40px"><i class="bi bi-exclamation-triangle-fill text-danger"></i></div>
        <div><strong class="text-body">{{ $sinExpediente->total() }} usuario(s)</strong> <span class="text-body-secondary">con rol estudiante requieren expediente académico.</span></div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Usuario</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Correo</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Persona Vinculada</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acción</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($sinExpediente as $usuario)
                <tr class="data-row border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3"><div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-person-exclamation"></i></div><div><h6 class="fw-bold mb-0 text-body row-name">{{ $usuario->name }}</h6><small class="text-body-secondary">ID: {{ $usuario->id }}</small></div></div></td>
                    <td class="py-3 border-0 text-body-secondary"><i class="bi bi-envelope-at me-1 opacity-50"></i>{{ $usuario->email }}</td>
                    <td class="text-center py-3 border-0">
                        @if($usuario->persona)
                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i>{{ $usuario->persona->nombre }} {{ $usuario->persona->apellido_paterno }}</span>
                        @else
                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2"><i class="bi bi-x-circle-fill me-1"></i>Sin persona</span>
                        @endif
                    </td>
                    <td class="text-end px-4 py-3 border-0">
                        @if($usuario->persona)
                            <button data-bs-toggle="modal" data-bs-target="#modalExpediente{{ $usuario->id }}" class="btn btn-sm btn-primary rounded-pill fw-bold shadow-sm px-3" title="Completar expediente"><i class="bi bi-file-earmark-plus me-1"></i>Crear Expediente</button>
                        @else
                            <a href="{{ route('admin.personas.index') }}" class="btn btn-sm btn-warning text-dark rounded-pill fw-bold shadow-sm px-3"><i class="bi bi-exclamation-triangle me-1"></i>Vincular Persona</a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-5 text-body-secondary border-0"><div class="d-flex flex-column align-items-center"><i class="bi bi-check-circle fs-1 text-success opacity-50 mb-3"></i><span class="fw-bold">Todos los usuarios con rol estudiante tienen expediente.</span></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($sinExpediente->hasPages())<div class="mt-3">{{ $sinExpediente->links() }}</div>@endif
</div>

{{-- =============================================== --}}
{{-- SECCIÓN 2: Estudiantes CON EXPEDIENTE pero SIN GRUPO --}}
{{-- =============================================== --}}
<div class="app-card p-4 p-md-5 mb-4">
    <div class="row align-items-center justify-content-between mb-4 gy-3">
        <div class="col-lg-8">
            <h4 class="fw-black text-body mb-1"><i class="bi bi-collection text-warning me-2"></i>Estudiantes sin Grupo</h4>
            <p class="text-body-secondary mb-0">Estudiantes con expediente completo que necesitan asignación de grupo.</p>
        </div>
        <div class="col-lg-4 d-flex justify-content-lg-end">
            <div class="position-relative w-100" style="max-width:280px"><div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2"><i class="bi bi-search"></i></div><input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar estudiante..." onkeyup="filterRows()"></div>
        </div>
    </div>

    @if($sinGrupo->total() > 0)
    <div class="alert bg-warning bg-opacity-10 border border-warning border-opacity-25 rounded-4 mb-4 d-flex align-items-center gap-3">
        <div class="bg-warning bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;min-width:40px"><i class="bi bi-exclamation-triangle-fill text-warning"></i></div>
        <div><strong class="text-body">{{ $sinGrupo->total() }} estudiante(s)</strong> <span class="text-body-secondary">con expediente pero sin grupo asignado.</span></div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Matrícula</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Estado</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($sinGrupo as $est)
                <tr class="data-row grupo-row border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3"><div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-person-exclamation"></i></div><div><h6 class="fw-bold mb-0 text-body row-name">{{ $est->persona?->nombre }} {{ $est->persona?->apellido_paterno }} {{ $est->persona?->apellido_materno }}</h6><small class="text-body-secondary">{{ $est->persona?->user?->email ?? '—' }}</small></div></div></td>
                    <td class="text-center py-3 border-0 text-body fw-bold">{{ $est->matricula }}</td>
                    <td class="text-center py-3 border-0"><span class="badge bg-warning text-dark rounded-pill px-3 py-2">Sin grupo</span></td>
                    <td class="text-end px-4 py-3 border-0">
                        @can('estudiantes.asignar_grupo')
                        <button data-bs-toggle="modal" data-bs-target="#modalAsignar{{ $est->id }}" class="btn btn-sm btn-primary rounded-pill fw-bold shadow-sm px-3" title="Asignar grupo"><i class="bi bi-plus-circle me-1"></i>Asignar Grupo</button>
                        @endcan
                    </td>
                </tr>
            @empty
                <tr><td colspan="4" class="text-center py-5 text-body-secondary border-0"><div class="d-flex flex-column align-items-center"><i class="bi bi-check-circle fs-1 text-success opacity-50 mb-3"></i><span class="fw-bold">Todos los estudiantes con expediente tienen grupo.</span></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($sinGrupo->hasPages())<div class="mt-3">{{ $sinGrupo->links() }}</div>@endif
</div>

{{-- =============================================== --}}
{{-- MODALES: Crear expediente (para sinExpediente) --}}
{{-- =============================================== --}}
@php $gruposForm = $grupos; @endphp
@foreach($sinExpediente as $usuario)
    @if($usuario->persona)
    <div class="modal fade" id="modalExpediente{{ $usuario->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
        <div class="modal-header modal-header-custom"><h5 class="modal-title fw-black mb-0"><i class="bi bi-file-earmark-plus me-2"></i>Crear Expediente</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
        <form action="{{ route('admin.expedientes-pendientes.update', $usuario->id) }}" method="POST">@csrf @method('PUT')
            <div class="modal-body modal-body-custom">
                <div class="alert bg-body-tertiary border border-secondary border-opacity-10 rounded-4 mb-4">
                    <div class="fw-bold text-body">{{ $usuario->persona->nombre }} {{ $usuario->persona->apellido_paterno }} {{ $usuario->persona->apellido_materno }}</div>
                    <small class="text-body-secondary">{{ $usuario->email }}</small>
                </div>
                <div class="row g-4">
                    <div class="col-md-6"><label class="form-label fw-bold text-body-secondary">Matrícula <span class="text-danger">*</span></label><input type="text" name="matricula" class="form-control form-control-lg bg-body-tertiary" placeholder="Ej. 20261001" required></div>
                    <div class="col-md-6"><label class="form-label fw-bold text-body-secondary">Grupo <span class="text-danger">*</span></label><select name="grupo_id" class="form-select form-select-lg bg-body-tertiary" required><option value="">Seleccionar grupo...</option>@foreach($gruposForm as $g)<option value="{{ $g->id }}">{{ $g->nombre }} — {{ $g->carrera?->nombre }}</option>@endforeach</select></div>
                </div>
            </div>
            <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-check-lg me-2"></i>Crear Expediente</button></div>
        </form>
    </div></div></div>
    @endif
@endforeach

{{-- =============================================== --}}
{{-- MODALES: Asignar grupo (para sinGrupo) --}}
{{-- =============================================== --}}
@foreach($sinGrupo as $est)
@can('estudiantes.asignar_grupo')
<div class="modal fade" id="modalAsignar{{ $est->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header modal-header-custom"><h5 class="modal-title fw-black mb-0"><i class="bi bi-plus-circle me-2"></i>Asignar Grupo</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('control_escolar.pendientes.asignarGrupo', $est) }}" method="POST">@csrf @method('PUT')
        <div class="modal-body modal-body-custom">
            <div class="alert bg-body-tertiary border border-secondary border-opacity-10 rounded-4 mb-4">
                <div class="fw-bold text-body">{{ $est->persona?->nombre }} {{ $est->persona?->apellido_paterno }} {{ $est->persona?->apellido_materno }}</div>
                <small class="text-body-secondary">Matrícula: {{ $est->matricula }}</small>
            </div>
            <div class="row g-4">
                <div class="col-12"><label class="form-label fw-bold text-body-secondary">Grupo a asignar <span class="text-danger">*</span></label><select name="grupo_id" class="form-select form-select-lg bg-body-tertiary" required><option value="">Seleccionar grupo...</option>@foreach($grupos as $g)<option value="{{ $g->id }}">{{ $g->nombre }} — {{ $g->carrera?->nombre }}</option>@endforeach</select></div>
                <div class="col-12"><label class="form-label fw-bold text-body-secondary">Observaciones</label><textarea name="observaciones" rows="2" class="form-control bg-body-tertiary" placeholder="Observaciones opcionales..."></textarea></div>
            </div>
        </div>
        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-check-lg me-2"></i>Asignar Grupo</button></div>
    </form>
</div></div></div>
@endcan
@endforeach
@endsection
@push('scripts')
<script>
function filterRows(){let f=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('.grupo-row').forEach(r=>{r.style.display=r.querySelector('.row-name').textContent.toLowerCase().includes(f)?'':'none'})}
document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.modal').forEach(m=>document.body.appendChild(m))});
</script>
@endpush
