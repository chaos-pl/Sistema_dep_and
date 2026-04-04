@extends('layouts.app')

@section('title', 'Diario Emocional - PROMETEO')
@section('page-title', 'Diario Emocional')
@section('page-subtitle', 'Registro privado de tu estado emocional')

@push('styles')
    <style>
        .anime-item { opacity: 0; transform: translateY(20px); }
        .diario-card:hover { transform: translateY(-3px); transition: .25s ease; }
    </style>
@endpush

@section('content')
    <div class="row g-4">
        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 border-bottom pb-4">
                    <div>
                        <h3 class="fw-black mb-1">Nuevo registro emocional</h3>
                        <p class="text-muted mb-0">
                            Escribe cómo te sientes. Este espacio está vinculado a tu código anónimo:
                            <span class="fw-bold">{{ $estudiante->codigo_anonimo }}</span>
                        </p>
                    </div>

                    <span class="badge bg-light text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold shadow-sm">
                        <i class="bi bi-lock-fill me-1"></i> 100% Privado
                    </span>
                </div>

                <form action="{{ route('diario.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="texto_ingresado" class="form-label fw-bold text-secondary">
                            ¿Cómo te sientes hoy?
                        </label>
                        <textarea
                            name="texto_ingresado"
                            id="texto_ingresado"
                            rows="6"
                            class="form-control form-control-lg bg-light border-0 shadow-sm rounded-4 p-4 @error('texto_ingresado') is-invalid @enderror"
                            placeholder="Hoy me he sentido..."
                            style="resize: none;"
                        >{{ old('texto_ingresado') }}</textarea>

                        @error('texto_ingresado')
                        <small class="text-danger fw-bold mt-2 d-block">
                            <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                        </small>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
                            <i class="bi bi-send-fill me-2"></i>Guardar entrada
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-12 anime-item">
            <div class="app-card p-4 p-md-5 border-0 shadow-sm rounded-4">
                <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
                    <div>
                        <h4 class="fw-black mb-1">Historial de entradas</h4>
                        <p class="text-muted mb-0 small">Solo puedes ver tus propios registros.</p>
                    </div>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                        {{ $entradas->total() }} registros
                    </span>
                </div>

                @forelse($entradas as $entrada)
                    <div class="diario-card bg-light rounded-4 p-4 mb-3 shadow-sm border-0">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2 mb-3">
                            <div class="fw-bold text-dark">
                                <i class="bi bi-calendar-event me-2 text-primary"></i>
                                {{ $entrada->created_at?->format('d/m/Y H:i') }}
                            </div>

                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge bg-light text-secondary border">
                                    Etiqueta: {{ ucfirst($entrada->etiqueta_roberta) }}
                                </span>

                                <span class="badge {{ $entrada->requiere_atencion ? 'bg-danger text-white' : 'bg-success text-white' }}">
                                    {{ $entrada->requiere_atencion ? 'Requiere atención' : 'Sin atención inmediata' }}
                                </span>
                            </div>
                        </div>

                        <p class="text-secondary mb-0" style="white-space: pre-line;">{{ $entrada->texto_ingresado }}</p>
                    </div>
                @empty
                    <div class="alert alert-light border rounded-4 mb-0">
                        Aún no has registrado entradas en tu diario emocional.
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $entradas->links() }}
                </div>
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
                delay: anime.stagger(120),
                easing: 'easeOutExpo',
                duration: 900
            });
        });
    </script>
@endpush
