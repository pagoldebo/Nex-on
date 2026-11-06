<?php

namespace Tests\Feature;

use App\Models\CommercialInvoice;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Order;
use App\Models\PosShift;
use App\Models\PosShiftMovement;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceCashFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.cashflow@nexon.com',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
    }

    public function test_cash_flow_dashboard_loads_and_calculates_inflows_and_outflows(): void
    {
        // 1. Create POS cash order (Inflow)
        Order::create([
            'reference'         => 'POS-2026-001',
            'channel'           => 'pos',
            'status'            => 'delivered',
            'subtotal'          => 15000,
            'total'             => 15000,
            'payment_method'    => 'cash',
            'payment_status'    => 'paid',
            'shipping_name'     => 'Walk-in Retail Shopper',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'pos@nexon.com',
            'shipping_address1' => 'Showroom Counter',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400050',
            'created_at'        => now(),
        ]);

        // 2. Create Online Paid order (Inflow)
        Order::create([
            'reference'         => 'WEB-2026-002',
            'channel'           => 'web',
            'status'            => 'delivered',
            'subtotal'          => 22000,
            'total'             => 22000,
            'payment_method'    => 'razorpay',
            'payment_status'    => 'paid',
            'shipping_name'     => 'Pooja Hegde',
            'shipping_phone'    => '9876543211',
            'shipping_email'    => 'pooja@example.com',
            'shipping_address1' => 'Flat 12, Horizon Towers',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400050',
            'created_at'        => now(),
        ]);

        // 3. Create Operating Expense (Outflow)
        $cat = ExpenseCategory::create(['name' => 'Logistics', 'slug' => 'logistics', 'color' => '#f59e0b', 'is_active' => true]);
        Expense::create([
            'expense_category_id' => $cat->id,
            'title'               => 'BlueDart Bulk Logistics',
            'amount'              => 8000,
            'tax_amount'          => 1440,
            'payment_method'      => 'bank_transfer',
            'expense_date'        => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.finance.cash-flow'));

        $response->assertStatus(200);
        $response->assertSee('Real-Time Daily Cash Flow');
        $response->assertSee('37,000.00'); // 15,000 + 22,000 Inflow
        $response->assertSee('8,000.00');  // 8,000 Outflow
        $response->assertSee('+₹29,000.00'); // Net Cash Surplus (37,000 - 8,000)
    }

    public function test_cash_flow_csv_export_streams_statement(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.finance.cash-flow.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('Daily Cash Flow Statement', $response->streamedContent());
        $this->assertStringContainsString('Operational Inflows', $response->streamedContent());
    }
}
