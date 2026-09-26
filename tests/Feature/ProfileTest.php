<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);

        $response = $this
            ->actingAs($user)
            ->get('/profile');

        $response->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')->patch('/profile', [
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        // La aplicación no implementa MustVerifyEmail: conserva este campo.
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_email_verification_status_is_unchanged_when_the_email_address_is_unchanged(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')->patch('/profile', [
                'name' => 'Test User',
                'email' => $user->email,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/profile');

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);

        $response = $this
            ->actingAs($user)
            ->delete('/profile', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);

        $response = $this
            ->actingAs($user)
            ->from('/profile')
            ->delete('/profile', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('deleteAccount', 'password')
            ->assertRedirect('/profile');

        $this->assertNotNull($user->fresh());
    }

    public function test_profile_updates_cannot_change_another_user_or_inject_consent(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $other = User::factory()->create();
        $this->actingAs($user)->from('/perfil')->patch('/perfil', [
            'name' => 'Own name', 'email' => $user->email, 'id' => $other->id,
            'acepto_consentimiento' => false, 'role' => 'admin',
        ])->assertSessionHasNoErrors()->assertRedirect('/perfil');
        $this->assertSame('Own name', $user->fresh()->name);
        $this->assertTrue($user->fresh()->acepto_consentimiento);
        $this->assertNotSame('Own name', $other->fresh()->name);
        $this->from('/perfil')->patch('/perfil', ['name' => 'Rejected', 'email' => $other->email])
            ->assertSessionHasErrorsIn('updateProfileInformation', 'email');
        $this->assertSame('Own name', $user->fresh()->name);
    }

    public function test_profile_password_endpoint_requires_current_password_and_confirmation(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $this->actingAs($user)->from('/perfil')->put('/perfil/password', [
            'current_password' => 'wrong', 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertSessionHasErrorsIn('updatePassword', 'current_password');
        $this->assertTrue(Hash::check('password', $user->fresh()->password));
        $this->put('/perfil/password', [
            'current_password' => 'password', 'password' => 'new-password', 'password_confirmation' => 'new-password',
        ])->assertSessionHasNoErrors();
        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    public function test_appearance_rejects_arbitrary_values_and_saves_valid_preferences(): void
    {
        $user = User::factory()->create(['acepto_consentimiento' => true]);
        $data = ['avatar_icon' => 'person-circle', 'theme' => 'dark', 'accent_color' => 'purple', 'density' => 'compact', 'reduced_motion' => true];
        $this->actingAs($user)->from('/perfil')->put('/perfil/apariencia', ['theme' => '<script>'] + $data)
            ->assertSessionHasErrorsIn('updateAppearance', 'theme');
        $this->put('/perfil/apariencia', $data)->assertSessionHasNoErrors();
        $this->assertSame('dark', $user->fresh()->appearance_settings['theme']);
        $this->assertTrue($user->fresh()->appearance_settings['reduced_motion']);
    }
}
