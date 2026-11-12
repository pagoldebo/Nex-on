<?php

namespace Database\Seeders;

use App\Models\Addon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Outlet;
use App\Models\OutletStock;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductReview;
use App\Models\SupportTicket;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DummyDataForCurrentCatalogSeeder extends Seeder
{
    /**
     * Seed dummy data strictly aligned with existing products and categories.
     * Does NOT alter product or category definitions.
     */
    public function run(): void
    {
        $products = Product::with(['colors.images', 'variants', 'category'])->where('is_active', true)->get();

        if ($products->isEmpty()) {
            $this->command->error('No active products found in database.');
            return;
        }

        $patriCustomer = User::where('email', 'priya.sharma@example.com')->first();
        if (!$patriCustomer) {
            $patriCustomer = User::create([
                'name'              => 'Priya Sharma',
                'email'             => 'priya.sharma@example.com',
                'password'          => bcrypt('password123'),
                'role'              => 'customer',
                'email_verified_at' => now(),
            ]);
        }

        // 1. REVIEWS
        $saree = $products->firstWhere('slug', 'ratri-saree') ?? $products->first();
        $pajama = $products->firstWhere('slug', 'lavender-pure-silk-lounge-pajama-set') ?? $products->last();

        $reviews = [
            [
                'product_id'    => $saree->id,
                'user_id'       => $patriCustomer->id,
                'customer_name' => 'Priya Sharma',
                'city'          => 'New Delhi',
                'rating'        => 5,
                'title'         => 'Pure handwoven heirloom',
                'comment'       => 'The handwoven tussar-by-cotton fabric carries a royal weight and delicate drape. The jaali embroidery on the pallu was the highlight of our Nobomi reception.',
                'is_approved'   => true,
            ],
            [
                'product_id'    => $saree->id,
                'user_id'       => null,
                'customer_name' => 'Sunita Kulkarni',
                'city'          => 'Mumbai',
                'rating'        => 5,
                'title'         => 'Exquisite craftsmanship & lace detail',
                'comment'       => 'The deep black color depth paired with antique zari embroidery is majestic. Arrived in regal packaging with scented satin lining.',
                'is_approved'   => true,
            ],
            [
                'product_id'    => $saree->id,
                'user_id'       => null,
                'customer_name' => 'Ananya Patel',
                'city'          => 'Bengaluru',
                'rating'        => 5,
                'title'         => 'Stunning Nobomi saree',
                'comment'       => 'Wore this Ratri Saree for a family gala. Received endless compliments on the handcrafted finish and intricate lace borders.',
                'is_approved'   => true,
            ],
            [
                'product_id'    => $saree->id,
                'user_id'       => null,
                'customer_name' => 'Meera Sen',
                'city'          => 'Kolkata',
                'rating'        => 5,
                'title'         => 'Collector’s edition quality',
                'comment'       => 'Breathable, lightweight, and incredibly rich in visual texture. Authentic artisanal craftsmanship from start to finish.',
                'is_approved'   => true,
            ],
            [
                'product_id'    => $pajama->id,
                'user_id'       => null,
                'customer_name' => 'Dr. Rajesh Menon',
                'city'          => 'Pune',
                'rating'        => 5,
                'title'         => 'Pure mulberry silk luxury',
                'comment'       => 'The softest lavender silk lounge pajama set. Unbelievably comfortable for leisure evenings and sleeps like a dream.',
                'is_approved'   => true,
            ],
            [
                'product_id'    => $pajama->id,
                'user_id'       => null,
                'customer_name' => 'Rohan Mehta',
                'city'          => 'Mumbai',
                'rating'        => 5,
                'title'         => 'Flawless tailoring & fit',
                'comment'       => 'Tailoring is sharp, fabric feels divine against the skin, and the elastic waistband provides relaxed comfort all night.',
                'is_approved'   => true,
            ],
        ];

        ProductReview::query()->delete();
        foreach ($reviews as $rev) {
            ProductReview::create($rev);
        }

        // 2. ATTACH ADDONS TO PRODUCTS
        $addons = Addon::where('is_active', true)->get();
        if ($addons->count() > 0) {
            DB::table('addon_product')->delete();
            foreach ($products as $prod) {
                foreach ($addons as $addon) {
                    DB::table('addon_product')->insert([
                        'product_id' => $prod->id,
                        'addon_id'   => $addon->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // 3. SEED REALISTIC ORDERS & ORDER ITEMS FOR CURRENT PRODUCTS
        $orders = Order::all();
        if ($orders->count() > 0) {
            OrderItem::query()->delete();

            foreach ($orders as $idx => $order) {
                $chosenProduct = ($idx % 2 === 0) ? $saree : $pajama;
                $color = $chosenProduct->colors->first();
                $variant = $chosenProduct->variants->first();

                $unitPrice = $variant ? (float) $variant->price : 3299.00;
                $compPrice = $variant ? (float) $variant->compare_at_price : 3999.00;
                $qty = ($idx % 3 === 0) ? 2 : 1;
                $lineTotal = $unitPrice * $qty;

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $chosenProduct->id,
                    'product_color_id'   => $color?->id,
                    'product_variant_id' => $variant?->id,
                    'sku'                => $variant?->sku ?: $chosenProduct->sku,
                    'product_name'       => $chosenProduct->name,
                    'product_slug'       => $chosenProduct->slug,
                    'color_name'         => $color?->name ?: 'Standard',
                    'color_hex'          => $color?->hex ?: '#000000',
                    'size'               => $variant?->size ?: 'M',
                    'cover_image'        => $chosenProduct->cover_image,
                    'price'              => $unitPrice,
                    'compare_at_price'   => $compPrice,
                    'qty'                => $qty,
                    'line_total'         => $lineTotal,
                    'created_at'         => $order->created_at,
                    'updated_at'         => $order->updated_at,
                ]);

                // Also attach second product on larger orders
                if ($idx % 2 === 0 && $products->count() > 1) {
                    $otherProduct = $pajama;
                    $otherColor = $otherProduct->colors->first();
                    $otherVariant = $otherProduct->variants->skip(1)->first() ?: $otherProduct->variants->first();

                    $otherUnitPrice = $otherVariant ? (float) $otherVariant->price : 3899.00;
                    $otherCompPrice = $otherVariant ? (float) $otherVariant->compare_at_price : 4599.00;
                    $otherQty = 1;
                    $otherLineTotal = $otherUnitPrice * $otherQty;

                    OrderItem::create([
                        'order_id'           => $order->id,
                        'product_id'         => $otherProduct->id,
                        'product_color_id'   => $otherColor?->id,
                        'product_variant_id' => $otherVariant?->id,
                        'sku'                => $otherVariant?->sku ?: $otherProduct->sku,
                        'product_name'       => $otherProduct->name,
                        'product_slug'       => $otherProduct->slug,
                        'color_name'         => $otherColor?->name ?: 'Standard',
                        'color_hex'          => $otherColor?->hex ?: '#c084fc',
                        'size'               => $otherVariant?->size ?: 'L',
                        'cover_image'        => $otherProduct->cover_image,
                        'price'              => $otherUnitPrice,
                        'compare_at_price'   => $otherCompPrice,
                        'qty'                => $otherQty,
                        'line_total'         => $otherLineTotal,
                        'created_at'         => $order->created_at,
                        'updated_at'         => $order->updated_at,
                    ]);

                    $lineTotal += $otherLineTotal;
                }

                $order->subtotal = $lineTotal;
                $order->total = $lineTotal + (float) $order->shipping_charge + (float) $order->cod_charge - (float) $order->discount;
                $order->save();
            }
        }

        // 4. OUTLET STOCKS ALLOCATION FOR CURRENT VARIANTS
        $outlets = Outlet::all();
        if ($outlets->count() > 0) {
            OutletStock::query()->delete();
            foreach ($products as $prod) {
                foreach ($prod->variants as $vIdx => $v) {
                    foreach ($outlets as $oIdx => $outlet) {
                        OutletStock::create([
                            'outlet_id'          => $outlet->id,
                            'product_variant_id' => $v->id,
                            'stock'              => 10 + ($vIdx * 3) + ($oIdx * 4),
                            'shelf_bin_location' => 'RACK-' . chr(65 + $oIdx) . '-' . ($vIdx + 1),
                        ]);
                    }
                }
            }
        }

        // 5. PRODUCT BATCHES
        ProductBatch::query()->delete();
        foreach ($products as $prod) {
            foreach ($prod->variants as $v) {
                ProductBatch::create([
                    'product_variant_id' => $v->id,
                    'batch_number'       => 'BATCH-' . ($v->sku ?: 'SKU' . $v->id),
                    'lot_number'         => 'LOT-2026-NEX-' . $v->id,
                    'mfg_date'           => now()->subMonths(1),
                    'expiry_date'        => now()->addMonths(24),
                    'initial_qty'        => 50,
                    'remaining_qty'      => 35,
                    'cost_price'         => 1200.00,
                    'status'             => 'active',
                    'notes'              => 'Passed master inspection & gold foil packaging verification.',
                ]);
            }
        }

        // 6. DEMO ABANDONED CARTS
        DB::table('abandoned_carts')->truncate();
        DB::table('abandoned_carts')->insert([
            [
                'user_id'          => $patriCustomer->id,
                'email'            => 'priya.sharma@example.com',
                'phone'            => '+91 98201 12345',
                'customer_name'    => 'Priya Sharma',
                'cart_data'        => json_encode([
                    [
                        'id'         => $saree->id,
                        'name'       => $saree->name,
                        'colorName'  => 'Black',
                        'size'       => 'M',
                        'price'      => 3299.00,
                        'qty'        => 1,
                        'image'      => $saree->cover_image,
                    ],
                ]),
                'recovery_hash'    => Str::random(32),
                'total_amount'     => 3299.00,
                'items_count'      => 1,
                'recovery_step'    => 1,
                'last_reminded_at' => now()->subHours(4),
                'is_recovered'     => false,
                'recovered_at'     => null,
                'recovered_order_id' => null,
                'created_at'       => now()->subDay(),
                'updated_at'       => now()->subHours(4),
            ],
            [
                'user_id'          => null,
                'email'            => 'kavita.deshmukh@gmail.com',
                'phone'            => '+91 98112 44332',
                'customer_name'    => 'Kavita Deshmukh',
                'cart_data'        => json_encode([
                    [
                        'id'         => $pajama->id,
                        'name'       => $pajama->name,
                        'colorName'  => 'Pastel Lavender',
                        'size'       => 'S',
                        'price'      => 3899.00,
                        'qty'        => 1,
                        'image'      => $pajama->cover_image,
                    ],
                ]),
                'recovery_hash'    => Str::random(32),
                'total_amount'     => 3899.00,
                'items_count'      => 1,
                'recovery_step'    => 2,
                'last_reminded_at' => now()->subHours(12),
                'is_recovered'     => false,
                'recovered_at'     => null,
                'recovered_order_id' => null,
                'created_at'       => now()->subDays(2),
                'updated_at'       => now()->subHours(12),
            ],
        ]);

        // 7. DEMO SUPPORT TICKETS
        $manager = User::where('email', 'manager@nexon.app')->first();
        $firstOrder = Order::first();
        if ($firstOrder) {
            SupportTicket::whereNull('order_id')->orWhereNotIn('order_id', $orders->pluck('id'))->delete();
            SupportTicket::updateOrCreate(
                ['ticket_number' => 'TCK-202611-001'],
                [
                    'user_id'     => $patriCustomer->id,
                    'order_id'    => $firstOrder->id,
                    'assigned_to' => $manager?->id,
                    'subject'     => 'Query regarding blouse piece with Ratri Saree',
                    'category'    => 'order_inquiry',
                    'priority'    => 'medium',
                    'status'      => 'in_progress',
                    'sla_due_at'  => now()->addDay(),
                ]
            );
        }
    }
}
