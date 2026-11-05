<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create a demo customer if none exists
        $user = User::firstOrCreate(
            ['email' => 'priya.sharma@example.com'],
            [
                'name'     => 'Priya Sharma',
                'password' => Hash::make('password123'),
            ]
        );

        // Fetch products with their colors and variants
        $products = Product::with(['colors', 'variants'])->get();
        if ($products->isEmpty()) {
            $this->command->warn('No products found in database. Run CatalogueSeeder first.');
            return;
        }

        // Clean out existing orders if any to avoid duplication
        OrderItem::query()->delete();
        Order::query()->delete();

        $sampleOrders = [
            [
                'reference'         => 'SAA-20261014-0001',
                'user_id'           => $user->id,
                'status'            => Order::STATUS_PENDING,
                'payment_method'    => 'cod',
                'payment_status'    => Order::PAYMENT_PENDING,
                'payment_reference' => null,
                'shipping_charge'   => 50,
                'cod_charge'        => 40,
                'discount'          => 0,
                'shipping_name'     => 'Ananya Kapoor',
                'shipping_phone'    => '+91 98201 12345',
                'shipping_email'    => 'ananya.k@gmail.com',
                'shipping_address1' => 'Flat 402, Sea Green Apts, Juhu Tara Road',
                'shipping_address2' => 'Opposite Palm Beach Hotel',
                'shipping_city'     => 'Mumbai',
                'shipping_state'    => 'Maharashtra',
                'shipping_pincode'  => '400049',
                'courier'           => null,
                'tracking_number'   => null,
                'notes'             => 'Please call before delivery. Ring bell twice.',
                'created_at'        => Carbon::now()->subHours(2),
                'items_count'       => 1,
            ],
            [
                'reference'         => 'SAA-20261014-0002',
                'user_id'           => $user->id,
                'status'            => Order::STATUS_CONFIRMED,
                'payment_method'    => 'upi',
                'payment_status'    => Order::PAYMENT_PAID,
                'payment_reference' => 'UPI/261014/98127391',
                'shipping_charge'   => 0,
                'cod_charge'        => 0,
                'discount'          => 200,
                'shipping_name'     => 'Rhea Sen',
                'shipping_phone'    => '+91 99302 98765',
                'shipping_email'    => 'rhea.sen@outlook.com',
                'shipping_address1' => 'Villa 12, Sobha Malabar, Lavelle Road',
                'shipping_address2' => 'Near Cubbon Park',
                'shipping_city'     => 'Bengaluru',
                'shipping_state'    => 'Karnataka',
                'shipping_pincode'  => '560001',
                'courier'           => null,
                'tracking_number'   => null,
                'notes'             => 'Leave with concierge if not available.',
                'created_at'        => Carbon::now()->subHours(4),
                'items_count'       => 2,
            ],
            [
                'reference'         => 'SAA-20261013-0001',
                'user_id'           => null,
                'status'            => Order::STATUS_PROCESSING,
                'payment_method'    => 'card',
                'payment_status'    => Order::PAYMENT_PAID,
                'payment_reference' => 'TXN_VISA_9831411',
                'shipping_charge'   => 0,
                'cod_charge'        => 0,
                'discount'          => 0,
                'shipping_name'     => 'Kavita Verma',
                'shipping_phone'    => '+91 97110 54321',
                'shipping_email'    => 'kavita.v@yahoo.com',
                'shipping_address1' => 'B-14, Defence Colony',
                'shipping_address2' => null,
                'shipping_city'     => 'New Delhi',
                'shipping_state'    => 'Delhi',
                'shipping_pincode'  => '110024',
                'courier'           => 'BlueDart Express',
                'tracking_number'   => 'BD98231049IN',
                'notes'             => 'Gift packaging requested.',
                'created_at'        => Carbon::now()->subDays(1)->setHour(14),
                'items_count'       => 2,
            ],
            [
                'reference'         => 'SAA-20261012-0001',
                'user_id'           => $user->id,
                'status'            => Order::STATUS_SHIPPED,
                'payment_method'    => 'upi',
                'payment_status'    => Order::PAYMENT_PAID,
                'payment_reference' => 'UPI/261012/11094821',
                'shipping_charge'   => 0,
                'cod_charge'        => 0,
                'discount'          => 300,
                'shipping_name'     => 'Sneha Mukherjee',
                'shipping_phone'    => '+91 98305 77123',
                'shipping_email'    => 'sneha.m@gmail.com',
                'shipping_address1' => '44/2 Ballygunge Circular Road',
                'shipping_address2' => 'Opposite Ice Skating Rink',
                'shipping_city'     => 'Kolkata',
                'shipping_state'    => 'West Bengal',
                'shipping_pincode'  => '700019',
                'courier'           => 'Delhivery Surface',
                'tracking_number'   => 'DEL192837465',
                'shipped_at'        => Carbon::now()->subDays(2)->setHour(11),
                'notes'             => null,
                'created_at'        => Carbon::now()->subDays(2)->setHour(10),
                'items_count'       => 3,
            ],
            [
                'reference'         => 'SAA-20261010-0001',
                'user_id'           => null,
                'status'            => Order::STATUS_DELIVERED,
                'payment_method'    => 'card',
                'payment_status'    => Order::PAYMENT_PAID,
                'payment_reference' => 'TXN_MC_49201948',
                'shipping_charge'   => 0,
                'cod_charge'        => 0,
                'discount'          => 0,
                'shipping_name'     => 'Natasha D\'Souza',
                'shipping_phone'    => '+91 98221 44556',
                'shipping_email'    => 'natasha.ds@gmail.com',
                'shipping_address1' => 'Row House 4, Silver Sands Estate, Candolim',
                'shipping_address2' => 'Near Lemon Tree Amarante',
                'shipping_city'     => 'North Goa',
                'shipping_state'    => 'Goa',
                'shipping_pincode'  => '403515',
                'courier'           => 'BlueDart Air',
                'tracking_number'   => 'BD77441199IN',
                'shipped_at'        => Carbon::now()->subDays(4)->setHour(9),
                'delivered_at'      => Carbon::now()->subDays(3)->setHour(16),
                'notes'             => 'Delivered safely at front desk.',
                'created_at'        => Carbon::now()->subDays(5)->setHour(12),
                'items_count'       => 2,
            ],
            [
                'reference'         => 'SAA-20261008-0001',
                'user_id'           => $user->id,
                'status'            => Order::STATUS_DELIVERED,
                'payment_method'    => 'cod',
                'payment_status'    => Order::PAYMENT_PAID,
                'payment_reference' => null,
                'shipping_charge'   => 50,
                'cod_charge'        => 40,
                'discount'          => 0,
                'shipping_name'     => 'Meera Joshi',
                'shipping_phone'    => '+91 94230 66789',
                'shipping_email'    => 'meera.joshi@gmail.com',
                'shipping_address1' => 'Plot 89, Kalyani Nagar',
                'shipping_address2' => 'Lane 4, Near Joggers Park',
                'shipping_city'     => 'Pune',
                'shipping_state'    => 'Maharashtra',
                'shipping_pincode'  => '411006',
                'courier'           => 'DTDC Express',
                'tracking_number'   => 'DTDC66554433',
                'shipped_at'        => Carbon::now()->subDays(6)->setHour(10),
                'delivered_at'      => Carbon::now()->subDays(5)->setHour(14),
                'notes'             => 'Cash collected on delivery.',
                'created_at'        => Carbon::now()->subDays(7)->setHour(15),
                'items_count'       => 1,
            ],
            [
                'reference'         => 'SAA-20261005-0001',
                'user_id'           => null,
                'status'            => Order::STATUS_CANCELLED,
                'payment_method'    => 'upi',
                'payment_status'    => Order::PAYMENT_REFUNDED,
                'payment_reference' => 'UPI/261005/77889900',
                'shipping_charge'   => 0,
                'cod_charge'        => 0,
                'discount'          => 0,
                'shipping_name'     => 'Tara Singhania',
                'shipping_phone'    => '+91 98199 00112',
                'shipping_email'    => 'tara.singh@gmail.com',
                'shipping_address1' => '101, Golf Links Tower',
                'shipping_address2' => 'Near DLF Cyber City',
                'shipping_city'     => 'Gurugram',
                'shipping_state'    => 'Haryana',
                'shipping_pincode'  => '122002',
                'courier'           => null,
                'tracking_number'   => null,
                'notes'             => 'Customer cancelled prior to dispatch. Refund processed.',
                'created_at'        => Carbon::now()->subDays(9)->setHour(11),
                'items_count'       => 1,
            ],
        ];

        foreach ($sampleOrders as $data) {
            $itemsCount = $data['items_count'];
            unset($data['items_count']);

            $data['subtotal'] = 0;
            $data['total']    = 0;

            $order = Order::create($data);

            // Add items
            $subtotal = 0;
            $selectedProducts = $products->random(min($itemsCount, $products->count()));

            foreach ($selectedProducts as $prod) {
                $variant = $prod->variants->first();
                $color   = $prod->colors->first();

                $unitPrice = $variant ? (int) $variant->price : 1499;
                $compPrice = $variant ? (int) $variant->compare_at_price : 1999;
                $qty       = rand(1, 2);
                $lineTotal = $unitPrice * $qty;
                $subtotal  += $lineTotal;

                OrderItem::create([
                    'order_id'           => $order->id,
                    'product_id'         => $prod->id,
                    'product_color_id'   => $color ? $color->id : null,
                    'product_variant_id' => $variant ? $variant->id : null,
                    'product_name'       => $prod->name,
                    'product_slug'       => $prod->slug,
                    'color_name'         => $color ? $color->name : 'Standard',
                    'color_hex'          => $color ? $color->hex : '#1A1A1A',
                    'size'               => $variant ? $variant->size : 'M',
                    'cover_image'        => null,
                    'price'              => $unitPrice,
                    'compare_at_price'   => $compPrice,
                    'qty'                => $qty,
                    'line_total'         => $lineTotal,
                ]);
            }

            // Recalculate and update order totals
            $total = max(0, $subtotal + $order->shipping_charge + $order->cod_charge - $order->discount);
            $order->update([
                'subtotal' => $subtotal,
                'total'    => $total,
            ]);
        }
    }
}
