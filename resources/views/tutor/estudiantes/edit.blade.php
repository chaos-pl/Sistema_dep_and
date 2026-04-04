@extends('layouts.app')

@section('title', 'Editar Estudiante - PROMETEO')
@section('page-title', 'Editar Estudiante')
@section('page-subtitle', 'Actualiza el expediente del estudiante')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-pencil-fill fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Editando Estudiante</h4>
                        <p class="text-body-secondary mb-0 small">Estudiante del grupo: <strong class="text-primary">{{ $estudiante->grupo->nombre ?? 'Grupo' }}</strong></p>
                    </div>
                </div>

                <form action="{{ route('tutor.estudiantes.update', $estudiante->id) }}" method="POST">
                    @include('tutor.estudiantes.partials.form', [
                        'tutor' => null,
                        'estudiante' => $estudiante,
                        'submitText' => 'Actualizar Expediente',
                        'cancelRoute' => route('tutor.grupos.show', $estudiante->grupo_id),
                        'method' => 'PUT'
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection
