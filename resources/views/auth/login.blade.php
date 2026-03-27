@extends('layouts.guest')

@push('styles')
    <style>
        /* Ocultamos los elementos inicialmente para que Anime.js los anime suavemente */
        .anime-left-item, .anime-right-item { opacity: 0; }
    </style>
@endpush

@section('content')
    <div class="container py-5">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-10">
                <div class="row g-0 auth-card overflow-hidden shadow-lg" style="border-radius: 24px;">
                    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-5 auth-side bg-primary bg-gradient text-white">
                        <div class="position-relative z-1">
                            <div class="mb-4 anime-left-item">
                                <span class="bg-white text-primary brand-icon d-inline-flex align-items-center justify-content-center shadow-sm rounded-4" style="width: 64px; height: 64px;">
                                    <i class="bi bi-heart-pulse-fill fs-2"></i>
                                </span>
                            </div>

                            <span class="badge bg-white bg-opacity-25 text-white mb-3 anime-left-item px-3 py-2 rounded-pill">
                                <i class="bi bi-shield-check me-1"></i> Entorno seguro
                            </span>

                            <h2 class="fw-bold mb-3 anime-left-item">Bienvenido a PROMETEO</h2>
                            <p class="mb-3 fs-5 anime-left-item opacity-75">
                                Programa de Monitoreo Emocional y Tamizaje Estudiantil Oportuno.
                            </p>
                            <p class="mb-4 anime-left-item">
                                Plataforma de detección oportuna de depresión y ansiedad con una experiencia más humana, clara y confidencial.
                            </p>

                            <div class="d-flex flex-column gap-3 mt-4">
                                <div class="d-flex align-items-center gap-3 anime-left-item">
                                    <div class="bg-white bg-opacity-25 p-2 rounded-3"><i class="bi bi-stars fs-5"></i></div>
                                    <span>Interfaz ligera, moderna y enfocada en bienestar</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 anime-left-item">
                                    <div class="bg-white bg-opacity-25 p-2 rounded-3"><i class="bi bi-shield-lock fs-5"></i></div>
                                    <span>Protección y tratamiento responsable de la información</span>
                                </div>
                                <div class="d-flex align-items-center gap-3 anime-left-item">
                                    <div class="bg-white bg-opacity-25 p-2 rounded-3"><i class="bi bi-clipboard2-pulse fs-5"></i></div>
                                    <span>Seguimiento oportuno con apoyo profesional</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 p-4 p-md-5 bg-white d-flex flex-column justify-content-center">
                        <div class="text-center mb-5 anime-right-item">
                            <h3 class="fw-bold mb-2 text-dark">Iniciar sesión</h3>
                            <p class="text-muted mb-0">Accede de forma segura a tu cuenta</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-4 anime-right-item">
                                <label class="form-label fw-semibold text-secondary">Correo electrónico</label>
                                <div class="input-group input-group-lg">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control border-start-0 bg-light" required autofocus placeholder="ejemplo@correo.com">
                                </div>
                                @error('email')
                                <small class="text-danger mt-2 d-block fw-medium"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-4 anime-right-item">
                                <label class="form-label fw-semibold text-secondary">Contraseña</label>
                                <div class="input-group input-group-lg position-relative">
                                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
                                    <input type="password" name="password" id="password" class="form-control border-start-0 bg-light pe-5" required placeholder="Ingresa tu contraseña">
                                    <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y border-0 text-muted z-3" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                <small class="text-danger mt-2 d-block fw-medium"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-2 anime-right-item">
                                <div class="form-check">
                                    <input class="form-check-input shadow-none" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label text-secondary" for="remember_me">Recordarme</label>
                                </div>

                                @if(Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-primary text-decoration-none fw-medium auth-link">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100 py-3 fw-bold shadow-sm anime-right-item rounded-4">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar a PROMETEO
                            </button>
                        </form>

                        <div class="text-center mt-5 anime-right-item">
                            <p class="mb-3 text-muted">
                                ¿Aún no tienes cuenta?
                                <a href="{{ route('register') }}" class="text-primary fw-bold text-decoration-none ms-1">Regístrate aquí</a>
                            </p>

                            <div class="small text-muted px-4">
                                Al acceder aceptas el tratamiento confidencial de tus datos conforme al
                                <a href="{{ route('aviso.privacidad') }}" class="text-decoration-underline text-secondary">aviso de privacidad</a>.
                            </div>
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
            // Animación del panel izquierdo
            anime({
                targets: '.anime-left-item',
                translateX: [-30, 0],
                opacity: [0, 1],
                delay: anime.stagger(150), // Cada elemento entra con 150ms de diferencia
                easing: 'easeOutExpo',
                duration: 1200
            });

            // Animación del formulario derecho
            anime({
                targets: '.anime-right-item',
                translateY: [20, 0],
                opacity: [0, 1],
                delay: anime.stagger(100, {start: 300}), // Empieza 300ms después
                easing: 'easeOutExpo',
                duration: 1000
            });
        });
    </script>
@endpush
