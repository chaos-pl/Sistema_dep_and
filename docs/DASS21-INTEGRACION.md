# Integración DASS-21

La [entrega 3](ENTREGA-3-CASOS.md) separa valoración y cierre: guardar un diagnóstico
ya no cierra la atención. El contador del tutor considera casos abiertos, incluidos
diarios, sin exponer el origen ni contenido clínico. Las descripciones previas de
cierre automático de este documento quedan sustituidas por ese flujo.

Actualización: la entrega 2 completa las migraciones base. Consultar
[ENTREGA-2-ESQUEMA.md](ENTREGA-2-ESQUEMA.md) para instalaciones nuevas y actualización
de la base existente; las advertencias de esta entrega sobre esquema incompleto
describen el estado anterior. No usar comandos que borren tablas sobre una base con datos.

## Arquitectura

Cada nueva aplicación crea una fila en `evaluaciones` y un detalle en
`dass21_evaluations`, vinculado por `evaluacion_id` único. Las respuestas siguen
en `dass21_evaluation_answers`. No se crea un puntaje total artificial en
`resultados_clinicos`. El servicio `Dass21EvaluationService` guarda todo en una
transacción; el cálculo continúa en `Dass21ScoringService`.

Las alertas y valoraciones usan las relaciones generales existentes. La
presentación usa `nivel_resumen` y `puntaje_resumen` de `Evaluacion`, sin copiar
las tres dimensiones a otra tabla. Los nuevos enlaces siguen las eliminaciones
en cascada del esquema existente.

## Reglas aplicadas

- Se exige el conjunto exacto de 21 preguntas del instrumento DASS21, siete por
  dimensión, con respuestas enteras de 0 a 3.
- Se conserva el cálculo y el criterio de `hasCriticalSeverity()`: generar una
  alerta cuando el nivel máximo sea Severo o Extremadamente severo. No se
  cambiaron umbrales de los instrumentos ni la API BETO.
- Los 14 días siguen siendo un recordatorio; no se añadió bloqueo de envíos.
- Psicología consulta `/psicologo/tamizajes` con filtros de fecha, grupo,
  instrumento y atención. El detalle permite valorar incluso sin alerta.
- El estudiante consulta su historial en `/dass21/historial` y únicamente la
  retroalimentación compartida; no la impresión diagnóstica privada.
- El tutor solo consulta participación y estado general de atención de sus
  grupos. No recibe respuestas, puntuaciones o notas clínicas en sus vistas.
- La cobertura cuenta estudiantes activos distintos, no aplicaciones. Por
  defecto muestra el mes actual hasta hoy, con fechas seleccionables.
- La carrera, grupo y ciclo corresponden a la asignación actual del estudiante.
  No es un reporte de matrícula histórica por fecha de aplicación.
- Los grupos sin estudiantes tienen 0 % de cobertura. Pendiente significa sin
  aplicación en el periodo; no significa abandono ni resultado normal.

## Asignaciones y permisos

`Grupo::visibleToTutor()` se usa en listados y autorizaciones de lectura/escritura
del módulo Tutor. Las asignaciones de `grupo_tutor` deben estar activas y no
eliminadas. Cuando especifican ciclo, debe coincidir con el grupo y estar activo,
no eliminado y dentro de sus fechas. Una asignación sin ciclo no tiene límite
de ciclo. La relación antigua `grupos.tutor_id` se usa solamente cuando el grupo
nunca tuvo filas en `grupo_tutor`; una revocación no reactiva acceso antiguo.

Se reutilizan los permisos existentes del seeder de roles. El listado clínico
exige `evaluaciones.historial.global`; el detalle exige además
`evaluaciones.respuestas.detalle`. La valoración exige `diagnosticos.crear`.
La participación nominativa del tutor usa `usuarios.ver.grupo`, y el estado de
atención usa `alertas.ver.general`. Las vistas administrativas muestran cobertura
agregada, no resultados clínicos individuales.

## Activación en una base existente

