<?php

namespace Tests\Feature;

use App\Models\AbandonedCart;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AbandonedCartRecoveryTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected ProductVariant $variant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'retention@nexon.com',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $category = Category::create(['name' => 'Intimates', 'slug' => 'intimates']);
        $product = Product::create(['category_id' => $category->id, 'name' => 'Lace Bralette', 'slug' => 'lace-bralette']);
        $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Noir', 'hex' => '#000000']);
        $this->variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'sku'              => 'BRAL-NOIR-S',
            'size'             => 'S',
            'price'            => 2999,
            'stock'            => 50,
        ]);
    }

    public function test_abandoned_carts_index_loads_with_metrics(): void
    {
        AbandonedCart::create([
            'customer_name' => 'Mira Rajput',
            'email'         => 'mira@rajput.in',
            'phone'         => '+91 98200 11223',
            'cart_data'     => [
                ['name' => 'Lace Bralette', 'size' => 'S', 'color' => 'Noir', 'qty' => 1, 'price' => 2999]
            ],
            'total_amount'  => 2999,
            'items_count'   => 1,
            'recovery_step' => 0,
            'is_recovered'  => false,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.marketing.abandoned-carts'));

        $response->assertStatus(200);
        $response->assertSee('Abandoned Cart Automated Recovery');
        $response->assertSee('Mira Rajput');
        $response->assertSee('2,999.00');
    }

    public function test_admin_can_simulate_abandoned_cart(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.marketing.abandoned-carts.simulate'));

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('abandoned_carts', [
            'customer_name' => 'Kavya Maran',
            'is_recovered'  => false,
        ]);
    }

    public function test_admin_can_advance_recovery_drip_stages(): void
    {
        $cart = AbandonedCart::create([
            'customer_name' => 'Radhika Merchant',
            'email'         => 'radhika@encore.in',
            'cart_data'     => [],
            'total_amount'  => 8500,
            'recovery_step' => 0,
        ]);

        // Stage 1 (1-Hour Email)
        $response = $this->actingAs($this->admin)->post(route('admin.marketing.abandoned-carts.drip', $cart));
        $response->assertRedirect();
        $cart->refresh();
        $this->assertEquals(1, $cart->recovery_step);
        $this->assertNotNull($cart->last_reminded_at);

        // Stage 2 (24-Hour SMS)
        $this->actingAs($this->admin)->post(route('admin.marketing.abandoned-carts.drip', $cart));
        $cart->refresh();
        $this->assertEquals(2, $cart->recovery_step);
    }

    public function test_admin_can_mark_cart_as_recovered(): void
    {
        $cart = AbandonedCart::create([
            'customer_name' => 'Sara Ali Khan',
            'email'         => 'sara@pataudi.in',
            'cart_data'     => [],
            'total_amount'  => 12000,
            'is_recovered'  => false,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.marketing.abandoned-carts.recovered', $cart));

        $response->assertRedirect();
        $cart->refresh();
        $this->assertTrue($cart->is_recovered);
        $this->assertNotNull($cart->recovered_at);
    }

    public function test_storefront_one_click_cart_restore(): void
    {
        $cart = AbandonedCart::create([
            'customer_name' => 'Janhvi Kapoor',
            'cart_data'     => [
                $this->variant->id => [
                    'name'  => 'Lace Bralette',
                    'sku'   => 'BRAL-NOIR-S',
                    'price' => 2999,
                    'qty'   => 2,
                ]
            ],
            'total_amount'  => 5998,
        ]);

        $response = $this->get(route('cart.recover', $cart->recovery_hash));

        $response->assertRedirect(route('cart'));
        $this->assertArrayHasKey($this->variant->id, session('cart'));
    }
}

