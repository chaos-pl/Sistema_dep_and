@php
    $isModal = $isModal ?? false;
    $modalType = $modalType ?? null;
    $modalId = $modalId ?? null;

    $showNombreError = true;

    if ($isModal && $errors->any()) {
        if ($modalType === 'create') {
            $showNombreError = old('modal_type') === 'create';
        } elseif ($modalType === 'edit') {
            $showNombreError = old('modal_type') === 'edit' && (string) old('modal_id') === (string) $modalId;
        }
    }

    if ($isModal) {
        if ($modalType === 'create' && old('modal_type') === 'create') {
            $nombreValue = old('nombre', '');
        } elseif ($modalType === 'edit' && old('modal_type') === 'edit' && (string) old('modal_id') === (string) $modalId) {
            $nombreValue = old('nombre', $carrera->nombre ?? '');
        } else {
            $nombreValue = $carrera->nombre ?? '';
        }
    } else {
        $nombreValue = old('nombre', $carrera->nombre ?? '');
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

<div class="row g-4">
    <div class="col-12">
        <label class="form-label fw-bold text-body-secondary">Nombre de la carrera</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                <i class="bi bi-mortarboard-fill"></i>
            </span>

            <input type="text"
                   name="nombre"
                   value="{{ $nombreValue }}"
                   class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if($showNombreError) @error('nombre') is-invalid @enderror @endif"
                   placeholder="Ej. Ingeniería en Sistemas Computacionales"
                   required>
        </div>

        @if($showNombreError)
            @error('nombre')
            <small class="text-danger fw-bold mt-1 d-block">
                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
            </small>
            @enderror
        @endif
    </div>
</div>

<div class="d-flex justify-content-between mt-5 pt-4 border-top border-secondary border-opacity-10">
    @if($isModal)
        <button type="button"
                class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm"
                data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-2"></i>Cancelar
        </button>
    @else
        <a href="{{ route('admin.carreras.index') }}"
           class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Cancelar
        </a>
    @endif

    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ $submitText }}
    </button>
</div>
