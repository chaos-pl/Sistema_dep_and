<div class="mb-4">
    <label class="form-label fw-bold text-secondary">Nombre del Rol</label>
    <input type="text" name="name" class="form-control form-control-lg bg-light" value="{{ old('name', $role->name ?? '') }}" placeholder="Ej. administrador, orientador..." required>
    @error('name')
    <small class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
    @enderror
</div>

<div class="mt-5 mb-4">
    <h5 class="fw-black text-dark mb-1"><i class="bi bi-ui-checks-grid text-primary me-2"></i>Asignación de Permisos</h5>
    <p class="text-muted small">Selecciona los módulos a los que este rol tendrá acceso.</p>
</div>

@php
    // AGRUPACIÓN INTELIGENTE: Cortamos el nombre del permiso por el punto (ej. 'usuarios.ver' -> 'usuarios')
    $groupedPermissions = $permissions->groupBy(function($perm) {
        return explode('.', $perm->name)[0];
    });
@endphp

<div class="row g-4">
    @foreach($groupedPermissions as $group => $perms)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="app-card p-4 h-100 bg-light border-0 shadow-sm" style="border-radius: 1.5rem;">
                <h6 class="fw-bold text-primary text-uppercase mb-3 border-bottom border-primary border-opacity-25 pb-2">
                    <i class="bi bi-folder2-open me-1"></i> {{ $group }}
                </h6>

                @foreach($perms as $permission)
                    <div class="form-check mb-2">
                        <input class="form-check-input shadow-none" type="checkbox"
                               name="permissions[]"
                               value="{{ $permission->id }}"
                               id="perm_{{ $permission->id }}"
                            {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || (isset($rolePermissions) && in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}>
                        <label class="form-check-label text-secondary fw-medium" style="font-size: 0.9rem;" for="perm_{{ $permission->id }}">
                            {{ str_replace($group . '.', '', $permission->name) }}
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach
</div>

<div class="d-flex justify-content-between mt-5 pt-3 border-top">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-secondary shadow-sm">
        <i class="bi bi-arrow-left me-2"></i>Cancelar
    </a>

    <button type="submit" class="btn btn-primary px-4 rounded-pill fw-bold shadow-sm">
        <i class="bi bi-save me-2"></i>Guardar configuración
    </button>
</div>
