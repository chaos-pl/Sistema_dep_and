<div class="app-card p-4 p-md-5 rounded-4 border-0 shadow-sm mt-4">
    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="bi bi-shield-lock fs-4"></i>
        </div>
        <div>
            <h5 class="fw-black mb-1 text-dark">Seguridad</h5>
            <p class="text-muted mb-0 small">Actualiza tu contraseña para mantener tu cuenta segura.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('perfil.password.update') }}">
        @csrf
        @method('PUT')

        <div class="row g-4">
            <div class="col-md-12">
                <label for="current_password" class="form-label fw-bold text-secondary">Contraseña actual</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-unlock"></i></span>
                    <input id="current_password" name="current_password" type="password" class="form-control form-control-lg bg-light border-start-0 border-end-0 ps-0 @if($errors->updatePassword->has('current_password')) is-invalid @endif" required placeholder="••••••••">
                    <button class="input-group-text bg-light border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('current_password', 'icon_current_pwd')">
                        <i class="bi bi-eye-fill" id="icon_current_pwd"></i>
                    </button>
                </div>
                @if($errors->updatePassword->has('current_password'))
                    <small class="text-danger fw-bold d-block mt-1">{{ $errors->updatePassword->first('current_password') }}</small>
                @endif
            </div>

            <div class="col-md-6">
                <label for="password" class="form-label fw-bold text-secondary">Nueva contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input id="password" name="password" type="password" class="form-control form-control-lg bg-light border-start-0 border-end-0 ps-0 @if($errors->updatePassword->has('password')) is-invalid @endif" required placeholder="Min. 8 caracteres">
                    <button class="input-group-text bg-light border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('password', 'icon_new_pwd')">
                        <i class="bi bi-eye-fill" id="icon_new_pwd"></i>
                    </button>
                </div>
                @if($errors->updatePassword->has('password'))
                    <small class="text-danger fw-bold d-block mt-1">{{ $errors->updatePassword->first('password') }}</small>
                @endif
            </div>

            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-bold text-secondary">Confirmar nueva contraseña</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
                    <input id="password_confirmation" name="password_confirmation" type="password" class="form-control form-control-lg bg-light border-start-0 border-end-0 ps-0" required placeholder="Repite la contraseña">
                    <button class="input-group-text bg-light border-start-0 text-muted" type="button" onclick="togglePasswordVisibility('password_confirmation', 'icon_confirm_pwd')">
                        <i class="bi bi-eye-fill" id="icon_confirm_pwd"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="mt-4 text-end border-top pt-3">
            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-key-fill me-2"></i>Actualizar Contraseña
            </button>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        // Función global para mostrar/ocultar contraseñas
        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-fill');
                icon.classList.add('bi-eye-slash-fill');
                icon.classList.replace('text-muted', 'text-primary'); // Resalta en morado
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash-fill');
                icon.classList.add('bi-eye-fill');
                icon.classList.replace('text-primary', 'text-muted');
            }
        }
    </script>
@endpush
