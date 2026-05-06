@extends('layouts.app')
@section('title', 'Tutores - Control Escolar')
@section('page-title', 'Gestión de Tutores')
@section('page-subtitle', 'Administración académica de tutores')
@push('styles')
<style>
.search-input-prometeo{padding-left:2.8rem!important}.hover-elevate{transition:transform .2s ease,box-shadow .2s ease}.hover-elevate:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08)!important}
</style>
@endpush
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="row align-items-center justify-content-between mb-5 gy-3">
        <div class="col-lg-5"><h4 class="fw-black text-body mb-1"><i class="bi bi-person-video3 text-primary me-2"></i>Tutores</h4><p class="text-body-secondary mb-0">Gestión académica del personal de tutoría.</p></div>
        <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
            <div class="position-relative w-100" style="max-width:280px"><div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2"><i class="bi bi-search"></i></div><input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar tutor..." onkeyup="filterRows()"></div>
            @can('tutores.crear')<a href="{{ route('control_escolar.tutores.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm px-4"><i class="bi bi-plus-lg me-2"></i>Nuevo Tutor</a>@endcan
        </div>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Tutor</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">No. Empleado</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Grupos</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Estado</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($tutores as $tutor)
                <tr class="data-row border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3" style="transition:transform .2s"><div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-person-video3"></i></div><div><h6 class="fw-bold mb-0 text-body row-name">{{ $tutor->persona?->nombre }} {{ $tutor->persona?->apellido_paterno }} {{ $tutor->persona?->apellido_materno }}</h6><small class="text-body-secondary">{{ $tutor->persona?->user?->email ?? '—' }}</small></div></div></td>
                    <td class="text-center py-3 border-0 text-body fw-bold">{{ $tutor->numero_empleado }}</td>
                    <td class="text-center py-3 border-0"><span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold shadow-sm"><i class="bi bi-collection-fill me-1"></i>{{ $tutor->grupos_count ?? 0 }}</span></td>
                    <td class="text-center py-3 border-0"><span class="badge {{ ($tutor->estado ?? 'activo')==='activo'?'bg-success':'bg-secondary' }} rounded-pill px-3 py-2">{{ ucfirst($tutor->estado ?? 'activo') }}</span></td>
                    <td class="text-end px-4 py-3 border-0"><div class="d-flex justify-content-end gap-2">
                        @can('tutores.editar')<a href="{{ route('control_escolar.tutores.edit', $tutor) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Editar"><i class="bi bi-pencil-fill"></i></a>@endcan
                    </div></td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-body-secondary border-0"><i class="bi bi-person-x fs-1 opacity-25 mb-3 d-block"></i>No hay tutores registrados.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $tutores->links() }}</div>
</div>
@endsection
@push('scripts')
<script>function filterRows(){let f=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('.data-row').forEach(r=>{r.style.display=r.querySelector('.row-name').textContent.toLowerCase().includes(f)?'':'none'})}</script>
@endpush
