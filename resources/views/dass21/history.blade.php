@extends('layouts.app')
@section('title', 'Mi historial DASS-21 - PROMETEO')
@section('page-title', 'Mi historial DASS-21')
@section('page-subtitle', 'Tus aplicaciones y resultados en un solo lugar')
@section('content')
<div class="tm-hero tm-enter"><span class="tm-eyebrow">Mi bienestar · DASS-21</span><h2>Tu historial, paso a paso</h2><p>Revisa tus aplicaciones anteriores y la retroalimentación compartida por psicología.</p></div>
<section class="tm-panel tm-enter">
    <div class="tm-heading"><div class="d-flex gap-3 align-items-center"><span class="tm-icon"><i class="bi bi-clock-history"></i></span><div><h3>Mis aplicaciones</h3><p>{{ $evaluaciones->total() }} resultados registrados</p></div></div><a class="btn btn-outline-primary tm-action" href="{{ route('evaluaciones.index') }}"><i class="bi bi-arrow-left"></i> Volver a evaluaciones</a></div>
    <div class="tm-body"><ul class="tm-timeline">@forelse($evaluaciones as $item)<li><div class="d-flex flex-wrap justify-content-between align-items-center gap-3"><div><span class="tm-eyebrow">{{ $item->completed_at?->format('d/m/Y · H:i') }}</span><h3 class="h6 fw-bold">Tamizaje DASS-21</h3><x-tamizaje-status :label="$item->max_severity_level" /></div><a class="btn btn-outline-primary tm-action" href="{{ route('dass21.show', $item) }}">Ver mis resultados <i class="bi bi-arrow-right"></i></a></div></li>@empty<li class="tm-empty"><i class="bi bi-journal-heart"></i><strong>Todavía no tienes resultados.</strong><p>Tu primera aplicación aparecerá aquí cuando la completes.</p><a class="btn btn-primary tm-action mt-3" href="{{ route('dass21.create') }}">Comenzar DASS-21</a></li>@endforelse</ul></div>
    <div class="tm-footer">{{ $evaluaciones->links() }}</div>
</section>
@endsection
