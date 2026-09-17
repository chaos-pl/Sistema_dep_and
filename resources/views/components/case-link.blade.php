@props(['source'])
@if($source->casoAtencion && \App\Models\CasoAtencion::visibleTo(auth()->user())->whereKey($source->casoAtencion->id)->exists())
    <div class="tm-note my-3 d-flex flex-wrap align-items-center gap-3"><span>Atención: <strong>{{ ucfirst($source->casoAtencion->estado) }}</strong></span><a class="btn btn-outline-primary tm-action" href="{{ route('psicologo.casos.show', $source->casoAtencion) }}">Ver seguimiento del caso <i class="bi bi-arrow-right"></i></a></div>
@endif
