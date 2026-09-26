<div class="space-y-8">
    <!-- Header Page & Filter Periode -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Laporan Laba Rugi & Kinerja Toko
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Perhitungan laba bersih murni usaha dan evaluasi omset penjualan per toko mitra.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase block">Dari Tanggal</label>
                <input type="date" wire:model.live="startDate" class="input input-sm input-bordered font-mono font-bold rounded-lg text-slate-900" />
            </div>
            <div>
                <label class="text-xs font-bold text-slate-500 uppercase block">Sampai Tanggal</label>
                <input type="date" wire:model.live="endDate" class="input input-sm input-bordered font-mono font-bold rounded-lg text-slate-900" />
            </div>
        </div>
    </div>

    <!-- Ringkasan Laba Bersih (Besar & Jelas) -->
    <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-6">
        <h2 class="text-xl font-bold text-slate-900 pb-3 border-b border-slate-200">
            Ringkasan Keuangan Usaha Periode Ini
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Total Pemasukan -->
            <div class="p-5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">1. Total Pemasukan Usaha</p>
                <p class="text-2xl font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500">Hasil tagihan toko & penjualan</p>
            </div>

            <!-- Total Pengeluaran -->
            <div class="p-5 bg-slate-50 rounded-xl border border-slate-200 space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-500">2. Biaya Operasional & Bahan</p>
                <p class="text-2xl font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500">Beli bahan, kemasan, bensin, dll.</p>
            </div>

            <!-- Laba Bersih Usaha -->
            <div class="p-5 bg-slate-900 text-white rounded-xl space-y-1">
                <p class="text-xs font-bold uppercase tracking-wider text-slate-400">3. LABA BERSIH MURNI USAHA</p>
                <p class="text-3xl font-extrabold font-mono text-white">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400">Pemasukan dikurangi biaya usaha</p>
            </div>
        </div>

        <!-- Info Tambahan Prive Pribadi -->
        <div class="p-4 bg-slate-100 rounded-xl border border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between text-sm gap-2">
            <span class="text-slate-700 font-medium">
                Total Penarikan Uang untuk Kebutuhan Keluarga (Prive):
            </span>
            <span class="font-mono font-bold text-base text-slate-900">
                Rp {{ number_format($totalPrive, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Grid 2 Kolom: Laporan per Produk & Laporan per Toko -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Rincian Penjualan per Produk -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-4">
            <h2 class="text-lg font-bold text-slate-900 pb-2 border-b border-slate-200">
                Penjualan Berdasarkan Jenis Makanan
            </h2>

            @if($productSales->count() > 0)
                <div class="space-y-3">
                    @foreach($productSales as $ps)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-base text-slate-900">{{ $ps['name'] }}</p>
                                <p class="text-xs text-slate-500 font-medium">Terjual: {{ $ps['qty_sold'] }} {{ $ps['unit'] }}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-mono font-bold text-lg text-slate-900">Rp {{ number_format($ps['total_rupiah'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-500 py-6 text-center">Belum ada data penjualan pada periode ini.</p>
            @endif
        </div>

        <!-- Kinerja Penjualan per Toko Mitra -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-4">
            <h2 class="text-lg font-bold text-slate-900 pb-2 border-b border-slate-200">
                Omset Penjualan per Toko Mitra
            </h2>

            @if($storePerformances->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @foreach($storePerformances as $sp)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                            <div>
                                <p class="font-bold text-base text-slate-900">{{ $sp['name'] }}</p>
                                <p class="text-xs text-slate-500">{{ $sp['route'] ?: 'Tanpa Rute' }} • {{ $sp['consignments_count'] }} kali transaksi</p>
                            </div>
                            <div class="text-right">
                                <p class="font-mono font-bold text-lg text-slate-900">Rp {{ number_format($sp['total_sold'], 0, ',', '.') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-500 py-6 text-center">Belum ada data transaksi toko pada periode ini.</p>
            @endif
        </div>
    </div>
</div>
