-- Esquema de pruebas SQLite derivado exclusivamente del DDL del respaldo.
-- No contiene registros. Mantiene nulabilidad, claves únicas y relaciones.
-- Índices únicos de estudiantes con nombres explícitos para permitir ALTER en SQLite.

CREATE TABLE `alertas` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `evaluacion_id` INTEGER NOT NULL,
  `estado` TEXT NOT NULL DEFAULT 'generada',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`evaluacion_id`),
  FOREIGN KEY (`evaluacion_id`) REFERENCES `evaluaciones` (`id`) ON DELETE CASCADE
);

CREATE TABLE `analisis_nlp` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `codigo_anonimo` varchar(64) NOT NULL,
  `texto_ingresado` text NOT NULL,
  `etiqueta_roberta` varchar(50) NOT NULL,
  `score_confianza` decimal(5,4) NOT NULL,
  `requiere_atencion` INTEGER NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`codigo_anonimo`) REFERENCES `estudiantes` (`codigo_anonimo`) ON DELETE CASCADE
);

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` INTEGER NOT NULL,
  PRIMARY KEY (`key`)
);

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` INTEGER NOT NULL,
  PRIMARY KEY (`key`)
);

CREATE TABLE `carreras` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre` varchar(255) NOT NULL,
  `clave` varchar(255) DEFAULT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `ciclos_escolares` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `nombre` varchar(255) NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
);

CREATE TABLE `dass21_evaluations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `codigo_anonimo` varchar(255) NOT NULL,
  `instrument_id` INTEGER NOT NULL,
  `depression_raw` INTEGER NOT NULL DEFAULT 0,
  `depression_score` INTEGER NOT NULL DEFAULT 0,
  `depression_level` varchar(30) NOT NULL DEFAULT 'Normal',
  `anxiety_raw` INTEGER NOT NULL DEFAULT 0,
  `anxiety_score` INTEGER NOT NULL DEFAULT 0,
  `anxiety_level` varchar(30) NOT NULL DEFAULT 'Normal',
  `stress_raw` INTEGER NOT NULL DEFAULT 0,
  `stress_score` INTEGER NOT NULL DEFAULT 0,
  `stress_level` varchar(30) NOT NULL DEFAULT 'Normal',
  `max_severity_level` varchar(30) NOT NULL DEFAULT 'Normal',
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`instrument_id`) REFERENCES `instrumentos` (`id`) ON DELETE CASCADE
);

CREATE TABLE `dass21_evaluation_answers` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `evaluation_id` INTEGER NOT NULL,
  `question_id` INTEGER NOT NULL,
  `score` INTEGER NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`evaluation_id`,`question_id`),
  FOREIGN KEY (`evaluation_id`) REFERENCES `dass21_evaluations` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`question_id`) REFERENCES `dass21_questions` (`id`) ON DELETE CASCADE
);

CREATE TABLE `dass21_questions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `instrument_id` INTEGER NOT NULL,
  `item_number` INTEGER NOT NULL,
  `statement` text NOT NULL,
  `dimension` varchar(20) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`instrument_id`,`item_number`),
  FOREIGN KEY (`instrument_id`) REFERENCES `instrumentos` (`id`) ON DELETE CASCADE
);

CREATE TABLE `diagnosticos` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `evaluacion_id` INTEGER NOT NULL,
  `psicologo_id` INTEGER NOT NULL,
  `impresion_diagnostica` text NOT NULL,
  `retroalimentacion_estudiante` text DEFAULT NULL,
  `requiere_derivacion` INTEGER NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`evaluacion_id`),
  FOREIGN KEY (`evaluacion_id`) REFERENCES `evaluaciones` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`psicologo_id`) REFERENCES `psicologos` (`id`)
);

CREATE TABLE `estudiantes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `persona_id` INTEGER NOT NULL,
  `matricula` varchar(50) NOT NULL,
  `grupo_id` INTEGER NOT NULL,
  `codigo_anonimo` varchar(64) NOT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`),
  FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
);

CREATE UNIQUE INDEX `estudiantes_persona_id_unique` ON `estudiantes` (`persona_id`);
CREATE UNIQUE INDEX `estudiantes_matricula_unique` ON `estudiantes` (`matricula`);
CREATE UNIQUE INDEX `estudiantes_codigo_anonimo_unique` ON `estudiantes` (`codigo_anonimo`);

CREATE TABLE `evaluaciones` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `codigo_anonimo` varchar(64) NOT NULL,
  `instrumento_id` INTEGER NOT NULL,
  `estado` TEXT NOT NULL DEFAULT 'en_proceso',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`codigo_anonimo`) REFERENCES `estudiantes` (`codigo_anonimo`) ON DELETE CASCADE,
  FOREIGN KEY (`instrumento_id`) REFERENCES `instrumentos` (`id`)
);

CREATE TABLE `failed_jobs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  UNIQUE (`uuid`)
);

