# Apariencia, animaciones y foto de perfil

## Cambios
- Ocho paletas centralizadas: lila, azul, verde, rosa, turquesa, índigo, ámbar y coral.
- Botones primarios, contornos, indicadores y controles utilizan el acento. Los colores semánticos de peligro, advertencia y resultados clínicos se conservan.
- Vista previa de tema, densidad, acento y movimiento. Restablecer prepara valores predeterminados; hay que guardar para conservarlos.
- Tema automático según el sistema. Las animaciones respetan la preferencia del dispositivo y la opción guardada.
- Un único Anime.js 3 compatible con las vistas existentes, incluido en Vite junto con Granim. Se eliminaron cargas duplicadas y referencias al archivo Granim inexistente.
- Entradas, transiciones de preguntas y efectos de interacción. Los bucles se pausan al ocultar la página; los fondos también al salir de vista.
- Interruptor de movimiento completo y diseño del perfil ajustado a móvil.
- Foto opcional con vista previa y encuadre horizontal/vertical. Se muestran iniciales cuando no existe foto.
- Login adaptable con tema según sistema, mostrar/ocultar contraseña y estado de envío. Mantiene las rutas y validación existentes.

## Fotos
Se reutiliza personas.foto_perfil: esta entrega no necesita migración.
Es necesario completar primero los datos personales.
JPEG, PNG y WebP, máximo 2 MB y 3000 × 3000 píxeles. El servidor recorta a 512 × 512, convierte a JPEG y descarta metadatos. Aplica orientación JPEG cuando EXIF está disponible.
Los archivos se guardan en el disco local privado, con nombre aleatorio. La ruta autenticada /perfil/foto devuelve únicamente la foto del usuario actual, sin caché. Reemplazar o eliminar una foto elimina el archivo anterior generado por esta funcionalidad. No se publica con storage:link.

## Activación local
1. GD quedó habilitada, con autorización y respaldo, en C:/xampp/php/php.ini. Reiniciar el servidor PHP/Apache que ya estaba abierto para que cargue el cambio. En otra instalación, habilitar extension=gd en su PHP.
2. Ejecutar npm install si se trasladan los cambios a otra máquina, y npm run build.
3. Completar los datos personales y subir una foto desde Mi perfil.
4. El diagnóstico prometeo:comprobar-produccion ahora comprueba GD.
No se ejecutaron migraciones ni se modificaron datos de la base local.

## Validación
- Suite completa: 110 pruebas y 848 aserciones durante la validación, incluyendo una prueba temporal de renderizado usada para capturas. Se retiró esa prueba; quedan 109 pruebas permanentes.
- Revisión posterior: 8 pruebas de foto y preparación de producción, 63 aserciones.
- Compilación Vite y formato PHP correctos.
- Navegador con datos sintéticos: perfil escritorio/móvil, cambio de acento, interruptor, login y mostrar contraseña; sin errores JavaScript y sin desbordamiento horizontal del perfil tras el ajuste.
- Las cargas de foto se probaron con almacenamiento aislado; no se usaron expedientes reales.
- Bootstrap e iconos siguen usando los CDN existentes. La inspección de perfil usó una copia temporal de Bootstrap porque el navegador de pruebas no pudo acceder a ese CDN.

## Archivos de esta entrega
Backend: app/Http/Controllers/ProfileController.php, ProfilePhotoController.php; app/Http/Requests/Profile/UpdateAppearanceRequest.php; app/Services/ProductionReadinessService.php; config/appearance.php; routes/web.php.
Frontend: resources/js/app.js, appearance.js, questionnaire.js; public/css/interface.css, appearance.css, login.css; resources/views/layouts/app.blade.php, guest.blade.php, modal.blade.php; resources/views/auth/login.blade.php; resources/views/perfil/index.blade.php y partials/appearance-form.blade.php, photo-form.blade.php; resources/views/components/profile-avatar.blade.php.
Compatibilidad de animaciones: vistas de dashboard de admin, control escolar, estudiante, psicología y tutor; auth/register y logout; aviso/privacidad; consentimiento/create; DASS-21 create y show; evaluaciones/aplicar e index; admin/expedientes-pendientes, admin/grupos/show; psicologo/alertas, diagnosticos/index y pendiente-expediente; tutor/grupos. Se eliminan dependencias duplicadas o se deja visible el contenido antes de iniciar la animación.
Dependencias y pruebas: package.json, package-lock.json, tests/Feature/ProfilePhotoTest.php, .github/workflows/tests.yml.
Se conservaron los cambios anteriores de entregas previas.

