<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Cetak Barcode - {{ $storeName ?: 'Halala Food' }}</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background-color: #f1f5f9;
            color: #0f172a;
            line-height: 1.4;
            padding: 20px;
        }
        .header-bar {
            max-width: 210mm;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 16px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }
        .btn-print {
            background-color: #0f172a;
            color: #ffffff;
            font-weight: bold;
            font-size: 14px;
            padding: 10px 20px;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-print:hover {
            background-color: #000000;
        }
        .guide-box {
            max-width: 210mm;
            margin: 0 auto 16px auto;
            background-color: #fffbeb;
            border: 1px solid #fde68a;
            color: #92400e;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
        }
        .print-sheet {
            width: 210mm;
            min-height: 297mm;
            background: #ffffff;
            margin: 0 auto;
            padding: {{ $marginTop }}mm {{ $marginLeft }}mm;
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
            page-break-after: always;
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
            padding: 1.5mm 2mm;
            background: #ffffff;
            overflow: hidden;
            page-break-inside: avoid;
            {{ $showCutBorders ? 'border: 1px dashed #cbd5e1;' : '' }}
        }
        .sticker-store {
            font-size: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: bold;
            color: #64748b;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            line-height: 1.1;
        }
        .sticker-title {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            width: 100%;
            line-height: 1.2;
        }
        .sticker-barcode {
            width: 100%;
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 1mm 0;
            max-height: {{ $barcodeHeight }}px;
        }
        .sticker-barcode svg {
            max-height: 100%;
            width: auto;
        }
        .sticker-footer {
            width: 100%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 9px;
            font-family: monospace;
            font-weight: bold;
            color: #0f172a;
            border-top: 1px solid #f1f5f9;
            padding-top: 1px;
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
            <h2 style="font-size: 16px; font-weight: bold;">Dokumen Cetak Barcode Toko — Halala Food</h2>
            <p style="font-size: 12px; color: #64748b;">
                Toko: <strong>{{ $storeName ?: 'Umum' }}</strong> • Total: <strong>{{ count($labels) }} Stiker Label</strong> • Kertas A4 (Grid {{ $columns }}x{{ $rows }})
            </p>
        </div>
        <button type="button" onclick="window.print()" class="btn-print">
            &#128424; Cetak Dokumen (Ctrl + P)
        </button>
    </div>

    <div class="guide-box">
        <strong>Petunjuk Percetakan:</strong> File ini siap cetak di kertas stiker label A4. Pada menu print browser, pilih <strong>Paper Size: A4</strong>, <strong>Scale: 100% (Actual Size)</strong>, dan <strong>Margins: None / Minimum</strong>.
    </div>

    <div class="print-sheet">
        <div class="label-grid">
            @foreach($labels as $label)
                <div class="barcode-sticker">
                    <div style="width: 100%;">
                        @if($showStoreName)
                            <div class="sticker-store">{{ $label['item']->store->name }}</div>
                        @endif
                        @if($showProductName)
                            <div class="sticker-title">{{ $label['item']->display_name }}</div>
                        @endif
                    </div>

                    <div class="sticker-barcode">
                        {!! $label['svg'] !!}
                    </div>

                    @if($showPrice && $label['item']->display_price > 0)
                        <div class="sticker-footer">
                            <span style="font-size: 8px; color: #64748b;">{{ $label['item']->store_sku ?: '' }}</span>
                            <span>Rp {{ number_format($label['item']->display_price, 0, ',', '.') }}</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

</body>
</html>
