<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
        $response->assertSee('name="matricula"', false);
    }

    public function test_new_users_can_register(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $response = $this->post('/register', [
            'nombre' => 'Prueba', 'apellido_paterno' => 'Sintetica',
            'fecha_nacimiento' => '2000-01-01', 'genero' => 'otro',
            'name' => 'Test User',
            'matricula' => '202307036',
            'email' => 'test@example.com',
            'password' => 'Prueba2026!' ,
            'password_confirmation' => 'Prueba2026!',
        ]);

        $response->assertSessionHasNoErrors();
        $this->assertAuthenticated();
        $user = User::where('email', 'test@example.com')->firstOrFail();
        $this->assertTrue($user->hasRole('estudiante'));
        $this->assertFalse($user->acepto_consentimiento);
        $this->assertDatabaseHas('personas', ['user_id' => $user->id, 'nombre' => 'Prueba']);
        $this->assertDatabaseHas('estudiantes', [
            'persona_id' => $user->persona->id,
            'matricula' => '202307036',
            'grupo_id' => null,
            'estado' => 'activo',
        ]);
        $this->assertNotEmpty($user->persona->estudiante->codigo_anonimo);
        $response->assertRedirect(route('consentimiento.create', absolute: false));
    }

    private function registrationData(string $matricula = '202307036'): array
    {
        return [
            'name' => 'Test User', 'email' => 'test@example.com',
            'password' => 'Prueba2026!' , 'password_confirmation' => 'Prueba2026!',
            'nombre' => 'Prueba', 'apellido_paterno' => 'Sintetica',
            'fecha_nacimiento' => '2000-01-01', 'genero' => 'otro',
            'matricula' => $matricula,
        ];
    }

    public static function invalidAccountNumbers(): array
    {
        return [[''], ['20230703'], ['2023070360'], ['20230703A'], ['202 07036'], ['202307.36'], ['202307036'."\n".'1']];
    }

    public static function invalidPhones(): array
    {
        return [['123456789'], ['12345678901'], ['551234567A'], ['55 1234567'], ['55-1234567'], ['+521234567890']];
    }

    #[DataProvider('invalidPhones')]
    public function test_registration_rejects_invalid_phone_numbers(string $phone): void
    {
        $this->post('/register', $this->registrationData() + ['telefono' => $phone])
            ->assertSessionHasErrors('telefono');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_registration_accepts_a_ten_digit_phone_and_preserves_zeroes(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $this->post('/register', $this->registrationData() + ['telefono' => '0123456789'])
            ->assertSessionHasNoErrors();
        $this->assertDatabaseHas('personas', ['telefono' => '0123456789']);
    }

    public static function weakPasswords(): array
    {
        return [['Ab1!'], ['prueba2026!'], ['PRUEBA2026!'], ['PruebaClave!'], ['Prueba2026'], ['123456789!']];
    }

    #[DataProvider('weakPasswords')]
    public function test_registration_rejects_passwords_missing_a_requirement(string $password): void
    {
        $data = array_replace($this->registrationData(), [
            'password' => $password, 'password_confirmation' => $password,
        ]);
        $this->post('/register', $data)->assertSessionHasErrors('password');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_uppercase_letter_does_not_have_to_be_the_first_character(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $data = array_replace($this->registrationData(), [
            'password' => 'prueba2026!A', 'password_confirmation' => 'prueba2026!A',
        ]);
        $this->post('/register', $data)->assertSessionHasNoErrors();
        $this->assertAuthenticated();
    }

    #[DataProvider('invalidAccountNumbers')]
    public function test_invalid_account_numbers_do_not_create_records(string $matricula): void
    {
        $this->post('/register', $this->registrationData($matricula))->assertSessionHasErrors('matricula');
        $this->assertGuest();
        $this->assertDatabaseCount('users', 0);
        $this->assertDatabaseCount('personas', 0);
        $this->assertDatabaseCount('estudiantes', 0);
    }

    public function test_account_numbers_preserve_leading_zeroes_and_cannot_be_reused(): void
    {
        $this->seed(RolesAndPermissionsSeeder::class);
        $data = $this->registrationData('002307036');
        $this->post('/register', $data)->assertSessionHasNoErrors();
        $this->assertDatabaseHas('estudiantes', ['matricula' => '002307036']);
        $this->post('/logout');

        $data['email'] = 'another@example.com';
        $this->post('/register', $data)->assertSessionHasErrors('matricula');
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('estudiantes', 1);

        User::firstOrFail()->persona->estudiante->delete();
        $this->post('/register', $data)->assertSessionHasErrors('matricula');
        $this->assertDatabaseCount('users', 1);
    }
}
