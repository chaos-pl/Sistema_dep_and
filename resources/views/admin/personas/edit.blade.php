@extends('layouts.app')

@section('title', 'Editar Persona - PROMETEO')
@section('page-title', 'Editar Persona')
@section('page-subtitle', 'Actualización de perfil personal')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-5">
            <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                <i class="bi bi-pencil-square fs-3"></i>
            </div>
            <div>
                <h4 class="fw-black mb-1 text-dark">Actualizar Expediente</h4>
                <p class="text-muted mb-0">Modifica la información personal del registro seleccionado.</p>
            </div>
        </div>

        <form action="{{ route('admin.personas.update', $persona) }}" method="POST">
            @csrf
            @method('PUT')
            @include('admin.personas.partials.form')
        </form>
    </div>
@endsection
