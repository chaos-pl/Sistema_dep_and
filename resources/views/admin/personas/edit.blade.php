@extends('layouts.app')

@section('title', 'Editar persona')
@section('page-title', 'Editar persona')
@section('page-subtitle', 'Actualización de perfil personal')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Editar persona</h4>
            <p class="text-muted mb-0">Modifica la información del registro seleccionado.</p>
        </div>

        <form action="{{ route('admin.personas.update', $persona) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.personas.partials.form')
        </form>
    </div>
@endsection
