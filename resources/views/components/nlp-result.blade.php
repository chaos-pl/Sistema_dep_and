@props(['analysis', 'details' => false])
<div class="d-flex flex-column gap-2 text-start">
    <span class="badge rounded-pill align-self-start px-3 py-2 {{ $analysis->estado_analisis === 'completado' ? 'text-bg-primary' : 'text-bg-secondary' }}">
        {{ $analysis->estado_texto }}
    </span>
    <span class="small {{ $analysis->requiere_atencion ? 'text-warning-emphasis fw-semibold' : 'text-body-secondary' }}">
        {{ $analysis->atencion_texto }}
    </span>
    @if($analysis->estado_analisis !== 'completado')
        <span class="small text-body-secondary">Actualiza la página para consultar el estado. Se conserva cualquier señal de atención anterior.</span>
    @endif
    @if($details && $analysis->etiqueta_hibrida !== null)
        @if($analysis->estado_analisis !== 'completado')
            <strong class="small">Último resultado disponible (anterior a esta solicitud)</strong>
        @endif
        <dl class="row small mb-0 mt-2">
            <dt class="col-7">Clasificación híbrida</dt>
            <dd class="col-5">{{ str_replace('_', ' ', $analysis->etiqueta_hibrida) }}</dd>
            <dt class="col-7">Confianza de esa clasificación</dt>
            <dd class="col-5">{{ number_format($analysis->confianza_hibrida * 100, 2) }} %</dd>
            <dt class="col-7">Probabilidad de la clase de riesgo BETO</dt>
            <dd class="col-5">{{ number_format($analysis->probabilidad_beto * 100, 2) }} %</dd>
        </dl>
        <p class="small text-body-secondary mb-0">La señal de atención es independiente de la clasificación híbrida. Estos porcentajes son salidas de modelos, no una probabilidad clínica individual.</p>
        <span class="small text-body-secondary">Procesado: {{ $analysis->procesado_at?->format('d/m/Y H:i') }}</span>
    @elseif($details && $analysis->estado_analisis === 'legacy')
        <p class="small text-body-secondary mb-0">El registro histórico no permite verificar la correspondencia entre etiqueta y confianza. Reanaliza la entrada para obtener las salidas separadas.</p>
    @endif
</div>
