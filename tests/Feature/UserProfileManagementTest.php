<?php

namespace Tests\Feature;

use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserProfileManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Template::firstOrCreate(
            ['key' => 'vyra'],
            [
                'name'       => 'Vyra',
                'is_active'  => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    public function test_profile_screen_can_be_rendered()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('profile.edit'));

        $response->assertStatus(200);
        $response->assertSee($user->name);
    }

    public function test_user_can_update_profile_information()
    {
        $user = User::factory()->create([
            'name'  => 'Old Name',
            'email' => 'old@example.com',
            'phone' => '1111111111',
        ]);

        $response = $this->actingAs($user)->patch(route('profile.update'), [
            'name'  => 'New Name',
            'email' => 'new@example.com',
            'phone' => '9999999999',
        ]);

        $response->assertSessionHas('status', 'profile-updated');
        $response->assertRedirect(route('profile.edit'));

        $user->refresh();
        $this->assertEquals('New Name', $user->name);
        $this->assertEquals('new@example.com', $user->email);
        $this->assertEquals('9999999999', $user->phone);
    }

    public function test_user_can_update_password_with_correct_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password'      => 'old-password',
            'password'              => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ]);

        $response->assertSessionHas('status', 'password-updated');
        $response->assertRedirect(route('profile.edit'));

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_password_cannot_be_updated_with_wrong_current_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('real-password'),
        ]);

        $response = $this->actingAs($user)->put(route('password.update'), [
            'current_password'      => 'wrong-password',
            'password'              => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response->assertSessionHasErrors('current_password');
        $this->assertTrue(Hash::check('real-password', $user->fresh()->password));
    }

    public function test_user_can_delete_account_with_correct_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('my-password'),
        ]);

        $response = $this->actingAs($user)->delete(route('profile.destroy'), [
            'password' => 'my-password',
        ]);

        $response->assertRedirect('/');
        $this->assertGuest();
        $this->assertNull(User::find($user->id));
    }
}

