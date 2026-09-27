<?php

namespace App\Services;

class BarcodePdfService
{
    /**
     * Generate standard vector A4 PDF binary string.
     *
     * @param  array<string, mixed>  $data
     */
    public static function generate(array $data): string
    {
        $labels = $data['labels'] ?? [];
        $columns = max(1, (int) ($data['columns'] ?? 3));
        $rows = max(1, (int) ($data['rows'] ?? 8));
        $labelWidthMm = (float) ($data['labelWidth'] ?? 65.0);
        $labelHeightMm = (float) ($data['labelHeight'] ?? 35.0);
        $marginTopMm = (float) ($data['marginTop'] ?? 8.0);
        $marginLeftMm = (float) ($data['marginLeft'] ?? 8.0);
        $gapXMm = (float) ($data['gapX'] ?? 3.0);
        $gapYMm = (float) ($data['gapY'] ?? 2.0);

        $showProductName = (bool) ($data['showProductName'] ?? true);
        $showStoreName = (bool) ($data['showStoreName'] ?? true);
        $showPrice = (bool) ($data['showPrice'] ?? true);
        $showBarcodeText = (bool) ($data['showBarcodeText'] ?? true);
        $showCutBorders = (bool) ($data['showCutBorders'] ?? true);

        // Responsive font sizing based on grid
        if ($columns >= 5 || $labelHeightMm <= 20) {
            // 5x8 Tom & Jerry 108 (38x18mm)
            $storeFontSize = 4.8;
            $titleFontSize = 5.8;
            $priceFontSize = 5.5;
            $skuFontSize = 4.5;
            $barcodeNumSize = 5.0;
            $maxBarHeightMm = 6.5;
        } elseif ($rows >= 10 || $columns >= 4 || $labelHeightMm <= 30) {
            // 4x10 (48x28mm)
            $storeFontSize = 5.8;
            $titleFontSize = 7.0;
            $priceFontSize = 6.8;
            $skuFontSize = 5.2;
            $barcodeNumSize = 6.0;
            $maxBarHeightMm = 10.0;
        } else {
            // 3x8 (65x35mm) / 2x6
            $storeFontSize = 7.0;
            $titleFontSize = 8.5;
            $priceFontSize = 8.0;
            $skuFontSize = 6.2;
            $barcodeNumSize = 7.0;
            $maxBarHeightMm = 13.0;
        }

        $sheetCapacity = max(1, $columns * $rows);
        $sheets = array_chunk($labels, $sheetCapacity);
        if (empty($sheets)) {
            $sheets = [[]];
        }

        $mmToPt = 72.0 / 25.4; // 2.83464567

        $pdfObjects = [];
        $pageObjectIds = [];

        // Build content streams for each sheet
        $pageStreams = [];
        foreach ($sheets as $sheetIndex => $sheetLabels) {
            $stream = "q\n";

            foreach ($sheetLabels as $idx => $labelItem) {
                $item = $labelItem['item'];
                $r = (int) floor($idx / $columns);
                $c = (int) ($idx % $columns);

                $stickerX_mm = $marginLeftMm + ($c * ($labelWidthMm + $gapXMm));
                $stickerY_mm = $marginTopMm + ($r * ($labelHeightMm + $gapYMm));

                $x_pt = $stickerX_mm * $mmToPt;
                $y_pt = (297.0 - $stickerY_mm - $labelHeightMm) * $mmToPt;
                $w_pt = $labelWidthMm * $mmToPt;
                $h_pt = $labelHeightMm * $mmToPt;

                // 1. Dashed Border
                if ($showCutBorders) {
                    $stream .= "0.80 0.83 0.88 RG\n";
                    $stream .= "[2 2] 0 d\n";
                    $stream .= "0.5 w\n";
                    $stream .= sprintf("%.2f %.2f %.2f %.2f re S\n", $x_pt, $y_pt, $w_pt, $h_pt);
                    $stream .= "[] 0 d\n";
                }

                // Inner content layout
                $currentTopMm = $stickerY_mm + 1.2;

                // 2. Store Name
                if ($showStoreName && ! empty($item->store?->name)) {
                    $storeName = strtoupper(mb_strimwidth($item->store->name, 0, 32, '..'));
                    $escapedStore = self::escapePdfString($storeName);
                    $storeY_pt = (297.0 - $currentTopMm - ($storeFontSize * 0.35)) * $mmToPt;
                    $centerX_pt = $x_pt + ($w_pt / 2.0);

                    $approxWidthPt = strlen($storeName) * ($storeFontSize * 0.52);
                    $textX_pt = max($x_pt + 3, $centerX_pt - ($approxWidthPt / 2.0));

                    $stream .= "0.40 0.45 0.55 rg\n";
                    $stream .= "BT\n";
                    $stream .= sprintf("/F2 %.1f Tf\n", $storeFontSize);
                    $stream .= sprintf("%.2f %.2f Td\n", $textX_pt, $storeY_pt);
                    $stream .= sprintf("(%s) Tj\n", $escapedStore);
                    $stream .= "ET\n";

                    $currentTopMm += ($storeFontSize * 0.45);
                }

                // 3. Product Name
                if ($showProductName && ! empty($item->display_name)) {
                    $prodName = mb_strimwidth($item->display_name, 0, 30, '..');
                    $escapedProd = self::escapePdfString($prodName);
                    $prodY_pt = (297.0 - $currentTopMm - ($titleFontSize * 0.35)) * $mmToPt;
                    $centerX_pt = $x_pt + ($w_pt / 2.0);

                    $approxWidthPt = strlen($prodName) * ($titleFontSize * 0.52);
                    $textX_pt = max($x_pt + 3, $centerX_pt - ($approxWidthPt / 2.0));

                    $stream .= "0.06 0.09 0.16 rg\n";
                    $stream .= "BT\n";
                    $stream .= sprintf("/F2 %.1f Tf\n", $titleFontSize);
                    $stream .= sprintf("%.2f %.2f Td\n", $textX_pt, $prodY_pt);
                    $stream .= sprintf("(%s) Tj\n", $escapedProd);
                    $stream .= "ET\n";

                    $currentTopMm += ($titleFontSize * 0.48);
                }

                // 4. Barcode Bars
                $rawBarcode = $item->barcode ?? '';
                $barcodeType = $item->barcode_type ?? 'AUTO';
                if (! empty($rawBarcode)) {
                    $structure = BarcodeService::getBarcodeBarsStructure($rawBarcode, $barcodeType);
                    $totalModules = max(1.0, $structure['totalModules']);
                    $bars = $structure['bars'];
                    $displayCode = $structure['text'];

                    $maxBarcodeWidthMm = $labelWidthMm * 0.88;
                    $moduleWidthMm = min(0.35, $maxBarcodeWidthMm / $totalModules);
                    $actualBarcodeWidthMm = $totalModules * $moduleWidthMm;

                    $barcodeLeftMm = $stickerX_mm + (($labelWidthMm - $actualBarcodeWidthMm) / 2.0);
                    $currentTopMm += 0.8;
                    $barcodeTopMm = $currentTopMm;
                    $barHeightMm = $maxBarHeightMm;

                    $stream .= "0 0 0 rg\n";
                    foreach ($bars as $bar) {
                        $barX_mm = $barcodeLeftMm + ($bar['offset'] * $moduleWidthMm);
                        $barW_mm = $bar['width'] * $moduleWidthMm;

                        $bX_pt = $barX_mm * $mmToPt;
                        $bY_pt = (297.0 - $barcodeTopMm - $barHeightMm) * $mmToPt;
                        $bW_pt = $barW_mm * $mmToPt;
                        $bH_pt = $barHeightMm * $mmToPt;

                        $stream .= sprintf("%.2f %.2f %.2f %.2f re f\n", $bX_pt, $bY_pt, $bW_pt, $bH_pt);
                    }

                    $currentTopMm += $barHeightMm + 0.3;

                    // Barcode number text
                    if ($showBarcodeText) {
                        $escapedCode = self::escapePdfString($displayCode);
                        $codeY_pt = (297.0 - $currentTopMm - ($barcodeNumSize * 0.35)) * $mmToPt;
                        $centerX_pt = $x_pt + ($w_pt / 2.0);
                        $approxWidthPt = strlen($displayCode) * ($barcodeNumSize * 0.60);
                        $textX_pt = max($x_pt + 3, $centerX_pt - ($approxWidthPt / 2.0));

                        $stream .= "0 0 0 rg\n";
                        $stream .= "BT\n";
                        $stream .= sprintf("/F3 %.1f Tf\n", $barcodeNumSize);
                        $stream .= sprintf("%.2f %.2f Td\n", $textX_pt, $codeY_pt);
                        $stream .= sprintf("(%s) Tj\n", $escapedCode);
                        $stream .= "ET\n";

                        $currentTopMm += ($barcodeNumSize * 0.45);
                    }
                }

                // 5. Footer: SKU & Price
                if ($showPrice && ($item->display_price > 0 || ! empty($item->store_sku))) {
                    $footerY_mm = $stickerY_mm + $labelHeightMm - 1.8;
                    $footerY_pt = (297.0 - $footerY_mm) * $mmToPt;

                    // Divider line
                    $lineY_pt = $footerY_pt + ($priceFontSize * 0.85);
                    $stream .= "0.94 0.96 0.98 RG\n";
                    $stream .= "0.4 w\n";
                    $stream .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x_pt + 4, $lineY_pt, $x_pt + $w_pt - 4, $lineY_pt);

                    // SKU Left
                    if (! empty($item->store_sku)) {
                        $skuText = self::escapePdfString(mb_strimwidth($item->store_sku, 0, 14, '..'));
                        $stream .= "0.40 0.45 0.55 rg\n";
                        $stream .= "BT\n";
                        $stream .= sprintf("/F1 %.1f Tf\n", $skuFontSize);
                        $stream .= sprintf("%.2f %.2f Td\n", $x_pt + 3.0, $footerY_pt);
                        $stream .= sprintf("(%s) Tj\n", $skuText);
                        $stream .= "ET\n";
                    }

                    // Price Right
                    if ($item->display_price > 0) {
                        $priceStr = 'Rp '.number_format((float) $item->display_price, 0, ',', '.');
                        $escapedPrice = self::escapePdfString($priceStr);
                        $approxPriceWidthPt = strlen($priceStr) * ($priceFontSize * 0.60);
                        $priceX_pt = $x_pt + $w_pt - $approxPriceWidthPt - 3.0;

                        $stream .= "0.06 0.09 0.16 rg\n";
                        $stream .= "BT\n";
                        $stream .= sprintf("/F3 %.1f Tf\n", $priceFontSize);
                        $stream .= sprintf("%.2f %.2f Td\n", $priceX_pt, $footerY_pt);
                        $stream .= sprintf("(%s) Tj\n", $escapedPrice);
                        $stream .= "ET\n";
                    }
                }
            }

            $stream .= "Q\n";
            $pageStreams[] = $stream;
        }

