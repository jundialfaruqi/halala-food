<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Lembar Barcode Toko - {{ $storeName ?: 'Halala Food' }}</title>

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
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        }

        .header-row-1 {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding-bottom: 12px;
            border-bottom: 1px solid #f1f5f9;
        }

        .header-row-2 {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding-top: 12px;
        }

        .btn-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 8px 18px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 10px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.15s ease;
        }

        .btn-back {
            background-color: #f8fafc;
            color: #334155;
            border-color: #cbd5e1;
        }
        .btn-back:hover {
            background-color: #e2e8f0;
        }

        .btn-print-main {
            background-color: #0f172a;
            color: #ffffff;
        }
        .btn-print-main:hover {
            background-color: #000000;
        }

        .btn-pdf {
            background-color: #be123c;
            color: #ffffff;
        }
        .btn-pdf:hover {
            background-color: #9f1239;
        }

        .btn-html {
            background-color: #047857;
            color: #ffffff;
        }
        .btn-html:hover {
            background-color: #065f46;
        }

        .badge-info {
            display: inline-flex;
            align-items: center;
            padding: 4px 10px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 6px;
            background-color: #f1f5f9;
            color: #334155;
            border: 1px solid #e2e8f0;
            font-family: ui-monospace, monospace;
        }

        .badge-warning {
            background-color: #fef3c7;
            color: #92400e;
            border-color: #fde68a;
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
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
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

        @keyframes spin {
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print header-bar">
        <!-- Baris 1: Navigasi & Informasi Dokumen -->
        <div class="header-row-1">
            <div style="display: flex; align-items: center; gap: 12px;">
                <a href="{{ route('barcodes.index', ['tab' => 'print']) }}" class="btn-action btn-back">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle;">
                        <line x1="19" y1="12" x2="5" y2="12"></line>
                        <polyline points="12 19 5 12 12 5"></polyline>
                    </svg>
                    <span>Kembali ke Studio</span>
                </a>
                <div>
                    <h1 style="font-size: 16px; font-weight: 700; color: #0f172a;">Lembar Siap Cetak Barcode Toko</h1>
                    <p style="font-size: 12px; color: #64748b; font-weight: 500;">
                        Toko: <strong style="color: #0f172a;">{{ $storeName ?: 'Umum' }}</strong>
                    </p>
                </div>
            </div>
            <div style="display: flex; flex-wrap: wrap; align-items: center; gap: 8px;">
                <span class="badge-info">
                    {{ count($labels) }} Stiker (Grid {{ $columns }} × {{ $rows }})
                </span>
                <span class="badge-info">
                    Ukuran {{ $labelWidth }} × {{ $labelHeight }} mm
                </span>
                @if(count($labels) > $columns * $rows)
                    <span class="badge-info badge-warning">
                        {{ ceil(count($labels) / ($columns * $rows)) }} Lembar A4
                    </span>
                @endif
            </div>
        </div>

        <!-- Baris 2: Tombol Aksi Pencetakan & Ekspor -->
        <div class="header-row-2">
            <p style="font-size: 12px; color: #64748b; font-weight: 500;">
                Pilih opsi pencetakan atau download file:
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 8px;">
                <!-- Print Button (Primary) -->
                <button type="button" onclick="window.print()" class="btn-action btn-print-main">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle;">
                        <polyline points="6 9 6 2 18 2 18 9"></polyline>
                        <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path>
                        <rect x="6" y="14" width="12" height="8"></rect>
                    </svg>
                    <span>Cetak Sekarang (Ctrl+P)</span>
                </button>

                <!-- Download PDF Button -->
                <button type="button" id="btn-download-pdf" onclick="exportToPdf()" class="btn-action btn-pdf"
                   title="Simpan lembar stiker ini langsung sebagai file PDF (.pdf)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle;">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                        <polyline points="14 2 14 8 20 8"></polyline>
                        <line x1="16" y1="13" x2="8" y2="13"></line>
                        <line x1="16" y1="17" x2="8" y2="17"></line>
                        <polyline points="10 9 9 9 8 9"></polyline>
                    </svg>
                    <span>Simpan PDF (.pdf)</span>
                </button>

                <!-- Download HTML for Flashdisk -->
                <a href="{{ route('barcodes.export-html', request()->query()) }}" class="btn-action btn-html"
                   title="Download file mandiri offline untuk dimasukkan ke flashdisk">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle;">
                        <path d="M7 2h10v4H7z"></path>
                        <path d="M5 6h14a2 2 0 0 1 2 2v11a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3V8a2 2 0 0 1 2-2z"></path>
                    </svg>
                    <span>Simpan Flashdisk (.html)</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Info banner for print shop -->
    <div class="no-print guide-box">
        <strong>💡 Petunjuk Cetak Percetakan:</strong> Gunakan kertas stiker / label HVS/Glossy A4. Pada dialog Print, pastikan <strong>Scale/Skala: 100% (Actual Size / Do not fit)</strong> dan <strong>Margins: None / Minimum</strong> agar presisi dengan ukuran pisau stiker.
    </div>

    @php
        $sheetCapacity = max(1, $columns * $rows);
        $sheets = array_chunk($labels, $sheetCapacity);

        if ($columns >= 5 || $labelHeight <= 20) {
            // 5x8 (Tom & Jerry 108 / 38x18mm)
            $printStoreSize = '6.5px';
            $printTitleSize = '7.5px';
            $printPriceSize = '7px';
            $printSkuSize = '5.5px';
            $printPadding = '1mm 1.5mm';
            $printBarcodeMaxH = '7.5mm';
        } elseif ($rows >= 10 || $columns >= 4 || $labelHeight <= 30) {
            // 4x10 (48x28mm) / 4x6
            $printStoreSize = '7.5px';
            $printTitleSize = '8.5px';
            $printPriceSize = '8px';
            $printSkuSize = '6.5px';
            $printPadding = '1.5mm 2mm';
            $printBarcodeMaxH = '11.5mm';
        } else {
            // 3x8 / 2x6 (65x35mm / 95x45mm)
            $printStoreSize = '8.5px';
            $printTitleSize = '10px';
            $printPriceSize = '9px';
            $printSkuSize = '7.5px';
            $printPadding = '2mm 2.5mm';
            $printBarcodeMaxH = '14.5mm';
        }
    @endphp

    <div id="print-container">
        <!-- Printable A4 Sheets -->
        @foreach($sheets as $sheetLabels)
            <div class="print-sheet">
                <div class="label-grid">
                    @foreach($sheetLabels as $idx => $label)
                        <div class="barcode-sticker" 
                             style="padding: {{ $printPadding }}; {{ $showCutBorders ? 'border: 1px dashed #cbd5e1;' : 'border: 1px solid transparent;' }}">
                            
                            <!-- Header Stiker: Nama Toko & Produk -->
                            <div class="sticker-header">
                                @if($showStoreName)
                                    <div class="sticker-store" style="font-size: {{ $printStoreSize }};">
                                        {{ $label['item']->store->name }}
                                    </div>
                                @endif
                                
                                @if($showProductName)
                                    <div class="sticker-title" style="font-size: {{ $printTitleSize }};">
                                        {{ $label['item']->display_name }}
                                    </div>
                                @endif
                            </div>

                            <!-- Barcode Graphic Vector SVG -->
                            <div class="sticker-barcode" style="max-height: {{ $printBarcodeMaxH }};">
                                {!! $label['svg'] !!}
                            </div>

                            <!-- Footer Stiker: Harga Jual & SKU -->
                            @if($showPrice && $label['item']->display_price > 0)
                                <div class="sticker-footer" style="font-size: {{ $printPriceSize }};">
                                    @if($label['item']->store_sku)
                                        <span class="sticker-sku" style="font-size: {{ $printSkuSize }};">
                                            {{ $label['item']->store_sku }}
                                        </span>
                                    @else
                                        <span></span>
                                    @endif
                                    <span class="sticker-price">
                                        Rp {{ number_format($label['item']->display_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <script src="/js/html2pdf.bundle.min.js"></script>
    <script>
        function exportToPdf() {
            const btn = document.getElementById('btn-download-pdf');
            const originalContent = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = `
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:inline-block; vertical-align:middle; animation: spin 1s linear infinite;">
                        <line x1="12" y1="2" x2="12" y2="6"></line>
                        <line x1="12" y1="18" x2="12" y2="22"></line>
                        <line x1="4.93" y1="4.93" x2="7.76" y2="7.76"></line>
                        <line x1="16.24" y1="16.24" x2="19.07" y2="19.07"></line>
                        <line x1="2" y1="12" x2="6" y2="12"></line>
                        <line x1="18" y1="12" x2="22" y2="12"></line>
                        <line x1="4.93" y1="19.07" x2="7.76" y2="16.24"></line>
                        <line x1="16.24" y1="7.76" x2="19.07" y2="4.93"></line>
                    </svg>
                    <span>Menyusun PDF...</span>
                `;
            }

            const element = document.getElementById('print-container');
            const filename = '{{ !empty($storeName) ? "Barcode-".preg_replace("/[^A-Za-z0-9_\-]/", "_", $storeName)."-".date("Ymd") : "Barcode-Cetak-".date("Ymd") }}.pdf';
            const opt = {
                margin: 0,
                filename: filename,
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 3, 
                    useCORS: true, 
                    logging: false,
                    letterRendering: true
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            };

            if (typeof html2pdf !== 'undefined') {
                html2pdf().set(opt).from(element).save().then(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                }).catch(err => {
                    console.error('PDF error:', err);
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalContent;
                    }
                    window.print();
                });
            } else {
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = originalContent;
                }
                window.print();
            }
        }

        @if(request()->boolean('autodownload_pdf'))
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    exportToPdf();
                }, 400);
            });
        @elseif(request()->boolean('autoprint'))
            window.addEventListener('DOMContentLoaded', () => {
                setTimeout(() => {
                    window.print();
                }, 350);
            });
        @endif
    </script>
</body>
</html>
