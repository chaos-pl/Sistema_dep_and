@extends('layouts.guest')

@section('title', 'Registro - PROMETEO')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ filemtime(public_path('css/login.css')) }}">
@endpush

@section('content')
    <main class="register-page">
    <div class="login__backdrop" aria-hidden="true">
        <span class="login__aurora login__aurora--deep"></span>
        <span class="login__aurora login__aurora--cyan"></span>
        <span class="login__aurora login__aurora--dawn"></span>
        <x-auth-logo-background />
    </div>
    <a href="{{ route('login') }}" class="login__brand register-brand">
        <img class="login__emblem" src="{{ asset('img/logo_prometeo.png') }}" width="112" height="112" alt="">
        <span class="login__wordmark">PROMETEO<small>Monitoreo emocional</small></span>
    </a>
    <div class="container pb-4 position-relative z-2">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-10">
                <div class="register-card p-4 p-md-5 anime-card">

                    <div class="text-center mb-4 anime-input">
                        <h2 class="fw-black text-dark mb-1">Registro de Estudiante</h2>
                        <p class="text-muted mb-0 fw-medium">Crea tu cuenta para acceder al sistema PROMETEO</p>
                    </div>

                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row g-4">
                            <div class="col-12 anime-input">
                                <h5 class="fw-bold section-title">
                                    <i class="bi bi-shield-lock-fill me-2"></i>Credenciales de Acceso
                                </h5>
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-bold text-secondary">Nombre de usuario</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Ej. jperez22" required>
                                @error('name')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-bold text-secondary">Correo electrónico</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="tu@correo.com" required>
                                @error('email')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-bold text-secondary">Contraseña</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control pe-5" placeholder="Mínimo 8 caracteres" required>
                                    <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y text-muted border-0 bg-transparent me-2" onclick="togglePasswordVisibility('password', 'togglePasswordIcon')">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-bold text-secondary">Confirmar contraseña</label>
                                <div class="position-relative">
                                    <input type="password" name="password_confirmation" id="password_confirmation" class="form-control pe-5" placeholder="Repite la contraseña" required>
                                    <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y text-muted border-0 bg-transparent me-2" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')">
                                        <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="col-12 anime-input">
                                <h5 class="fw-bold section-title">
                                    <i class="bi bi-person-vcard-fill me-2"></i>Expediente Personal
                                </h5>
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-bold text-secondary">Nombre(s)</label>
                                <input type="text" name="nombre" class="form-control" value="{{ old('nombre') }}" placeholder="Ej. Ana" required>
                                @error('nombre')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-bold text-secondary">Apellido paterno</label>
                                <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno') }}" placeholder="Ej. López" required>
                                @error('apellido_paterno')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-bold text-secondary">Apellido materno <span class="fw-normal text-muted">(Opcional)</span></label>
                                <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno') }}" placeholder="Ej. Ruiz">
                                @error('apellido_materno')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-bold text-secondary">Fecha de nacimiento</label>
                                <input type="text" name="fecha_nacimiento" id="fechaNacimientoInput" class="form-control text-secondary" value="{{ old('fecha_nacimiento') }}" placeholder="Seleccionar fecha..." required readonly style="cursor: pointer; background-color: #ffffff;">
                                @error('fecha_nacimiento')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-bold text-secondary">Género</label>
                                <select name="genero" class="form-select text-secondary" required>
                                    <option value="">Selecciona...</option>
                                    <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="otro" {{ old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
                                    <option value="prefiero_no_decirlo" {{ old('genero') == 'prefiero_no_decirlo' ? 'selected' : '' }}>Prefiero no decirlo</option>
                                </select>
                                @error('genero')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-4 anime-input">
                                <label class="form-label fw-bold text-secondary">Teléfono <span class="fw-normal text-muted">(Opcional)</span></label>
                                <input type="text" name="telefono" class="form-control" value="{{ old('telefono') }}" placeholder="10 dígitos">
                                @error('telefono')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>
                        </div>

                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mt-5 pt-3 border-top anime-btn gap-3">
                            <a href="{{ route('login') }}" class="text-decoration-none fw-bold text-secondary hover-primary transition-all">
                                <i class="bi bi-arrow-left me-1"></i> Ya tengo cuenta
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg shadow-sm">
                                Crear mi cuenta <i class="bi bi-person-check-fill ms-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </main>
@endsection

@push('scripts')

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Función reutilizable para alternar visibilidad en cualquier campo de contraseña
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
                icon.style.color = 'var(--lg-brand)';
            } else {
                input.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                icon.style.color = ''; // Vuelve al gris original
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // ==============================================================
            // LÓGICA DE SWEETALERT2 PARA LA FECHA DE NACIMIENTO
            // ==============================================================
            const fechaInput = document.getElementById('fechaNacimientoInput');

            fechaInput.addEventListener('click', function() {
                // Obtenemos la fecha de hoy en formato YYYY-MM-DD para limitar el input
                const today = new Date().toISOString().split('T')[0];
                const currentValue = this.value;

                Swal.fire({
                    title: 'Selecciona tu fecha de nacimiento',
                    html: `
                        <input type="date" id="swal-date" class="swal2-input w-75" 
                               max="${today}" value="${currentValue}">
                    `,
                    showCancelButton: true,
                    confirmButtonText: '<i class="bi bi-calendar-check me-1"></i> Aceptar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#036b9b',
                    customClass: {
                        popup: 'rounded-4 shadow-lg',
                        confirmButton: 'btn btn-primary rounded-pill px-4 fw-bold shadow-sm',
                        cancelButton: 'btn btn-light rounded-pill px-4 fw-bold shadow-sm'
                    },
                    buttonsStyling: false,
                    preConfirm: () => {
                        const dateVal = document.getElementById('swal-date').value;
                        if (!dateVal) {
                            Swal.showValidationMessage('Por favor selecciona una fecha válida');
                        }
                        return dateVal;
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Asignamos la fecha seleccionada al input original
                        this.value = result.value;
                    }
                });
            });
        });
    </script>
@endpush