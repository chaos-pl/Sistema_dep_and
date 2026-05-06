@extends('layouts.app')

@section('title', 'Roles de Sistema - PROMETEO')
@section('page-title', 'Gestión de Roles')
@section('page-subtitle', 'Administra los roles y accesos del sistema')

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
    </style>
@endpush

@section('content')
    @php
        $permissionsList = \Spatie\Permission\Models\Permission::orderBy('name')->get();
    @endphp

    <div class="app-card p-4 p-md-5">

        <div class="row align-items-center justify-content-between mb-5 gy-3">
            <div class="col-lg-5">
                <h4 class="fw-black text-body mb-1"><i class="bi bi-shield-check text-primary me-2"></i>Roles Registrados</h4>
                <p class="text-body-secondary mb-0">Controla quién tiene acceso a cada módulo.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar rol..." onkeyup="filterRoles()">
                </div>

                @can('roles.crear')
                    <button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" data-bs-toggle="modal" data-bs-target="#modalCreateRole" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-plus-lg me-2"></i> Nuevo Rol
                    </button>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="rolesTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Rol</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Permisos Totales</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Usuarios Asignados</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($roles as $role)
                    <tr class="role-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-badge-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body role-name">{{ ucfirst($role->name) }}</h6>
                                    <small class="text-body-secondary">ID: #{{ $role->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="text-center py-3 border-0">
                            <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-key-fill me-1"></i> {{ $role->permissions_count }}
                            </span>
                        </td>
                        <td class="text-center py-3 border-0">
                            <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-people-fill me-1"></i> {{ $role->users_count ?? 0 }}
                            </span>
                        </td>
                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                @can('roles.ver')
                                    <a href="{{ route('admin.roles.show', $role) }}" class="btn btn-sm btn-light border text-info rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Ver detalle">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                @endcan

                                @can('roles.editar')
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalEditRole{{ $role->id }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                @endcan

                                @can('roles.eliminar')
                                    <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" onsubmit="return confirm('ATENCIÓN: Eliminar este rol revocará el acceso a todos sus usuarios. ¿Continuar?');">
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
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-shield-x fs-1 opacity-25 mb-3"></i>
                                <span>No se encontraron roles registrados.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $roles->links() }}
        </div>
    </div>

    <!-- Modal Nuevo Rol -->
    <div class="modal fade" id="modalCreateRole" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-black mb-0"><i class="bi bi-plus-lg me-2"></i>Registrar Nuevo Rol</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.roles.store') }}" method="POST">
                    @csrf
                    <div class="modal-body modal-body-custom">
                        @include('admin.roles.partials.form', ['role' => null, 'permissions' => $permissionsList, 'rolePermissions' => []])
                    </div>
                    <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-save me-2"></i>Guardar Rol</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modales de Edición -->
    @foreach($roles as $role)
        @php
            $rolePermissions = $role->permissions->pluck('id')->toArray();
        @endphp
        <div class="modal fade" id="modalEditRole{{ $role->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center" style="background-color: var(--app-primary-dark);">
                        <h5 class="modal-title fw-black mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar Rol #{{ $role->id }}</h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body modal-body-custom">
                            @include('admin.roles.partials.form', ['role' => $role, 'permissions' => $permissionsList, 'rolePermissions' => $rolePermissions])
                        </div>
                        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Rol</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
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

        function filterRoles() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.role-row');

            rows.forEach(row => {
                let name = row.querySelector('.role-name').textContent.toLowerCase();
                if (name.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
@endpush
