<div class="app-card p-4 p-md-5 rounded-4 border-0 shadow-sm">
    <div class="d-flex align-items-center gap-3 mb-4 border-bottom pb-3">
        <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
            <i class="bi bi-person-vcard fs-4"></i>
        </div>
        <div>
            <h5 class="fw-black mb-1 text-dark">Expediente Personal</h5>
            <p class="text-muted mb-0 small">Completa tus datos reales (demográficos) para el sistema.</p>
        </div>
    </div>

    <form method="POST" action="{{ route('perfil.persona.update') }}">
        @csrf
        @method('PUT')

        @php $persona = $user->persona; @endphp

        <div class="row g-4">
            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Nombre(s)</label>
                <input type="text" name="nombre" class="form-control form-control-lg bg-light" value="{{ old('nombre', $persona->nombre ?? '') }}" placeholder="Ej. Ana" required>
                @error('nombre')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Apellido paterno</label>
                <input type="text" name="apellido_paterno" class="form-control form-control-lg bg-light" value="{{ old('apellido_paterno', $persona->apellido_paterno ?? '') }}" placeholder="Ej. López" required>
                @error('apellido_paterno')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Apellido materno <span class="text-muted fw-normal">(Opc.)</span></label>
                <input type="text" name="apellido_materno" class="form-control form-control-lg bg-light" value="{{ old('apellido_materno', $persona->apellido_materno ?? '') }}">
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Fecha de nacimiento</label>
                <input type="date" name="fecha_nacimiento" class="form-control form-control-lg bg-light" value="{{ old('fecha_nacimiento', $persona->fecha_nacimiento ?? '') }}" required>
                @error('fecha_nacimiento')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Género</label>
                <select name="genero" class="form-select form-select-lg bg-light" required>
                    <option value="">Selecciona</option>
                    <option value="masculino" {{ old('genero', $persona->genero ?? '') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                    <option value="femenino" {{ old('genero', $persona->genero ?? '') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                    <option value="otro" {{ old('genero', $persona->genero ?? '') == 'otro' ? 'selected' : '' }}>Otro</option>
                    <option value="prefiero_no_decirlo" {{ old('genero', $persona->genero ?? '') == 'prefiero_no_decirlo' ? 'selected' : '' }}>Prefiero no decirlo</option>
                </select>
                @error('genero')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
            </div>

            <div class="col-md-4">
                <label class="form-label fw-bold text-secondary">Teléfono <span class="text-muted fw-normal">(Opc.)</span></label>
                <input type="text" name="telefono" class="form-control form-control-lg bg-light" value="{{ old('telefono', $persona->telefono ?? '') }}" placeholder="10 dígitos">
                @error('telefono')<small class="text-danger fw-bold mt-1 d-block">{{ $message }}</small>@enderror
            </div>
        </div>

        <div class="mt-4 text-end border-top pt-3">
            <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">
                <i class="bi bi-save me-2"></i>Guardar Expediente
            </button>
        </div>
    </form>
</div>
