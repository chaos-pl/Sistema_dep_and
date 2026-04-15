@extends('layouts.app')

@section('title', 'Tutores - PROMETEO')
@section('page-title', 'Directorio de Tutores')
@section('page-subtitle', 'Gestión de tutores e instructores académicos')

@push('styles')
    <style>
        .search-input-prometeo {
            padding-left: 2.8rem !important;
        }

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

        body.theme-dark .modal-content,
        body.theme-system .modal-content {
            background-color: #1e293b !important;
            border: 1px solid rgba(255,255,255,0.1);
        }

        body.theme-dark .modal-body-custom,
        body.theme-system .modal-body-custom {
            background-color: #0f172a !important;
        }

        body.theme-dark .modal-footer-custom,
        body.theme-system .modal-footer-custom {
            background-color: #1e293b !important;
            border-color: rgba(255,255,255,0.1) !important;
        }

        .modal.fade {
            perspective: 2000px;
        }

        .modal.fade .modal-dialog {
            opacity: 0;
            transform-origin: center center;
            transform: translateZ(-500px) rotateY(90deg) scale(0.5);
            transition:
                transform 0.7s cubic-bezier(0.165, 0.84, 0.44, 1),
                opacity 0.4s ease-in-out;
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
    <div class="app-card p-4 p-md-5 mb-4">

        <div class="row align-items-center justify-content-between mb-5 gy-3">
            <div class="col-lg-5">
                <h4 class="fw-black text-body mb-1">
                    <i class="bi bi-person-video3 text-primary me-2"></i>Tutores Registrados
                </h4>
                <p class="text-body-secondary mb-0">Directorio y asignaciones de tutores.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text"
                           id="searchInput"
                           class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo"
                           placeholder="Buscar tutor..."
                           onkeyup="filterTutores()">
                </div>

                <button type="button"
                        class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4"
                        data-bs-toggle="modal"
                        data-bs-target="#modalCreateTutor"
                        style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                    <i class="bi bi-person-plus-fill me-2"></i> Nuevo Tutor
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="tutoresTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Tutor</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Contacto Institucional</th>
                    <th class="py-3 text-body-secondary fw-bold border-0 text-center">Nº Empleado</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Grupos</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($tutores as $tutor)
                    <tr class="tutor-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-video3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body tutor-name">
                                        {{ $tutor->persona?->nombre ?? '' }} {{ $tutor->persona?->apellido_paterno ?? '' }}
                                    </h6>
                                    <small class="text-body-secondary">ID: #{{ $tutor->id }}</small>
                                </div>
                            </div>
                        </td>

                        <td class="py-3 border-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope-at text-primary"></i>
                                <span class="fw-medium text-body tutor-email">{{ $tutor->persona?->user?->email ?? 'Sin correo' }}</span>
                            </div>
                        </td>

                        <td class="text-center py-3 border-0 text-body-secondary fw-bold tutor-empleado">
                            {{ $tutor->numero_empleado }}
                        </td>

                        <td class="text-center py-3 border-0">
                            <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm">
                                {{ $tutor->grupos_count ?? 0 }}
                            </span>
                        </td>

                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalShowTutor{{ $tutor->id }}"
                                        class="btn btn-sm btn-light border text-info rounded-circle shadow-sm"
                                        style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                        title="Ver Detalle">
                                    <i class="bi bi-eye-fill"></i>
                                </button>

                                <button type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditTutor{{ $tutor->id }}"
                                        class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm"
                                        style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                        title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </button>

                                <form action="{{ route('admin.tutores.destroy', $tutor) }}"
                                      method="POST"
                                      class="d-inline m-0 form-eliminar-tutor">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                            class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm"
                                            style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                            title="Eliminar">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-person-video3 fs-1 opacity-25 mb-3"></i>
                                <span>No hay tutores registrados en el sistema.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $tutores->links() }}
        </div>
    </div>

    <div class="modal fade" id="modalCreateTutor" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                    <h5 class="modal-title fw-black mb-0">
                        <i class="bi bi-person-plus-fill me-2"></i>Registrar Nuevo Tutor
                    </h5>
                    <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <form action="{{ route('admin.tutores.store') }}" method="POST">
                    @include('admin.tutores.partials.form', [
                        'tutor' => new \App\Models\Tutor(),
                        'submitText' => 'Guardar Tutor',
                        'method' => 'POST',
                        'isModal' => true,
                        'modalType' => 'create'
                    ])
                </form>
            </div>
        </div>
    </div>

    @foreach($tutores as $tutor)
        <div class="modal fade" id="modalShowTutor{{ $tutor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-black mb-0">
                            <i class="bi bi-eye-fill me-2"></i>Detalle de Tutor
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body modal-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Nombre completo</small>
                                    <div class="fw-bold text-body">
                                        {{ $tutor->persona?->nombre ?? '' }}
                                        {{ $tutor->persona?->apellido_paterno ?? '' }}
                                        {{ $tutor->persona?->apellido_materno ?? '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Número de empleado</small>
                                    <div class="fw-bold text-body">{{ $tutor->numero_empleado }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Correo institucional</small>
                                    <div class="fw-bold text-body">{{ $tutor->persona?->user?->email ?? 'No asignado' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Teléfono</small>
                                    <div class="fw-bold text-body">{{ $tutor->persona?->telefono ?? 'No especificado' }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Fecha de nacimiento</small>
                                    <div class="fw-bold text-body">
                                        {{ $tutor->persona?->fecha_nacimiento ? \Carbon\Carbon::parse($tutor->persona->fecha_nacimiento)->format('d/m/Y') : 'No especificada' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Género</small>
                                    <div class="fw-bold text-body">{{ ucfirst($tutor->persona?->genero ?? 'No especificado') }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="bg-body-tertiary rounded-4 p-4 text-center border border-secondary border-opacity-10">
                                    <div class="display-6 fw-black text-primary mb-2">{{ $tutor->grupos_count ?? 0 }}</div>
                                    <p class="text-body-secondary mb-0 fw-medium">Grupos actualmente gestionados por este tutor.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                            Cerrar
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modalEditTutor{{ $tutor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center" style="background-color: var(--app-primary-dark);">
                        <h5 class="modal-title fw-black mb-0">
                            <i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar Tutor #{{ $tutor->id }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.tutores.update', $tutor) }}" method="POST">
                        @include('admin.tutores.partials.form', [
                            'tutor' => $tutor,
                            'submitText' => 'Actualizar Expediente',
                            'method' => 'PUT',
                            'isModal' => true,
                            'modalType' => 'edit',
                            'modalId' => $tutor->id
                        ])
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function filterTutores() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.tutor-row');

            rows.forEach(row => {
                let name = row.querySelector('.tutor-name')?.textContent.toLowerCase() || '';
                let email = row.querySelector('.tutor-email')?.textContent.toLowerCase() || '';
                let empleado = row.querySelector('.tutor-empleado')?.textContent.toLowerCase() || '';

                row.style.display = (name.includes(filter) || email.includes(filter) || empleado.includes(filter)) ? '' : 'none';
            });
        }

        function togglePasswordVisibility(inputId, iconId) {
            const input = document.getElementById(inputId);
            const icon = document.getElementById(iconId);

            if (!input || !icon) return;

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
                icon.style.color = 'var(--app-primary)';
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
                icon.style.color = '';
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.modal').forEach(modal => {
                document.body.appendChild(modal);
            });

            document.querySelectorAll('.form-eliminar-tutor').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const currentForm = this;
                    const isDark =
                        document.body.classList.contains('theme-dark') ||
                        (document.body.classList.contains('theme-system') &&
                            window.matchMedia('(prefers-color-scheme: dark)').matches);

                    Swal.fire({
                        title: '¿Eliminar tutor?',
                        text: "Esta acción eliminará permanentemente el tutor del sistema. No se puede deshacer.",
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

            const hasErrors = @json($errors->any());
            const modalType = @json(old('modal_type'));
            const modalId = @json(old('modal_id'));

            if (hasErrors) {
                if (modalType === 'create') {
                    const modalElement = document.getElementById('modalCreateTutor');
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                }

                if (modalType === 'edit' && modalId) {
                    const modalElement = document.getElementById('modalEditTutor' + modalId);
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                }
            }
        });
    </script>
@endpush
