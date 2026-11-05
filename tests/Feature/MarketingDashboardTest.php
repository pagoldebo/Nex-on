<?php

namespace Tests\Feature;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Subscriber;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'cmo@nexon.luxury',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    public function test_marketing_dashboard_loads_and_displays_kpis(): void
    {
        // 1. Create Coupon & Order using coupon
        $coupon = Coupon::create([
            'code'      => 'FESTIVE25',
            'type'      => 'percentage',
            'value'     => 25,
            'is_active' => true,
        ]);

        Order::create([
            'reference'         => 'ORD-MKT-01',
            'channel'           => 'web',
            'status'            => 'delivered',
            'subtotal'          => 20000,
            'discount'          => 5000,
            'total'             => 15000,
            'coupon_code'       => 'FESTIVE25',
            'payment_method'    => 'razorpay',
            'payment_status'    => 'paid',
            'shipping_name'     => 'Kriti Sanon',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'kriti@example.com',
            'shipping_address1' => 'Juhu Tara Road',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400049',
            'created_at'        => now(),
        ]);

        // 2. Create Newsletter Subscriber
        Subscriber::create([
            'email'     => 'vip.patron@nexon.luxury',
            'is_active' => true,
        ]);

        // 3. Create Web Push Notification Campaign
        \App\Models\PushSubscription::create([
            'endpoint'      => 'https://updates.push.services.mozilla.com/wpush/v2/test',
            'endpoint_hash' => hash('sha256', 'https://updates.push.services.mozilla.com/wpush/v2/test'),
            'device_type'   => 'desktop',
            'is_active'     => true,
        ]);

        \App\Models\PushCampaign::create([
            'title'      => 'Diwali Couture Drop',
            'body'       => 'Exclusive festive collection now live.',
            'action_url' => 'https://nexon.luxury/new-arrivals',
            'sent_count' => 125,
            'status'     => 'sent',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.marketing.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Marketing &amp; ROI Analytics', false);
        $response->assertSee('FESTIVE25');
        $response->assertSee('15,000.00'); // Coupon driven sales
        $response->assertSee('5,000.00');  // Discount granted
        $response->assertSee('1 broadcast alerts dispatched');
    }
}

