@csrf
@if(isset($method) && strtoupper($method) !== 'POST')
    @method($method)
@endif

<div class="row g-4">
    <div class="col-12">
        <h6 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-2">
            <i class="bi bi-person-vcard-fill me-2"></i>Datos Personales
        </h6>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Nombre(s)</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-person"></i></span>
            <input type="text" name="nombre" value="{{ old('nombre', $psicologo->persona->nombre ?? '') }}" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" placeholder="Ej. Carlos" required>
        </div>
        @error('nombre') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Apellido paterno</label>
        <input type="text" name="apellido_paterno" value="{{ old('apellido_paterno', $psicologo->persona->apellido_paterno ?? '') }}" class="form-control form-control-lg bg-body-tertiary" placeholder="Ej. Méndez" required>
        @error('apellido_paterno') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Apellido materno <span class="fw-normal text-body-secondary">(Opcional)</span></label>
        <input type="text" name="apellido_materno" value="{{ old('apellido_materno', $psicologo->persona->apellido_materno ?? '') }}" class="form-control form-control-lg bg-body-tertiary" placeholder="Ej. Torres">
        @error('apellido_materno') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Fecha de nacimiento</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-calendar-event"></i></span>
            <input type="date" name="fecha_nacimiento" value="{{ old('fecha_nacimiento', $psicologo->persona->fecha_nacimiento ?? '') }}" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" required>
        </div>
        @error('fecha_nacimiento') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Género</label>
        <select name="genero" class="form-select form-select-lg bg-body-tertiary" required>
            <option value="">Selecciona...</option>
            <option value="masculino" @selected(old('genero', $psicologo->persona->genero ?? '') === 'masculino')>Masculino</option>
            <option value="femenino" @selected(old('genero', $psicologo->persona->genero ?? '') === 'femenino')>Femenino</option>
            <option value="otro" @selected(old('genero', $psicologo->persona->genero ?? '') === 'otro')>Otro</option>
            <option value="prefiero_no_decirlo" @selected(old('genero', $psicologo->persona->genero ?? '') === 'prefiero_no_decirlo')>Prefiero no decirlo</option>
        </select>
        @error('genero') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Teléfono <span class="fw-normal text-body-secondary">(Opcional)</span></label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-telephone"></i></span>
            <input type="text" name="telefono" value="{{ old('telefono', $psicologo->persona->telefono ?? '') }}" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" placeholder="10 dígitos">
        </div>
        @error('telefono') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-12 mt-4">
        <h6 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-2">
            <i class="bi bi-shield-lock-fill me-2"></i>Credenciales y Profesión
        </h6>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold text-body-secondary">Cédula Profesional</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-award-fill"></i></span>
            <input type="text" name="cedula_profesional" value="{{ old('cedula_profesional', $psicologo->cedula_profesional ?? '') }}" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" placeholder="Ej. 1234567" required>
        </div>
        @error('cedula_profesional') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-8">
        <label class="form-label fw-bold text-body-secondary">Correo electrónico institucional</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-envelope-at"></i></span>
            <input type="email" name="email" value="{{ old('email', $psicologo->persona->user->email ?? '') }}" class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0" placeholder="psicologo@institucion.edu" required>
        </div>
        @error('email') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-body-secondary">
            {{ isset($psicologo) ? 'Nueva contraseña' : 'Contraseña' }}
            @if(isset($psicologo)) <span class="fw-normal text-body-secondary">(Opcional)</span> @endif
        </label>
        <div class="position-relative">
            <input type="password" name="password" id="password" class="form-control form-control-lg bg-body-tertiary {{ isset($psicologo) ? '' : 'pe-5' }}" placeholder="Mínimo 8 caracteres" {{ isset($psicologo) ? '' : 'required' }}>
            @if(!isset($psicologo))
                <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y text-body-secondary border-0 bg-transparent me-2" onclick="togglePasswordVisibility('password', 'togglePasswordIcon')">
                    <i class="bi bi-eye" id="togglePasswordIcon"></i>
                </button>
            @endif
        </div>
        @error('password') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold text-body-secondary">Confirmar contraseña</label>
        <div class="position-relative">
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control form-control-lg bg-body-tertiary {{ isset($psicologo) ? '' : 'pe-5' }}" placeholder="Repite la contraseña" {{ isset($psicologo) ? '' : 'required' }}>
            @if(!isset($psicologo))
                <button type="button" class="btn position-absolute end-0 top-50 translate-middle-y text-body-secondary border-0 bg-transparent me-2" onclick="togglePasswordVisibility('password_confirmation', 'toggleConfirmPasswordIcon')">
                    <i class="bi bi-eye" id="toggleConfirmPasswordIcon"></i>
                </button>
            @endif
        </div>
    </div>
</div>

<div class="d-flex justify-content-between mt-5 pt-4 border-top border-secondary border-opacity-10">
    <a href="{{ route('admin.psicologos.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ $submitText }}
    </button>
</div>
