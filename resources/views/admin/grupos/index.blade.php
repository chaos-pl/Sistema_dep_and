@extends('layouts.app')

@section('title', 'Grupos - PROMETEO')
@section('page-title', 'Directorio de Grupos')
@section('page-subtitle', 'Gestión administrativa de grupos académicos')

@push('styles')
    <style>
        .search-input-prometeo { padding-left: 2.8rem !important; }
    </style>
@endpush

@section('content')
    <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

        <div class="row align-items-center justify-content-between mb-5 gy-3">
            <div class="col-lg-5">
                <h4 class="fw-black text-body mb-1"><i class="bi bi-collection-fill text-primary me-2"></i>Grupos Registrados</h4>
                <p class="text-body-secondary mb-0">Administra y asigna tutores a los grupos de la institución.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar grupo, tutor o carrera..." onkeyup="filterGrupos()">
                </div>

                @can('grupos.crear')
                    <a href="{{ route('admin.grupos.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-folder-plus me-2"></i> Nuevo Grupo
                    </a>
                @endcan
            </div>
        </div>

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
                            <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-collection-fill"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body grupo-name">{{ $grupo->nombre }}</h6>
                                    <span class="badge bg-body-tertiary text-body-secondary border rounded-pill mt-1 grupo-periodo">{{ $grupo->periodo }}</span>
                                </div>
                            </div>
                        </td>

                        <td class="py-3 border-0">
                            <span class="fw-medium text-body-secondary grupo-carrera"><i class="bi bi-mortarboard me-1 opacity-50"></i> {{ $grupo->carrera->nombre ?? 'Sin carrera' }}</span>
                        </td>

                        <td class="py-3 border-0">
                            @if($grupo->tutor)
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-person-video3 text-info"></i>
                                    <span class="fw-medium text-body grupo-tutor">
                                        {{ $grupo->tutor->persona->nombre ?? '' }} {{ $grupo->tutor->persona->apellido_paterno ?? '' }}
                                    </span>
                                </div>
                            @else
                                <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 fw-medium grupo-tutor">
                                    <i class="bi bi-person-x"></i> Sin tutor
                                </span>
                            @endif
                        </td>

                        <td class="text-center py-3 border-0">
                            <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-semibold">
                                <i class="bi bi-people-fill me-1"></i> {{ $grupo->estudiantes_count ?? 0 }}
                            </span>
                        </td>

                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">

                                @can('grupos.ver')
                                    <a href="{{ route('admin.grupos.show', $grupo->id) }}" class="btn btn-sm btn-light border text-info rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Ver Detalle">
                                        <i class="bi bi-eye-fill"></i>
                                    </a>
                                @endcan

                                @can('grupos.editar')
                                    <a href="{{ route('admin.grupos.edit', $grupo->id) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                @endcan

                                @can('grupos.eliminar')
                                    <form action="{{ route('admin.grupos.destroy', $grupo->id) }}"
                                          method="POST"
                                          onsubmit="return confirm('ATENCIÓN: ¿Seguro que deseas eliminar este grupo?');">
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
@endsection

@push('scripts')
    <script>
        function filterGrupos() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.grupo-row');

            rows.forEach(row => {
                let name = row.querySelector('.grupo-name').textContent.toLowerCase();
                let periodo = row.querySelector('.grupo-periodo').textContent.toLowerCase();
                let carrera = row.querySelector('.grupo-carrera').textContent.toLowerCase();
                let tutor = row.querySelector('.grupo-tutor').textContent.toLowerCase();

                if (name.includes(filter) || periodo.includes(filter) || carrera.includes(filter) || tutor.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
@endpush
