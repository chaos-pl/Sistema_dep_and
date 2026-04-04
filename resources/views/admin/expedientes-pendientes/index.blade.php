@extends('layouts.app')

@section('title', 'Estudiantes sin expediente - PROMETEO')
@section('page-title', 'Estudiantes sin expediente')
@section('page-subtitle', 'Usuarios con rol estudiante pendientes de asignación académica')

@push('styles')
    <style>
        .search-input-prometeo { padding-left: 2.8rem !important; }
        .anime-item { opacity: 0; transform: translateY(20px); }
    </style>
@endpush

@section('content')
    <div class="row g-4 anime-item">
        <div class="col-12">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">

                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 border-bottom border-secondary border-opacity-10 pb-4 mb-4">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-warning bg-opacity-10 text-warning-emphasis rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                            <i class="bi bi-person-exclamation fs-4"></i>
                        </div>
                        <div>
                            <h4 class="fw-black mb-1 text-body">Expedientes Incompletos</h4>
                            <p class="text-body-secondary mb-0 small">Usuarios con rol de estudiante que aún no tienen matrícula ni grupo asignado.</p>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-prometeo border-top border-secondary border-opacity-10">
                        <thead class="bg-body-tertiary">
                        <tr>
                            <th class="py-3 px-4 rounded-start-3 text-body-secondary fw-bold border-0">Cuenta de Usuario</th>
                            <th class="py-3 text-body-secondary fw-bold border-0">Correo Electrónico</th>
                            <th class="py-3 text-body-secondary fw-bold border-0">Expediente (Persona)</th>
                            <th class="py-3 px-4 rounded-end-3 text-end text-body-secondary fw-bold border-0">Acción Requerida</th>
                        </tr>
                        </thead>
                        <tbody class="border-top-0">
                        @forelse($usuarios as $usuario)
                            <tr class="border-bottom border-secondary border-opacity-10">
                                <td class="px-4 py-3 fw-bold text-body border-0">
                                    <div class="d-flex align-items-center gap-2" style="transition: transform 0.2s ease;">
                                        <i class="bi bi-person-circle text-primary"></i> {{ $usuario->name }}
                                    </div>
                                </td>

                                <td class="py-3 text-body-secondary fw-medium border-0">
                                    <i class="bi bi-envelope-at me-1 opacity-50"></i> {{ $usuario->email }}
                                </td>

                                <td class="py-3 border-0">
                                    @if($usuario->persona)
                                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-medium">
                                            <i class="bi bi-check-circle-fill me-1"></i>
                                            {{ $usuario->persona->nombre }}
                                            {{ $usuario->persona->apellido_paterno }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-2 fw-medium">
                                            <i class="bi bi-x-circle-fill me-1"></i> Faltan datos personales
                                        </span>
                                    @endif
                                </td>

                                <td class="text-end px-4 py-3 border-0">
                                    @if($usuario->persona)
                                        <a href="{{ route('admin.expedientes-pendientes.edit', $usuario->id) }}"
                                           class="btn btn-sm btn-primary rounded-pill shadow-sm fw-bold px-3 hover-elevate">
                                            Completar Expediente <i class="bi bi-arrow-right-circle ms-1"></i>
                                        </a>
                                    @else
                                        <a href="{{ route('admin.personas.index') }}"
                                           class="btn btn-sm btn-light border text-warning-emphasis rounded-pill shadow-sm fw-bold px-3 hover-elevate">
                                            <i class="bi bi-exclamation-triangle-fill me-1"></i> Vincular Persona Primero
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-body-secondary border-0">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-check2-all fs-1 text-success opacity-50 mb-3"></i>
                                        <span>¡Excelente! Todos los estudiantes tienen su expediente completo.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if(method_exists($usuarios, 'links'))
                    <div class="mt-4">
                        {{ $usuarios->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            anime({
                targets: '.anime-item',
                translateY: [30, 0],
                opacity: [0, 1],
                delay: 100,
                easing: 'easeOutExpo',
                duration: 900
            });
        });
    </script>
@endpush
