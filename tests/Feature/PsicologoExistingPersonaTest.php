<?php

namespace Tests\Feature;

use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\Tutor;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PsicologoExistingPersonaTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $tutorUser;
    private Persona $persona;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->admin = User::factory()->create(['acepto_consentimiento' => true]);
        $this->admin->assignRole('admin');
        $this->tutorUser = User::factory()->create(['acepto_consentimiento' => true]);
        $this->tutorUser->assignRole('tutor');
        $this->persona = Persona::create([
            'user_id' => $this->tutorUser->id, 'nombre' => 'Ana', 'apellido_paterno' => 'Prueba',
            'fecha_nacimiento' => '1990-01-01', 'genero' => 'femenino',
        ]);
        Tutor::create(['persona_id' => $this->persona->id, 'numero_empleado' => 'T-001']);
    }

    private function payload(): array
    {
        return ['tipo_registro' => 'existente', 'persona_id' => $this->persona->id,
            'cedula_profesional' => '1234567', 'telefono' => '5512345678'];
    }

    private function registerSharedProfile(): void
    {
        $this->actingAs($this->admin)->post(route('admin.psicologos.store'), $this->payload())
            ->assertSessionHasNoErrors()->assertRedirect(route('admin.psicologos.index'));
    }

    public function test_existing_person_keeps_account_and_roles_and_only_missing_contact_is_filled(): void
    {
        $hash = $this->tutorUser->password;
        $this->actingAs($this->admin)->post(route('admin.psicologos.store'), $this->payload() + [
            'email' => 'injected@example.test', 'password' => 'Injected123!', 'nombre' => 'Changed',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('personas', 1);
        $this->assertDatabaseHas('psicologos', ['persona_id' => $this->persona->id]);
        $this->assertSame('Ana', $this->persona->fresh()->nombre);
        $this->assertSame('5512345678', $this->persona->fresh()->telefono);
        $this->assertSame($hash, $this->tutorUser->fresh()->password);
        $this->assertSame($this->tutorUser->email, $this->tutorUser->fresh()->email);
        $this->assertTrue($this->tutorUser->fresh()->hasAllRoles(['tutor', 'psicologo']));
    }

    public function test_existing_phone_is_not_overwritten(): void
    {
        $this->persona->update(['telefono' => '5598765432']);
        $this->registerSharedProfile();
        $this->assertSame('5598765432', $this->persona->fresh()->telefono);
    }

    public function test_duplicate_profile_and_invalid_person_or_phone_are_rejected(): void
    {
        $this->actingAs($this->admin)->post(route('admin.psicologos.store'), array_replace($this->payload(), ['persona_id' => 99999]))
            ->assertSessionHasErrors('persona_id');
        $this->post(route('admin.psicologos.store'), array_replace($this->payload(), ['telefono' => '123']))
            ->assertSessionHasErrors('telefono');
        $this->assertDatabaseCount('psicologos', 0);
        $this->registerSharedProfile();
        $this->post(route('admin.psicologos.store'), array_replace($this->payload(), ['cedula_profesional' => '7654321']))
            ->assertSessionHasErrors('persona_id');
        $this->assertDatabaseCount('psicologos', 1);
    }

    public function test_tutor_cannot_assign_itself_a_psychologist_profile(): void
    {
        $this->actingAs($this->tutorUser)->post(route('admin.psicologos.store'), $this->payload())->assertForbidden();
        $this->assertDatabaseCount('psicologos', 0);
    }

    public function test_duplicate_license_is_rejected_and_linked_people_are_not_offered_again(): void
    {
        $this->registerSharedProfile();
        $this->get(route('admin.psicologos.create'))->assertOk()->assertDontSee($this->tutorUser->email);
        $other = User::factory()->create();
        $person = Persona::create([
            'user_id' => $other->id, 'nombre' => 'Otra', 'apellido_paterno' => 'Persona',
            'fecha_nacimiento' => '1990-01-01', 'genero' => 'otro',
        ]);
        $this->post(route('admin.psicologos.store'), array_replace($this->payload(), ['persona_id' => $person->id]))
            ->assertSessionHasErrors('cedula_profesional');
        $this->assertFalse($other->fresh()->hasRole('psicologo'));
    }

    public function test_school_can_remove_shared_tutor_without_removing_psychologist(): void
    {
        $this->registerSharedProfile();
        $this->delete(route('control_escolar.tutores.destroy', $this->persona->tutor))->assertRedirect();
        $this->assertNotNull($this->persona->fresh()->psicologo);
        $this->assertTrue($this->tutorUser->fresh()->hasRole('psicologo'));
        $this->assertFalse($this->tutorUser->fresh()->hasRole('tutor'));
    }

    public function test_shared_user_can_open_both_dashboards_and_sees_both_links(): void
    {
        $this->registerSharedProfile();
        $this->actingAs($this->tutorUser->fresh());
        foreach (['tutor.dashboard', 'psicologo.dashboard'] as $route) {
            $this->get(route($route))->assertOk()
                ->assertSee(route('tutor.dashboard'), false)
                ->assertSee(route('psicologo.dashboard'), false);
        }
    }

    public function test_removing_psychologist_preserves_tutor_and_account(): void
    {
        $this->registerSharedProfile();
        $this->delete(route('admin.psicologos.destroy', Psicologo::firstOrFail()))->assertRedirect();
        $this->assertDatabaseCount('psicologos', 0);
        $this->assertNotNull($this->persona->fresh()->tutor);
        $this->assertTrue($this->tutorUser->fresh()->hasRole('tutor'));
        $this->assertFalse($this->tutorUser->fresh()->hasRole('psicologo'));
    }

    public function test_removing_shared_tutor_preserves_psychologist_and_account(): void
    {
        $this->registerSharedProfile();
        $this->delete(route('admin.tutores.destroy', $this->persona->tutor))->assertRedirect();
        $this->assertNotNull($this->persona->fresh()->psicologo);
        $this->assertTrue($this->tutorUser->fresh()->hasRole('psicologo'));
        $this->assertFalse($this->tutorUser->fresh()->hasRole('tutor'));
    }

    public function test_forms_render_and_new_people_can_still_be_registered(): void
    {
        $this->actingAs($this->admin)->get(route('admin.psicologos.create'))->assertOk()->assertSee($this->tutorUser->email);
        $this->post(route('admin.psicologos.store'), [
            'tipo_registro' => 'nueva', 'nombre' => 'Nueva', 'apellido_paterno' => 'Persona',
            'fecha_nacimiento' => '1990-01-01', 'genero' => 'otro', 'telefono' => null,
            'email' => 'new@example.test', 'password' => 'Prueba2026!', 'password_confirmation' => 'Prueba2026!',
            'cedula_profesional' => '9999999',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('users', 3);
        $this->assertDatabaseCount('personas', 2);
        $this->get(route('admin.psicologos.index'))->assertOk()->assertSee('Seleccionar persona existente');
    }
}
