@extends('layouts.guest')

@section('content')
    <div class="container py-5 fade-up">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-xl-10">
                <div class="row g-0 auth-card overflow-hidden">
                    <div class="col-lg-6 d-none d-lg-flex align-items-center justify-content-center p-5 auth-side">
                        <div class="text-white position-relative z-1">
                            <div class="mb-4">
                            <span class="bg-white text-primary brand-icon d-inline-flex align-items-center justify-content-center shadow-sm">
                                <i class="bi bi-heart-pulse-fill fs-2"></i>
                            </span>
                            </div>

                            <span class="auth-tag mb-3">
                            <i class="bi bi-shield-check"></i> Entorno seguro
                        </span>

                            <h2 class="fw-bold mb-3">Bienvenido a PROMETEO</h2>
                            <p class="mb-3 fs-5">
                                Programa de Monitoreo Emocional y Tamizaje Estudiantil Oportuno.
                            </p>
                            <p class="mb-4">
                                Plataforma de detección oportuna de depresión y ansiedad con una experiencia más humana, clara y confidencial.
                            </p>

                            <div class="d-flex flex-column gap-3">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-stars fs-4"></i>
                                    <span>Interfaz ligera, moderna y enfocada en bienestar</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-shield-lock fs-4"></i>
                                    <span>Protección y tratamiento responsable de la información</span>
                                </div>
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-clipboard2-pulse fs-4"></i>
                                    <span>Seguimiento oportuno con apoyo profesional</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6 p-4 p-md-5 bg-white">
                        <div class="text-center mb-4">
                            <h3 class="fw-bold mb-2">Iniciar sesión</h3>
                            <p class="text-muted mb-0">Accede de forma segura a tu cuenta</p>
                        </div>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Correo electrónico</label>
                                <div class="input-group-modern">
                                    <i class="bi bi-envelope"></i>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus placeholder="ejemplo@correo.com">
                                </div>
                                @error('email')
                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">Contraseña</label>
                                <div class="position-relative input-group-modern">
                                    <i class="bi bi-lock"></i>
                                    <input type="password" name="password" id="password" class="form-control pe-5" required placeholder="Ingresa tu contraseña">
                                    <button type="button" class="btn btn-sm position-absolute end-0 top-50 translate-middle-y me-2 border-0 bg-transparent" onclick="togglePassword()">
                                        <i class="bi bi-eye" id="togglePasswordIcon"></i>
                                    </button>
                                </div>
                                @error('password')
                                <small class="text-danger mt-2 d-block">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember_me">
                                    <label class="form-check-label" for="remember_me">Recordarme</label>
                                </div>

                                @if(Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="auth-link">
                                        ¿Olvidaste tu contraseña?
                                    </a>
                                @endif
                            </div>

                            <button type="submit" class="btn btn-primary w-100 py-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Entrar
                            </button>
                        </form>

                        <div class="text-center mt-4">
                            <p class="mb-2 text-muted">
                                ¿Aún no tienes cuenta?
                                <a href="{{ route('register') }}" class="auth-link">Regístrate aquí</a>
                            </p>

                            <small class="text-muted">
                                Al acceder aceptas el tratamiento confidencial de tus datos conforme al
                                <a href="{{ route('aviso.privacidad') }}" class="auth-link">aviso de privacidad</a>.
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function togglePassword() {
            const password = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');

            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }
    </script>
@endpush
