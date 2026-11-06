<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutPriceVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_server_verifies_real_database_prices_and_ignores_client_tampering()
    {
        Setting::set('free_shipping_threshold', '1499');
        Setting::set('shipping_cost', '99');
        Setting::set('cod_charge', '40');

        $category = Category::create([
            'name' => 'Luxury Silk',
            'slug' => 'luxury-silk',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Pure Mulberry Silk Robe',
            'slug'        => 'pure-mulberry-silk-robe',
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Ivory Cream',
            'hex'        => '#FFFDD0',
            'sort_order' => 1,
        ]);

        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'M',
            'price'            => 2999.00,
            'compare_at_price' => 3499.00,
            'stock'            => 10,
        ]);

        // Malicious client payload with tampered price (₹10 instead of ₹2999)
        $tamperedPayload = [
            'shipping' => [
                'name'     => 'Patron User',
                'phone'    => '9876543210',
                'email'    => 'patron@luxury.test',
                'address1' => 'Suite 500, Luxury Towers',
                'city'     => 'Mumbai',
                'state'    => 'Maharashtra',
                'pincode'  => '400001',
            ],
            'payment'  => 'upi',
            'subtotal' => 10, // Tampered subtotal
            'total'    => 10, // Tampered total
            'items'    => [
                [
                    'productId'        => $product->id,
                    'productVariantId' => $variant->id,
                    'productColorId'   => $color->id,
                    'name'             => $product->name,
                    'colorName'        => $color->name,
                    'size'             => 'M',
                    'price'            => 10, // Tampered item price
                    'qty'              => 1,
                ]
            ],
        ];

        $order = Order::createFromCheckout($tamperedPayload);

        // Subtotal must be the genuine DB price (2999), NOT the tampered 10
        $this->assertEquals(2999, $order->subtotal);

        // Since 2999 >= 1499 free shipping threshold, shipping charge is 0
        $this->assertEquals(0, $order->shipping_charge);
        $this->assertEquals(0, $order->cod_charge);
        $this->assertEquals(2999, $order->total);

        // Order item in DB must also record genuine verified price
        $this->assertCount(1, $order->items);
        $item = $order->items->first();
        $this->assertEquals(2999, $item->price);
        $this->assertEquals(2999, $item->line_total);

        // Stock must have been deducted
        $this->assertEquals(9, $variant->fresh()->stock);
    }

    public function test_coupon_is_recalculated_against_server_subtotal()
    {
        Setting::set('free_shipping_threshold', '1499');
        Setting::set('shipping_cost', '99');
        Setting::set('cod_charge', '40');

        $category = Category::create([
            'name' => 'Atelier',
            'slug' => 'atelier',
        ]);

        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Cashmere Wrap',
            'slug'        => 'cashmere-wrap',
            'is_active'   => true,
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Charcoal',
            'hex'        => '#333333',
            'sort_order' => 1,
        ]);

        $variant = ProductVariant::create([
            'product_id'       => $product->id,
            'product_color_id' => $color->id,
            'size'             => 'Free Size',
            'price'            => 2000.00,
            'stock'            => 5,
        ]);

        $coupon = Coupon::create([
            'code'             => 'LUXURY10',
            'type'             => 'percentage',
            'value'            => 10, // 10%
            'min_order_amount' => 1000,
            'is_active'        => true,
        ]);

        $payload = [
            'shipping' => [
                'name'     => 'Alice',
                'phone'    => '9123456789',
                'email'    => 'alice@luxury.test',
                'address1' => '10 Atelier Lane',
                'city'     => 'Delhi',
                'state'    => 'Delhi',
                'pincode'  => '110001',
            ],
            'payment'     => 'cod',
            'coupon_code' => 'LUXURY10',
            'discount'    => 9999, // Tampered discount claiming 9999
            'items'       => [
                [
                    'productId'        => $product->id,
                    'productVariantId' => $variant->id,
                    'qty'              => 1,
                    'price'            => 2000,
                ]
            ],
        ];

        $order = Order::createFromCheckout($payload);

        // Subtotal: 2000
        $this->assertEquals(2000, $order->subtotal);

        // Discount: 10% of 2000 = 200 (not the tampered 9999)
        $this->assertEquals(200, $order->discount);

        // Free shipping (2000 >= 1499), COD fee: 40
        $this->assertEquals(0, $order->shipping_charge);
        $this->assertEquals(40, $order->cod_charge);

        // Total: 2000 + 0 + 40 - 200 = 1840
        $this->assertEquals(1840, $order->total);
    }
}
