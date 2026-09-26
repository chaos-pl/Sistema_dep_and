# Entrega 5: pruebas y preparación operativa

## Cambios

- Pruebas actualizadas al registro con datos personales, rol estudiante y consentimiento; perfil con sus rutas, redirecciones y bolsas de error reales.
- Protección de intentos de acceso conectada al LoginRequest existente: cinco errores por correo/IP, bloqueo temporal de 60 segundos, limpieza tras autenticación correcta.
- Control Escolar ahora exige permisos en servidor por acción, además del rol. Ocultar enlaces no sustituye autorización.
- Nuevas pruebas de consentimiento, privilegios inyectados, propiedad de perfil, contraseña, apariencia, permisos revocados y configuración de producción.
- Toda la suite usa SQLite en memoria, rechaza configuración cacheada y bloquea llamadas HTTP no simuladas. Las pruebas especializadas conservan sus conexiones aisladas.
- composer dev ahora consume la conexión prometeo y cola prometeo-ia. El trabajador anterior de la cola default no procesaba los diarios.
- CI en .github/workflows/tests.yml: dependencias bloqueadas, frontend, suite completa y sintaxis PHP, sin servicios externos ni base real. Se ejecutará al enviar el repositorio a GitHub; no se publicó ni ejecutó remotamente.
- Comando de diagnóstico de solo lectura y plantilla Linux/systemd para el trabajador.

No se añadieron migraciones ni se modificó la base local. No se cambiaron umbrales clínicos, contrato IA ni consentimiento.

## Permisos de Control Escolar

Cada índice exige su permiso .ver; creación .crear; edición .editar. Carreras, grupos y ciclos requieren .eliminar para borrado. Los roles predeterminados de Control Escolar no tienen esos permisos de eliminación.

Para estudiantes y tutores, cuyo catálogo carece de permiso específico de eliminación, eliminar exige usuarios.eliminar (administrador por defecto). Asignación, cambio y retiro de grupo usan estudiantes.asignar_grupo, estudiantes.cambiar_grupo y estudiantes.quitar_grupo. Historial usa estudiantes.ver_historial; pendientes estudiantes.ver_pendientes. Asignaciones de tutor usan tutores.asignar_grupo. El panel exige control_escolar.dashboard.

No se ejecutó el seeder ni se otorgaron permisos automáticamente. Revisar roles personalizados antes de habilitar las acciones.

## Validación local

Desde la raíz, con dependencias instaladas:

~~~sh
php artisan test --compact
npm run build
php artisan route:list --path=control-escolar
~~~

En Windows, si PHP no está en PATH, usar C:/xampp/php/php.exe. Las pruebas desactivan Vite y no dependen del archivo public/hot.

Si la suite detecta configuración cacheada, ejecutar php artisan config:clear solo en la copia local/de pruebas. No ejecutar pruebas desde el directorio de producción. La suite fuerza variables de pruebas, pero la protección adicional rechaza cualquier base distinta de SQLite en memoria antes de usar RefreshDatabase.

## Comprobación de producción

~~~sh
php artisan prometeo:comprobar-produccion
php artisan prometeo:comprobar-produccion --database
~~~

El primer comando revisa configuración, assets, escritura y extensiones, sin consultar datos ni llamar a la API. La opción --database agrega SELECT y consultas de esquema/registro de migraciones. Ninguno migra, envía diarios, imprime valores de configuración o muestra excepciones completas. Devuelve 0 cuando todo lo comprobado pasa; 1 cuando queda algo pendiente. En Windows/local habrá pendientes esperados: entorno, HTTPS o pcntl.

Los resultados OK no certifican despliegue: faltan pruebas reales de TLS, correo, trabajador, inferencia y restauración. El comando no comprueba reglas de firewall, dependencias vulnerables ni capacidad del servidor.

## Entorno de referencia

La plantilla incluida asume Linux con PHP CLI/FPM 8.2+, pcntl para el trabajador, servidor web con HTTPS y MySQL/MariaDB compatible. Ajustar versión, rutas, usuario, dominio y capacidad al servidor elegido. No se instaló software ni se cambió infraestructura.

Instalar dependencias según composer.lock y package-lock.json. La publicación necesita los assets compilados, no el servidor de desarrollo. Mantener la raíz pública del servidor web en public/; el código, .env y storage no deben estar bajo una raíz pública general. Conceder escritura solo a storage/ y bootstrap/cache/ para el usuario del servicio.

Configurar de forma privada:

- APP_ENV=production y APP_DEBUG=false.
- APP_URL con HTTPS; APP_KEY existente conservada. Generar una clave solo para instalaciones nuevas; nunca regenerarla durante una actualización.
- Conexión y credenciales de la base del entorno.
- SESSION_SECURE_COOKIE=true, SESSION_HTTP_ONLY=true, SESSION_SAME_SITE=lax.
- Sesión y caché persistentes. Si hay varias instancias, compartir la base/Redis y la clave de aplicación; file no coordina hosts.
- PROMETEO_IA_URL con HTTPS para el endpoint POST de inferencia. Validar el contrato de la entrega 1 con texto sintético. No usar datos reales para comprobar conectividad.
- Correo operativo para recuperación de contraseña; los avisos internos no dependen del correo.
- TLS y proxies de confianza según infraestructura. No confiar indiscriminadamente en cualquier proxy.

La API estable fuera de Colab debe proveerse y desplegarse por separado. Esta entrega prepara Laravel; no aloja BETO/XGBoost ni demuestra capacidad o disponibilidad del modelo.

