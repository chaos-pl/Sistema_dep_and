@extends('layouts.app')

@section('title', 'Estudiantes - PROMETEO')
@section('page-title', 'Directorio de Estudiantes')
@section('page-subtitle', 'Gestión de asignación de grupos de los estudiantes')

@push('styles')
    <style>
        .search-input-prometeo {
            padding-left: 2.8rem !important;
        }
        .hover-elevate { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .hover-elevate:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important; }

        .modal-content {
            border-radius: 1.5rem;
            overflow: hidden;
            border: 0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }

        .modal-header-custom {
            background-color: var(--app-primary-dark);
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
    <div class="app-card p-4 p-md-5 mb-4 border border-secondary border-opacity-10 shadow-sm rounded-4">

        <div class="row align-items-center justify-content-between mb-5 gy-3">
            <div class="col-lg-5">
                <h4 class="fw-black text-body mb-1">
                    <i class="bi bi-people-fill text-primary me-2"></i>Asignación de Grupos
                </h4>
                <p class="text-body-secondary mb-0">Gestiona a qué grupo pertenece cada estudiante.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text"
                           id="searchInput"
                           class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo"
                           placeholder="Buscar por nombre, matrícula o grupo..."
                           onkeyup="filterEstudiantes()">
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="table-responsive">
            <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10" id="estudiantesTable">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Estudiante</th>
                    <th class="py-3 text-body-secondary fw-bold border-0 text-center">Matrícula</th>
                    <th class="py-3 text-body-secondary fw-bold text-center border-0">Grupo Actual</th>
                    <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Asignación</th>
                </tr>
                </thead>
                <tbody class="border-top-0">
                @forelse($estudiantes as $estudiante)
                    <tr class="estudiante-row border-bottom border-secondary border-opacity-10">
                        <td class="px-4 py-3 border-0">
                            <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body est-name">
                                        {{ $estudiante->persona->nombre ?? '' }} {{ $estudiante->persona->apellido_paterno ?? '' }} {{ $estudiante->persona->apellido_materno ?? '' }}
                                    </h6>
                                    <small class="text-body-secondary">
                                        {{ $estudiante->persona->user->email ?? 'Sin correo' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td class="text-center py-3 border-0 fw-bold text-body-secondary est-mat">
                            {{ $estudiante->matricula ?? 'Sin matrícula' }}
                        </td>

                        <td class="text-center py-3 border-0 est-grupo">
                            @if($estudiante->grupo)
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm">
                                    {{ $estudiante->grupo->nombre }}
                                </span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 fw-bold shadow-sm">
                                    Sin grupo
                                </span>
                            @endif
                        </td>

                        <td class="text-end px-4 py-3 border-0">
                            <button type="button"
                                    data-bs-toggle="modal"
                                    data-bs-target="#modalEditGrupo{{ $estudiante->id }}"
                                    class="btn btn-sm btn-light border text-primary rounded-pill shadow-sm px-3 hover-elevate d-inline-flex align-items-center"
                                    title="Cambiar u asignar grupo">
                                <i class="bi bi-arrow-left-right me-1"></i> Asignar
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-body-secondary border-0">
                            <div class="d-flex flex-column align-items-center">
                                <i class="bi bi-people fs-1 opacity-25 mb-3"></i>
                                <span>No hay estudiantes registrados o disponibles en el sistema.</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $estudiantes->links() }}
        </div>
    </div>

    @foreach($estudiantes as $estudiante)
        <div class="modal fade" id="modalEditGrupo{{ $estudiante->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header modal-header-custom d-flex justify-content-between align-items-center">
                        <h5 class="modal-title fw-black mb-0">
                            <i class="bi bi-collection-fill me-2 text-warning"></i>Asignación de Grupo
                        </h5>
                        <button type="button" class="btn-close btn-close-white shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <form action="{{ route('admin.estudiantes.updateGrupo', $estudiante) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-body modal-body-custom text-start align-items-center">
                            
                            <div class="text-center mb-4">
                                <h6 class="fw-bold mb-1">{{ $estudiante->persona->nombre ?? '' }} {{ $estudiante->persona->apellido_paterno ?? '' }}</h6>
                                <p class="text-body-secondary small mb-0">{{ $estudiante->matricula ?? 'Sin matrícula' }}</p>
                            </div>

                            <label class="form-label fw-bold text-body">Selecciona el nuevo grupo</label>
                            
                            <!-- Búsqueda interna para filtro fácil -->
                            <div class="form-floating mb-3">
                                <select name="grupo_id" class="form-select border-secondary border-opacity-25 shadow-sm" required>
                                    <option value="" {{ !$estudiante->grupo_id ? 'selected' : '' }}>-- Ninguno (Quitar del grupo actual) --</option>
                                    @foreach($grupos as $grupo)
                                        <option value="{{ $grupo->id }}" {{ $estudiante->grupo_id == $grupo->id ? 'selected' : '' }}>
                                            {{ $grupo->nombre }} ({{ $grupo->periodo }}) - {{ $grupo->carrera->nombre ?? 'Sin carrera' }}
                                        </option>
                                    @endforeach
                                </select>
                                <label><i class="bi bi-folder me-1"></i>Grupo destino</label>
                            </div>
                            
                            <div class="alert alert-info bg-info bg-opacity-10 border border-info border-opacity-25 rounded-4 d-flex gap-3 mb-0">
                                <i class="bi bi-info-circle-fill text-info mt-1"></i>
                                <span class="small font-medium text-body">Al seleccionar "Ninguno", el estudiante perderá el acceso a las evaluaciones que se administran desde su grupo. Sin embargo, no se perderá su historial individual.</span>
                            </div>

                        </div>

                        <div class="modal-footer modal-footer-custom d-flex justify-content-end gap-2">
                            <button type="button" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm" data-bs-dismiss="modal">
                                Cancelar
                            </button>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-2"></i>Guardar Asignación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach
@endsection

@push('scripts')
    <script>
        function filterEstudiantes() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.estudiante-row');

            rows.forEach(row => {
                let name = row.querySelector('.est-name')?.textContent.toLowerCase() || '';
                let matricula = row.querySelector('.est-mat')?.textContent.toLowerCase() || '';
                let grupo = row.querySelector('.est-grupo')?.textContent.toLowerCase() || '';

                row.style.display = (name.includes(filter) || matricula.includes(filter) || grupo.includes(filter)) ? '' : 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.modal').forEach(modal => {
                document.body.appendChild(modal);
            });
        });
    </script>
@endpush
