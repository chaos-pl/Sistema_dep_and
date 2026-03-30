@extends('layouts.app')

@section('title', 'Usuarios - PROMETEO')
@section('page-title', 'Directorio de Usuarios')
@section('page-subtitle', 'Listado general de cuentas del sistema')

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
                <h4 class="fw-black text-body mb-1"><i class="bi bi-people-fill text-primary me-2"></i>Cuentas de Acceso</h4>
                <p class="text-body-secondary mb-0">Administra los accesos y vínculos personales.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar por nombre o correo..." onkeyup="filterUsuarios()">
                </div>

                @can('usuarios.crear')
                    <a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
                    </a>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="usuariosTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Cuenta</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Roles Asignados</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Persona Vinculada</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Alta</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($usuarios as $usuario)
                    <tr class="usuario-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body usuario-name">{{ $usuario->name }}</h6>
                                    <small class="text-body-secondary usuario-email">{{ $usuario->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 border-0">
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($usuario->roles as $role)
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 rounded-pill px-2 py-1 fw-semibold">
                                        {{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 rounded-pill px-2 py-1 fw-semibold">
                                        Sin rol
                                    </span>
                                @endforelse
                            </div>
                        </td>
                        <td class="py-3 border-0">
                            @if($usuario->persona)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-vcard text-success"></i>
                                    <span class="fw-medium text-body">{{ $usuario->persona->nombre }} {{ $usuario->persona->apellido_paterno }}</span>
                                </div>
                            @else
                                <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-medium">
                                    <i class="bi bi-link-45deg"></i> Sin vincular
                                </span>
                            @endif
                        </td>
                        <td class="text-center py-3 text-body-secondary fw-medium border-0">
                            <i class="bi bi-calendar-check opacity-50 me-1"></i>
                            {{ optional($usuario->created_at)->format('d/m/Y') }}
                        </td>
                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                @can('usuarios.editar')
                                    <a href="{{ route('admin.usuarios.edit', $usuario) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                @endcan

                                @can('usuarios.eliminar')
                                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('ATENCIÓN: ¿Deseas eliminar permanentemente a este usuario y revocar su acceso?');">
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
                        <td colspan="5" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-people fs-1 opacity-25 mb-3"></i>
                                <span>No hay usuarios registrados en el sistema.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $usuarios->links() }}
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function filterUsuarios() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.usuario-row');

            rows.forEach(row => {
                let name = row.querySelector('.usuario-name').textContent.toLowerCase();
                let email = row.querySelector('.usuario-email').textContent.toLowerCase();
                if (name.includes(filter) || email.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
@endpush
