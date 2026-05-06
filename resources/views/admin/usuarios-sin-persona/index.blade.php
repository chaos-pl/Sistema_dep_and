@extends('layouts.app')
@section('title', 'Usuarios sin Persona - PROMETEO')
@section('page-title', 'Usuarios sin Persona')
@section('page-subtitle', 'Usuarios que no tienen datos personales vinculados')
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
        <div class="col-lg-6">
            <h4 class="fw-black text-body mb-1"><i class="bi bi-person-dash-fill text-danger me-2"></i>Usuarios sin Persona</h4>
            <p class="text-body-secondary mb-0">Usuarios registrados que no tienen datos personales (nombre, apellidos, etc.) vinculados al sistema.</p>
        </div>
        <div class="col-lg-6 d-flex justify-content-lg-end">
            <div class="position-relative w-100" style="max-width:320px"><div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2"><i class="bi bi-search"></i></div><input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar usuario..." onkeyup="filterRows()"></div>
        </div>
    </div>

    @if($usuarios->total() > 0)
    <div class="alert bg-danger bg-opacity-10 border border-danger border-opacity-25 rounded-4 mb-4 d-flex align-items-center gap-3">
        <div class="bg-danger bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;min-width:40px"><i class="bi bi-exclamation-triangle-fill text-danger"></i></div>
        <div><strong class="text-body">{{ $usuarios->total() }} usuario(s)</strong> <span class="text-body-secondary">no tienen datos personales vinculados.</span></div>
    </div>
    @endif

    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Usuario</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Correo Electrónico</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Roles</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Registrado</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acción</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($usuarios as $usuario)
                <tr class="data-row border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3"><div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-person-dash-fill"></i></div><div><h6 class="fw-bold mb-0 text-body row-name">{{ $usuario->name }}</h6><small class="text-body-secondary">ID: {{ $usuario->id }}</small></div></div></td>
                    <td class="py-3 border-0 text-body-secondary"><i class="bi bi-envelope-at me-1 opacity-50"></i>{{ $usuario->email }}</td>
                    <td class="text-center py-3 border-0">
                        @forelse($usuario->roles as $role)
                            <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-2 py-1 fw-semibold">{{ $role->name }}</span>
                        @empty
                            <span class="badge bg-secondary bg-opacity-50 rounded-pill px-2 py-1">Sin rol</span>
                        @endforelse
                    </td>
                    <td class="text-center py-3 border-0 text-body-secondary">{{ $usuario->created_at->format('d/m/Y') }}</td>
                    <td class="text-end px-4 py-3 border-0">
                        <button data-bs-toggle="modal" data-bs-target="#modalPersona{{ $usuario->id }}" class="btn btn-sm btn-primary rounded-pill fw-bold shadow-sm px-3"><i class="bi bi-person-plus-fill me-1"></i>Vincular Persona</button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-body-secondary border-0"><div class="d-flex flex-column align-items-center"><i class="bi bi-check-circle fs-1 text-success opacity-50 mb-3"></i><span class="fw-bold">¡Excelente! Todos los usuarios tienen persona vinculada.</span></div></td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    @if($usuarios->hasPages())<div class="mt-4">{{ $usuarios->links() }}</div>@endif
</div>

{{-- Modales --}}
@foreach($usuarios as $usuario)
<div class="modal fade" id="modalPersona{{ $usuario->id }}" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered modal-lg"><div class="modal-content">
    <div class="modal-header modal-header-custom"><h5 class="modal-title fw-black mb-0"><i class="bi bi-person-plus-fill me-2"></i>Vincular Datos Personales</h5><button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal"></button></div>
    <form action="{{ route('admin.usuarios-sin-persona.update', $usuario) }}" method="POST">@csrf @method('PUT')
        <div class="modal-body modal-body-custom">
            <div class="alert bg-body-tertiary border border-secondary border-opacity-10 rounded-4 mb-4">
                <div class="fw-bold text-body">{{ $usuario->name }}</div>
                <small class="text-body-secondary">{{ $usuario->email }}</small>
            </div>
            <div class="row g-4">
                <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Nombre <span class="text-danger">*</span></label><input type="text" name="nombre" class="form-control form-control-lg bg-body-tertiary" required></div>
                <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Paterno <span class="text-danger">*</span></label><input type="text" name="apellido_paterno" class="form-control form-control-lg bg-body-tertiary" required></div>
                <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Apellido Materno</label><input type="text" name="apellido_materno" class="form-control form-control-lg bg-body-tertiary"></div>
                <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Fecha de Nacimiento <span class="text-danger">*</span></label><input type="date" name="fecha_nacimiento" class="form-control form-control-lg bg-body-tertiary" required></div>
                <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Género <span class="text-danger">*</span></label><select name="genero" class="form-select form-select-lg bg-body-tertiary" required><option value="">Seleccionar...</option><option value="masculino">Masculino</option><option value="femenino">Femenino</option><option value="otro">Otro</option><option value="prefiero_no_decirlo">Prefiero no decirlo</option></select></div>
                <div class="col-md-4"><label class="form-label fw-bold text-body-secondary">Teléfono</label><input type="text" name="telefono" class="form-control form-control-lg bg-body-tertiary"></div>
            </div>
        </div>
        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2"><button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-check-lg me-2"></i>Vincular Persona</button></div>
    </form>
</div></div></div>
@endforeach
@endsection
@push('scripts')
<script>
function filterRows(){let f=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('.data-row').forEach(r=>{r.style.display=r.querySelector('.row-name').textContent.toLowerCase().includes(f)?'':'none'})}
document.addEventListener('DOMContentLoaded',()=>{document.querySelectorAll('.modal').forEach(m=>document.body.appendChild(m))});
</script>
@endpush
