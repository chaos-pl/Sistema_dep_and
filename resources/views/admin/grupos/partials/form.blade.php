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
            $carreraValue = old('carrera_id', '');
            $tutorValue = old('tutor_id', '');
            $nombreValue = old('nombre', '');
            $periodoValue = old('periodo', '');
        } elseif ($modalType === 'edit' && old('modal_type') === 'edit' && (string) old('modal_id') === (string) $modalId) {
            $carreraValue = old('carrera_id', $grupo->carrera_id ?? '');
            $tutorValue = old('tutor_id', $grupo->tutor_id ?? '');
            $nombreValue = old('nombre', $grupo->nombre ?? '');
            $periodoValue = old('periodo', $grupo->periodo ?? '');
        } else {
            $carreraValue = $grupo->carrera_id ?? '';
            $tutorValue = $grupo->tutor_id ?? '';
            $nombreValue = $grupo->nombre ?? '';
            $periodoValue = $grupo->periodo ?? '';
        }
    } else {
        $carreraValue = old('carrera_id', $grupo->carrera_id ?? '');
        $tutorValue = old('tutor_id', $grupo->tutor_id ?? '');
        $nombreValue = old('nombre', $grupo->nombre ?? '');
        $periodoValue = old('periodo', $grupo->periodo ?? '');
    }
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
        <div class="col-md-6">
            <label class="form-label fw-bold text-body-secondary">Carrera vinculada</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-mortarboard-fill"></i>
                </span>
                <select name="carrera_id"
                        class="form-select form-select-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('carrera_id') is-invalid @enderror @endif"
                        required>
                    <option value="">Selecciona una carrera...</option>
                    @foreach($carreras as $carrera)
                        <option value="{{ $carrera->id }}" @selected((string)$carreraValue === (string)$carrera->id)>
                            {{ $carrera->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($isCurrentModalError)
                @error('carrera_id')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold text-body-secondary">Tutor asignado</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-person-video3"></i>
                </span>
                <select name="tutor_id"
                        class="form-select form-select-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('tutor_id') is-invalid @enderror @endif"
                        required>
                    <option value="">Selecciona un tutor...</option>
                    @foreach($tutores as $tutor)
                        <option value="{{ $tutor->id }}" @selected((string)$tutorValue === (string)$tutor->id)>
                            {{ $tutor->persona->nombre ?? '' }}
                            {{ $tutor->persona->apellido_paterno ?? '' }}
                            {{ $tutor->persona->apellido_materno ?? '' }}
                            — {{ $tutor->numero_empleado }}
                        </option>
                    @endforeach
                </select>
            </div>
            @if($isCurrentModalError)
                @error('tutor_id')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold text-body-secondary">Nombre del grupo</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-collection-fill"></i>
                </span>
                <input type="text"
                       name="nombre"
                       value="{{ $nombreValue }}"
                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('nombre') is-invalid @enderror @endif"
                       placeholder="Ej. 401-A"
                       required>
            </div>
            @if($isCurrentModalError)
                @error('nombre')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                @enderror
            @endif
        </div>

        <div class="col-md-6">
            <label class="form-label fw-bold text-body-secondary">Periodo escolar</label>
            <div class="input-group">
                <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                    <i class="bi bi-calendar3"></i>
                </span>
                <input type="text"
                       name="periodo"
                       value="{{ $periodoValue }}"
                       class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($isCurrentModalError) @error('periodo') is-invalid @enderror @endif"
                       placeholder="Ej. 2026-A"
                       required>
            </div>
            @if($isCurrentModalError)
                @error('periodo')
                <small class="text-danger fw-bold mt-1 d-block">
                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                </small>
                @enderror
            @endif
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
        <a href="{{ route('admin.grupos.index') }}"
           class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Cancelar
        </a>
    @endif

    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ $submitText }}
    </button>
</div>
