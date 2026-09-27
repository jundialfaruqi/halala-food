<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Cetak Barcode - {{ $storeName ?: 'Halala Food' }}</title>
    <style>
        *, *::before, *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            line-height: 1.4;
            padding: 24px 16px;
            -webkit-font-smoothing: antialiased;
        }
        .header-bar {
            max-width: 210mm;
            margin: 0 auto 16px auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 16px;
            padding: 16px 20px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .btn-print {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: 700;
            font-size: 14px;
            padding: 10px 22px;
            border-radius: 10px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: background 0.15s ease;
        }
        .btn-print:hover {
            background-color: #000000;
        }
        .guide-box {
            max-width: 210mm;
            margin: 0 auto 20px auto;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 12px 18px;
            border-radius: 12px;
            font-size: 13px;
            line-height: 1.5;
        }
        .print-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto 24px auto;
            padding: {{ $marginTop }}mm {{ $marginLeft }}mm;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.08), 0 2px 4px -2px rgba(0,0,0,0.05);
            page-break-after: always;
            break-after: page;
        }
        .label-grid {
            display: grid;
            grid-template-columns: repeat({{ $columns }}, {{ $labelWidth }}mm);
            grid-auto-rows: {{ $labelHeight }}mm;
            column-gap: {{ $gapX }}mm;
            row-gap: {{ $gapY }}mm;
        }
        .barcode-sticker {
            width: {{ $labelWidth }}mm;
            height: {{ $labelHeight }}mm;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            background: #ffffff;
            overflow: hidden;
            page-break-inside: avoid;
            break-inside: avoid;
            border-radius: 2px;
        }
        .sticker-header {
            width: 100%;
            flex-shrink: 0;
            line-height: 1.15;
        }
        .sticker-store {
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 700;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            line-height: 1.15;
        }
        .sticker-title {
            font-weight: 700;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            line-height: 1.2;
        }
        .sticker-barcode {
            width: 100%;
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            margin: 0.5mm 0;
        }
        .sticker-barcode svg {
            max-height: 100%;
            max-width: 100%;
            width: auto;
            height: 100%;
            display: block;
        }
        .sticker-footer {
            width: 100%;
            flex-shrink: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-weight: 700;
            color: #0f172a;
            border-top: 1px solid #f1f5f9;
            padding-top: 0.5mm;
            line-height: 1;
        }
        .sticker-sku {
            color: #64748b;
            font-weight: 500;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 42%;
        }
        .sticker-price {
            font-weight: 800;
            white-space: nowrap;
            margin-left: auto;
        }

        @media print {
            body {
                background: #ffffff !important;
                color: #000000 !important;
                padding: 0 !important;
                margin: 0 !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .header-bar, .guide-box, .no-print {
                display: none !important;
            }
            .print-sheet {
                box-shadow: none !important;
                border: none !important;
                margin: 0 !important;
                padding: {{ $marginTop }}mm {{ $marginLeft }}mm !important;
                width: 210mm !important;
                min-height: 297mm !important;
                height: 297mm !important;
                page-break-after: always !important;
                break-after: page !important;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
        }
    </style>
</head>
<body>

    <div class="header-bar">
        <div>
            <h2 style="font-size: 16px; font-weight: 700; color: #0f172a;">Dokumen Siap Cetak Barcode Toko — Halala Food</h2>
            <p style="font-size: 12px; color: #64748b; margin-top: 2px;">
                Toko: <strong style="color: #0f172a;">{{ $storeName ?: 'Umum' }}</strong> • Total: <strong style="color: #0f172a;">{{ count($labels) }} Stiker Label</strong> • Kertas A4 (Grid {{ $columns }} × {{ $rows }})
            </p>
        </div>
        <button type="button" onclick="window.print()" class="btn-print">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle;">
                <polyline points="6 9 6 2 18 2 18 9"></polyline>
                <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                <rect x="6" y="14" width="12" height="8"></rect>
            </svg>
            <span>Cetak Dokumen (Ctrl + P)</span>
        </button>
    </div>

    <div class="guide-box">
        <strong>💡 Petunjuk Percetakan:</strong> File ini siap cetak di kertas stiker label A4. Pada menu print browser, pastikan pilih <strong>Paper Size: A4</strong>, <strong>Scale: 100% (Actual Size / Do not fit)</strong>, dan <strong>Margins: None / Minimum</strong> agar presisi dengan ukuran pisau stiker.
    </div>

    @php
        $sheetCapacity = max(1, $columns * $rows);
        $sheets = array_chunk($labels, $sheetCapacity);

        if ($columns >= 5 || $labelHeight <= 20) {
            // 5x8 / Tom & Jerry 108 (38x18mm)
            $exportStoreSize = '6.5px';
            $exportTitleSize = '7.5px';
            $exportPriceSize = '7px';
            $exportSkuSize = '5.5px';
            $exportPadding = '1mm 1.5mm';
            $exportBarcodeMaxH = '7.5mm';
        } elseif ($rows >= 10 || $columns >= 4 || $labelHeight <= 30) {
            // 4x10 (48x28mm) / 4x6
            $exportStoreSize = '7.5px';
            $exportTitleSize = '8.5px';
            $exportPriceSize = '8px';
            $exportSkuSize = '6.5px';
            $exportPadding = '1.5mm 2mm';
            $exportBarcodeMaxH = '11.5mm';
        } else {
            // 3x8 / 2x6 (65x35mm / 95x45mm)
            $exportStoreSize = '8.5px';
            $exportTitleSize = '10px';
            $exportPriceSize = '9px';
            $exportSkuSize = '7.5px';
            $exportPadding = '2mm 2.5mm';
            $exportBarcodeMaxH = '14.5mm';
        }
    @endphp

    @foreach($sheets as $sheetLabels)
        <div class="print-sheet">
            <div class="label-grid">
                @foreach($sheetLabels as $label)
                    <div class="barcode-sticker" style="padding: {{ $exportPadding }}; {{ $showCutBorders ? 'border: 1px dashed #cbd5e1;' : 'border: 1px solid transparent;' }}">
                        <div class="sticker-header">
                            @if($showStoreName)
                                <div class="sticker-store" style="font-size: {{ $exportStoreSize }};">{{ $label['item']->store->name }}</div>
                            @endif
                            @if($showProductName)
                                <div class="sticker-title" style="font-size: {{ $exportTitleSize }};">{{ $label['item']->display_name }}</div>
                            @endif
                        </div>

                        <div class="sticker-barcode" style="max-height: {{ $exportBarcodeMaxH }};">
                            {!! $label['svg'] !!}
                        </div>

                        @if($showPrice && $label['item']->display_price > 0)
                            <div class="sticker-footer" style="font-size: {{ $exportPriceSize }};">
                                @if($label['item']->store_sku)
                                    <span class="sticker-sku" style="font-size: {{ $exportSkuSize }};">{{ $label['item']->store_sku }}</span>
                                @else
                                    <span></span>
                                @endif
                                <span class="sticker-price">Rp {{ number_format($label['item']->display_price, 0, ',', '.') }}</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endforeach

</body>
</html>

