@extends('layouts.app')

@section('title', 'Dashboard principal')
@section('page-title', 'Panel principal según rol')
@section('page-subtitle', 'Este contenedor enruta visualmente a la experiencia del estudiante, tutor o psicólogo.')

@section('content')
    @php($rol = auth()->user()->rol ?? 'estudiante')

    @if ($rol === 'estudiante')
        @include('estudiante.dashboard')
    @elseif ($rol === 'tutor')
        @include('tutor.dashboard')
    @elseif ($rol === 'psicologo')
        @include('psicologo.dashboard')
    @else
        <div class="app-card p-4">
            <h3 class="fw-bold">Rol no configurado</h3>
            <p class="text-muted-soft mb-0">Configura un rol válido para visualizar el menú contextual del sistema.</p>
        </div>
    @endif
@endsection
