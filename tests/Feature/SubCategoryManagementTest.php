<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SubCategoryManagementTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'debo.admin@nexon.luxury',
            'name' => 'Deborshi Karfa',
            'role' => 'admin',
        ]);

        \App\Models\Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);
        \App\Models\Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => false]);
        \App\Models\Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => false]);
        \App\Models\Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
    }

    public function test_can_create_multi_level_subcategories()
    {
        // Root: Men
        $men = Category::create([
            'name' => 'Men Test',
            'slug' => 'men-test',
            'parent_id' => null,
            'is_active' => true,
        ]);

        // Subcategory: Shirt
        $shirt = Category::create([
            'name' => 'Shirt Test',
            'slug' => 'shirt-test',
            'parent_id' => $men->id,
            'is_active' => true,
        ]);

        // Sub-subcategory: Formal Shirt
        $formalShirt = Category::create([
            'name' => 'Formal Shirt Test',
            'slug' => 'formal-shirt-test',
            'parent_id' => $shirt->id,
            'is_active' => true,
        ]);

        $this->assertTrue($men->isRoot());
        $this->assertFalse($shirt->isRoot());
        $this->assertFalse($formalShirt->isRoot());

        $this->assertEquals($men->id, $shirt->parent->id);
        $this->assertEquals($shirt->id, $formalShirt->parent->id);
        $this->assertEquals($men->id, $formalShirt->parent->parent->id);

        $this->assertCount(1, $men->children);
        $this->assertEquals('Shirt Test', $men->children->first()->name);

        $this->assertCount(1, $shirt->children);
        $this->assertEquals('Formal Shirt Test', $shirt->children->first()->name);

        // Test descendant IDs
        $descendants = $men->getAllDescendantIds();
        $this->assertContains($men->id, $descendants);
        $this->assertContains($shirt->id, $descendants);
        $this->assertContains($formalShirt->id, $descendants);

        // Test breadcrumbs
        $breadcrumbs = $formalShirt->getBreadcrumbs();
        $this->assertCount(3, $breadcrumbs);
        $this->assertEquals('Men Test', $breadcrumbs[0]->name);
        $this->assertEquals('Shirt Test', $breadcrumbs[1]->name);
        $this->assertEquals('Formal Shirt Test', $breadcrumbs[2]->name);

        // Clean up
        $formalShirt->delete();
        $shirt->delete();
        $men->delete();
    }

    public function test_flat_tree_ordering_and_indentation()
    {
        $women = Category::create([
            'name' => 'Women Test Tree',
            'slug' => 'women-test-tree',
            'parent_id' => null,
            'is_active' => true,
        ]);

        $bra = Category::create([
            'name' => 'Bra Test Tree',
            'slug' => 'bra-test-tree',
            'parent_id' => $women->id,
            'is_active' => true,
        ]);

        $bralette = Category::create([
            'name' => 'Bralette Test Tree',
            'slug' => 'bralette-test-tree',
            'parent_id' => $bra->id,
            'is_active' => true,
        ]);

        $flatTree = Category::getFlatTree();

        $womenItem = $flatTree->firstWhere('id', $women->id);
        $braItem = $flatTree->firstWhere('id', $bra->id);
        $braletteItem = $flatTree->firstWhere('id', $bralette->id);

        $this->assertNotNull($womenItem);
        $this->assertEquals(0, $womenItem->depth);

        $this->assertNotNull($braItem);
        $this->assertEquals(1, $braItem->depth);

        $this->assertNotNull($braletteItem);
        $this->assertEquals(2, $braletteItem->depth);

        // Clean up
        $bralette->delete();
        $bra->delete();
        $women->delete();
    }

    public function test_admin_category_store_with_parent_id()
    {
        $root = Category::create([
            'name' => 'Root Cat Test',
            'slug' => 'root-cat-test',
            'parent_id' => null,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Child Cat Test',
            'parent_id' => $root->id,
            'description' => 'Test child category description',
        ]);

        $response->assertRedirect(route('admin.categories.index'));

        $child = Category::where('name', 'Child Cat Test')->first();
        $this->assertNotNull($child);
        $this->assertEquals($root->id, $child->parent_id);

        $child->delete();
        $root->delete();
    }

    public function test_admin_category_update_prevents_cycles()
    {
        $root = Category::create(['name' => 'Cycle Root', 'slug' => 'cycle-root']);
        $sub = Category::create(['name' => 'Cycle Sub', 'slug' => 'cycle-sub', 'parent_id' => $root->id]);
        $subSub = Category::create(['name' => 'Cycle SubSub', 'slug' => 'cycle-sub-sub', 'parent_id' => $sub->id]);

        // Attempt to make root a child of its own sub-subcategory
        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $root->id), [
            'name' => 'Cycle Root Updated',
            'parent_id' => $subSub->id,
        ]);

        $response->assertSessionHasErrors(['parent_id']);
        $root->refresh();
        $this->assertNull($root->parent_id);

        // Attempt to make root its own parent
        $responseSelf = $this->actingAs($this->admin)->put(route('admin.categories.update', $root->id), [
            'name' => 'Cycle Root Updated Self',
            'parent_id' => $root->id,
        ]);

        $responseSelf->assertSessionHasErrors(['parent_id']);

        $subSub->delete();
        $sub->delete();
        $root->delete();
    }

    public function test_admin_cannot_delete_category_with_subcategories()
    {
        $root = Category::create(['name' => 'Parent Protect', 'slug' => 'parent-protect']);
        $sub = Category::create(['name' => 'Child Protect', 'slug' => 'child-protect', 'parent_id' => $root->id]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $root->id));

        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $root->id]);

        $sub->delete();
        $root->delete();
    }

    public function test_storefront_parent_category_displays_products_from_subcategories()
    {
        $parent = Category::create(['name' => 'Men Apparel', 'slug' => 'men-apparel']);
        $child = Category::create(['name' => 'Shirts', 'slug' => 'men-shirts', 'parent_id' => $parent->id]);

        $product = Product::create([
            'name' => 'Linen Casual Shirt',
            'slug' => 'linen-casual-shirt-test',
            'price' => 1999,
            'category_id' => $child->id,
            'is_active' => true,
            'stock_quantity' => 10,
        ]);

        // Viewing the parent category should show the product from the subcategory!
        $response = $this->get(route('category.show', $parent->slug));
        $response->assertStatus(200);
        $response->assertSee('Linen Casual Shirt');
        $response->assertSee('Shirts'); // subcategory pill

        $product->delete();
        $child->delete();
        $parent->delete();
    }

    public function test_admin_categories_index_screen_renders_successfully()
    {
        $parent = Category::create(['name' => 'Men Admin Test', 'slug' => 'men-admin-test']);
        $child = Category::create(['name' => 'Shirts Admin Test', 'slug' => 'shirts-admin-test', 'parent_id' => $parent->id]);
        $subChild = Category::create([
            'name' => 'Bralettes Admin Test',
            'slug' => 'bralettes-admin-test',
            'parent_id' => $child->id,
            'image' => 'categories/test-sub.webp',
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.categories.index'));
        $response->assertStatus(200);
        $response->assertSee('Men Admin Test');
        $response->assertSee('Shirts Admin Test');
        $response->assertSee('Bralettes Admin Test');
        // Both the subcategory with image and without image must render connector arrows
        $response->assertSee('↳');
        $response->assertSee('↳ ↳');
        // Auto-collapse accordion controls
        $response->assertSee('Auto-Collapse');
        $response->assertSee('Expand All');
        $response->assertSee('Collapse All');

        $subChild->delete();
        $child->delete();
        $parent->delete();
    }

    public function test_product_category_slugs_include_ancestor_slugs_for_home_tabs()
    {
        $grandparent = Category::create(['name' => 'Women Root', 'slug' => 'women-root']);
        $parent = Category::create(['name' => 'Lingerie Mid', 'slug' => 'lingerie-mid', 'parent_id' => $grandparent->id]);
        $child = Category::create(['name' => 'Bra Deep', 'slug' => 'bra-deep', 'parent_id' => $parent->id]);

        $product = Product::create([
            'name' => 'Silk Bra Test',
            'slug' => 'silk-bra-test',
            'category_id' => $child->id,
            'is_active' => true,
        ]);

        $slugs = $product->getAllCategorySlugs();
        $this->assertContains('bra-deep', $slugs);
        $this->assertContains('lingerie-mid', $slugs);
        $this->assertContains('women-root', $slugs);
        $this->assertStringContainsString('women-root', $product->category_slugs_string);

        $product->delete();
        $child->delete();
        $parent->delete();
        $grandparent->delete();
    }

    public function test_admin_products_screens_render_with_category_tree()
    {
        $parent = Category::create(['name' => 'Men Tree Test', 'slug' => 'men-tree-test']);
        $child = Category::create(['name' => 'Shirts Tree Test', 'slug' => 'shirts-tree-test', 'parent_id' => $parent->id]);

        $product = Product::create([
            'name' => 'Test Cotton Shirt',
            'slug' => 'test-cotton-shirt',
            'price' => 1299,
            'category_id' => $child->id,
            'is_active' => true,
            'stock_quantity' => 10,
        ]);

        // 1. Products index
        $resIndex = $this->actingAs($this->admin)->get(route('admin.products.index'));
        $resIndex->assertStatus(200);
        $resIndex->assertSee('Men Tree Test');
        $resIndex->assertSee('Shirts Tree Test');

        // 2. Products create
        $resCreate = $this->actingAs($this->admin)->get(route('admin.products.create'));
        $resCreate->assertStatus(200);
        $resCreate->assertSee('Men Tree Test');
        $resCreate->assertSee('Shirts Tree Test');

        // 3. Products edit
        $resEdit = $this->actingAs($this->admin)->get(route('admin.products.edit', $product));
        $resEdit->assertStatus(200);
        $resEdit->assertSee('Men Tree Test');
        $resEdit->assertSee('Shirts Tree Test');

        $product->delete();
        $child->delete();
        $parent->delete();
    }

    public function test_homepage_shop_by_category_section_shows_only_parent_categories_across_all_themes()
    {
        $parent = Category::create(['name' => 'Men Parent Only', 'slug' => 'men-parent-only']);
        $child = Category::create(['name' => 'Subcategory Never On Home', 'slug' => 'sub-never-on-home', 'parent_id' => $parent->id]);

        foreach (['buttercup', 'saaya', 'vyra', 'lumiere'] as $themeKey) {
            \App\Models\Template::query()->update(['is_active' => false]);
            \App\Models\Template::where('key', $themeKey)->update(['is_active' => true]);

            $response = $this->get(route('home'));
            $response->assertStatus(200);

            // Extract the Shop by Category section HTML
            $content = $response->getContent();
            preg_match('/Shop by Category.*?<\/section>/s', $content, $m);
            $section = $m[0] ?? '';

            $this->assertStringContainsString('Men Parent Only', $section, "Theme {$themeKey} should display parent category");
            $this->assertStringNotContainsString('Subcategory Never On Home', $section, "Theme {$themeKey} should NOT display subcategories in Shop by Category section");
        }

        $child->delete();
        $parent->delete();
    }
}
