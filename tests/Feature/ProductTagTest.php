<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTagTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Category $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin_tag_test_' . uniqid() . '@example.com',
            'role' => 'admin',
        ]);

        $this->category = Category::create([
            'name' => 'Tag Test Category',
            'slug' => 'tag-test-category-' . uniqid(),
            'is_active' => true,
        ]);
    }

    public function test_tag_resolution_creates_new_tags_and_resolves_existing(): void
    {
        $existingTag = Tag::create([
            'name' => 'Silk Elegance',
            'slug' => 'silk-elegance-' . uniqid(),
            'color' => '#E11D48',
        ]);

        $input = [$existingTag->id, 'Handwoven Royal Zari', 'Organic Cotton'];
        $resolvedIds = Tag::resolveTagIds($input);

        $this->assertCount(3, $resolvedIds);
        $this->assertContains($existingTag->id, $resolvedIds);

        $newTag1 = Tag::where('name', 'Handwoven Royal Zari')->first();
        $this->assertNotNull($newTag1);
        $this->assertContains($newTag1->id, $resolvedIds);
        $this->assertEquals('handwoven-royal-zari', $newTag1->slug);
    }

    public function test_product_can_attach_tags_and_filter_scope(): void
    {
        $tag = Tag::create([
            'name' => 'Bridal Special',
            'slug' => 'bridal-special-' . uniqid(),
            'color' => '#DC2626',
        ]);

        $product = Product::create([
            'name' => 'Royal Bridal Saree',
            'slug' => 'royal-bridal-saree-' . uniqid(),
            'sku' => 'SKU-BR-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 24999.00,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);

        $product->tags()->sync([$tag->id]);

        $this->assertTrue($product->tags->contains($tag->id));
        $this->assertEquals('Bridal Special', $product->tags_string);

        $matchedProducts = Product::withTag($tag->id)->get();
        $this->assertTrue($matchedProducts->contains($product->id));

        $matchedBySlug = Product::withTag($tag->slug)->get();
        $this->assertTrue($matchedBySlug->contains($product->id));
    }

    public function test_admin_can_store_product_with_tags(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'name' => 'Festive Anarkali',
            'sku' => 'ANR-FEST-001',
            'category_id' => $this->category->id,
            'price' => 8999.00,
            'stock_quantity' => 10,
            'is_active' => 1,
            'colors' => [
                0 => [
                    'name' => 'Ruby Red',
                    'hex' => '#990000',
                    'sizes' => [
                        0 => [
                            'size' => 'M',
                            'sku' => 'ANR-FEST-RED-M',
                            'price' => 8999.00,
                            'stock' => 10,
                        ],
                    ],
                ],
            ],
            'tags' => ['Festive Season', 'Pure Chanderi'],
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('name', 'Festive Anarkali')->first();
        $this->assertNotNull($product);
        $this->assertCount(2, $product->tags);
        $this->assertTrue($product->tags->pluck('name')->contains('Festive Season'));
    }

    public function test_admin_can_filter_products_by_tag_in_catalog(): void
    {
        $tagA = Tag::create(['name' => 'Tag Alpha', 'slug' => 'tag-alpha-' . uniqid()]);
        $tagB = Tag::create(['name' => 'Tag Beta', 'slug' => 'tag-beta-' . uniqid()]);

        $prodA = Product::create([
            'name' => 'Alpha Saree',
            'slug' => 'alpha-saree-' . uniqid(),
            'sku' => 'SKU-A-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 5000,
            'stock_quantity' => 10,
        ]);
        $prodA->tags()->sync([$tagA->id]);

        $prodB = Product::create([
            'name' => 'Beta Lehenga',
            'slug' => 'beta-lehenga-' . uniqid(),
            'sku' => 'SKU-B-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 12000,
            'stock_quantity' => 5,
        ]);
        $prodB->tags()->sync([$tagB->id]);

        $response = $this->actingAs($this->admin)->get(route('admin.products.index', ['tag_id' => $tagA->id]));
        $response->assertOk();
        $response->assertSee('Alpha Saree');
        $response->assertDontSee('Beta Lehenga');
    }

    public function test_admin_can_manage_tags_crud(): void
    {
        // Store
        $storeResponse = $this->actingAs($this->admin)->post(route('admin.tags.store'), [
            'name' => 'Velvet Edit',
            'description' => 'Rich velvet winter outfits',
            'color' => '#7C3AED',
        ]);
        $storeResponse->assertRedirect();

        $tag = Tag::where('name', 'Velvet Edit')->first();
        $this->assertNotNull($tag);
        $this->assertEquals('#7C3AED', $tag->color);

        // Update
        $updateResponse = $this->actingAs($this->admin)->put(route('admin.tags.update', $tag), [
            'name' => 'Velvet Edit 2026',
            'description' => 'Updated winter collection',
            'color' => '#DC2626',
        ]);
        $updateResponse->assertRedirect();

        $tag->refresh();
        $this->assertEquals('Velvet Edit 2026', $tag->name);
        $this->assertEquals('#DC2626', $tag->color);

        // Destroy
        $destroyResponse = $this->actingAs($this->admin)->delete(route('admin.tags.destroy', $tag));
        $destroyResponse->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }

    public function test_storefront_can_filter_and_search_by_tag(): void
    {
        $tag = Tag::create(['name' => 'Handcrafted Silk', 'slug' => 'handcrafted-silk-' . uniqid()]);

        $prod = Product::create([
            'name' => 'Kanjivaram Saree Special',
            'slug' => 'kanjivaram-saree-special-' . uniqid(),
            'sku' => 'SKU-KANJ-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 15000,
            'stock_quantity' => 5,
            'is_active' => true,
        ]);
        $prod->tags()->sync([$tag->id]);

        $otherProd = Product::create([
            'name' => 'Georgette Kurti',
            'slug' => 'georgette-kurti-' . uniqid(),
            'sku' => 'SKU-GEO-' . uniqid(),
            'category_id' => $this->category->id,
            'price' => 3000,
            'stock_quantity' => 10,
            'is_active' => true,
        ]);

        // Filter by ?tag=slug
        $response = $this->get(route('search', ['tag' => $tag->slug]));
        $response->assertOk();
        $response->assertSee('Kanjivaram Saree Special');
        $response->assertDontSee('Georgette Kurti');

        // Free search matching tag name
        $searchResponse = $this->get(route('search', ['q' => 'Handcrafted Silk']));
        $searchResponse->assertOk();
        $searchResponse->assertSee('Kanjivaram Saree Special');
    }
}
