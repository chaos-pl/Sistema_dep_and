@php
    $tarjetas = [
        ['Casos sin asignar', $sinAsignar->total, $sinAsignar->primero ? 'En espera desde el '.\Illuminate\Support\Carbon::parse($sinAsignar->primero)->format('d/m/Y H:i') : 'Sin casos en espera', 'inbox', '#d48b24', route('psicologo.casos.index', ['bandeja' => 'sin_asignar'])],
        ['Mis casos por revisar', $porRevisar, 'Pendientes de primera revisión', 'clipboard-check', '#3282ce', route('psicologo.casos.index', ['bandeja' => 'por_revisar'])],
        ['Mis seguimientos', $misSeguimientos, 'Ordenados por última valoración o nota', 'journal-medical', '#159989', route('psicologo.casos.index', ['bandeja' => 'seguimientos'])],
    ];
    if (auth()->user()->can('evaluaciones.historial.global')) {
        $niveles = ['Normal', 'Leve', 'Moderado', 'Severo', 'Extremadamente severo'];
        $tarjetas[] = ['Estudiantes con último DASS-21 severo', $estudiantesSeveros, 'Incluye el nivel extremadamente severo', 'activity', '#be6175', route('psicologo.tamizajes.ultimos-severos')];
    }
@endphp
<div class="row row-cols-1 row-cols-sm-2 row-cols-xl-{{ count($tarjetas) }} g-3 mb-4">
    @foreach($tarjetas as [$titulo, $total, $descripcion, $icono, $color, $url])
        <div class="col"><a class="clinical-metric" href="{{ $url }}" style="--metric-color:{{ $color }}">
            <div class="d-flex justify-content-between align-items-center" aria-hidden="true">
                <i class="metric-icon bi bi-{{ $icono }}"></i><i class="bi bi-arrow-up-right"></i>
            </div>
            <h3 class="h6 fw-bold mb-0">{{ $titulo }}</h3>
            <strong>{{ $total }}</strong><small>{{ $descripcion }}</small>
        </a></div>
    @endforeach
</div>
<div class="row g-3 mb-4">
    <div class="col-12 col-xl">
        @include('psicologo.grafica-barras', [
            'titulo' => 'Mis casos por estado', 'descripcion' => 'Distribución actual de los casos a tu cargo.',
            'conteos' => $casosPorEstado, 'color' => '#159989',
            'etiquetas' => ['pendiente' => 'Pendientes', 'asignado' => 'Asignados', 'seguimiento' => 'En seguimiento', 'cerrado' => 'Cerrados'],
            'vacio' => 'Todavía no tienes casos asignados.',
        ])
    </div>
    @can('evaluaciones.historial.global')
    <div class="col-12 col-xl">
        @include('psicologo.grafica-barras', [
            'titulo' => 'Último DASS-21 por estudiante', 'descripcion' => 'Nivel máximo en la última aplicación completada. Cada estudiante cuenta una vez.',
            'conteos' => $nivelesDass, 'color' => '#7181c4',
            'etiquetas' => array_combine($niveles, $niveles),
            'vacio' => 'Todavía no hay aplicaciones completadas.',
        ])
    </div>
    @endcan
</div>