## Primera instalación frente a actualización

**Base nueva:** ejecutar migraciones completas y solo los seeders de catálogos descritos en ENTREGA-2-ESQUEMA.md. No usar el DatabaseSeeder general para crear cuentas de operación: contiene comportamiento de demostración. Definir la cuenta administradora por el procedimiento controlado del operador.

**Base existente:** respaldo verificado y revisión de migrate:status, guías 1–4 y migraciones pendientes. No ejecutar migrate:fresh, composer setup ni el seeder completo de roles sobre usuarios reales. Los históricos DASS y casos requieren los comandos idempotentes de sus guías.

## Publicación de una actualización

1. Probar la revisión en una copia aislada. Ejecutar suite y build antes de preparar el paquete.
2. Respaldar base, archivos persistentes y configuración privada; comprobar restauración en otra base.
3. Poner la aplicación en mantenimiento y detener el trabajador de manera ordenada. Preservar el diario y los trabajos pendientes.
4. Instalar dependencias de producción y publicar assets construidos. Conservar configuración, APP_KEY y archivos persistentes. No publicar public/hot.
5. Aplicar únicamente migraciones revisadas. Esta entrega 5 no añade ninguna.
6. Construir cachés y ejecutar la comprobación de producción con --database.
7. Iniciar trabajador, retirar mantenimiento y comprobar recorridos con cuentas sintéticas.

Comandos de referencia en la raíz del release, ejecutados por el operador:

~~~sh
composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan prometeo:comprobar-produccion --database
~~~

Configurar el entorno antes de config:cache. No ejecutar estos comandos sobre la copia local durante pruebas. El build puede producirse previamente con npm ci y npm run build; conservar public/build/ en el paquete publicado.

## Trabajador IA

Plantilla: deploy/prometeo-worker.service.example. Editar rutas, usuario y binario PHP, copiar al administrador de servicios del servidor y habilitarlo allí. No se instala automáticamente.

Comando equivalente:

~~~sh
php artisan queue:work prometeo --queue=prometeo-ia --sleep=3 --tries=3 --timeout=75 --max-time=3600
~~~

La reserva configurada es 120 segundos, mayor que el timeout de 75; el cierre del servicio permite 100 segundos. systemd reinicia el trabajador al finalizar su hora de vida o fallar. No usar queue:listen --tries=1 para esta cola.

Después de cambiar código/configuración, reiniciar el servicio o usar queue:restart con caché persistente compartida y un supervisor que lo vuelva a iniciar. No editar trabajos serializados.

Vigilar cantidad/antigüedad de pendientes, fallos, capacidad de almacenamiento y disponibilidad del trabajador. /up comprueba arranque de Laravel; no representa salud de IA, cola ni base completa. No exponer paneles de cola ni diagnósticos a usuarios sin autorización.

Para fallos IA, usar el código controlado mostrado por la aplicación y reanálisis desde Psicología una vez corregida la causa. No hacer reintentos masivos indiscriminados ni copiar diarios/cuerpos de respuesta en tickets o registros.

## Comprobaciones antes de habilitar usuarios

- Acceso válido, rechazo de credenciales y bloqueo temporal.
- Registro, consentimiento y expediente pendiente.
- Control Escolar: creación/asignación autorizada y rechazo tras revocar un permiso.
- Envío sintético de diario: pendiente → procesando → completado; caso/aviso según contrato simulado o endpoint de pruebas.
- DASS-21, valoración, retroalimentación compartida, seguimiento y cierre.
- Tutor sin resultados clínicos; reportes y CSV con permisos.
- Recuperación de contraseña con buzón de prueba.
- Navegación móvil y modo oscuro, fuentes, archivos estáticos y HTTPS.
- Reinicio del trabajador sin duplicar procesamiento; restauración de respaldo en ambiente aislado.

## Recuperación y pendientes externos

Si falla un release, mantener mantenimiento y trabajador detenido, evaluar compatibilidad de esquema antes de volver al código anterior. No usar migrate:rollback indiscriminadamente: varias migraciones adoptan datos existentes y los contextos/avisos se perderían. Restaurar un respaldo probado cuando sea necesario y contabilizar escrituras posteriores antes de decidir.

Quedan por definir/probar en el servidor real: hosting y dominio, TLS/proxy, correo, endpoint IA estable, supervisor, respaldos/restauración, monitoreo, revisión visual y capacidad con carga representativa. No se ejecutaron aquí ni se debe interpretar esta entrega como publicación en producción.

## Archivos principales

- app/Console/Commands/CheckProductionReadiness.php
- app/Services/ProductionReadinessService.php
- app/Http/Controllers/Auth/AuthenticatedSessionController.php
- app/Http/Controllers/ControlEscolar/ (los siete controladores: permisos)
- tests/TestCase.php y phpunit.xml
- tests/Feature/Auth/AuthenticationTest.php, RegistrationTest.php y ConsentAndAccessTest.php
- tests/Feature/ProfileTest.php, ExampleTest.php y ProductionReadinessTest.php
- composer.json (trabajador local)
- .github/workflows/tests.yml
- deploy/prometeo-worker.service.example
- README.md y este documento

## Resultado de validación local

100 pruebas aprobadas, 742 aserciones, sin fallos. Pint aprobó para todos los archivos PHP modificados y git diff --check no encontró errores. Vite compiló; persisten advertencias previas de resolución de fuentes y datos Browserslist antiguos. No se ejecutó el workflow remoto ni una publicación en servidor.
