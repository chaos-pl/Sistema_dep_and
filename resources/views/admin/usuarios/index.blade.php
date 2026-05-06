@extends('layouts.app')

@section('title', 'Usuarios - PROMETEO')
@section('page-title', 'Directorio de Usuarios')
@section('page-subtitle', 'Listado general de cuentas del sistema')

@push('styles')
    <style>
        .search-input-prometeo { padding-left: 2.8rem !important; }
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
        $rolesForm = \Spatie\Permission\Models\Role::orderBy('name')->get();
        $personasDisponibles = \App\Models\Persona::whereNull('user_id')->orderBy('nombre')->get();
    @endphp

    <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

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
                    <button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" data-bs-toggle="modal" data-bs-target="#modalCreateUsuario" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-person-plus-fill me-2"></i> Nuevo Usuario
                    </button>
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
                    <th class="py-3 text-center text-body-secondary fw-bold border-0">Fecha de Alta</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($usuarios as $usuario)
                    <tr class="usuario-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-circle"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body usuario-name">{{ $usuario->name }}</h6>
                                    <small class="text-body-secondary fw-medium usuario-email"><i class="bi bi-envelope-at me-1 opacity-50"></i>{{ $usuario->email }}</small>
                                </div>
                            </div>
                        </td>

                        <td class="py-3 border-0">
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($usuario->roles as $role)
                                    @php
                                        // Mapa de colores e íconos por rol (Jerarquía)
                                        $roleTheme = match(strtolower($role->name)) {
                                            'admin' => ['class' => 'bg-danger text-danger border-danger', 'icon' => 'bi-shield-lock-fill'],
                                            'psicologo' => ['class' => 'bg-info text-info border-info', 'icon' => 'bi-activity'],
                                            'tutor' => ['class' => 'bg-success text-success border-success', 'icon' => 'bi-person-video3'],
                                            'estudiante' => ['class' => 'bg-primary text-primary border-primary', 'icon' => 'bi-mortarboard-fill'],
                                            default => ['class' => 'bg-secondary text-body-secondary border-secondary', 'icon' => 'bi-person-badge']
                                        };
                                    @endphp
                                    <span class="badge bg-opacity-10 border border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm {{ $roleTheme['class'] }}">
                                        <i class="bi {{ $roleTheme['icon'] }} me-1"></i> {{ ucfirst($role->name) }}
                                    </span>
                                @empty
                                    <span class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                                        <i class="bi bi-exclamation-circle me-1"></i> Sin rol
                                    </span>
                                @endforelse
                            </div>
                        </td>

                        <td class="py-3 border-0">
                            @if($usuario->persona)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-vcard text-success"></i>
                                    <span class="fw-bold text-body">{{ $usuario->persona->nombre }} {{ $usuario->persona->apellido_paterno }}</span>
                                </div>
                            @else
                                <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm">
                                    <i class="bi bi-link-45deg fs-6"></i> Sin vincular
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
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalEditUsuario{{ $usuario->id }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm hover-elevate" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                @endcan

                                @can('usuarios.eliminar')
                                    <form action="{{ route('admin.usuarios.destroy', $usuario) }}" method="POST" onsubmit="return confirm('ATENCIÓN: ¿Deseas eliminar permanentemente a este usuario y revocar su acceso?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm hover-elevate" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Eliminar">
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

    <!-- Modal Nuevo Usuario -->
    <div class="modal fade" id="modalCreateUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-black mb-0"><i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Usuario</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.usuarios.store') }}" method="POST">
                    @csrf
                    <div class="modal-body modal-body-custom">
                        @include('admin.usuarios.partials.form', ['user' => null, 'roles' => $rolesForm, 'personas' => $personasDisponibles])
                    </div>

                    <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-floppy-fill me-2"></i>Guardar Usuario</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modales de Edición -->
    @foreach($usuarios as $usuario)
        @php
            $personasEdit = \App\Models\Persona::whereNull('user_id')
                ->orWhere('user_id', $usuario->id)
                ->orderBy('nombre')
                ->get();
        @endphp
        <div class="modal fade" id="modalEditUsuario{{ $usuario->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center" style="background-color: var(--app-primary-dark);">
                        <h5 class="modal-title fw-black mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar Cuenta #{{ $usuario->id }}</h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.usuarios.update', $usuario) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body modal-body-custom">
                            @include('admin.usuarios.partials.form', ['user' => $usuario, 'roles' => $rolesForm, 'personas' => $personasEdit])
                        </div>

                        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Cuenta</button>
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
