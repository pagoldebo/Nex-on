<?php

namespace App\Services\Auth;

class QrCodeService
{
    private static array $exp = [];
    private static array $log = [];
    private static bool $gfInit = false;

    // Alignment pattern positions for versions 1 to 10
    private static array $alignmentPatternPositions = [
        1 => [],
        2 => [6, 18],
        3 => [6, 22],
        4 => [6, 26],
        5 => [6, 30],
        6 => [6, 34],
        7 => [6, 22, 38],
        8 => [6, 24, 42],
        9 => [6, 26, 46],
        10 => [6, 28, 50],
    ];

    // Total codewords and EC codewords for Level M (medium 15% error correction)
    // Version => [totalCodewords, ecCodewords, numBlocksGroup1, dataCodewordsGroup1, numBlocksGroup2, dataCodewordsGroup2]
    private static array $versionInfoM = [
        1 => [26, 10, 1, 16, 0, 0],
        2 => [44, 16, 1, 28, 0, 0],
        3 => [70, 26, 1, 44, 0, 0],
        4 => [100, 36, 2, 32, 0, 0],
        5 => [134, 48, 2, 43, 0, 0],
        6 => [172, 64, 4, 27, 0, 0],
        7 => [196, 72, 4, 31, 0, 0],
        8 => [242, 88, 2, 38, 2, 39],
        9 => [292, 110, 3, 36, 2, 37],
        10 => [346, 130, 4, 43, 1, 44],
    ];

    // Format information bit patterns for Level M and mask 0 to 7 (BCH error corrected, masked with 0x5412)
    private static array $formatInfoM = [
        0 => 0x5045, // 101000001000101
        1 => 0x5554, // 101010101010100
        2 => 0x5A7B, // 101101001111011
        3 => 0x5F6A, // 101111101101010
        4 => 0x4292, // 100001010010010
        5 => 0x4783, // 100011110000011
        6 => 0x48A8, // 100100010101000
        7 => 0x4DB9, // 100110110111001
    ];

    private static function initGf(): void
    {
        if (self::$gfInit) {
            return;
        }

        $x = 1;
        for ($i = 0; $i < 255; $i++) {
            self::$exp[$i] = $x;
            self::$log[$x] = $i;
            $x <<= 1;
            if ($x & 0x100) {
                $x ^= 0x11d;
            }
        }
        for ($i = 255; $i < 512; $i++) {
            self::$exp[$i] = self::$exp[$i - 255];
        }
        self::$gfInit = true;
    }

    private static function gfMul(int $x, int $y): int
    {
        if ($x === 0 || $y === 0) {
            return 0;
        }
        return self::$exp[self::$log[$x] + self::$log[$y]];
    }

    /**
     * Generate Reed-Solomon generator polynomial for degree $degree.
     */
    private static function rsGeneratorPoly(int $degree): array
    {
        $poly = [1];
        for ($i = 0; $i < $degree; $i++) {
            $next = array_fill(0, count($poly) + 1, 0);
            $factor = self::$exp[$i];
            foreach ($poly as $k => $c) {
                $next[$k] ^= self::gfMul($c, $factor);
                $next[$k + 1] ^= $c;
            }
            $poly = $next;
        }
        return $poly;
    }

    /**
     * Calculate Reed-Solomon error correction codewords.
     */
    private static function rsComputeEc(array $data, int $ecCount): array
    {
        self::initGf();
        $generator = self::rsGeneratorPoly($ecCount);
        $remainder = array_fill(0, $ecCount, 0);

        foreach ($data as $byte) {
            $factor = $byte ^ $remainder[0];
            array_shift($remainder);
            $remainder[] = 0;
            if ($factor !== 0) {
                for ($j = 0; $j < $ecCount; $j++) {
                    $remainder[$j] ^= self::gfMul($generator[$j + 1], $factor);
                }
            }
        }

        return $remainder;
    }

