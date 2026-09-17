@extends('layouts.app')
@section('page-title', 'Tu retroalimentación')
@section('content')
<section class="tm-panel tm-body">
    <a class="btn btn-outline-primary mb-3" href="{{ route('avisos.index') }}">Volver a notificaciones</a>
    <h2>Retroalimentación · {{ $instrumento }}</h2>
    <p class="text-body-secondary">Aplicación del {{ $fecha->format('d/m/Y H:i') }}</p>
    <div class="feedback-content">{{ $feedback }}</div>
</section>
@endsection
