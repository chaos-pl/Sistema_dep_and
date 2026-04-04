@extends('layouts.app')

@section('title', 'Editar Carrera - PROMETEO')
@section('page-title', 'Editar Carrera')
@section('page-subtitle', 'Actualiza la información del programa educativo')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Modificando Carrera</h4>
                        <p class="text-body-secondary mb-0 small">Corrige o actualiza el nombre de este programa académico.</p>
                    </div>
                </div>

                <form action="{{ route('admin.carreras.update', $carrera->id) }}" method="POST">
                    @include('admin.carreras.partials.form', [
                        'carrera' => $carrera,
                        'submitText' => 'Actualizar Carrera',
                        'method' => 'PUT'
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection
