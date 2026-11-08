<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizerService;
use Tests\TestCase;

class HtmlSanitizerServiceTest extends TestCase
{
    public function test_strips_script_tags(): void
    {
        $input = '<p>Hello <script>alert("XSS")</script>World</p>';
        $clean = HtmlSanitizerService::clean($input);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('alert("XSS")', $clean);
        $this->assertStringContainsString('Hello', $clean);
        $this->assertStringContainsString('World', $clean);
    }

    public function test_strips_inline_event_handlers(): void
    {
        $input = '<img src="/test.jpg" onerror="alert(1)" onload="fetch(\'/admin\')">';
        $clean = HtmlSanitizerService::clean($input);

        $this->assertStringNotContainsString('onerror', $clean);
        $this->assertStringNotContainsString('onload', $clean);
        $this->assertStringNotContainsString('alert', $clean);
        $this->assertStringContainsString('src="/test.jpg"', $clean);
    }

    public function test_strips_javascript_href_protocols(): void
    {
        $input = '<a href="javascript:alert(1)">Click me</a>';
        $clean = HtmlSanitizerService::clean($input);

        $this->assertStringNotContainsString('javascript:', $clean);
        $this->assertStringNotContainsString('href', $clean);
        $this->assertStringContainsString('Click me', $clean);
    }

    public function test_strips_data_uri_href(): void
    {
        $input = '<a href="data:text/html,<script>alert(1)</script>">Click me</a>';
        $clean = HtmlSanitizerService::clean($input);

        $this->assertStringNotContainsString('data:text/html', $clean);
        $this->assertStringNotContainsString('<script', $clean);
    }

    public function test_strips_iframes_and_objects(): void
    {
        $input = '<div><iframe src="https://evil.com"></iframe><object data="test.swf"></object>Safe text</div>';
        $clean = HtmlSanitizerService::clean($input);

        $this->assertStringNotContainsString('<iframe', $clean);
        $this->assertStringNotContainsString('<object', $clean);
        $this->assertStringContainsString('Safe text', $clean);
    }

    public function test_preserves_safe_html_formatting(): void
    {
        $input = '<p><strong>Bold</strong> and <em>Italic</em></p><ul><li>One</li><li>Two</li></ul><table class="table"><thead><tr><th>Col</th></tr></thead><tbody><tr><td>Val</td></tr></tbody></table>';
        $clean = HtmlSanitizerService::clean($input);

        $this->assertStringContainsString('<strong>Bold</strong>', $clean);
        $this->assertStringContainsString('<em>Italic</em>', $clean);
        $this->assertStringContainsString('<li>One</li>', $clean);
        $this->assertStringContainsString('<table class="table">', $clean);
    }

    public function test_enforces_noopener_on_blank_target(): void
    {
        $input = '<a href="https://example.com" target="_blank">External</a>';
        $clean = HtmlSanitizerService::clean($input);

        $this->assertStringContainsString('rel="noopener noreferrer"', $clean);
    }

    public function test_sanitizes_svg_content(): void
    {
        $svg = '<svg xmlns="http://www.w3.org/2000/svg" onload="alert(1)"><script>alert("svg")</script><circle cx="50" cy="50" r="40" fill="red" /></svg>';
        $clean = HtmlSanitizerService::sanitizeSvg($svg);

        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('onload', $clean);
        $this->assertStringNotContainsString('alert', $clean);
        $this->assertStringContainsString('<circle', $clean);
    }
}

