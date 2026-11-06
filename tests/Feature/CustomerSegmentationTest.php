<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Template;
use App\Models\User;
use App\Services\CustomerSegmentationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerSegmentationTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected CustomerSegmentationService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.rfm@nexon.com',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
        $this->service = new CustomerSegmentationService();
    }

    public function test_segmentation_dashboard_loads_successfully()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.customers.segments'));

        $response->assertStatus(200);
        $response->assertSee('RFM Behavioral Customer Segmentation', false);
        $response->assertSee('Champions');
        $response->assertSee('Loyal Shoppers');
        $response->assertSee('At Risk');
    }

    public function test_rfm_classification_prospect()
    {
        // 0 orders, no recency
        $result = $this->service->classifySegment(0, 0.0, null);

        $this->assertEquals('prospect', $result['key']);
        $this->assertEquals('Prospect / Lead', $result['name']);
    }

    public function test_rfm_classification_champions()
    {
        // Recency <= 45 days, frequency >= 3, monetary >= 3000
        $result = $this->service->classifySegment(4, 5500.0, 15);

        $this->assertEquals('champions', $result['key']);
        $this->assertEquals('Champions', $result['name']);
    }

    public function test_rfm_classification_loyal()
    {
        // Frequency >= 3, recency <= 90 days
        $result = $this->service->classifySegment(3, 2000.0, 60);

        $this->assertEquals('loyal', $result['key']);
        $this->assertEquals('Loyal Customers', $result['name']);
    }

    public function test_rfm_classification_potential()
    {
        // Recency <= 30 days, frequency 1-2
        $result = $this->service->classifySegment(1, 1500.0, 10);

        $this->assertEquals('potential', $result['key']);
        $this->assertEquals('Potential Loyalists', $result['name']);
    }

    public function test_rfm_classification_at_risk()
    {
        // Recency > 90 days, monetary >= 2000
        $result = $this->service->classifySegment(2, 3500.0, 110);

        $this->assertEquals('at_risk', $result['key']);
        $this->assertEquals('At Risk', $result['name']);
    }

    public function test_rfm_classification_hibernating()
    {
        // Recency > 90 days, low monetary spend
        $result = $this->service->classifySegment(1, 800.0, 120);

        $this->assertEquals('hibernating', $result['key']);
        $this->assertEquals('Hibernating', $result['name']);
    }

    public function test_segmentation_csv_export_streams_properly()
    {
        $customer = User::factory()->create([
            'name'  => 'Kiara Advani',
            'email' => 'kiara@example.com',
            'role'  => 'customer',
        ]);

        Order::create([
            'user_id'          => $customer->id,
            'reference'        => 'ORD-KIARA-01',
            'status'           => 'delivered',
            'subtotal'         => 4500,
            'shipping_charge'  => 0,
            'cod_charge'       => 0,
            'discount'         => 0,
            'total'            => 4500,
            'payment_method'   => 'prepaid',
            'payment_status'   => 'paid',
            'shipping_name'    => 'Kiara Advani',
            'shipping_phone'   => '9876543210',
            'shipping_email'   => 'kiara@example.com',
            'shipping_address1'=> 'Juhu Beach Villa',
            'shipping_city'    => 'Mumbai',
            'shipping_state'   => 'Maharashtra',
            'shipping_pincode' => '400049',
            'created_at'       => Carbon::now()->subDays(5),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.customers.segments.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('Content-Type'));
        $content = $response->streamedContent();

        $this->assertStringContainsString('Customer ID', $content);
        $this->assertStringContainsString('Orders Count (Frequency)', $content);
        $this->assertStringContainsString('Kiara Advani', $content);
        $this->assertStringContainsString('kiara@example.com', $content);
        $this->assertStringContainsString('Potential Loyalists', $content);
    }
}
