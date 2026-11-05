<?php

namespace Tests\Feature;

use App\Models\CustomPage;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VisualPageBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);

        Template::create([
            'key'         => 'saaya',
            'name'        => 'Saaya Luxury',
            'is_active'   => true,
            'description' => 'Test Theme',
        ]);
    }

    public function test_admin_can_view_pages_list_and_builder_screen()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.pages.index'));

        $response->assertStatus(200);
        $response->assertSee('Custom Pages &amp; Landing Microsites', false);

        $createResp = $this->actingAs($this->admin)->get(route('admin.pages.create'));
        $createResp->assertStatus(200);
        $createResp->assertSee('Visual Block Page Builder');
        $createResp->assertSee('Hero Banner');
        $createResp->assertSee('Drop Timer');
    }

    public function test_admin_can_store_custom_page_with_modular_blocks()
    {
        $payload = [
            'title'            => 'Bridal Silk Trousseau Edit',
            'slug'             => 'bridal-silk-trousseau-edit',
            'meta_title'       => 'Luxury Bridal Silk Sets 2026',
            'meta_description' => 'Explore the handcrafted bridal edit in Varanasi mulberry silk.',
            'is_published'     => '1',
            'content_blocks'   => [
                [
                    'type'        => 'hero',
                    'headline'    => 'Handcrafted Bridal Grandeur',
                    'subheadline' => 'Limited edition sets woven for royal trousseaus.',
                    'cta_text'    => 'Order Now',
                    'cta_link'    => '/category/bridal',
                ],
                [
                    'type'        => 'countdown',
                    'title'       => 'Trunk Show Flash Window',
                    'subtitle'    => 'Expires in 48 hours',
                    'target_date' => '2026-12-31T23:59',
                    'coupon_code' => 'ROYAL20',
                ],
                [
                    'type'  => 'faq',
                    'title' => 'Trousseau FAQ',
                    'items' => [
                        ['q' => 'Is alteration complimentary?', 'a' => 'Yes, all bridal orders receive bespoke tailoring.'],
                    ],
                ],
            ],
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.pages.store'), $payload);

        $response->assertRedirect(route('admin.pages.index'));
        $this->assertDatabaseHas('custom_pages', [
            'slug'         => 'bridal-silk-trousseau-edit',
            'is_published' => true,
        ]);

        $page = CustomPage::where('slug', 'bridal-silk-trousseau-edit')->first();
        $this->assertEquals(3, $page->blocksCount());
    }

    public function test_storefront_renders_published_custom_page_and_blocks()
    {
        $page = CustomPage::create([
            'title'            => 'Midnight Velvet Drop',
            'slug'             => 'midnight-velvet-drop',
            'meta_title'       => 'Midnight Velvet Drop Preview',
            'meta_description' => 'Limited velvet sleepwear drop.',
            'is_published'     => true,
            'content_blocks'   => [
                [
                    'type'        => 'hero',
                    'headline'    => 'Midnight Luxury Velvet',
                    'subheadline' => 'Exquisite darkness in French velvet.',
                    'cta_text'    => 'View Collection',
                    'cta_link'    => '/category/velvet',
                ],
                [
                    'type'        => 'countdown',
                    'title'       => 'Countdown to Velvet',
                    'target_date' => '2026-12-31T00:00',
                    'coupon_code' => 'VELVETVIP',
                ],
            ],
        ]);

        $response = $this->get(route('custom-page.show', $page->slug));

        $response->assertStatus(200);
        $response->assertSee('Midnight Luxury Velvet');
        $response->assertSee('Countdown to Velvet');
        $response->assertSee('VELVETVIP');
    }

    public function test_unpublished_custom_page_returns_404_on_storefront()
    {
        $page = CustomPage::create([
            'title'          => 'Draft Secret Page',
            'slug'           => 'draft-secret-page',
            'is_published'   => false,
            'content_blocks' => [],
        ]);

        $response = $this->get(route('custom-page.show', $page->slug));

        $response->assertStatus(404);
    }

    public function test_admin_can_toggle_published_state_and_delete()
    {
        $page = CustomPage::create([
            'title'          => 'Temporary Event',
            'slug'           => 'temporary-event',
            'is_published'   => true,
            'content_blocks' => [],
        ]);

        $toggleResp = $this->actingAs($this->admin)->patch(route('admin.pages.toggle', $page));
        $toggleResp->assertRedirect();
        $page->refresh();
        $this->assertFalse($page->is_published);

        $deleteResp = $this->actingAs($this->admin)->delete(route('admin.pages.destroy', $page));
        $deleteResp->assertRedirect();
        $this->assertDatabaseMissing('custom_pages', ['id' => $page->id]);
    }
}