CREATE TABLE `grupos` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `carrera_id` INTEGER NOT NULL,
  `tutor_id` INTEGER NOT NULL,
  `ciclo_escolar_id` INTEGER DEFAULT NULL,
  `nombre` varchar(50) NOT NULL,
  `periodo` varchar(20) NOT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`carrera_id`) REFERENCES `carreras` (`id`),
  FOREIGN KEY (`tutor_id`) REFERENCES `tutores` (`id`),
  FOREIGN KEY (`ciclo_escolar_id`) REFERENCES `ciclos_escolares` (`id`) ON DELETE SET NULL
);

CREATE TABLE `grupo_tutor` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `grupo_id` INTEGER NOT NULL,
  `tutor_id` INTEGER NOT NULL,
  `ciclo_escolar_id` INTEGER DEFAULT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`grupo_id`,`tutor_id`,`ciclo_escolar_id`),
  FOREIGN KEY (`ciclo_escolar_id`) REFERENCES `ciclos_escolares` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`grupo_id`) REFERENCES `grupos` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`tutor_id`) REFERENCES `tutores` (`id`) ON DELETE CASCADE
);

CREATE TABLE `instrumentos` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `acronimo` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`acronimo`)
);

CREATE TABLE `jobs` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` INTEGER NOT NULL,
  `reserved_at` INTEGER DEFAULT NULL,
  `available_at` INTEGER NOT NULL,
  `created_at` INTEGER NOT NULL
);

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` INTEGER NOT NULL,
  `pending_jobs` INTEGER NOT NULL,
  `failed_jobs` INTEGER NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` INTEGER DEFAULT NULL,
  `created_at` INTEGER NOT NULL,
  `finished_at` INTEGER DEFAULT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `migrations` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` INTEGER NOT NULL
);

CREATE TABLE `model_has_permissions` (
  `permission_id` INTEGER NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` INTEGER NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
);

CREATE TABLE `model_has_roles` (
  `role_id` INTEGER NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` INTEGER NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
);

CREATE TABLE `movimientos_estudiantes` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `estudiante_id` INTEGER NOT NULL,
  `grupo_origen_id` INTEGER DEFAULT NULL,
  `grupo_destino_id` INTEGER DEFAULT NULL,
  `accion` varchar(50) NOT NULL DEFAULT 'cambiado',
  `motivo` text NOT NULL,
  `observaciones` text DEFAULT NULL,
  `realizado_por` INTEGER NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`estudiante_id`) REFERENCES `estudiantes` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`grupo_destino_id`) REFERENCES `grupos` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`grupo_origen_id`) REFERENCES `grupos` (`id`) ON DELETE SET NULL,
  FOREIGN KEY (`realizado_por`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
);

CREATE TABLE `permissions` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`name`,`guard_name`)
);

CREATE TABLE `personas` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `user_id` INTEGER NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellido_paterno` varchar(100) NOT NULL,
  `apellido_materno` varchar(100) DEFAULT NULL,
  `fecha_nacimiento` date NOT NULL,
  `genero` TEXT NOT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `foto_perfil` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`user_id`),
  FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

CREATE TABLE `psicologos` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `persona_id` INTEGER NOT NULL,
  `cedula_profesional` varchar(50) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`persona_id`),
  UNIQUE (`cedula_profesional`),
  FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
);

CREATE TABLE `respuestas` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `evaluacion_id` INTEGER NOT NULL,
  `numero_pregunta` INTEGER NOT NULL,
  `valor` INTEGER NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  FOREIGN KEY (`evaluacion_id`) REFERENCES `evaluaciones` (`id`) ON DELETE CASCADE
);

CREATE TABLE `resultados_clinicos` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `evaluacion_id` INTEGER NOT NULL,
  `puntaje_total` INTEGER NOT NULL,
  `nivel_riesgo` TEXT NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`evaluacion_id`),
  FOREIGN KEY (`evaluacion_id`) REFERENCES `evaluaciones` (`id`) ON DELETE CASCADE
);

CREATE TABLE `roles` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`name`,`guard_name`)
);

CREATE TABLE `role_has_permissions` (
  `permission_id` INTEGER NOT NULL,
  `role_id` INTEGER NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
);

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` INTEGER DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` INTEGER NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE `tutores` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `persona_id` INTEGER NOT NULL,
  `numero_empleado` varchar(50) NOT NULL,
  `estado` varchar(255) NOT NULL DEFAULT 'activo',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`persona_id`),
  UNIQUE (`numero_empleado`),
  FOREIGN KEY (`persona_id`) REFERENCES `personas` (`id`) ON DELETE CASCADE
);

CREATE TABLE `users` (
  `id` INTEGER PRIMARY KEY AUTOINCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `avatar_icon` varchar(255) NOT NULL DEFAULT 'person-circle',
  `appearance_settings` longtext DEFAULT NULL CHECK (`appearance_settings` IS NULL OR json_valid(`appearance_settings`)),
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `acepto_consentimiento` INTEGER NOT NULL DEFAULT 0,
  `consentimiento_aceptado_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  UNIQUE (`email`)
);
