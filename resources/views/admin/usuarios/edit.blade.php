@extends('layouts.app')

@section('title', 'Editar usuario')
@section('page-title', 'Editar usuario')
@section('page-subtitle', 'Actualización de cuenta, roles y vinculación')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="mb-4">
            <h4 class="fw-bold mb-1">Editar usuario</h4>
            <p class="text-muted mb-0">Modifica los datos del usuario y sus roles.</p>
        </div>

        <form action="{{ route('admin.usuarios.update', $user) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.usuarios.partials.form', ['user' => $user])
        </form>
    </div>
@endsection
