<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SsrfProtectionService
{
    /**
     * Permitted URI schemes.
     */
    protected static array $allowedSchemes = ['https', 'http'];

    /**
     * Permitted outbound TCP destination ports.
     */
    protected static array $allowedPorts = [80, 443, 8443];

    /**
     * Explicitly blocked hostnames (lowercase).
     */
    protected static array $blockedHosts = [
        'localhost',
        'localhost.localdomain',
        'ip6-localhost',
        'ip6-loopback',
        'instance-data',
        'metadata.google.internal',
        'metadata.internal',
        'metadata',
    ];

    /**
     * Explicitly forbidden IPv4 CIDR blocks (Private, Loopback, Link-Local/Metadata, CGNAT, Broadcast).
     */
    protected static array $forbiddenIpv4Cidrs = [
        '0.0.0.0/8',          // Current network ("this" network)
        '10.0.0.0/8',         // Private RFC 1918
        '100.64.0.0/10',      // Shared Address Space / Carrier-Grade NAT (RFC 6598)
        '127.0.0.0/8',        // Loopback (127.0.0.1 - 127.255.255.255)
        '169.254.0.0/16',     // Link-Local & Cloud Instance Metadata (169.254.169.254)
        '172.16.0.0/12',      // Private RFC 1918 (172.16.0.0 - 172.31.255.255)
        '192.0.0.0/24',       // IETF Protocol Assignments
        '192.0.2.0/24',       // TEST-NET-1 (RFC 5737)
        '192.168.0.0/16',     // Private RFC 1918
        '198.18.0.0/15',      // Network benchmark tests (RFC 2544)
        '198.51.100.0/24',    // TEST-NET-2 (RFC 5737)
        '203.0.113.0/24',     // TEST-NET-3 (RFC 5737)
        '224.0.0.0/4',        // Multicast
        '240.0.0.0/4',        // Reserved / Future use
        '255.255.255.255/32', // Broadcast
    ];

    /**
     * Validate a target URL against SSRF vulnerabilities.
     * Throws \InvalidArgumentException if the URL violates security policies.
     *
     * @param string $url
     * @param bool $requireHttps
     * @throws \InvalidArgumentException
     */
    public static function validateUrl(string $url, bool $requireHttps = false): void
    {
        $trimmed = trim($url);
        if (empty($trimmed)) {
            throw new \InvalidArgumentException('URL cannot be empty.');
        }

        // Prevent whitespace / control character bypasses
        if (preg_match('/[\s\r\n\t]/', $trimmed)) {
            throw new \InvalidArgumentException("URL contains invalid whitespace or control characters: '{$url}'.");
        }

        $parts = parse_url($trimmed);
        if ($parts === false || !isset($parts['host'])) {
            throw new \InvalidArgumentException("Malformed URL structure: '{$url}'.");
        }

        // 1. Validate Scheme
        $scheme = strtolower($parts['scheme'] ?? '');
        if ($requireHttps) {
            if ($scheme !== 'https') {
                throw new \InvalidArgumentException("URL must use HTTPS protocol: '{$url}'.");
            }
        } else {
            if (!in_array($scheme, self::$allowedSchemes, true)) {
                throw new \InvalidArgumentException("Prohibited URL scheme '{$scheme}'. Only HTTP and HTTPS are permitted.");
            }
        }

        // 2. Validate Port
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        if (!in_array($port, self::$allowedPorts, true)) {
            throw new \InvalidArgumentException("Prohibited port '{$port}'. Only standard web ports (80, 443, 8443) are allowed.");
        }

        // 3. Normalize Hostname
        $host = strtolower(trim($parts['host'], '[]'));

        // Disallow credentials in authority (user:pass@host)
        if (isset($parts['user']) || isset($parts['pass'])) {
            throw new \InvalidArgumentException("Embedded user credentials in URL are prohibited: '{$url}'.");
        }

        // Blacklisted Hostnames
        if (in_array($host, self::$blockedHosts, true)) {
            throw new \InvalidArgumentException("Prohibited hostname '{$host}'. Access to internal and loopback hosts is forbidden.");
        }

        // Detect Decimal IP Notation (e.g., http://2130706433/ or http://0)
        if (ctype_digit($host)) {
            $decimalVal = (int) $host;
            $ip = long2ip($decimalVal);
            if (self::isPrivateOrReservedIp($ip)) {
                throw new \InvalidArgumentException("Decimal host '{$host}' ({$ip}) resolves to a private or reserved IP address.");
            }
        }

        // Detect Hexadecimal IP Notation (e.g., http://0x7f000001/)
        if (str_starts_with($host, '0x') && ctype_xdigit(substr($host, 2))) {
            $ip = long2ip(hexdec($host));
            if (self::isPrivateOrReservedIp($ip)) {
                throw new \InvalidArgumentException("Hexadecimal host '{$host}' ({$ip}) resolves to a private or reserved IP address.");
            }
        }

        // 4. Validate Direct IP Address
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (self::isPrivateOrReservedIp($host)) {
                throw new \InvalidArgumentException("Destination IP address '{$host}' is within a private, loopback, or cloud metadata range.");
            }
            return;
        }

        // 5. DNS Resolution and Resolution Verification
        $resolvedIps = self::resolveHostIps($host);
        if (empty($resolvedIps)) {
            throw new \InvalidArgumentException("Hostname '{$host}' could not be resolved via DNS.");
        }

        foreach ($resolvedIps as $ip) {
            if (self::isPrivateOrReservedIp($ip)) {
                throw new \InvalidArgumentException("Hostname '{$host}' resolved to prohibited IP address '{$ip}'.");
            }
        }
    }

    /**
     * Check if a URL is safe without throwing exceptions.
     */
    public static function isSafeUrl(string $url, bool $requireHttps = false): bool
    {
        try {
            self::validateUrl($url, $requireHttps);
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Filter an array of URLs, keeping only safe ones.
     */
    public static function filterSafeUrls(array $urls, bool $requireHttps = false): array
    {
        $safe = [];
        foreach ($urls as $url) {
            $url = trim($url);
            if (!empty($url) && self::isSafeUrl($url, $requireHttps)) {
                $safe[] = $url;
            }
        }

        return array_values(array_unique($safe));
    }

    /**
     * Determine if an IP address (IPv4 or IPv6) is private, loopback, link-local, or reserved.
     */
    public static function isPrivateOrReservedIp(string $ip): bool
    {
        // Handle IPv4-mapped IPv6 address (e.g. ::ffff:127.0.0.1)
        if (str_starts_with($ip, '::ffff:')) {
            $ipv4Part = substr($ip, 7);
            if (filter_var($ipv4Part, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
                $ip = $ipv4Part;
            }
        }

        // 1. IPv4 Checks
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            // PHP native filter flag check
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return true;
            }

            $long = ip2long($ip);
            if ($long === false) {
                return true;
            }

            // Check against comprehensive CIDR table
            foreach (self::$forbiddenIpv4Cidrs as $cidr) {
                if (self::ipMatchesCidr($long, $cidr)) {
                    return true;
                }
            }

            return false;
        }

        // 2. IPv6 Checks
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // PHP native filter flag check for IPv6
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                return true;
            }

            $packed = @inet_pton($ip);
            if ($packed === false) {
                return true;
            }

            // Loopback ::1 or Unspecified ::
            if ($ip === '::1' || $ip === '::') {
                return true;
            }

            $firstByte = ord($packed[0]);

            // Unique Local Address (ULA) fc00::/7 (covers fc00... through fdff...)
            if (($firstByte & 0xfe) === 0xfc) {
                return true;
            }

            // Link-Local fe80::/10
            if ($firstByte === 0xfe && (ord($packed[1]) & 0xc0) === 0x80) {
                return true;
            }

            // Multicast ff00::/8
            if ($firstByte === 0xff) {
                return true;
            }

            return false;
        }

        // Malformed IP is treated as unsafe
        return true;
    }

    /**
     * Resolve all IPv4 and IPv6 addresses for a hostname.
     */
    public static function resolveHostIps(string $host): array
    {
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            return [$host];
        }

        $ips = [];

        // IPv4 DNS resolution
        $aRecords = @dns_get_record($host, DNS_A);
        if (is_array($aRecords)) {
            foreach ($aRecords as $rec) {
                if (!empty($rec['ip'])) {
                    $ips[] = $rec['ip'];
                }
            }
        }

        // Fallback gethostbynamel
        if (empty($ips)) {
            $hostIps = @gethostbynamel($host);
            if (is_array($hostIps)) {
                $ips = array_merge($ips, $hostIps);
            }
        }

        // IPv6 DNS resolution
        $aaaaRecords = @dns_get_record($host, DNS_AAAA);
        if (is_array($aaaaRecords)) {
            foreach ($aaaaRecords as $rec) {
                if (!empty($rec['ipv6'])) {
                    $ips[] = $rec['ipv6'];
                }
            }
        }

        return array_values(array_unique($ips));
    }

    /**
     * Create an SSRF-hardened HTTP client instance.
     * Disables automatic redirect following and enforces strict timeout limits.
     */
    public static function createSafeClient(int $timeout = 8): PendingRequest
    {
        return Http::timeout($timeout)
            ->withoutRedirecting() // Prevent SSRF redirect bypass to loopback/metadata
            ->withHeaders([
                'User-Agent' => 'Nexon-Secure-Webhook/1.0',
            ]);
    }

    /**
     * Check if a 32-bit integer IPv4 matches a CIDR range.
     */
    protected static function ipMatchesCidr(int $longIp, string $cidr): bool
    {
        [$subnet, $bits] = explode('/', $cidr, 2);
        $subnetLong = ip2long($subnet);
        $mask = -1 << (32 - (int) $bits);

        return ($longIp & $mask) === ($subnetLong & $mask);
    }
}

