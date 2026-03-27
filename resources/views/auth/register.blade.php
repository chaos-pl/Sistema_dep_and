@extends('layouts.guest')

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-10">
                <div class="auth-card p-4 p-md-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">Registro de estudiante</h2>
                        <p class="text-muted mb-0">Crea tu cuenta para acceder al sistema</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nombre de usuario</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}">
                                @error('name')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Correo electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                                @error('email')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <input type="password" name="password" class="form-control">
                                @error('password')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Nombre</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}">
                                @error('nombre')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Apellido paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno') }}">
                                @error('apellido_paterno')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Apellido materno</label>
                                <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno') }}">
                                @error('apellido_materno')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Fecha de nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento') }}">
                                @error('fecha_nacimiento')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Género</label>
                                <select name="genero" class="form-select">
                                    <option value="">Selecciona</option>
                                    <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="otro" {{ old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    <option value="prefiero_no_decirlo" {{ old('genero') == 'prefiero_no_decirlo' ? 'selected' : '' }}>Prefiero no decirlo</option>
                                </select>
                                @error('genero')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Teléfono</label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}">
                                @error('telefono')
                                <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button class="btn btn-primary">
                                <i class="bi bi-person-plus me-2"></i>Registrarme
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
