<div class="row g-4">
    <div class="col-md-12 mb-2">
        <label class="form-label fw-bold text-body-secondary">Usuario vinculado al sistema</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary px-3"><i class="bi bi-envelope-at-fill"></i></span>
            <select name="user_id" class="form-select form-select-lg bg-body-tertiary border-start-0 ps-0">
                <option value="">Sin vincular a cuenta de sistema</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ old('user_id', $persona->user_id ?? '') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }} — {{ $user->email }}
                    </option>
                @endforeach
            </select>
        </div>
        @error('user_id')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Nombre(s)</label>
        <input type="text" name="nombre" class="form-control form-control-lg bg-body-tertiary" value="{{ old('nombre', $persona->nombre ?? '') }}" placeholder="Ej. Juan Carlos" required>
        @error('nombre')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Apellido paterno</label>
        <input type="text" name="apellido_paterno" class="form-control form-control-lg bg-body-tertiary" value="{{ old('apellido_paterno', $persona->apellido_paterno ?? '') }}" placeholder="Ej. Pérez" required>
        @error('apellido_paterno')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Apellido materno <span class="text-body-secondary fw-normal">(Opcional)</span></label>
        <input type="text" name="apellido_materno" class="form-control form-control-lg bg-body-tertiary" value="{{ old('apellido_materno', $persona->apellido_materno ?? '') }}" placeholder="Ej. López">
        @error('apellido_materno')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Fecha de nacimiento</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-calendar-event"></i></span>
            <input type="date" name="fecha_nacimiento" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento ?? '') }}" required>
        </div>
        @error('fecha_nacimiento')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Género</label>
        <select name="genero" class="form-select form-select-lg bg-body-tertiary" required>
            <option value="">Selecciona</option>
            <option value="masculino" {{ old('genero', $persona->genero ?? '') == 'masculino' ? 'selected' : '' }}>Masculino</option>
            <option value="femenino" {{ old('genero', $persona->genero ?? '') == 'femenino' ? 'selected' : '' }}>Femenino</option>
            <option value="otro" {{ old('genero', $persona->genero ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
            <option value="prefiero_no_decirlo" {{ old('genero', $persona->genero ?? '') == 'prefiero_no_decirlo' ? 'selected' : '' }}>Prefiero no decirlo</option>
        </select>
        @error('genero')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Teléfono <span class="text-body-secondary fw-normal">(Opcional)</span></label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-telephone"></i></span>
            <input type="text" name="telefono" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" value="{{ old('telefono', $persona->telefono ?? '') }}" placeholder="10 dígitos">
        </div>
        @error('telefono')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>
</div>

<div class="d-flex justify-content-between mt-5 pt-4 border-top">
    <a href="{{ route('admin.personas.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Cancelar
    </a>

    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ isset($persona) ? 'Actualizar Persona' : 'Guardar Persona' }}
    </button>
</div>
