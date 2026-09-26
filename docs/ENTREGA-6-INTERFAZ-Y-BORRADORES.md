# Entrega 6: interfaz y cuestionarios en borrador

## Comportamiento

DASS-21, PHQ9 y GAD7 guardan respuestas parciales en questionnaire_drafts, por estudiante e instrumento. Hay guardado automático tras cambiar una respuesta, botón Guardar borrador y enlace Guardar y salir. El último guarda antes de abandonar; si falla, permanece en el formulario.

En Evaluaciones aparece Continúa tus cuestionarios. Volver al instrumento recupera respuestas y abre la primera pregunta sin responder. Conviene revisar respuestas antiguas según el periodo indicado por cada cuestionario.

El borrador no es una evaluación: no crea resultados, alertas, casos, avisos ni cuenta para cobertura. Al enviar, se validan todas las respuestas y se limpia el borrador en la misma transacción del resultado. Se conserva una versión sin respuestas para rechazar guardados atrasados y envíos repetidos de esa versión. La aplicación siguiente comienza vacía.

Un borrador solo puede guardarse con rol estudiante, permiso evaluaciones.realizar, consentimiento y expediente propio. Nunca se recibe un identificador de otro estudiante del navegador. Versiones distintas entre pestañas producen un error y piden recargar, sin sobrescribir.

No se guardan respuestas en localStorage. Un corte antes de confirmar el guardado puede perder los últimos cambios; la interfaz distingue cambios pendientes, guardando, guardado y error. Sin conexión o sesión vigente no se puede confirmar el guardado. Sin JavaScript se puede enviar el cuestionario completo, pero no guardar automáticamente.

## Interfaz

- DASS-21 está debajo de la bienvenida en los paneles; el estudiante tiene su atajo antes del resumen académico.
- Campana con contador y nombre accesible en la barra superior junto al área de Salir, para todos los roles. Abre el centro existente, con propiedad y permisos sin cambios. Se retiró el enlace duplicado de Ajustes.
- Tablas, filtros, campos de búsqueda, paginación y botones comparten estilos, adaptación móvil y contraste en modo oscuro.
- Radios del cuestionario accesibles por teclado; avance mediante botones nativos. No se depende de Anime para responder.
- Enlace Saltar al contenido, foco visible, estados de guardado anunciados y respeto de movimiento reducido.
- Las fuentes se importan como assets reales de Vite y ya no producen advertencias de resolución. Sigue el aviso previo de antigüedad de Browserslist.

## Activación

Requiere las entregas anteriores. Con respaldo de la base local prevista, aplicar solo esta migración antes de abrir los formularios:

~~~sh
php artisan migrate --path=database/migrations/2026_09_19_000001_create_questionnaire_drafts.php
php artisan view:clear
npm run build
~~~

No se ejecutó esta migración en la base del usuario. No usar migrate:fresh sobre datos existentes. Publicar también public/css/interface.css y public/build/. El rollback borra borradores; no ensayarlo con respuestas que se quieran conservar.

## Archivos

Nuevos:

- database/migrations/2026_09_19_000001_create_questionnaire_drafts.php
- app/Models/QuestionnaireDraft.php
- app/Services/QuestionnaireDraftService.php
- app/Http/Controllers/QuestionnaireDraftController.php
- resources/js/questionnaire.js
- resources/views/components/questionnaire-draft.blade.php
- public/css/interface.css
- tests/Feature/QuestionnaireDraftTest.php
- Este documento.

Integraciones:

- routes/web.php
- app/Http/Controllers/Dass21Controller.php
- app/Http/Controllers/Estudiante/EvaluacionController.php
- app/Services/Dass21EvaluationService.php
- app/Http/Requests/StoreDass21Request.php
- app/Http/Requests/Estudiante/StoreEvaluacionRequest.php
- resources/js/app.js y resources/css/app.css
- resources/views/layouts/app.blade.php
- resources/views/components/delivery-four-nav.blade.php
- resources/views/dass21/create.blade.php
- resources/views/evaluaciones/aplicar.blade.php e index.blade.php
- Dashboard de estudiante, psicólogo, tutor, administrador y Control Escolar.
- tests/Feature/AcademicSchemaTest.php
- README.md

Se conservaron los cambios pendientes de la entrega 5.

## Validación

Pruebas de borrador: propiedad, valores inválidos, pestañas desactualizadas, recuperación en los tres instrumentos, envío incompleto, limpieza transaccional y rechazo de guardados atrasados. Pruebas de orden de dashboard y acceso único a notificaciones.

La suite funcional usa migraciones en SQLite en memoria; no consulta expedientes reales. Se revisaron vistas renderizadas con datos sintéticos en navegador: panel clínico en escritorio, centro de notificaciones y cuestionario a 390 px en tema oscuro, selección por teclado y navegación. La vista estática se usó también para comprobar el mensaje de fallo de guardado; el guardado real del servidor se cubre en las pruebas HTTP de Laravel.

Antes de habilitar usuarios: aplicar migración y comprobar una aplicación de prueba en el entorno local con recarga/cierre de pestaña. La revisión no sustituye ensayos en todos los navegadores, lectores de pantalla ni una auditoría exhaustiva de accesibilidad.

Resultado final: 105 pruebas aprobadas, 802 aserciones. Pint, sintaxis JavaScript y git diff --check correctos. Build de Vite correcto, sin advertencias de fuentes. La revisión también encontró una referencia antigua a js/granim.min.js que devuelve 404; el módulo Granim incluido por Vite permite el funcionamiento actual. Su limpieza global queda como mantenimiento posterior.
