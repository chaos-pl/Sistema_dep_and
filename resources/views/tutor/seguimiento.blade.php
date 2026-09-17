@extends('layouts.app')
@section('title', 'Seguimiento de estudiantes - PROMETEO')
@section('page-title', 'Seguimiento pendiente')
@section('page-subtitle', 'Acompañamiento de tus grupos asignados')
@section('content')
<div class="tm-hero tm-enter">
    <span class="tm-eyebrow">Tutorías · Atención y acompañamiento</span>
    <h2>Estudiantes con seguimiento pendiente</h2>
    <p>Casos abiertos de atención psicológica. Cada estudiante aparece una vez, aunque tenga varios casos. Incluye pendientes de cualquier fecha.</p>
</div>
<section class="tm-panel tm-enter">
    <div class="tm-heading"><div class="d-flex align-items-center gap-3"><span class="tm-icon"><i class="bi bi-heart-pulse"></i></span><div><h3>{{ $estudiantes->total() }} estudiantes</h3><p>Información general para acompañar a tus grupos.</p></div></div><a class="btn btn-outline-primary tm-action" href="{{ route('tutor.dashboard') }}"><i class="bi bi-arrow-left"></i> Volver al panel</a></div>
    <div class="table-responsive"><table class="table tm-table"><thead><tr><th>Estudiante</th><th>Grupo</th><th>Estado</th><th>Acción</th></tr></thead><tbody>
    @forelse($estudiantes as $estudiante)
        <tr><td class="fw-bold">{{ $estudiante->persona?->nombre }} {{ $estudiante->persona?->apellido_paterno }}</td><td>{{ $estudiante->grupo?->nombre }}</td><td><x-tamizaje-status label="Seguimiento pendiente" /></td><td><a class="btn btn-outline-primary tm-action" href="{{ route('tutor.grupos.show', $estudiante->grupo_id) }}#seguimiento-dass21">Ver grupo <i class="bi bi-arrow-right"></i></a></td></tr>
    @empty<tr><td colspan="4"><div class="tm-empty"><i class="bi bi-check2-circle"></i><strong>Sin seguimiento pendiente registrado</strong><p>No hay estudiantes con seguimiento abierto en tus grupos actuales.</p></div></td></tr>@endforelse
    </tbody></table></div><div class="tm-footer">{{ $estudiantes->links() }}</div>
</section>
@endsection
