<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductColor;
use App\Models\ProductVariant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SqlInjectionSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_scope_handles_parameterized_query_safely(): void
    {
        $category = Category::create(['name' => 'Test Cat', 'slug' => 'test-cat']);
        $product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Test Product',
            'slug'        => 'test-product',
        ]);

        $color = ProductColor::create([
            'product_id' => $product->id,
            'name'       => 'Blue',
            'hex'        => '#0000FF',
        ]);

        $v1 = ProductVariant::create([
            'product_id'        => $product->id,
            'product_color_id'  => $color->id,
            'sku'               => 'SKU-001',
            'size'              => 'M',
            'price'             => 100,
            'stock'             => 3,
            'reorder_threshold' => 5,
        ]);

        $v2 = ProductVariant::create([
            'product_id'        => $product->id,
            'product_color_id'  => $color->id,
            'sku'               => 'SKU-002',
            'size'              => 'L',
            'price'             => 100,
            'stock'             => 20,
            'reorder_threshold' => 5,
        ]);

        $lowStock = ProductVariant::lowStock()->get();

        $this->assertTrue($lowStock->contains('id', $v1->id));
        $this->assertFalse($lowStock->contains('id', $v2->id));
    }

    public function test_search_query_with_sql_injection_payload_does_not_break_or_inject(): void
    {
        $category = Category::create(['name' => 'Apparel', 'slug' => 'apparel']);
        Product::create([
            'category_id' => $category->id,
            'name'        => 'Cotton T-Shirt',
            'slug'        => 'cotton-tshirt',
            'is_active'   => true,
        ]);

        $payloads = [
            "' OR '1'='1",
            "'; DROP TABLE products; --",
            "\" OR \"\"=\"",
            "1' UNION SELECT 1,2,3,4,5--",
            "%%%",
            "___",
        ];

        foreach ($payloads as $payload) {
            $response = $this->get('/search?q=' . urlencode($payload));
            $response->assertStatus(200);
        }
    }

    public function test_category_and_product_sorting_does_not_execute_arbitrary_sql(): void
    {
        $category = Category::create(['name' => 'Dresses', 'slug' => 'dresses']);

        $maliciousSorts = [
            'id; DROP TABLE users;',
            'price_asc, (SELECT 1 FROM users WHERE 1=1)',
            'sleep(5)',
        ];

        foreach ($maliciousSorts as $sort) {
            $response = $this->get(route('category.show', $category->slug) . '?sort=' . urlencode($sort));
            $response->assertStatus(200);
        }
    }

    public function test_database_configuration_disables_emulated_prepares_for_mysql(): void
    {
        $mysqlConfig = config('database.connections.mysql');
        $this->assertArrayHasKey('options', $mysqlConfig);
        if (extension_loaded('pdo_mysql')) {
            $this->assertArrayHasKey(\PDO::ATTR_EMULATE_PREPARES, $mysqlConfig['options']);
            $this->assertFalse($mysqlConfig['options'][\PDO::ATTR_EMULATE_PREPARES]);
        }
    }
}