        // Build PDF document structures
        // Object IDs allocation:
        // 1: Catalog
        // 2: Pages
        // 3: Font F1 (Helvetica)
        // 4: Font F2 (Helvetica-Bold)
        // 5: Font F3 (Courier-Bold)
        // Then for each page i:
        // Page Object: 6 + (2 * i)
        // Content Stream: 7 + (2 * i)
        $catalogObjId = 1;
        $pagesObjId = 2;
        $fontF1ObjId = 3;
        $fontF2ObjId = 4;
        $fontF3ObjId = 5;

        $pageCount = count($pageStreams);
        $pageObjIds = [];
        $contentObjIds = [];

        for ($i = 0; $i < $pageCount; $i++) {
            $pageObjIds[$i] = 6 + ($i * 2);
            $contentObjIds[$i] = 7 + ($i * 2);
        }

        $totalObjects = 5 + ($pageCount * 2);

        $out = "%PDF-1.4\n";
        $offsets = [];

        // 1 0 obj: Catalog
        $offsets[1] = strlen($out);
        $out .= "1 0 obj\n<< /Type /Catalog /Pages {$pagesObjId} 0 R >>\nendobj\n";

        // 2 0 obj: Pages
        $kidsStr = implode(' 0 R ', $pageObjIds).' 0 R';
        $offsets[2] = strlen($out);
        $out .= "2 0 obj\n<< /Type /Pages /Kids [ {$kidsStr} ] /Count {$pageCount} /MediaBox [ 0 0 595.28 841.89 ] >>\nendobj\n";

