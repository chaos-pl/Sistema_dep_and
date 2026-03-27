@extends('layouts.guest')

@push('styles')
    <style>
        .anime-card { opacity: 0; transform: scale(0.95); }
        .anime-input, .anime-btn { opacity: 0; }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-lg-10">
                <div class="auth-card p-4 p-md-5 bg-white shadow-lg rounded-4 anime-card border-0">
                    <div class="text-center mb-5 anime-input">
                        <div class="d-inline-flex bg-primary bg-opacity-10 text-primary p-3 rounded-circle mb-3">
                            <i class="bi bi-person-badge fs-2"></i>
                        </div>
                        <h2 class="fw-bold text-dark">Registro de estudiante</h2>
                        <p class="text-muted mb-0">Crea tu cuenta para acceder al sistema PROMETEO</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row g-4">
                            <div class="col-12 anime-input"><h5 class="fw-bold border-bottom pb-2 mb-0 mt-3"><i class="bi bi-shield-lock me-2"></i>Datos de acceso</h5></div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-semibold text-secondary">Nombre de usuario</label>
                                <input type="text" name="name" class="form-control form-control-lg bg-light" value="{{ old('name') }}">
                                @error('name')<small class="text-danger fw-medium"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-semibold text-secondary">Correo electrónico</label>
                                <input type="email" name="email" class="form-control form-control-lg bg-light" value="{{ old('email') }}">
                                @error('email')<small class="text-danger fw-medium"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-semibold text-secondary">Contraseña</label>
                                <input type="password" name="password" class="form-control form-control-lg bg-light">
                                @error('password')<small class="text-danger fw-medium"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-semibold text-secondary">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control form-control-lg bg-light">
                            </div>

                            <div class="col-12 anime-input"><h5 class="fw-bold border-bottom pb-2 mb-0 mt-4"><i class="bi bi-person-vcard me-2"></i>Datos personales</h5></div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-semibold text-secondary">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control bg-light" value="{{ old('nombre') }}">
                                @error('nombre')<small class="text-danger fw-medium">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-semibold text-secondary">Apellido paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control bg-light" value="{{ old('apellido_paterno') }}">
                                @error('apellido_paterno')<small class="text-danger fw-medium">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-semibold text-secondary">Apellido materno</label>
                                <input type="text" name="apellido_materno" class="form-control bg-light" value="{{ old('apellido_materno') }}">
                                @error('apellido_materno')<small class="text-danger fw-medium">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-semibold text-secondary">Fecha de nacimiento</label>
                                <input type="date" name="fecha_nacimiento" class="form-control bg-light" value="{{ old('fecha_nacimiento') }}">
                                @error('fecha_nacimiento')<small class="text-danger fw-medium">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-semibold text-secondary">Género</label>
                                <select name="genero" class="form-select bg-light">
                                    <option value="">Selecciona una opción</option>
                                    <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="otro" {{ old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    <option value="prefiero_no_decirlo" {{ old('genero') == 'prefiero_no_decirlo' ? 'selected' : '' }}>Prefiero no decirlo</option>
                                </select>
                                @error('genero')<small class="text-danger fw-medium">{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-semibold text-secondary">Teléfono</label>
                                <input type="text" name="telefono" class="form-control bg-light" value="{{ old('telefono') }}">
                                @error('telefono')<small class="text-danger fw-medium">{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-5 anime-btn">
                            <a href="{{ route('login') }}" class="text-decoration-none text-muted fw-medium"><i class="bi bi-arrow-left me-1"></i> Volver al login</a>
                            <button class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm fw-bold">
                                Registrarme <i class="bi bi-arrow-right ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Aparece la tarjeta con un ligero efecto de escala
            anime({
                targets: '.anime-card',
                scale: [0.95, 1],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1000
            });

            // Los inputs aparecen desde abajo secuencialmente
            anime({
                targets: '.anime-input',
                translateY: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(50, {start: 300}), // Muy rápido para no desesperar al usuario
                easing: 'easeOutQuad',
                duration: 800
            });

            // Botón inferior entra al final
            anime({
                targets: '.anime-btn',
                translateY: [15, 0],
                opacity: [0, 1],
                delay: 1000,
                easing: 'easeOutQuad'
            });
        });
    </script>
@endpush
