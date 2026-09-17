@props(['label'])
@php
    $tone = match(mb_strtolower($label ?? '')) {
        'normal', 'nulo', 'completado', 'valorado', 'atendido' => 'success',
        'leve', 'moderado', 'pendiente', 'generada', 'asignada_psicologo', 'seguimiento pendiente' => 'warning',
        'severo', 'extremadamente severo' => 'danger',
        default => 'neutral',
    };
@endphp
<span class="tm-badge tm-badge--{{ $tone }}">{{ str_replace('_', ' ', ucfirst($label ?? 'Sin resultado')) }}</span>
