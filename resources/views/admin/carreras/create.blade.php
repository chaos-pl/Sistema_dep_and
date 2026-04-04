@extends('layouts.app')

@section('title', 'Nueva Carrera - PROMETEO')
@section('page-title', 'Registrar Carrera')
@section('page-subtitle', 'Alta administrativa de un nuevo programa educativo')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-mortarboard-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Nueva Carrera</h4>
                        <p class="text-body-secondary mb-0 small">Ingresa el nombre oficial de la carrera para registrarla en el sistema.</p>
                    </div>
                </div>

                <form action="{{ route('admin.carreras.store') }}" method="POST">
                    @include('admin.carreras.partials.form', [
                        'submitText' => 'Guardar Carrera',
                        'method' => 'POST'
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection
