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
        .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }

        /* --- DISEÑO PREMIUM PARA MODALES --- */
        .modal-content {
            border-radius: 1.5rem;
            overflow: hidden;
            border: 0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .modal-header-custom {
            background-color: var(--app-primary);
            color: white;
            border-bottom: 0;
            padding: 1.5rem 2rem;
        }

        .modal-body-custom {
            padding: 2rem;
            background-color: var(--app-surface);
        }

        .modal-footer-custom {
            padding: 1.5rem 2rem;
            border-top: 1px solid rgba(148, 163, 184, 0.15);
            background-color: rgba(148, 163, 184, 0.05);
        }

        /* Ajustes Modo Oscuro para Modales */
        body.theme-dark .modal-content, body.theme-system .modal-content {
            background-color: #1e293b !important;
            border: 1px solid rgba(255,255,255,0.1);
        }
        body.theme-dark .modal-body-custom, body.theme-system .modal-body-custom {
            background-color: #0f172a !important;
        }
        body.theme-dark .modal-footer-custom, body.theme-system .modal-footer-custom {
            background-color: #1e293b !important;
            border-color: rgba(255,255,255,0.1) !important;
        }

        /* Animación de Despliegue */
        .modal.fade {
            perspective: 2000px;
        }

        .modal.fade .modal-dialog {
            opacity: 0;
            transform-origin: center center;
            transform: translateZ(-500px) rotateY(90deg) scale(0.5);
            transition: transform 0.7s cubic-bezier(0.165, 0.84, 0.44, 1), opacity 0.4s ease-in-out;
        }

        .modal.show .modal-dialog {
            opacity: 1;
            transform: translateZ(0) rotateY(0deg) scale(1);
        }

        .modal-backdrop.show {
            opacity: 0.75;
            backdrop-filter: blur(8px) brightness(0.4);
            background-color: #000000;
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
                    <button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" data-bs-toggle="modal" data-bs-target="#modalCreatePermiso" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-plus-lg me-2"></i> Nuevo Permiso
                    </button>
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
                                        <button type="button" data-bs-toggle="modal" data-bs-target="#modalEditPermiso{{ $permission->id }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                            <i class="bi bi-pencil-fill"></i>
                                        </button>
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

    <!-- Modal Nuevo Permiso -->
    <div class="modal fade" id="modalCreatePermiso" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-black mb-0"><i class="bi bi-plus-lg me-2"></i>Registrar Nuevo Permiso</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.permisos.store') }}" method="POST">
                    @csrf
                    <div class="modal-body modal-body-custom">
                        @include('admin.permisos.partials.form', ['permiso' => null])
                    </div>
                    <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-save me-2"></i>Guardar Permiso</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modales de Edición -->
    @foreach($groupedPermissions as $grupo => $permisos)
        @foreach($permisos as $permission)
            <div class="modal fade" id="modalEditPermiso{{ $permission->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center" style="background-color: var(--app-primary-dark);">
                            <h5 class="modal-title fw-black mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar Permiso #{{ $permission->id }}</h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('admin.permisos.update', $permission->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body modal-body-custom">
                                @include('admin.permisos.partials.form', ['permiso' => $permission])
                            </div>
                            <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                                <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Permiso</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    @endforeach
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // FIX: EVITAR QUE LA PANTALLA SE CONGELE CON LOS MODALES
            document.querySelectorAll('.modal').forEach(modal => {
                document.body.appendChild(modal);
            });
        });

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
