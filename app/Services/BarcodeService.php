<?php

namespace App\Services;

class BarcodeService
{
    /**
     * Code 128 bar and space widths table (indices 0 to 106).
     *
     * @var array<int, string>
     */
    protected static array $code128Patterns = [
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
        100 => '114131', 101 => '311141', 102 => '411131', 103 => '211412', 104 => '211214',
        105 => '211232', 106 => '2331112',
    ];

    /**
     * Generate SVG for any barcode string.
     */
    public static function getSvg(
        string $code,
        string $type = 'AUTO',
        int $height = 50,
        float $moduleWidth = 1.5,
        bool $showText = true,
        string $color = '#000000',
        string $bgColor = 'transparent'
    ): string {
        $cleanCode = trim($code);
        if ($cleanCode === '') {
            return '';
        }

        // Auto determine or format
        $resolvedType = strtoupper($type);
        if ($resolvedType === 'AUTO') {
            $digitsOnly = preg_replace('/[^0-9]/', '', $cleanCode);
            if (strlen($digitsOnly) === 13 && strlen($cleanCode) === 13) {
                $resolvedType = 'EAN13';
            } else {
                $resolvedType = 'CODE128';
            }
        }

        if ($resolvedType === 'EAN13' && ctype_digit($cleanCode) && (strlen($cleanCode) === 12 || strlen($cleanCode) === 13)) {
            return self::generateEan13Svg($cleanCode, $height, $moduleWidth, $showText, $color, $bgColor);
        }

        return self::generateCode128Svg($cleanCode, $height, $moduleWidth, $showText, $color, $bgColor);
    }

