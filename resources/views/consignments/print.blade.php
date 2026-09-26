<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Surat Titip Barang - {{ $consignment->consignment_number }} - {{ $consignment->store->name }}</title>
    
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
            .print-page {
                padding: 0 !important;
                margin: 0 !important;
                max-width: 100% !important;
                box-shadow: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen p-0 sm:p-6">

    <!-- Action Bar (Screen Only / Hidden on Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('consignments.index') }}" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl border border-slate-300 gap-1.5">
                <x-icon name="arrow-left" class="text-base" />
                <span>Kembali</span>
            </a>
            <div>
                <p class="font-bold text-slate-900 text-sm leading-tight">Surat Titip Barang / Invoice Konsinyasi</p>
                <p class="text-xs text-slate-500 font-mono">{{ $consignment->consignment_number }} • {{ $consignment->store->name }}</p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Mode Switcher -->
            <div class="inline-flex items-center gap-1 p-1 bg-slate-100 rounded-xl border border-slate-200 text-xs font-semibold">
                <button type="button" id="btn-mode-dual" onclick="setMode('dual')" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white shadow-xs text-slate-900 font-bold">
                    2 Rangkap (Toko & Pengantar)
                </button>
                <button type="button" id="btn-mode-single" onclick="setMode('single')" class="px-3 py-1.5 rounded-lg transition-all cursor-pointer text-slate-600 hover:text-slate-900">
                    1 Lembar Penuh
                </button>
            </div>

            <!-- Print Button -->
            <button type="button" onclick="window.print()" class="btn btn-sm bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-4 gap-2 shadow-sm cursor-pointer">
                <x-icon name="printer" class="text-base" />
                <span>Cetak / Print</span>
            </button>
        </div>
    </div>

    <!-- Printable Container -->
    <div class="print-page max-w-4xl mx-auto space-y-4">
        
        <!-- Mode 1: Dual Copy (2 Rangkap dalam 1 Lembar Kertas A4 - Atas Toko, Bawah Pengantar) -->
        <div id="view-mode-dual" class="space-y-4">
            <!-- Lembar 1: Toko Mitra -->
            @include('consignments.invoice-slip', [
                'consignment' => $consignment,
                'copyTitle' => 'LEMBAR 1: UNTUK TOKO MITRA',
                'badgeClass' => 'bg-slate-900 text-white',
                'totalQty' => $totalQty,
                'totalValue' => $totalValue,
                'compact' => true,
            ])

            <!-- Dashed Cut Line -->
            <div class="relative py-2 flex items-center justify-center select-none text-slate-400 text-xs font-mono">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t-2 border-dashed border-slate-300"></div>
                </div>
                <div class="relative bg-slate-100 print:bg-white px-4 py-0.5 rounded-full border border-slate-300 flex items-center gap-2 text-[10px] uppercase font-bold tracking-wider text-slate-500">
                    <x-icon name="scissors" class="text-xs" />
                    <span>Gunting di sini (Lembar atas untuk Toko Mitra • Lembar bawah untuk Arsip Pengantar)</span>
                </div>
            </div>

            <!-- Lembar 2: Arsip Pengantar Halala Food -->
            @include('consignments.invoice-slip', [
                'consignment' => $consignment,
                'copyTitle' => 'LEMBAR 2: ARSIP PENGANTAR HALALA',
                'badgeClass' => 'bg-slate-700 text-white',
                'totalQty' => $totalQty,
                'totalValue' => $totalValue,
                'compact' => true,
            ])
        </div>

        <!-- Mode 2: Single Copy (1 Lembar Standar) -->
        <div id="view-mode-single" class="hidden">
            @include('consignments.invoice-slip', [
                'consignment' => $consignment,
                'copyTitle' => 'SURAT TITIP BARANG KONSINYASI',
                'badgeClass' => 'bg-slate-900 text-white',
                'totalQty' => $totalQty,
                'totalValue' => $totalValue,
                'compact' => false,
            ])
        </div>

    </div>

    <script>
        function setMode(mode) {
            const dualView = document.getElementById('view-mode-dual');
            const singleView = document.getElementById('view-mode-single');
            const btnDual = document.getElementById('btn-mode-dual');
            const btnSingle = document.getElementById('btn-mode-single');

            if (mode === 'dual') {
                dualView.classList.remove('hidden');
                singleView.classList.add('hidden');
                btnDual.className = 'px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white shadow-xs text-slate-900 font-bold';
                btnSingle.className = 'px-3 py-1.5 rounded-lg transition-all cursor-pointer text-slate-600 hover:text-slate-900';
            } else {
                dualView.classList.add('hidden');
                singleView.classList.remove('hidden');
                btnDual.className = 'px-3 py-1.5 rounded-lg transition-all cursor-pointer text-slate-600 hover:text-slate-900';
                btnSingle.className = 'px-3 py-1.5 rounded-lg transition-all cursor-pointer bg-white shadow-xs text-slate-900 font-bold';
            }
        }
    </script>
</body>
</html>
