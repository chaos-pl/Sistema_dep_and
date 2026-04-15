@php
    $isModal = $isModal ?? false;
    $modalType = $modalType ?? null;
    $modalId = $modalId ?? null;

    $isCurrentModalError = true;

    if ($isModal && $errors->any()) {
        $isCurrentModalError =
            ($modalType === 'create' && old('modal_type') === 'create') ||
            ($modalType === 'edit' && old('modal_type') === 'edit' && (string) old('modal_id') === (string) $modalId);
    }

    if ($isModal) {
        if ($modalType === 'create' && old('modal_type') === 'create') {
            $nombreValue = old('nombre', '');
            $apellidoPaternoValue = old('apellido_paterno', '');
            $apellidoMaternoValue = old('apellido_materno', '');
            $fechaNacimientoValue = old('fecha_nacimiento', '');
            $generoValue = old('genero', '');
            $telefonoValue = old('telefono', '');
            $numeroEmpleadoValue = old('numero_empleado', '');
            $emailValue = old('email', '');
        } elseif ($modalType === 'edit' && old('modal_type') === 'edit' && (string) old('modal_id') === (string) $modalId) {
            $nombreValue = old('nombre', $tutor->persona?->nombre ?? '');
            $apellidoPaternoValue = old('apellido_paterno', $tutor->persona?->apellido_paterno ?? '');
            $apellidoMaternoValue = old('apellido_materno', $tutor->persona?->apellido_materno ?? '');
            $fechaNacimientoValue = old('fecha_nacimiento', $tutor->persona?->fecha_nacimiento ?? '');
            $generoValue = old('genero', $tutor->persona?->genero ?? '');
            $telefonoValue = old('telefono', $tutor->persona?->telefono ?? '');
            $numeroEmpleadoValue = old('numero_empleado', $tutor->numero_empleado ?? '');
            $emailValue = old('email', $tutor->persona?->user?->email ?? '');
        } else {
            $nombreValue = $tutor->persona?->nombre ?? '';
            $apellidoPaternoValue = $tutor->persona?->apellido_paterno ?? '';
            $apellidoMaternoValue = $tutor->persona?->apellido_materno ?? '';
            $fechaNacimientoValue = $tutor->persona?->fecha_nacimiento ?? '';
            $generoValue = $tutor->persona?->genero ?? '';
            $telefonoValue = $tutor->persona?->telefono ?? '';
            $numeroEmpleadoValue = $tutor->numero_empleado ?? '';
            $emailValue = $tutor->persona?->user?->email ?? '';
        }
    } else {
        $nombreValue = old('nombre', $tutor->persona?->nombre ?? '');
        $apellidoPaternoValue = old('apellido_paterno', $tutor->persona?->apellido_paterno ?? '');
        $apellidoMaternoValue = old('apellido_materno', $tutor->persona?->apellido_materno ?? '');
        $fechaNacimientoValue = old('fecha_nacimiento', $tutor->persona?->fecha_nacimiento ?? '');
        $generoValue = old('genero', $tutor->persona?->genero ?? '');
        $telefonoValue = old('telefono', $tutor->persona?->telefono ?? '');
        $numeroEmpleadoValue = old('numero_empleado', $tutor->numero_empleado ?? '');
        $emailValue = old('email', $tutor->persona?->user?->email ?? '');
    }

    $formSuffix = $isModal
        ? ($modalType === 'edit' ? 'edit_' . $modalId : 'create')
        : 'page';

    $passwordId = 'password_' . $formSuffix;
    $passwordConfirmId = 'password_confirmation_' . $formSuffix;
    $passwordIconId = 'togglePasswordIcon_' . $formSuffix;
    $passwordConfirmIconId = 'toggleConfirmPasswordIcon_' . $formSuffix;
@endphp

@csrf
@if(isset($method) && strtoupper($method) !== 'POST')
    @method($method)
@endif

@if($isModal)
    <input type="hidden" name="modal_type" value="{{ $modalType }}">
    @if($modalType === 'edit')
        <input type="hidden" name="modal_id" value="{{ $modalId }}">
    @endif
@endif

