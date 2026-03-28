<div class="row g-4">

    <div class="col-12">
        <h6 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-3">
            <i class="bi bi-person-badge me-2"></i>Credenciales de Acceso
        </h6>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary">Nombre de usuario</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-person"></i></span>
            <input type="text" name="name" class="form-control form-control-lg bg-light border-start-0 ps-0" value="{{ old('name', $user->name ?? '') }}" placeholder="Ej. jperez_22" required>
        </div>
        @error('name')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary">Correo electrónico</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-envelope-at"></i></span>
            <input type="email" name="email" class="form-control form-control-lg bg-light border-start-0 ps-0" value="{{ old('email', $user->email ?? '') }}" placeholder="usuario@correo.com" required>
        </div>
        @error('email')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary">
            Contraseña <span class="text-muted fw-normal">{{ $user ? '(Opcional: solo si deseas cambiarla)' : '' }}</span>
        </label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock"></i></span>
            <input type="password" name="password" class="form-control form-control-lg bg-light border-start-0 ps-0" placeholder="••••••••">
        </div>
        @error('password')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary">Confirmar contraseña</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-lock-fill"></i></span>
            <input type="password" name="password_confirmation" class="form-control form-control-lg bg-light border-start-0 ps-0" placeholder="Repite la contraseña">
        </div>
    </div>

    <div class="col-12 mt-5">
        <h6 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-3">
            <i class="bi bi-shield-lock me-2"></i>Asignación y Vinculación
        </h6>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary mb-3">Roles asignados</label>
        <div class="app-card p-3 bg-light border-0 shadow-sm" style="border-radius: 1rem;">
            <div class="row g-2">
                @foreach($roles as $role)
                    <div class="col-sm-6">
                        <div class="form-check custom-check">
                            <input class="form-check-input shadow-none" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}"
                                {{ in_array($role->name, old('roles', $user?->roles->pluck('name')->toArray() ?? [])) ? 'checked' : '' }}>
                            <label class="form-check-label text-dark fw-medium" for="role_{{ $role->id }}" style="cursor: pointer;">
                                {{ ucfirst($role->name) }}
                            </label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @error('roles')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-secondary mb-3">Expediente personal (Persona vinculada)</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-person-vcard"></i></span>
            <select name="persona_id" class="form-select form-select-lg bg-light border-start-0 ps-0">
                <option value="">No vincular todavía</option>
                @foreach($personas as $persona)
                    <option value="{{ $persona->id }}" {{ old('persona_id', $user?->persona?->id ?? '') == $persona->id ? 'selected' : '' }}>
                        {{ $persona->nombre }} {{ $persona->apellido_paterno }} {{ $persona->user_id ? '(Ya vinculada)' : '' }}
                    </option>
                @endforeach
            </select>
        </div>
        <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i>Si vinculas una persona, este usuario heredará su expediente médico/educativo.</small>
        @error('persona_id')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>
</div>

<div class="d-flex justify-content-between mt-5 pt-4 border-top">
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-secondary shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Cancelar
    </a>

    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ $user ? 'Actualizar Cuenta' : 'Guardar Usuario' }}
    </button>
</div>
