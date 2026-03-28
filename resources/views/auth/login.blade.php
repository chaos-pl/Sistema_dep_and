@extends('layouts.guest')

@section('title', 'Iniciar sesión - PROMETEO')

@push('styles')
    <style>
        /* VARIABLES DE COLOR */
        :root {
            --app-primary: #7c3aed;
            --app-primary-soft: #ede9fe;
        }

        /* Ocultamos elementos base para la animación en cascada posterior */
        .anime-form-item, .anime-left-text {
            opacity: 0;
            transform: translateY(20px);
        }

        /* Contenedor principal con sombra suave y bordes grandes */
        .login-card {
            background: var(--app-surface);
            border-radius: 2.5rem;
            box-shadow: 0 30px 70px rgba(124, 58, 237, 0.12);
            overflow: hidden;
            border: 1px solid rgba(124, 58, 237, 0.05);
        }

        /* Panel izquierdo con gradiente oscuro para resaltar la luz */
        .login-sidebar {
            background: linear-gradient(135deg, #2e1065 0%, #7c3aed 100%);
            color: #ffffff;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center; /* Centrado horizontal */
            text-align: center; /* Centrado de texto */
        }

        /* =========================================
           EL EFECTO CINEMÁTICO ETÉREO (Logo)
           ========================================= */
        .prometeo-logo-container {
            display: flex;
            gap: 0.15rem;
            margin-bottom: 2.5rem;
            justify-content: center; /* Centrado de las letras */
        }

        .neon-tube {
            font-family: 'Montserrat', sans-serif;
            font-size: 4.5rem;
            font-weight: 900;

            /* ESTADO INICIAL: Transparente, borroso y más pequeño */
            color: #ffffff;
            opacity: 0;
            filter: blur(12px);
            transform: translateY(40px) scale(0.8);
            text-shadow: 0 0 0px rgba(255,255,255,0);

            will-change: transform, opacity, filter, text-shadow, color;
            display: inline-block;
        }

        /* PULSO SUAVE (Respiración) AL TERMINAR */
        .neon-steady .neon-tube {
            animation: etherealBreathe 4s infinite alternate ease-in-out;
        }

        @keyframes etherealBreathe {
            0% {
                text-shadow: 0 0 10px rgba(255,255,255,0.3),
                0 0 20px rgba(147, 197, 253, 0.2);
            }
            100% {
                text-shadow: 0 0 20px rgba(255,255,255,0.8),
                0 0 40px rgba(147, 197, 253, 0.6);
            }
        }

        /* Estilos para inputs modernos */
        .form-control-lg {
            border-radius: 1.25rem;
            border: 2px solid #e5e7eb;
            padding: 1.1rem 1.4rem;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-control-lg:focus {
            border-color: var(--app-primary);
            box-shadow: 0 0 0 4px var(--app-primary-soft);
        }

        .btn-lg {
            border-radius: 1.25rem;
            padding: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }
    </style>
@endpush

@section('content')
    <div class="container py-5 mt-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-10 col-lg-11">
                <div class="row g-0 login-card shadow-lg">

                    <div class="col-lg-6 login-sidebar d-none d-lg-flex">
                        <div class="w-100 text-center">

                            <div id="prometeo-logo" class="prometeo-logo-container">
                                <span class="neon-tube">P</span>
                                <span class="neon-tube">R</span>
                                <span class="neon-tube">O</span>
                                <span class="neon-tube">M</span>
                                <span class="neon-tube">E</span>
                                <span class="neon-tube">T</span>
                                <span class="neon-tube">E</span>
                                <span class="neon-tube">O</span>
                            </div>

                            <p class="fs-5 anime-left-text opacity-75 mb-5 fw-medium text-center px-4">
                                Programa de Monitoreo Emocional y Tamizaje Estudiantil Oportuno.
                            </p>

                            <div class="d-inline-block text-start anime-left-text opacity-75 mt-4">
                                <div class="d-flex flex-column gap-3 mt-4 px-2">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-10 p-2 rounded-3"><i class="bi bi-shield-check fs-5"></i></div>
                                        <span>Entorno confidencial y seguro</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-10 p-2 rounded-3"><i class="bi bi-robot fs-5"></i></div>
                                        <span>Detección oportuna con IA</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-10 p-2 rounded-3"><i class="bi bi-heart-pulse fs-5"></i></div>
                                        <span>Enfoque en tu bienestar emocional</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 p-5 bg-white d-flex flex-column justify-content-center">
                        <div class="text-center mb-5 anime-form-item">
                            <h2 class="fw-black text-dark fs-1">Iniciar sesión</h2>
                            <p class="text-muted">Accede a tu cuenta de forma segura</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4 anime-form-item">
                                <label class="form-label fw-bold text-secondary">Correo electrónico</label>
                                <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-lg bg-light" required autofocus placeholder="tu@correo.com">
                                @error('email')
                                <small class="text-danger mt-2 d-block fw-bold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4 anime-form-item">
                                <label class="form-label fw-bold text-secondary">Contraseña</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control form-control-lg bg-light pe-5" required placeholder="Tu contraseña">
                                    <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y text-muted border-0 bg-transparent me-2" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                <small class="text-danger mt-2 d-block fw-bold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-2 anime-form-item">
                                <div class="form-check">
                                    <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label text-secondary fw-medium" for="remember_me">Recordarme</label>
                                </div>

                                @if(Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-primary text-decoration-none fw-bold">¿Olvidaste tu contraseña?</a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 anime-form-item shadow">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar a PROMETEO
                            </button>
                        </form>

                        <div class="text-center mt-5 anime-form-item">
                            <p class="text-muted">¿Aún no tienes cuenta? <a href="{{ route('register') }}" class="fw-black text-primary text-decoration-none ms-1">Regístrate aquí</a></p>
                            <small class="text-muted px-4 d-block mt-3">Confidencialidad garantizada según el <a href="{{ route('aviso.privacidad') }}" class="text-secondary text-decoration-underline fw-medium">aviso de privacidad</a>.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const tl = anime.timeline({
                easing: 'easeOutExpo',
            });

            // 1. REVELACIÓN CINEMÁTICA LETRA POR LETRA
            tl.add({
                targets: '.neon-tube',
                translateY: [40, 0], // Sube desde abajo
                scale: [0.8, 1], // Crece ligeramente
                opacity: [0, 1], // Aparece
                filter: ['blur(12px)', 'blur(0px)'], // Se enfoca
                color: ['#3b82f6', '#ffffff'], // Pasa de azul profundo a blanco
                textShadow: [
                    '0 0 0px rgba(59, 130, 246, 0)',
                    '0 0 40px rgba(96, 165, 250, 1)', // Destello azul intenso en el medio
                    '0 0 15px rgba(147, 197, 253, 0.6)' // Brillo suave al final
                ],
                duration: 1200,
                delay: anime.stagger(120), // Efecto cascada entre letras
                easing: 'easeOutQuint',
                complete: function() {
                    // Activa la respiración infinita de la luz
                    document.getElementById('prometeo-logo').classList.add('neon-steady');
                }
            })

                // 2. Animación en cascada del resto de elementos de la interfaz
                .add({
                    targets: '.anime-left-text',
                    translateY: [20, 0],
                    opacity: [0, 1],
                    duration: 800,
                    delay: anime.stagger(100),
                }, '-=600')
                .add({
                    targets: '.anime-form-item',
                    translateY: [30, 0],
                    opacity: [0, 1],
                    duration: 800,
                    delay: anime.stagger(100),
                }, '-=600');
        });
    </script>
@endpush
