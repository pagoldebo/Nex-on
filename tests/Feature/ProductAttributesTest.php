<?php

namespace Tests\Feature;

use App\Models\Attribute;
use App\Models\AttributeGroup;
use App\Models\Category;
use App\Models\Product;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductAttributesTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin.attributes@nexon.com',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => true]);

        $category = Category::create([
            'name'      => 'Premium Apparel',
            'slug'      => 'premium-apparel',
            'is_active' => true,
        ]);

        $this->product = Product::create([
            'category_id' => $category->id,
            'name'        => 'Pure Mulberry Silk Robe',
            'slug'        => 'pure-mulberry-silk-robe',
            'sku'         => 'SLK-ROBE-001',
            'is_active'   => true,
        ]);
    }

    public function test_attributes_index_loads_successfully()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.attributes.index'));

        $response->assertStatus(200);
        $response->assertSee('Product Attributes &amp; Specifications Engine', false);
    }

    public function test_admin_can_create_attribute_group()
    {
        $response = $this->actingAs($this->admin)->post(route('admin.attributes.groups.store'), [
            'name'       => 'Material & Care',
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('admin.attributes.index'));
        $this->assertDatabaseHas('attribute_groups', [
            'name'       => 'Material & Care',
            'sort_order' => 1,
        ]);
    }

    public function test_admin_can_create_attribute_with_options()
    {
        $group = AttributeGroup::create(['name' => 'Technical Specs', 'sort_order' => 0]);

        $response = $this->actingAs($this->admin)->post(route('admin.attributes.store'), [
            'attribute_group_id' => $group->id,
            'name'               => 'Fabric Composition',
            'code'               => 'fabric_composition',
            'type'               => 'select',
            'options'            => 'Silk, Satin, Cashmere, Chiffon',
            'is_filterable'      => 1,
            'is_comparable'      => 1,
        ]);

        $response->assertRedirect(route('admin.attributes.index'));
        $this->assertDatabaseHas('attributes', [
            'name'               => 'Fabric Composition',
            'code'               => 'fabric_composition',
            'type'               => 'select',
            'attribute_group_id' => $group->id,
            'is_filterable'      => 1,
            'is_comparable'      => 1,
        ]);

        $attribute = Attribute::where('code', 'fabric_composition')->first();
        $this->assertCount(4, $attribute->options);
        $this->assertContains('Cashmere', $attribute->options);
    }

    public function test_admin_can_update_attribute()
    {
        $attribute = Attribute::create([
            'name'          => 'Collar Style',
            'code'          => 'collar_style',
            'type'          => 'text',
            'is_filterable' => true,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.attributes.update', $attribute), [
            'name'          => 'Collar Cut',
            'code'          => 'collar_cut',
            'type'          => 'select',
            'options'       => 'Spread, Mandarin, Classic',
            'is_filterable' => 1,
            'is_comparable' => 1,
        ]);

        $response->assertRedirect(route('admin.attributes.index'));
        $attribute->refresh();
        $this->assertEquals('Collar Cut', $attribute->name);
        $this->assertEquals('collar_cut', $attribute->code);
        $this->assertEquals('select', $attribute->type);
    }

    public function test_admin_can_delete_attribute()
    {
        $attribute = Attribute::create([
            'name' => 'Temporary Spec',
            'code' => 'temp_spec',
            'type' => 'text',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.attributes.destroy', $attribute));

        $response->assertRedirect(route('admin.attributes.index'));
        $this->assertDatabaseMissing('attributes', ['id' => $attribute->id]);
    }

    public function test_admin_can_save_product_attribute_values()
    {
        $fabricAttr = Attribute::create([
            'name' => 'Fabric',
            'code' => 'fabric',
            'type' => 'text',
        ]);

        $dryCleanAttr = Attribute::create([
            'name' => 'Dry Clean Only',
            'code' => 'dry_clean_only',
            'type' => 'boolean',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.products.attributes.save', $this->product), [
            'attributes' => [
                $fabricAttr->id   => '100% Grade 6A Mulberry Silk',
                $dryCleanAttr->id => '1',
            ],
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id'   => $this->product->id,
            'attribute_id' => $fabricAttr->id,
            'value_text'   => '100% Grade 6A Mulberry Silk',
        ]);

        $this->assertDatabaseHas('product_attribute_values', [
            'product_id'   => $this->product->id,
            'attribute_id' => $dryCleanAttr->id,
            'value_text'   => '1',
        ]);

        $this->product->refresh();
        $this->assertCount(2, $this->product->attributeValues);
        $this->assertEquals('100% Grade 6A Mulberry Silk', $this->product->attributeValues->first()->value_text);
    }
}

