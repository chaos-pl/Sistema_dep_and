# Entrega 1: análisis del diario en segundo plano

La [entrega 3](ENTREGA-3-CASOS.md) añade casos de atención para señales de IA,
con responsable, seguimiento y cierre independientes del resultado del modelo.

Actualización: [ENTREGA-2-ESQUEMA.md](ENTREGA-2-ESQUEMA.md) completa el esquema base y
actualiza el estado de la suite general. Las referencias a migraciones faltantes
y los 23 errores de pruebas de este documento corresponden al estado anterior.

## Cambios

La petición web guarda la entrada y un trabajo en la tabla `jobs` en una misma
transacción. La conexión de cola `prometeo` usa la misma base de datos que el
diario, aunque `QUEUE_CONNECTION` sea `sync`. El trabajo contiene únicamente el
identificador del análisis y el UUID de su solicitud; no contiene texto ni el
código del estudiante.

Los estados son `pendiente` (en cola), `procesando`, `completado` y `fallido`.
`legacy` identifica resultados anteriores cuya pareja etiqueta/confianza no puede
verificarse. La migración no recalcula históricos ni llama a la API. Los antiguos
registros con etiqueta `pendiente` quedan como `fallido`, disponibles para reintento.

Se guardan por separado:

- `probabilidad_beto`: salida de la clase de riesgo de BETO.
- `etiqueta_hibrida` y `confianza_hibrida`: clasificación híbrida y confianza de esa clase.
- `requiere_atencion`: señal independiente devuelta por la API.

Por ejemplo, `SIN_RIESGO`, confianza 95 %, BETO 80 % y atención verdadera se
conservan tal como llegan. No se cambia la clasificación a riesgo. Los porcentajes
no se presentan como probabilidad clínica individual. Los campos antiguos
`etiqueta_roberta` y `score_confianza` se mantienen por compatibilidad, copiando
la pareja híbrida únicamente cuando el procesamiento finaliza correctamente.

Un reanálisis conserva el resultado y la señal de atención anteriores mientras
está en curso o falla. Las vistas identifican ese resultado como anterior; una
entrada sin resultado no se cuenta como ausencia de señal de atención.

## Contrato y privacidad

Se valida `status`, los tipos JSON, probabilidades entre 0 y 1, la suma de las
probabilidades BETO y la correspondencia entre código y etiqueta híbrida. Una
respuesta incompleta, HTML, etiquetas desconocidas o booleanos como texto falla
con `contrato_invalido`. Solo se persiste la lista de campos permitidos; se
descarta `entrada` y el cuerpo original de la respuesta. No se siguen redirecciones HTTP.

Los errores de conexión y los HTTP 408, 429 y 5xx tienen hasta tres intentos en
total, con esperas de 30 y 120 segundos. Otros errores HTTP, falta de configuración
y contrato inválido terminan sin reintentos automáticos. Conexión HTTP: 10 segundos;
petición: máximo 60 segundos; trabajo: 75 segundos donde PHP admite señales de
timeout; reserva de cola: 120 segundos. Un timeout del trabajo termina como fallido.

Se evita duplicar solicitudes activas con bloqueo transaccional. El middleware
de cola evita ejecuciones simultáneas para una entrada; el UUID impide que un
trabajo anterior sobrescriba una solicitud nueva. Es necesario compartir el
almacén de caché entre workers para que los bloqueos funcionen entre procesos.

Los errores no registran texto, código del estudiante, URL, cuerpo HTTP ni
excepciones originales. Se registra únicamente el ID técnico y un código de
fallo controlado. El texto permanece en `analisis_nlp` para la función del diario.
Esta entrega no elimina logs históricos ni cambia la retención de datos.

La petición mantiene `texto`, `phq9: 0`, `gad7: 0` por compatibilidad con el
notebook actual. Esos ceros son valores de compatibilidad, no cuestionarios
aplicados. Sigue pendiente definir y validar en la API un modo exclusivo de
texto que no use ceros como evidencia clínica. No se modificaron el notebook,
los modelos entrenados, sus umbrales, ni las reglas DASS-21.

