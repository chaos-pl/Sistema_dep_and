# Entrega 4: evolución, avisos y reportes

## Funcionalidad

- Estudiante: **Mi evolución** en el menú, con periodo seleccionable (último año por defecto).
- Psicología: **Evolución del estudiante** desde el detalle del tamizaje y del caso.
- Cinco gráficas independientes: DASS-21 depresión, ansiedad y estrés (0–42 cada una), PHQ9 (0–27) y GAD7 (0–21). Eje por fecha real, tablas accesibles desplegables y estados vacíos. Las líneas conectan aplicaciones, no mediciones intermedias. No se combinan escalas ni se recalculan resultados.
- DASS-21 incluye detalles históricos aunque aún no estén vinculados a una evaluación general.
- **Notificaciones** en escritorio y móvil: contador, filtros todos/sin leer, marcar leído o sin leer y consultar contenido.
- Los nuevos casos de cuestionarios y diarios IA avisan a psicólogos con expediente y permisos para ese origen. La sincronización histórica es silenciosa. Reanalizar el mismo caso no duplica avisos.
- Al registrar una valoración con retroalimentación compartida se avisa al estudiante propietario con permiso para verla. El aviso no guarda textos del diario, puntuaciones, nombres ni notas clínicas. La impresión diagnóstica y notas de seguimiento permanecen privadas.
- Abrir un aviso usa POST con CSRF y comprueba nuevamente propiedad, rol, expediente y permisos del destino. Tras revocar el acceso, el aviso genérico puede permanecer pero su contenido no se abre.
- Reportes agregados con fechas (mes actual por defecto), carrera, grupo, ciclo, instrumento y condición de asignación. CSV con iguales filtros y permisos, sin expedientes, identificadores personales, puntuaciones ni resultados de IA. Se neutralizan fórmulas procedentes de etiquetas del catálogo.

## Permisos

No se cambió el seeder ni se añadieron permisos.

| Acceso | Requisitos |
| --- | --- |
| Evolución propia | Estudiante, expediente y evaluaciones.historial.propio |
| Evolución clínica | Psicólogo, expediente, evaluaciones.historial.global y evaluaciones.respuestas.detalle |
| Retroalimentación propia | Estudiante, propiedad de la evaluación y retroalimentacion.ver.propia |
| Reporte y CSV | Admin, psicologo o control_escolar y reportes_globales.ver |
| Avisos | Autenticación y consentimiento; cada aviso pertenece exclusivamente al usuario |

Admin y psicologo ya tienen reportes_globales.ver por defecto. Control Escolar verá el acceso solo si se concede ese permiso mediante la gestión existente. El tutor conserva exclusivamente sus vistas generales actuales.

## Asignación académica e interpretación

Las nuevas aplicaciones DASS-21, PHQ9 y GAD7 guardan identificadores y nombres de grupo, carrera y ciclo en evaluaciones, con contexto_registrado_at, dentro de la transacción del envío. Los nombres se conservan aunque se renombre o elimine el catálogo.

No se infieren asignaciones históricas. El comando dass21:link-evaluations deja el contexto histórico vacío. Se distinguen:

1. Asignación registrada al responder.
2. Sin grupo al responder: hubo captura, pero no había grupo disponible.
3. Asignación histórica no registrada: no hubo captura.

El reporte usa la fecha de aplicación de la evaluación general, no la fecha de conversión ni la asignación actual. Incluye evaluaciones completadas sin filtrar por matrícula activa actual. Participantes son códigos distintos **dentro de cada fila**. Una persona puede aparecer en diferentes instrumentos o asignaciones: no sumar filas como personas únicas globales. Si cambia la etiqueta de una asignación, sus versiones se conservan en filas separadas.

No se calcula cobertura histórica sin padrón fechado. La cobertura vigente sigue en los paneles anteriores. El reporte requiere terminar la vinculación de históricos DASS-21; los diarios no se presentan como cuestionarios.

## Activación local

No se ejecutaron migraciones sobre la base local ni se consultaron expedientes reales. Pruebas con SQLite aislado y respuestas simuladas de IA.

Requiere las entregas 1–3, especialmente casos_atencion. Con respaldo de la base prevista, detener temporalmente envíos y trabajadores mientras se aplica la migración y despliega el código: el menú consulta la nueva tabla de avisos.

~~~sh
php artisan migrate --path=database/migrations/2026_09_15_000001_add_evolution_reports_and_notices.php
php artisan view:clear
php artisan queue:restart
~~~

Los avisos son escrituras locales dentro de las transacciones existentes; no requieren correo ni otro trabajador. El diario sigue requiriendo el trabajador de la entrega 1:

~~~sh
php artisan queue:work prometeo --queue=prometeo-ia
~~~

Si faltan históricos por convertir, seguir primero las guías anteriores:

~~~sh
php artisan dass21:link-evaluations --dry-run
php artisan dass21:link-evaluations
php artisan dass21:link-evaluations --dry-run
php artisan casos:sincronizar --dry-run
php artisan casos:sincronizar
~~~

No usar migrate:fresh sobre datos existentes ni ejecutar el seeder completo para activar esta entrega. No se rellenan contextos ni avisos retrospectivos. El rollback elimina los contextos y avisos nuevos; no usarlo como ensayo sobre la base con datos.

## Archivos de esta entrega

Nuevos:

- database/migrations/2026_09_15_000001_add_evolution_reports_and_notices.php
- app/Models/Aviso.php
- app/Services/AvisoService.php
- app/Services/EvaluationContextService.php
- app/Http/Controllers/AvisoController.php
- app/Http/Controllers/EvolucionController.php
- app/Http/Controllers/ReporteController.php
- resources/views/avisos/index.blade.php y feedback.blade.php
- resources/views/evolucion/index.blade.php
- resources/views/reportes/index.blade.php
- resources/views/components/delivery-four-nav.blade.php, evolution-link.blade.php y period-filter.blade.php
- tests/Feature/EvolutionReportsNoticesTest.php
- Este documento.

Integraciones en archivos existentes, conservando las entregas anteriores:

- app/Services/Dass21EvaluationService.php
- app/Services/CasoAtencionService.php
- app/Console/Commands/SyncAttentionCases.php
- app/Http/Controllers/Estudiante/EvaluacionController.php
- app/Http/Controllers/Psicologo/DiagnosticoController.php
- resources/views/layouts/app.blade.php
- resources/views/psicologo/casos/show.blade.php
- resources/views/psicologo/tamizajes/show.blade.php
- resources/css/tamizajes.css
- routes/web.php
- tests/Feature/AcademicSchemaTest.php

## Validación y pendientes operativos

~~~sh
php artisan test --filter="EvolutionReportsNoticesTest|AcademicSchemaTest|AttentionCaseTest|Dass21IntegrationTest|AnalisisNlpProcessingTest"
npm run build
~~~

Antes de habilitar usuarios: aplicar migración, reiniciar trabajador, comprobar navegación con cuentas locales de cada rol y revisar móvil/tema oscuro. No se hizo prueba visual contra la base local real.

La entrega 5 sigue pendiente: actualizar pruebas iniciales de autenticación/perfil, ampliar cobertura restante y preparar operación fuera de desarrollo. Persisten avisos previos de Vite por fuentes locales no resueltas y Browserslist desactualizado.

Resultado de validación: las 10 pruebas nuevas pasaron. Suite completa: 78 aprobadas, 8 fallos previos, 617 aserciones. Los ocho fallos corresponden a expectativas iniciales de autenticación, registro, perfil y ruta raíz. Pint y git diff --check aprobaron. Vite compiló con los avisos preexistentes descritos arriba.
