<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\Template;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeroBannerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@nexon.com',
            'name'  => 'Admin User',
            'role'  => 'admin',
        ]);

        Template::create(['key' => 'vyra',      'name' => 'Vyra',      'is_active' => true]);
        Template::create(['key' => 'buttercup', 'name' => 'Buttercup', 'is_active' => false]);
        Template::create(['key' => 'lumiere',   'name' => 'Lumière',   'is_active' => false]);
        Template::create(['key' => 'saaya',     'name' => 'Saaya',     'is_active' => false]);
        Template::create(['key' => 'organic',   'name' => 'Organic',   'is_active' => false]);
    }

    public function test_admin_settings_index_renders_hero_banners_section()
    {
        $response = $this->actingAs($this->admin)->get(route('admin.settings.index'));

        $response->assertStatus(200);
        $response->assertSee('Homepage Hero Banners');
        $response->assertSee('Vyra Hero Image');
    }

    public function test_admin_can_upload_and_save_vyra_hero_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('vyra-hero.jpg', 200, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'vyra_hero_image' => $file,
            'vyra_hero_title' => "Fearless\nStyle",
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $savedPath = Setting::get('vyra_hero_image');
        $this->assertNotEmpty($savedPath);
        Storage::disk('public')->assertExists($savedPath);

        // Check that Vyra storefront renders the uploaded hero image
        Template::query()->update(['is_active' => false]);
        Template::where('key', 'vyra')->update(['is_active' => true]);

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);
        $storefront->assertSee(asset('images/' . $savedPath));
        $storefront->assertSee('Fearless');
    }

    public function test_admin_can_remove_vyra_hero_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('initial-hero.jpg', 200, 'image/jpeg');
        $path = $file->store('banners', 'public');
        Setting::set('vyra_hero_image', $path);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'remove_vyra_hero_image' => '1',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEmpty(Setting::get('vyra_hero_image'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_vyra_theme_renders_top_slider_with_banner_video()
    {
        Setting::set('vyra_hero_image', 'slider/custom-banner.mp4');

        Template::query()->update(['is_active' => false]);
        Template::where('key', 'vyra')->update(['is_active' => true]);
        Template::clearActiveCache();

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);

        // Verify video element and custom uploaded video source
        $storefront->assertSee(asset('images/slider/custom-banner.mp4'));
        $storefront->assertSee('hero-video');
        $storefront->assertSee('autoplay');
        $storefront->assertSee('muted');
        $storefront->assertSee('loop');
        $storefront->assertSee('playsinline');

        // Verify slider structure & branding
        $storefront->assertSee('Vyra Top Hero Slider');
        $storefront->assertSee('Drop 03');
        $storefront->assertSee('Shop Drop 03');
    }

    public function test_admin_can_upload_vyra_video_and_configure_explore_button()
    {
        Storage::fake('public');

        $video = UploadedFile::fake()->create('custom-vyra.mp4', 1024, 'video/mp4');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'vyra_hero_image'     => $video,
            'vyra_hero_btn2_text' => 'Custom Explore',
            'vyra_hero_btn2_url'  => 'https://example.com/custom-vyra-explore',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $savedPath = Setting::get('vyra_hero_image');
        $this->assertNotEmpty($savedPath);
        $this->assertStringEndsWith('.mp4', $savedPath);
        Storage::disk('public')->assertExists($savedPath);

        Template::query()->update(['is_active' => false]);
        Template::where('key', 'vyra')->update(['is_active' => true]);
        Template::clearActiveCache();

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);
        $storefront->assertSee(asset('images/' . $savedPath));
        $storefront->assertSee('Custom Explore');
        $storefront->assertSee('https://example.com/custom-vyra-explore');
    }

    public function test_saaya_theme_renders_full_width_multi_slide_auto_slider_and_no_shop_men()
    {
        \App\Models\Category::create([
            'name' => 'Men',
            'slug' => 'men',
            'is_active' => true,
        ]);

        Template::query()->update(['is_active' => false]);
        Template::where('key', 'saaya')->update(['is_active' => true]);
        Template::clearActiveCache();

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);

        // Verify full width slider structure & auto-play attributes
        $storefront->assertSee('Saaya Hero Slider');
        $storefront->assertSee('startAutoplay()');
        $storefront->assertSee('totalSlides: 5', false);
        $storefront->assertSee('Crafted for Her');
        $storefront->assertSee('Explore Collection');

        // Confirm "Shop Men" was removed
        $storefront->assertDontSee('Shop Men');
    }

    public function test_admin_can_upload_and_configure_saaya_slide_with_custom_link()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('saaya-slide-2.jpg', 300, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'saaya_slide_2_image'    => $file,
            'saaya_slide_2_badge'    => 'Bespoke Trousseau',
            'saaya_slide_2_title'    => "Gold & Royal\nSilk",
            'saaya_slide_2_subtitle' => 'Handcrafted pure zari elegance.',
            'saaya_slide_2_btn_text' => 'Shop Royal Silk',
            'saaya_slide_2_btn_url'  => 'https://example.com/saaya-custom-link',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $savedPath = Setting::get('saaya_slide_2_image');
        $this->assertNotEmpty($savedPath);
        Storage::disk('public')->assertExists($savedPath);

        Template::query()->update(['is_active' => false]);
        Template::where('key', 'saaya')->update(['is_active' => true]);
        Template::clearActiveCache();

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);
        $storefront->assertSee(asset('images/' . $savedPath));
        $storefront->assertSee('Bespoke Trousseau');
        $storefront->assertSee('Shop Royal Silk');
        $storefront->assertSee('https://example.com/saaya-custom-link');
    }

    public function test_admin_can_upload_and_render_saaya_story_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('story-portrait.jpg', 250, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'saaya_story_image' => $file,
            'saaya_story_badge' => 'Heritage Craft',
            'saaya_story_title' => "Custom Story\nHeadline",
            'saaya_stat1_num'   => '25+',
            'saaya_stat1_label' => 'Ateliers',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $savedPath = Setting::get('saaya_story_image');
        $this->assertNotEmpty($savedPath);
        Storage::disk('public')->assertExists($savedPath);

        // Switch to Saaya theme and verify storefront renders it
        Template::query()->update(['is_active' => false]);
        Template::where('key', 'saaya')->update(['is_active' => true]);

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);
        $storefront->assertSee(asset('images/' . $savedPath));
        $storefront->assertSee('Heritage Craft');
        $storefront->assertSee('Custom Story');
        $storefront->assertSee('25+');
        $storefront->assertSee('Ateliers');
    }

    public function test_admin_can_remove_saaya_story_image()
    {
        Storage::fake('public');

        $file = UploadedFile::fake()->create('story-del.jpg', 200, 'image/jpeg');
        $path = $file->store('banners', 'public');
        Setting::set('saaya_story_image', $path);

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'remove_saaya_story_image' => '1',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $this->assertEmpty(Setting::get('saaya_story_image'));
        Storage::disk('public')->assertMissing($path);
    }

    public function test_all_themes_render_matching_sections()
    {
        $cat = \App\Models\Category::create(['name' => 'Intimates', 'slug' => 'intimates']);
        $product = \App\Models\Product::create([
            'category_id' => $cat->id,
            'name'        => 'Luxe Silk Set',
            'slug'        => 'luxe-silk-set',
            'base_price'  => 2499,
            'is_active'   => true,
            'is_featured' => true,
        ]);

        foreach (['saaya', 'vyra', 'buttercup', 'lumiere'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);

            $response = $this->get(route('home'));
            $response->assertStatus(200);

            // 1. Shop by category section
            $response->assertSee('Shop by Category');

            // 2. Curated Featured Picks section
            $response->assertSee('Curated Featured Picks');
            $response->assertSee('Luxe Silk Set');
            $response->assertSee('Explore Curated Edit');

            // 3. Best Sellers section
            $response->assertSee('Best Sellers');
            $response->assertSee('View Full Collection');

            // 4. Contact section
            $response->assertSee(route('contact.submit'));
        }
    }

    public function test_all_themes_render_matching_about_and_contact_pages()
    {
        foreach (['saaya', 'vyra', 'buttercup', 'lumiere'] as $themeKey) {
            Template::query()->update(['is_active' => false]);
            Template::where('key', $themeKey)->update(['is_active' => true]);

            // Test About Page
            $aboutResponse = $this->get(route('about'));
            $aboutResponse->assertStatus(200);
            $aboutResponse->assertSee('Home');

            // Test Contact Page
            $contactResponse = $this->get(route('contact'));
            $contactResponse->assertStatus(200);
            $contactResponse->assertSee(route('contact.submit'));
        }
    }

    public function test_admin_can_update_organic_theme_texts_and_view_on_storefront()
    {
        // 1. Check settings page shows organic tab sections
        $settingsPage = $this->actingAs($this->admin)->get(route('admin.settings.index'));
        $settingsPage->assertStatus(200);
        $settingsPage->assertSee('Organic Market Theme Content &amp; Visuals', false);
        $settingsPage->assertSee('organic_prop1_title');
        $settingsPage->assertSee('organic_ad1_title');
        $settingsPage->assertSee('organic_story_title');
        $settingsPage->assertSee('organic_news_title');

        // 2. Submit updated texts via admin settings update
        $postData = [
            // Image 1: Value Props
            'organic_prop1_title' => 'Direct from Himalayas',
            'organic_prop1_desc'  => 'Handpicked altitude crops brought fresh.',
            'organic_prop2_title' => '100% Bio Certified',
            'organic_prop3_title' => 'Same-Day Dispatch',

            // Image 2: Triple Banners
            'organic_ad1_title'   => 'Kashmiri Acacia Honey',
            'organic_ad2_title'   => 'Cold Pressed Coconut Oil',
            'organic_ad3_title'   => 'Pure A2 Vedic Ghee',

            // Image 3: Story
            'organic_story_title' => 'Living Soil & Sustainable Harvests',
            'organic_story_p1'    => 'Our organic community redefines pure farming across India.',

            // Image 4: Newsletter
            'organic_news_title'  => 'Get ₹500 Welcome Discount',
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), $postData);
        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        // 3. Switch to organic theme and verify on storefront
        Template::query()->update(['is_active' => false]);
        Template::where('key', 'organic')->update(['is_active' => true]);
        Template::clearActiveCache();

        $homeResponse = $this->get(route('home'));
        $homeResponse->assertStatus(200);

        // Verify Image 1 changes
        $homeResponse->assertSee('Direct from Himalayas');
        $homeResponse->assertSee('Handpicked altitude crops brought fresh.');
        $homeResponse->assertSee('100% Bio Certified');
        $homeResponse->assertSee('Same-Day Dispatch');

        // Verify Image 2 changes
        $homeResponse->assertSee('Kashmiri Acacia Honey');
        $homeResponse->assertSee('Cold Pressed Coconut Oil');
        $homeResponse->assertSee('Pure A2 Vedic Ghee');

        // Verify Image 3 changes
        $homeResponse->assertSee('Living Soil & Sustainable Harvests');
        $homeResponse->assertSee('Our organic community redefines pure farming across India.');

        // Verify Image 4 changes
        $homeResponse->assertSee('Get ₹500 Welcome Discount');
    }

    public function test_buttercup_theme_renders_5_full_background_image_slider_with_3s_autoplay()
    {
        Template::query()->update(['is_active' => false]);
        Template::where('key', 'buttercup')->update(['is_active' => true]);
        Template::clearActiveCache();

        $response = $this->get(route('home'));
        $response->assertStatus(200);

        // Verify carousel container & 3-second (3000ms) interval
        $response->assertSee('Buttercup Hero Slider');
        $response->assertSee('totalSlides: 5');
        $response->assertSee('intervalMs: 3000');
        $response->assertSee('startAutoplay()');

        // Verify slide kickers and titles
        $response->assertSee(asset('images/slider/slide-1.jpg'));
        $response->assertSee('New Season Edit');
        $response->assertSee('Comfort that');
        $response->assertSee('Feels Like Home');
        $response->assertSee('Cloud-Soft Intimates');
        $response->assertSee('Second-Skin Ease');
        $response->assertSee('Pure Indulgence');
        $response->assertSee('Soft as a Whisper');
        $response->assertSee('Wire-Free Freedom');
        $response->assertSee('Zero Pinching');
        $response->assertSee('Lounge &amp; Sleep', false);
        $response->assertSee('Dreamy Loungewear');

        // Verify navigation controls (5 dots and previous/next arrows)
        $response->assertSee('Go to slide 1');
        $response->assertSee('Go to slide 2');
        $response->assertSee('Go to slide 3');
        $response->assertSee('Go to slide 4');
        $response->assertSee('Go to slide 5');
        $response->assertSee('Previous slide');
        $response->assertSee('Next slide');
    }

    public function test_admin_can_customize_buttercup_slider_images_and_texts()
    {
        Storage::fake('public');

        $slide2Img = UploadedFile::fake()->create('custom-slide-2.jpg', 300, 'image/jpeg');

        $response = $this->actingAs($this->admin)->post(route('admin.settings.update'), [
            'buttercup_slide_2_image'    => $slide2Img,
            'buttercup_slide_2_badge'    => 'Midnight Edition',
            'buttercup_slide_2_title'    => "Silk Sensations\nAfter Dark",
            'buttercup_slide_2_subtitle' => 'Exquisite midnight silk sets for pure nighttime indulgence.',
            'buttercup_slide_2_btn1_text'=> 'Shop Midnight',
            'buttercup_slide_2_btn1_url' => '/collections/midnight',
        ]);

        $response->assertRedirect(route('admin.settings.index'));
        $response->assertSessionHas('success');

        $savedPath = Setting::get('buttercup_slide_2_image');
        $this->assertNotEmpty($savedPath);
        Storage::disk('public')->assertExists($savedPath);

        // Switch to Buttercup and verify on storefront
        Template::query()->update(['is_active' => false]);
        Template::where('key', 'buttercup')->update(['is_active' => true]);
        Template::clearActiveCache();

        $storefront = $this->get(route('home'));
        $storefront->assertStatus(200);
        $storefront->assertSee(asset('images/' . $savedPath));
        $storefront->assertSee('Midnight Edition');
        $storefront->assertSee('Silk Sensations');
        $storefront->assertSee('After Dark');
        $storefront->assertSee('Shop Midnight');
        $storefront->assertSee('/collections/midnight');
    }
}


