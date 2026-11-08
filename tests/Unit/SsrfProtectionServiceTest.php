<?php

namespace Tests\Unit;

use App\Services\SsrfProtectionService;
use PHPUnit\Framework\TestCase;

class SsrfProtectionServiceTest extends TestCase
{
    /**
     * Test valid public URLs are recognized as safe.
     */
    public function test_safe_public_urls(): void
    {
        $safeUrls = [
            'https://discord.com/api/webhooks/123456789/abcdef',
            'https://hooks.slack.com/services/T0000/B0000/XXXX',
            'https://api.github.com/repos/test/hooks',
            'https://webhook.site/#!/view/123456',
            'https://zapier.com/hooks/catch/12345/abcde/',
        ];

        foreach ($safeUrls as $url) {
            $this->assertTrue(SsrfProtectionService::isSafeUrl($url), "Expected {$url} to be safe.");
        }
    }

    /**
     * Test localhost and loopback IPv4 addresses are blocked.
     */
    public function test_blocks_localhost_and_loopback(): void
    {
        $loopbacks = [
            'http://127.0.0.1/',
            'http://127.0.0.1:8080/admin',
            'http://127.0.0.2/',
            'http://127.255.255.254/',
            'http://localhost/',
            'http://localhost:3000/',
            'http://localhost.localdomain/',
            'http://ip6-localhost/',
        ];

        foreach ($loopbacks as $url) {
            $this->assertFalse(SsrfProtectionService::isSafeUrl($url), "Expected loopback {$url} to be blocked.");
        }
    }

    /**
     * Test AWS, GCP, and Azure cloud metadata endpoints are strictly blocked.
     */
    public function test_blocks_cloud_metadata_endpoints(): void
    {
        $metadataUrls = [
            'http://169.254.169.254/latest/meta-data/',
            'http://169.254.169.254/computeMetadata/v1/',
            'http://169.254.1.1/',
            'http://metadata.google.internal/computeMetadata/v1/',
            'http://instance-data/latest/meta-data/',
        ];

        foreach ($metadataUrls as $url) {
            $this->assertFalse(SsrfProtectionService::isSafeUrl($url), "Expected cloud metadata {$url} to be blocked.");
        }
    }

    /**
     * Test RFC 1918 private subnets are blocked.
     */
    public function test_blocks_rfc1918_private_ranges(): void
    {
        $privateUrls = [
            'http://10.0.0.1/api',
            'http://10.255.255.254/',
            'http://172.16.0.1/',
            'http://172.24.10.5:80/',
            'http://172.31.255.255/',
            'http://192.168.0.1/router-login',
            'http://192.168.1.254:443/',
        ];

        foreach ($privateUrls as $url) {
            $this->assertFalse(SsrfProtectionService::isSafeUrl($url), "Expected private URL {$url} to be blocked.");
        }
    }

    /**
     * Test alternative IP encodings (decimal, hex, 0.0.0.0) are blocked.
     */
    public function test_blocks_alternative_ip_encodings(): void
    {
        $encodedUrls = [
            'http://0.0.0.0/',              // Current host
            'http://0/',                    // Shorthand 0
            'http://2130706433/',           // Decimal 127.0.0.1
            'http://2852039166/',           // Decimal 169.254.169.254
            'http://0x7f000001/',           // Hex 127.0.0.1
        ];

        foreach ($encodedUrls as $url) {
            $this->assertFalse(SsrfProtectionService::isSafeUrl($url), "Expected encoded URL {$url} to be blocked.");
        }
    }

    /**
     * Test IPv6 loopback, link-local, and ULA addresses are blocked.
     */
    public function test_blocks_ipv6_private_and_loopback(): void
    {
        $ipv6Urls = [
            'http://[::1]/',
            'http://[::]/',
            'http://[fe80::1]/',
            'http://[fc00::1]/',
            'http://[fd12:3456:789a:1::1]/',
            'http://[::ffff:127.0.0.1]/',
            'http://[::ffff:169.254.169.254]/',
        ];

        foreach ($ipv6Urls as $url) {
            $this->assertFalse(SsrfProtectionService::isSafeUrl($url), "Expected IPv6 URL {$url} to be blocked.");
        }
    }

    /**
     * Test non-HTTP protocols are rejected.
     */
    public function test_blocks_dangerous_schemes(): void
    {
        $badSchemes = [
            'gopher://127.0.0.1:6379/_flushall',
            'file:///etc/passwd',
            'file://C:/Windows/System32/drivers/etc/hosts',
            'dict://127.0.0.1:11211/stat',
            'ftp://example.com/test',
            'php://filter/resource=index.php',
            'data:text/html;base64,PHNjcmlwdD5hbGVydCgxKTwvc2NyaXB0Pg==',
        ];

        foreach ($badSchemes as $url) {
            $this->assertFalse(SsrfProtectionService::isSafeUrl($url), "Expected scheme for {$url} to be blocked.");
        }
    }

    /**
     * Test non-standard destination ports are rejected.
     */
    public function test_blocks_prohibited_ports(): void
    {
        $badPorts = [
            'http://example.com:22/',    // SSH
            'http://example.com:25/',    // SMTP
            'http://example.com:3306/',  // MySQL
            'http://example.com:5432/',  // Postgres
            'http://example.com:6379/',  // Redis
            'http://example.com:11211/', // Memcached
            'http://example.com:2375/',  // Docker
        ];

        foreach ($badPorts as $url) {
            $this->assertFalse(SsrfProtectionService::isSafeUrl($url), "Expected port in {$url} to be blocked.");
        }
    }

    /**
     * Test embedded credentials and control characters are rejected.
     */
    public function test_blocks_credentials_and_control_characters(): void
    {
        $this->assertFalse(SsrfProtectionService::isSafeUrl('http://admin:secret@example.com/webhook'));
        $this->assertFalse(SsrfProtectionService::isSafeUrl("http://example.com\r\n/webhook"));
        $this->assertFalse(SsrfProtectionService::isSafeUrl("http://example.com\t/webhook"));
    }

    /**
     * Test filterSafeUrls strips out all unsafe URLs from an input array.
     */
    public function test_filter_safe_urls(): void
    {
        $input = [
            'http://127.0.0.1/admin',
            'https://discord.com/api/webhooks/123/xyz',
            'http://169.254.169.254/latest/meta-data/',
            'https://hooks.slack.com/services/safe',
            'file:///etc/passwd',
        ];

        $filtered = SsrfProtectionService::filterSafeUrls($input);

        $this->assertEquals([
            'https://discord.com/api/webhooks/123/xyz',
            'https://hooks.slack.com/services/safe',
        ], $filtered);
    }
}

