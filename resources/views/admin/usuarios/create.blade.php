@extends('layouts.app')

@section('title', 'Crear usuario')
@section('page-title', 'Nuevo usuario')
@section('page-subtitle', 'Registro de cuenta y asignación de roles')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Registrar usuario</h4>
            <p class="text-muted mb-0">Crea la cuenta, asigna roles y vincula una persona si lo deseas.</p>
        </div>

        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf
            @include('admin.usuarios.partials.form', ['user' => null])
        </form>
    </div>
@endsection
