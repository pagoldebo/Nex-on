<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTerminalTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_terminal_screen_loads()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        $this->withoutExceptionHandling();
        $response = $this->actingAs($admin)->get('/admin/pos');
        $response->assertStatus(200);
        $response->assertSee('POS');
    }

    public function test_pos_product_search()
    {
        $admin = User::first() ?? User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)->getJson(route('admin.pos.products.search'));
        $response->assertStatus(200);
        $response->assertJsonStructure(['success', 'count', 'data']);
    }

    public function test_pos_checkout_flow_and_stock_deduction()
    {
        $admin = User::first() ?? User::create([
            'name'     => 'Admin User',
            'email'    => 'admin@pos.test',
            'phone'    => '9999999999',
            'password' => bcrypt('password'),
            'role'     => 'admin',
        ]);

        $category = Category::create([
            'name' => 'Lingerie',
            'slug' => 'lingerie',
            'is_active' => true,
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Silk Lace Bralette',
            'slug'        => 'silk-lace-bralette',
            'sku'         => 'SLB-001',
            'price'       => 1299,
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Blush Pink',
            'hex'        => '#E8C4C0',
        ]);

        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'M',
            'sku'              => 'SLB-001-M',
            'price'            => 1299,
            'stock'            => 25,
        ]);

        $initialStock = $variant->stock;

        $response = $this->actingAs($admin)->postJson(route('admin.pos.checkout'), [
            'items' => [
                [
                    'variant_id' => $variant->id,
                    'qty'        => 2,
                    'price'      => (int) $variant->price,
                ],
            ],
            'customer_name'   => 'Walk-in Test Guest',
            'customer_phone'  => '9876543210',
            'payment_method'  => 'cash',
            'tendered_amount' => (int) $variant->price * 2 + 100,
            'discount'        => 0,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $variant->refresh();
        $this->assertEquals($initialStock - 2, $variant->stock);

        $order = Order::where('channel', 'pos')->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('pos', $order->channel);
        $this->assertEquals(Order::STATUS_DELIVERED, $order->status);
        $this->assertEquals(Order::PAYMENT_PAID, $order->payment_status);
        $this->assertEquals(100, $order->change_amount);
    }

    public function test_pos_checkout_prevents_overselling_via_duplicate_variant_entries(): void
    {
        $admin = User::create([
            'name'              => 'Cashier Admin',
            'email'             => 'cashier.dup@nexon.test',
            'password'          => bcrypt('password'),
            'role'              => 'admin',
            'email_verified_at' => now(),
        ]);

        $cat = Category::create(['name' => 'Intimates', 'slug' => 'intimates-dup']);
        $product = Product::create([
            'category_id'    => $cat->id,
            'name'           => 'Limited Bralette',
            'slug'           => 'limited-bralette-dup',
            'sku'            => 'LB-DUP',
            'starting_price' => 999,
            'is_active'      => true,
        ]);
        $color = ProductColor::create(['product_id' => $product->id, 'name' => 'Rose', 'hex' => '#FF007F']);
        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'S',
            'sku'              => 'LB-DUP-S',
            'price'            => 999,
            'stock'            => 4,
        ]);

        $response = $this->actingAs($admin)->postJson(route('admin.pos.checkout'), [
            'items' => [
                ['variant_id' => $variant->id, 'qty' => 3, 'price' => 999],
                ['variant_id' => $variant->id, 'qty' => 3, 'price' => 999],
            ],
            'payment_method'  => 'cash',
            'tendered_amount' => 6000,
        ]);

        $response->assertStatus(422);
        $this->assertEquals(4, $variant->fresh()->stock);
    }
}
