<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Lembar Barcode Toko - {{ $storeName ?: 'Halala Food' }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
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
                page-break-after: always;
            }
            @page {
                size: A4 portrait;
                margin: 0;
            }
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
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            padding: 1.5mm 2mm;
            background: #ffffff;
            overflow: hidden;
            page-break-inside: avoid;
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen p-0 sm:p-6">

    <!-- Action Bar (Hidden on Print) -->
    <div class="no-print max-w-5xl mx-auto mb-6 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('barcodes.index', ['tab' => 'print']) }}" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl border border-slate-300 gap-1.5">
                <x-icon name="arrow-left" class="text-base" />
                <span>Kembali ke Studio</span>
            </a>
            <div>
                <p class="font-bold text-slate-900 text-sm leading-tight">Lembar Siap Cetak Barcode Toko</p>
                <p class="text-xs text-slate-500 font-mono">
                    {{ count($labels) }} Stiker Label • Grid {{ $columns }}x{{ $rows }} • Ukuran {{ $labelWidth }}x{{ $labelHeight }} mm
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Download HTML for Flashdisk -->
            <a href="{{ route('barcodes.export-html', request()->query()) }}" 
               class="btn btn-sm bg-emerald-700 hover:bg-emerald-800 text-white font-bold rounded-xl px-4 gap-2 shadow-xs"
               title="Download file mandiri offline untuk dimasukkan ke flashdisk">
                <x-icon name="device-usb" class="text-base" />
                <span>Simpan ke Flashdisk (.html)</span>
            </a>

            <!-- Print Button -->
            <button type="button" onclick="window.print()" class="btn btn-sm bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-5 gap-2 shadow-sm cursor-pointer">
                <x-icon name="printer" class="text-base" />
                <span>Cetak Lembar A4 (Ctrl+P)</span>
            </button>
        </div>
    </div>

    <!-- Info banner for print shop -->
    <div class="no-print max-w-5xl mx-auto mb-4 bg-amber-50 border border-amber-200/80 rounded-xl p-3 text-xs text-amber-800 flex items-center gap-2">
        <x-icon name="info-circle" class="text-lg text-amber-600 shrink-0" />
        <span>
            <strong>Petunjuk Cetak Percetakan:</strong> Gunakan kertas stiker / label HVS/Glossy A4. Pada dialog Print, pastikan <strong>Scale/Skala: 100% (Actual Size / Do not fit)</strong> dan <strong>Margins: None / None</strong> agar presisi dengan ukuran stiker.
        </span>
    </div>

    <!-- Printable A4 Sheet -->
    <div class="print-sheet max-w-[210mm] mx-auto bg-white border border-slate-200 shadow-xl rounded-none transition-all"
         style="padding: {{ $marginTop }}mm {{ $marginLeft }}mm; min-height: 297mm; width: 210mm;">
        
        <div class="label-grid">
            @foreach($labels as $idx => $label)
                <div class="barcode-sticker {{ $showCutBorders ? 'border border-dashed border-slate-300 print:border-slate-300' : '' }}">
                    
                    <!-- Header Stiker: Nama Toko & Produk -->
                    <div class="w-full space-y-0.5 shrink-0">
                        @if($showStoreName)
                            <p class="text-[9px] uppercase tracking-wider font-bold text-slate-500 truncate leading-tight">
                                {{ $label['item']->store->name }}
                            </p>
                        @endif
                        
                        @if($showProductName)
                            <p class="text-[11px] font-bold text-slate-900 leading-tight line-clamp-1 truncate">
                                {{ $label['item']->display_name }}
                            </p>
                        @endif
                    </div>

                    <!-- Barcode Graphic Vector SVG -->
                    <div class="w-full flex-1 flex flex-col justify-center items-center my-0.5 max-h-[{{ $barcodeHeight }}px] overflow-hidden">
                        {!! $label['svg'] !!}
                    </div>

                    <!-- Footer Stiker: Harga Jual & SKU -->
                    @if($showPrice && $label['item']->display_price > 0)
                        <div class="w-full flex items-center justify-between text-[10px] font-mono font-bold text-slate-900 border-t border-slate-100 pt-0.5 shrink-0">
                            @if($label['item']->store_sku)
                                <span class="text-[8px] text-slate-500 font-normal truncate max-w-[45%]">
                                    {{ $label['item']->store_sku }}
                                </span>
                            @else
                                <span></span>
                            @endif
                            <span class="text-[10px] font-extrabold text-slate-900 ml-auto whitespace-nowrap">
                                Rp {{ number_format($label['item']->display_price, 0, ',', '.') }}
                            </span>
                        </div>
                    @endif

                </div>
            @endforeach
        </div>

    </div>

</body>
</html>
