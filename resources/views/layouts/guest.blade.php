<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'PROMETEO'))</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ==========================================
           1. DEFINICIÓN DE FUENTES LOCALES (.ttf)
           ========================================== */

        /* --- INTER (Para textos generales y legibilidad) --- */
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter_18pt-Regular.ttf') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter_18pt-SemiBold.ttf') format('truetype'); font-weight: 600; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Inter'; src: url('/fonts/inter/Inter_18pt-Bold.ttf') format('truetype'); font-weight: 700; font-style: normal; font-display: swap; }

        /* --- MONTSERRAT (Para títulos y el logo de PROMETEO) --- */
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-Regular.ttf') format('truetype'); font-weight: 400; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-SemiBold.ttf') format('truetype'); font-weight: 600; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-Bold.ttf') format('truetype'); font-weight: 700; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-ExtraBold.ttf') format('truetype'); font-weight: 800; font-style: normal; font-display: swap; }
        @font-face { font-family: 'Montserrat'; src: url('/fonts/montserrat/Montserrat-Black.ttf') format('truetype'); font-weight: 900; font-style: normal; font-display: swap; }

        /* VARIABLES DE COLOR Y FUENTES GLOBALES */
        :root {
            --app-font-base: 'Inter', sans-serif;
            --app-font-title: 'Montserrat', sans-serif;
            --app-text: #1e293b;
            --app-muted: #64748b;
        }

        body {
            font-family: var(--app-font-base);
            color: var(--app-text);
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            background-color: #0f172a;
        }

        h1, h2, h3, h4, h5, h6, .fw-bold, .modal-title {
            font-family: var(--app-font-title);
        }

        .fw-extrabold { font-weight: 800 !important; }
        .fw-black { font-weight: 900 !important; }

        .auth-shell {
            position: relative;
            z-index: 10;
            width: 100%;
            min-height: 100vh;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="auth-shell">
    @yield('content')
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<script src="{{ asset('js/granim.min.js') }}"></script>

@include('sweetalert::alert')

@stack('scripts')
</body>
</html>
