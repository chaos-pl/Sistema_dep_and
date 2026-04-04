@csrf
@if(isset($method) && strtoupper($method) !== 'POST')
    @method($method)
@endif

<div class="row g-4">
    <div class="col-12">
        <label class="form-label fw-bold text-body-secondary">Nombre de la carrera</label>
        <div class="input-group">
            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary"><i class="bi bi-mortarboard-fill"></i></span>
            <input type="text"
                   name="nombre"
                   value="{{ old('nombre', $carrera->nombre ?? '') }}"
                   class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0"
                   placeholder="Ej. Ingeniería en Sistemas Computacionales" required>
        </div>
        @error('nombre') <small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small> @enderror
    </div>
</div>

<div class="d-flex justify-content-between mt-5 pt-4 border-top border-secondary border-opacity-10">
    <a href="{{ route('admin.carreras.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-body-secondary shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Cancelar
    </a>
    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ $submitText }}
    </button>
</div>