## Pendientes de operación
- Reiniciar el servidor habitual para cargar GD y validar allí una foto propia.
- Verificar la interfaz con las cuentas y navegadores habituales de cada rol; la revisión visual automatizada utilizó un perfil sintético y no todos los módulos.
- La instalación de npm informó 13 avisos de dependencias (2 bajos, 2 moderados, 7 altos, 2 críticos). Falta revisar su alcance en una actualización específica; no se aplicó npm audit fix de forma indiscriminada.

## Correcciones visuales del 20 de septiembre
- Los cinco dashboards y la bienvenida del perfil comparten la clase accent-gradient. El fondo del tema oscuro ya no sobrescribe estos degradados.
- Los componentes antiguos del administrador heredan el acento y las superficies claras/oscuras. Las tarjetas de métricas y gráficas conservan contraste.
- Sidebar, bienvenidas y paneles tm-hero tienen un degradado animado y un degradado estático cuando se reduce el movimiento.
- Las tablas resaltan las celdas al pasar el cursor o entrar con teclado; vuelve el desplazamiento suave del contenido de la primera columna.
- Los modales tienen entrada suave y superficies acordes al tema. El movimiento reducido se respeta.
- La reducción de movimiento oculta únicamente los lienzos decorativos Granim, no las gráficas informativas.
- El login conserva img/logo_prometeo.png y muestra la leyenda «Sistema web para la detección oportuna de depresión y ansiedad estudiantil», también en móvil. Se añadieron luz ambiental, degradados y transiciones.
- GD habilitada en XAMPP con copia de respaldo; comprobada desde PHP habitual, sin añadir -d extension=gd.
- Validación: 38 pruebas relacionadas y 300 aserciones; compilación Vite. Revisión con datos sintéticos de dashboards admin/estudiante en oscuro con coral, modal, logo y login móvil; sin errores JavaScript. Las cuatro gráficas del administrador permanecen visibles con movimiento reducido.
- Archivos de esta corrección: dashboards de los cinco roles, perfil/index, auth/login, public/css/appearance.css, interface.css y login.css, resources/js/appearance.js, tests/Feature/Auth/AuthenticationTest.php y este documento. Fuera del repositorio, únicamente la directiva extension=gd de C:/xampp/php/php.ini (con respaldo).

## Integración local de Granim solicitada el 21 de septiembre
- Se incorporó public/js/granim.min.js desde la distribución 2.0.0 ya instalada en node_modules, junto con granim.LICENSE. El archivo no estaba presente en public/js en este checkout.
- Los layouts app, guest y modal cargan la biblioteca local una sola vez; Vite ya no importa otra copia.
- public/js/prometeo-gradients.js inicializa únicamente los canvas marcados data-prometeo-granim. Cada uno tiene una instancia y un estado default-state.
- Paleta institucional solicitada: #1e1b4b, #4c1d95, #7c3aed y #db2777. Dirección diagonal, opacidades [1, 1], transición de 10000 ms. Esta paleta fija sustituye el acento personal solo en sidebar y bienvenidas de los dashboards.
- Desktop: un envoltorio sidebar-anchor conserva la posición fija; el sidebar interior es relativo, con aislamiento y overflow hidden. Su primer hijo es granim-sidebar, con z-index -1.
- Móvil: granim-sidebar-mobile mantiene un identificador independiente. Se actualizan las dimensiones al abrir el offcanvas.
- Los cinco dashboards utilizan granim-dashboard con z-index 0, borde de 1.5rem y contenido en z-index 1. Se retiraron las inicializaciones y paletas duplicadas de cada vista.
- Se pausará al ocultar la pestaña, salir de vista o reducir el movimiento. Se conserva un fondo estático en ese modo y las gráficas informativas siguen visibles.
- Validación: 30 pruebas relacionadas y 246 aserciones aprobadas; compilación Vite; revisión en navegador con datos sintéticos y muestreo de píxeles que confirma cambio de color, menú móvil, posiciones y capas correctas, una sola carga de la biblioteca y ningún error JavaScript.

