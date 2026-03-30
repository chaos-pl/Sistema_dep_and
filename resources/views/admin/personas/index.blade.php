@extends('layouts.app')

@section('title', 'Personas - PROMETEO')
@section('page-title', 'Directorio de Personas')
@section('page-subtitle', 'Listado general de perfiles personales')

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
                    <a href="{{ route('admin.personas.create') }}" class="btn btn-primary rounded-pill fw-bold shadow-sm d-flex align-items-center justify-content-center px-4" style="padding-top: 0.7rem; padding-bottom: 0.7rem;">
                        <i class="bi bi-person-plus-fill me-2"></i> Nueva Persona
                    </a>
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
                                <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
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
                                <span class="badge bg-body-tertiary text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fw-medium">
                                    <i class="bi bi-envelope-at-fill me-1"></i> {{ $persona->user->email }}
                                </span>
                            @else
                                <span class="badge bg-body-tertiary text-body-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fw-medium">
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
                            <span class="badge border border-opacity-25 rounded-pill px-3 py-1 fw-semibold {{ $badgeColor }}">
                                {{ ucfirst($persona->genero) }}
                            </span>
                        </td>
                        <td class="py-3 text-body-secondary border-0">
                            {{ $persona->telefono ?? '—' }}
                        </td>
                        <td class="text-end px-4 py-3 border-0">
                            <div class="d-flex justify-content-end gap-2">
                                @can('personas.editar')
                                    <a href="{{ route('admin.personas.edit', $persona) }}" class="btn btn-sm btn-light border text-warning rounded-circle shadow-sm" style="width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center;" title="Editar">
                                        <i class="bi bi-pencil-fill"></i>
                                    </a>
                                @endcan

                                @can('personas.eliminar')
                                    <form action="{{ route('admin.personas.destroy', $persona) }}" method="POST" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este registro personal?');">
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
@endsection

@push('scripts')
    <script>
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
    </script>
@endpush
