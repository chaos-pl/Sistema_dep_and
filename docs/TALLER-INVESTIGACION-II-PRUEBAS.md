# Informe de pruebas — Proyecto PROMETEO

**Asignatura:** Taller de Investigación II  
**Proyecto:** Sistema web de tamizaje psicológico para estudiantes del TESVB  
**Fecha de ejecución:** 8 de octubre de 2026 (America/Mexico_City)  
**Referencia del código antes de agregar pruebas:** `53b5acddacfa5b3232e25e89d43dea7f51828896`  
**Autor y grupo:** completar con los datos del estudiante.

## 1. Objetivo

Verificar el funcionamiento de los módulos de registro, inicio de sesión y aplicación de cuestionarios de PROMETEO, así como los controles de acceso y el procesamiento de análisis de texto. Documentar los resultados obtenidos y las limitaciones de la verificación respecto de las actividades programadas para el 2 y el 9 de octubre.

## 2. Alcance y metodología

Se revisaron el código y las pruebas existentes, se agregaron seis pruebas automatizadas y se ejecutó PHPUnit mediante Laravel. Se utilizaron cuentas y respuestas sintéticas, una base SQLite temporal en memoria y respuestas HTTP simuladas del servicio de IA. No se usaron expedientes reales ni se enviaron textos a un servicio externo.

Entorno: Windows, PHP 8.2.12, Laravel 12 y PHPUnit 11.5.55. `phpunit.xml` establece el entorno de pruebas; `tests/TestCase.php` rechaza configuraciones que no correspondan a SQLite en memoria e impide solicitudes HTTP externas no simuladas.

Aunque la primera actividad del cronograma se denomina «pruebas unitarias», la evidencia principal corresponde a pruebas funcionales y de integración automatizadas: intervienen rutas, permisos, modelos y base de datos. La prueba del servicio DASS-21 también consulta preguntas persistidas. No se presentan estos casos como pruebas unitarias puras ni como validación clínica de los instrumentos.

## 3. Matriz de pruebas y evidencias

| ID | Requisito | Procedimiento y resultado esperado | Evidencia automatizada | Alcance |
|---|---|---|---|---|
| MOD-01 | Registro | Registrar datos sintéticos; comprobar usuario, persona, rol estudiante y consentimiento pendiente; rechazar datos incompletos y privilegios inyectados. | `RegistrationTest`, `ConsentAndAccessTest` | Funcional, aislado |
| MOD-02 | Inicio de sesión | Admitir credenciales correctas, rechazar contraseña incorrecta, cerrar sesión y bloquear tras intentos fallidos. | `AuthenticationTest`, `ConsentAndAccessTest` | Funcional, aislado |
| MOD-03 | PHQ-9 | Enviar 9 respuestas; verificar suma, número de respuestas guardadas, nivel y alerta en puntajes 0, 4, 5, 9, 10, 14, 15 y 27. | `ResearchScheduleTest::test_phq9_and_gad7_totals_and_current_application_thresholds` | Comportamiento actual del sistema |
| MOD-04 | GAD-7 | Enviar 7 respuestas; verificar suma, persistencia, nivel y alerta en puntajes 0, 4, 5, 9, 10, 14, 15 y 21. | Misma prueba de MOD-03 | Comportamiento actual del sistema |
| MOD-05 | DASS-21 | Comprobar 29 escenarios de dimensiones, límites, multiplicador por dos y severidad global; verificar además envío, validaciones, alertas y reversión de transacciones. | `ResearchScheduleTest::test_dass21_dimension_boundaries_multiplier_and_global_maximum`, `Dass21IntegrationTest` | Integración con SQLite |
| INT-01 | Procesamiento IA | Guardar diario y trabajo en cola; procesar respuestas simuladas; comprobar estados, reintentos, contrato y protección frente a respuestas obsoletas. | `AnalisisNlpProcessingTest` | Servicio IA simulado |
| SEG-01 | Acceso por rol | Intentar acceder con roles sin autorización y con permisos revocados; verificar rechazo y separación de información clínica. | `ConsentAndAccessTest`, `Dass21IntegrationTest`, `PendingExpedienteAuthorizationTest` | Rutas y casos cubiertos |
| SEG-02 | Inyección SQL | Enviar una expresión SQL en el inicio de sesión y otra en la consulta de instrumentos; comprobar que no se autentica ni se obtiene un instrumento y que no cambian los conteos. | Dos pruebas `test_sql_payload_*` en `ResearchScheduleTest` | Dos puntos de entrada; no auditoría exhaustiva |
| SEG-03 | CSRF | Enviar el inicio de sesión sin token, con token incorrecto y con token válido; esperar 419, 419 y autenticación correcta. | `ResearchScheduleTest::test_real_csrf_validation_rejects_missing_and_wrong_tokens_and_accepts_matching_token` | Middleware web activado expresamente para esta prueba |
| SEG-04 | Contraseñas | Comparar valor almacenado, comprobar hash y comprobar que la serialización omite la contraseña. | `ResearchScheduleTest::test_password_is_hashed_and_hidden_from_serialization` | Hash de contraseñas; no cifrado clínico |
| SEG-05 | Cifrado de información sensible | Revisar modelos y columnas del diario, notas clínicas y borradores. | `AnalisisNlp`, `Diagnostico`, `QuestionnaireDraft` y migraciones | Hallazgo pendiente de corrección |
| INT-02 | Laravel–MariaDB–BETO real | Ejecutar un flujo completo en un entorno separado con esos servicios reales. | Protocolo del apartado 6 | Pendiente de ejecución |

Los nombres de clases de la tabla corresponden a archivos de `tests/Feature/`, con las pruebas de autenticación en `tests/Feature/Auth/`.