    /**
     * Generate an SVG string representing the QR code.
     */
    public function generateSvg(string $text, int $size = 200): string
    {
        self::initGf();
        $len = strlen($text);

        // Determine minimum version (Level M)
        $version = null;
        foreach (self::$versionInfoM as $v => $info) {
            $capacity = ($info[2] * $info[3]) + ($info[4] * $info[5]);
            if ($len + 3 <= $capacity) {
                $version = $v;
                break;
            }
        }

        if ($version === null) {
            $version = 10;
        }

        $vInfo = self::$versionInfoM[$version];
        $totalDataCodewords = ($vInfo[2] * $vInfo[3]) + ($vInfo[4] * $vInfo[5]);

        // 1. Bitstream creation (Byte mode = 0100)
        $bitStream = '';
        $bitStream .= '0100'; // Mode
        $bitStream .= str_pad(decbin($len), 8, '0', STR_PAD_LEFT); // Length

        for ($i = 0; $i < $len; $i++) {
            $bitStream .= str_pad(decbin(ord($text[$i])), 8, '0', STR_PAD_LEFT);
        }

        // Terminator (up to 4 zero bits)
        $bitStream .= str_repeat('0', min(4, ($totalDataCodewords * 8) - strlen($bitStream)));

        // Pad to nearest byte
        if (strlen($bitStream) % 8 !== 0) {
            $bitStream .= str_repeat('0', 8 - (strlen($bitStream) % 8));
        }

        // Convert to data codewords
        $dataCodewords = [];
        $bytes = str_split($bitStream, 8);
        foreach ($bytes as $b) {
            $dataCodewords[] = bindec($b);
        }

        // Pad with 0xEC and 0x11
        $pad = [0xEC, 0x11];
        $padIdx = 0;
        while (count($dataCodewords) < $totalDataCodewords) {
            $dataCodewords[] = $pad[$padIdx];
            $padIdx = ($padIdx + 1) % 2;
        }

        // 2. Block division and Reed-Solomon computation
        $blocks = [];
        $ecBlocks = [];
        $offset = 0;
        $ecCountPerBlock = (int) ($vInfo[1] / ($vInfo[2] + $vInfo[4]));

        // Group 1
        for ($b = 0; $b < $vInfo[2]; $b++) {
            $blockData = array_slice($dataCodewords, $offset, $vInfo[3]);
            $offset += $vInfo[3];
            $blocks[] = $blockData;
            $ecBlocks[] = self::rsComputeEc($blockData, $ecCountPerBlock);
        }

        // Group 2
        for ($b = 0; $b < $vInfo[4]; $b++) {
            $blockData = array_slice($dataCodewords, $offset, $vInfo[5]);
            $offset += $vInfo[5];
            $blocks[] = $blockData;
            $ecBlocks[] = self::rsComputeEc($blockData, $ecCountPerBlock);
        }

        // 3. Interleaving data and error correction codewords
        $finalCodewords = [];
        $maxDataLen = max($vInfo[3], $vInfo[5]);
        for ($i = 0; $i < $maxDataLen; $i++) {
            foreach ($blocks as $block) {
                if ($i < count($block)) {
                    $finalCodewords[] = $block[$i];
                }
            }
        }
        for ($i = 0; $i < $ecCountPerBlock; $i++) {
            foreach ($ecBlocks as $ecBlock) {
                $finalCodewords[] = $ecBlock[$i];
            }
        }

        // 4. Matrix construction
        $matrixDim = $version * 4 + 17;
        $matrix = array_fill(0, $matrixDim, array_fill(0, $matrixDim, null));
        $reserved = array_fill(0, $matrixDim, array_fill(0, $matrixDim, false));

        // Place Finder Patterns
        $placeFinder = function (int $r, int $c) use (&$matrix, &$reserved, $matrixDim) {
            for ($i = -1; $i <= 7; $i++) {
                for ($j = -1; $j <= 7; $j++) {
                    $currR = $r + $i;
                    $currC = $c + $j;
                    if ($currR >= 0 && $currR < $matrixDim && $currC >= 0 && $currC < $matrixDim) {
                        $reserved[$currR][$currC] = true;
                        if ($i >= 0 && $i <= 6 && $j >= 0 && $j <= 6) {
                            if ($i === 0 || $i === 6 || $j === 0 || $j === 6 || ($i >= 2 && $i <= 4 && $j >= 2 && $j <= 4)) {
                                $matrix[$currR][$currC] = 1;
                            } else {
                                $matrix[$currR][$currC] = 0;
                            }
                        } else {
                            $matrix[$currR][$currC] = 0; // Separator
                        }
                    }
                }
            }
        };

        $placeFinder(0, 0);
        $placeFinder(0, $matrixDim - 7);
        $placeFinder($matrixDim - 7, 0);

        // Place Alignment Patterns
        $alignPos = self::$alignmentPatternPositions[$version];
        foreach ($alignPos as $ar) {
            foreach ($alignPos as $ac) {
                if ($reserved[$ar][$ac]) {
                    continue;
                }
                for ($i = -2; $i <= 2; $i++) {
                    for ($j = -2; $j <= 2; $j++) {
                        $currR = $ar + $i;
                        $currC = $ac + $j;
                        $reserved[$currR][$currC] = true;
                        if (abs($i) === 2 || abs($j) === 2 || ($i === 0 && $j === 0)) {
                            $matrix[$currR][$currC] = 1;
                        } else {
                            $matrix[$currR][$currC] = 0;
                        }
                    }
                }
            }
        }

        // Place Timing Patterns
        for ($i = 8; $i < $matrixDim - 8; $i++) {
            if (!$reserved[6][$i]) {
                $reserved[6][$i] = true;
                $matrix[6][$i] = ($i % 2 === 0) ? 1 : 0;
            }
            if (!$reserved[$i][6]) {
                $reserved[$i][6] = true;
                $matrix[$i][6] = ($i % 2 === 0) ? 1 : 0;
            }
        }

        // Dark Module
        $darkR = 4 * $version + 9;
        $matrix[$darkR][8] = 1;
        $reserved[$darkR][8] = true;

        // Reserve Format Information areas
        for ($i = 0; $i <= 8; $i++) {
            $reserved[8][$i] = true;
            $reserved[$i][8] = true;
        }
        for ($i = $matrixDim - 8; $i < $matrixDim; $i++) {
            $reserved[8][$i] = true;
            $reserved[$i][8] = true;
        }

        // 5. Data placement with mask pattern 0: (row + col) % 2 === 0
        $dataBits = '';
        foreach ($finalCodewords as $cw) {
            $dataBits .= str_pad(decbin($cw), 8, '0', STR_PAD_LEFT);
        }

        $bitIdx = 0;
        $bitLen = strlen($dataBits);
        $col = $matrixDim - 1;
        $goingUp = true;

        while ($col > 0) {
            if ($col === 6) {
                $col--; // Skip vertical timing column
            }

            $rows = $goingUp ? range($matrixDim - 1, 0, -1) : range(0, $matrixDim - 1);
            foreach ($rows as $row) {
                foreach ([$col, $col - 1] as $c) {
                    if (!$reserved[$row][$c]) {
                        $bit = ($bitIdx < $bitLen) ? (int) $dataBits[$bitIdx++] : 0;
                        // Mask 0: (row + col) % 2 === 0
                        $maskBit = (($row + $c) % 2 === 0) ? 1 : 0;
                        $matrix[$row][$c] = $bit ^ $maskBit;
                    }
                }
            }

            $col -= 2;
            $goingUp = !$goingUp;
        }

        // 6. Write Format Information (Level M, Mask 0: 0x5045)
        $formatBits = str_pad(decbin(self::$formatInfoM[0]), 15, '0', STR_PAD_LEFT);

        // Top-left finder format info
        $formatCoordsTL = [
            [8, 0], [8, 1], [8, 2], [8, 3], [8, 4], [8, 5], [8, 7], [8, 8],
            [7, 8], [5, 8], [4, 8], [3, 8], [2, 8], [1, 8], [0, 8]
        ];
        for ($i = 0; $i < 15; $i++) {
            [$r, $c] = $formatCoordsTL[$i];
            $matrix[$r][$c] = (int) $formatBits[$i];
        }

        // Top-right and bottom-left format info
        for ($i = 0; $i < 7; $i++) {
            $matrix[$matrixDim - 1 - $i][8] = (int) $formatBits[$i];
        }
        for ($i = 0; $i < 8; $i++) {
            $matrix[8][$matrixDim - 8 + $i] = (int) $formatBits[7 + $i];
        }

        // 7. Render SVG
        $quietZone = 4;
        $viewBoxDim = $matrixDim + ($quietZone * 2);
        $rects = [];

        for ($r = 0; $r < $matrixDim; $r++) {
            for ($c = 0; $c < $matrixDim; $c++) {
                if ($matrix[$r][$c] === 1) {
                    $rects[] = sprintf(
                        '<rect x="%d" y="%d" width="1" height="1" fill="#000000" />',
                        $c + $quietZone,
                        $r + $quietZone
                    );
                }
            }
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d">' .
            '<rect width="100%%" height="100%%" fill="#ffffff"/>' .
            '%s' .
            '</svg>',
            $viewBoxDim,
            $viewBoxDim,
            $size,
            $size,
            implode('', $rects)
        );
    }

    /**
     * Generate an RFC 2397 Data URI for embedding directly in <img src="...">.
     */
    public function generateDataUri(string $text, int $size = 200): string
    {
        $svg = $this->generateSvg($text, $size);
        return 'data:image/svg+xml;base64,' . base64_encode($svg);
    }
}

