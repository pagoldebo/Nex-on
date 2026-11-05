<?php

namespace Tests\Feature;

use App\Models\PushCampaign;
use App\Models\PushSubscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PushNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected function createAdmin(): User
    {
        return User::create([
            'name'     => 'Marketing Admin',
            'email'    => 'admin@push.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);
    }

    public function test_push_dashboard_loads()
    {
        $admin = $this->createAdmin();

        $response = $this->actingAs($admin)->get(route('admin.push.index'));
        $response->assertStatus(200);
        $response->assertSee('Web &amp; Mobile Push Notifications', false);
        $response->assertSee('Broadcast Push Campaign');
        $response->assertSee('Active Subscribers');
    }

    public function test_client_can_subscribe_device()
    {
        $endpoint = 'https://fcm.googleapis.com/fcm/send/test-device-token-12345';

        $response = $this->postJson(route('api.push.subscribe'), [
            'endpoint'    => $endpoint,
            'p256dh_key'  => 'BNcRdreALRF8FsII...',
            'auth_token'  => 'tBHItDaQ...',
            'device_type' => 'desktop',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint_hash' => hash('sha256', $endpoint),
            'is_active'     => true,
            'device_type'   => 'desktop',
        ]);
    }

    public function test_client_can_unsubscribe_device()
    {
        $endpoint = 'https://fcm.googleapis.com/fcm/send/unsub-test-token';
        PushSubscription::create([
            'endpoint'      => $endpoint,
            'endpoint_hash' => hash('sha256', $endpoint),
            'is_active'     => true,
        ]);

        $response = $this->postJson(route('api.push.unsubscribe'), [
            'endpoint' => $endpoint,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
            ]);

        $this->assertDatabaseHas('push_subscriptions', [
            'endpoint_hash' => hash('sha256', $endpoint),
            'is_active'     => false,
        ]);
    }

    public function test_admin_can_broadcast_campaign()
    {
        $admin = $this->createAdmin();

        // Seed 3 active subscribers
        for ($i = 1; $i <= 3; $i++) {
            $ep = "https://fcm.googleapis.com/fcm/send/device-{$i}";
            PushSubscription::create([
                'endpoint'      => $ep,
                'endpoint_hash' => hash('sha256', $ep),
                'is_active'     => true,
            ]);
        }

        $response = $this->actingAs($admin)->post(route('admin.push.send'), [
            'title'      => 'Weekend Flash Sale 40% Off',
            'body'       => 'Enjoy 40% discount on all luxury collections this weekend.',
            'action_url' => 'http://localhost/category/luxury-bras',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('push_campaigns', [
            'title'      => 'Weekend Flash Sale 40% Off',
            'sent_count' => 3,
            'status'     => 'sent',
        ]);
    }
}
