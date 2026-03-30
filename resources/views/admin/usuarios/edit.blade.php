@extends('layouts.app')

@section('title', 'Editar Usuario - PROMETEO')
@section('page-title', 'Configuración de Cuenta')
@section('page-subtitle', 'Actualización de credenciales y accesos')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-5">
            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                <i class="bi bi-person-gear fs-3"></i>
            </div>
            <div>
                <h4 class="fw-black mb-1 text-dark">Modificar Usuario</h4>
                <p class="text-muted mb-0">Cambia las contraseñas, actualiza el correo o modifica los roles asignados.</p>
            </div>
        </div>

        <form action="{{ route('admin.usuarios.update', $user) }}" method="POST" autocomplete="off">
            @csrf
            @method('PUT')
            @include('admin.usuarios.partials.form', ['user' => $user])
        </form>
    </div>
@endsection
