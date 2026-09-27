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
        $labelHeightMm = (float) ($data['labelHeight'] ?? 34.0);
        $marginTopMm = (float) ($data['marginTop'] ?? 6.0);
        $marginLeftMm = (float) ($data['marginLeft'] ?? 6.0);
        $gapXMm = (float) ($data['gapX'] ?? 2.5);
        $gapYMm = (float) ($data['gapY'] ?? 1.5);

        $showProductName = (bool) ($data['showProductName'] ?? true);
        $showStoreName = (bool) ($data['showStoreName'] ?? true);
        $showPrice = (bool) ($data['showPrice'] ?? true);
        $showBarcodeText = (bool) ($data['showBarcodeText'] ?? true);
        $showCutBorders = (bool) ($data['showCutBorders'] ?? true);

        // Safety Guard: Auto-fit sheet within A4 boundaries (210mm x 297mm)
        $totalSheetWidthMm = $marginLeftMm + ($columns * $labelWidthMm) + (($columns - 1) * $gapXMm);
        $totalSheetHeightMm = $marginTopMm + ($rows * $labelHeightMm) + (($rows - 1) * $gapYMm);

        if ($totalSheetHeightMm > 294.0) {
            $scaleY = 290.0 / $totalSheetHeightMm;
            $marginTopMm = max(2.0, $marginTopMm * $scaleY);
            $gapYMm = max(0.5, $gapYMm * $scaleY);
            $labelHeightMm = $labelHeightMm * $scaleY;
        }

        if ($totalSheetWidthMm > 208.0) {
            $scaleX = 204.0 / $totalSheetWidthMm;
            $marginLeftMm = max(2.0, $marginLeftMm * $scaleX);
            $gapXMm = max(0.5, $gapXMm * $scaleX);
            $labelWidthMm = $labelWidthMm * $scaleX;
        }

        // Dynamic typography sizing based on sticker size
        if ($columns >= 5 || $labelHeightMm <= 20) {
            // Tom & Jerry 108 / Small stickers (38x18mm)
            $storeFontSize = 4.2;
            $titleFontSize = 5.0;
            $priceFontSize = 5.0;
            $skuFontSize = 4.0;
            $barcodeNumSize = 4.5;
        } elseif ($rows >= 10 || $columns >= 4 || $labelHeightMm <= 30) {
            // 4x10 (48x27mm) / 4x6
            $storeFontSize = 5.2;
            $titleFontSize = 6.2;
            $priceFontSize = 6.0;
            $skuFontSize = 4.8;
            $barcodeNumSize = 5.5;
        } else {
            // 3x8 (65x34mm) / 2x6
            $storeFontSize = 6.5;
            $titleFontSize = 7.8;
            $priceFontSize = 7.5;
            $skuFontSize = 5.8;
            $barcodeNumSize = 6.5;
        }

        $sheetCapacity = max(1, $columns * $rows);
        $sheets = array_chunk($labels, $sheetCapacity);
        if (empty($sheets)) {
            $sheets = [[]];
        }

        $mmToPt = 72.0 / 25.4; // 2.83464567
        $ptToMm = 25.4 / 72.0; // 0.35277778

        // Build content streams for each sheet
        $pageStreams = [];
        foreach ($sheets as $sheetLabels) {
            $stream = "q\n";

            foreach ($sheetLabels as $idx => $labelItem) {
                $item = $labelItem['item'];
                $r = (int) floor($idx / $columns);
                $c = (int) ($idx % $columns);

                $stickerX_mm = $marginLeftMm + ($c * ($labelWidthMm + $gapXMm));
                $stickerY_mm = $marginTopMm + ($r * ($labelHeightMm + $gapYMm));

                $x_pt = $stickerX_mm * $mmToPt;
                $y_pt = (297.0 - ($stickerY_mm + $labelHeightMm)) * $mmToPt;
                $w_pt = $labelWidthMm * $mmToPt;
                $h_pt = $labelHeightMm * $mmToPt;

                // 1. Dashed Cut Border
                if ($showCutBorders) {
                    $stream .= "0.80 0.83 0.88 RG\n";
                    $stream .= "[2 2] 0 d\n";
                    $stream .= "0.5 w\n";
                    $stream .= sprintf("%.2f %.2f %.2f %.2f re S\n", $x_pt, $y_pt, $w_pt, $h_pt);
                    $stream .= "[] 0 d\n";
                }

                // 2. Strict Sticker Clipping to prevent any visual cutoffs / bleed
                $stream .= "q\n";
                $stream .= sprintf("%.2f %.2f %.2f %.2f re W n\n", $x_pt + 0.5, $y_pt + 0.5, $w_pt - 1.0, $h_pt - 1.0);

                // Vertical Layout Distribution
                $pTopMm = max(0.8, min(1.5, $labelHeightMm * 0.04));
                $pBottomMm = max(0.8, min(1.5, $labelHeightMm * 0.04));
                $currentTopMm = $stickerY_mm + $pTopMm;

                // 3. Store Name (Header Top)
                if ($showStoreName && ! empty($item->store?->name)) {
                    $storeLineHeightMm = ($storeFontSize * $ptToMm * 1.15);
                    $storeBaselineY_mm = $currentTopMm + ($storeFontSize * $ptToMm * 0.82);
                    $storeY_pt = (297.0 - $storeBaselineY_mm) * $mmToPt;

                    $maxStoreChars = max(6, (int) floor(($w_pt - 6.0) / ($storeFontSize * 0.52)));
                    $storeName = strtoupper(mb_strimwidth($item->store->name, 0, $maxStoreChars, '..'));
                    $escapedStore = self::escapePdfString($storeName);

                    $actualStoreWidthPt = strlen($storeName) * ($storeFontSize * 0.52);
                    $storeX_pt = $x_pt + max(2.0, ($w_pt - $actualStoreWidthPt) / 2.0);

                    $stream .= "0.40 0.45 0.55 rg\n";
                    $stream .= "BT\n";
                    $stream .= sprintf("/F2 %.1f Tf\n", $storeFontSize);
                    $stream .= sprintf("%.2f %.2f Td\n", $storeX_pt, $storeY_pt);
                    $stream .= sprintf("(%s) Tj\n", $escapedStore);
                    $stream .= "ET\n";

                    $currentTopMm += $storeLineHeightMm + 0.3;
                }

                // 4. Product Name (Header)
                if ($showProductName && ! empty($item->display_name)) {
                    $titleLineHeightMm = ($titleFontSize * $ptToMm * 1.15);
                    $prodBaselineY_mm = $currentTopMm + ($titleFontSize * $ptToMm * 0.82);
                    $prodY_pt = (297.0 - $prodBaselineY_mm) * $mmToPt;

                    $maxTitleChars = max(8, (int) floor(($w_pt - 6.0) / ($titleFontSize * 0.52)));
                    $prodName = mb_strimwidth($item->display_name, 0, $maxTitleChars, '..');
                    $escapedProd = self::escapePdfString($prodName);

                    $actualTitleWidthPt = strlen($prodName) * ($titleFontSize * 0.52);
                    $prodX_pt = $x_pt + max(2.0, ($w_pt - $actualTitleWidthPt) / 2.0);

                    $stream .= "0.06 0.09 0.16 rg\n";
                    $stream .= "BT\n";
                    $stream .= sprintf("/F2 %.1f Tf\n", $titleFontSize);
                    $stream .= sprintf("%.2f %.2f Td\n", $prodX_pt, $prodY_pt);
                    $stream .= sprintf("(%s) Tj\n", $escapedProd);
                    $stream .= "ET\n";

                    $currentTopMm += $titleLineHeightMm + 0.4;
                }

                // 5. Footer Bounds Calculation (Bottom-Up)
                $hasFooter = $showPrice && ($item->display_price > 0 || ! empty($item->store_sku));
                $footerHeightMm = $hasFooter ? (($priceFontSize * $ptToMm * 1.15) + 1.2) : 0.0;
                $footerTopLimitMm = $stickerY_mm + $labelHeightMm - $pBottomMm - $footerHeightMm;

                // 6. Barcode Number Text (Bottom of Barcode)
                $rawBarcode = $item->barcode ?? '';
                $barcodeType = $item->barcode_type ?? 'AUTO';
                $structure = ! empty($rawBarcode) ? BarcodeService::getBarcodeBarsStructure($rawBarcode, $barcodeType) : null;
                $displayCode = $structure ? $structure['text'] : '';

                if ($showBarcodeText && ! empty($displayCode)) {
                    $codeBaselineY_mm = $footerTopLimitMm - 0.3;
                    $codeY_pt = (297.0 - $codeBaselineY_mm) * $mmToPt;

                    // Clamp barcode font size if code is long
                    $maxCodeWidthPt = $w_pt - 6.0;
                    $calcCodeFontSize = $barcodeNumSize;
                    if (strlen($displayCode) * ($calcCodeFontSize * 0.60) > $maxCodeWidthPt) {
                        $calcCodeFontSize = max(3.5, $maxCodeWidthPt / (strlen($displayCode) * 0.60));
                    }

                    $escapedCode = self::escapePdfString($displayCode);
                    $actualCodeWidthPt = strlen($displayCode) * ($calcCodeFontSize * 0.60);
                    $codeX_pt = $x_pt + max(2.0, ($w_pt - $actualCodeWidthPt) / 2.0);

                    $stream .= "0 0 0 rg\n";
                    $stream .= "BT\n";
                    $stream .= sprintf("/F3 %.1f Tf\n", $calcCodeFontSize);
                    $stream .= sprintf("%.2f %.2f Td\n", $codeX_pt, $codeY_pt);
                    $stream .= sprintf("(%s) Tj\n", $escapedCode);
                    $stream .= "ET\n";

                    $barcodeAreaBottomMm = $codeBaselineY_mm - ($calcCodeFontSize * $ptToMm) - 0.3;
                } else {
                    $barcodeAreaBottomMm = $footerTopLimitMm - 0.3;
                }

                // 7. Middle Barcode Graphic Bars
                if ($structure) {
                    $totalModules = max(1.0, $structure['totalModules']);
                    $bars = $structure['bars'];

                    $barcodeAreaTopMm = $currentTopMm + 0.3;
                    $availableBarHeightMm = max(3.0, $barcodeAreaBottomMm - $barcodeAreaTopMm);
                    $actualBarHeightMm = min($availableBarHeightMm, $labelHeightMm * 0.38);

                    $barcodeTopMm = $barcodeAreaTopMm + (($availableBarHeightMm - $actualBarHeightMm) / 2.0);

                    $maxBarcodeWidthMm = $labelWidthMm - 4.0;
                    $moduleWidthMm = min(0.32, $maxBarcodeWidthMm / $totalModules);
                    $actualBarcodeWidthMm = $totalModules * $moduleWidthMm;

                    $barcodeLeftMm = $stickerX_mm + (($labelWidthMm - $actualBarcodeWidthMm) / 2.0);

                    $stream .= "0 0 0 rg\n";
                    foreach ($bars as $bar) {
                        $barX_mm = $barcodeLeftMm + ($bar['offset'] * $moduleWidthMm);
                        $barW_mm = $bar['width'] * $moduleWidthMm;

                        $bX_pt = $barX_mm * $mmToPt;
                        $bY_pt = (297.0 - ($barcodeTopMm + $actualBarHeightMm)) * $mmToPt;
                        $bW_pt = $barW_mm * $mmToPt;
                        $bH_pt = $actualBarHeightMm * $mmToPt;

                        $stream .= sprintf("%.2f %.2f %.2f %.2f re f\n", $bX_pt, $bY_pt, $bW_pt, $bH_pt);
                    }
                }

                // 8. Footer (SKU & Price)
                if ($hasFooter) {
                    $footerBaselineY_mm = $stickerY_mm + $labelHeightMm - $pBottomMm - 0.2;
                    $footerY_pt = (297.0 - $footerBaselineY_mm) * $mmToPt;
                    $dividerLineY_pt = $footerY_pt + ($priceFontSize * 0.95);

                    // Divider line
                    $stream .= "0.90 0.92 0.95 RG\n";
                    $stream .= "0.4 w\n";
                    $stream .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x_pt + 3.0, $dividerLineY_pt, $x_pt + $w_pt - 3.0, $dividerLineY_pt);

                    // SKU Left
                    if (! empty($item->store_sku)) {
                        $maxSkuWidthPt = ($w_pt * 0.45) - 3.0;
                        $maxSkuChars = max(4, (int) floor($maxSkuWidthPt / ($skuFontSize * 0.52)));
                        $skuText = self::escapePdfString(mb_strimwidth($item->store_sku, 0, $maxSkuChars, '..'));

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
                        $priceWidthPt = strlen($priceStr) * ($priceFontSize * 0.58);
                        $priceX_pt = $x_pt + $w_pt - $priceWidthPt - 3.0;

                        $stream .= "0.06 0.09 0.16 rg\n";
                        $stream .= "BT\n";
                        $stream .= sprintf("/F3 %.1f Tf\n", $priceFontSize);
                        $stream .= sprintf("%.2f %.2f Td\n", $priceX_pt, $footerY_pt);
                        $stream .= sprintf("(%s) Tj\n", $escapedPrice);
                        $stream .= "ET\n";
                    }
                }

                $stream .= "Q\n"; // End of sticker clipping
            }

            $stream .= "Q\n"; // End of sheet stream
            $pageStreams[] = $stream;
        }

        // Build PDF document structures
        // Object IDs allocation:
        // 1: Catalog
        // 2: Pages
        // 3: Font F1 (Helvetica)
        // 4: Font F2 (Helvetica-Bold)
        // 5: Font F3 (Courier-Bold)
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