### Corrección posterior: movimiento poco visible o detenido
- El controlador de fondos captura el constructor local antes de que appearance.js lo envuelva. Las instancias principales tienen un único responsable de pausa/reanudación; se desactivó el controlador de scroll interno de Granim para usar exclusivamente IntersectionObserver.
- Se alternan pares violeta/magenta con mayor diferencia entre fotogramas, conservando transiciones de 10 segundos.
- Los scripts locales y estilos compartidos del layout incluyen una versión por fecha de modificación para invalidar caché.
- Si el dispositivo o la cuenta solicitan movimiento reducido, se muestra un aviso con la causa y un enlace a Mi perfil. No se cambian preferencias guardadas automáticamente.
- Validación: cambio de píxel RGB [40,30,91] a [70,32,96] en 1.8 segundos en navegador; pausa por sistema/perfil y reanudación al volver a vista verificadas, sin errores JavaScript. 30 pruebas PHP / 246 aserciones aprobadas.
- Esta comprobación usa datos aislados; no confirma la preferencia que está activa en la sesión real del usuario.

### Degradados dinámicos según el acento (sustituye la paleta fija anterior)
- Los cinco dashboards y la cabecera de perfil usan granim-surface + granim-canvas; los dos sidebars usan granim-canvas-sidebar.
- Los contenedores tienen fondo transparente, posición relativa y overflow hidden. Canvas z-index 0, contenido z-index 1. Una capa ::before ofrece un respaldo del mismo acento sin tapar el canvas.
- prometeo-gradients.js comprueba typeof Granim, lee --app-primary y --app-primary-dark mediante getComputedStyle(document.body) y trim(), valida los valores hexadecimales del catálogo y deriva un tono oscuro.
- Cabeceras: direction left-right / 6000 ms. Sidebar: direction diagonal / 8000 ms.
- El cambio de data-accent, clase o estilo del body actualiza las instancias, destruyendo primero las anteriores para evitar duplicados. La vista previa del perfil se actualiza sin recargar.
- Se eliminó la inicialización separada de Mi Perfil. El movimiento reducido pausa el canvas con un fotograma completo, sin ocultarlo.
- No se modificaron las gráficas de Chart.js ni los estilos de modales.
- Validación: navegador con ocho acentos, actualización de paleta, capas transparentes, direcciones/tiempos, movimiento y pausa; 30 pruebas PHP / 246 aserciones; compilación Vite correcta.

### Intensidad del degradado
- El ciclo pasa por cuatro combinaciones de acento, tono intenso y tonos profundos de la misma gama.
- Se conserva el matiz del acento al aumentar saturación/luminosidad. El tono intenso se limita para conservar contraste con texto blanco.
- Ejemplos: lila #7c3aed → #853dff; ámbar #92400e → #c44a00; rosa #be185d → #e00560.
- Se mantienen 6 segundos por etapa en cabeceras y 8 segundos en sidebar. No se añaden ondas ni desplazamiento espacial.
- Validación en navegador de los ocho acentos: cuatro etapas, contraste de blanco mínimo 4.5 en los extremos, cambio temporal de píxeles y pausa por movimiento reducido.
- Cambio limitado a public/js/prometeo-gradients.js, servido con versión de archivo; no requiere compilar Vite.
