<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfilePhotoTest extends TestCase
{
    use RefreshDatabase;

    private function user(): User
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $user->persona()->create([
            'nombre' => 'Prueba', 'apellido_paterno' => 'Perfil', 'fecha_nacimiento' => '2000-01-01',
            'genero' => 'prefiero_no_decirlo',
        ]);

        return $user;
    }

    public function test_photo_is_reencoded_private_replaced_and_removed(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD es necesaria para las pruebas de imágenes.');
        }
        Storage::fake('local');
        $user = $this->user();
        $other = $this->user();
        $this->actingAs($user)->from('/perfil')->post('/perfil/foto', [
            'photo' => UploadedFile::fake()->image('portrait.png', 700, 500),
            'position_x' => 100, 'position_y' => 50, 'user_id' => $other->id,
        ])->assertSessionHasNoErrors()->assertRedirect('/perfil');
        $first = $user->fresh()->persona->foto_perfil;
        Storage::disk('local')->assertExists($first);
        $info = getimagesizefromstring(Storage::disk('local')->get($first));
        $this->assertSame([512, 512], [$info[0], $info[1]]);
        $this->assertSame('image/jpeg', $info['mime']);
        $this->assertNull($other->fresh()->persona->foto_perfil);
        $this->get('/perfil/foto')->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->actingAs($other)->get('/perfil/foto')->assertNotFound();
        $this->actingAs($user)->post('/perfil/foto', [
            'photo' => UploadedFile::fake()->image('second.jpg', 500, 700),
        ])->assertSessionHasNoErrors();
        Storage::disk('local')->assertMissing($first);
        $second = $user->fresh()->persona->foto_perfil;
        $this->delete('/perfil/foto')->assertRedirect();
        Storage::disk('local')->assertMissing($second);
        $this->assertNull($user->fresh()->persona->foto_perfil);
        $this->get('/perfil/foto')->assertNotFound();
    }

    public function test_photo_rejects_nonimages_and_unauthenticated_access(): void
    {
        Storage::fake('local');
        $this->get('/perfil/foto')->assertRedirect('/login');
        $this->post('/perfil/foto')->assertRedirect('/login');
        $this->delete('/perfil/foto')->assertRedirect('/login');
        $user = $this->user();
        $this->actingAs($user)->post('/perfil/foto', [
            'photo' => UploadedFile::fake()->create('payload.svg', 1, 'image/svg+xml'),
        ])->assertSessionHasErrors('photo');
        $this->assertNull($user->fresh()->persona->foto_perfil);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_photo_validates_size_dimensions_and_crop(): void
    {
        if (! extension_loaded('gd')) {
            $this->markTestSkipped('GD es necesaria para las pruebas de imágenes.');
        }
        Storage::fake('local');
        $this->actingAs($this->user());
        foreach ([
            ['photo' => UploadedFile::fake()->image('large.jpg')->size(2049)],
            ['photo' => UploadedFile::fake()->image('wide.jpg', 3001, 10)],
            ['photo' => UploadedFile::fake()->image('ok.png'), 'position_x' => 101],
        ] as $data) {
            $this->post('/perfil/foto', $data)->assertSessionHasErrors();
        }
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_new_palettes_save_without_legacy_icon_and_render_photo_controls(): void
    {
        $user = $this->user();
        $this->actingAs($user);
        foreach (['teal', 'indigo', 'amber', 'coral'] as $accent) {
            $this->put('/perfil/apariencia', [
                'theme' => 'system', 'accent_color' => $accent, 'density' => 'comfortable', 'reduced_motion' => true,
            ])->assertSessionHasNoErrors();
            $this->assertSame($accent, $user->fresh()->appearance_settings['accent_color']);
        }
        $this->get('/perfil')->assertOk()->assertSee('Guardar foto')->assertSee('Restablecer apariencia')
            ->assertDontSee('Icono de perfil')->assertSee('data-appearance-form', false);
    }
}
