<?php

namespace Tests\Feature;

use App\Models\Carrera;
use App\Models\Grupo;
use App\Models\Persona;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PendingExpedienteAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function account(string $role, bool $withPersona = false): User
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole($role);
        if ($withPersona) {
            Persona::create([
                'user_id' => $user->id, 'nombre' => 'Prueba', 'apellido_paterno' => 'Sintetica',
                'fecha_nacimiento' => '2000-01-01', 'genero' => 'otro',
            ]);
        }

        return $user;
    }

    public static function allowedRoles(): array
    {
        return [['admin'], ['control_escolar']];
    }

    #[DataProvider('allowedRoles')]
    public function test_authorized_roles_can_list_open_and_complete_an_expediente(string $role): void
    {
        $operator = $this->account($role);
        $student = $this->account('estudiante', true);
        $carrera = Carrera::create(['nombre' => 'Prueba', 'estado' => 'activo']);
        $group = Grupo::create(['nombre' => 'Prueba', 'carrera_id' => $carrera->id, 'periodo' => 'Prueba', 'estado' => 'activo']);

        $this->actingAs($operator)->get(route('admin.expedientes-pendientes.index'))->assertOk();
        $this->get(route('admin.expedientes-pendientes.edit', $student))->assertOk()->assertSee('Guardar Expediente');
        $this->get(route('control_escolar.pendientes.index'))->assertOk()
            ->assertSee(route('admin.expedientes-pendientes.update', $student), false);
        $this->put(route('admin.expedientes-pendientes.update', $student), [
            'matricula' => 'TEST-001', 'grupo_id' => $group->id,
        ])->assertSessionHasNoErrors()->assertRedirect(route('admin.expedientes-pendientes.index'));
        $this->assertDatabaseHas('estudiantes', [
            'persona_id' => $student->persona->id, 'grupo_id' => $group->id, 'matricula' => 'TEST-001',
        ]);
    }

    public static function blockedRoles(): array
    {
        return [['estudiante'], ['tutor'], ['psicologo']];
    }

    #[DataProvider('blockedRoles')]
    public function test_other_roles_remain_blocked_even_with_individual_permissions(string $role): void
    {
        $operator = $this->account($role);
        $operator->givePermissionTo(['estudiantes.ver_pendientes', 'estudiantes.asignar_grupo']);
        $student = $this->account('estudiante', true);
        $this->actingAs($operator)->get(route('admin.expedientes-pendientes.index'))->assertForbidden();
        $this->get(route('admin.expedientes-pendientes.edit', $student))->assertForbidden();
        $this->put(route('admin.expedientes-pendientes.update', $student), [])->assertForbidden();
        $this->assertDatabaseCount('estudiantes', 0);
    }

    public function test_guests_are_redirected_to_login(): void
    {
        $student = $this->account('estudiante', true);
        $this->get(route('admin.expedientes-pendientes.index'))->assertRedirect(route('login'));
        $this->get(route('admin.expedientes-pendientes.edit', $student))->assertRedirect(route('login'));
        $this->put(route('admin.expedientes-pendientes.update', $student), [])->assertRedirect(route('login'));
    }

    public function test_control_escolar_does_not_gain_other_admin_modules(): void
    {
        $this->actingAs($this->account('control_escolar'));
        foreach (['dashboard', 'usuarios', 'roles', 'permisos', 'personas', 'usuarios-sin-persona'] as $path) {
            $this->get('/admin/'.$path)->assertForbidden();
        }
    }

    public function test_read_only_control_escolar_cannot_assign_or_see_assignment_buttons(): void
    {
        Role::findByName('control_escolar', 'web')->revokePermissionTo('estudiantes.asignar_grupo');
        $student = $this->account('estudiante', true);
        $this->account('estudiante'); // Missing persona must not expose an admin-only link.
        $this->actingAs($this->account('control_escolar'));
        $this->get(route('admin.expedientes-pendientes.index'))->assertOk()
            ->assertDontSee('data-bs-target="#modalCompletar', false)->assertDontSee(route('admin.personas.index'), false);
        $this->get(route('admin.expedientes-pendientes.edit', $student))->assertOk()->assertDontSee('Guardar Expediente');
        $this->get(route('control_escolar.pendientes.index'))->assertOk()->assertDontSee('Crear Expediente')
            ->assertDontSee(route('admin.expedientes-pendientes.update', $student), false)
            ->assertDontSee(route('admin.personas.index'), false);
        $this->put(route('admin.expedientes-pendientes.update', $student), [])->assertForbidden();
        $this->assertDatabaseCount('estudiantes', 0);
    }

    public function test_control_escolar_needs_read_permission_and_admin_keeps_existing_access(): void
    {
        Role::findByName('control_escolar', 'web')->revokePermissionTo('estudiantes.ver_pendientes');
        $student = $this->account('estudiante', true);
        $this->actingAs($this->account('control_escolar'))->get(route('admin.expedientes-pendientes.index'))->assertForbidden();
        $this->get(route('admin.expedientes-pendientes.edit', $student))->assertForbidden();
        Role::findByName('admin', 'web')->revokePermissionTo(['estudiantes.ver_pendientes', 'estudiantes.asignar_grupo']);
        $this->actingAs($this->account('admin'))->get(route('admin.expedientes-pendientes.index'))->assertOk();
        $this->get(route('admin.expedientes-pendientes.edit', $student))->assertOk();
        // Validation instead of 403 confirms the existing admin write authorization.
        $this->put(route('admin.expedientes-pendientes.update', $student), [])->assertSessionHasErrors(['grupo_id', 'matricula']);
    }

    public function test_non_students_cannot_be_assigned_an_expediente(): void
    {
        $target = $this->account('psicologo', true);
        $this->actingAs($this->account('control_escolar'));
        $this->get(route('admin.expedientes-pendientes.edit', $target))->assertNotFound();
    }
}
