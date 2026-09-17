<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\Estudiante;
use App\Models\Grupo;
use App\Models\Instrumento;
use App\Models\MovimientoEstudiante;
use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\Tutor;
use App\Models\User;
use App\Services\StudentGroupAssignmentService;
use Database\Seeders\Dass21Seeder;
use Database\Seeders\InstrumentosBaseSeeder;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AcademicSchemaTest extends TestCase
{
    private int $sequence = 0;

    protected function setUp(): void
    {
        parent::setUp();
        // Migraciones reales, exclusivamente en una conexión SQLite aislada en memoria.
        config([
            'database.default' => 'dass21_testing',
            'database.connections.dass21_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true],
            'cache.default' => 'array', 'session.driver' => 'array',
            'mail.default' => 'array', 'queue.default' => 'sync',
        ]);
        DB::purge('dass21_testing');
        $this->artisan('migrate', ['--database' => 'dass21_testing', '--force' => true])->assertExitCode(0);
        $this->seed([RolesAndPermissionsSeeder::class, Dass21Seeder::class]);
        $this->withoutVite();
    }

    private function account(string $role): User
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole($role);
        $persona = Persona::create([
            'user_id' => $user->id, 'nombre' => 'Persona de prueba', 'apellido_paterno' => 'Sintética',
            'fecha_nacimiento' => '2000-01-01', 'genero' => 'otro',
        ]);
        if ($role === 'tutor') {
            Tutor::create(['persona_id' => $persona->id, 'numero_empleado' => 'T-'.$user->id]);
        }
        if ($role === 'psicologo') {
            Psicologo::create(['persona_id' => $persona->id, 'cedula_profesional' => 'P-'.$user->id]);
        }

        return $user->fresh();
    }

    private function group(?Tutor $tutor = null): Grupo
    {
        $tutor ??= $this->account('tutor')->persona->tutor;
        $carrera = Carrera::create(['nombre' => 'Carrera de prueba', 'estado' => 'activo']);

        return Grupo::create(['nombre' => 'Grupo '.++$this->sequence, 'carrera_id' => $carrera->id, 'tutor_id' => $tutor->id, 'periodo' => 'Prueba', 'estado' => 'activo']);
    }

    private function student(?Grupo $grupo = null): Estudiante
    {
        $user = $this->account('estudiante');

        return Estudiante::create([
            'persona_id' => $user->persona->id, 'matricula' => 'M-'.$user->id,
            'grupo_id' => ($grupo ?? $this->group())->id, 'codigo_anonimo' => 'TEST-'.$user->id, 'estado' => 'activo',
        ]);
    }

    private function nullable(string $table, string $column): bool
    {
        return collect(Schema::getColumns($table))->firstWhere('name', $column)['nullable'];
    }

    public function test_instrument_catalog_is_repeatable_and_preserves_existing_names(): void
    {
        $existing = Instrumento::create(['acronimo' => 'phq9', 'nombre' => 'Nombre personalizado']);
        $this->seed(InstrumentosBaseSeeder::class);
        $this->seed(InstrumentosBaseSeeder::class);
        $this->assertSame('Nombre personalizado', $existing->fresh()->nombre);
        $this->assertSame(1, Instrumento::whereRaw('LOWER(acronimo) = ?', ['phq9'])->count());
        $this->assertSame(1, Instrumento::where('acronimo', 'GAD7')->count());
    }

    public function test_all_migrations_build_the_complete_schema_and_can_run_again(): void
    {
        $expected = ['users', 'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs',
            'migrations', 'roles', 'permissions', 'model_has_roles', 'model_has_permissions', 'role_has_permissions',
            'personas', 'carreras', 'tutores', 'psicologos', 'grupos', 'estudiantes', 'instrumentos', 'evaluaciones',
            'respuestas', 'resultados_clinicos', 'alertas', 'diagnosticos', 'analisis_nlp', 'ciclos_escolares',
            'grupo_tutor', 'movimientos_estudiantes', 'dass21_questions', 'dass21_evaluations', 'dass21_evaluation_answers', 'casos_atencion', 'seguimientos_caso', 'avisos'];
        $this->assertEqualsCanonicalizing($expected, array_column(Schema::getTables(), 'name'));
        $this->assertTrue(Schema::hasColumns('users', ['acepto_consentimiento', 'consentimiento_aceptado_at']));
        $this->assertTrue(Schema::hasColumn('dass21_evaluations', 'evaluacion_id'));
        $this->assertTrue(Schema::hasColumn('analisis_nlp', 'estado_analisis'));
        $this->assertTrue($this->nullable('estudiantes', 'grupo_id'));
        $this->assertTrue($this->nullable('grupos', 'tutor_id'));
        $before = DB::table('migrations')->count();
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->assertSame($before, DB::table('migrations')->count());
    }

    public function test_existing_imported_schema_is_adopted_without_changing_rows(): void
    {
        config(['database.default' => 'import_testing',
            'database.connections.import_testing' => ['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]]);
        DB::purge('import_testing');
        DB::unprepared(file_get_contents(base_path('tests/Fixtures/dass21-schema.sql')));
        (require database_path('migrations/2026_09_09_000001_link_dass21_to_evaluaciones.php'))->up();
        (require database_path('migrations/2026_09_10_000001_add_processing_to_analisis_nlp.php'))->up();
        (require database_path('migrations/2026_09_10_000003_create_casos_atencion.php'))->up();
        (require database_path('migrations/2026_09_15_000001_add_evolution_reports_and_notices.php'))->up();
        foreach (glob(database_path('migrations/*.php')) as $file) {
            if (! str_contains($file, 'create_domain_baseline') && ! str_contains($file, 'allow_pending_academic_assignments')) {
                DB::table('migrations')->insert(['migration' => basename($file, '.php'), 'batch' => 1]);
            }
        }
        $this->seed(RolesAndPermissionsSeeder::class);
        $student = $this->student();
        $before = $student->fresh()->getAttributes();
        $this->assertFalse($this->nullable('estudiantes', 'grupo_id'));
        $this->artisan('migrate', ['--force' => true])->assertExitCode(0);
        $this->assertSame($before, $student->fresh()->getAttributes());
        $this->assertTrue($this->nullable('estudiantes', 'grupo_id'));
        $this->assertTrue($this->nullable('grupos', 'tutor_id'));
        $this->assertCount(2, Schema::getForeignKeys('estudiantes'));
        $this->assertCount(3, Schema::getForeignKeys('grupos'));
        $student->update(['grupo_id' => null]);
        $this->assertNull($student->fresh()->grupo_id);
    }

    public function test_control_escolar_can_create_a_student_without_a_group(): void
    {
        $this->actingAs($this->account('control_escolar'))->post(route('control_escolar.estudiantes.store'), [
            'nombre' => 'Estudiante sintético', 'apellido_paterno' => 'Prueba',
            'fecha_nacimiento' => '2000-01-01', 'genero' => 'prefiero_no_decirlo',
            'matricula' => 'SIN-GRUPO-1', 'email' => 'sin-grupo@example.test',
            'password' => 'Testing-only-123!', 'password_confirmation' => 'Testing-only-123!', 'grupo_id' => null,
        ])->assertSessionHasNoErrors()->assertRedirect();
        $student = Estudiante::where('matricula', 'SIN-GRUPO-1')->firstOrFail();
        $this->assertNull($student->grupo_id);
        $this->assertNotEmpty($student->codigo_anonimo);
        $this->get(route('control_escolar.pendientes.index'))->assertOk()->assertSee('Estudiante sintético');
        $this->actingAs($student->persona->user)->get(route('consentimiento.create'))->assertOk();
    }

    public function test_control_escolar_can_create_a_group_without_a_tutor_and_rejects_oversized_fields(): void
    {
        $carrera = Carrera::create(['nombre' => 'Carrera sintética']);
        $data = ['nombre' => 'Grupo pendiente', 'carrera_id' => $carrera->id, 'tutor_id' => null, 'periodo' => '2026-2'];
        $this->actingAs($this->account('control_escolar'))->post(route('control_escolar.grupos.store'), $data)
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull(Grupo::where('nombre', 'Grupo pendiente')->firstOrFail()->tutor_id);
        $this->post(route('control_escolar.grupos.store'), array_replace($data, ['nombre' => str_repeat('x', 51), 'periodo' => str_repeat('x', 21)]))
            ->assertSessionHasErrors(['nombre', 'periodo']);
        $this->assertDatabaseCount('grupos', 1);
    }

    public function test_assign_change_and_remove_are_recorded_once_with_the_real_origin(): void
    {
        $actor = $this->account('control_escolar');
        $student = $this->student();
        $original = $student->grupo_id;
        $group = $this->group();
        $this->actingAs($actor)->put(route('control_escolar.pendientes.asignarGrupo', $student), ['grupo_id' => $group->id])
            ->assertSessionHasNoErrors()->assertRedirect();
        $move = MovimientoEstudiante::sole();
        $this->assertSame($original, $move->grupo_origen_id);
        $this->assertSame('cambiado', $move->accion);
        $this->put(route('control_escolar.pendientes.asignarGrupo', $student), ['grupo_id' => $group->id])->assertRedirect();
        $this->assertDatabaseCount('movimientos_estudiantes', 1);
        $this->delete(route('control_escolar.estudiantes.quitarGrupo', $student), ['motivo' => 'Retiro sintético'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull($student->fresh()->grupo_id);
        $this->delete(route('control_escolar.estudiantes.quitarGrupo', $student), ['motivo' => 'Sin cambios'])->assertRedirect();
        $this->assertDatabaseCount('movimientos_estudiantes', 2);
        $this->put(route('control_escolar.estudiantes.updateGrupo', $student), ['grupo_id' => $group->id, 'motivo' => 'Reasignación sintética'])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('movimientos_estudiantes', ['estudiante_id' => $student->id, 'grupo_origen_id' => null,
            'grupo_destino_id' => $group->id, 'accion' => 'asignado', 'realizado_por' => $actor->id]);
    }

    public function test_failed_history_write_rolls_back_the_group_change(): void
    {
        $student = $this->student();
        $origin = $student->grupo_id;
        try {
            // La FK del actor falla después del cambio de grupo.
            app(StudentGroupAssignmentService::class)->assign($student, null, 99999999, 'Prueba de transacción');
            $this->fail('Debió rechazarse el actor inexistente');
        } catch (QueryException) {
            $this->assertSame($origin, $student->fresh()->grupo_id);
            $this->assertDatabaseCount('movimientos_estudiantes', 0);
        }
    }

    public function test_inactive_or_deleted_groups_are_rejected_and_tutors_cannot_assign(): void
    {
        $student = $this->student();
        $group = $this->group();
        $actor = $this->account('control_escolar');
        $group->update(['estado' => 'inactivo']);
        $this->actingAs($actor)->put(route('control_escolar.pendientes.asignarGrupo', $student), ['grupo_id' => $group->id])
            ->assertSessionHasErrors('grupo_id');
        $group->update(['estado' => 'activo']);
        $group->delete();
        $this->put(route('control_escolar.estudiantes.updateGrupo', $student), ['grupo_id' => $group->id, 'motivo' => 'Prueba'])
            ->assertSessionHasErrors('grupo_id');
        $this->actingAs($this->account('tutor'))->delete(route('control_escolar.estudiantes.quitarGrupo', $student),
            ['motivo' => 'Prueba'])->assertForbidden();
        $this->assertDatabaseCount('movimientos_estudiantes', 0);
    }

    public function test_admin_removal_is_also_atomic_and_recorded(): void
    {
        $student = $this->student();
        $this->actingAs($this->account('admin'))->put(route('admin.estudiantes.updateGrupo', $student), ['grupo_id' => null])
            ->assertSessionHasNoErrors()->assertRedirect();
        $this->assertNull($student->fresh()->grupo_id);
        $this->assertDatabaseHas('movimientos_estudiantes', ['estudiante_id' => $student->id, 'accion' => 'quitado']);
    }

    public function test_nullable_assignments_still_enforce_foreign_keys_and_unique_student_codes(): void
    {
        $student = $this->student();
        foreach ([['grupo_id' => 99999999], ['persona_id' => 99999999]] as $change) {
            try {
                $student->update($change);
                $this->fail('Debió rechazarse la referencia inexistente');
            } catch (QueryException) {
                $student->refresh();
            }
        }
        $other = $this->student();
        $this->expectException(QueryException::class);
        $other->update(['codigo_anonimo' => $student->codigo_anonimo]);
    }

    public function test_rollback_is_refused_if_pending_assignments_exist_without_changing_either_table(): void
    {
        $student = $this->student();
        $student->update(['grupo_id' => null]);
        $migration = require database_path('migrations/2026_09_10_000002_allow_pending_academic_assignments.php');
        try {
            $migration->down();
            $this->fail('Debió proteger las asignaciones pendientes');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('No se puede revertir', $exception->getMessage());
        }
        $this->assertTrue($this->nullable('estudiantes', 'grupo_id'));
        $this->assertTrue($this->nullable('grupos', 'tutor_id'));
        $this->assertNull($student->fresh()->grupo_id);
    }

    public function test_baseline_refuses_to_drop_adopted_tables(): void
    {
        $migration = require database_path('migrations/2026_04_01_000000_create_domain_baseline.php');
        try {
            $migration->down();
            $this->fail('Debió proteger las tablas importadas');
        } catch (\RuntimeException $exception) {
            $this->assertStringContainsString('no se revierte automáticamente', $exception->getMessage());
        }
        $this->assertTrue(Schema::hasTable('estudiantes'));
    }
}
