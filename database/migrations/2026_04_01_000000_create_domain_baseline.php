<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Esquema anterior a las ampliaciones de abril. En bases importadas se
     * conservan las tablas existentes; no se recrean ni se copian registros.
     */
    public function up(): void
    {
        $definitions = [
            'personas' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained('users')->cascadeOnDelete();
                $table->string('nombre', 100);
                $table->string('apellido_paterno', 100);
                $table->string('apellido_materno', 100)->nullable();
                $table->date('fecha_nacimiento');
                $table->enum('genero', ['masculino', 'femenino', 'otro', 'prefiero_no_decirlo']);
                $table->string('telefono', 20)->nullable();
                $table->string('foto_perfil')->nullable();
                $table->timestamps();
            },
            'carreras' => function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->timestamps();
            },
            'tutores' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('persona_id')->unique()->constrained('personas')->cascadeOnDelete();
                $table->string('numero_empleado', 50)->unique();
                $table->timestamps();
            },
            'psicologos' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('persona_id')->unique()->constrained('personas')->cascadeOnDelete();
                $table->string('cedula_profesional', 50)->unique();
                $table->timestamps();
            },
            'grupos' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('carrera_id')->constrained('carreras');
                $table->foreignId('tutor_id')->constrained('tutores');
                $table->string('nombre', 50);
                $table->string('periodo', 20);
                $table->timestamps();
            },
            'estudiantes' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('persona_id')->unique()->constrained('personas')->cascadeOnDelete();
                $table->string('matricula', 50)->unique();
                $table->foreignId('grupo_id')->constrained('grupos');
                $table->string('codigo_anonimo', 64)->unique();
                $table->timestamps();
            },
            'instrumentos' => function (Blueprint $table) {
                $table->id();
                $table->string('acronimo', 50)->unique();
                $table->string('nombre');
                $table->timestamps();
            },
            'evaluaciones' => function (Blueprint $table) {
                $table->id();
                $table->string('codigo_anonimo', 64);
                $table->foreign('codigo_anonimo')->references('codigo_anonimo')->on('estudiantes')->cascadeOnDelete();
                $table->foreignId('instrumento_id')->constrained('instrumentos');
                $table->enum('estado', ['en_proceso', 'completada', 'abandonada'])->default('en_proceso');
                $table->timestamps();
            },
            'respuestas' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluacion_id')->constrained('evaluaciones')->cascadeOnDelete();
                $table->unsignedTinyInteger('numero_pregunta');
                $table->unsignedTinyInteger('valor');
                $table->timestamps();
            },
            'resultados_clinicos' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluacion_id')->unique()->constrained('evaluaciones')->cascadeOnDelete();
                $table->unsignedTinyInteger('puntaje_total');
                $table->enum('nivel_riesgo', ['nulo', 'leve', 'moderado', 'severo']);
                $table->timestamps();
            },
            'alertas' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluacion_id')->unique()->constrained('evaluaciones')->cascadeOnDelete();
                $table->enum('estado', ['generada', 'asignada_psicologo', 'atendida'])->default('generada');
                $table->timestamps();
            },
            'diagnosticos' => function (Blueprint $table) {
                $table->id();
                $table->foreignId('evaluacion_id')->unique()->constrained('evaluaciones')->cascadeOnDelete();
                $table->foreignId('psicologo_id')->constrained('psicologos');
                $table->text('impresion_diagnostica');
                $table->text('retroalimentacion_estudiante')->nullable();
                $table->boolean('requiere_derivacion')->default(false);
                $table->timestamps();
            },
            'analisis_nlp' => function (Blueprint $table) {
                $table->id();
                $table->string('codigo_anonimo', 64);
                $table->foreign('codigo_anonimo')->references('codigo_anonimo')->on('estudiantes')->cascadeOnDelete();
                $table->text('texto_ingresado');
                $table->string('etiqueta_roberta', 50);
                $table->decimal('score_confianza', 5, 4);
                $table->boolean('requiere_atencion')->default(false);
                $table->timestamps();
            },
        ];

        // Revisar todas las tablas presentes antes de crear ninguna. Un esquema
        // parcial incompatible requiere revisión, no una reparación implícita.
        foreach ($definitions as $name => $definition) {
            if (Schema::hasTable($name)) {
                $blueprint = new Blueprint(Schema::getConnection(), $name);
                $definition($blueprint);
                $expected = array_map(fn ($column) => $column->name, $blueprint->getColumns());
                if (! Schema::hasColumns($name, $expected)) {
                    throw new RuntimeException('Esquema base incompatible en la tabla '.$name.'. Revisar sus columnas antes de migrar.');
                }
            }
        }

        foreach ($definitions as $name => $definition) {
            if (! Schema::hasTable($name)) {
                Schema::create($name, $definition);
            }
        }

        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'acepto_consentimiento')) {
                $table->boolean('acepto_consentimiento')->default(false);
            }
            if (! Schema::hasColumn('users', 'consentimiento_aceptado_at')) {
                $table->timestamp('consentimiento_aceptado_at')->nullable();
            }
        });
    }

    public function down(): void
    {
        // Puede haber adoptado tablas importadas. Nunca borrarlas en rollback.
        throw new RuntimeException('El esquema base no se revierte automáticamente: puede contener tablas y datos anteriores a esta migración.');
    }
};
