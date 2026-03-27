@extends('layouts.app')

@section('title', 'Crear persona')
@section('page-title', 'Nueva persona')
@section('page-subtitle', 'Registro de perfil personal')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Registrar persona</h4>
            <p class="text-muted mb-0">Completa la información personal y vincúlala a un usuario si lo deseas.</p>
        </div>

        <form action="{{ route('admin.personas.store') }}" method="POST">
            @csrf
            @include('admin.personas.partials.form')
        </form>
    </div>
@endsection