<div class="modal-body modal-body-custom">
    <div class="row g-4">
        <div class="col-12">
            <h6 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-2">
                <i class="bi bi-person-vcard-fill me-2"></i>Datos Personales
            </h6>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-body-secondary">Nombre(s)</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-person"></i>
                </span>
                <input type="text"
                       name="nombre"
                       value="{{ $nombreValue }}"
                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('nombre') is-invalid @enderror @endif"
                       placeholder="Ej. Laura"
                       required>
            </div>
            @if($isCurrentModalError)
                @error('nombre')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-body-secondary">Apellido paterno</label>
            <input type="text"
                   name="apellido_paterno"
                   value="{{ $apellidoPaternoValue }}"
                   class="form-control form-control-lg bg-body-tertiary @if($isCurrentModalError) @error('apellido_paterno') is-invalid @enderror @endif"
                   placeholder="Ej. Gómez"
                   required>
            @if($isCurrentModalError)
                @error('apellido_paterno')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-body-secondary">Apellido materno <span class="fw-normal text-body-secondary">(Opcional)</span></label>
            <input type="text"
                   name="apellido_materno"
                   value="{{ $apellidoMaternoValue }}"
                   class="form-control form-control-lg bg-body-tertiary @if($isCurrentModalError) @error('apellido_materno') is-invalid @enderror @endif"
                   placeholder="Ej. Silva">
            @if($isCurrentModalError)
                @error('apellido_materno')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-body-secondary">Fecha de nacimiento</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-calendar-event"></i>
                </span>
                <input type="date"
                       name="fecha_nacimiento"
                       value="{{ $fechaNacimientoValue }}"
                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('fecha_nacimiento') is-invalid @enderror @endif"
                       required>
            </div>
            @if($isCurrentModalError)
                @error('fecha_nacimiento')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-body-secondary">Género</label>
            <select name="genero"
                    class="form-select form-select-lg bg-body-tertiary @if($isCurrentModalError) @error('genero') is-invalid @enderror @endif"
                    required>
                <option value="">Selecciona...</option>
                <option value="masculino" @selected($generoValue === 'masculino')>Masculino</option>
                <option value="femenino" @selected($generoValue === 'femenino')>Femenino</option>
                <option value="otro" @selected($generoValue === 'otro')>Otro</option>
                <option value="prefiero_no_decirlo" @selected($generoValue === 'prefiero_no_decirlo')>Prefiero no decirlo</option>
            </select>
            @if($isCurrentModalError)
                @error('genero')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-body-secondary">Teléfono <span class="fw-normal text-body-secondary">(Opcional)</span></label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-telephone"></i>
                </span>
                <input type="text"
                       name="telefono"
                       value="{{ $telefonoValue }}"
                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('telefono') is-invalid @enderror @endif"
                       placeholder="10 dígitos">
            </div>
            @if($isCurrentModalError)
                @error('telefono')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-12 mt-4">
            <h6 class="fw-bold text-primary border-bottom border-primary border-opacity-25 pb-2 mb-2">
                <i class="bi bi-shield-lock-fill me-2"></i>Credenciales Institucionales
            </h6>
        </div>

        <div class="col-md-4">
            <label class="form-label fw-bold text-body-secondary">Número de empleado</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-hash"></i>
                </span>
                <input type="text"
                       name="numero_empleado"
                       value="{{ $numeroEmpleadoValue }}"
                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('numero_empleado') is-invalid @enderror @endif"
                       placeholder="Ej. EMP-0012"
                       required>
            </div>
            @if($isCurrentModalError)
                @error('numero_empleado')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-8">
            <label class="form-label fw-bold text-body-secondary">Correo electrónico institucional</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-envelope-at"></i>
                </span>
                <input type="email"
                       name="email"
                       value="{{ $emailValue }}"
                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('email') is-invalid @enderror @endif"
                       placeholder="tutor@institucion.edu"
                       required>
            </div>
            @if($isCurrentModalError)
                @error('email')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold text-body-secondary">
                {{ isset($tutor) && $tutor->exists ? 'Nueva contraseña' : 'Contraseña' }}
                @if(isset($tutor) && $tutor->exists)
                    <span class="fw-normal text-body-secondary">(Opcional)</span>
                @endif
            </label>
            <div class="position-relative">
                <input type="password"
                       name="password"
                       id="{{ $passwordId }}"
                       class="form-control form-control-lg bg-body-tertiary pe-5 @if($isCurrentModalError) @error('password') is-invalid @enderror @endif"
                       placeholder="Mínimo 8 caracteres"
                    {{ isset($tutor) && $tutor->exists ? '' : 'required' }}>
                <button type="button"
                        class="btn position-absolute end-0 top-50 translate-middle-y text-body-secondary border-0 bg-transparent me-2"
                        onclick="togglePasswordVisibility('{{ $passwordId }}', '{{ $passwordIconId }}')">
                    <i class="bi bi-eye" id="{{ $passwordIconId }}"></i>
                </button>
            </div>
            @if($isCurrentModalError)
                @error('password')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold text-body-secondary">Confirmar contraseña</label>
            <div class="position-relative">
                <input type="password"
                       name="password_confirmation"
                       id="{{ $passwordConfirmId }}"
                       class="form-control form-control-lg bg-body-tertiary pe-5"
                       placeholder="Repite la contraseña"
                    {{ isset($tutor) && $tutor->exists ? '' : 'required' }}>
                <button type="button"
                        class="btn position-absolute end-0 top-50 translate-middle-y text-body-secondary border-0 bg-transparent me-2"
                        onclick="togglePasswordVisibility('{{ $passwordConfirmId }}', '{{ $passwordConfirmIconId }}')">
                    <i class="bi bi-eye" id="{{ $passwordConfirmIconId }}"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal-footer modal-footer-custom d-flex justify-content-between gap-2">
    @if($isModal)
        <button type="button"
                class="btn btn-light rounded-pill px-4 fw-bold shadow-sm"
                data-bs-dismiss="modal">
            Cancelar
        </button>
    @else
        <a href="{{ route('admin.tutores.index') }}"
           class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Cancelar
        </a>
    @endif

    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ $submitText }}
    </button>
</div>
