<?php

namespace Tests\Feature;

use App\Models\{Carrera, CicloEscolar, Grupo, GrupoTutor, Persona, Tutor, User};
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GroupTutorReassignmentTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Tutor $oldTutor;
    private Tutor $newTutor;
    private Grupo $grupo;
    private CicloEscolar $cycle;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->admin = User::factory()->create(['acepto_consentimiento' => true]);
        $this->admin->assignRole('admin');
        foreach (['oldTutor', 'newTutor'] as $property) {
            $user = User::factory()->create(['acepto_consentimiento' => true]);
            $user->assignRole('tutor');
            $persona = Persona::create(['user_id' => $user->id, 'nombre' => $property, 'apellido_paterno' => 'Prueba', 'fecha_nacimiento' => '1990-01-01', 'genero' => 'otro']);
            $this->$property = Tutor::create(['persona_id' => $persona->id, 'numero_empleado' => $property]);
        }
        $this->cycle = CicloEscolar::create(['nombre' => 'Actual', 'fecha_inicio' => today()->subMonth(), 'fecha_fin' => today()->addMonth(), 'estado' => 'activo']);
        $career = Carrera::create(['nombre' => 'Prueba', 'estado' => 'activo']);
        $this->grupo = Grupo::create(['nombre' => '602', 'periodo' => 'Prueba', 'estado' => 'activo', 'carrera_id' => $career->id, 'ciclo_escolar_id' => $this->cycle->id, 'tutor_id' => $this->oldTutor->id]);
        GrupoTutor::create(['grupo_id' => $this->grupo->id, 'tutor_id' => $this->oldTutor->id, 'ciclo_escolar_id' => null, 'estado' => 'activo']);
    }

    private function data(?int $tutor): array
    {
        return ['nombre' => '602', 'periodo' => 'Prueba', 'estado' => 'activo', 'carrera_id' => $this->grupo->carrera_id, 'ciclo_escolar_id' => $this->cycle->id, 'tutor_id' => $tutor];
    }

    public static function areas(): array { return [['admin'], ['control_escolar']]; }

    #[DataProvider('areas')]
    public function test_edit_replaces_access_and_can_reassign_back_without_duplicates(string $area): void
    {
        $this->actingAs($this->admin)->put(route($area.'.grupos.update', $this->grupo), $this->data($this->newTutor->id))->assertSessionHasNoErrors();
        $this->assertFalse(Grupo::visibleToTutor($this->oldTutor)->whereKey($this->grupo->id)->exists());
        $this->assertTrue(Grupo::visibleToTutor($this->newTutor)->whereKey($this->grupo->id)->exists());
        $this->actingAs($this->oldTutor->persona->user)->get(route('tutor.grupos.show', $this->grupo))->assertForbidden();
        $this->actingAs($this->newTutor->persona->user)->get(route('tutor.grupos.show', $this->grupo))->assertOk();
        $this->actingAs($this->admin)->put(route($area.'.grupos.update', $this->grupo), $this->data($this->oldTutor->id))->assertSessionHasNoErrors();
        $this->assertTrue(Grupo::visibleToTutor($this->oldTutor)->whereKey($this->grupo->id)->exists());
        $this->assertFalse(Grupo::visibleToTutor($this->newTutor)->whereKey($this->grupo->id)->exists());
        $this->assertSame(1, GrupoTutor::where('grupo_id', $this->grupo->id)->where('estado', 'activo')->count());
    }

    public function test_removing_tutor_revokes_access_and_restores_a_deleted_assignment_safely(): void
    {
        $this->actingAs($this->admin)->put(route('control_escolar.grupos.update', $this->grupo), $this->data(null))->assertSessionHasNoErrors();
        $this->assertNull($this->grupo->fresh()->tutor_id);
        $this->assertFalse(Grupo::visibleToTutor($this->oldTutor)->exists());
        $payload = ['grupo_id' => $this->grupo->id, 'tutor_id' => $this->newTutor->id, 'ciclo_escolar_id' => $this->cycle->id];
        $this->post(route('control_escolar.asignaciones.store'), $payload)->assertSessionHasNoErrors();
        $assignment = GrupoTutor::where('tutor_id', $this->newTutor->id)->firstOrFail();
        $this->delete(route('control_escolar.asignaciones.destroy', $assignment))->assertRedirect();
        $this->assertNull($this->grupo->fresh()->tutor_id);
        $this->post(route('control_escolar.asignaciones.store'), $payload)->assertSessionHasNoErrors();
        $this->assertSame($this->newTutor->id, $this->grupo->fresh()->tutor_id);
        $this->assertTrue(Grupo::visibleToTutor($this->newTutor)->exists());
        $this->assertSame(1, GrupoTutor::withTrashed()->where('tutor_id', $this->newTutor->id)->count());
    }

    public function test_cycle_and_group_details_are_linked_from_dashboard(): void
    {
        $this->actingAs($this->admin)->get(route('control_escolar.dashboard'))->assertOk()
            ->assertSee(route('control_escolar.ciclos-escolares.show', $this->cycle), false)
            ->assertSee(route('control_escolar.grupos.show', $this->grupo), false);
        $this->get(route('control_escolar.ciclos-escolares.show', $this->cycle))->assertOk()
            ->assertSee(route('control_escolar.tutores.show', $this->oldTutor), false);
        $this->actingAs($this->oldTutor->persona->user)->get(route('control_escolar.ciclos-escolares.show', $this->cycle))->assertForbidden();
    }

    public function test_tutor_edit_and_show_use_modals_and_update_contact(): void
    {
        $operator = User::factory()->create(['acepto_consentimiento' => true]);
        $operator->assignRole('control_escolar');
        $this->actingAs($operator)->get(route('control_escolar.tutores.index'))->assertOk()
            ->assertSee('id="modalEditTutor'.$this->newTutor->id.'"', false)
            ->assertSee('id="modalShowTutor'.$this->newTutor->id.'"', false);
        $this->get(route('control_escolar.tutores.edit', $this->newTutor))->assertRedirect(route('control_escolar.tutores.index', ['editar' => $this->newTutor->id, 'page' => 1]));
        $person = $this->newTutor->persona;
        $data = ['nombre' => 'Actualizado', 'apellido_paterno' => 'Prueba', 'fecha_nacimiento' => '1990-01-01', 'genero' => 'otro', 'numero_empleado' => $this->newTutor->numero_empleado, 'email' => $person->user->email, 'telefono' => '5512345678'];
        $this->put(route('control_escolar.tutores.update', $this->newTutor), $data)->assertSessionHasNoErrors();
        $this->assertSame('5512345678', $person->fresh()->telefono);
    }

    public function test_cycle_update_uses_the_requested_model(): void
    {
        $this->actingAs($this->admin)->put(route('control_escolar.ciclos-escolares.update', $this->cycle), ['nombre' => 'Renombrado', 'fecha_inicio' => today()->subMonth()->toDateString(), 'fecha_fin' => today()->addMonth()->toDateString(), 'estado' => 'activo'])->assertSessionHasNoErrors();
        $this->assertSame('Renombrado', $this->cycle->fresh()->nombre);
    }

    public function test_removing_history_does_not_revoke_current_tutor_and_closed_cycle_denies_access(): void
    {
        $oldAssignment = GrupoTutor::firstOrFail();
        $this->actingAs($this->admin)->put(route('control_escolar.grupos.update', $this->grupo), $this->data($this->newTutor->id))->assertSessionHasNoErrors();
        $this->delete(route('control_escolar.asignaciones.destroy', $oldAssignment))->assertRedirect();
        $this->assertSame($this->newTutor->id, $this->grupo->fresh()->tutor_id);
        $this->assertTrue(Grupo::visibleToTutor($this->newTutor)->exists());
        $this->cycle->update(['estado' => 'cerrado']);
        $this->assertFalse(Grupo::visibleToTutor($this->newTutor)->exists());
    }

    public function test_authorized_operator_sees_delete_confirmation_modal(): void
    {
        $this->actingAs($this->admin)->get(route('control_escolar.tutores.index'))->assertOk()
            ->assertSee('id="modalDeleteTutor'.$this->newTutor->id.'"', false);
    }
}
