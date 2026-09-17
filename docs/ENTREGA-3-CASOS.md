# Entrega 3: asignación, seguimiento y cierre de casos

## Funcionamiento

Psicología dispone de `/psicologo/casos`, con filtros por estado, origen y
responsable, y acceso desde el menú, el panel, las alertas y los resultados de IA.
El detalle muestra responsable, fechas, resultado de origen y un historial privado.

Cada caso corresponde a una evaluación o a una entrada del diario, no al estudiante
completo. Varias evaluaciones o entradas pueden originar varios casos de la misma
persona. Las claves únicas evitan duplicar casos para un mismo origen.

Las nuevas alertas de cuestionarios abren un caso pendiente. Un análisis de diario
con `requiere_atencion = true` abre un caso en la misma transacción que guarda el
resultado. Las valoraciones de evaluaciones sin alerta también abren seguimiento.
No se cambian umbrales, puntuaciones ni etiquetas de los modelos.

| Estado | Significado |
| --- | --- |
| Pendiente | Todavía no tiene responsable. |
| Asignado | Tiene responsable, sin seguimiento registrado todavía. |
| Seguimiento | Se registró una valoración, una nota o una reapertura. |
| Cerrado | El responsable decidió cerrar e indicó un motivo. |

Consultar un resultado o un caso no lo asigna ni cambia su estado. Para tomar un
caso, elegir “Asignar responsable”, seleccionar al propio profesional y registrar
el motivo. También se puede asignar a otro profesional con acceso al origen.
Una vez asignado, solo su responsable puede transferirlo, añadir notas, cerrarlo
o reabrirlo. Cada acción exige una nota o motivo, de hasta 3000 caracteres.

Guardar una valoración conserva su impresión privada y retroalimentación compartida
en `diagnosticos`, y deja el caso en seguimiento. No lo cierra. Si aún no tenía
responsable, queda asignado al profesional que registra esa valoración. Si ya
pertenece a otro profesional, la valoración se rechaza sin guardados parciales.

El cierre requiere una valoración o nota previa y un motivo explícito. La reapertura
también exige motivo. El historial conserva la fecha y autor de cada acción y el
responsable asociado, incluidas las transferencias. No hay endpoints para editar
o borrar notas anteriores; una corrección se registra como una nota nueva.

Las acciones bloquean la fila del caso y verifican su versión. Una página antigua
o el reenvío de un formulario no sobrescribe el trabajo de otro profesional ni
duplica una acción. El cambio de estado y su entrada de historial son transaccionales.

## Resultados y atención son independientes

Para compatibilidad, las alertas de cuestionarios reflejan el caso: pendiente como
`generada`, asignado/seguimiento como `asignada_psicologo` y cerrado como `atendida`.
Los diagnósticos siguen siendo valoraciones; su existencia no prueba un cierre.

La señal `requiere_atencion` de IA conserva su significado como salida del análisis.
Cerrar un caso no la borra. Un reanálisis no cierra ni reabre automáticamente un
caso existente, aunque cambie la señal. Si se necesita continuar la atención de
esa entrada, el profesional puede reabrir su caso. Una entrada nueva con señal
de atención origina otro caso.

La pantalla de resultados IA sigue mostrando señales del modelo, incluidas las
de entradas cuyo caso está cerrado. La bandeja de casos es la referencia para
consultar atención pendiente, responsable y cierre.

## Permisos y privacidad

- Solo cuentas con rol `psicologo` y expediente profesional acceden a los casos.
- Evaluaciones: requieren `evaluaciones.historial.global` y
  `evaluaciones.respuestas.detalle` para consultar su caso.
- Diarios: requieren `resultados_ia.ver` para consultar su caso.
- Las mutaciones requieren además `diagnosticos.crear`. Se reutilizan permisos
  existentes; no hay que volver a ejecutar el seeder completo de roles.
- El listado y los contadores respetan los permisos por origen. Otro psicólogo
  autorizado puede leer el caso, pero no modificarlo mientras tenga responsable.
- El profesional elegido como responsable debe tener rol, expediente y permisos4r
  para consultar ese origen y registrar atención.
- Tutoría solo recibe el estado general de seguimiento de sus estudiantes activos
  y grupos autorizados. Cuenta personas distintas con al menos un caso abierto,
  incluidos diarios, sin mostrar origen, texto, puntuaciones o notas privadas.
