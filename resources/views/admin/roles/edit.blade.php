@extends('layouts.app')

@section('title', 'Editar Rol - PROMETEO')
@section('page-title', 'Editar Rol')
@section('page-subtitle', 'Actualización de matriz de accesos')

@php
    // Mapeo de íconos para mantener la consistencia visual
    $iconMap = [
        'usuarios' => 'bi-people-fill text-primary',
        'personas' => 'bi-person-vcard-fill text-info',
        'roles' => 'bi-shield-lock-fill text-warning',
        'permisos' => 'bi-key-fill text-danger',
        'evaluaciones' => 'bi-clipboard2-check-fill text-success',
        'diario_ia' => 'bi-journal-text text-primary',
        'alertas' => 'bi-exclamation-triangle-fill text-danger',
        'diagnosticos' => 'bi-file-earmark-medical-fill text-info',
        'resultados_ia' => 'bi-robot text-primary',
        'grupos' => 'bi-collection-fill text-secondary',
        'perfil' => 'bi-person-circle text-success',
        'consentimiento' => 'bi-file-earmark-lock-fill text-warning',
        'aviso_privacidad' => 'bi-shield-check text-success',
        'carreras' => 'bi-mortarboard-fill text-info',
        'reportes' => 'bi-bar-chart-fill text-primary'
    ];
@endphp

@section('content')
    <div class="app-card p-4 p-md-5">

        <form action="{{ route('admin.roles.update', $role) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row align-items-end border-bottom pb-4 mb-5">
                <div class="col-md-7 mb-4 mb-md-0">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="bg-warning bg-opacity-10 text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                            <i class="bi bi-pencil-fill fs-5"></i>
                        </div>
                        <h4 class="fw-black mb-0 text-dark">Modificando rol</h4>
                    </div>
                    <p class="text-muted mb-0 ms-5 ps-2">Edita el nombre del rol o revoca sus capacidades.</p>
                </div>
                <div class="col-md-5">
                    <label for="name" class="form-label fw-bold text-secondary">Nombre del rol</label>
                    <input type="text" name="name" id="name" class="form-control form-control-lg bg-light" value="{{ old('name', $role->name) }}" required>
                    @error('name')<small class="text-danger fw-bold mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>@enderror
                </div>
            </div>

            <div class="mb-4">
                <h5 class="fw-bold mb-1 text-dark"><i class="bi bi-ui-checks-grid text-primary me-2"></i>Matriz de Permisos</h5>
                <p class="text-muted mb-4">Selecciona o desmarca los módulos a los que este rol tendrá acceso.</p>

                @error('permissions')
                <div class="alert alert-danger border-0 shadow-sm rounded-4"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ $message }}</div>
                @enderror

                <div class="row g-4">
                    @foreach($permissions as $grupo => $items)
                        @php
                            $icon = $iconMap[$grupo] ?? 'bi-folder2-open text-secondary';
                        @endphp
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div class="app-card p-4 h-100 bg-light border-0 shadow-sm" style="border-radius: 1.5rem;">
                                <h6 class="fw-black text-uppercase mb-3 border-bottom pb-2" style="letter-spacing: 0.5px;">
                                    <i class="bi {{ $icon }} me-2 fs-5"></i>
                                    {{ str_replace('_', ' ', $grupo) }}
                                </h6>

                                <div class="d-flex flex-column gap-2">
                                    @foreach($items as $permission)
                                        <div class="form-check custom-check">
                                            <input class="form-check-input shadow-none" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}" {{ in_array($permission->name, old('permissions', $rolePermissions)) ? 'checked' : '' }}>
                                            <label class="form-check-label text-secondary fw-medium" for="perm_{{ $permission->id }}" style="font-size: 0.9rem; cursor: pointer;">
                                                {{ ucfirst(str_replace($grupo . '.', '', $permission->name)) }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="d-flex justify-content-between mt-5 pt-4 border-top">
                <a href="{{ route('admin.roles.index') }}" class="btn btn-light border px-4 rounded-pill fw-bold text-secondary shadow-sm">
                    <i class="bi bi-arrow-left me-2"></i>Cancelar
                </a>
                <button type="submit" class="btn btn-primary px-5 rounded-pill fw-bold shadow-sm">
                    <i class="bi bi-save me-2"></i>Actualizar Rol
                </button>
            </div>
        </form>
    </div>
@endsection
