<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Usuario vinculado</label>
        <select name="user_id" class="form-select">
            <option value="">Sin vincular</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}"
                    {{ old('user_id', $persona->user_id ?? '') == $user->id ? 'selected' : '' }}>
                    {{ $user->name }} — {{ $user->email }}
                </option>
            @endforeach
        </select>
        @error('user_id')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Nombre</label>
        <input type="text" name="nombre" class="form-control" value="{{ old('nombre', $persona->nombre ?? '') }}">
        @error('nombre')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Apellido paterno</label>
        <input type="text" name="apellido_paterno" class="form-control" value="{{ old('apellido_paterno', $persona->apellido_paterno ?? '') }}">
        @error('apellido_paterno')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-semibold">Apellido materno</label>
        <input type="text" name="apellido_materno" class="form-control" value="{{ old('apellido_materno', $persona->apellido_materno ?? '') }}">
        @error('apellido_materno')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Fecha de nacimiento</label>
        <input type="date" name="fecha_nacimiento" class="form-control" value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento ?? '') }}">
        @error('fecha_nacimiento')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Género</label>
        <select name="genero" class="form-select">
            <option value="">Selecciona</option>
            <option value="masculino" {{ old('genero', $persona->genero ?? '') == 'masculino' ? 'selected' : '' }}>Masculino</option>
            <option value="femenino" {{ old('genero', $persona->genero ?? '') == 'femenino' ? 'selected' : '' }}>Femenino</option>
            <option value="otro" {{ old('genero', $persona->genero ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
            <option value="prefiero_no_decirlo" {{ old('genero', $persona->genero ?? '') == 'prefiero_no_decirlo' ? 'selected' : '' }}>Prefiero no decirlo</option>
        </select>
        @error('genero')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-semibold">Teléfono</label>
        <input type="text" name="telefono" class="form-control" value="{{ old('telefono', $persona->telefono ?? '') }}">
        @error('telefono')
        <small class="text-danger">{{ $message }}</small>
        @enderror
    </div>
</div>

<div class="d-flex justify-content-between mt-4">
    <a href="{{ route('admin.personas.index') }}" class="btn btn-light border">
        <i class="bi bi-arrow-left me-2"></i>Volver
    </a>

    <button class="btn btn-primary">
        <i class="bi bi-save me-2"></i>Guardar
    </button>
</div>
