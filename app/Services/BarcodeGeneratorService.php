<?php

namespace App\Services;

class BarcodeGeneratorService
{
    /**
     * Code 128 pattern definitions (0 to 106).
     * Alternating bar and space module widths.
     */
    private static array $patterns = [
        0 => '212222', 1 => '222122', 2 => '222221', 3 => '121223', 4 => '121322',
        5 => '131222', 6 => '122213', 7 => '122312', 8 => '132212', 9 => '221213',
        10 => '221312', 11 => '231212', 12 => '112232', 13 => '122132', 14 => '122231',
        15 => '113222', 16 => '123122', 17 => '123221', 18 => '223211', 19 => '221132',
        20 => '221231', 21 => '213212', 22 => '223112', 23 => '312131', 24 => '311222',
        25 => '321122', 26 => '321221', 27 => '312212', 28 => '322112', 29 => '322211',
        30 => '212123', 31 => '212321', 32 => '232121', 33 => '111323', 34 => '131123',
        35 => '131321', 36 => '112313', 37 => '132113', 38 => '132311', 39 => '211313',
        40 => '231113', 41 => '231311', 42 => '112133', 43 => '112331', 44 => '132131',
        45 => '113123', 46 => '113321', 47 => '133121', 48 => '313121', 49 => '211331',
        50 => '231131', 51 => '213113', 52 => '213311', 53 => '213131', 54 => '311123',
        55 => '311321', 56 => '331121', 57 => '312113', 58 => '312311', 59 => '332111',
        60 => '314111', 61 => '221411', 62 => '431111', 63 => '111224', 64 => '111422',
        65 => '121124', 66 => '121421', 67 => '141122', 68 => '141221', 69 => '112214',
        70 => '112412', 71 => '122114', 72 => '122411', 73 => '142112', 74 => '142211',
        75 => '241211', 76 => '221114', 77 => '413111', 78 => '241112', 79 => '134111',
        80 => '111242', 81 => '121142', 82 => '121241', 83 => '114212', 84 => '124112',
        85 => '124211', 86 => '411212', 87 => '421112', 88 => '421211', 89 => '212141',
        90 => '214121', 91 => '412121', 92 => '111143', 93 => '111341', 94 => '131141',
        95 => '114113', 96 => '114311', 97 => '411113', 98 => '411311', 99 => '113141',
        100 => '114131', 101 => '311141', 102 => '411131',
        103 => '211412', // Start A
        104 => '211214', // Start B
        105 => '211232', // Start C
        106 => '2331112' // Stop
    ];

    /**
     * Generate an inline SVG for Code-128 barcode.
     */
    public function generateSvg(string $text, int $barWidth = 2, int $height = 50, bool $showText = false): string
    {
        $text = trim($text);
        if ($text === '') {
            $text = 'EMPTY';
        }

        // Code 128 Set B encoding (ASCII 32 to 126)
        $startCode = 104; // Start B
        $values = [$startCode];
        $checksum = $startCode;

        $len = strlen($text);
        for ($i = 0; $i < $len; $i++) {
            $ascii = ord($text[$i]);
            // Map ASCII to Code 128 Set B index (ASCII 32 to 126 maps to 0 to 94)
            $codeVal = ($ascii >= 32 && $ascii <= 126) ? ($ascii - 32) : 0;
            $values[] = $codeVal;
            $checksum += $codeVal * ($i + 1);
        }

        $checkDigit = $checksum % 103;
        $values[] = $checkDigit;
        $values[] = 106; // Stop code

        // Assemble full pattern of alternating bars and spaces
        $patternString = '';
        foreach ($values as $val) {
            $patternString .= self::$patterns[$val] ?? self::$patterns[0];
        }

        // Calculate total module width
        $totalModules = 0;
        $patternLen = strlen($patternString);
        for ($k = 0; $k < $patternLen; $k++) {
            $totalModules += (int) $patternString[$k];
        }

        $quietZoneModules = 10;
        $svgWidth = ($totalModules + ($quietZoneModules * 2)) * $barWidth;
        $svgHeight = $height + ($showText ? 18 : 0);

        $rects = [];
        $currentX = $quietZoneModules * $barWidth;
        $isBar = true;

        for ($k = 0; $k < $patternLen; $k++) {
            $width = ((int) $patternString[$k]) * $barWidth;
            if ($isBar) {
                $rects[] = sprintf('<rect x="%d" y="0" width="%d" height="%d" fill="#000000" />', $currentX, $width, $height);
            }
            $currentX += $width;
            $isBar = ! $isBar;
        }

        $textElement = '';
        if ($showText) {
            $textX = (int) ($svgWidth / 2);
            $textY = $height + 14;
            $textElement = sprintf(
                '<text x="%d" y="%d" text-anchor="middle" font-family="monospace, monospace" font-size="12" fill="#111827">%s</text>',
                $textX,
                $textY,
                htmlspecialchars($text, ENT_QUOTES, 'UTF-8')
            );
        }

        return sprintf(
            '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 %d %d" width="%d" height="%d" style="max-width: 100%%; height: auto;">' .
            '<rect width="100%%" height="100%%" fill="#ffffff" />' .
            '%s%s</svg>',
            $svgWidth,
            $svgHeight,
            $svgWidth,
            $svgHeight,
            implode('', $rects),
            $textElement
        );
    }

    /**
     * Get predefined thermal label configurations.
     */
    public function getLabelSizes(): array
    {
        return [
            '50x25' => [
                'name'         => '50mm × 25mm (Garment & Jewelry Tag)',
                'width_mm'     => 50,
                'height_mm'    => 25,
                'css_width'    => '50mm',
                'css_height'   => '25mm',
                'barcode_height' => 30,
                'font_size'    => '9px',
            ],
            '38x25' => [
                'name'         => '38mm × 25mm (Shelf Bin & Rack Sticker)',
                'width_mm'     => 38,
                'height_mm'    => 25,
                'css_width'    => '38mm',
                'css_height'   => '25mm',
                'barcode_height' => 26,
                'font_size'    => '8px',
            ],
            '100x50' => [
                'name'         => '100mm × 50mm (Outer Carton & Dispatch Box)',
                'width_mm'     => 100,
                'height_mm'    => 50,
                'css_width'    => '100mm',
                'css_height'   => '50mm',
                'barcode_height' => 60,
                'font_size'    => '13px',
            ],
        ];
    }
}

