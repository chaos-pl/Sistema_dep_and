@extends('layouts.app')

@section('title', 'Roles de Sistema - PROMETEO')
@section('page-title', 'Gestión de Roles')
@section('page-subtitle', 'Administra los roles y accesos del sistema')

@push('styles')
    <style>
        .search-input-prometeo {
            padding-left: 2.8rem !important;
        }
    </style>
@endpush

@section('content')
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
                    <a href="{{ route('admin.roles.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-plus-lg me-2"></i> Nuevo Rol
                    </a>
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
                                    <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
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
@endsection

@push('scripts')
    <script>
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