    /**
     * Generate Code 128 (B and C auto-switch) SVG.
     */
    public static function generateCode128Svg(
        string $code,
        int $height = 50,
        float $moduleWidth = 1.5,
        bool $showText = true,
        string $color = '#000000',
        string $bgColor = 'transparent'
    ): string {
        $codes = [];
        $length = strlen($code);
        $isNumeric = ctype_digit($code) && $length >= 4 && ($length % 2 === 0);

        if ($isNumeric) {
            // Use Code C (Double digits)
            $codes[] = 105; // START C
            for ($i = 0; $i < $length; $i += 2) {
                $codes[] = (int) substr($code, $i, 2);
            }
        } else {
            // Use Code B (ASCII)
            $codes[] = 104; // START B
            for ($i = 0; $i < $length; $i++) {
                $ascii = ord($code[$i]);
                $val = ($ascii >= 32 && $ascii <= 126) ? $ascii - 32 : 0;
                $codes[] = $val;
            }
        }

        // Compute Checksum
        $checkSum = $codes[0];
        for ($i = 1; $i < count($codes); $i++) {
            $checkSum += $i * $codes[$i];
        }
        $codes[] = $checkSum % 103;
        $codes[] = 106; // STOP

        // Convert codes to bar pattern
        $patternString = '';
        foreach ($codes as $c) {
            if (isset(self::$code128Patterns[$c])) {
                $patternString .= self::$code128Patterns[$c];
            }
        }

        // Calculate total modules width
        $totalModules = 0;
        for ($i = 0; $i < strlen($patternString); $i++) {
            $totalModules += (int) $patternString[$i];
        }

        $quietZone = 10; // 10 modules quiet zone each side
        $totalWidth = ($totalModules + ($quietZone * 2)) * $moduleWidth;
        $barHeight = $height;
        $totalSvgHeight = $showText ? ($barHeight + 16) : $barHeight;

        $svgBars = [];
        $currentX = $quietZone * $moduleWidth;

        for ($i = 0; $i < strlen($patternString); $i++) {
            $w = (int) $patternString[$i] * $moduleWidth;
            $isBar = ($i % 2 === 0);

            if ($isBar) {
                $xFormatted = number_format($currentX, 2, '.', '');
                $wFormatted = number_format($w, 2, '.', '');
                $svgBars[] = "<rect x=\"{$xFormatted}\" y=\"0\" width=\"{$wFormatted}\" height=\"{$barHeight}\" fill=\"{$color}\" />";
            }
            $currentX += $w;
        }

        $barsSvg = implode('', $svgBars);
        $escapedText = htmlspecialchars($code, ENT_QUOTES, 'UTF-8');
        $textSvg = '';
        if ($showText) {
            $textY = $barHeight + 12;
            $textSvg = "<text x=\"50%\" y=\"{$textY}\" text-anchor=\"middle\" font-family=\"monospace, -apple-system, system-ui\" font-weight=\"700\" font-size=\"11\" letter-spacing=\"1\" fill=\"{$color}\">{$escapedText}</text>";
        }

        $bgRect = $bgColor !== 'transparent' ? "<rect width=\"100%\" height=\"100%\" fill=\"{$bgColor}\" />" : '';

        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$totalWidth} {$totalSvgHeight}\" width=\"100%\" height=\"100%\" preserveAspectRatio=\"xMidYMid meet\">{$bgRect}{$barsSvg}{$textSvg}</svg>";
    }

    /**
     * Generate EAN-13 SVG.
     */
    public static function generateEan13Svg(
        string $code,
        int $height = 50,
        float $moduleWidth = 1.5,
        bool $showText = true,
        string $color = '#000000',
        string $bgColor = 'transparent'
    ): string {
        $digits = preg_replace('/[^0-9]/', '', $code);
        if (strlen($digits) === 12) {
            $sum = 0;
            for ($i = 0; $i < 12; $i++) {
                $sum += ((int) $digits[$i]) * ($i % 2 === 0 ? 1 : 3);
            }
            $check = (10 - ($sum % 10)) % 10;
            $digits .= $check;
        }

        if (strlen($digits) !== 13) {
            return self::generateCode128Svg($code, $height, $moduleWidth, $showText, $color, $bgColor);
        }

        $patternsL = ['0001101', '0011001', '0010011', '0111101', '0100011', '0110001', '0101111', '0111011', '0110111', '0001011'];
        $patternsG = ['0100111', '0110011', '0011011', '0100001', '0011101', '0111001', '0000101', '0010001', '0001001', '0010111'];
        $patternsR = ['1110010', '1100110', '1101100', '1000010', '1011100', '1001110', '1010000', '1000100', '1001000', '1110100'];
        $structure = [
            0 => 'LLLLLL', 1 => 'LLGLGG', 2 => 'LLGGLG', 3 => 'LLGGGL', 4 => 'LGLLGG',
            5 => 'LGGLLG', 6 => 'LGGGLL', 7 => 'LGLGLG', 8 => 'LGLGGL', 9 => 'LGGLGL',
        ];

        $firstDigit = (int) $digits[0];
        $struct = $structure[$firstDigit];

        $bin = '101'; // Left Guard

        // Left 6 digits (digits 1 to 6)
        for ($i = 1; $i <= 6; $i++) {
            $d = (int) $digits[$i];
            $type = $struct[$i - 1];
            $bin .= ($type === 'L') ? $patternsL[$d] : $patternsG[$d];
        }

        $bin .= '01010'; // Center Guard

        // Right 6 digits (digits 7 to 12)
        for ($i = 7; $i <= 12; $i++) {
            $d = (int) $digits[$i];
            $bin .= $patternsR[$d];
        }

        $bin .= '101'; // Right Guard

        $moduleCount = strlen($bin);
        $quietZone = 9;
        $totalWidth = ($moduleCount + ($quietZone * 2)) * $moduleWidth;
        $barHeight = $height;
        $totalSvgHeight = $showText ? ($barHeight + 16) : $barHeight;

        $svgBars = [];
        $currentX = $quietZone * $moduleWidth;

        for ($i = 0; $i < $moduleCount; $i++) {
            $w = $moduleWidth;
            if ($bin[$i] === '1') {
                $xFormatted = number_format($currentX, 2, '.', '');
                $wFormatted = number_format($w, 2, '.', '');
                $svgBars[] = "<rect x=\"{$xFormatted}\" y=\"0\" width=\"{$wFormatted}\" height=\"{$barHeight}\" fill=\"{$color}\" />";
            }
            $currentX += $w;
        }

        $barsSvg = implode('', $svgBars);
        $escapedText = htmlspecialchars($digits, ENT_QUOTES, 'UTF-8');
        $textSvg = '';
        if ($showText) {
            $textY = $barHeight + 12;
            $textSvg = "<text x=\"50%\" y=\"{$textY}\" text-anchor=\"middle\" font-family=\"monospace, -apple-system, system-ui\" font-weight=\"700\" font-size=\"11\" letter-spacing=\"1\" fill=\"{$color}\">{$escapedText}</text>";
        }

        $bgRect = $bgColor !== 'transparent' ? "<rect width=\"100%\" height=\"100%\" fill=\"{$bgColor}\" />" : '';

        return "<svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 {$totalWidth} {$totalSvgHeight}\" width=\"100%\" height=\"100%\" preserveAspectRatio=\"xMidYMid meet\">{$bgRect}{$barsSvg}{$textSvg}</svg>";
    }

    /**
     * Get Base64 Data URI of SVG.
     */
    public static function getDataUri(string $code, string $type = 'AUTO', int $height = 50, float $moduleWidth = 1.5, bool $showText = true): string
    {
        $svg = self::getSvg($code, $type, $height, $moduleWidth, $showText);

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
