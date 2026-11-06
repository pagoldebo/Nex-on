<?php

namespace App\Services\Auth;

class TotpService
{
    /**
     * Base32 character map per RFC 4648.
     */
    private const BASE32_CHARS = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';

    /**
     * Generate a cryptographically secure random Base32 secret key.
     */
    public function generateSecretKey(int $length = 32): string
    {
        $secret = '';
        $max = strlen(self::BASE32_CHARS) - 1;

        for ($i = 0; $i < $length; $i++) {
            $secret .= self::BASE32_CHARS[random_int(0, $max)];
        }

        return $secret;
    }

    /**
     * Verify a 6-digit TOTP code against a secret key with a given drift window.
     */
    public function verify(string $secret, string $code, int $window = 1): bool
    {
        $code = trim($code);
        if (strlen($code) !== 6 || ! ctype_digit($code)) {
            return false;
        }

        $currentTimeSlice = (int) floor(time() / 30);

        for ($i = -$window; $i <= $window; $i++) {
            $calculatedCode = $this->calculateCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Calculate a 6-digit TOTP code for a specific time slice (RFC 6238).
     */
    public function calculateCode(string $secret, int $timeSlice): string
    {
        $secretKey = $this->base32Decode($secret);

        // Pack time slice into 8-byte big-endian binary string
        $time = pack('N*', 0) . pack('N*', $timeSlice);

        // HMAC-SHA1
        $hash = hash_hmac('sha1', $time, $secretKey, true);

        // Dynamic truncation
        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);
        $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

        return str_pad((string) ($value % 1000000), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Generate an otpauth:// URI for QR code generation.
     */
    public function getQrCodeUrl(string $company, string $email, string $secret): string
    {
        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s&algorithm=SHA1&digits=6&period=30',
            rawurlencode($company),
            rawurlencode($email),
            $secret,
            rawurlencode($company)
        );
    }

    /**
     * Generate an image QR code URL (using reliable SVG/charts API).
     */
    public function getQrCodeSvgUrl(string $company, string $email, string $secret): string
    {
        $otpUrl = $this->getQrCodeUrl($company, $email, $secret);

        return 'https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=' . urlencode($otpUrl);
    }

    /**
     * Generate an array of 8 random recovery codes.
     *
     * @return array<int, string>
     */
    public function generateRecoveryCodes(int $amount = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $amount; $i++) {
            $codes[] = sprintf(
                '%s-%s',
                bin2hex(random_bytes(5)),
                bin2hex(random_bytes(5))
            );
        }

        return $codes;
    }

    /**
     * Decode a Base32 string into raw binary.
     */
    private function base32Decode(string $b32): string
    {
        $b32 = strtoupper(preg_replace('/[^A-Z2-7]/', '', $b32));
        $buffer = 0;
        $bitsLeft = 0;
        $binary = '';

        $length = strlen($b32);
        for ($i = 0; $i < $length; $i++) {
            $val = strpos(self::BASE32_CHARS, $b32[$i]);
            if ($val === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $val;
            $bitsLeft += 5;

            if ($bitsLeft >= 8) {
                $bitsLeft -= 8;
                $binary .= chr(($buffer >> $bitsLeft) & 0xFF);
            }
        }

        return $binary;
    }
}
