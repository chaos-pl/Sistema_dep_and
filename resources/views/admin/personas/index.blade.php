@extends('layouts.app')

@section('title', 'Personas - PROMETEO')
@section('page-title', 'Directorio de Personas')
@section('page-subtitle', 'Listado general de perfiles personales')

@push('styles')
    <style>
        .search-input-prometeo {
            padding-left: 2.8rem !important;
        }

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

        /* ==============================================================
    DISEÑO PREMIUM PARA MODALES (Oscuro y Cristalino)
    ============================================================== */
        .modal-content {
            border-radius: 1.5rem;
            overflow: hidden;
            border: 0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
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

        /* ==============================================================
           ANIMACIÓN DE DESPLIEGUE: VOLTEO 3D (FLIP & ZOOM)
           ============================================================== */
        .modal.fade {
            perspective: 2000px; /* Gran profundidad para el giro */
        }

        .modal.fade .modal-dialog {
            opacity: 0;
            /* El punto de giro es el centro */
            transform-origin: center center;

            /* Estado inicial: Muy lejos, rotado hacia atrás y pequeño */
            transform: translateZ(-500px) rotateY(90deg) scale(0.5);

            transition:
                transform 0.7s cubic-bezier(0.165, 0.84, 0.44, 1),
                opacity 0.4s ease-in-out;
        }

        .modal.show .modal-dialog {
            opacity: 1;
            /* Estado final: Al frente, plano y tamaño real */
            transform: translateZ(0) rotateY(0deg) scale(1);
        }

        /* ==============================================================
           FONDO OSCURO Y CRISTALINO (Corregido)
           ============================================================== */
        .modal-backdrop.show {
            opacity: 0.75;
            backdrop-filter: blur(8px) brightness(0.4); /* Oscurecemos el fondo */
            background-color: #000000;
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
    </style>
@endpush

@section('content')
    <div class="app-card p-4 p-md-5 mb-4">

        <div class="row align-items-center justify-content-between mb-5 gy-3">
            <div class="col-lg-5">
                <h4 class="fw-black text-body mb-1"><i class="bi bi-person-lines-fill text-primary me-2"></i>Personas Registradas</h4>
                <p class="text-body-secondary mb-0">Consulta la información personal vinculada a los usuarios.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar por nombre..." onkeyup="filterPersonas()">
                </div>

                @can('personas.crear')
                    <button type="button" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" data-bs-toggle="modal" data-bs-target="#modalCreatePersona" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-person-plus-fill me-2"></i> Nueva Persona
                    </button>
                @endcan
            </div>
        </div>

        <div class="table-responsive">
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="personasTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">ID / Nombre</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Usuario Vinculado</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Nacimiento</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Género</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Teléfono</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($personas as $persona)
                    <tr class="persona-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body persona-name">
                                        {{ $persona->nombre }} {{ $persona->apellido_paterno }} {{ $persona->apellido_materno }}
                                    </h6>
                                    <small class="text-body-secondary">ID: #{{ $persona->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 border-0">
                            @if($persona->user)
                                <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                                    <i class="bi bi-envelope-at-fill me-1"></i> {{ $persona->user->email }}
                                </span>
                            @else
                                <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-medium shadow-sm">
                                    <i class="bi bi-link-45deg"></i> Sin vincular
                                </span>
                            @endif
                        </td>
                        <td class="py-3 text-body-secondary fw-medium border-0">
                            <i class="bi bi-calendar-event opacity-50 me-1"></i>
                            {{ \Carbon\Carbon::parse($persona->fecha_nacimiento)->format('d/m/Y') }}
                        </td>
                        <td class="text-center py-3 border-0">
                            @php
                                $badgeColor = match($persona->genero) {
                                    'masculino' => 'bg-info bg-opacity-10 text-info border-info',
                                    'femenino' => 'bg-danger bg-opacity-10 text-danger border-danger',
                                    default => 'bg-secondary bg-opacity-10 text-secondary border-secondary',
                                };
                            @endphp
                            <span class="badge border border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm {{ $badgeColor }}">
                                {{ ucfirst($persona->genero) }}
                            </span>
                        </td>
                        <td class="py-3 text-body-secondary fw-medium border-0">
                            {{ $persona->telefono ?? '—' }}
                        </td>
                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                @can('personas.editar')
                                    <button type="button" data-bs-toggle="modal" data-bs-target="#modalEditPersona{{ $persona->id }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                @endcan

                                @can('personas.eliminar')
                                    <form action="{{ route('admin.personas.destroy', $persona) }}" method="POST" class="d-inline m-0 form-eliminar-persona">
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
                        <td colspan="6" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-person-x fs-1 opacity-25 mb-3"></i>
                                <span>No hay personas registradas en el sistema.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $personas->links() }}
        </div>
    </div>

    <div class="modal fade" id="modalCreatePersona" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content">
                <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-black mb-0"><i class="bi bi-person-plus-fill me-2"></i>Registrar Nueva Persona</h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.personas.store') }}" method="POST">
                    @csrf
                    <div class="modal-body modal-body-custom">
                        @include('admin.personas.partials.form', ['persona' => new \App\Models\Persona])
                    </div>

                    <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-floppy-fill me-2"></i>Guardar Registro</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @foreach($personas as $persona)
        <div class="modal fade" id="modalEditPersona{{ $persona->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center" style="background-color: var(--app-primary-dark);">
                        <h5 class="modal-title fw-black mb-0"><i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar Expediente #{{ $persona->id }}</h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.personas.update', $persona) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body modal-body-custom">
                            @include('admin.personas.partials.form', ['persona' => $persona])
                        </div>

                        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm"><i class="bi bi-arrow-repeat me-2"></i>Actualizar Datos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Búsqueda en tiempo real
        function filterPersonas() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.persona-row');

            rows.forEach(row => {
                let name = row.querySelector('.persona-name').textContent.toLowerCase();
                if (name.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            // FIX: EVITAR QUE LA PANTALLA SE CONGELE CON LOS MODALES
            document.querySelectorAll('.modal').forEach(modal => {
                document.body.appendChild(modal);
            });

            // ALERTA MODERNIZADA PARA ELIMINAR (SweetAlert2)
            document.querySelectorAll('.form-eliminar-persona').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const currentForm = this;
                    const isDark = document.body.classList.contains('theme-dark') || document.body.classList.contains('theme-system') && window.matchMedia('(prefers-color-scheme: dark)').matches;

                    Swal.fire({
                        title: '¿Eliminar persona?',
                        text: "Esta acción borrará permanentemente sus datos demográficos del sistema. No se puede deshacer.",
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#ef4444',
                        cancelButtonColor: isDark ? '#334155' : '#e2e8f0',
                        confirmButtonText: '<i class="bi bi-trash-fill me-1"></i> Sí, eliminar',
                        cancelButtonText: 'Cancelar',
                        background: isDark ? '#1e293b' : '#ffffff',
                        color: isDark ? '#f8fafc' : '#1e293b',
                        customClass: {
                            popup: 'rounded-4 shadow-lg border border-secondary border-opacity-10',
                            confirmButton: 'btn btn-danger rounded-pill px-4 fw-bold shadow-sm',
                            cancelButton: 'btn rounded-pill px-4 fw-bold ms-2 ' + (isDark ? 'text-white btn-outline-secondary' : 'text-dark btn-light')
                        },
                        buttonsStyling: false
                    }).then((result) => {
                        if (result.isConfirmed) {
                            currentForm.submit();
                        }
                    });
                });
            });
        });
    </script>
@endpush
