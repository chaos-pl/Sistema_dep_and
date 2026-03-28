<div class="row g-4">
    <div class="col-md-12 mb-2">
        <label class="form-label fw-bold text-secondary">Nombre del permiso</label>
        <div class="input-group">
            <span class="input-group-text bg-light border-end-0 text-muted px-3"><i class="bi bi-key-fill"></i></span>
            <input type="text" name="name" class="form-control form-control-lg bg-light border-start-0 ps-0" value="{{ old('name', $permiso->name ?? '') }}" placeholder="Ej. usuarios.crear" required>
        </div>
        <small class="text-muted d-block mt-2"><i class="bi bi-info-circle me-1"></i>Se recomienda usar la nomenclatura <code>modulo.accion</code> (ej. <i>reportes.ver</i>).</small>
        @error('name')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
    </div>
</div>

<div class="d-flex justify-content-between mt-5 pt-4 border-top">
    <a href="{{ route('admin.permisos.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-secondary shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Cancelar
    </a>

    <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>{{ isset($permiso) ? 'Actualizar Permiso' : 'Guardar Permiso' }}
    </button>
</div>