## 4. Resultados

La ejecución previa a los nuevos casos obtuvo **119 pruebas aprobadas y 909 aserciones**. La ejecución inicial de los seis casos agregados obtuvo **6 pruebas aprobadas y 363 aserciones**. La ejecución final conjunta obtuvo **125 pruebas aprobadas, 1272 aserciones y cero fallos**, con una duración de **56.88 segundos**. El resultado queda registrado en los archivos siguientes:

- [Salida de la ejecución completa](evidencias/taller-investigacion-ii/ejecucion.txt).
- [Reporte estructurado de PHPUnit](evidencias/taller-investigacion-ii/resultados-phpunit.xml).
- [Pruebas agregadas](../tests/Feature/ResearchScheduleTest.php).

Cada aserción comprueba una condición concreta; su cantidad no representa un porcentaje de cobertura de código ni una garantía de ausencia de defectos.

## 5. Hallazgos y limitaciones

**Cifrado clínico pendiente.** Los modelos revisados no incorporan cifrado de aplicación para `analisis_nlp.texto_ingresado`, `diagnosticos.impresion_diagnostica`, `diagnosticos.retroalimentacion_estudiante` y las respuestas de borradores. Las columnas son texto o JSON y no se encontró una transformación de cifrado en esos modelos. No se verificó cifrado de discos, tablas o respaldos en infraestructura. Por ello, este requisito no se acredita como cumplido. El hash de contraseñas y el código anónimo del estudiante no sustituyen el cifrado de esos datos.

La corrección debe incluir inventario de campos sensibles, gestión de claves, conversión de registros existentes, pruebas de lectura autorizada y comprobación directa de que los valores almacenados no quedan en claro. Agregar un cast de cifrado sin convertir los registros existentes podría impedir su lectura; esta ejecución de pruebas no modificó los datos ni su formato.

**Integración real pendiente.** SQLite y las respuestas simuladas permiten verificar la lógica, pero no prueban conectividad, compatibilidad de MariaDB, configuración del servicio BETO, tiempos reales ni comportamiento de concurrencia entre servicios. Es incorrecto registrar INT-02 como aprobado con esta evidencia.

**Clasificación que se verificó.** PHQ-9 y GAD-7 usan actualmente cuatro etiquetas en el controlador: `nulo` hasta 4, `leve` de 5 a 9, `moderado` de 10 a 14 y `severo` desde 15. Las pruebas comprueban esa implementación. Su correspondencia con el protocolo académico y clínico aprobado requiere una revisión independiente; no se cambió el baremo durante esta actividad.

**Alcance de seguridad.** Las dos entradas SQL y el formulario de inicio de sesión cubiertos no prueban todos los puntos de entrada del sistema. Las pruebas existentes agregan controles por rol, pero esta entrega no constituye una auditoría integral de seguridad.

## 6. Protocolo pendiente para MariaDB y BETO

1. Preparar una instalación separada de pruebas con una base MariaDB exclusiva, cuentas sintéticas, cola y servicio BETO de prueba. Registrar versiones y fecha; no utilizar expedientes reales.
2. Aplicar las migraciones exclusivamente a esa base separada. Mantener la protección actual de PHPUnit: no cambiar su conexión a una base con información existente.
3. Registrar un estudiante, completar su expediente y aplicar los cuestionarios con respuestas predeterminadas. Comparar los resultados con la matriz de casos anterior.
4. Enviar un texto sintético al diario. Registrar el identificador técnico del análisis y la transición de pendiente a procesamiento y completado al ejecutar el trabajador de la cola.
5. Comprobar en MariaDB la persistencia del análisis y la correspondencia de los campos BETO e híbridos con la respuesta real. Documentar la configuración efectiva del envío de PHQ-9 y GAD-7: el flujo actual utiliza ceros de compatibilidad según la guía de integración, no necesariamente resultados aplicados.
6. Comprobar con cuentas estudiante, tutor y psicólogo las vistas permitidas y denegadas. Capturar evidencia sin contraseñas, claves ni datos personales.
7. En ese entorno separado, provocar una indisponibilidad controlada del servicio IA y verificar el manejo de fallo y recuperación. No interrumpir servicios de uso real.
8. Tras implementar cifrado, verificar con un registro sintético que el valor bruto en la base no coincide con el texto, y que la aplicación lo recupera para el usuario autorizado. Registrar también la migración de un registro previo.
9. Completar una bitácora por caso: identificador, fecha, versión, entrada sintética, resultado esperado, resultado observado, estado, evidencia e incidencia. Solo marcar como aprobado lo ejecutado y comprobado.

## 7. Reproducción de la evidencia automatizada

Desde la carpeta del proyecto, con las dependencias instaladas y sin configuración en caché:

```powershell
php artisan test --compact --filter=ResearchScheduleTest
php artisan test --compact --log-junit=docs/evidencias/taller-investigacion-ii/resultados-phpunit.xml
```

La primera instrucción ejecuta los casos agregados y la segunda toda la suite. El reporte XML se reemplaza en cada ejecución; conservar una copia fechada si se necesita comparar entregas.

## 8. Conclusión para el avance académico

Se verificaron mediante PHPUnit los módulos de registro, autenticación, cuestionarios y controles de seguridad incluidos en la matriz. Se ampliaron las pruebas de límites de puntaje, entradas SQL, CSRF y almacenamiento de contraseñas, y se conservaron evidencias reproducibles. El avance acredita pruebas funcionales y de integración aislada. La actividad de integración y seguridad permanece parcialmente completada porque falta ejecutar el flujo real Laravel–MariaDB–BETO y resolver y comprobar el cifrado de los datos sensibles identificados.
