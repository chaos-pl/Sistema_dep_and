<div class="row g-4">
    <div class="col-12">
        <div class="p-3 bg-primary bg-opacity-10 border border-primary border-opacity-10 rounded-4">
            <label class="form-label fw-bold text-primary mb-2"><i class="bi bi-link-45deg me-1"></i>Vincular cuenta de sistema (Opcional)</label>
            <p class="small text-body-secondary mb-3">Si esta persona usará el sistema, selecciona su correo. Si es solo un registro (ej. un tutor externo sin acceso), déjalo en blanco.</p>
            <div class="input-group shadow-sm rounded-3 overflow-hidden">
                <span class="input-group-text bg-body border-0 text-primary px-3"><i class="bi bi-envelope-at-fill"></i></span>
                <select name="user_id" class="form-select form-select-lg bg-body border-0 ps-0" style="cursor: pointer;">
                    <option value="">Sin vincular a cuenta de sistema</option>
                    @foreach($users as $user)
                        @if(!$user->persona || $user->id == ($persona->user_id ?? null))
                            <option value="{{ $user->id }}" {{ old('user_id', $persona->user_id ?? '') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }} - {{ $user->email }}
                            </option>
                        @endif
                    @endforeach
                </select>
            </div>
            @error('user_id')<small class="text-danger fw-bold mt-2 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
        </div>
    </div>

    <div class="col-12"><hr class="border-secondary border-opacity-10 my-1"></div>

    <div class="col-md-12 mb-1">
        <h6 class="fw-bold text-body mb-0"><i class="bi bi-person-vcard me-2 text-secondary"></i>Datos Personales</h6>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary small text-uppercase">Nombre(s) <span class="text-danger">*</span></label>
        <input type="text" name="nombre" class="form-control form-control-lg bg-body-tertiary border-0 shadow-sm rounded-3" value="{{ old('nombre', $persona->nombre ?? '') }}" required placeholder="Ej. Juan Pablo">
        @error('nombre')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary small text-uppercase">Ap. Paterno <span class="text-danger">*</span></label>
        <input type="text" name="apellido_paterno" class="form-control form-control-lg bg-body-tertiary border-0 shadow-sm rounded-3" value="{{ old('apellido_paterno', $persona->apellido_paterno ?? '') }}" required placeholder="Ej. Pérez">
        @error('apellido_paterno')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary small text-uppercase">Ap. Materno</label>
        <input type="text" name="apellido_materno" class="form-control form-control-lg bg-body-tertiary border-0 shadow-sm rounded-3" value="{{ old('apellido_materno', $persona->apellido_materno ?? '') }}" placeholder="Ej. Gómez">
        @error('apellido_materno')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary small text-uppercase">Nacimiento <span class="text-danger">*</span></label>
        <input type="date" name="fecha_nacimiento" class="form-control form-control-lg bg-body-tertiary border-0 shadow-sm rounded-3" value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento ?? '') }}" required>
        @error('fecha_nacimiento')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary small text-uppercase">Género <span class="text-danger">*</span></label>
        <select name="genero" class="form-select form-select-lg bg-body-tertiary border-0 shadow-sm rounded-3" style="cursor: pointer;" required>
            <option value="">Seleccionar...</option>
            <option value="masculino" {{ old('genero', $persona->genero ?? '') == 'masculino' ? 'selected' : '' }}>Masculino</option>
            <option value="femenino" {{ old('genero', $persona->genero ?? '') == 'femenino' ? 'selected' : '' }}>Femenino</option>
            <option value="otro" {{ old('genero', $persona->genero ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
        </select>
        @error('genero')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
    </div>
    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary small text-uppercase">Teléfono</label>
        <input type="text" name="telefono" class="form-control form-control-lg bg-body-tertiary border-0 shadow-sm rounded-3" value="{{ old('telefono', $persona->telefono ?? '') }}" placeholder="10 dígitos">
        @error('telefono')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
    </div>
</div>
