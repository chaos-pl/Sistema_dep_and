@extends('layouts.guest')

@section('title', 'Iniciar sesión - PROMETEO')

@push('styles')
    <style>
        /* VARIABLES DE COLOR (Tema Morado) */
        :root {
            --app-primary: #7c3aed; /* Morado principal */
            --app-primary-rgb: 124, 58, 237;
            --app-secondary: #db2777; /* Magenta */
            --app-surface: #ffffff;
        }

        /* Ocultamos elementos base para la animación */
        .anime-form-item, .anime-left-text {
            opacity: 0;
            transform: translateY(20px);
        }

        /* Contenedor principal (Glassmorphism sutil) */
        .login-card {
            background: rgba(255, 255, 255, 0.92);
            border-radius: 2.5rem;
            box-shadow: 0 40px 100px rgba(124, 58, 237, 0.25);
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.4);
            backdrop-filter: blur(12px);
            z-index: 10;
            position: relative;
        }

        /* =========================================
           1. FONDO ANIMADO DE GRADIENTE LÍQUIDO
           ========================================= */
        body {
            background: linear-gradient(135deg, #1e1b4b 0%, #4c1d95 50%, #1e1b4b 100%);
            background-size: 400% 400%;
            animation: liquidBackground 15s ease infinite;
            overflow-x: hidden;
        }

        @keyframes liquidBackground {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        /* Capa de plasma difuso (Morado y Magenta) */
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

        .orb-1 {
            width: 550px; height: 550px;
            background: var(--app-primary);
            top: -100px; left: -100px;
            animation: orbFloat1 22s infinite alternate;
        }

        .orb-2 {
            width: 450px; height: 450px;
            background: var(--app-secondary);
            bottom: -100px; right: -100px;
            animation: orbFloat2 28s infinite alternate-reverse;
        }

        @keyframes orbFloat1 { 0% { transform: translate(0, 0); } 100% { transform: translate(150px, 150px); } }
        @keyframes orbFloat2 { 0% { transform: translate(0, 0); } 100% { transform: translate(-150px, -150px); } }

        /* =========================================
           PANEL IZQUIERDO Y LOGO
           ========================================= */
        .login-sidebar {
            background: linear-gradient(135deg, rgba(30, 27, 75, 0.8) 0%, rgba(124, 58, 237, 0.8) 100%);
            color: #ffffff;
            padding: 3rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
        }

        .logo-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
            animation: logoFloat 6s infinite ease-in-out;
            opacity: 0;
            display: flex;
            justify-content: center;
        }

        @keyframes logoFloat {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
            100% { transform: translateY(0px); }
        }

        .logo-container {
            width: 120px;
            height: 120px;
            border-radius: 2rem;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.3);
            border: 3px solid rgba(255,255,255,0.1);
            position: relative;
            z-index: 2;
            background: white;
        }

        .logo-container img { width: 100%; height: 100%; object-fit: cover; }

        .logo-particle {
            position: absolute; width: 6px; height: 6px; border-radius: 50%; pointer-events: none; opacity: 0; z-index: 1;
        }

        /* =========================================
           TEXTO PROMETEO (Neón Morado/Magenta)
           ========================================= */
        .prometeo-logo-container {
            display: flex;
            gap: 0.15rem;
            margin-bottom: 0.5rem;
            justify-content: center;
            width: 100%;
        }

        .neon-tube {
            font-family: 'Montserrat', sans-serif;
            font-size: 3.5rem;
            font-weight: 900;
            color: #ffffff;
            opacity: 0;
            filter: blur(12px);
            transform: translateY(40px) scale(0.8);
            will-change: transform, opacity, filter, text-shadow;
            display: inline-block;
        }

        .neon-steady .neon-tube { animation: etherealBreathe 4s infinite alternate ease-in-out; }

        @keyframes etherealBreathe {
            0% { text-shadow: 0 0 10px rgba(255,255,255,0.3), 0 0 20px rgba(124, 58, 237, 0.4); }
            100% { text-shadow: 0 0 15px rgba(255,255,255,0.8), 0 0 35px rgba(219, 39, 119, 0.6); }
        }

        /* =========================================
           SIGLAS (Kinetic Typography)
           ========================================= */
        .siglas-container {
            font-family: 'Inter', sans-serif;
            font-size: 1rem;
            font-weight: 500;
            color: rgba(255,255,255,0.9);
            margin-bottom: 3rem;
            line-height: 1.6;
            width: 100%;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            text-align: center;
            perspective: 1000px;
        }

        .sigla-word {
            display: inline-flex;
            margin-right: 4px;
        }

        .sigla-letter {
            opacity: 0;
            display: inline-block;
            transform-origin: bottom;
            will-change: transform, opacity, filter;
        }

        .highlight-sigla {
            color: #ffffff;
            font-weight: 800;
            text-shadow: 0 0 10px rgba(124, 58, 237, 0.8);
            animation: siglaPulse 2s infinite alternate ease-in-out;
        }

        @keyframes siglaPulse {
            0% { text-shadow: 0 0 5px rgba(124, 58, 237, 0.4); }
            100% { text-shadow: 0 0 15px rgba(219, 39, 119, 1); } /* Magenta brillante */
        }

        /* Lista de características centrada */
        .features-list {
            display: flex;
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
            margin: 0 auto;
            width: fit-content;
        }

        /* =========================================
           FORMULARIO
           ========================================= */
        .form-control-lg {
            border-radius: 1.25rem;
            border: 2px solid #e5e7eb;
            padding: 1.1rem 1.4rem;
            font-size: 1rem;
            transition: all 0.2s;
        }

        .form-control-lg:focus {
            border-color: var(--app-primary);
            box-shadow: 0 0 0 4px rgba(124, 58, 237, 0.15);
        }

        .btn-lg {
            border-radius: 1.25rem;
            padding: 1.1rem;
            font-weight: 700;
            letter-spacing: 0.5px;
            background: linear-gradient(135deg, var(--app-primary) 0%, #5b21b6 100%);
            border: none;
            color: white;
            transition: all 0.3s ease;
        }

        .btn-lg:hover {
            box-shadow: 0 10px 25px rgba(124, 58, 237, 0.4);
            transform: translateY(-2px);
        }
    </style>
@endpush

@section('content')
    <div class="plasma-bg">
        <div class="plasma-orb orb-1"></div>
        <div class="plasma-orb orb-2"></div>
    </div>

    <div class="container py-5 mt-5 position-relative z-2">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-10 col-lg-11">
                <div class="row g-0 login-card shadow-lg">

                    <div class="col-lg-6 login-sidebar d-none d-lg-flex">
                        <div class="w-100 position-relative z-1 d-flex flex-column align-items-center">

                            <div class="logo-wrapper" id="logo-wrapper">
                                <div class="logo-container">
                                    <img src="{{ asset('img/logo_prometeo.png') }}" alt="Logo Prometeo">
                                </div>
                            </div>

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

                            <div class="siglas-container" id="siglas-text"></div>

                            <div class="anime-left-text opacity-75 mt-2 fw-medium w-100">
                                <div class="features-list">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-10 p-2 rounded-3 text-info"><i class="bi bi-shield-check fs-5"></i></div>
                                        <span>Entorno confidencial y seguro</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-10 p-2 rounded-3 text-warning"><i class="bi bi-robot fs-5"></i></div>
                                        <span>Detección oportuna con IA</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-white bg-opacity-10 p-2 rounded-3 text-danger"><i class="bi bi-heart-pulse fs-5"></i></div>
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
                                @error('email')<small class="text-danger mt-2 d-block fw-bold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>
                            <div class="mb-4 anime-form-item">
                                <label class="form-label fw-bold text-secondary">Contraseña</label>
                                <div class="position-relative">
                                    <input type="password" name="password" id="password" class="form-control form-control-lg bg-light pe-5" required placeholder="Tu contraseña">
                                    <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y text-muted border-0 bg-transparent me-2" onclick="togglePassword()"><i class="bi bi-eye" id="togglePasswordIcon"></i></button>
                                </div>
                                @error('password')<small class="text-danger mt-2 d-block fw-bold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-2 anime-form-item">
                                <div class="form-check">
                                    <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label text-secondary fw-medium" for="remember_me">Recordarme</label>
                                </div>
                                @if(Route::has('password.request'))<a href="{{ route('password.request') }}" style="color: var(--app-primary);" class="text-decoration-none fw-bold">¿Olvidaste tu contraseña?</a>@endif
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg w-100 anime-form-item text-white"><i class="bi bi-box-arrow-in-right me-2"></i>Entrar a PROMETEO</button>
                        </form>
                        <div class="text-center mt-5 anime-form-item">
                            <p class="text-muted">¿Aún no tienes cuenta? <a href="{{ route('register') }}" style="color: var(--app-primary);" class="fw-black text-decoration-none ms-1">Regístrate aquí</a></p>
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
                icon.style.color = 'var(--app-primary)';
            } else {
                password.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
                icon.style.color = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {

            const textContainer = document.getElementById('siglas-text');
            const textoOriginal = "Programa de Monitoreo Emocional y Tamizaje Estudiantil Oportuno.";

            const words = textoOriginal.split(' ');
            textContainer.innerHTML = words.map(word => {
                if (/[A-Z]/.test(word[0])) {
                    return `<span class="sigla-word"><span class="sigla-letter highlight-sigla">${word[0]}</span>${word.slice(1).split('').map(char => `<span class="sigla-letter">${char}</span>`).join('')}</span>`;
                }
                return `<span class="sigla-word">${word.split('').map(char => `<span class="sigla-letter">${char}</span>`).join('')}</span>`;
            }).join(' ');

            const tl = anime.timeline({ easing: 'easeOutExpo' });

            tl.add({
                targets: '.logo-wrapper',
                scale: [0.3, 1],
                opacity: [0, 1],
                rotate: [20, 0],
                filter: ['brightness(10)', 'brightness(1)'],
                duration: 1400,
                easing: 'easeOutElastic(1, .6)',
            })
                .add({
                    targets: '.neon-tube',
                    translateY: [40, 0],
                    scale: [0.8, 1],
                    opacity: [0, 1],
                    filter: ['blur(12px)', 'blur(0px)'],
                    color: ['#c084fc', '#ffffff'], // Morado claro a blanco
                    textShadow: [
                        '0 0 0px rgba(124, 58, 237, 0)',
                        '0 0 50px rgba(219, 39, 119, 1)', // Destello Magenta
                        '0 0 15px rgba(124, 58, 237, 0.6)' // Brillo Morado final
                    ],
                    duration: 1200,
                    delay: anime.stagger(100),
                    easing: 'easeOutQuint',
                    complete: function() {
                        document.getElementById('prometeo-logo').classList.add('neon-steady');
                    }
                }, '-=600')
                .add({
                    targets: '.sigla-letter',
                    opacity: [0, 1],
                    translateY: [20, 0],
                    translateZ: [50, 0],
                    rotateX: [90, 0],
                    filter: ['blur(10px)', 'blur(0px)'],
                    duration: 800,
                    easing: 'easeOutElastic(1, .7)',
                    delay: anime.stagger(25),
                }, '-=800')
                .add({
                    targets: '.anime-left-text',
                    translateY: [20, 0],
                    opacity: [0, 1],
                    duration: 800,
                    delay: anime.stagger(100),
                }, '-=800')
                .add({
                    targets: '.anime-form-item',
                    translateY: [30, 0],
                    opacity: [0, 1],
                    duration: 800,
                    delay: anime.stagger(100),
                }, '-=800');

            // Partículas actualizadas a Morado y Magenta
            const logoWrapper = document.getElementById('logo-wrapper');
            const colors = ['#7c3aed', '#db2777', '#a855f7'];

            function createParticle() {
                const particle = document.createElement('div');
                particle.classList.add('logo-particle');
                particle.style.background = colors[Math.floor(Math.random() * colors.length)];
                particle.style.left = '50%';
                particle.style.top = '50%';
                logoWrapper.appendChild(particle);

                const angle = Math.random() * Math.PI * 2;
                const radius = 50 + Math.random() * 80;

                anime({
                    targets: particle,
                    translateX: [0, Math.cos(angle) * radius],
                    translateY: [0, Math.sin(angle) * radius],
                    opacity: [0, 0.8, 0],
                    scale: [0, 1, 0.5],
                    easing: 'easeOutCubic',
                    duration: 2000 + Math.random() * 1000,
                    complete: function() {
                        particle.remove();
                    }
                });
            }

            tl.finished.then(() => {
                for(let i=0; i<30; i++) { setTimeout(createParticle, i * 20); }
                setInterval(createParticle, 500);
            });
        });
    </script>
@endpush
