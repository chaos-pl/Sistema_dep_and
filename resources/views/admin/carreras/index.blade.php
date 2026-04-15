@extends('layouts.app')

@section('title', 'Carreras - PROMETEO')
@section('page-title', 'Directorio de Carreras')
@section('page-subtitle', 'Gestión administrativa de carreras académicas')

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
                    <i class="bi bi-mortarboard-fill text-primary me-2"></i>Carreras Registradas
                </h4>
                <p class="text-body-secondary mb-0">Administra los programas educativos de la institución.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text"
                           id="searchInput"
                           class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo"
                           placeholder="Buscar carrera..."
                           onkeyup="filterCarreras()">
                </div>

                @can('carreras.crear')
                    <button type="button"
                            class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCreateCarrera"
                            style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-bookmark-plus-fill me-2"></i> Nueva Carrera
                    </button>
                @endcan
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="carrerasTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Nombre de la Carrera</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Grupos Asignados</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($carreras as $carrera)
                    <tr class="carrera-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                     style="width: 40px; height: 40px;">
                                    <i class="bi bi-mortarboard-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body carrera-name">{{ $carrera->nombre }}</h6>
                                    <small class="text-body-secondary">ID: #{{ $carrera->id }}</small>
                                </div>
                            </div>
                        </td>

                        <td class="text-center py-3 border-0">
                                <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold shadow-sm">
                                    <i class="bi bi-people-fill me-1"></i> {{ $carrera->grupos_count ?? 0 }}
                                </span>
                        </td>

                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalShowCarrera{{ $carrera->id }}"
                                        class="btn btn-sm btn-light border text-info rounded-circle shadow-sm"
                                        style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                        title="Ver">
                                    <i class="bi bi-eye-fill"></i>
                                </button>

                                @can('carreras.editar')
                                    <button type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditCarrera{{ $carrera->id }}"
                                            class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm"
                                            style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                            title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                @endcan

                                @can('carreras.eliminar')
                                    <form action="{{ route('admin.carreras.destroy', $carrera) }}"
                                          method="POST"
                                          class="d-inline m-0 form-eliminar-carrera">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm"
                                                style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                                title="Eliminar">
                                            <i class="bi bi-trash-fill"></i>
                                        </button>
                                    </form>
                                @endcan
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-mortarboard fs-1 opacity-25 mb-3"></i>
                                <span>No hay carreras registradas en la institución.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $carreras->links() }}
        </div>
    </div>

    @can('carreras.crear')
        <div class="modal fade" id="modalCreateCarrera" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-black mb-0">
                            <i class="bi bi-bookmark-plus-fill me-2"></i>Registrar Nueva Carrera
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.carreras.store') }}" method="POST">
                        @csrf
                        <div class="modal-body modal-body-custom">
                            <input type="hidden" name="modal_type" value="create">

                            <div class="row g-4">
                                <div class="col-12">
                                    <label class="form-label fw-bold text-body-secondary">Nombre de la carrera</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                                            <i class="bi bi-mortarboard-fill"></i>
                                        </span>
                                        <input type="text"
                                               name="nombre"
                                               value="{{ old('modal_type') == 'create' ? old('nombre') : '' }}"
                                               class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if(old('modal_type') == 'create') @error('nombre') is-invalid @enderror @endif"
                                               placeholder="Ej. Ingeniería en Sistemas Computacionales"
                                               required>
                                    </div>
                                    @if(old('modal_type') == 'create')
                                        @error('nombre')
                                        <small class="text-danger fw-bold mt-1 d-block">
                                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                        </small>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-floppy-fill me-2"></i>Guardar Registro
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @foreach($carreras as $carrera)
        <div class="modal fade" id="modalShowCarrera{{ $carrera->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-black mb-0">
                            <i class="bi bi-eye-fill me-2"></i>Detalle de Carrera
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body modal-body-custom">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">ID</small>
                                    <div class="fw-bold text-body">#{{ $carrera->id }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Grupos asignados</small>
                                    <div class="fw-bold text-body">{{ $carrera->grupos_count ?? 0 }}</div>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="bg-body-tertiary rounded-4 p-3">
                                    <small class="text-body-secondary d-block mb-1">Nombre de la carrera</small>
                                    <div class="fw-bold text-body">{{ $carrera->nombre }}</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Fecha de creación</small>
                                    <div class="fw-bold text-body">
                                        {{ $carrera->created_at ? $carrera->created_at->format('d/m/Y h:i A') : 'Sin registro' }}
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                    <small class="text-body-secondary d-block mb-1">Última actualización</small>
                                    <div class="fw-bold text-body">
                                        {{ $carrera->updated_at ? $carrera->updated_at->format('d/m/Y h:i A') : 'Sin registro' }}
                                    </div>
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

        @can('carreras.editar')
            <div class="modal fade" id="modalEditCarrera{{ $carrera->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center" style="background-color: var(--app-primary-dark);">
                            <h5 class="modal-title fw-black mb-0">
                                <i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar Carrera #{{ $carrera->id }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('admin.carreras.update', $carrera) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body modal-body-custom">
                                <input type="hidden" name="modal_type" value="edit">
                                <input type="hidden" name="modal_id" value="{{ $carrera->id }}">

                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label fw-bold text-body-secondary">Nombre de la carrera</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-body-tertiary border-end-0 text-body-secondary">
                                                <i class="bi bi-mortarboard-fill"></i>
                                            </span>
                                            <input type="text"
                                                   name="nombre"
                                                   value="{{ old('modal_type') == 'edit' && old('modal_id') == $carrera->id ? old('nombre') : $carrera->nombre }}"
                                                   class="form-control form-control-lg bg-body-tertiary border-start-0 ps-0 @if(old('modal_type') == 'edit' && old('modal_id') == $carrera->id) @error('nombre') is-invalid @enderror @endif"
                                                   placeholder="Ej. Ingeniería en Sistemas Computacionales"
                                                   required>
                                        </div>
                                        @if(old('modal_type') == 'edit' && old('modal_id') == $carrera->id)
                                            @error('nombre')
                                            <small class="text-danger fw-bold mt-1 d-block">
                                                <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                            </small>
                                            @enderror
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                                <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                                    Cancelar
                                </button>
                                <button type="submit" class="btn btn-warning text-dark rounded-pill px-4 fw-bold shadow-sm">
                                    <i class="bi bi-arrow-repeat me-2"></i>Actualizar Datos
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endcan
    @endforeach
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function filterCarreras() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.carrera-row');

            rows.forEach(row => {
                let name = row.querySelector('.carrera-name').textContent.toLowerCase();
                row.style.display = name.includes(filter) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.modal').forEach(modal => {
                document.body.appendChild(modal);
            });

            document.querySelectorAll('.form-eliminar-carrera').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const currentForm = this;
                    const isDark =
                        document.body.classList.contains('theme-dark') ||
                        (document.body.classList.contains('theme-system') &&
                            window.matchMedia('(prefers-color-scheme: dark)').matches);

                    Swal.fire({
                        title: '¿Eliminar carrera?',
                        text: "Esta acción eliminará el registro de la carrera del sistema. No se puede deshacer.",
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
                    const modal = new bootstrap.Modal(document.getElementById('modalCreateCarrera'));
                    modal.show();
                }

                if (modalType === 'edit' && modalId) {
                    const modalElement = document.getElementById('modalEditCarrera' + modalId);
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                }
            }
        });
    </script>
@endpush
