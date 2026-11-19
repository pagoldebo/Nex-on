<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Product;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductCollectionTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_col_test_' . uniqid() . '@example.com',
            'role' => 'admin',
        ]);

        $this->category = Category::create([
            'name' => 'Collection Test Category',
            'slug' => 'collection-test-category-' . uniqid(),
            'is_active' => true,
        ]);
    }

    public function test_collection_resolution_creates_new_collections_and_resolves_existing(): void
    {
        $existingCol = Collection::create([
            'name' => 'Royal Bridal Trousseau',
            'slug' => 'royal-bridal-trousseau-' . uniqid(),
            'description' => 'Opulent bridal garments',
            'is_active' => true,
        ]);

        $input = [$existingCol->id, 'Festive Velvet Edit', 'Benarasi Heritage Archive'];
        $resolvedIds = Collection::resolveCollectionIds($input);

        $this->assertCount(3, $resolvedIds);
        $this->assertContains($existingCol->id, $resolvedIds);

        $newCol = Collection::where('name', 'Festive Velvet Edit')->first();
        $this->assertNotNull($newCol);
        $this->assertContains($newCol->id, $resolvedIds);
        $this->assertEquals('festive-velvet-edit', $newCol->slug);
    }

    public function test_product_can_attach_collections_and_filter_scope(): void
    {
        $collection = Collection::create([
            'name' => 'Heritage Couture',
            'slug' => 'heritage-couture-' . uniqid(),
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Zardozi Velvet Sherwani',
            'slug' => 'zardozi-velvet-sherwani-' . uniqid(),
            'sku' => 'SKU-ZAR-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 45000.00,
            'stock_quantity' => 4,
            'is_active' => true,
        ]);

        $product->collections()->sync([$collection->id]);

        $this->assertTrue($product->collections->contains($collection->id));
        $this->assertEquals('Heritage Couture', $product->collections_string);

        $matchedProducts = Product::withCollection($collection->id)->get();
        $this->assertTrue($matchedProducts->contains($product->id));

        $matchedBySlug = Product::withCollection($collection->slug)->get();
        $this->assertTrue($matchedBySlug->contains($product->id));
    }

    public function test_admin_can_store_product_with_collections(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Imperial Brocade Lehenga',
            'sku' => 'LEH-IMP-001',
            'category_id' => $this->category->id,
            'price' => 55000.00,
            'stock_quantity' => 8,
            'is_active' => 1,
            'colors' => [
                0 => [
                    'name' => 'Wine Crimson',
                    'hex' => '#720e1e',
                    'sizes' => [
                        0 => [
                            'size' => 'Free Size',
                            'sku' => 'LEH-IMP-CRIM-FS',
                            'price' => 55000.00,
                            'stock' => 8,
                        ],
                    ],
                ],
            ],
            'collections' => ['Royal Bridal 2026', 'Velvet Luxe'],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Imperial Brocade Lehenga')->first();
        $this->assertNotNull($product);
        $this->assertCount(2, $product->collections);
        $this->assertTrue($product->collections->pluck('name')->contains('Royal Bridal 2026'));
    }

    public function test_admin_can_update_product_collections(): void
    {
        $product = Product::create([
            'name' => 'Handloom Jamdani Sari',
            'slug' => 'handloom-jamdani-sari-' . uniqid(),
            'sku' => 'SKU-JAM-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 22000.00,
            'stock_quantity' => 6,
            'is_active' => true,
        ]);

        $initialCol = Collection::create([
            'name' => 'Initial Collection',
            'slug' => 'initial-collection-' . uniqid(),
        ]);
        $product->collections()->sync([$initialCol->id]);

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), [
            'name' => 'Handloom Jamdani Sari Premium',
            'sku' => $product->sku,
            'category_id' => $this->category->id,
            'price' => 24000.00,
            'stock_quantity' => 6,
            'is_active' => 1,
            'colors' => [
                0 => [
                    'name' => 'Ivory White',
                    'hex' => '#FFFFF0',
                    'sizes' => [
                        0 => [
                            'size' => 'Free Size',
                            'sku' => 'SKU-JAM-IVORY-FS',
                            'price' => 24000.00,
                            'stock' => 6,
                        ],
                    ],
                ],
            ],
            'collections' => ['Summer Linen Edit', 'Bengal Handloom Heritage'],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product->refresh();
        $this->assertCount(2, $product->collections);
        $this->assertTrue($product->collections->pluck('name')->contains('Summer Linen Edit'));
        $this->assertFalse($product->collections->pluck('name')->contains('Initial Collection'));
    }

    public function test_admin_can_filter_products_by_collection_in_catalog(): void
    {
        $colA = Collection::create(['name' => 'Collection Alpha', 'slug' => 'collection-alpha-' . uniqid()]);
        $colB = Collection::create(['name' => 'Collection Beta', 'slug' => 'collection-beta-' . uniqid()]);

        $prodA = Product::create([
            'name' => 'Alpha Saree',
            'slug' => 'alpha-saree-' . uniqid(),
            'sku' => 'SKU-COLA-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 5000,
            'stock_quantity' => 10,
        ]);
        $prodA->collections()->sync([$colA->id]);

        $prodB = Product::create([
            'name' => 'Beta Lehenga',
            'slug' => 'beta-lehenga-' . uniqid(),
            'sku' => 'SKU-COLB-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 12000,
            'stock_quantity' => 5,
        ]);
        $prodB->collections()->sync([$colB->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['collection_id' => $colA->id]));
        $response->assertOk();
        $response->assertSee('Alpha Saree');
        $response->assertDontSee('Beta Lehenga');
    }

    public function test_admin_can_manage_collections_crud(): void
    {
        // Store
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.collections.store'), [
            'name' => 'Kashmir Pashmina Showcase',
            'slug' => 'kashmir-pashmina-showcase',
            'description' => 'Authentic handspun Himalayan pashminas and shawls',
            'sort_order' => 1,
            'is_active' => 1,
        ]);
        $storeResponse->assertRedirect();

        $collection = Collection::where('slug', 'kashmir-pashmina-showcase')->first();
        $this->assertNotNull($collection);
        $this->assertEquals('Kashmir Pashmina Showcase', $collection->name);
        $this->assertTrue((bool)$collection->is_active);

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.collections.update', $collection), [
            'name' => 'Kashmir Pashmina & Silk Showcase',
            'slug' => 'kashmir-pashmina-silk',
            'description' => 'Updated collection story and curation',
            'sort_order' => 2,
            'is_active' => 1,
        ]);
        $updateResponse->assertRedirect();

        $collection->refresh();
        $this->assertEquals('Kashmir Pashmina & Silk Showcase', $collection->name);
        $this->assertEquals('kashmir-pashmina-silk', $collection->slug);

        // Destroy
        $destroyResponse = $this->actingAs($this->admin)->delete(route('admin.collections.destroy', $collection));
        $destroyResponse->assertRedirect();
        $this->assertDatabaseMissing('collections', ['id' => $collection->id]);
    }

    public function test_storefront_can_resolve_custom_collection_by_slug(): void
    {
        $collection = Collection::create([
            'name' => 'Royal Trousseau 2026',
            'slug' => 'royal-trousseau-2026',
            'description' => 'The definitive bridal curation',
            'is_active' => true,
        ]);

        $prod = Product::create([
            'name' => 'Heritage Gold Brocade Odhni',
            'slug' => 'heritage-gold-brocade-odhni-' . uniqid(),
            'sku' => 'SKU-ODH-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 18000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        $prod->collections()->sync([$collection->id]);

        $otherProd = Product::create([
            'name' => 'Casual Cotton Tunic',
            'slug' => 'casual-cotton-tunic-' . uniqid(),
            'sku' => 'SKU-CAS-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 2000,
            'stock_quantity' => 15,
            'is_active' => true,
        ]);

        $response = $this->get(route('collections.show', ['slug' => 'royal-trousseau-2026']));
        $response->assertOk();
        $response->assertSee('Heritage Gold Brocade Odhni');
        $response->assertDontSee('Casual Cotton Tunic');
    }
}
