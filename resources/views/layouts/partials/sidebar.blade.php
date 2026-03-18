@php
    $user = auth()->user();
    $rol = $user->rol ?? null;

    $menuByRole = [
        'estudiante' => [
            ['label' => 'Panel de bienestar', 'icon' => 'bi-house-door', 'route' => 'estudiante.dashboard'],
            ['label' => 'Evaluaciones PHQ-9 / GAD-7', 'icon' => 'bi-clipboard-check', 'route' => 'tamizaje.show'],
            ['label' => 'Diario emocional', 'icon' => 'bi-journal-richtext', 'route' => 'diario.index'],
            ['label' => 'Mi perfil', 'icon' => 'bi-person-circle', 'route' => 'profile.edit'],
        ],
        'tutor' => [
            ['label' => 'Dashboard tutor', 'icon' => 'bi-speedometer2', 'route' => 'tutor.dashboard'],
            ['label' => 'Grupos asignados', 'icon' => 'bi-people', 'route' => 'grupos.index'],
            ['label' => 'Métricas anónimas', 'icon' => 'bi-bar-chart-line', 'route' => 'reportes.tutor'],
            ['label' => 'Seguimiento académico', 'icon' => 'bi-clipboard2-pulse', 'route' => 'seguimiento.index'],
        ],
        'psicologo' => [
            ['label' => 'Panel clínico', 'icon' => 'bi-activity', 'route' => 'psicologo.dashboard'],
            ['label' => 'Alertas prioritarias', 'icon' => 'bi-exclamation-triangle', 'route' => 'alertas.index'],
            ['label' => 'Diagnósticos', 'icon' => 'bi-file-medical', 'route' => 'diagnosticos.index'],
            ['label' => 'Resultados NLP', 'icon' => 'bi-robot', 'route' => 'analisis.index'],
        ],
    ];

    $menuItems = $menuByRole[$rol] ?? [['label' => 'Dashboard', 'icon' => 'bi-house-door', 'route' => 'dashboard']];
@endphp

<div class="d-flex align-items-center gap-3 mb-4 pb-2 border-bottom border-light border-opacity-25">
    <div class="brand-mark text-white fs-4">
        <i class="bi bi-heart-pulse-fill"></i>
    </div>
    <div>
        <div class="fw-bold fs-5">Tamizaje Mental</div>
        <div class="small text-white text-opacity-75">Entorno seguro, empático y confidencial</div>
    </div>
</div>

<div class="small text-uppercase fw-semibold text-white text-opacity-50 mb-3">Módulos disponibles</div>
<nav class="mb-4">
    @foreach ($menuItems as $item)
        <a href="{{ route($item['route']) }}" class="sidebar-link {{ request()->routeIs($item['route']) ? 'active' : '' }}">
            <i class="bi {{ $item['icon'] }}"></i>
            <span>{{ $item['label'] }}</span>
        </a>
    @endforeach
</nav>

<div class="sidebar-footer-card p-3 mt-auto">
    <div class="d-flex align-items-start gap-3 mb-3">
        <div class="brand-mark flex-shrink-0" style="width:44px;height:44px;">
            <i class="bi bi-shield-check"></i>
        </div>
        <div>
            <div class="fw-semibold">Protección de datos</div>
            <div class="small text-white text-opacity-75">Uso de código anónimo y acceso clínico restringido.</div>
        </div>
    </div>

    <a href="{{ route('consentimiento.create') }}" class="sidebar-link p-2 mb-2">
        <i class="bi bi-file-earmark-lock"></i>
        <span>Consentimiento</span>
    </a>

    <a href="{{ route('aviso.privacidad') }}" class="sidebar-link p-2 mb-3">
        <i class="bi bi-shield-lock"></i>
        <span>Aviso de privacidad</span>
    </a>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" class="btn btn-light w-100 rounded-4">
            <i class="bi bi-box-arrow-right me-2"></i>Cerrar sesión
        </button>
    </form>
</div>
