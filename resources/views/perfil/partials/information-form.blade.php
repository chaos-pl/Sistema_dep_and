<div class="app-card p-4 p-md-5 rounded-4 border-0 shadow-sm">
    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="bi bi-envelope-at fs-4"></i>
        </div>
        <div>
            <h5 class="fw-black mb-1 text-dark">Credenciales de Acceso</h5>
            <p class="text-muted mb-0 small">Actualiza tu nombre de usuario y correo de sesión.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('perfil.update') }}">
        @csrf
        @method('PATCH')

        <div class="row g-4">
            <div class="col-md-6">
                <label for="name" class="form-label fw-bold text-secondary">Nombre de Usuario</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
                    <input id="name" name="name" type="text" class="form-control form-control-lg bg-light border-start-0 ps-0 @if($errors->updateProfileInformation->has('name')) is-invalid @endif" value="{{ old('name', $user->name) }}" required>
                </div>
                @if($errors->updateProfileInformation->has('name'))
                    <small class="text-danger fw-bold d-block mt-1">{{ $errors->updateProfileInformation->first('name') }}</small>
                @endif
            </div>

            <div class="col-md-6">
                <label for="email" class="form-label fw-bold text-secondary">Correo electrónico</label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope"></i></span>
                    <input id="email" name="email" type="email" class="form-control form-control-lg bg-light border-start-0 ps-0 @if($errors->updateProfileInformation->has('email')) is-invalid @endif" value="{{ old('email', $user->email) }}" required>
                </div>
                @if($errors->updateProfileInformation->has('email'))
                    <small class="text-danger fw-bold d-block mt-1">{{ $errors->updateProfileInformation->first('email') }}</small>
                @endif
            </div>
        </div>

        <div class="mt-4 text-end border-top pt-3">
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-check-circle me-2"></i>Actualizar Credenciales
            </button>
        </div>
    </form>
</div>