La implementación no ejecuta migraciones o conversiones automáticamente. Antes
de aplicarlas, disponer de un respaldo y confirmar que se utiliza la base local
prevista. El repositorio todavía carece de migraciones de creación de varias
tablas de dominio; no usar `migrate:fresh` ni `composer setup` para esta entrega.

Aplicar únicamente la nueva migración sobre el esquema existente:

```sh
php artisan migrate --path=database/migrations/2026_09_09_000001_link_dass21_to_evaluaciones.php
php artisan dass21:link-evaluations --dry-run
php artisan dass21:link-evaluations
php artisan dass21:link-evaluations --dry-run
```

El último comando debe indicar cero resultados sin vínculo. El comando comprueba
referencias de estudiantes e instrumentos y no imprime datos de expedientes.
No genera alertas históricas ni recalcula puntuaciones. Preserva fechas del
detalle y usa su fecha de aplicación para la evaluación general. Procesa por
lotes con transacciones por registro; puede reanudarse sin duplicar vínculos.
La columna se mantiene nullable para permitir la transición de históricos.

Durante la transición, las tarjetas DASS-21 y la cobertura consultan el detalle
original; el listado clínico general incorpora un histórico cuando queda
vinculado. Completar la conversión antes de habilitar la entrega a usuarios.

No ejecutar el seeder completo de roles sobre permisos personalizados para
activar esta entrega. Si el catálogo DASS-21 ya existe, no es necesario volver
a sembrarlo.

## Verificación

```sh
php vendor/bin/phpunit tests/Feature/Dass21IntegrationTest.php
npm run build
```

Las pruebas usan una conexión dedicada SQLite en memoria y un esquema de prueba
vacío derivado únicamente del DDL del respaldo. No contienen registros del SQL
ni usan la conexión local. Se adaptan tipos y sintaxis MariaDB a SQLite; no
sustituyen una comprobación de despliegue y concurrencia sobre MariaDB.

La suite verifica guardado, rollback de escrituras parciales, conversión
repetible, permisos, privacidad de la retroalimentación, cobertura sin duplicados,
asignaciones vigentes, filtros, alertas y compatibilidad de resultados PHQ-9.

La suite histórica de Breeze tiene discrepancias previamente identificadas con
el registro y las migraciones del proyecto; el esquema dedicado no cambia esas
pruebas ni afirma resolverlas.

## Reversión

La migración puede retirarse antes de vincular datos. Después de la conversión,
no hacer rollback automático: eliminar el vínculo dejaría evaluaciones generales
sin detalle y una reconversión podría duplicarlas. Preparar una reversión de
datos específica o restaurar el respaldo correspondiente si fuese necesario.

## Presentación y seguimiento del tutor

Los componentes visuales se encuentran en `resources/css/tamizajes.css`,
importados por `app.css`. Compilar con `npm run build`, o usar `npm run dev`
durante el desarrollo. Respetan los colores, tema y movimiento reducido del perfil.
Esta actualización de estilos y seguimiento no requiere migraciones adicionales.

La tarjeta antes llamada Posible Riesgo ahora se llama Seguimiento pendiente.
Cuenta estudiantes activos distintos de los grupos autorizados del tutor con
alguna alerta `generada` o `asignada_psicologo`, de cualquier fecha. Incluye
PHQ-9, GAD-7 y DASS-21 vinculados a evaluaciones; no incluye entradas NLP sin
alerta en esa tabla. Abre `/tutor/seguimiento`, un listado general sin respuestas,
puntuaciones ni notas clínicas. Es un seguimiento dentro del sistema, no un
correo, mensaje externo o confirmación de una canalización realizada.

Al guardar la valoración del psicólogo se atiende la alerta de esa evaluación.
El estudiante deja de contar solo cuando ya no tiene ninguna alerta pendiente.
Una aplicación nueva normal no cierra pendientes anteriores. Los históricos
vinculados sin alertas no entran automáticamente al contador. Este criterio es
independiente del periodo usado para la cobertura.
