@extends('layouts.app')

@section('title', 'Perfil')
@section('page-title', 'Mi perfil')
@section('page-subtitle', 'Información de usuario y persona')

@section('content')
<div class="row justify-content-center">
    <div class="col-xl-9">
        <div class="app-card p-4 p-md-5">
            <div class="mb-4">
                <h3 class="fw-bold mb-1">Perfil</h3>
                <p class="text-muted mb-0">Actualiza tus datos generales y personales.</p>
            </div>

            <form action="{{ route('perfil.update') }}" method="POST">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Usuario</label>
                        <input type="text" name="name" class="form-control" value="{{ auth()->user()->name }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">Correo</label>
                        <input type="email" name="email" class="form-control" value="{{ auth()->user()->email }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{ $persona->nombre ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Apellido paterno</label>
                        <input type="text" name="apellido_paterno" class="form-control" value="{{ $persona->apellido_paterno ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Apellido materno</label>
                        <input type="text" name="apellido_materno" class="form-control" value="{{ $persona->apellido_materno ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Fecha de nacimiento</label>
                        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ $persona->fecha_nacimiento ?? '' }}">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Género</label>
                        <select name="genero" class="form-select">
                            <option value="masculino" {{ ($persona->genero ?? '') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                            <option value="femenino" {{ ($persona->genero ?? '') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                            <option value="otro" {{ ($persona->genero ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                            <option value="prefiero_no_decirlo" {{ ($persona->genero ?? '') == 'prefiero_no_decirlo' ? 'selected' : '' }}>Prefiero no decirlo</option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">Teléfono</label>
                        <input type="text" name="telefono" class="form-control" value="{{ $persona->telefono ?? '' }}">
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>Guardar cambios
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
