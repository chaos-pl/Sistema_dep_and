<figure class="tm-panel h-100 mb-0" style="--chart-color:{{ $color }}">
    <figcaption class="tm-heading d-block">
        <h2>{{ $titulo }}</h2><p>{{ $descripcion }}</p>
    </figcaption>
    <div class="tm-body">
        @if($conteos->sum())
            <ul class="list-unstyled mb-0">
                @foreach($etiquetas as $clave => $etiqueta)
                    @php($cantidad = $conteos->get($clave, 0))
                    <li class="mb-2">
                        <div class="d-flex justify-content-between gap-2 small"><span>{{ $etiqueta }}</span><strong>{{ $cantidad }}</strong></div>
                        <div class="clinical-chart-bar" aria-hidden="true"><span style="width:{{ 100 * $cantidad / $conteos->sum() }}%"></span></div>
                    </li>
                @endforeach
            </ul>
        @else
            <p class="mb-0">{{ $vacio }}</p>
        @endif
    </div>
</figure>
