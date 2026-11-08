<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * TemplateSeeder
 *
 * Seeds the four storefront themes into the `templates` table.
 * Saaya is set as the default active theme.
 *
 * Usage:
 *   php artisan db:seed --class=TemplateSeeder
 *
 * Safe to re-run: uses upsert on `key` so existing rows are updated, not duplicated.
 */
class TemplateSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            [
                'key'       => 'saaya',
                'name'      => 'Saaya',
                'is_active' => true,   // ← default active theme
            ],
            [
                'key'       => 'buttercup',
                'name'      => 'Buttercup',
                'is_active' => false,
            ],
            [
                'key'       => 'vyra',
                'name'      => 'Vyra',
                'is_active' => false,
            ],
            [
                'key'       => 'lumiere',
                'name'      => 'Lumière',
                'is_active' => false,
            ],
            [
                'key'       => 'organic',
                'name'      => 'Verdia',
                'label'     => 'Verdia Botanical Living',
                'is_active' => false,
            ],
        ];

        $hasActive = DB::table('templates')->where('is_active', true)->exists();
        $updateColumns = $hasActive 
            ? ['name', 'label', 'updated_at'] 
            : ['name', 'label', 'is_active', 'updated_at'];

        foreach ($themes as $theme) {
            DB::table('templates')->upsert(
                array_merge($theme, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ]),
                ['key'],         // unique column to match on
                $updateColumns   // columns to update on conflict
            );
        }

        $this->command->info('Templates seeded: saaya (active), buttercup, vyra, lumière.');
    }
}
