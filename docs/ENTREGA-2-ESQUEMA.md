# Entrega 2: esquema reproducible y asignaciones académicas

La [entrega 3](ENTREGA-3-CASOS.md) incorpora dos tablas adicionales de casos e
historial. Con esa entrega el esquema completo contiene 35 tablas.

## Resultado

Las migraciones crean las 33 tablas desde una base vacía, sin importar un respaldo
con datos. Se incorporó el esquema de 13 tablas de dominio y los campos de
consentimiento de usuarios que faltaban. Las migraciones existentes de abril,
DASS-21 y procesamiento de IA completan el resto del esquema.

La nueva migración base tiene fecha `2026_04_01_000000` para ejecutarse antes de
las ampliaciones de abril en instalaciones nuevas. En una base existente Laravel
también ejecuta esta migración pendiente, aunque su fecha sea anterior a las ya
registradas. Conserva las tablas existentes y comprueba que tengan las columnas
base esperadas antes de crear cualquier tabla faltante. No importa registros,
no cambia identificadores ni restablece permisos. Esta comprobación no reemplaza
una auditoría completa de tipos e índices de una base modificada manualmente.

La migración de asignaciones permite `NULL` en `estudiantes.grupo_id` y
`grupos.tutor_id`, conservando las claves foráneas, sus reglas de eliminación y
los índices. Un estudiante puede quedar pendiente de grupo y un grupo pendiente
de tutor. Un ID inexistente sigue rechazándose.

Se confirmó en la estructura local que ambas columnas eran obligatorias y que
las migraciones DASS-21 y de la entrega 1 ya estaban aplicadas. La entrega 2 queda
pendiente de activación en esa base.

## Consistencia de movimientos

Administración y Control Escolar utilizan `StudentGroupAssignmentService` para
asignar o retirar grupos. Bloquea el estudiante durante la operación y guarda
la asignación y el movimiento en una misma transacción. Si falla cualquiera de
las dos escrituras, se revierte la otra.

El historial usa el grupo de origen real: `asignado` si no tenía grupo,
`cambiado` si tenía otro y `quitado` al retirarlo. Repetir la misma operación no
duplica movimientos. No se admiten nuevas asignaciones a grupos inactivos o
eliminados. No se modifican movimientos históricos anteriores a esta entrega.

Control Escolar valida nombres de grupo de hasta 50 caracteres y periodos de
hasta 20, coincidiendo con las columnas de la base. El alta sin grupo conserva
la creación transaccional de usuario, persona y expediente estudiantil.

## Activación sobre la base existente

Con respaldo disponible y la conexión local prevista seleccionada, detener las
escrituras de usuarios y workers durante la migración. Desde la raíz:

```sh
php artisan config:clear
php artisan migrate --path=database/migrations/2026_04_01_000000_create_domain_baseline.php
php artisan migrate --path=database/migrations/2026_09_10_000002_allow_pending_academic_assignments.php
php artisan migrate:status
```

No ejecutar `migrate:fresh`, `migrate:refresh` ni `composer setup` sobre una base
con datos. No volver a sembrar roles ni catálogos para activar esta entrega.
La tabla `migrations` debe conservar el historial de las migraciones existentes.
Una importación sin ese historial requiere revisar la correspondencia entre
estructura y migraciones antes de ejecutar `migrate`; no se inventa ese historial.

Después de aplicar, reiniciar los workers persistentes y comprobar alta sin
grupo, asignación, cambio, retiro y visualización en pendientes.

## Instalación nueva en una base vacía

Seleccionar explícitamente una base nueva y vacía en la configuración local:

```sh
php artisan config:clear
php artisan migrate
php artisan db:seed --class=RolesAndPermissionsSeeder
php artisan db:seed --class=InstrumentosBaseSeeder
php artisan db:seed --class=Dass21Seeder
```

