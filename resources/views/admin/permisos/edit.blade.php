@extends('layouts.app')

@section('title', 'Editar Permiso - PROMETEO')
@section('page-title', 'Editar Permiso')
@section('page-subtitle', 'Actualización de capacidad del sistema')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-5">
            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                <i class="bi bi-pencil-square fs-3"></i>
            </div>
            <div>
                <h4 class="fw-black mb-1 text-dark">Modificar Permiso</h4>
                <p class="text-muted mb-0">Atención: Cambiar el nombre del permiso puede afectar partes del sistema que dependan de él.</p>
            </div>
        </div>

        <form action="{{ route('admin.permisos.update', $permiso) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.permisos.partials.form')
        </form>
    </div>
@endsection
