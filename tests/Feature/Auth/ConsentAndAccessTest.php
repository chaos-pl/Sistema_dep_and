<?php

namespace Tests\Feature\Auth;

use App\Models\Carrera;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ConsentAndAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_with_consent_reaches_dashboard_and_clears_failed_attempts(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        $key = Str::transliterate(Str::lower($user->email).'|127.0.0.1');
        $this->assertSame(1, RateLimiter::attempts($key));
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
        $this->assertSame(0, RateLimiter::attempts($key));
    }

    public function test_five_failures_block_even_a_correct_password_until_window_expires(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        for ($i = 0; $i < 5; $i++) {
            $this->post('/login', ['email' => $user->email, 'password' => 'wrong'])->assertSessionHasErrors('email');
        }
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->travel(61)->seconds();
        $this->post('/login', ['email' => $user->email, 'password' => 'password'])->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_consent_is_required_for_reads_and_writes_then_recorded_explicitly(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/perfil')->assertRedirect(route('consentimiento.create'));
        $this->patch('/perfil', ['name' => 'Blocked', 'email' => $user->email])->assertRedirect(route('consentimiento.create'));
        $this->assertNotSame('Blocked', $user->fresh()->name);
        $this->post('/consentimiento', ['acepta' => false])->assertSessionHasErrors('acepta');
        $this->assertFalse($user->fresh()->acepto_consentimiento);
        $this->post('/consentimiento', ['acepta' => true])->assertRedirect(route('dashboard'));
        $this->assertTrue($user->fresh()->acepto_consentimiento);
        $this->assertNotNull($user->fresh()->consentimiento_aceptado_at);
        $this->get('/perfil')->assertOk();
    }

    public function test_rejecting_consent_logs_out_without_accepting_it(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post('/consentimiento/rechazar')->assertRedirect(route('login'));
        $this->assertGuest();
        $this->assertFalse($user->fresh()->acepto_consentimiento);
    }

    public function test_registration_rejects_incomplete_data_and_ignores_injected_privileges(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $data = ['name' => 'Synthetic', 'email' => 'synthetic@example.test', 'password' => 'password', 'password_confirmation' => 'password'];
        $this->post('/register', $data)->assertSessionHasErrors(['nombre', 'apellido_paterno', 'fecha_nacimiento', 'genero']);
        $this->assertDatabaseCount('users', 0);
        $this->post('/register', $data + ['nombre' => 'Synthetic', 'apellido_paterno' => 'Test', 'fecha_nacimiento' => '2000-01-01',
            'genero' => 'otro', 'role' => 'admin', 'acepto_consentimiento' => true])->assertRedirect(route('consentimiento.create'));
        $user = User::firstOrFail();
        $this->assertTrue($user->hasRole('estudiante'));
        $this->assertFalse($user->hasRole('admin'));
        $this->assertFalse($user->acepto_consentimiento);
    }

    public function test_school_and_clinical_routes_reject_other_roles_and_revoked_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole('estudiante');
        $this->actingAs($user)->get(route('control_escolar.estudiantes.index'))->assertForbidden();
        $this->get(route('psicologo.casos.index'))->assertForbidden();
        $this->get(route('reportes.export'))->assertForbidden();
        $user->syncRoles(['control_escolar']);
        Role::findByName('control_escolar')->revokePermissionTo('estudiantes.ver');
        $this->actingAs($user->fresh())->get(route('control_escolar.estudiantes.index'))->assertForbidden();
    }

    public function test_school_catalog_writes_and_deletes_require_their_specific_permissions(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole('control_escolar');
        $role = Role::findByName('control_escolar');
        foreach (['carreras', 'grupos', 'ciclos_escolares', 'estudiantes', 'tutores'] as $resource) {
            $role->revokePermissionTo($resource.'.crear');
        }
        $role->revokePermissionTo('tutores.asignar_grupo');
        $this->actingAs($user->fresh());
        foreach (['carreras', 'grupos', 'ciclos-escolares', 'estudiantes', 'tutores', 'asignaciones'] as $resource) {
            $this->post('/control-escolar/'.$resource, [])->assertForbidden();
        }
        $career = Carrera::create(['nombre' => 'Synthetic', 'estado' => 'activo']);
        $this->delete('/control-escolar/carreras/'.$career->id)->assertForbidden();
        $this->assertNull($career->fresh()->deleted_at);
        $role->givePermissionTo('carreras.eliminar');
        $this->actingAs($user->fresh())->delete('/control-escolar/carreras/'.$career->id)->assertRedirect();
        $this->assertSoftDeleted('carreras', ['id' => $career->id]);
    }
}
