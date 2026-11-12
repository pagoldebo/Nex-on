<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TemplateSeeder::class,
            CatalogueSeeder::class,
            HierarchicalCategoriesSeeder::class,
            SkuAndAddonSeeder::class,
            OrderSeeder::class,
            ReviewSeeder::class,
            StoreOperationsSeeder::class,
            StaffReferenceSeeder::class,
            EnterpriseDemoSeeder::class,
        ]);
    }
}
