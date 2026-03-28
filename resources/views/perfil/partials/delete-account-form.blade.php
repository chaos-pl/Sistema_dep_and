<div class="app-card p-4 p-md-5 rounded-4 border border-danger border-opacity-25 bg-danger bg-opacity-10 shadow-sm mt-4">
    <div class="d-flex align-items-center gap-3 mb-4 border-bottom border-danger border-opacity-25 pb-3">
        <div class="bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 48px; height: 48px;">
            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
        </div>
        <div>
            <h5 class="fw-black mb-1 text-danger">Zona de Riesgo</h5>
            <p class="text-danger text-opacity-75 mb-0 small">Esta acción elimina permanentemente tu cuenta y expediente del sistema.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('perfil.destroy') }}" onsubmit="return confirm('ATENCIÓN: ¿Seguro que deseas eliminar tu cuenta? Esta acción destruirá tu información y no se puede deshacer.');">
        @csrf
        @method('DELETE')

        <div class="mb-4">
            <label for="delete_password" class="form-label fw-bold text-danger">Confirma tu contraseña para continuar</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-danger border-opacity-50 text-danger"><i class="bi bi-shield-slash"></i></span>
                <input id="delete_password" name="password" type="password" class="form-control form-control-lg border-danger border-opacity-50 border-start-0 border-end-0 ps-0 @if($errors->deleteAccount->has('password')) is-invalid @endif" required placeholder="Ingresa tu contraseña actual">
                <button class="input-group-text bg-white border-danger border-opacity-50 text-danger" type="button" onclick="togglePasswordVisibility('delete_password', 'icon_delete_pwd')">
                    <i class="bi bi-eye-fill" id="icon_delete_pwd"></i>
                </button>
            </div>
            @if($errors->deleteAccount->has('password'))
                <small class="text-danger fw-bold d-block mt-1">{{ $errors->deleteAccount->first('password') }}</small>
            @endif
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm fw-bold">
                <i class="bi bi-trash3-fill me-2"></i>Eliminar Cuenta Permanentemente
            </button>
        </div>
    </form>
</div>
