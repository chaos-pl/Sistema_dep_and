<div class="mb-4">
    <label class="form-label fw-bold text-secondary">Nombre del Rol</label>
    <input type="text" name="name" class="form-control form-control-lg bg-light" value="{{ old('name', $role->name ?? '') }}" placeholder="Ej. administrador, orientador..." required>
    @error('name')
    <small class="text-danger fw-bold"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
    @enderror
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-3 mt-5 gap-3">
    <div>
        <h5 class="fw-black text-dark mb-1"><i class="bi bi-ui-checks-grid text-primary me-2"></i>Asignación de Permisos</h5>
        <p class="text-muted small mb-0">Selecciona los módulos a los que este rol tendrá acceso.</p>
    </div>
    <div class="position-relative w-100" style="max-width: 300px;">
        <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
            <i class="bi bi-search"></i>
        </div>
        <input type="text" class="form-control bg-light rounded-pill border-0 shadow-sm filter-permissions-input" style="padding-left: 2.8rem;" placeholder="Filtrar permisos...">
    </div>
</div>

<style>
    .permission-item {
        transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid transparent;
        border-radius: 0.5rem;
    }
    .permission-item:hover {
        background-color: rgba(124, 58, 237, 0.08);
        border-color: rgba(124, 58, 237, 0.2);
        transform: translateX(4px);
    }
    .permissions-wrapper {
        max-height: 48vh;
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .permissions-wrapper::-webkit-scrollbar {
        width: 6px;
    }
    .permissions-wrapper::-webkit-scrollbar-track {
        background: rgba(0,0,0,0.03);
        border-radius: 8px;
    }
    .permissions-wrapper::-webkit-scrollbar-thumb {
        background: rgba(0,0,0,0.15);
        border-radius: 8px;
    }
    .permissions-wrapper::-webkit-scrollbar-thumb:hover {
        background: rgba(0,0,0,0.25);
    }
    /* Modo Oscuro adaptaciones */
    body.theme-dark .permissions-wrapper::-webkit-scrollbar-track, body.theme-system .permissions-wrapper::-webkit-scrollbar-track {
        background: rgba(255,255,255,0.03);
    }
    body.theme-dark .permissions-wrapper::-webkit-scrollbar-thumb, body.theme-system .permissions-wrapper::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.15);
    }
    body.theme-dark .permission-item:hover, body.theme-system .permission-item:hover {
        background-color: rgba(124, 58, 237, 0.15);
        border-color: rgba(124, 58, 237, 0.3);
    }
</style>

@php
    // AGRUPACIÓN INTELIGENTE: Cortamos el nombre del permiso por el punto (ej. 'usuarios.ver' -> 'usuarios')
    $groupedPermissions = $permissions->groupBy(function($perm) {
        return explode('.', $perm->name)[0];
    });

    $actionIcons = [
        'ver' => 'bi-eye-fill text-info',
        'crear' => 'bi-plus-circle-fill text-success',
        'editar' => 'bi-pencil-fill text-warning',
        'eliminar' => 'bi-trash-fill text-danger',
        'asignar' => 'bi-person-badge-fill text-primary',
        'responder' => 'bi-pencil-square text-success',
        'aplicar' => 'bi-clipboard2-check text-primary',
        'clinicas' => 'bi-heart-pulse-fill text-danger',
        'propio' => 'bi-person-check-fill text-info',
        'default' => 'bi-check-circle-fill text-secondary'
    ];
@endphp

<div class="permissions-wrapper">
    <div class="row g-4">
        @foreach($groupedPermissions as $group => $perms)
            <div class="col-xl-3 col-lg-4 col-md-6">
                <div class="app-card p-4 h-100 bg-light border-0 shadow-sm" style="border-radius: 1.5rem;">
                    <h6 class="fw-bold text-primary text-uppercase mb-3 border-bottom border-primary border-opacity-25 pb-2">
                        <i class="bi bi-folder2-open me-2"></i>{{ str_replace('_', ' ', $group) }}
                    </h6>

                    @foreach($perms as $permission)
                        @php
                            $actionName = str_replace($group . '.', '', $permission->name);
                            $iconClass = $actionIcons['default'];
                            foreach($actionIcons as $key => $icon) {
                                if (str_contains($actionName, $key)) {
                                    $iconClass = $icon;
                                    break;
                                }
                            }
                        @endphp
                        <div class="form-check p-2 permission-item d-flex align-items-center mb-1">
                            <input class="form-check-input shadow-none mt-0 me-3 ms-1" type="checkbox"
                                   name="permissions[]"
                                   value="{{ $permission->id }}"
                                   id="perm_{{ $permission->id }}"
                                {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || (isset($rolePermissions) && in_array($permission->id, $rolePermissions)) ? 'checked' : '' }}>
                            
                            <label class="form-check-label text-secondary fw-medium d-flex align-items-center mb-0 w-100 flex-grow-1" style="font-size: 0.9rem; cursor: pointer;" for="perm_{{ $permission->id }}">
                                <i class="bi {{ $iconClass }} me-2 opacity-75 fs-6"></i>
                                <span class="text-truncate">{{ ucfirst(str_replace('_', ' ', $actionName)) }}</span>
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
</div>

<script>
    // Se ejecuta de inmediato para vincular el evento a la barra de búsqueda de este formulario específico
    (function() {
        // Encontramos todos los inputs de búsqueda en el DOM pero filtramos solo los que no están inicializados
        document.querySelectorAll('.filter-permissions-input').forEach(function(input) {
            if (input.dataset.searchBound) return;
            input.dataset.searchBound = "true";

            input.addEventListener('keyup', function() {
                const filter = this.value.toLowerCase().trim();
                const container = this.closest('form').querySelector('.permissions-wrapper');
                const modules = container.querySelectorAll('.col-xl-3');

                modules.forEach(function(mod) {
                    const groupTitle = mod.querySelector('h6').textContent.toLowerCase();
                    const items = mod.querySelectorAll('.permission-item');
                    let hasVisibleItem = false;

                    // Si el título del grupo coincide con la búsqueda, mostramos todo el grupo
                    const groupMatches = groupTitle.includes(filter);

                    items.forEach(function(item) {
                        const permissionText = item.querySelector('.text-truncate').textContent.toLowerCase();
                        if (groupMatches || permissionText.includes(filter)) {
                            item.style.display = 'flex'; // Usamos flex porque la clase d-flex se puede ver afectada
                            item.classList.add('d-flex');
                            hasVisibleItem = true;
                        } else {
                            item.style.display = 'none';
                            item.classList.remove('d-flex');
                        }
                    });

                    if (hasVisibleItem) {
                        mod.style.display = '';
                    } else {
                        mod.style.display = 'none';
                    }
                });
            });
        });
    })();
</script>
