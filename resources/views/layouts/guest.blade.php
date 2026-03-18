<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema de Tamizaje') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @include('layouts.partials.theme')
</head>
<body>
    <div class="app-shell d-flex align-items-center min-vh-100 py-4">
        <div class="container">
            <div class="row justify-content-center align-items-center g-4">
                <div class="col-lg-5 d-none d-lg-block">
                    <div class="pe-lg-4">
                        <span class="soft-badge soft-primary mb-3">Sistema web para salud mental universitaria</span>
                        <h1 class="display-6 fw-bold mb-3">Una experiencia calmada, clara y empática para detectar depresión y ansiedad a tiempo.</h1>
                        <p class="text-muted-soft fs-5 mb-4">La interfaz utiliza tonos lila suaves, jerarquía visual limpia y mensajes de apoyo para reducir la carga cognitiva del estudiante.</p>
                        <div class="app-card-soft p-4">
                            <div class="d-flex gap-3 mb-3">
                                <div class="metric-icon"><i class="bi bi-incognito"></i></div>
                                <div>
                                    <h5 class="fw-semibold mb-1">Privacidad por diseño</h5>
                                    <p class="text-muted-soft mb-0">El código anónimo y el acceso por roles protegen la información sensible.</p>
                                </div>
                            </div>
                            <div class="d-flex gap-3 mb-3">
                                <div class="metric-icon"><i class="bi bi-clipboard2-heart"></i></div>
                                <div>
                                    <h5 class="fw-semibold mb-1">Tamizaje guiado</h5>
                                    <p class="text-muted-soft mb-0">PHQ-9 y GAD-7 con formularios paso a paso y progresos fáciles de entender.</p>
                                </div>
                            </div>
                            <div class="d-flex gap-3">
                                <div class="metric-icon"><i class="bi bi-chat-square-text"></i></div>
                                <div>
                                    <h5 class="fw-semibold mb-1">Expresión emocional</h5>
                                    <p class="text-muted-soft mb-0">Espacios para diario personal orientados a análisis NLP y acompañamiento clínico.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-lg-5 col-xl-4">
                    <div class="app-card p-4 p-md-5">
                        {{ $slot }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
