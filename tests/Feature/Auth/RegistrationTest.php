<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $response = $this->post('/register', [
            'nombre' => 'Prueba', 'apellido_paterno' => 'Sintetica',
            'fecha_nacimiento' => '2000-01-01', 'genero' => 'otro',
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('estudiante'));
        $this->assertFalse($user->acepto_consentimiento);
        $this->assertDatabaseHas('personas', ['user_id' => $user->id, 'nombre' => 'Prueba']);
        $this->assertDatabaseCount('estudiantes', 0);
        $response->assertRedirect(route('consentimiento.create', absolute: false));
    }
}
