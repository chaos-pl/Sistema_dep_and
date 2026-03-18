<x-guest-layout>
    <div class="text-center mb-4">
        <div class="metric-icon mx-auto mb-3"><i class="bi bi-shield-lock"></i></div>
        <h2 class="fw-bold mb-2">Ingreso seguro al sistema</h2>
        <p class="text-muted-soft mb-0">Accede a tu panel de bienestar con una interfaz tranquila, protegida y adaptada a tu rol institucional.</p>
    </div>

    <x-auth-session-status class="mb-3" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Correo institucional</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-envelope"></i></span>
                <input id="email" class="form-control" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nombre@universidad.edu">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2 text-danger small" />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label fw-semibold">Contraseña</label>
            <div class="input-group">
                <span class="input-group-text bg-white"><i class="bi bi-key"></i></span>
                <input id="password" class="form-control" type="password" name="password" required autocomplete="current-password" placeholder="Ingresa tu contraseña">
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2 text-danger small" />
        </div>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
            <div class="form-check m-0">
                <input id="remember_me" type="checkbox" class="form-check-input" name="remember">
                <label for="remember_me" class="form-check-label text-muted-soft">Mantener sesión activa</label>
            </div>

            @if (Route::has('password.request'))
                <a class="text-decoration-none fw-semibold" href="{{ route('password.request') }}" style="color: #7B53B5;">
                    ¿Olvidaste tu contraseña?
                </a>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="bi bi-box-arrow-in-right me-2"></i>Iniciar sesión
        </button>

        <div class="app-card-soft p-3">
            <div class="d-flex gap-3">
                <div class="metric-icon flex-shrink-0" style="width:44px;height:44px;"><i class="bi bi-info-circle"></i></div>
                <div>
                    <div class="fw-semibold mb-1">Tu privacidad está protegida</div>
                    <small class="text-muted-soft">Después del primer ingreso, deberás aceptar el consentimiento informado sobre el uso del código anónimo y el resguardo de tus respuestas clínicas.</small>
                </div>
            </div>
        </div>
    </form>
</x-guest-layout>
