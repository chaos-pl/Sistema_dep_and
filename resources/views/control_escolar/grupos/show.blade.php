@extends('layouts.app')
@section('title', 'Grupo {{ $grupo->nombre }} - Control Escolar')
@section('page-title', 'Detalle del Grupo')
@section('page-subtitle', 'Estudiantes asignados al grupo {{ $grupo->nombre }}')
@push('styles')
<style>.hover-elevate{transition:transform .2s ease,box-shadow .2s ease}.hover-elevate:hover{transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.08)!important}</style>
@endpush
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-5">
        <div>
            <h4 class="fw-black text-body mb-1"><i class="bi bi-collection-fill text-primary me-2"></i>{{ $grupo->nombre }}</h4>
            <p class="text-body-secondary mb-0">Carrera: <strong>{{ $grupo->carrera?->nombre ?? '—' }}</strong> · Periodo: <strong>{{ $grupo->periodo }}</strong> · Ciclo: <strong>{{ $grupo->cicloEscolar?->nombre ?? '—' }}</strong></p>
            <p class="text-body-secondary mb-0">Tutor: <strong>{{ $grupo->tutor?->persona?->nombre ?? 'Sin asignar' }} {{ $grupo->tutor?->persona?->apellido_paterno ?? '' }}</strong></p>
        </div>
        <a href="{{ route('control_escolar.grupos.index') }}" class="btn btn-light rounded-pill fw-bold shadow-sm px-4"><i class="bi bi-arrow-left me-2"></i>Volver a Grupos</a>
    </div>

    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Matrícula</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Estado</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Consentimiento</th>
                <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($grupo->estudiantes as $est)
                @php
                    $user = $est->persona?->user;
                    $consColor = match(true) {
                        $user?->acepto_consentimiento => 'bg-success',
                        default => 'bg-warning text-dark',
                    };
                    $consText = $user?->acepto_consentimiento ? 'Aceptado' : 'Pendiente';
                @endphp
                <tr class="border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0"><div class="d-flex align-items-center gap-3" style="transition:transform .2s"><div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width:40px;height:40px"><i class="bi bi-mortarboard-fill"></i></div><div><h6 class="fw-bold mb-0 text-body">{{ $est->persona?->nombre }} {{ $est->persona?->apellido_paterno }} {{ $est->persona?->apellido_materno }}</h6><small class="text-body-secondary">{{ $user?->email ?? '—' }}</small></div></div></td>
                    <td class="text-center py-3 border-0 text-body fw-bold">{{ $est->matricula }}</td>
                    <td class="text-center py-3 border-0"><span class="badge {{ $est->estado==='activo'?'bg-success':'bg-secondary' }} rounded-pill px-3 py-2">{{ ucfirst($est->estado ?? 'activo') }}</span></td>
                    <td class="text-center py-3 border-0"><span class="badge {{ $consColor }} rounded-pill px-3 py-2">{{ $consText }}</span></td>
                    <td class="text-end px-4 py-3 border-0">
                        <a href="{{ route('control_escolar.estudiantes.historial', $est) }}" class="btn btn-sm btn-light border text-info rounded-circle shadow-sm hover-elevate" style="width:35px;height:35px;display:inline-flex;align-items:center;justify-content:center" title="Historial"><i class="bi bi-clock-history"></i></a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="text-center py-5 text-body-secondary border-0"><i class="bi bi-person-x fs-1 opacity-25 mb-3 d-block"></i>No hay estudiantes en este grupo.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
