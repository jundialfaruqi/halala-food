<?php

namespace App\Services;

use Carbon\Carbon;

class FinancialStatementPdfService
{
    /**
     * Generate crisp vector A4 PDF for Income Statement or Balance Sheet.
     *
     * @param  array<string, mixed>  $data
     */
    public static function generate(array $data): string
    {
        $tab = $data['activeTab'] ?? 'income_statement';
        $preset = $data['periodPreset'] ?? 'this_month';

        $periodText = match ($preset) {
            'this_month' => 'Bulan Ini ('.Carbon::now()->translatedFormat('F Y').')',
            'last_month' => 'Bulan Lalu ('.Carbon::now()->subMonth()->translatedFormat('F Y').')',
            'this_year' => 'Tahun '.Carbon::now()->format('Y'),
            default => 'Seluruh Periode Berjalan',
        };

        $stream = "q\n";

        // Margins: Left 40pt, Right 40pt, Top 40pt (A4: 595.28 x 841.89 pt)
        $pageWidth = 595.28;
        $pageHeight = 841.89;
        $leftMargin = 42.0;
        $contentWidth = $pageWidth - ($leftMargin * 2);
        $rightMargin = $leftMargin + $contentWidth;

        $currentY = $pageHeight - 45.0; // Starting Y from top

        // 1. KOP SURAT
        // Title HALALA FOOD
        $stream .= "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F2 18 Tf\n";
        $titleText = 'HALALA FOOD';
        $stream .= sprintf("%.2f %.2f Td\n", ($pageWidth / 2) - 60, $currentY);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($titleText));
        $currentY -= 14.0;

