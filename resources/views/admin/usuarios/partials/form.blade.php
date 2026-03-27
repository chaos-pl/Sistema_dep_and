<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Nombre de usuario</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name ?? '') }}">
        @error('name')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Correo</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email ?? '') }}">
        @error('email')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">
            Contraseña {{ $user ? '(dejar en blanco para no cambiar)' : '' }}
        </label>
        <input type="password" name="password" class="form-control">
        @error('password')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Confirmar contraseña</label>
        <input type="password" name="password_confirmation" class="form-control">
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Roles</label>
        <select name="roles[]" class="form-select" multiple size="4">
            @foreach($roles as $role)
                <option value="{{ $role->name }}"
                    {{ in_array($role->name, old('roles', $user?->roles->pluck('name')->toArray() ?? [])) ? 'selected' : '' }}>
                    {{ ucfirst($role->name) }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Mantén presionado Ctrl para seleccionar varios.</small>
        @error('roles')
        <small class="text-danger d-block">{{ $message }}</small>
        @enderror
        @error('roles.*')
        <small class="text-danger d-block">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Persona vinculada</label>
        <select name="persona_id" class="form-select">
            <option value="">Sin vincular</option>
            @foreach($personas as $persona)
                <option value="{{ $persona->id }}"
                    {{ old('persona_id', $user?->persona?->id ?? '') == $persona->id ? 'selected' : '' }}>
                    {{ $persona->nombre }} {{ $persona->apellido_paterno }} — {{ $persona->user_id ? 'Ya vinculada' : 'Disponible' }}
                </option>
            @endforeach
        </select>
        @error('persona_id')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="d-flex justify-content-between mt-4">
    <a href="{{ route('admin.usuarios.index') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>

    <button class="btn btn-primary">
        <i class="bi bi-save me-2"></i>Guardar
    </button>
</div>
