@extends('layouts.app')

@section('title', 'Nuevo Grupo - PROMETEO')
@section('page-title', 'Registrar Grupo')
@section('page-subtitle', 'Alta administrativa de un nuevo grupo escolar')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-secondary border-opacity-10 pb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                        <i class="bi bi-folder-plus fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-black mb-1 text-body">Nuevo Grupo</h4>
                        <p class="text-body-secondary mb-0 small">Asigna carrera, tutor y define el nombre y periodo.</p>
                    </div>
                </div>

                <form action="{{ route('admin.grupos.store') }}" method="POST">
                    @include('admin.grupos.partials.form', [
                        'submitText' => 'Guardar Grupo',
                        'method' => 'POST'
                    ])
                </form>
            </div>
        </div>
    </div>
@endsection
