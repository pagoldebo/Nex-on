<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinanceProfitLossTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'cfo@nexon.com',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $category = Category::create(['name' => 'Couture', 'slug' => 'couture']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Silk Kimono Gown', 'slug' => 'silk-kimono-gown']);
        $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Emerald', 'hex' => '#059669']);
        $this->variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'KIM-EME-M',
            'size'             => 'M',
            'price'            => 10000,
            'cost_price'       => 4000, // COGS per unit
            'stock'            => 50,
        ]);
    }

    public function test_profit_and_loss_statement_loads_and_computes_margins(): void
    {
        // 1. Create Delivered Order with Line Item
        $order = Order::create([
            'reference'         => 'ORD-PL-001',
            'channel'           => 'web',
            'status'            => 'delivered',
            'subtotal'          => 20000,
            'shipping_charge'   => 500,
            'cod_charge'        => 0,
            'discount'          => 1000,
            'total'             => 19500,
            'payment_method'    => 'razorpay',
            'payment_status'    => 'paid',
            'shipping_name'     => 'Ananya Panday',
            'shipping_phone'    => '9876543210',
            'shipping_email'    => 'ananya@example.com',
            'shipping_address1' => 'Bandra West',
            'shipping_city'     => 'Mumbai',
            'shipping_state'    => 'Maharashtra',
            'shipping_pincode'  => '400050',
            'created_at'        => now(),
        ]);

        OrderItem::create([
            'order_id'           => $order->id,
            'product_id'         => $this->variant->product_id,
            'product_color_id'   => $this->variant->product_color_id,
            'product_variant_id' => $this->variant->id,
            'sku'                => $this->variant->sku,
            'product_name'       => 'Silk Kimono Gown',
            'product_slug'       => 'silk-kimono-gown',
            'color_name'         => 'Emerald',
            'color_hex'          => '#059669',
            'size'               => 'M',
            'price'              => 10000,
            'qty'                => 2, // 2 x 4000 = 8000 COGS
            'line_total'         => 20000,
        ]);

        // 2. Create Operating Expenses
        $cat = ExpenseCategory::create(['name' => 'Packaging', 'slug' => 'packaging', 'color' => '#10b981', 'is_active' => true]);
        Expense::create([
            'expense_category_id' => $cat->id,
            'title'               => 'Silk Ribbon & Gift Wraps',
            'amount'              => 3500,
            'tax_amount'          => 630,
            'payment_method'      => 'bank_transfer',
            'expense_date'        => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.finance.pl'));

        $response->assertStatus(200);
        $response->assertSee('Profit &amp; Loss (P&amp;L) Statement', false);
        $response->assertSee('19,500.00'); // Net Revenue (20,000 + 500 - 1,000)
        $response->assertSee('8,000.00');  // Total COGS (2 x 4,000)
        $response->assertSee('11,500.00'); // Gross Profit (19,500 - 8,000)
        $response->assertSee('3,500.00');  // Total OPEX
        $response->assertSee('8,000.00');  // EBITDA (11,500 - 3,500)
    }

    public function test_printable_pl_view_renders_cleanly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.finance.pl.print'));

        $response->assertStatus(200);
        $response->assertSee('INCOME STATEMENT');
        $response->assertSee('NET OPERATING REVENUE');
        $response->assertSee('COST OF GOODS SOLD');
    }

    public function test_pl_csv_export_streams_statement(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.finance.pl.export'));

        $response->assertStatus(200);
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
        $this->assertStringContainsString('Profit & Loss Statement (P&L)', $response->streamedContent());
        $this->assertStringContainsString('NET OPERATING REVENUE', $response->streamedContent());
    }
}

