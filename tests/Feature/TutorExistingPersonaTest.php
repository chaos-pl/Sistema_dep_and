<?php

namespace Tests\Feature;

use App\Models\Persona;
use App\Models\Psicologo;
use App\Models\Tutor;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class TutorExistingPersonaTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function account(string $role): User
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->assignRole($role);
        return $user;
    }

    private function psychologist(): Persona
    {
        $user = $this->account('psicologo');
        $persona = Persona::create([
            'user_id' => $user->id, 'nombre' => 'Prueba', 'apellido_paterno' => 'Compartida',
            'fecha_nacimiento' => '1990-01-01', 'genero' => 'otro',
        ]);
        Psicologo::create(['persona_id' => $persona->id, 'cedula_profesional' => 'CED-1']);
        return $persona;
    }

    public static function areas(): array
    {
        return [['admin'], ['control_escolar']];
    }

    #[DataProvider('areas')]
    public function test_modal_registration_preserves_existing_psychologist_and_enables_tutor_routes(string $area): void
    {
        $operator = $this->account($area);
        $persona = $this->psychologist();
        $hash = $persona->user->password;
        $email = $persona->user->email;
        $this->actingAs($operator)->get(route($area.'.tutores.index'))->assertOk()
            ->assertSee('id="modalCreateTutor"', false)->assertSee('Seleccionar persona existente');
        $this->get(route($area.'.tutores.create'))->assertRedirect(route($area.'.tutores.index', ['crear' => 1]));
        $this->from(route($area.'.tutores.index'))->post(route($area.'.tutores.store'), [
            'tipo_registro' => 'existente', 'persona_id' => $persona->id, 'numero_empleado' => 'EMP-1',
            'telefono' => '5512345678', 'nombre' => 'Injected', 'email' => 'changed@example.test',
            'password' => 'Injected123!',
        ])->assertSessionHasNoErrors()->assertRedirect(route($area.'.tutores.index'));
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('personas', 1);
        $this->assertDatabaseHas('tutores', ['persona_id' => $persona->id, 'numero_empleado' => 'EMP-1']);
        $this->assertSame('5512345678', $persona->fresh()->telefono);
        $this->assertSame('Prueba', $persona->fresh()->nombre);
        $this->assertSame($hash, $persona->user->fresh()->password);
        $this->assertSame($email, $persona->user->fresh()->email);
        $this->assertTrue($persona->user->fresh()->hasAllRoles(['tutor', 'psicologo']));
        $this->actingAs($persona->user->fresh())->get(route('tutor.grupos.index'))->assertOk();
        $this->get(route('tutor.dashboard'))->assertOk();
        $this->get(route('psicologo.dashboard'))->assertOk();
    }

    #[DataProvider('areas')]
    public function test_duplicate_and_soft_deleted_profiles_are_rejected(string $area): void
    {
        $operator = $this->account($area);
        $persona = $this->psychologist();
        $tutor = Tutor::create(['persona_id' => $persona->id, 'numero_empleado' => 'EMP-1']);
        $data = ['tipo_registro' => 'existente', 'persona_id' => $persona->id, 'numero_empleado' => 'EMP-2'];
        $this->actingAs($operator)->post(route($area.'.tutores.store'), $data)->assertSessionHasErrors('persona_id');
        $tutor->delete();
        $this->post(route($area.'.tutores.store'), $data)->assertSessionHasErrors('persona_id');
        $this->assertDatabaseCount('tutores', 1);
        $this->get(route($area.'.tutores.index'))->assertOk()->assertDontSee($persona->user->email);
    }

    #[DataProvider('areas')]
    public function test_required_employee_number_phone_and_unique_number_are_validated(string $area): void
    {
        $this->actingAs($this->account($area));
        $persona = $this->psychologist();
        $data = ['tipo_registro' => 'existente', 'persona_id' => $persona->id];
        $this->post(route($area.'.tutores.store'), $data + ['telefono' => '123'])->assertSessionHasErrors(['numero_empleado', 'telefono']);
        $this->post(route($area.'.tutores.store'), array_replace($data, ['persona_id' => 999999, 'numero_empleado' => 'EMP-1']))
            ->assertSessionHasErrors('persona_id');
        $this->assertDatabaseCount('tutores', 0);
        $persona->update(['telefono' => '5598765432']);
        $this->post(route($area.'.tutores.store'), $data + ['numero_empleado' => 'EMP-1', 'telefono' => '5512345678'])
            ->assertSessionHasNoErrors();
        $this->assertSame('5598765432', $persona->fresh()->telefono);
        $other = Persona::create(['user_id' => $this->account('psicologo')->id, 'nombre' => 'Otra', 'apellido_paterno' => 'Persona', 'fecha_nacimiento' => '1990-01-01', 'genero' => 'otro']);
        $this->post(route($area.'.tutores.store'), ['tipo_registro' => 'existente', 'persona_id' => $other->id, 'numero_empleado' => 'EMP-1'])
            ->assertSessionHasErrors('numero_empleado');
    }

    #[DataProvider('areas')]
    public function test_new_person_registration_still_works(string $area): void
    {
        $this->actingAs($this->account($area))->post(route($area.'.tutores.store'), [
            'tipo_registro' => 'nueva', 'nombre' => 'Nuevo', 'apellido_paterno' => 'Tutor',
            'fecha_nacimiento' => '1990-01-01', 'genero' => 'otro', 'numero_empleado' => 'EMP-NEW',
            'email' => 'new@example.test', 'password' => 'Prueba2026!', 'password_confirmation' => 'Prueba2026!',
        ])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('tutores', 1);
    }

    public function test_school_without_creation_permission_cannot_link_a_person(): void
    {
        $operator = $this->account('control_escolar');
        $persona = $this->psychologist();
        Role::findByName('control_escolar')->revokePermissionTo('tutores.crear');
        $this->actingAs($operator)->post(route('control_escolar.tutores.store'), [
            'tipo_registro' => 'existente', 'persona_id' => $persona->id, 'numero_empleado' => 'EMP-1',
        ])->assertForbidden();
        $this->assertDatabaseCount('tutores', 0);
    }
}
