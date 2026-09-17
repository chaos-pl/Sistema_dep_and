@extends('layouts.app')
@section('page-title', 'Evolución de tamizajes')
@section('content')
<section class="tm-hero mb-4">
    <span class="tm-eyebrow">Historial por instrumento</span>
    <h2>{{ $student->persona?->nombre }} {{ $student->persona?->apellido_paterno }}</h2>
    <p>Consulta cada escala por separado. Los puntos corresponden a aplicaciones realizadas; los espacios entre fechas no representan mediciones.</p>
</section>
@include('components.period-filter')
<div class="row g-4">
@foreach($series as $serie)
    <div class="col-xl-6"><section class="tm-panel tm-body h-100">
        <h3 class="h5">{{ $serie['name'] }}</h3>
        <p class="text-body-secondary small">Escala de 0 a {{ $serie['max'] }} · {{ count($serie['points']) }} aplicaciones</p>
        @if(count($serie['points']))
            <svg viewBox="0 0 640 230" class="evolution-chart" role="img" aria-label="{{ $serie['name'] }}: gráfica por fecha. Valores disponibles en la tabla siguiente.">
                @foreach([0, 0.5, 1] as $ratio)
                    <line x1="45" y1="{{ 190 - 160*$ratio }}" x2="605" y2="{{ 190 - 160*$ratio }}" stroke="currentColor" opacity=".2"/>
                    <text x="5" y="{{ 194 - 160*$ratio }}" fill="currentColor" font-size="12">{{ $serie['max']*$ratio }}</text>
                @endforeach
                @php($coords = collect($serie['points'])->map(fn($p) => (45 + 560 * ($p['date']->timestamp - $from->timestamp) / max(1, $to->timestamp - $from->timestamp)).','. (190 - 160 * $p['score'] / $serie['max']))->all())
                <polyline points="{{ implode(' ', $coords) }}" fill="none" stroke="currentColor" stroke-width="2" opacity=".6"/>
                @foreach($serie['points'] as $p)
                    @php($xy = explode(',', $coords[$loop->index]))
                    <circle cx="{{ $xy[0] }}" cy="{{ $xy[1] }}" r="4" fill="currentColor"><title>{{ $p['date']->format('d/m/Y H:i') }}: {{ $p['score'] }} · {{ $p['level'] }}</title></circle>
                @endforeach
                <text x="45" y="217" fill="currentColor" font-size="12">{{ $from->format('d/m/Y') }}</text>
                <text x="605" y="217" text-anchor="end" fill="currentColor" font-size="12">{{ $to->format('d/m/Y') }}</text>
            </svg>
            <details><summary class="mb-2">Ver fechas y valores</summary>
                <div class="table-responsive evolution-values"><table class="table">
                    <caption class="visually-hidden">{{ $serie['name'] }}: aplicaciones del periodo</caption>
                    <thead><tr><th scope="col">Fecha</th><th scope="col">Puntuación</th><th scope="col">Nivel</th></tr></thead>
                    <tbody>@foreach($serie['points'] as $p)<tr><td>{{ $p['date']->format('d/m/Y H:i') }}</td><td>{{ $p['score'] }}</td><td>{{ $p['level'] }}</td></tr>@endforeach</tbody>
                </table></div>
            </details>
        @else
            <p class="tm-note mb-0">Sin aplicaciones en este periodo.</p>
        @endif
    </section></div>
@endforeach
</div>
@endsection
