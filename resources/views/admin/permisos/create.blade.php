@extends('layouts.app')

@section('title', 'Crear Permiso - PROMETEO')
@section('page-title', 'Nuevo Permiso')
@section('page-subtitle', 'Registro de capacidad del sistema')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-5">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                <i class="bi bi-key-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-black mb-1 text-dark">Crear Permiso</h4>
                <p class="text-muted mb-0">Registra una nueva regla de acceso para posteriormente asignarla a los roles.</p>
            </div>
        </div>

        <form action="{{ route('admin.permisos.store') }}" method="POST">
            @csrf
            @include('admin.permisos.partials.form')
        </form>
    </div>
@endsection
