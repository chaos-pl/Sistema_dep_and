<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PROMETEO')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --app-bg: #f7f4fb;
            --app-surface: #ffffff;
            --app-primary: #9b72cf;
            --app-text: #374151;
            --app-border: #eadff7;
        }

        body {
            background: var(--app-bg);
            color: var(--app-text);
            font-family: "Inter", "Segoe UI", sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
        }

        .modal-container {
            background: var(--app-surface);
            border: 1px solid var(--app-border);
            border-radius: 24px;
            box-shadow: 0 10px 40px rgba(155, 114, 207, 0.15);
            width: 100%;
            max-width: 650px;
            overflow: hidden;
            opacity: 0;
            transform: translateY(30px);
        }

        /* ... (resto de tus estilos de este archivo se quedan igual) ... */
        .modal-header-prometeo { padding: 1.5rem 2rem; border-bottom: 1px solid var(--app-border); background: rgba(255, 255, 255, 0.9); }
        .modal-body-prometeo { padding: 2rem; }
        .modal-badge { width: 64px; height: 64px; border-radius: 20px; display: inline-flex; align-items: center; justify-content: center; background: #efe6fb; color: var(--app-primary); margin: 0 auto; }
        .app-card { border-radius: 16px; border: 1px solid var(--app-border); }
        .btn { border-radius: 12px; padding: 0.6rem 1.2rem; font-weight: 500; }
        .btn-primary { background: var(--app-primary); border-color: var(--app-primary); }
        .btn-primary:hover { background: #8258bb; border-color: #8258bb; }
    </style>
    @stack('styles')
</head>
<body>

<div class="modal-container" id="mainModalContainer">
    @yield('content')
</div>

@stack('modals')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        anime({
            targets: '#mainModalContainer',
            translateY: [30, 0],
            opacity: [0, 1],
            duration: 800,
            easing: 'easeOutExpo'
        });
    });
</script>

@include('sweetalert::alert')
@stack('scripts')
</body>
</html>
