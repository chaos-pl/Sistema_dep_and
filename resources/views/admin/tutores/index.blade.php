@extends('layouts.app')

@section('title', 'Tutores - PROMETEO')
@section('page-title', 'Directorio de Tutores')
@section('page-subtitle', 'Gestión de tutores e instructores académicos')

@push('styles')
    <style>
        .search-input-prometeo { padding-left: 2.8rem !important; }
    </style>
@endpush

@section('content')
    <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

        <div class="row align-items-center justify-content-between mb-5 gy-3">
            <div class="col-lg-5">
                <h4 class="fw-black text-body mb-1"><i class="bi bi-person-video3 text-primary me-2"></i>Tutores Registrados</h4>
                <p class="text-body-secondary mb-0">Directorio y asignaciones de tutores.</p>
            </div>

            <div class="col-lg-7 d-flex flex-column flex-sm-row justify-content-lg-end gap-3">
                <div class="position-relative w-100" style="max-width: 320px;">
                    <div class="position-absolute top-50 start-0 translate-middle-y ms-3 text-primary z-2">
                        <i class="bi bi-search"></i>
                    </div>
                    <input type="text" id="searchInput" class="form-control bg-body-tertiary rounded-pill border-0 shadow-sm search-input-prometeo" placeholder="Buscar tutor..." onkeyup="filterTutores()">
                </div>

                <a href="{{ route('admin.tutores.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                    <i class="bi bi-person-plus-fill me-2"></i> Nuevo Tutor
                </a>
            </div>
        </div>

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
                            <div class="d-flex align-items-center gap-3" style="transition: transform 0.2s ease;">
                                <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                    <i class="bi bi-person-video3"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0 text-body tutor-name">
                                        {{ $tutor->persona->nombre ?? '' }} {{ $tutor->persona->apellido_paterno ?? '' }}
                                    </h6>
                                    <small class="text-body-secondary">ID: #{{ $tutor->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3 border-0">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope-at text-primary"></i>
                                <span class="fw-medium text-body tutor-email">{{ $tutor->persona->user->email ?? 'Sin correo' }}</span>
                            </div>
                        </td>
                        <td class="text-center py-3 border-0 text-body-secondary fw-bold">
                            {{ $tutor->numero_empleado }}
                        </td>
                        <td class="text-center py-3 border-0">
                            <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-1 fw-bold">
                                {{ $tutor->grupos_count ?? 0 }}
                            </span>
                        </td>
                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.tutores.show', $tutor->id) }}" class="btn btn-sm btn-light border text-info rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Ver Detalle">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                <a href="{{ route('admin.tutores.edit', $tutor->id) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                <form action="{{ route('admin.tutores.destroy', $tutor->id) }}" method="POST" onsubmit="return confirm('ATENCIÓN: ¿Deseas eliminar permanentemente este tutor?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-light border text-danger rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Eliminar">
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
@endsection

@push('scripts')
    <script>
        function filterTutores() {
            let input = document.getElementById('searchInput');
            let filter = input.value.toLowerCase();
            let rows = document.querySelectorAll('.tutor-row');

            rows.forEach(row => {
                let name = row.querySelector('.tutor-name').textContent.toLowerCase();
                let email = row.querySelector('.tutor-email').textContent.toLowerCase();
                if (name.includes(filter) || email.includes(filter)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }
    </script>
@endpush
