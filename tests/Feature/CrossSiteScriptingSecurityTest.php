<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use App\Models\User;
use App\Services\HtmlSanitizerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CrossSiteScriptingSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_category_flat_tree_escapes_xss_in_category_name(): void
    {
        $xssName = '<script>alert("xss")</script>Shoes';
        $cat = Category::create([
            'name'       => $xssName,
            'slug'       => 'xss-shoes',
            'sort_order' => 1,
        ]);

        $tree = Category::getFlatTree();
        $target = $tree->firstWhere('id', $cat->id);

        $this->assertNotNull($target);
        // The indent_name must have escaped HTML entities, NOT raw unescaped script tag
        $this->assertStringNotContainsString('<script>alert("xss")</script>', $target->indent_name);
        $this->assertStringContainsString('&lt;script&gt;', $target->indent_name);
    }

    public function test_product_description_mutator_sanitizes_xss_payloads(): void
    {
        $category = Category::create(['name' => 'Dresses', 'slug' => 'dresses']);

        $product = Product::create([
            'category_id'      => $category->id,
            'name'             => 'Silk Dress',
            'slug'             => 'silk-dress',
            'description'      => '<p>Luxury dress <script>alert("xss")</script></p><img src="x" onerror="alert(1)">',
            'long_description' => '<a href="javascript:alert(1)">Click</a><div>Safe content</div>',
        ]);

        $this->assertStringNotContainsString('<script', $product->description);
        $this->assertStringNotContainsString('alert("xss")', $product->description);
        $this->assertStringNotContainsString('onerror', $product->description);
        $this->assertStringContainsString('<p>Luxury dress </p>', $product->description);

        $this->assertStringNotContainsString('javascript:', $product->long_description);
        $this->assertStringContainsString('Safe content', $product->long_description);
    }

    public function test_security_headers_are_present_on_web_requests(): void
    {
        $response = $this->get('/');

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');

        $csp = $response->headers->get('Content-Security-Policy');
        preg_match('/script-src\s+([^;]+)/', $csp, $matches);
        $scriptSrc = $matches[1] ?? '';

        $this->assertStringNotContainsString("'unsafe-inline'", $scriptSrc);
        $this->assertStringNotContainsString("'unsafe-eval'", $scriptSrc);
        $this->assertStringContainsString("'strict-dynamic'", $scriptSrc);
        $this->assertStringContainsString("'nonce-", $scriptSrc);
    }

    public function test_html_sanitizer_cleans_stored_settings(): void
    {
        $clean = HtmlSanitizerService::clean('<p>Return policy <script>steal()</script></p>');
        $this->assertEquals('<p>Return policy </p>', $clean);
    }
}
