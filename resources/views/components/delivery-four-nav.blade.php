@role('estudiante') @can('evaluaciones.historial.propio')
<div class="nav-item-wrapper"><a class="nav-link {{ request()->routeIs('evolucion.own') ? 'active' : '' }}" href="{{ route('evolucion.own') }}"><i class="bi bi-graph-up"></i> Mi evolución</a></div>
@endcan @endrole
@hasanyrole('admin|psicologo|control_escolar') @can('reportes_globales.ver')
<div class="nav-item-wrapper"><a class="nav-link {{ request()->routeIs('reportes.*') ? 'active' : '' }}" href="{{ route('reportes.index') }}"><i class="bi bi-bar-chart"></i> Reportes</a></div>
@endcan @endhasanyrole
