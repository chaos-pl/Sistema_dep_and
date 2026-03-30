@extends('layouts.app')

@section('title', 'Permisos - PROMETEO')
@section('page-title', 'Catálogo de Permisos')
@section('page-subtitle', 'Administración de capacidades y accesos del sistema')

@php
    $iconMap = [
        'usuarios' => 'bi-people-fill text-primary', 'personas' => 'bi-person-vcard-fill text-info', 'roles' => 'bi-shield-lock-fill text-warning',
        'permisos' => 'bi-key-fill text-danger', 'evaluaciones' => 'bi-clipboard2-check-fill text-success', 'diario_ia' => 'bi-journal-text text-primary',
        'alertas' => 'bi-exclamation-triangle-fill text-danger', 'diagnosticos' => 'bi-file-earmark-medical-fill text-info', 'resultados_ia' => 'bi-robot text-primary',
        'grupos' => 'bi-collection-fill text-secondary', 'perfil' => 'bi-person-circle text-success', 'consentimiento' => 'bi-file-earmark-lock-fill text-warning',
        'aviso_privacidad' => 'bi-shield-check text-success', 'carreras' => 'bi-mortarboard-fill text-info', 'reportes' => 'bi-bar-chart-fill text-primary'
    ];
@endphp

@push('styles')
    <style>
        .search-input-prometeo {
            padding-left: 2.8rem !important;
        }

        /* --- ESTILO PARA LAS CATEGORÍAS ADAPTADO A MODO OSCURO --- */
        .category-header-row td {
            /* Usamos rgba para que se adapte al fondo oscuro o claro mágicamente */
            background: linear-gradient(90deg, rgba(124, 58, 237, 0.08) 0%, transparent 100%) !important;
            border-left: 5px solid var(--app-primary) !important;
            border-bottom: 1px solid rgba(124, 58, 237, 0.1) !important;
            border-top: none !important;
            transition: all 0.3s ease !important;
            cursor: default;
        }

        .category-header-row:hover td {
            background: linear-gradient(90deg, rgba(124, 58, 237, 0.15) 0%, transparent 100%) !important;
            padding-left: 2rem !important;
        }

        .category-header-content {
            transition: transform 0.3s ease;
        }
    </style>
@endpush

@section('content')
    <div class="app-card p-4 p-md-5">

        <div class="row align-items-center justify-content-between mb-5 gy-3">
            <div class="col-lg-5">
                <h4 class="fw-black text-body mb-1"><i class="bi bi-key-fill text-primary me-2"></i>Gestión de Permisos</h4>
                <p class="text-body-secondary mb-0">Administra las reglas de acceso agrupadas por módulo.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar permiso..." onkeyup="filterPermisos()">
                </div>

                @can('permisos.crear')
                    <a href="{{ route('admin.permisos.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-plus-lg me-2"></i> Nuevo Permiso
                    </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="permisosTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Nombre del Permiso</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Roles Asignados</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>

                @forelse($groupedPermissions as $grupo => $permisos)
                    <tbody class="permiso-group-container border-top-0">
                    <tr class="category-header-row">
                        <td colspan="3" class="py-3 px-4">
                            @php $icon = $iconMap[$grupo] ?? 'bi-folder-fill text-secondary'; @endphp
                            <div class="category-header-content d-flex align-items-center gap-2">
                                <h6 class="fw-black text-primary text-uppercase mb-0 d-flex align-items-center gap-2" style="letter-spacing: 0.5px;">
                                    <i class="bi {{ $icon }} fs-5"></i>
                                    Módulo: {{ str_replace('_', ' ', $grupo) }}
                                </h6>
                                <span class="badge bg-body text-primary border border-primary border-opacity-25 rounded-pill ms-2 fs-7 shadow-sm">
                                    {{ $permisos->count() }} permisos
                                </span>
                            </div>
                        </td>
                    </tr>

                    @foreach($permisos as $permission)
                        <tr class="permiso-row border-bottom border-secondary border-opacity-10">
                            <td class="px-4 py-3 ps-5 border-0">
                                <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                    <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 35px; height: 35px;">
                                        <i class="bi bi-shield-lock"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold text-body permiso-name">{{ $permission->name }}</span>
                                        <small class="text-body-secondary d-block" style="font-size: 0.75rem;">ID: #{{ $permission->id }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center py-3 border-0">
                                @if($permission->roles->count() > 0)
                                    <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold shadow-sm">
                                        <i class="bi bi-shield-check me-1"></i> {{ $permission->roles->count() }} roles
                                    </span>
                                @else
                                    <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-semibold">
                                        Sin asignar
                                    </span>
                                @endif
                            </td>
                            <td class="text-end px-4 py-3 border-0">
                                <div class="d-flex justify-content-end gap-2">
                                    @can('permisos.ver')
                                        <a href="{{ route('admin.permisos.show', $permission) }}" class="btn btn-sm btn-light border text-info rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Ver detalle">
                                            <i class="bi bi-eye-fill"></i>
                                        </a>
                                    @endcan

                                    @can('permisos.editar')
                                        <a href="{{ route('admin.permisos.edit', $permission) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </a>
                                    @endcan

                                    @can('permisos.eliminar')
                                        <form action="{{ route('admin.permisos.destroy', $permission) }}" method="POST" onsubmit="return confirm('ATENCIÓN: ¿Estás seguro de que deseas eliminar este permiso permanentemente?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Eliminar">
                                                <i class="bi bi-trash-fill"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                @empty
                    <tbody>
                    <tr>
                        <td colspan="3" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-key-fill fs-1 opacity-25 mb-3"></i>
                                <span>No hay permisos registrados en el sistema.</span>
                            </div>
                        </td>
                    </tr>
                    </tbody>
                @endforelse
            </table>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterPermisos() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let groups = document.querySelectorAll('.permiso-group-container');

            groups.forEach(group => {
                let rows = group.querySelectorAll('.permiso-row');
                let hasVisibleRow = false;

                rows.forEach(row => {
                    let name = row.querySelector('.permiso-name').textContent.toLowerCase();
                    if (name.includes(filter)) {
                        row.style.display = '';
                        hasVisibleRow = true;
                    } else {
                        row.style.display = 'none';
                    }
                });

                if (hasVisibleRow) {
                    group.style.display = '';
                } else {
                    group.style.display = 'none';
                }
            });
        }
    </script>
@endpush
