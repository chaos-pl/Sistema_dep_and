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

        /* VARIABLES DE COLOR Y FUENTES */
        :root {
            --app-font-base: 'Inter', sans-serif;
            --app-font-title: 'Montserrat', sans-serif;

            /* Colores Base (Adaptados al nuevo diseño profundo) */
            --app-bg: #f8fafc; /* Fondo general muy claro (casi blanco) */
            --app-surface: #ffffff; /* Superficie de tarjetas */
            --app-primary: #7c3aed; /* Púrpura principal */
            --app-primary-dark: #6d28d9;
            --app-primary-soft: #ede9fe;
            --app-text: #1e293b; /* Texto muy oscuro */
            --app-muted: #64748b; /* Texto secundario */
        }

        body {
            font-family: var(--app-font-base);
            color: var(--app-text);
            background-color: var(--app-bg);
            margin: 0;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        h1, h2, h3, h4, h5, h6, .fw-bold, .modal-title {
            font-family: var(--app-font-title);
        }

        /* Clases de utilidad para pesos gruesos */
        .fw-extrabold { font-weight: 800 !important; }
        .fw-black { font-weight: 900 !important; }

        /* ==========================================
           2. ENTORNO VISUAL Y PARTICULAS
           ========================================== */

        /* Contenedor principal que envuelve toda la página pública */
        .auth-page {
            position: relative;
            min-height: 100vh;
            /* Un sutil gradiente radial en el fondo general para dar profundidad */
            background: radial-gradient(circle at top right, rgba(124, 58, 237, 0.05), transparent 40%),
            radial-gradient(circle at bottom left, rgba(59, 130, 246, 0.05), transparent 40%);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Contenedor de las partículas de fondo animadas */
        .particle-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0; /* Detrás de todo el contenido */
            pointer-events: none; /* No interfiere con clics */
            overflow: hidden;
        }

        /* Estilo base para cada partícula */
        .particle {
            position: absolute;
            border-radius: 50%;
            background: linear-gradient(135deg, rgba(124, 58, 237, 0.1), rgba(167, 139, 250, 0.15));
            filter: blur(40px); /* Borrosidad extrema para que parezcan "auras" */
        }

        /* El cascarón que contiene el formulario (login/registro) */
        .auth-shell {
            position: relative;
            z-index: 10; /* Siempre por encima de las partículas */
            width: 100%;
        }
    </style>

    @stack('styles')
</head>
<body>

<div class="auth-page">
    <div class="particle-container" id="particles">
        <div class="particle" style="width: 400px; height: 400px; top: -10%; left: -5%;"></div>
        <div class="particle" style="width: 300px; height: 300px; bottom: 10%; right: -5%; background: linear-gradient(135deg, rgba(59, 130, 246, 0.1), rgba(147, 197, 253, 0.15));"></div>
        <div class="particle" style="width: 200px; height: 200px; top: 40%; left: 45%;"></div>
    </div>

    <div class="auth-shell">
        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Animamos las 3 auras de luz del fondo para que floten lentamente
        anime({
            targets: '.particle',
            translateX: function() {
                return anime.random(-50, 50); // Se mueven al azar en X
            },
            translateY: function() {
                return anime.random(-50, 50); // Se mueven al azar en Y
            },
            scale: function() {
                return anime.random(8, 12) / 10; // Crecen y se encogen ligeramente (0.8 a 1.2)
            },
            easing: 'easeInOutSine',
            duration: function() {
                return anime.random(8000, 12000); // Tardan entre 8 y 12 segundos (muy suave)
            },
            direction: 'alternate',
            loop: true
        });
    });
</script>

@include('sweetalert::alert')

@stack('scripts')
</body>
</html>
