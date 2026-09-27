<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Lembar Barcode Toko - {{ $storeName ?: 'Halala Food' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        *, *::before, *::after {
            box-sizing: border-box;
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
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen p-0 sm:p-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('barcodes.index', ['tab' => 'print']) }}" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl border border-slate-300 gap-1.5 cursor-pointer">
                <x-icon name="arrow-left" class="text-base" />
                <span>Kembali ke Studio</span>
            </a>
            <div>
                <p class="font-bold text-slate-900 text-sm leading-tight">Lembar Siap Cetak Barcode Toko</p>
                <p class="text-xs text-slate-500 font-mono">
                    {{ count($labels) }} Stiker Label • Grid {{ $columns }} × {{ $rows }} • Ukuran {{ $labelWidth }} × {{ $labelHeight }} mm
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Download PDF Button -->
            <button type="button" id="btn-download-pdf" onclick="exportToPdf()" 
               class="btn btn-sm bg-rose-700 hover:bg-rose-800 text-white font-bold rounded-xl px-4 gap-2 shadow-xs cursor-pointer"
               title="Simpan lembar stiker ini langsung sebagai file PDF (.pdf)">
                <x-icon name="file-type-pdf" class="text-base" />
                <span>Simpan PDF (.pdf)</span>
            </button>

            <!-- Download HTML for Flashdisk -->
            <a href="{{ route('barcodes.export-html', request()->query()) }}" 
               class="btn btn-sm bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl px-4 gap-2 shadow-xs cursor-pointer"
               title="Download file mandiri offline untuk dimasukkan ke flashdisk">
                <x-icon name="device-usb" class="text-base" />
                <span>Simpan ke Flashdisk (.html)</span>
            </a>

            <!-- Print Button -->
            <button type="button" onclick="window.print()" class="btn btn-sm bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-5 gap-2 shadow-xs cursor-pointer">
                <x-icon name="printer" class="text-base" />
                <span>Cetak Lembar A4 (Ctrl+P)</span>
            </button>
        </div>
    </div>

    <!-- Info banner for print shop -->
    <div class="no-print max-w-5xl mx-auto mb-4 bg-amber-50 border border-amber-200/80 rounded-xl p-3 text-xs text-amber-800 flex items-center gap-2">
        <x-icon name="info-circle" class="text-lg text-amber-600 shrink-0" />
        <span>
            <strong>Petunjuk Cetak Percetakan:</strong> Gunakan kertas stiker / label HVS/Glossy A4. Pada dialog Print, pastikan <strong>Scale/Skala: 100% (Actual Size / Do not fit)</strong> dan <strong>Margins: None / Minimum</strong> agar presisi dengan ukuran pisau stiker.
        </span>
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
                    <svg class="animate-spin h-4 w-4 text-white inline-block mr-1" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Menyusun PDF...</span>
                `;
            }

            const element = document.getElementById('print-container');
            const opt = {
                margin: 0,
                filename: '{{ !empty($storeName) ? "Barcode-".preg_replace("/[^A-Za-z0-9_\-]/", "_", $storeName)."-".date("Ymd") : "Barcode-Cetak-".date("Ymd") }}.pdf',
                image: { type: 'jpeg', quality: 0.98 },
                html2canvas: { 
                    scale: 3, 
                    useCORS: true, 
                    logging: false 
                },
                jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
                pagebreak: { mode: ['css', 'legacy'] }
            };

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
                alert('Gagal menyusun PDF otomatis. Silakan gunakan tombol Cetak lalu pilih opsi "Save as PDF / Simpan sebagai PDF".');
            });
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
