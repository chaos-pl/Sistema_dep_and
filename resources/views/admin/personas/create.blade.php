@extends('layouts.app')

@section('title', 'Crear Persona - PROMETEO')
@section('page-title', 'Nueva Persona')
@section('page-subtitle', 'Registro de datos personales y demográficos')

@section('content')
    <div class="app-card p-4 p-md-5">
        <div class="d-flex align-items-center gap-3 border-bottom pb-4 mb-5">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                <i class="bi bi-person-plus-fill fs-3"></i>
            </div>
            <div>
                <h4 class="fw-black mb-1 text-dark">Registrar Nueva Persona</h4>
                <p class="text-muted mb-0">Completa la información personal y vincúlala a un usuario activo si es necesario.</p>
            </div>
        </div>

        <form action="{{ route('admin.personas.store') }}" method="POST">
            @csrf
            @include('admin.personas.partials.form')
        </form>
    </div>
@endsection