- El estudiante continúa viendo su diario y la retroalimentación compartida de
  sus evaluaciones. Las notas de casos no se comparten con él.

Se conservan las eliminaciones en cascada del esquema: eliminar definitivamente
el origen elimina su caso e historial. La eliminación de un autor o responsable
deja esas referencias nulas. Este historial no es una auditoría inmutable ni
modifica la política de retención del sistema.

## Activación en la base existente

Esta entrega no ejecuta migraciones ni convierte históricos automáticamente.
Primero deben estar aplicadas las entregas 1 y 2. Con respaldo disponible, detener
las escrituras de usuarios y workers durante la activación:

```sh
php artisan config:clear
php artisan migrate --path=database/migrations/2026_09_10_000003_create_casos_atencion.php
php artisan casos:sincronizar --dry-run
php artisan casos:sincronizar
php artisan casos:sincronizar --dry-run
```

El último comando debe indicar cero evaluaciones y diarios sin caso. El comando
solo imprime cantidades, no expedientes ni notas. Procesa por lotes, con una
transacción por origen, y puede reanudarse sin duplicar casos o eventos.

Incluye evaluaciones con alerta o diagnóstico y diarios con señal de atención.
No crea alertas retrospectivas para cuestionarios que nunca la tuvieron ni
recalcula resultados. Los históricos ya valorados quedan en seguimiento con el
psicólogo del diagnóstico, sin inventar fecha de asignación o cierre. Las alertas
anteriormente “atendidas” por guardar una valoración vuelven a reflejar seguimiento;
no se supone que su atención terminó. Es esperable que aumenten los pendientes.
La fecha de apertura del caso importado es la de su incorporación, no la fecha
del cuestionario original; el enlace al origen conserva la fecha de aplicación.

Completar la sincronización antes de habilitar la entrega a usuarios: durante
la transición, tutoría conserva también la lectura de alertas pendientes sin caso,
pero los diarios históricos entran en su estado general al quedar vinculados.

Reiniciar los workers persistentes después de actualizar el código. Para la cola
local de diarios se mantiene el comando de la entrega 1:

```sh
php artisan queue:listen prometeo --queue=prometeo-ia --tries=3 --timeout=90 --sleep=3
```

No usar `migrate:fresh` sobre la base con datos. Revertir esta migración elimina
las dos tablas nuevas y su historial: requiere una estrategia de respaldo y
restauración; no restaura automáticamente los estados anteriores de las alertas.

## Validación

```sh
php vendor/bin/phpunit tests/Feature/AttentionCaseTest.php tests/Feature/Dass21IntegrationTest.php tests/Feature/AnalisisNlpProcessingTest.php tests/Feature/AcademicSchemaTest.php
npm run build
```

Las pruebas usan las migraciones reales en SQLite en memoria y HTTP simulado.
Cubren el ciclo completo, exclusividad del responsable, transferencia, versiones
desactualizadas, permisos por origen, privacidad, valoración sin cierre automático,
rollback de eventos fallidos, conversión repetible de históricos y cascadas.
Las pruebas anteriores de DASS-21 ahora verifican que tutoría conserva el pendiente
después de valorar y lo retira únicamente después del cierre explícito.

Pendientes de otros entregables: notificaciones, gráficas de evolución, reportes,
auditoría de consultas y actualización de las pruebas generales de autenticación
y perfil. Esta entrega no envía correos ni avisos externos.

## Archivos principales

- `database/migrations/2026_09_10_000003_create_casos_atencion.php`.
- `app/Models/CasoAtencion.php` y `app/Models/SeguimientoCaso.php`.
- `app/Services/CasoAtencionService.php`.
- `app/Http/Controllers/Psicologo/CasoAtencionController.php`.
- `app/Console/Commands/SyncAttentionCases.php`.
- `resources/views/psicologo/casos/index.blade.php` y `show.blade.php`.
- `resources/views/components/case-link.blade.php`.
- `tests/Feature/AttentionCaseTest.php`.

Se integraron los modelos `Alerta`, `Evaluacion`, `AnalisisNlp` y `Estudiante`,
el trabajo `ProcesarAnalisisNlp`, los controladores de alertas, diagnósticos,
tamizajes y grupos del tutor, las rutas, el menú y las vistas relacionadas de
psicología/tutoría. Se actualizaron las pruebas de las entregas anteriores y sus
documentos para reflejar este nuevo significado del cierre. El esquema completo
pasa de 33 a 35 tablas.
