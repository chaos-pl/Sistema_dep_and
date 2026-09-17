@props(['resultado'])
<div class="row g-3 my-3">
    @foreach(['depression' => ['Depresión', 'heart-pulse'], 'anxiety' => ['Ansiedad', 'activity'], 'stress' => ['Estrés', 'lightning-charge']] as $key => [$label, $icon])
        <div class="col-md-4"><div class="tm-stat h-100">
            <div class="d-flex align-items-center justify-content-between mb-3"><span class="fw-bold">{{ $label }}</span><i class="bi bi-{{ $icon }} text-primary fs-4"></i></div>
            <strong>{{ $resultado->{$key.'_score'} }} <small class="text-body-secondary fs-6">/ 42</small></strong>
            <progress class="tm-progress mb-3" value="{{ $resultado->{$key.'_score'} }}" max="42" aria-label="Puntuación de {{ $label }}">{{ $resultado->{$key.'_score'} }}</progress>
            <x-tamizaje-status :label="$resultado->{$key.'_level'}" />
        </div></div>
    @endforeach
</div>
<p class="d-flex align-items-center flex-wrap gap-2"><span class="text-body-secondary">Severidad máxima</span><x-tamizaje-status :label="$resultado->max_severity_level" /></p>
