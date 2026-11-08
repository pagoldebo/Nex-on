<?php

namespace Database\Seeders;

use App\Models\CustomPage;
use App\Models\DeliveryPersonnel;
use App\Models\Order;
use App\Models\Outlet;
use App\Models\OutletStock;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\ProductVariant;
use App\Models\PurchaseOrder;
use App\Models\StockTransfer;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EnterpriseDemoSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Staff & Admin Accounts
        $admin = User::updateOrCreate(
            ['email' => 'admin@nexon.app'],
            [
                'name'              => 'Super Administrator',
                'password'          => Hash::make('admin123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        $staff = User::updateOrCreate(
            ['email' => 'staff@nexon.com'],
            [
                'name'              => 'Meera Kapoor',
                'password'          => Hash::make('staff123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 2. Physical Outlets & Warehouses
        $colaba = Outlet::updateOrCreate(
            ['code' => 'MUM-COL-01'],
            [
                'name'       => 'Colaba Flagship Boutique',
                'type'       => 'store',
                'address'    => 'Ground Floor, Regal Circle, Colaba Causeway, Mumbai 400001',
                'city'       => 'Mumbai',
                'phone'      => '+91 22 2202 1100',
                'email'      => 'colaba@nexon.com',
                'is_central' => true,
                'is_active'  => true,
                'channel'    => 'all',
            ]
        );

        $indiranagar = Outlet::updateOrCreate(
            ['code' => 'BLR-IND-02'],
            [
                'name'       => 'Indiranagar Experience Studio',
                'type'       => 'store',
                'address'    => '100ft Road, HAL 2nd Stage, Indiranagar, Bengaluru 560038',
                'city'       => 'Bengaluru',
                'phone'      => '+91 80 4112 3344',
                'email'      => 'bengaluru@nexon.com',
                'is_central' => false,
                'is_active'  => true,
                'channel'    => 'pos',
            ]
        );

        $bhiwandi = Outlet::updateOrCreate(
            ['code' => 'BHW-LOG-03'],
            [
                'name'       => 'Bhiwandi Central Fulfillment Hub',
                'type'       => 'warehouse',
                'address'    => 'Warehouse Bay 14, Mumbai-Nashik Expressway, Bhiwandi 421302',
                'city'       => 'Bhiwandi',
                'phone'      => '+91 2522 667788',
                'email'      => 'warehouse@nexon.com',
                'is_central' => false,
                'is_active'  => true,
                'channel'    => 'online',
            ]
        );

        // 3. Map Stock Allocations Across Outlets
        $variants = ProductVariant::take(10)->get();
        foreach ($variants as $idx => $v) {
            // Allocate stocks
            OutletStock::updateOrCreate(
                ['outlet_id' => $colaba->id, 'product_variant_id' => $v->id],
                ['stock' => 12 + ($idx % 5), 'shelf_bin_location' => 'SHOWCASE-A' . ($idx + 1)]
            );

            OutletStock::updateOrCreate(
                ['outlet_id' => $indiranagar->id, 'product_variant_id' => $v->id],
                ['stock' => 8 + ($idx % 4), 'shelf_bin_location' => 'RACK-B' . ($idx + 1)]
            );

            OutletStock::updateOrCreate(
                ['outlet_id' => $bhiwandi->id, 'product_variant_id' => $v->id],
                ['stock' => 45 + ($idx * 3), 'shelf_bin_location' => 'PALLET-C' . ($idx + 1)]
            );
        }

        // 4. Inter-Store Stock Transfers
        if ($variants->count() >= 2) {
            // Transfer 1: Received
            $trfReceived = StockTransfer::updateOrCreate(
                ['transfer_no' => 'TRF-20261016-0001'],
                [
                    'from_outlet_id' => $bhiwandi->id,
                    'to_outlet_id'   => $colaba->id,
                    'status'         => 'received',
                    'dispatched_by'  => $admin->id,
                    'received_by'    => $staff->id,
                    'dispatched_at'  => now()->subDays(2),
                    'received_at'    => now()->subDay(),
                    'notes'          => 'Pre-festive restock of mulberry silk robes. Tracking: EXP-BHW-COL-9901',
                ]
            );

            $trfReceived->items()->delete();
            $trfReceived->items()->create([
                'product_variant_id' => $variants[0]->id,
                'qty_sent'           => 10,
                'qty_received'       => 10,
            ]);

            // Transfer 2: Dispatched & In-Transit
            $trfInTransit = StockTransfer::updateOrCreate(
                ['transfer_no' => 'TRF-20261016-0002'],
                [
                    'from_outlet_id' => $bhiwandi->id,
                    'to_outlet_id'   => $indiranagar->id,
                    'status'         => 'dispatched',
                    'dispatched_by'  => $admin->id,
                    'dispatched_at'  => now()->subHours(6),
                    'notes'          => 'New bridal trousseau samples for Bengaluru studio launch. Tracking: BLUEDART-88992211',
                ]
            );

            $trfInTransit->items()->delete();
            $trfInTransit->items()->create([
                'product_variant_id' => $variants[1]->id,
                'qty_sent'           => 6,
                'qty_received'       => 0,
            ]);
        }

        // 5. Visual Page Builder CMS Pages
        CustomPage::updateOrCreate(
            ['slug' => 'bridal-silk-trousseau'],
            [
                'title'            => 'Royal Bridal Silk Trousseau 2026',
                'meta_title'       => 'Handcrafted Bridal Silk Collection | Haute Couture',
                'meta_description' => 'Explore the handcrafted bridal trousseau collection in Varanasi pure mulberry silk and French chantilly lace.',
                'is_published'     => true,
                'content_blocks'   => [
                    [
                        'type'        => 'hero',
                        'headline'    => 'Handcrafted Bridal Grandeur',
                        'subheadline' => 'Woven in pure mulberry silk and embellished with hand-carved zardozi motifs for timeless royal elegance.',
                        'cta_text'    => 'Explore The Edit',
                        'cta_link'    => '/#products',
                    ],
                    [
                        'type'        => 'countdown',
                        'title'       => 'Exclusive Bridal Trunk Show Drops In',
                        'target_date' => now()->addDays(5)->format('Y-m-d\TH:i'),
                    ],
                    [
                        'type'        => 'product_grid',
                        'title'       => 'Iconic Bridal Ensembles',
                        'limit'       => '4',
                    ],
                    [
                        'type'        => 'testimonials',
                        'quote'       => 'The craftsmanship of Saaya’s silk robe exceeded all expectations on my wedding morning. Pure royal elegance.',
                        'author'      => 'Aishwarya Rai Bachchan',
                        'location'    => 'Mumbai &amp; London',
                    ],
                    [
                        'type'        => 'faq',
                        'question'    => 'How do I book a private bridal consultation?',
                        'answer'      => 'Visit our Colaba Flagship or Indiranagar Experience Studio for a one-on-one measurement fitting with our master couturiers.',
                    ],
                    [
                        'type'        => 'newsletter',
                        'headline'    => 'Join The VIP Private Salon',
                        'subheadline' => 'Receive private invites to trunk shows and bespoke bridal preview edits.',
                    ],
                ],
            ]
        );

        CustomPage::updateOrCreate(
            ['slug' => 'summer-resort-edit'],
            [
                'title'            => 'Summer Resort Wear & Loungewear',
                'meta_title'       => 'Breezy Silks & French Lace Loungewear 2026',
                'meta_description' => 'Lightweight mulberry chemises and French lace sets designed for sun-kissed Riviera getaways.',
                'is_published'     => true,
                'content_blocks'   => [
                    [
                        'type'        => 'hero',
                        'headline'    => 'Sun-Drenched Riviera Chic',
                        'subheadline' => 'Ultralight pure mulberry silk designed for Mediterranean sunsets and relaxed private villas.',
                        'cta_text'    => 'Shop Resort Wear',
                        'cta_link'    => '/#products',
                    ],
                    [
                        'type'        => 'product_grid',
                        'title'       => 'Curated Riviera Looks',
                        'limit'       => '4',
                    ],
                    [
                        'type'        => 'newsletter',
                        'headline'    => 'Unlock Complimentary Resort Delivery',
                        'subheadline' => 'Subscribe for priority express shipping to over 24 global destinations.',
                    ],
                ],
            ]
        );

        // 6. Delivery Personnel Fleet & Order Assignments
        $driver1 = DeliveryPersonnel::updateOrCreate(
            ['phone' => '9876543210'],
            [
                'name'           => 'Rahul Sharma',
                'email'          => 'rahul.courier@nexon.com',
                'vehicle_type'   => 'bike',
                'vehicle_number' => 'MH 01 AB 1001',
                'status'         => 'active',
            ]
        );

        $driver2 = DeliveryPersonnel::updateOrCreate(
            ['phone' => '9876543211'],
            [
                'name'           => 'Deepak Verma',
                'email'          => 'deepak.courier@nexon.com',
                'vehicle_type'   => 'scooter',
                'vehicle_number' => 'KA 05 EV 2020',
                'status'         => 'active',
            ]
        );

        $driver3 = DeliveryPersonnel::updateOrCreate(
            ['phone' => '9876543212'],
            [
                'name'           => 'Sanjay Patil',
                'email'          => 'sanjay.fleet@nexon.com',
                'vehicle_type'   => 'van',
                'vehicle_number' => 'MH 04 VN 8888',
                'status'         => 'active',
            ]
        );

        // Assign sample orders to drivers
        $orders = Order::latest()->take(3)->get();
        if ($orders->count() >= 3) {
            // Order 1: Assigned to Deepak
            $orders[0]->update([
                'delivery_personnel_id' => $driver2->id,
                'delivery_status'       => 'assigned',
            ]);

            // Order 2: Out for delivery with Rahul
            $orders[1]->update([
                'delivery_personnel_id' => $driver1->id,
                'delivery_status'       => 'out_for_delivery',
                'status'                => Order::STATUS_SHIPPED,
                'shipped_at'            => now()->subHours(2),
            ]);

            // Order 3: Delivered by Sanjay with proof
            $orders[2]->update([
                'delivery_personnel_id' => $driver3->id,
                'delivery_status'       => 'delivered',
                'status'                => Order::STATUS_DELIVERED,
                'delivered_at'          => now()->subHours(4),
                'proof_signature'       => 'Mrs. Radhika Merchant',
                'proof_notes'           => 'Package handed directly to concierge reception with boutique gift wrap intact.',
            ]);
        }

        // 7. Suppliers & Purchase Orders
        $supplier1 = Supplier::updateOrCreate(
            ['code' => 'SUP-VAR-01'],
            [
                'name'           => 'Varanasi Mulberry Silk Mills',
                'contact_name'   => 'Pandit Rakesh Mishra',
                'email'          => 'orders@varanasi-silk.in',
                'phone'          => '+91 542 250 8899',
                'lead_time_days' => 7,
                'gst_number'     => '09AABCU9603R1ZM',
                'address'        => 'Ghat Road, Chowk, Varanasi, UP 221001',
                'is_active'      => true,
            ]
        );

        $supplier2 = Supplier::updateOrCreate(
            ['code' => 'SUP-SUR-02'],
            [
                'name'           => 'Surat Premium Lace & Weaves',
                'contact_name'   => 'Bhavesh Shah',
                'email'          => 'sales@suratlaceweaves.com',
                'phone'          => '+91 261 245 1122',
                'lead_time_days' => 5,
                'gst_number'     => '24AABCS4455C1ZL',
                'address'        => 'Ring Road Textile Market, Surat, Gujarat 395002',
                'is_active'      => true,
            ]
        );

        // Update variant cost prices & suppliers
        foreach ($variants as $v) {
            $v->update([
                'supplier_id'      => $supplier1->id,
                'cost_price'       => (int) ($v->price * 0.45),
                'reorder_threshold'=> 5,
                'reorder_qty'      => 20,
            ]);

            // 8. Batches (FIFO & Expiry)
            ProductBatch::updateOrCreate(
                ['batch_number' => 'BATCH-' . $v->sku . '-01'],
                [
                    'product_variant_id' => $v->id,
                    'lot_number'         => 'LOT-2026-A' . $v->id,
                    'mfg_date'           => now()->subMonths(2),
                    'expiry_date'        => now()->addMonths(22),
                    'initial_qty'        => 30,
                    'remaining_qty'      => $v->stock,
                    'cost_price'         => (int) ($v->price * 0.45),
                    'status'             => 'active',
                    'notes'              => 'QC inspected and certified pure mulberry silk batch.',
                ]
            );
        }
    }
}
