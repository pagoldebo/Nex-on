<?php

namespace Database\Seeders;

use App\Models\Coupon;
use App\Models\PincodeZone;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class StoreOperationsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Settings
        $settings = [
            'free_shipping_threshold' => '1499',
            'shipping_cost'           => '99',
            'cod_charge'              => '40',
            'tax_percentage'          => '18',
            'gstin'                   => '27AABCS1429B1ZB',
            'support_phone'           => '+91 98765 43210',
            'support_whatsapp'        => '+91 98765 43210',
            'support_email'           => env('SUPPORT_EMAIL', env('MAIL_FROM_ADDRESS', 'support@nexon.app')),
            'store_address'           => '101 Fashion Boulevard, Bandra West, Mumbai, Maharashtra 400050',
            'enable_cod'              => '1',
            'maintenance_mode'        => '0',
            'announcement_bar'        => 'Complimentary Shipping on all orders over ₹1,499 | Use code WELCOME10 for 10% off',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }

        // 2. Coupons
        $coupons = [
            [
                'code'             => 'WELCOME10',
                'type'             => 'percentage',
                'value'            => 10,
                'min_order_amount' => 999,
                'max_uses'         => 1000,
                'expires_at'       => now()->addMonths(6),
                'is_active'        => true,
            ],
            [
                'code'             => 'FESTIVE500',
                'type'             => 'fixed',
                'value'            => 500,
                'min_order_amount' => 2499,
                'max_uses'         => 500,
                'expires_at'       => now()->addMonths(3),
                'is_active'        => true,
            ],
            [
                'code'             => 'LUXE15',
                'type'             => 'percentage',
                'value'            => 15,
                'min_order_amount' => 1999,
                'max_uses'         => 300,
                'expires_at'       => now()->addMonths(4),
                'is_active'        => true,
            ],
        ];

        foreach ($coupons as $coupon) {
            Coupon::updateOrCreate(['code' => $coupon['code']], $coupon);
        }

        // 3. Indian Pincodes
        $zones = [
            ['pincode' => '400001', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'cod_available' => true, 'delivery_days' => 2, 'is_serviceable' => true],
            ['pincode' => '400050', 'city' => 'Mumbai', 'state' => 'Maharashtra', 'cod_available' => true, 'delivery_days' => 2, 'is_serviceable' => true],
            ['pincode' => '110001', 'city' => 'New Delhi', 'state' => 'Delhi', 'cod_available' => true, 'delivery_days' => 2, 'is_serviceable' => true],
            ['pincode' => '560001', 'city' => 'Bengaluru', 'state' => 'Karnataka', 'cod_available' => true, 'delivery_days' => 3, 'is_serviceable' => true],
            ['pincode' => '700001', 'city' => 'Kolkata', 'state' => 'West Bengal', 'cod_available' => true, 'delivery_days' => 3, 'is_serviceable' => true],
            ['pincode' => '600001', 'city' => 'Chennai', 'state' => 'Tamil Nadu', 'cod_available' => true, 'delivery_days' => 3, 'is_serviceable' => true],
            ['pincode' => '500001', 'city' => 'Hyderabad', 'state' => 'Telangana', 'cod_available' => true, 'delivery_days' => 3, 'is_serviceable' => true],
            ['pincode' => '380001', 'city' => 'Ahmedabad', 'state' => 'Gujarat', 'cod_available' => true, 'delivery_days' => 3, 'is_serviceable' => true],
            ['pincode' => '302001', 'city' => 'Jaipur', 'state' => 'Rajasthan', 'cod_available' => true, 'delivery_days' => 3, 'is_serviceable' => true],
            ['pincode' => '226001', 'city' => 'Lucknow', 'state' => 'Uttar Pradesh', 'cod_available' => true, 'delivery_days' => 4, 'is_serviceable' => true],
            ['pincode' => '411001', 'city' => 'Pune', 'state' => 'Maharashtra', 'cod_available' => true, 'delivery_days' => 2, 'is_serviceable' => true],
        ];

        foreach ($zones as $zone) {
            PincodeZone::updateOrCreate(['pincode' => $zone['pincode']], $zone);
        }
    }
}
