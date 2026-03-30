@extends('layouts.guest')

@section('title', 'Registro - PROMETEO')

@push('styles')
    <style>
        /* VARIABLES DE COLOR (Tema Morado) */
        :root {
            --app-primary: #7c3aed; /* Morado principal */
            --app-primary-rgb: 124, 58, 237;
            --app-secondary: #db2777; /* Magenta para contraste */
            --app-surface: #ffffff;
        }

        /* Ocultamos elementos base para la animación */
        .anime-card { opacity: 0; transform: scale(0.95); }
        .anime-input, .anime-btn { opacity: 0; transform: translateY(20px); }

        /* =========================================
           FONDO ANIMADO LÍQUIDO (Tonos Morados)
           ========================================= */
        body {
            /* Fondo oscuro base con tonos violeta oscuro */
            background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 50%, #1e1b4b 100%);
            background-size: 400% 400%;
            animation: liquidBackground 15s ease infinite;
            overflow-x: hidden;
            min-height: 100vh;
        }

        @keyframes liquidBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Capa de plasma difuso */
        .plasma-bg {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 1;
            overflow: hidden;
            pointer-events: none;
        }

        .plasma-orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(90px);
            opacity: 0.25;
            will-change: transform;
        }

        /* Orbe Morado brillante */
        .orb-1 {
            width: 550px; height: 550px;
            background: var(--app-primary);
            top: -150px; left: -100px;
            animation: orbFloat1 22s infinite alternate;
        }

        /* Orbe Magenta/Rosa */
        .orb-2 {
            width: 450px; height: 450px;
            background: var(--app-secondary);
            bottom: -100px; right: -100px;
            animation: orbFloat2 28s infinite alternate-reverse;
        }

        @keyframes orbFloat1 { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(150px, 150px) scale(1.1); } }
        @keyframes orbFloat2 { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(-150px, -150px) scale(1.2); } }

        /* =========================================
           TARJETA DE REGISTRO (Glassmorphism)
           ========================================= */
        .auth-card {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 2rem;
            box-shadow: 0 40px 100px rgba(124, 58, 237, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.5);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            position: relative;
            z-index: 2;
        }

        .icon-circle {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, var(--app-primary) 0%, #5b21b6 100%);
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.4);
            margin-bottom: 1.5rem;
        }

        /* =========================================
           FORMULARIO
           ========================================= */
        .form-control, .form-select {
            border-radius: 1rem;
            border: 2px solid #e2e8f0;
            padding: 0.85rem 1.2rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            background-color: #f8fafc;
            color: #1e293b;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--app-primary);
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15);
            background-color: #ffffff;
        }

        .section-title {
            color: var(--app-primary);
            font-size: 1.1rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            border-bottom: 2px solid rgba(124, 58, 237, 0.1);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
            margin-top: 1rem;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--app-primary) 0%, #5b21b6 100%);
            border: none;
            border-radius: 1rem;
            padding: 0.9rem 2rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.4);
        }
    </style>
@endpush

@section('content')
    <div class="plasma-bg">
        <div class="plasma-orb orb-1"></div>
        <div class="plasma-orb orb-2"></div>
    </div>

    <div class="container py-5 position-relative z-2">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-9 col-lg-10">
                <div class="auth-card p-4 p-md-5 anime-card">

                    <div class="text-center mb-4 anime-input">
                        <div class="icon-circle">
                            <i class="bi bi-person-badge fs-2"></i>
                        </div>
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
                                <input type="password" name="password" class="form-control" placeholder="Mínimo 8 caracteres" required>
                                @error('password')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>

                            <div class="col-md-6 anime-input">
                                <label class="form-label fw-bold text-secondary">Confirmar contraseña</label>
                                <input type="password" name="password_confirmation" class="form-control" placeholder="Repite la contraseña" required>
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
                                <input type="date" name="fecha_nacimiento" class="form-control text-secondary" value="{{ old('fecha_nacimiento') }}" required>
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
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Aparece la tarjeta flotando hacia arriba con fade
            anime({
                targets: '.anime-card',
                translateY: [40, 0],
                scale: [0.98, 1],
                opacity: [0, 1],
                easing: 'easeOutExpo',
                duration: 1200
            });

            // Los inputs aparecen en cascada
            anime({
                targets: '.anime-input',
                translateY: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(60, {start: 300}), // Efecto cascada rápido
                easing: 'easeOutQuad',
                duration: 800
            });

            // Los botones entran al final
            anime({
                targets: '.anime-btn',
                translateY: [15, 0],
                opacity: [0, 1],
                delay: 1100,
                easing: 'easeOutQuad'
            });
        });
    </script>
@endpush