El seeder nuevo agrega PHQ9/GAD7 si faltan, sin renombrar instrumentos existentes
ni duplicar sus variantes en minúsculas. DASS-21 utiliza su seeder existente.
Estos comandos no crean cuentas de acceso administrativo: se conserva el flujo
de administración de cuentas del proyecto. No ejecutar el `DatabaseSeeder`
general por comodidad: incluye asignación de roles a usuarios existentes y una
cuenta de ejemplo. Los seeders de roles/DASS de este apartado son para la base nueva.

## Reversión

La migración base rechaza `down()` deliberadamente: puede haber adoptado tablas
que existían antes, por lo que eliminarlas durante rollback borraría información
ajena a la migración. Utilizar cambios correctivos o un respaldo verificado.

La migración de nulabilidad permite revertir solo si no hay estudiantes sin
grupo ni grupos sin tutor, incluidos registros eliminados lógicamente. Comprueba
ambas condiciones antes de modificar cualquiera de las tablas. No rellena IDs,
no borra registros ni inventa asignaciones. En MariaDB el DDL no es transaccional;
se recomienda realizar la operación sin escrituras concurrentes.

## Pruebas y límites

```sh
php vendor/bin/phpunit tests/Feature/AcademicSchemaTest.php tests/Feature/Dass21IntegrationTest.php tests/Feature/AnalisisNlpProcessingTest.php
php vendor/bin/phpunit
```

Las pruebas de DASS-21 e IA ahora ejecutan las migraciones reales en SQLite en
memoria. El fixture SQL queda únicamente para ensayar la actualización de un
esquema importado; no contiene personas ni expedientes reales. Sus índices
únicos de estudiantes tienen nombres explícitos para que SQLite pueda reconstruir
la tabla al modificar columnas, conservando las mismas restricciones.

Se ensayaron también las migraciones y movimientos en dos bases temporales de
MariaDB: una vacía y otra con una copia solo de la estructura local y del historial
de migraciones. Ambas crearon/conservaron las 33 tablas y permitieron asignaciones
nulas sin perder las claves foráneas. Solo se insertaron datos sintéticos y ambas
bases temporales se eliminaron al terminar.

Durante el primer ensayo, una configuración copiada conservó el nombre de la
conexión original y cambió temporalmente la nulabilidad de las dos columnas locales.
Se restauraron ambas restricciones, se corrigió el nombre de conexión y se añadió
al ensayo una comprobación del destino y un bloqueo de escrituras en la conexión
original. Se comprobó que las migraciones nuevas no quedaron registradas en la base
original y que no quedaron la cuenta ni los movimientos sintéticos del ensayo.

La suite general ya no tiene los 23 errores por tablas faltantes. Persisten ocho
fallos en pruebas anteriores: login/perfil no contemplan el consentimiento,
registro no envía los datos personales exigidos y la prueba de `/` espera 200
en lugar de su redirección. Su actualización corresponde al entregable de pruebas;
no se alteraron las reglas de autenticación para satisfacer esas expectativas.

## Archivos de esta entrega

- `database/migrations/2026_04_01_000000_create_domain_baseline.php` (nuevo).
- `database/migrations/2026_09_10_000002_allow_pending_academic_assignments.php` (nuevo).
- `database/seeders/InstrumentosBaseSeeder.php` (nuevo).
- `app/Services/StudentGroupAssignmentService.php` (nuevo).
- `app/Http/Controllers/Admin/EstudianteController.php`.
- `app/Http/Controllers/ControlEscolar/EstudianteController.php`.
- `app/Http/Controllers/ControlEscolar/GrupoController.php`.
- `tests/Feature/AcademicSchemaTest.php` (nuevo).
- `tests/Feature/Dass21IntegrationTest.php`.
- `tests/Feature/AnalisisNlpProcessingTest.php`.
- `tests/Fixtures/dass21-schema.sql`.
- `docs/ENTREGA-2-ESQUEMA.md` (nuevo).
- `docs/DASS21-INTEGRACION.md` y `docs/ENTREGA-1-IA.md`: referencia al esquema completado.

Se conservaron los cambios previos de DASS-21, tutoría, estilos e IA.
