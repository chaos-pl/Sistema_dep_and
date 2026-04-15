@extends('layouts.app')

@section('title', 'Grupos - PROMETEO')
@section('page-title', 'Directorio de Grupos')
@section('page-subtitle', 'Gestión administrativa de grupos académicos')

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
                    <i class="bi bi-collection-fill text-primary me-2"></i>Grupos Registrados
                </h4>
                <p class="text-body-secondary mb-0">Administra y asigna tutores a los grupos de la institución.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text"
                           id="searchInput"
                           class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo"
                           placeholder="Buscar grupo, tutor o carrera..."
                           onkeyup="filterGrupos()">
                </div>

                @can('grupos.crear')
                    <button type="button"
                            class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4"
                            data-bs-toggle="modal"
                            data-bs-target="#modalCreateGrupo"
                            style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-folder-plus me-2"></i> Nuevo Grupo
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
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="gruposTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Grupo y Periodo</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Carrera</th>
                    <th class="py-3 text-body-secondary fw-bold border-0">Tutor Asignado</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Estudiantes</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acciones</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($grupos as $grupo)
                    <tr class="grupo-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="bi bi-collection-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body grupo-name">{{ $grupo->nombre }}</h6>
                                    <span class="badge bg-body-tertiary text-body-secondary border rounded-pill mt-1 grupo-periodo">
                                        {{ $grupo->periodo }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-3 border-0">
                            <span class="fw-medium text-body-secondary grupo-carrera">
                                <i class="bi bi-mortarboard me-1 opacity-50"></i>
                                {{ $grupo->carrera->nombre ?? 'Sin carrera' }}
                            </span>
                        </td>

                        <td class="py-3 border-0">
                            @if($grupo->tutor)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-video3 text-info"></i>
                                    <span class="fw-medium text-body grupo-tutor">
                                        {{ $grupo->tutor->persona->nombre ?? '' }}
                                        {{ $grupo->tutor->persona->apellido_paterno ?? '' }}
                                    </span>
                                </div>
                            @else
                                <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-medium grupo-tutor">
                                    <i class="bi bi-person-x"></i> Sin tutor
                                </span>
                            @endif
                        </td>

                        <td class="text-center py-3 border-0">
                            <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold shadow-sm">
                                <i class="bi bi-people-fill me-1"></i> {{ $grupo->estudiantes_count ?? 0 }}
                            </span>
                        </td>

                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                @can('grupos.ver')
                                    <button type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalShowGrupo{{ $grupo->id }}"
                                            class="btn btn-sm btn-light border text-info rounded-circle shadow-sm"
                                            style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                            title="Ver Detalle">
                                        <i class="bi bi-eye-fill"></i>
                                    </button>
                                @endcan

                                @can('grupos.editar')
                                    <button type="button"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalEditGrupo{{ $grupo->id }}"
                                            class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm"
                                            style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;"
                                            title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button>
                                @endcan

                                @can('grupos.eliminar')
                                    <form action="{{ route('admin.grupos.destroy', $grupo) }}"
                                          method="POST"
                                          class="d-inline m-0 form-eliminar-grupo">
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
                        <td colspan="5" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-collection fs-1 opacity-25 mb-3"></i>
                                <span>No hay grupos registrados en la institución.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $grupos->links() }}
        </div>
    </div>

    @can('grupos.crear')
        <div class="modal fade" id="modalCreateGrupo" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-black mb-0">
                            <i class="bi bi-folder-plus me-2"></i>Registrar Nuevo Grupo
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.grupos.store') }}" method="POST">
                        @include('admin.grupos.partials.form', [
                            'grupo' => new \App\Models\Grupo(),
                            'submitText' => 'Guardar Grupo',
                            'method' => 'POST',
                            'isModal' => true,
                            'modalType' => 'create'
                        ])
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @foreach($grupos as $grupo)
        @can('grupos.ver')
            <div class="modal fade" id="modalShowGrupo{{ $grupo->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
                    <div class="modal-content">
                        <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                            <h5 class="modal-title fw-black mb-0">
                                <i class="bi bi-eye-fill me-2"></i>Detalle del Grupo
                            </h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body modal-body-custom">
                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                        <small class="text-body-secondary d-block mb-1">Grupo</small>
                                        <div class="fw-bold text-body">{{ $grupo->nombre }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                        <small class="text-body-secondary d-block mb-1">Periodo</small>
                                        <div class="fw-bold text-body">{{ $grupo->periodo }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                        <small class="text-body-secondary d-block mb-1">Carrera</small>
                                        <div class="fw-bold text-body">{{ $grupo->carrera->nombre ?? 'Sin carrera' }}</div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                        <small class="text-body-secondary d-block mb-1">Integrantes</small>
                                        <div class="fw-bold text-body">{{ $grupo->estudiantes_count ?? 0 }}</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                        <small class="text-body-secondary d-block mb-1">Tutor asignado</small>
                                        <div class="fw-bold text-body">
                                            @if($grupo->tutor)
                                                {{ $grupo->tutor->persona->nombre ?? '' }}
                                                {{ $grupo->tutor->persona->apellido_paterno ?? '' }}
                                                {{ $grupo->tutor->persona->apellido_materno ?? '' }}
                                            @else
                                                Sin tutor asignado
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                        <small class="text-body-secondary d-block mb-1">Fecha de creación</small>
                                        <div class="fw-bold text-body">
                                            {{ $grupo->created_at ? $grupo->created_at->format('d/m/Y h:i A') : 'Sin registro' }}
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="bg-body-tertiary rounded-4 p-3 h-100">
                                        <small class="text-body-secondary d-block mb-1">Última actualización</small>
                                        <div class="fw-bold text-body">
                                            {{ $grupo->updated_at ? $grupo->updated_at->format('d/m/Y h:i A') : 'Sin registro' }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-body-tertiary rounded-4 p-3">
                                <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
                                    <h6 class="fw-bold text-body mb-0">
                                        <i class="bi bi-people-fill text-primary me-2"></i>Integrantes del grupo
                                    </h6>
                                    <span class="badge bg-body text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold">
                                        {{ $grupo->estudiantes_count ?? 0 }} estudiantes
                                    </span>
                                </div>

                                <div class="table-responsive">
                                    <table class="table align-middle mb-0">
                                        <thead class="bg-body">
                                        <tr>
                                            <th class="border-0 text-body-secondary">Nombre completo</th>
                                            <th class="border-0 text-body-secondary">Matrícula</th>
                                            <th class="border-0 text-body-secondary">Correo</th>
                                            <th class="border-0 text-body-secondary text-center">Código</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @forelse($grupo->estudiantes as $estudiante)
                                            <tr>
                                                <td class="border-0 fw-semibold text-body">
                                                    {{ $estudiante->persona->nombre ?? '' }}
                                                    {{ $estudiante->persona->apellido_paterno ?? '' }}
                                                    {{ $estudiante->persona->apellido_materno ?? '' }}
                                                </td>
                                                <td class="border-0 text-body-secondary">
                                                    {{ $estudiante->matricula ?? 'Sin matrícula' }}
                                                </td>
                                                <td class="border-0 text-body-secondary">
                                                    {{ $estudiante->persona->user->email ?? 'Sin correo' }}
                                                </td>
                                                <td class="border-0 text-center">
                                                    <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-bold">
                                                        {{ $estudiante->codigo_anonimo ?? 'N/D' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-4 text-body-secondary border-0">
                                                    Este grupo todavía no tiene integrantes registrados.
                                                </td>
                                            </tr>
                                        @endforelse
                                        </tbody>
                                    </table>
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
        @endcan

        @can('grupos.editar')
            <div class="modal fade" id="modalEditGrupo{{ $grupo->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
                    <div class="modal-content">
                        <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center" style="background-color: var(--app-primary-dark);">
                            <h5 class="modal-title fw-black mb-0">
                                <i class="bi bi-pencil-square me-2 text-warning"></i>Actualizar Grupo #{{ $grupo->id }}
                            </h5>
                            <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <form action="{{ route('admin.grupos.update', $grupo) }}" method="POST">
                            @include('admin.grupos.partials.form', [
                                'grupo' => $grupo,
                                'submitText' => 'Actualizar Grupo',
                                'method' => 'PUT',
                                'isModal' => true,
                                'modalType' => 'edit',
                                'modalId' => $grupo->id
                            ])
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
        function filterGrupos() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.grupo-row');

            rows.forEach(row => {
                let name = row.querySelector('.grupo-name')?.textContent.toLowerCase() || '';
                let periodo = row.querySelector('.grupo-periodo')?.textContent.toLowerCase() || '';
                let carrera = row.querySelector('.grupo-carrera')?.textContent.toLowerCase() || '';
                let tutor = row.querySelector('.grupo-tutor')?.textContent.toLowerCase() || '';

                row.style.display = (name.includes(filter) || periodo.includes(filter) || carrera.includes(filter) || tutor.includes(filter)) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.modal').forEach(modal => {
                document.body.appendChild(modal);
            });

            document.querySelectorAll('.form-eliminar-grupo').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    const currentForm = this;
                    const isDark =
                        document.body.classList.contains('theme-dark') ||
                        (document.body.classList.contains('theme-system') &&
                            window.matchMedia('(prefers-color-scheme: dark)').matches);

                    Swal.fire({
                        title: '¿Eliminar grupo?',
                        text: "Esta acción eliminará el registro del grupo del sistema. No se puede deshacer.",
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
                    const modalElement = document.getElementById('modalCreateGrupo');
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                }

                if (modalType === 'edit' && modalId) {
                    const modalElement = document.getElementById('modalEditGrupo' + modalId);
                    if (modalElement) {
                        const modal = new bootstrap.Modal(modalElement);
                        modal.show();
                    }
                }
            }
        });
    </script>
@endpush