        // Fonts
        $offsets[3] = strlen($out);
        $out .= "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $offsets[4] = strlen($out);
        $out .= "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n";

        $offsets[5] = strlen($out);
        $out .= "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Courier-Bold >>\nendobj\n";

        // Pages and Content streams
        for ($i = 0; $i < $pageCount; $i++) {
            $pId = $pageObjIds[$i];
            $cId = $contentObjIds[$i];
            $streamData = $pageStreams[$i];
            $streamLen = strlen($streamData);

            // Page Object
            $offsets[$pId] = strlen($out);
            $out .= "{$pId} 0 obj\n<< /Type /Page /Parent {$pagesObjId} 0 R /Contents {$cId} 0 R /Resources << /Font << /F1 {$fontF1ObjId} 0 R /F2 {$fontF2ObjId} 0 R /F3 {$fontF3ObjId} 0 R >> >> >>\nendobj\n";

            // Content Stream Object
            $offsets[$cId] = strlen($out);
            $out .= "{$cId} 0 obj\n<< /Length {$streamLen} >>\nstream\n{$streamData}\nendstream\nendobj\n";
        }

        // XRef Table
        $xrefOffset = strlen($out);
        $out .= "xref\n";
        $out .= '0 '.($totalObjects + 1)."\n";
        $out .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $totalObjects; $i++) {
            $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        // Trailer
        $out .= "trailer\n<< /Size ".($totalObjects + 1)." /Root {$catalogObjId} 0 R >>\n";
        $out .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $out;
    }

    /**
     * Escape characters for PDF literal strings.
     */
    protected static function escapePdfString(string $str): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        if ($ascii === false) {
            $ascii = preg_replace('/[^\x20-\x7E]/', '', $str);
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $ascii);
    }
}
