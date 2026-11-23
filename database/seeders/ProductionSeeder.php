<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use App\Services\CurrencyService;
use App\Services\RbacService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

/**
 * Class ProductionSeeder
 *
 * Seeds a clean, production-ready foundation with zero dummy/demo records:
 * - 5 official storefront themes (Saaya as active default)
 * - Standard roles and centralized permission taxonomy
 * - Default Super-Administrator user
 * - Essential baseline system and multi-currency settings
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Storefront Themes
        $themes = [
            ['key' => 'saaya',    'name' => 'Saaya',   'label' => 'Heritage Silk & Bridal Couture', 'is_active' => true],
            ['key' => 'buttercup','name' => 'Buttercup','label' => 'Pastel Intimates & Sleepwear',    'is_active' => false],
            ['key' => 'vyra',     'name' => 'Vyra',      'label' => 'Modern Noir & Streetwear',       'is_active' => false],
            ['key' => 'lumiere',  'name' => 'Lumière',   'label' => 'Atelier Silk & Minimalist',      'is_active' => false],
            ['key' => 'organic',  'name' => 'Verdia',    'label' => 'Verdia Botanical Living',        'is_active' => false],
        ];

        foreach ($themes as $theme) {
            DB::table('templates')->updateOrInsert(
                ['key' => $theme['key']],
                array_merge($theme, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 2. Seed Standard Roles & System Permissions
        app(RbacService::class)->syncDefaults();

        // 3. Seed Primary Administrator Account
        $admin = User::updateOrCreate(
            ['email' => 'admin@nexon.app'],
            [
                'name'              => 'Administrator',
                'password'          => Hash::make('Password@123'),
                'role'              => 'admin',
                'email_verified_at' => now(),
                'created_at'        => now(),
                'updated_at'        => now(),
            ]
        );

        $superAdminRole = Role::where('slug', 'super-admin')->first();
        if ($superAdminRole) {
            $admin->roles()->syncWithoutDetaching([$superAdminRole->id]);
        }

        // 4. Seed Essential Storefront & Multi-Currency Settings
        $essentialSettings = [
            'store_name'               => 'Nex-on',
            'support_email'            => 'support@nexon.app',
            'support_phone'            => '+91 98765 43210',
            'store_address'            => '100 Metro Avenue, Suite 400, Mumbai, Maharashtra 400001',
            'multi_currency_enabled'   => '1',
            'base_currency'            => 'INR',
            'enabled_currencies'       => json_encode(['INR', 'USD', 'EUR', 'GBP', 'AED', 'CAD', 'AUD', 'SGD', 'SAR', 'JPY']),
            'currency_rates'           => json_encode(app(CurrencyService::class)->getRates()),
            'free_shipping_threshold'  => '1499',
            'shipping_cost'            => '99',
            'enable_cod'               => '1',
            'cod_charge'               => '40',
            'tax_enabled'              => '0',
            'announcement_bar'         => 'Free delivery on orders over ₹1,499 | Use code WELCOME10 for 10% off',
            'ticker_enabled'           => '1',
        ];

        foreach ($essentialSettings as $key => $val) {
            DB::table('settings')->updateOrInsert(
                ['key' => $key],
                ['value' => $val, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
