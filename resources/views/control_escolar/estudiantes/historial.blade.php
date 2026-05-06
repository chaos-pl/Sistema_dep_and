@extends('layouts.app')
@section('title', 'Historial - {{ $estudiante->persona?->nombre }}')
@section('page-title', 'Historial de Movimientos')
@section('page-subtitle', 'Registro de cambios de grupo del estudiante')
@section('content')
<div class="app-card p-4 p-md-5 mb-4">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start gap-3 mb-5">
        <div>
            <h4 class="fw-black text-body mb-1"><i class="bi bi-clock-history text-primary me-2"></i>{{ $estudiante->persona?->nombre }} {{ $estudiante->persona?->apellido_paterno }} {{ $estudiante->persona?->apellido_materno }}</h4>
            <p class="text-body-secondary mb-0">Matrícula: <strong>{{ $estudiante->matricula }}</strong> · Grupo actual: <strong>{{ $estudiante->grupo?->nombre ?? 'Sin grupo' }}</strong></p>
        </div>
        <a href="{{ route('control_escolar.estudiantes.index') }}" class="btn btn-light rounded-pill fw-bold shadow-sm px-4"><i class="bi bi-arrow-left me-2"></i>Volver</a>
    </div>
    <div class="table-responsive">
        <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
            <thead class="bg-body-tertiary"><tr>
                <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Fecha</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0">Acción</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Grupo Origen</th>
                <th class="py-3 text-body-secondary fw-bold text-center border-0"><i class="bi bi-arrow-right"></i></th>
                <th class="py-3 text-body-secondary fw-bold border-0">Grupo Destino</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Motivo</th>
                <th class="py-3 text-body-secondary fw-bold border-0">Observaciones</th>
                <th class="py-3 px-4 rounded-end-3 text-body-secondary fw-bold border-0">Realizado por</th>
            </tr></thead>
            <tbody class="border-top-0">
            @forelse($movimientos as $mov)
                @php
                    $badgeColor = match($mov->accion) {
                        'asignado' => 'bg-success',
                        'cambiado' => 'bg-warning text-dark',
                        'quitado' => 'bg-danger',
                        default => 'bg-secondary'
                    };
                @endphp
                <tr class="border-bottom border-secondary border-opacity-10">
                    <td class="px-4 py-3 border-0 text-body fw-bold">{{ $mov->created_at->format('d/m/Y H:i') }}</td>
                    <td class="text-center py-3 border-0"><span class="badge {{ $badgeColor }} rounded-pill px-3 py-2">{{ ucfirst($mov->accion ?? 'cambiado') }}</span></td>
                    <td class="py-3 border-0 text-body">{{ $mov->grupoOrigen?->nombre ?? '—' }}</td>
                    <td class="py-3 border-0 text-center text-primary"><i class="bi bi-arrow-right-circle-fill fs-5"></i></td>
                    <td class="py-3 border-0 text-body">{{ $mov->grupoDestino?->nombre ?? 'Sin grupo' }}</td>
                    <td class="py-3 border-0 text-body-secondary" style="max-width:200px">{{ $mov->motivo }}</td>
                    <td class="py-3 border-0 text-body-secondary" style="max-width:200px">{{ $mov->observaciones ?? '—' }}</td>
                    <td class="px-4 py-3 border-0 text-body">{{ $mov->realizadoPor?->name ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center py-5 text-body-secondary border-0"><i class="bi bi-inbox fs-1 opacity-25 mb-3 d-block"></i>No hay movimientos registrados para este estudiante.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $movimientos->links() }}</div>
</div>
@endsection