        // Subtitle
        $stream .= "0.40 0.45 0.55 rg\n";
        $stream .= "BT\n/F2 8.5 Tf\n";
        $subText = 'USAHA MAKANAN & OLEH-OLEH KELUARGA';
        $stream .= sprintf("%.2f %.2f Td\n", ($pageWidth / 2) - 85, $currentY);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($subText));
        $currentY -= 16.0;

        // Document Name
        $stream .= "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F2 13 Tf\n";
        $docTitle = $tab === 'income_statement' ? 'LAPORAN LABA RUGI' : 'NERACA KEUANGAN (BALANCE SHEET)';
        $stream .= sprintf("%.2f %.2f Td\n", ($pageWidth / 2) - ($tab === 'income_statement' ? 70 : 120), $currentY);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($docTitle));
        $currentY -= 13.0;

        // Period Info
        $stream .= "0.30 0.35 0.42 rg\n";
        $stream .= "BT\n/F1 9.5 Tf\n";
        $pText = $tab === 'income_statement' ? "Periode: {$periodText}" : 'Posisi per: '.Carbon::now()->translatedFormat('d F Y');
        $stream .= sprintf("%.2f %.2f Td\n", ($pageWidth / 2) - (strlen($pText) * 2.5), $currentY);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($pText));
        $currentY -= 10.0;

        // Divider Line below Kop
        $stream .= "0.06 0.09 0.16 RG\n2 w\n";
        $stream .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $leftMargin, $currentY, $rightMargin, $currentY);
        $currentY -= 20.0;

        if ($tab === 'income_statement') {
            // === INCOME STATEMENT ===
            $revenueAccounts = $data['revenueAccounts'] ?? [];
            $cogsAccounts = $data['cogsAccounts'] ?? [];
            $expenseAccounts = $data['expenseAccounts'] ?? [];
            $totalRevenue = (float) ($data['totalRevenue'] ?? 0);
            $totalCogs = (float) ($data['totalCogs'] ?? 0);
            $grossProfit = (float) ($data['grossProfit'] ?? 0);
            $totalExpense = (float) ($data['totalExpense'] ?? 0);
            $netIncome = (float) ($data['netIncome'] ?? 0);

            // A. PENDAPATAN USAHA
            $currentY = self::renderSectionHeader($stream, '1. PENDAPATAN USAHA', $leftMargin, $currentY, $contentWidth);
            foreach ($revenueAccounts as $rev) {
                $currentY = self::renderRow($stream, $rev->name, 'Rp '.number_format($rev->balance, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            }
            $currentY = self::renderSubtotalRow($stream, 'TOTAL PENDAPATAN USAHA:', 'Rp '.number_format($totalRevenue, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            $currentY -= 12.0;

            // B. HPP
            $currentY = self::renderSectionHeader($stream, '2. BEBAN POKOK PENJUALAN (HPP)', $leftMargin, $currentY, $contentWidth);
            foreach ($cogsAccounts as $cogs) {
                $currentY = self::renderRow($stream, $cogs->name, 'Rp '.number_format($cogs->balance, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            }
            $currentY = self::renderSubtotalRow($stream, 'TOTAL BEBAN POKOK PENJUALAN (HPP):', '(Rp '.number_format($totalCogs, 0, ',', '.').')', $leftMargin, $currentY, $contentWidth, true);
            $currentY -= 12.0;

            // C. LABA KOTOR (Highlight Box)
            $currentY = self::renderHighlightBox($stream, 'LABA KOTOR (PENDAPATAN - HPP):', 'Rp '.number_format($grossProfit, 0, ',', '.'), $leftMargin, $currentY, $contentWidth, false);
            $currentY -= 14.0;

            // D. BEBAN OPERASIONAL
            $currentY = self::renderSectionHeader($stream, '3. BEBAN OPERASIONAL USAHA', $leftMargin, $currentY, $contentWidth);
            foreach ($expenseAccounts as $exp) {
                $currentY = self::renderRow($stream, $exp->name, 'Rp '.number_format($exp->balance, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            }
            $currentY = self::renderSubtotalRow($stream, 'TOTAL BEBAN OPERASIONAL:', '(Rp '.number_format($totalExpense, 0, ',', '.').')', $leftMargin, $currentY, $contentWidth, true);
            $currentY -= 14.0;

            // E. LABA BERSIH (Big Highlight Box)
            $currentY = self::renderHighlightBox($stream, 'LABA BERSIH USAHA (NET PROFIT):', 'Rp '.number_format($netIncome, 0, ',', '.'), $leftMargin, $currentY, $contentWidth, true);
        } else {
            // === BALANCE SHEET (NERACA) ===
            $assetAccounts = $data['assetAccounts'] ?? [];
            $liabilityAccounts = $data['liabilityAccounts'] ?? [];
            $equityAccounts = $data['equityAccounts'] ?? [];
            $totalAssets = (float) ($data['totalAssets'] ?? 0);
            $totalLiabilities = (float) ($data['totalLiabilities'] ?? 0);
            $cumulativeNetIncome = (float) ($data['cumulativeNetIncome'] ?? 0);
            $totalEquity = (float) ($data['totalEquity'] ?? 0);
            $totalLiabilitiesAndEquity = (float) ($data['totalLiabilitiesAndEquity'] ?? 0);

            // A. ASET
            $currentY = self::renderSectionHeader($stream, 'ASET (HARTA USAHA)', $leftMargin, $currentY, $contentWidth);
            foreach ($assetAccounts as $asset) {
                $currentY = self::renderRow($stream, $asset->name, 'Rp '.number_format($asset->balance, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            }
            $currentY = self::renderSubtotalRow($stream, 'TOTAL ASET:', 'Rp '.number_format($totalAssets, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            $currentY -= 14.0;

            // B. KEWAJIBAN
            $currentY = self::renderSectionHeader($stream, 'KEWAJIBAN (HUTANG USAHA)', $leftMargin, $currentY, $contentWidth);
            if ($liabilityAccounts->isEmpty()) {
                $currentY = self::renderRow($stream, 'Tidak ada kewajiban / hutang usaha.', '-', $leftMargin, $currentY, $contentWidth, true);
            } else {
                foreach ($liabilityAccounts as $liab) {
                    $currentY = self::renderRow($stream, $liab->name, 'Rp '.number_format($liab->balance, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
                }
            }
            $currentY = self::renderSubtotalRow($stream, 'TOTAL KEWAJIBAN:', 'Rp '.number_format($totalLiabilities, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            $currentY -= 14.0;

            // C. EKUITAS
            $currentY = self::renderSectionHeader($stream, 'EKUITAS (MODAL USAHA)', $leftMargin, $currentY, $contentWidth);
            foreach ($equityAccounts as $eq) {
                $eqVal = $eq->normal_balance === 'debit' ? '(Rp '.number_format($eq->balance, 0, ',', '.').')' : 'Rp '.number_format($eq->balance, 0, ',', '.');
                $currentY = self::renderRow($stream, $eq->name, $eqVal, $leftMargin, $currentY, $contentWidth);
            }
            $currentY = self::renderRow($stream, 'Laba Bersih Periode Berjalan', 'Rp '.number_format($cumulativeNetIncome, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            $currentY = self::renderSubtotalRow($stream, 'TOTAL EKUITAS / MODAL:', 'Rp '.number_format($totalEquity, 0, ',', '.'), $leftMargin, $currentY, $contentWidth);
            $currentY -= 14.0;

            // D. TOTAL KEWAJIBAN & EKUITAS
            $currentY = self::renderHighlightBox($stream, 'TOTAL KEWAJIBAN & EKUITAS:', 'Rp '.number_format($totalLiabilitiesAndEquity, 0, ',', '.'), $leftMargin, $currentY, $contentWidth, true);
        }

        // TANDA TANGAN (Signatures) at bottom
        $sigY = max(80.0, $currentY - 35.0);
        $stream .= "0.80 0.83 0.88 RG\n1 w\n";
        $stream .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $leftMargin, $sigY + 45.0, $rightMargin, $sigY + 45.0);

        // Kolom 1 (Kiri)
        $stream .= "0.30 0.35 0.42 rg\n";
        $stream .= "BT\n/F1 9 Tf\n";
        $stream .= sprintf("%.2f %.2f Td\n", $leftMargin + 40.0, $sigY + 30.0);
        $stream .= "(Dibuat Oleh,) Tj\nET\n";

        $stream .= "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F2 9.5 Tf\n";
        $stream .= sprintf("%.2f %.2f Td\n", $leftMargin + 20.0, $sigY - 10.0);
        $stream .= "(Bagian Pembukuan / Kasir) Tj\nET\n";

        // Kolom 2 (Kanan)
        $stream .= "0.30 0.35 0.42 rg\n";
        $stream .= "BT\n/F1 9 Tf\n";
        $stream .= sprintf("%.2f %.2f Td\n", $rightMargin - 150.0, $sigY + 30.0);
        $stream .= "(Disetujui Oleh,) Tj\nET\n";

        $stream .= "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F2 9.5 Tf\n";
        $stream .= sprintf("%.2f %.2f Td\n", $rightMargin - 170.0, $sigY - 10.0);
        $stream .= "(Pemilik Usaha Halala Food) Tj\nET\n";

        // Timestamp
        $stream .= "0.55 0.60 0.68 rg\n";
        $stream .= "BT\n/F1 8 Tf\n";
        $timeStr = 'Dicetak pada: '.Carbon::now()->translatedFormat('d F Y H:i');
        $stream .= sprintf("%.2f %.2f Td\n", $rightMargin - 160.0, 30.0);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($timeStr));

        $stream .= "Q\n";

        // Assemble PDF 1.4 output
        return self::buildPdfDocument([$stream]);
    }

    protected static function renderSectionHeader(string &$stream, string $title, float $x, float $y, float $width): float
    {
        // Light Gray Background Bar
        $stream .= "0.94 0.96 0.98 rg\n";
        $stream .= sprintf("%.2f %.2f %.2f %.2f re f\n", $x, $y - 4.0, $width, 18.0);

        // Header Text
        $stream .= "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F2 9.5 Tf\n";
        $stream .= sprintf("%.2f %.2f Td\n", $x + 6.0, $y + 1.5);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($title));

        return $y - 18.0;
    }

    protected static function renderRow(string &$stream, string $label, string $value, float $x, float $y, float $width, bool $italic = false): float
    {
        // Bottom border line
        $stream .= "0.90 0.92 0.95 RG\n0.5 w\n";
        $stream .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x + 4.0, $y - 3.0, $x + $width - 4.0, $y - 3.0);

        // Label
        $stream .= ($italic ? "0.45 0.50 0.58 rg\n" : "0.15 0.20 0.28 rg\n");
        $stream .= "BT\n/F1 9 Tf\n";
        $stream .= sprintf("%.2f %.2f Td\n", $x + 8.0, $y + 2.0);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($label));

        // Value (Courier-Bold for mono alignment)
        $stream .= "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F3 9.5 Tf\n";
        $valWidth = strlen($value) * 5.7;
        $stream .= sprintf("%.2f %.2f Td\n", $x + $width - $valWidth - 8.0, $y + 2.0);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($value));

        return $y - 15.0;
    }

    protected static function renderSubtotalRow(string &$stream, string $label, string $value, float $x, float $y, float $width, bool $isNegative = false): float
    {
        // Top thick line
        $stream .= "0.15 0.20 0.28 RG\n1.2 w\n";
        $stream .= sprintf("%.2f %.2f m %.2f %.2f l S\n", $x + 4.0, $y + 14.0, $x + $width - 4.0, $y + 14.0);

        // Label
        $stream .= "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F2 9.5 Tf\n";
        $stream .= sprintf("%.2f %.2f Td\n", $x + 8.0, $y + 2.0);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($label));

        // Value
        $stream .= $isNegative ? "0.72 0.11 0.11 rg\n" : "0.06 0.09 0.16 rg\n";
        $stream .= "BT\n/F3 10.5 Tf\n";
        $valWidth = strlen($value) * 6.3;
        $stream .= sprintf("%.2f %.2f Td\n", $x + $width - $valWidth - 8.0, $y + 2.0);
        $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($value));

        return $y - 16.0;
    }

    protected static function renderHighlightBox(string &$stream, string $label, string $value, float $x, float $y, float $width, bool $isDark = false): float
    {
        $boxHeight = 26.0;
        $boxY = $y - 6.0;

        if ($isDark) {
            // Dark Navy Fill
            $stream .= "0.06 0.09 0.16 rg\n";
            $stream .= sprintf("%.2f %.2f %.2f %.2f re f\n", $x, $boxY, $width, $boxHeight);

            // Text White
            $stream .= "1.0 1.0 1.0 rg\n";
            $stream .= "BT\n/F2 11 Tf\n";
            $stream .= sprintf("%.2f %.2f Td\n", $x + 12.0, $boxY + 8.5);
            $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($label));

            // Amount White
            $stream .= "1.0 1.0 1.0 rg\n";
            $stream .= "BT\n/F3 12 Tf\n";
            $valWidth = strlen($value) * 7.2;
            $stream .= sprintf("%.2f %.2f Td\n", $x + $width - $valWidth - 12.0, $boxY + 8.5);
            $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($value));
        } else {
            // Light Gray Fill with Border
            $stream .= "0.96 0.97 0.99 rg\n";
            $stream .= sprintf("%.2f %.2f %.2f %.2f re f\n", $x, $boxY, $width, $boxHeight);
            $stream .= "0.80 0.83 0.88 RG\n1.2 w\n";
            $stream .= sprintf("%.2f %.2f %.2f %.2f re S\n", $x, $boxY, $width, $boxHeight);

            // Text Dark
            $stream .= "0.06 0.09 0.16 rg\n";
            $stream .= "BT\n/F2 10.5 Tf\n";
            $stream .= sprintf("%.2f %.2f Td\n", $x + 10.0, $boxY + 8.5);
            $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($label));

            // Amount Dark
            $stream .= "0.06 0.09 0.16 rg\n";
            $stream .= "BT\n/F3 11.5 Tf\n";
            $valWidth = strlen($value) * 6.9;
            $stream .= sprintf("%.2f %.2f Td\n", $x + $width - $valWidth - 10.0, $boxY + 8.5);
            $stream .= sprintf("(%s) Tj\nET\n", self::escapePdfString($value));
        }

        return $boxY - 14.0;
    }

    protected static function buildPdfDocument(array $pageStreams): string
    {
        $catalogObjId = 1;
        $pagesObjId = 2;
        $fontF1ObjId = 3; // Helvetica
        $fontF2ObjId = 4; // Helvetica-Bold
        $fontF3ObjId = 5; // Courier-Bold

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

        $offsets[1] = strlen($out);
        $out .= "1 0 obj\n<< /Type /Catalog /Pages {$pagesObjId} 0 R >>\nendobj\n";

        $kidsStr = implode(' 0 R ', $pageObjIds).' 0 R';
        $offsets[2] = strlen($out);
        $out .= "2 0 obj\n<< /Type /Pages /Kids [ {$kidsStr} ] /Count {$pageCount} /MediaBox [ 0 0 595.28 841.89 ] >>\nendobj\n";

        $offsets[3] = strlen($out);
        $out .= "3 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>\nendobj\n";

        $offsets[4] = strlen($out);
        $out .= "4 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica-Bold >>\nendobj\n";

        $offsets[5] = strlen($out);
        $out .= "5 0 obj\n<< /Type /Font /Subtype /Type1 /BaseFont /Courier-Bold >>\nendobj\n";

        for ($i = 0; $i < $pageCount; $i++) {
            $pId = $pageObjIds[$i];
            $cId = $contentObjIds[$i];
            $streamData = $pageStreams[$i];
            $streamLen = strlen($streamData);

            $offsets[$pId] = strlen($out);
            $out .= "{$pId} 0 obj\n<< /Type /Page /Parent {$pagesObjId} 0 R /Contents {$cId} 0 R /Resources << /Font << /F1 {$fontF1ObjId} 0 R /F2 {$fontF2ObjId} 0 R /F3 {$fontF3ObjId} 0 R >> >> >>\nendobj\n";

            $offsets[$cId] = strlen($out);
            $out .= "{$cId} 0 obj\n<< /Length {$streamLen} >>\nstream\n{$streamData}\nendstream\nendobj\n";
        }

        $xrefOffset = strlen($out);
        $out .= "xref\n";
        $out .= '0 '.($totalObjects + 1)."\n";
        $out .= "0000000000 65535 f \n";
        for ($i = 1; $i <= $totalObjects; $i++) {
            $out .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $out .= "trailer\n<< /Size ".($totalObjects + 1)." /Root {$catalogObjId} 0 R >>\n";
        $out .= "startxref\n{$xrefOffset}\n%%EOF\n";

        return $out;
    }

    protected static function escapePdfString(string $str): string
    {
        $ascii = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $str);
        if ($ascii === false) {
            $ascii = preg_replace('/[^\x20-\x7E]/', '', $str);
        }

        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $ascii);
    }
}