La señal NLP aparece en su módulo de psicología; esta entrega no crea filas en
`alertas`, no canaliza automáticamente y no equivale a una notificación al profesional.

## Activación local sobre la base existente

Los cambios de código no ejecutan migraciones ni arrancan workers automáticamente.
Con un respaldo disponible y la base local prevista seleccionada, desde la raíz:

```sh
php artisan config:clear
php artisan migrate --path=database/migrations/2026_09_10_000001_add_processing_to_analisis_nlp.php
```

El esquema existente debe contar con `analisis_nlp`, `jobs`, `failed_jobs` y el
almacén de caché configurado. El respaldo usado para las pruebas contiene esas
tablas. No ejecutar `migrate:fresh` ni `composer setup`: aún faltan migraciones
de creación de tablas del dominio. No volver a ejecutar la migración de creación
de jobs sobre tablas existentes.

En una terminal adicional, mantener este proceso abierto para desarrollo local,
incluido Windows (el listener limita a 90 segundos cada proceso hijo):

```sh
php artisan queue:listen prometeo --queue=prometeo-ia --tries=3 --timeout=90 --sleep=3
```

`composer dev` conserva su worker predeterminado; ese proceso no consume la cola
dedicada. Es necesario iniciar también el comando anterior. No iniciar workers
antes de aplicar la migración. Sin worker, la entrada permanece en cola y el
guardado web sigue funcionando.

En un servidor con supervisión de procesos y soporte PCNTL se puede usar:

```sh
php artisan queue:work prometeo --queue=prometeo-ia --tries=3 --timeout=75 --sleep=3
```

Reiniciar los workers persistentes después de desplegar cambios. Tras corregir
la disponibilidad/configuración de la API, usar “Solicitar reanálisis” o
“Reintentar fallidos e históricos” en psicología. El segundo botón procesa hasta
20 registros por solicitud y excluye entradas ya en curso. Usar estos botones
para generar un UUID nuevo; `queue:retry` sobre un trabajo ya fallido no reactiva
el estado del análisis. No borrar trabajos pendientes para intentar recuperarlos.

Actualizar la página para consultar los estados; no se incorporaron polling,
correo, notificaciones push ni procesamiento de históricos automático.

## Verificación

```sh
php vendor/bin/phpunit tests/Feature/AnalisisNlpProcessingTest.php tests/Feature/Dass21IntegrationTest.php
npm run build
```

Las pruebas usan exclusivamente SQLite en memoria y HTTP simulado. Comprueban
guardado transaccional, aislamiento de diarios, permisos, estados, clasificación
y atención independientes, reintentos, fallos sin datos sensibles en `failed_jobs`,
deduplicación, rechazo de trabajos obsoletos y transición de históricos.

La suite general todavía tiene 23 errores en pruebas de autenticación/perfil
por ausencia de migraciones base (falla primero `carreras`) y una prueba de ejemplo
que espera 200 en `/`, aunque esa ruta redirige con 302. Son pendientes del
esquema y de las pruebas anteriores, fuera de esta entrega.

## Archivos de esta entrega

- `app/Exceptions/PrometeoIaException.php` (nuevo).
- `app/Jobs/ProcesarAnalisisNlp.php` (nuevo).
- `app/Services/AnalisisNlpQueueService.php` (nuevo).
- `app/Services/PrometeoIaService.php`.
- `app/Models/AnalisisNlp.php`.
- `app/Http/Controllers/Estudiante/DiarioController.php`.
- `app/Http/Controllers/Psicologo/AnalisisNlpController.php`.
- `config/queue.php`.
- `database/migrations/2026_09_10_000001_add_processing_to_analisis_nlp.php` (nuevo).
- `resources/views/components/nlp-result.blade.php` (nuevo).
- `resources/views/Diario/index.blade.php`.
- `resources/views/psicologo/analisis-nlp/index.blade.php`.
- `resources/views/psicologo/analisis-nlp/show.blade.php`.
- `tests/Feature/AnalisisNlpProcessingTest.php` (nuevo).
- `docs/ENTREGA-1-IA.md` (nuevo).

Se conservaron los cambios previos de DASS-21 y tutoría.
