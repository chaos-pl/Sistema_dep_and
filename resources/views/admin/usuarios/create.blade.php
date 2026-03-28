@extends('layouts.app')

@section('title', 'Crear Usuario - PROMETEO')
@section('page-title', 'Nueva Cuenta de Acceso')
@section('page-subtitle', 'Registro de credenciales y roles')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-5">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                <i class="bi bi-person-plus-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-black mb-1 text-dark">Registrar Nuevo Usuario</h4>
                <p class="text-muted mb-0">Genera las credenciales de acceso y asigna permisos mediante roles.</p>
            </div>
        </div>

        <form action="{{ route('admin.usuarios.store') }}" method="POST">
            @csrf
            @include('admin.usuarios.partials.form', ['user' => null])
        </form>
    </div>
@endsection
