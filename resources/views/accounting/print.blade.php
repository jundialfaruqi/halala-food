<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $activeTab === 'income_statement' ? 'Laporan Laba Rugi' : 'Neraca Keuangan' }} - Halala Food</title>
    
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
                border: none !important;
                box-shadow: none !important;
            }
            @page {
                size: A4 portrait;
                margin: 10mm 15mm;
            }
        }
    </style>
</head>
<body class="bg-slate-100 text-slate-900 font-sans antialiased min-h-screen p-0 sm:p-6">

    <!-- Action Bar (Screen Only / Hidden on Print) -->
    <div class="no-print max-w-4xl mx-auto mb-6 bg-white border border-slate-200/80 rounded-2xl p-4 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.financial-statements', ['activeTab' => $activeTab, 'periodPreset' => $periodPreset]) }}" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold rounded-xl border border-slate-300 gap-1.5">
                <x-icon name="arrow-left" class="text-base" />
                <span>Kembali ke Laporan</span>
            </a>
            <div>
                <p class="font-bold text-slate-900 text-sm leading-tight">
                    {{ $activeTab === 'income_statement' ? 'Laporan Laba Rugi' : 'Neraca Keuangan' }}
                </p>
                <p class="text-xs text-slate-500 font-mono">
                    Periode: {{ $periodPreset === 'this_month' ? 'Bulan Ini' : ($periodPreset === 'last_month' ? 'Bulan Lalu' : ($periodPreset === 'this_year' ? 'Tahun Ini' : 'Seluruh Periode')) }}
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('accounting.financial-statements.export-pdf', ['tab' => $activeTab, 'period' => $periodPreset]) }}" class="btn btn-sm bg-rose-700 hover:bg-rose-800 text-white font-bold rounded-xl px-4 gap-2 shadow-sm cursor-pointer">
                <x-icon name="file-type-pdf" class="text-base" />
                <span>Download PDF</span>
            </a>
            <button type="button" onclick="window.print()" class="btn btn-sm bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-4 gap-2 shadow-sm cursor-pointer">
                <x-icon name="printer" class="text-base" />
                <span>Cetak / Print</span>
            </button>
        </div>
    </div>

    <!-- Printable Container (Clean A4 Paper) -->
    <div class="print-page max-w-4xl mx-auto bg-white p-8 rounded-2xl border border-slate-200 shadow-sm space-y-6" id="printable-report-card">
        
        @if($activeTab === 'income_statement')
            <!-- 1. LAPORAN LABA RUGI -->
            <!-- Kop Dokumen Laporan -->
            <div class="text-center pb-4 border-b-2 border-slate-900">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">HALALA FOOD</h1>
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mt-0.5">Usaha Makanan & Oleh-Oleh Keluarga</p>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 mt-2">LAPORAN LABA RUGI</h2>
                <p class="text-sm font-semibold text-slate-600 mt-0.5">
                    Periode: {{ $periodPreset === 'this_month' ? 'Bulan Ini (' . Carbon\Carbon::now()->translatedFormat('F Y') . ')' : ($periodPreset === 'last_month' ? 'Bulan Lalu (' . Carbon\Carbon::now()->subMonth()->translatedFormat('F Y') . ')' : ($periodPreset === 'this_year' ? 'Tahun ' . Carbon\Carbon::now()->format('Y') : 'Seluruh Periode Berjalan')) }}
                </p>
            </div>

            <div class="space-y-6 text-base">
                <!-- A. PENDAPATAN USAHA -->
                <div>
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-sm bg-slate-100 print:bg-slate-200 p-2 rounded-lg mb-2">
                        1. PENDAPATAN USAHA
                    </h3>
                    <div class="space-y-1.5 px-3">
                        @foreach($revenueAccounts as $rev)
                            <div class="flex justify-between py-1.5 border-b border-slate-200">
                                <span class="text-slate-800 font-medium">{{ $rev->name }}</span>
                                <span class="font-mono font-bold text-slate-900">Rp {{ number_format($rev->balance, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between pt-2.5 font-bold text-slate-900 border-t-2 border-slate-800">
                            <span>TOTAL PENDAPATAN USAHA:</span>
                            <span class="font-mono text-lg">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- B. BEBAN POKOK PENJUALAN (HPP) -->
                <div>
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-sm bg-slate-100 print:bg-slate-200 p-2 rounded-lg mb-2">
                        2. BEBAN POKOK PENJUALAN (HPP)
                    </h3>
                    <div class="space-y-1.5 px-3">
                        @foreach($cogsAccounts as $cogs)
                            <div class="flex justify-between py-1.5 border-b border-slate-200">
                                <span class="text-slate-800 font-medium">{{ $cogs->name }}</span>
                                <span class="font-mono font-bold text-slate-900">Rp {{ number_format($cogs->balance, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between pt-2.5 font-bold text-slate-900 border-t-2 border-slate-800">
                            <span>TOTAL BEBAN POKOK PENJUALAN (HPP):</span>
                            <span class="font-mono text-lg text-red-700 print:text-black">(Rp {{ number_format($totalCogs, 0, ',', '.') }})</span>
                        </div>
                    </div>
                </div>

                <!-- C. LABA KOTOR -->
                <div class="p-4 bg-slate-50 print:bg-white rounded-xl border-2 border-slate-300 print:border-slate-800 flex justify-between items-center font-bold text-lg">
                    <span class="text-slate-900">LABA KOTOR (PENDAPATAN - HPP):</span>
                    <span class="font-mono text-xl text-slate-900">Rp {{ number_format($grossProfit, 0, ',', '.') }}</span>
                </div>

                <!-- D. BEBAN OPERASIONAL -->
                <div>
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-sm bg-slate-100 print:bg-slate-200 p-2 rounded-lg mb-2">
                        3. BEBAN OPERASIONAL USAHA
                    </h3>
                    <div class="space-y-1.5 px-3">
                        @foreach($expenseAccounts as $exp)
                            <div class="flex justify-between py-1.5 border-b border-slate-200">
                                <span class="text-slate-800 font-medium">{{ $exp->name }}</span>
                                <span class="font-mono font-bold text-slate-900">Rp {{ number_format($exp->balance, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="flex justify-between pt-2.5 font-bold text-slate-900 border-t-2 border-slate-800">
                            <span>TOTAL BEBAN OPERASIONAL:</span>
                            <span class="font-mono text-lg text-red-700 print:text-black">(Rp {{ number_format($totalExpense, 0, ',', '.') }})</span>
                        </div>
                    </div>
                </div>

                <!-- E. LABA BERSIH (NET INCOME) -->
                <div class="p-5 bg-slate-900 print:bg-white text-white print:text-black rounded-xl border-2 border-slate-900 flex justify-between items-center font-bold text-xl">
                    <span>LABA BERSIH USAHA (NET PROFIT):</span>
                    <span class="font-mono text-2xl">
                        Rp {{ number_format($netIncome, 0, ',', '.') }}
                    </span>
                </div>
            </div>

        @else
            <!-- 2. LAPORAN NERACA KEUANGAN (BALANCE SHEET) -->
            <!-- Kop Dokumen Laporan -->
            <div class="text-center pb-4 border-b-2 border-slate-900">
                <h1 class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">HALALA FOOD</h1>
                <p class="text-xs uppercase tracking-widest text-slate-500 font-bold mt-0.5">Usaha Makanan & Oleh-Oleh Keluarga</p>
                <h2 class="text-lg sm:text-xl font-bold text-slate-800 mt-2">NERACA KEUANGAN (BALANCE SHEET)</h2>
                <p class="text-sm font-semibold text-slate-600 mt-0.5">
                    Posisi per: {{ Carbon\Carbon::now()->translatedFormat('d F Y') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 print:grid-cols-2 gap-8 text-base">
                <!-- KOLOM KIRI: ASET / AKTIVA -->
                <div class="space-y-4">
                    <h3 class="font-bold text-slate-900 uppercase tracking-wider text-base bg-slate-100 print:bg-slate-200 p-2.5 rounded-lg">
                        ASET (HARTA USAHA)
                    </h3>
                    <div class="space-y-2 px-2">
                        @foreach($assetAccounts as $asset)
                            <div class="flex justify-between py-1.5 border-b border-slate-200">
                                <span class="text-slate-800 font-medium">{{ $asset->name }}</span>
                                <span class="font-mono font-bold text-slate-900">Rp {{ number_format($asset->balance, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="p-4 bg-slate-50 print:bg-white rounded-xl border-2 border-slate-300 print:border-slate-800 flex justify-between items-center font-bold text-lg">
                        <span class="text-slate-900">TOTAL ASET:</span>
                        <span class="font-mono text-xl text-slate-900">Rp {{ number_format($totalAssets, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- KOLOM KANAN: KEWAJIBAN & EKUITAS / PASIVA -->
                <div class="space-y-6">
                    <!-- Kewajiban -->
                    <div class="space-y-4">
                        <h3 class="font-bold text-slate-900 uppercase tracking-wider text-base bg-slate-100 print:bg-slate-200 p-2.5 rounded-lg">
                            KEWAJIBAN (HUTANG)
                        </h3>
                        <div class="space-y-2 px-2">
                            @forelse($liabilityAccounts as $liab)
                                <div class="flex justify-between py-1.5 border-b border-slate-200">
                                    <span class="text-slate-800 font-medium">{{ $liab->name }}</span>
                                    <span class="font-mono font-bold text-slate-900">Rp {{ number_format($liab->balance, 0, ',', '.') }}</span>
                                </div>
                            @empty
                                <p class="text-sm text-slate-500 py-1 italic">Tidak ada kewajiban / hutang usaha.</p>
                            @endforelse
                        </div>
                        <div class="flex justify-between px-2 font-bold text-slate-800 border-t border-slate-300 pt-2">
                            <span>Total Kewajiban:</span>
                            <span class="font-mono text-base">Rp {{ number_format($totalLiabilities, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Ekuitas / Modal -->
                    <div class="space-y-4">
                        <h3 class="font-bold text-slate-900 uppercase tracking-wider text-base bg-slate-100 print:bg-slate-200 p-2.5 rounded-lg">
                            EKUITAS (MODAL USAHA)
                        </h3>
                        <div class="space-y-2 px-2">
                            @foreach($equityAccounts as $eq)
                                <div class="flex justify-between py-1.5 border-b border-slate-200">
                                    <span class="text-slate-800 font-medium">{{ $eq->name }}</span>
                                    <span class="font-mono font-bold text-slate-900">
                                        {{ $eq->normal_balance === 'debit' ? '(Rp ' . number_format($eq->balance, 0, ',', '.') . ')' : 'Rp ' . number_format($eq->balance, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                            <!-- Laba Bersih Berjalan Kumulatif -->
                            <div class="flex justify-between py-1.5 border-b border-slate-200">
                                <span class="text-slate-800 font-medium">Laba Bersih Periode Berjalan</span>
                                <span class="font-mono font-bold text-slate-900">Rp {{ number_format($cumulativeNetIncome, 0, ',', '.') }}</span>
                            </div>
                        </div>
                        <div class="flex justify-between px-2 font-bold text-slate-800 border-t border-slate-300 pt-2">
                            <span>Total Ekuitas / Modal:</span>
                            <span class="font-mono text-base">Rp {{ number_format($totalEquity, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 print:bg-white rounded-xl border-2 border-slate-300 print:border-slate-800 flex justify-between items-center font-bold text-lg">
                        <span class="text-slate-900">TOTAL KEWAJIBAN & EKUITAS:</span>
                        <span class="font-mono text-xl text-slate-900">Rp {{ number_format($totalLiabilitiesAndEquity, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        @endif

        <!-- Bagian Tanda Tangan Cetak -->
        <div class="pt-8 mt-8 border-t border-slate-300">
            <div class="grid grid-cols-2 text-center text-sm font-semibold">
                <div>
                    <p class="text-slate-600">Dibuat Oleh,</p>
                    <div class="h-16"></div>
                    <p class="font-bold text-slate-900 border-t border-slate-400 inline-block px-8 pt-1">Bagian Pembukuan / Kasir</p>
                </div>
                <div>
                    <p class="text-slate-600">Disetujui Oleh,</p>
                    <div class="h-16"></div>
                    <p class="font-bold text-slate-900 border-t border-slate-400 inline-block px-8 pt-1">Pemilik Usaha Halala Food</p>
                </div>
            </div>
            <div class="text-right text-xs text-slate-400 mt-6 font-mono">
                Dicetak pada: {{ Carbon\Carbon::now()->translatedFormat('d F Y H:i') }}
            </div>
        </div>

    </div>

    <!-- Script Export ke PDF via html2pdf.js -->
    <script>
        function exportToPdf(btn) {
            const reportEl = document.getElementById('printable-report-card');
            if (!reportEl) return;

            const originalContent = btn ? btn.innerHTML : null;
            if (btn) {
                btn.disabled = true;
                btn.classList.add('opacity-75', 'cursor-not-allowed');
                btn.innerHTML = `
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Mengunduh PDF...</span>
                `;
            }

            const restoreBtn = () => {
                if (btn && originalContent) {
                    btn.disabled = false;
                    btn.classList.remove('opacity-75', 'cursor-not-allowed');
                    btn.innerHTML = originalContent;
                }
            };

            const runExport = () => {
                const isLabaRugi = @json($activeTab === 'income_statement');
                const reportName = isLabaRugi ? 'Laporan-Laba-Rugi-HalalaFood' : 'Neraca-Keuangan-HalalaFood';
                const dateStr = new Date().toISOString().slice(0, 10);

                const opt = {
                    margin:       [10, 12, 10, 12],
                    filename:     `${reportName}-${dateStr}.pdf`,
                    image:        { type: 'jpeg', quality: 0.98 },
                    html2canvas:  {
                        scale: 2,
                        useCORS: true,
                        letterRendering: true,
                        backgroundColor: '#ffffff',
                        onclone: function(clonedDoc) {
                            const oldStyles = clonedDoc.querySelectorAll('link[rel="stylesheet"], style');
                            oldStyles.forEach(s => s.remove());

                            const cleanStyle = clonedDoc.createElement('style');
                            cleanStyle.id = 'pdf-clean-style';
                            cleanStyle.innerHTML = `
                                * { box-sizing: border-box; margin: 0; padding: 0; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; }
                                body { background-color: #ffffff; color: #0f172a; padding: 10px; }
                                #printable-report-card { background-color: #ffffff; color: #0f172a; width: 100%; }
                                .text-center { text-align: center; }
                                .pb-4 { padding-bottom: 16px; }
                                .pt-8 { padding-top: 32px; }
                                .mt-8 { margin-top: 32px; }
                                .mt-6 { margin-top: 24px; }
                                .mt-2 { margin-top: 8px; }
                                .mt-1 { margin-top: 4px; }
                                .mt-0\\.5, .mt-0\.5 { margin-top: 2px; }
                                .mb-2 { margin-bottom: 8px; }
                                .border-b-2 { border-bottom: 2px solid #0f172a; }
                                .border-b { border-bottom: 1px solid #e2e8f0; }
                                .border-t-2 { border-top: 2px solid #0f172a; }
                                .border-t { border-top: 1px solid #cbd5e1; }
                                .border-2 { border: 2px solid #cbd5e1; }
                                .border { border: 1px solid #e2e8f0; }
                                .border-slate-800 { border-color: #1e293b; }
                                .border-slate-900 { border-color: #0f172a; }
                                .border-slate-400 { border-color: #94a3b8; }
                                .border-slate-300 { border-color: #cbd5e1; }
                                .border-slate-200 { border-color: #e2e8f0; }
                                .text-2xl, .text-3xl { font-size: 20px; font-weight: 800; }
                                .text-lg, .text-xl { font-size: 14px; font-weight: 700; }
                                .text-base { font-size: 12px; }
                                .text-sm { font-size: 11px; }
                                .text-xs { font-size: 10px; }
                                .font-extrabold { font-weight: 800; }
                                .font-bold { font-weight: 700; }
                                .font-semibold { font-weight: 600; }
                                .font-medium { font-weight: 500; }
                                .font-mono { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; }
                                .tracking-tight { letter-spacing: -0.025em; }
                                .tracking-wider { letter-spacing: 0.05em; }
                                .tracking-widest { letter-spacing: 0.1em; }
                                .uppercase { text-transform: uppercase; }
                                .space-y-6 > * + * { margin-top: 18px; }
                                .space-y-4 > * + * { margin-top: 12px; }
                                .space-y-2 > * + * { margin-top: 8px; }
                                .space-y-1\\.5 > * + *, .space-y-1\.5 > * + * { margin-top: 6px; }
                                .flex { display: flex; }
                                .justify-between { justify-content: space-between; }
                                .items-center { align-items: center; }
                                .grid { display: grid; }
                                .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
                                .gap-8 { gap: 24px; }
                                .p-2 { padding: 8px; }
                                .p-2\\.5, .p-2\.5 { padding: 10px; }
                                .p-4 { padding: 12px; }
                                .p-5 { padding: 14px; }
                                .p-6, .p-8 { padding: 16px; }
                                .px-2 { padding-left: 8px; padding-right: 8px; }
                                .px-3 { padding-left: 12px; padding-right: 12px; }
                                .px-8 { padding-left: 32px; padding-right: 32px; }
                                .py-1\\.5, .py-1\.5 { padding-top: 5px; padding-bottom: 5px; }
                                .pt-1 { padding-top: 4px; }
                                .pt-2 { padding-top: 8px; }
                                .pt-2\\.5, .pt-2\.5 { padding-top: 10px; }
                                .rounded-lg { border-radius: 8px; }
                                .rounded-xl { border-radius: 10px; }
                                .rounded-2xl { border-radius: 12px; }
                                .bg-white { background-color: #ffffff; }
                                .bg-slate-50 { background-color: #f8fafc; }
                                .bg-slate-100 { background-color: #f1f5f9; }
                                .bg-slate-900 { background-color: #0f172a; color: #ffffff !important; }
                                .text-white { color: #ffffff !important; }
                                .text-slate-900 { color: #0f172a; }
                                .text-slate-800 { color: #1e293b; }
                                .text-slate-700 { color: #334155; }
                                .text-slate-600 { color: #475569; }
                                .text-slate-500 { color: #64748b; }
                                .text-slate-400 { color: #94a3b8; }
                                .text-red-700 { color: #b91c1c; }
                                .inline-block { display: inline-block; }
                                .h-16 { height: 50px; }
                                .text-right { text-align: right; }
                                .print-only { display: block !important; }
                            `;
                            clonedDoc.head.appendChild(cleanStyle);
                        }
                    },
                    jsPDF:        { unit: 'mm', format: 'a4', orientation: 'portrait' }
                };

                window.html2pdf().set(opt).from(reportEl).save().then(() => {
                    restoreBtn();
                }).catch((err) => {
                    console.error('PDF export error:', err);
                    restoreBtn();
                    alert('Gagal mengunduh file PDF secara langsung. Silakan gunakan tombol Cetak / Print.');
                });
            };

            if (typeof window.html2pdf !== 'undefined') {
                runExport();
            } else {
                const script = document.createElement('script');
                script.src = "{{ asset('js/html2pdf.bundle.min.js') }}";
                script.onload = () => runExport();
                script.onerror = () => {
                    restoreBtn();
                    alert('Modul PDF tidak dapat dimuat. Silakan gunakan tombol Cetak / Print.');
                };
                document.head.appendChild(script);
            }
        }
    </script>
</body>
</html>
