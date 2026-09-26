<div class="space-y-6">
    <!-- Clean Header & Top Period Selector (Unified 1-Level Header) -->
    <div class="flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900">
                Laporan & Trend Penjualan
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Analisis performa omset konsinyasi, pergerakan volume produk, dan ringkasan laba bersih usaha.
            </p>
        </div>

        <!-- Apple-style Segmented Period Switcher -->
        <div
            class="inline-flex items-center gap-1 p-1 bg-slate-200/80 rounded-2xl border border-slate-300/60 shadow-xs self-start xl:self-auto overflow-x-auto max-w-full">
            <button type="button" wire:click="setPeriodPreset('7d')"
                class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 whitespace-nowrap cursor-pointer {{ $periodPreset === '7d' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                7 Hari
            </button>
            <button type="button" wire:click="setPeriodPreset('30d')"
                class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 whitespace-nowrap cursor-pointer {{ $periodPreset === '30d' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                30 Hari
            </button>
            <button type="button" wire:click="setPeriodPreset('this_month')"
                class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 whitespace-nowrap cursor-pointer {{ $periodPreset === 'this_month' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Bulan Ini
            </button>
            <button type="button" wire:click="setPeriodPreset('last_month')"
                class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 whitespace-nowrap cursor-pointer {{ $periodPreset === 'last_month' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Bulan Lalu
            </button>
            <button type="button" wire:click="setPeriodPreset('this_year')"
                class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 whitespace-nowrap cursor-pointer {{ $periodPreset === 'this_year' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                Tahun Ini
            </button>

            <!-- Custom Flatpickr Button -->
            <div class="relative" wire:ignore x-data="{
                fp: null,
                init() {
                    this.fp = flatpickr(this.$refs.customPicker, {
                        mode: 'range',
                        dateFormat: 'Y-m-d',
                        positionElement: this.$refs.calendarBtn,
                        defaultDate: @js($dateRange ? explode(' - ', str_replace(' to ', ' - ', $dateRange)) : null),
                        onClose: (selectedDates) => {
                            if (selectedDates && selectedDates.length > 0) {
                                const pad = (n) => String(n).padStart(2, '0');
                                const fmt = (d) => `${d.getFullYear()}-${pad(d.getMonth() + 1)}-${pad(d.getDate())}`;
                                const start = fmt(selectedDates[0]);
                                const end = selectedDates[1] ? fmt(selectedDates[1]) : start;
                                $wire.setCustomRange(`${start} - ${end}`);
                            }
                        }
                    });
                }
            }">
                <button x-ref="calendarBtn" type="button" @click="fp.open()"
                    class="px-3.5 py-1.5 text-xs sm:text-sm font-semibold rounded-xl transition-all duration-150 flex items-center gap-1.5 whitespace-nowrap cursor-pointer {{ $periodPreset === 'custom' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                    <x-icon name="calendar" class="text-sm" />
                    <span>Kalender</span>
                </button>
                <input x-ref="customPicker" type="text" class="sr-only" />
            </div>
        </div>
    </div>

    <!-- Unified Apple Control Toolbar (All in 1 Clean Card) -->
    <div class="bg-white p-4 sm:p-5 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
        <!-- Top Toolbar Row: Metric Tabs & Time Granularity -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pb-3 border-b border-slate-100">
            <!-- Metric Pills -->
            <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200/70 self-start md:self-auto">
                <button type="button" wire:click="setMetric('revenue')"
                    class="px-3 py-1.5 text-xs sm:text-sm font-bold rounded-lg transition-all cursor-pointer {{ $metric === 'revenue' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Omset Penjualan (Rp)
                </button>
                <button type="button" wire:click="setMetric('quantity')"
                    class="px-3 py-1.5 text-xs sm:text-sm font-bold rounded-lg transition-all cursor-pointer {{ $metric === 'quantity' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Volume Terjual (Pcs)
                </button>
                <button type="button" wire:click="setMetric('profit')"
                    class="px-3 py-1.5 text-xs sm:text-sm font-bold rounded-lg transition-all cursor-pointer {{ $metric === 'profit' ? 'bg-slate-900 text-white shadow-xs' : 'text-slate-600 hover:text-slate-900' }}">
                    Laba Kotor (Rp)
                </button>
            </div>

            <!-- Granularity Pills -->
            <div class="flex items-center gap-2 self-start md:self-auto">
                <span class="text-xs font-semibold text-slate-400">Grup:</span>
                <div class="inline-flex p-1 bg-slate-100 rounded-xl border border-slate-200/70">
                    <button type="button" wire:click="setGroupBy('daily')"
                        class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $groupBy === 'daily' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        Harian
                    </button>
                    <button type="button" wire:click="setGroupBy('weekly')"
                        class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $groupBy === 'weekly' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        Mingguan
                    </button>
                    <button type="button" wire:click="setGroupBy('monthly')"
                        class="px-2.5 py-1 text-xs font-bold rounded-lg transition-all cursor-pointer {{ $groupBy === 'monthly' ? 'bg-white text-slate-900 shadow-xs' : 'text-slate-500 hover:text-slate-900' }}">
                        Bulanan
                    </button>
                </div>
            </div>
        </div>

        <!-- Bottom Toolbar Row: Filter Dropdowns -->
        <div
            class="grid grid-cols-1 sm:grid-cols-3 {{ $selectedProductId !== 'all' || $selectedRoute !== 'all' || $selectedStoreId !== 'all' || $metric !== 'revenue' || $periodPreset !== '30d' ? 'lg:grid-cols-4' : 'lg:grid-cols-3' }} gap-3 items-end">
            <!-- Filter Produk -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                    Produk
                </label>
                <select wire:model.live="selectedProductId"
                    class="select select-bordered select-sm w-full rounded-xl bg-slate-50 border-slate-200 font-medium text-slate-800 focus:border-slate-900 focus:bg-white">
                    <option value="all">Semua Produk Jadi</option>
                    @foreach ($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Rute Toko -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                    Rute Pengantaran
                </label>
                <select wire:model.live="selectedRoute"
                    class="select select-bordered select-sm w-full rounded-xl bg-slate-50 border-slate-200 font-medium text-slate-800 focus:border-slate-900 focus:bg-white">
                    <option value="all">Semua Rute</option>
                    @foreach ($routes as $route)
                        <option value="{{ $route }}">{{ $route }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Spesifik Toko -->
            <div>
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">
                    Toko Mitra
                </label>
                <select wire:model.live="selectedStoreId"
                    class="select select-bordered select-sm w-full rounded-xl bg-slate-50 border-slate-200 font-medium text-slate-800 focus:border-slate-900 focus:bg-white">
                    <option value="all">Semua Toko Mitra</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Reset Filters (Only appears when active) -->
            @if (
                $selectedProductId !== 'all' ||
                    $selectedRoute !== 'all' ||
                    $selectedStoreId !== 'all' ||
                    $metric !== 'revenue' ||
                    $periodPreset !== '30d')
                <div>
                    <button type="button" wire:click="resetFilters"
                        class="btn btn-ghost btn-sm text-xs font-bold text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-xl w-full flex items-center justify-center gap-1.5 border border-slate-200">
                        <x-icon name="rotate-ccw" class="text-sm" />
                        <span>Reset Filter</span>
                    </button>
                </div>
            @endif
        </div>
    </div>

    <!-- Apple-Style Interactive Chart Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-xs overflow-hidden transition-all duration-200"
        x-data="appleSalesChart({
            labels: @js($chartLabels),
            values: @js($chartValues),
            tooltips: @js($chartTooltips),
            metric: @js($metric)
        })" @update-sales-chart.window="updateData($event.detail?.[0] || $event.detail)">
        <!-- Chart Header with Clean Stat Summary Strip -->
        <div class="p-6 sm:p-8 border-b border-slate-100 bg-linear-to-b from-slate-50/50 to-white">
            <div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6">
                <!-- Main KPI Stat -->
                <div>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 font-mono">
                        @if ($metric === 'revenue' || $metric === 'profit')
                            Rp {{ number_format($chartSummary['total'], 0, ',', '.') }}
                        @else
                            {{ number_format($chartSummary['total'], 0, ',', '.') }} <span
                                class="text-xl font-sans font-medium text-slate-500">pcs/toples</span>
                        @endif
                    </h2>
                    <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">
                        @if ($metric === 'revenue')
                            Total Omset Penjualan Bersih
                        @elseif($metric === 'quantity')
                            Total Volume Produk Terjual
                        @else
                            Estimasi Laba Kotor Usaha
                        @endif
                        <span class="text-slate-400 font-normal">• {{ $periodLabel }}</span>
                    </p>
                </div>

                <!-- 3 Apple-style Mini KPI Badges -->
                <div class="grid grid-cols-3 gap-4 sm:gap-6 border-t lg:border-t-0 pt-4 lg:pt-0 border-slate-100">
                    <!-- Rata-rata -->
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Rata-rata</p>
                        <p class="text-base sm:text-lg font-bold text-slate-900 font-mono">
                            @if ($metric === 'revenue' || $metric === 'profit')
                                Rp {{ number_format($chartSummary['average'], 0, ',', '.') }}
                            @else
                                {{ number_format($chartSummary['average'], 1, ',', '.') }}
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-400">per titik aktif</p>
                    </div>

                    <!-- Puncak Penjualan -->
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Puncak Tertinggi</p>
                        <p class="text-base sm:text-lg font-bold text-slate-900 font-mono">
                            @if ($metric === 'revenue' || $metric === 'profit')
                                Rp {{ number_format($chartSummary['peak'], 0, ',', '.') }}
                            @else
                                {{ number_format($chartSummary['peak'], 0, ',', '.') }}
                            @endif
                        </p>
                        <p class="text-[11px] text-slate-500 truncate max-w-32.5"
                            title="{{ $chartSummary['peakLabel'] }}">
                            {{ $chartSummary['peakLabel'] }}
                        </p>
                    </div>

                    <!-- Total Transaksi Selesai -->
                    <div class="space-y-0.5">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Faktur</p>
                        <p class="text-base sm:text-lg font-bold text-slate-900 font-mono">
                            {{ $chartSummary['transactions'] }}
                        </p>
                        <p class="text-[11px] text-slate-400">faktur selesai</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Canvas Container -->
        <div class="p-4 sm:p-8 pt-6">
            <div wire:ignore class="relative w-full h-80 sm:h-96">
                <canvas x-ref="canvas"></canvas>
            </div>

            <!-- Minimalist Chart Footer Legend / Guide -->
            <div
                class="mt-4 pt-4 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-2">
                <div class="flex items-center gap-4">
                    <div class="flex items-center gap-1.5">
                        <span class="w-3 h-0.5 bg-slate-900 rounded-full inline-block"></span>
                        <span class="font-medium text-slate-600">
                            @if ($metric === 'revenue')
                                Omset Penjualan Bersih
                            @elseif($metric === 'quantity')
                                Jumlah Produk Terjual
                            @else
                                Estimasi Laba Kotor Usaha
                            @endif
                        </span>
                    </div>
                    <div class="flex items-center gap-1.5">
                        <span class="w-2.5 h-2.5 rounded-sm bg-slate-200 inline-block"></span>
                        <span>Area Trend</span>
                    </div>
                </div>
                <div>
                    Arahkan kursor / sentuh grafik untuk melihat rincian tanggal.
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Performance Summary (Pemasukan, Biaya, Laba Bersih) -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-xs space-y-6">
        <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
            <div>
                <h2 class="text-xl font-bold text-slate-900">
                    Ringkasan Keuangan Periode Ini
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">
                    Hasil kalkulasi seluruh pemasukan titip jual, pemasukan kas lain, serta beban operasional usaha.
                </p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Total Pemasukan -->
            <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">1. Total Pemasukan</p>
                    <x-icon name="arrow-up-right" class="text-emerald-600 text-base" />
                </div>
                <p class="text-2xl font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($totalIncome, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500">Omset toko (Rp {{ number_format($totalSales, 0, ',', '.') }}) + kas
                    lain</p>
            </div>

            <!-- Total Pengeluaran Usaha -->
            <div class="p-5 bg-slate-50/80 rounded-2xl border border-slate-200/80 space-y-1">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-500">2. Biaya Usaha & Bahan</p>
                    <x-icon name="arrow-down-right" class="text-rose-600 text-base" />
                </div>
                <p class="text-2xl font-extrabold text-slate-900 font-mono">
                    Rp {{ number_format($totalExpenses, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-500">Beli bahan, kemasan, bensin, dll.</p>
            </div>

            <!-- Laba Bersih Murni Usaha -->
            <div class="p-5 bg-slate-900 text-white rounded-2xl space-y-1 shadow-sm">
                <div class="flex items-center justify-between">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">3. LABA BERSIH MURNI</p>
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                </div>
                <p class="text-3xl font-extrabold font-mono text-white">
                    Rp {{ number_format($netProfit, 0, ',', '.') }}
                </p>
                <p class="text-xs text-slate-400">Pemasukan dikurangi seluruh biaya usaha</p>
            </div>
        </div>

        <!-- Info Tambahan Prive Pribadi -->
        <div
            class="p-4 bg-slate-100/80 rounded-2xl border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between text-sm gap-2">
            <div class="flex items-center gap-2">
                <x-icon name="user" class="text-slate-500 text-base" />
                <span class="text-slate-700 font-medium">
                    Total Pengambilan Dana Usaha untuk Keluarga (Prive):
                </span>
            </div>
            <span class="font-mono font-bold text-base text-slate-900">
                Rp {{ number_format($totalPrive, 0, ',', '.') }}
            </span>
        </div>
    </div>

    <!-- Grid 2 Kolom: Penjualan per Produk & Kinerja Toko Mitra -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Rincian Penjualan per Produk -->
        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Penjualan Berdasarkan Produk
                    </h3>
                    <p class="text-xs text-slate-500">Volume laku, omset, dan estimasi margin</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                    {{ $productSales->count() }} Produk
                </span>
            </div>

            @if ($productSales->count() > 0)
                <div class="space-y-3">
                    @foreach ($productSales as $ps)
                        <div
                            class="p-4 bg-slate-50/80 hover:bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between transition-colors">
                            <div>
                                <p class="font-bold text-base text-slate-900">{{ $ps['name'] }}</p>
                                <div class="flex items-center gap-2 text-xs text-slate-500 font-medium mt-0.5">
                                    <span>Terjual: <strong class="text-slate-800">{{ $ps['qty_sold'] }}
                                            {{ $ps['unit'] }}</strong></span>
                                    <span>•</span>
                                    <span>Est. Margin: <strong class="text-emerald-700">Rp
                                            {{ number_format($ps['gross_margin'], 0, ',', '.') }}</strong></span>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="font-mono font-bold text-lg text-slate-900">Rp
                                    {{ number_format($ps['total_rupiah'], 0, ',', '.') }}</p>
                                <span class="text-[11px] text-slate-400">Total Omset</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-slate-400">
                    <x-icon name="package-off" class="text-4xl mx-auto mb-2 opacity-40" />
                    <p class="text-sm font-semibold text-slate-600">Belum ada data penjualan produk</p>
                    <p class="text-xs text-slate-400 mt-0.5">Coba sesuaikan pilihan filter tanggal di bagian atas.</p>
                </div>
            @endif
        </div>

        <!-- Kinerja Penjualan per Toko Mitra -->
        <div class="bg-white p-6 sm:p-7 rounded-3xl border border-slate-200/80 shadow-xs space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200/80">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">
                        Kinerja Omset per Toko Mitra
                    </h3>
                    <p class="text-xs text-slate-500">Peringkat kontribusi toko terhadap penjualan</p>
                </div>
                <span class="text-xs font-bold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700">
                    {{ $storePerformances->count() }} Toko
                </span>
            </div>

            @if ($storePerformances->count() > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                    @foreach ($storePerformances as $sp)
                        <div
                            class="p-4 bg-slate-50/80 hover:bg-slate-50 rounded-2xl border border-slate-200/80 flex items-center justify-between transition-colors">
                            <div>
                                <p class="font-bold text-base text-slate-900">{{ $sp['name'] }}</p>
                                <p class="text-xs text-slate-500 mt-0.5">
                                    <span
                                        class="inline-flex items-center px-2 py-0.5 rounded-md bg-slate-200/80 text-slate-700 font-semibold text-[11px]">
                                        {{ $sp['route'] ?: 'Tanpa Rute' }}
                                    </span>
                                    <span class="ml-1.5">• {{ $sp['consignments_count'] }} kali penagihan</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="font-mono font-bold text-lg text-slate-900">Rp
                                    {{ number_format($sp['total_sold'], 0, ',', '.') }}</p>
                                <span class="text-[11px] text-slate-400">Total Setoran</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center text-slate-400">
                    <x-icon name="building-store" class="text-4xl mx-auto mb-2 opacity-40" />
                    <p class="text-sm font-semibold text-slate-600">Belum ada transaksi toko</p>
                    <p class="text-xs text-slate-400 mt-0.5">Coba sesuaikan pilihan filter tanggal di bagian atas.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('appleSalesChart', (initialConfig) => ({
            chart: null,
            metric: initialConfig ? initialConfig.metric : 'revenue',

            init() {
                this.$nextTick(() => {
                    if (initialConfig) {
                        this.createChart(initialConfig.labels || [], initialConfig.values ||
                            [], initialConfig.tooltips || [], this.metric);
                    }
                });
            },

            updateData(detail) {
                if (!detail) return;
                const labels = detail.labels || [];
                const values = detail.values || [];
                const tooltips = detail.tooltips || [];
                this.metric = detail.metric || this.metric;

                const canvas = this.$refs.canvas;
                const activeChart = (canvas && typeof Chart !== 'undefined') ? Chart.getChart(
                    canvas) : this.chart;

                if (activeChart) {
                    this.chart = activeChart;
                    this.chart.data.labels = labels;
                    this.chart.data.datasets[0].data = values;
                    this.chart.data.datasets[0].tooltipsMeta = tooltips;
                    this.chart.data.datasets[0].metricType = this.metric;
                    this.chart.data.datasets[0].label = this.metric === 'revenue' ? 'Omset' : (this
                        .metric === 'quantity' ? 'Volume Terjual' : 'Laba Kotor');
                    this.chart.update('active');
                    return;
                }

                this.createChart(labels, values, tooltips, this.metric);
            },

            formatCurrency(num) {
                return 'Rp ' + Number(num).toLocaleString('id-ID');
            },

            createChart(labels, values, tooltips, metric) {
                const canvas = this.$refs.canvas;
                if (!canvas || typeof Chart === 'undefined') return;

                const existingChart = Chart.getChart(canvas);
                if (existingChart) {
                    existingChart.destroy();
                }
                if (this.chart) {
                    this.chart.destroy();
                    this.chart = null;
                }

                const ctx = canvas.getContext('2d');
                const gradient = ctx.createLinearGradient(0, 0, 0, 320);
                gradient.addColorStop(0, 'rgba(15, 23, 42, 0.16)');
                gradient.addColorStop(0.7, 'rgba(15, 23, 42, 0.03)');
                gradient.addColorStop(1, 'rgba(15, 23, 42, 0.00)');

                const self = this;
                this.chart = new Chart(canvas, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: metric === 'revenue' ? 'Omset' : (metric ===
                                'quantity' ? 'Volume Terjual' : 'Laba Kotor'),
                            data: values,
                            tooltipsMeta: tooltips,
                            metricType: metric,
                            borderColor: '#0f172a',
                            borderWidth: 2.5,
                            backgroundColor: gradient,
                            fill: true,
                            tension: 0.35,
                            cubicInterpolationMode: 'monotone',
                            pointRadius: function(context) {
                                return (context.raw && context.raw > 0) ? 3.5 :
                                    0;
                            },
                            pointHoverRadius: 6.5,
                            pointBackgroundColor: '#0f172a',
                            pointBorderColor: '#ffffff',
                            pointBorderWidth: 2.5,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 400,
                            easing: 'easeOutQuart'
                        },
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                enabled: true,
                                backgroundColor: '#ffffff',
                                titleColor: '#64748b',
                                titleFont: {
                                    size: 12,
                                    weight: '600',
                                    family: 'ui-sans-serif, system-ui, -apple-system, sans-serif'
                                },
                                bodyColor: '#0f172a',
                                bodyFont: {
                                    size: 15,
                                    weight: '800',
                                    family: 'ui-monospace, SFMono-Regular, monospace'
                                },
                                borderColor: '#e2e8f0',
                                borderWidth: 1,
                                padding: {
                                    top: 10,
                                    bottom: 10,
                                    left: 14,
                                    right: 14
                                },
                                cornerRadius: 12,
                                boxPadding: 4,
                                usePointStyle: true,
                                callbacks: {
                                    title: function(context) {
                                        const index = context[0].dataIndex;
                                        const meta = context[0].dataset.tooltipsMeta;
                                        return meta && meta[index] ? meta[index] :
                                            context[0].label;
                                    },
                                    label: function(context) {
                                        const val = context.parsed.y;
                                        const mType = context.dataset.metricType;
                                        if (mType === 'quantity') {
                                            return ' ' + Number(val).toLocaleString(
                                                'id-ID') + ' pcs / toples';
                                        }
                                        return ' ' + self.formatCurrency(val);
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false,
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: '#94a3b8',
                                    font: {
                                        size: 11,
                                        weight: '500',
                                        family: 'ui-sans-serif, system-ui, -apple-system, sans-serif'
                                    },
                                    maxRotation: 0,
                                    autoSkip: true,
                                    maxTicksLimit: 10,
                                    padding: 8
                                },
                                border: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: '#f1f5f9',
                                    borderDash: [4, 4],
                                    drawBorder: false,
                                },
                                ticks: {
                                    color: '#94a3b8',
                                    font: {
                                        size: 11,
                                        weight: '500',
                                        family: 'ui-monospace, SFMono-Regular, monospace'
                                    },
                                    padding: 10,
                                    callback: function(value) {
                                        if (self.metric === 'quantity') {
                                            return Number(value).toLocaleString(
                                            'id-ID');
                                        }
                                        if (value >= 1000000) {
                                            return 'Rp ' + (value / 1000000).toFixed(1)
                                                .replace('.0', '') + 'jt';
                                        }
                                        if (value >= 1000) {
                                            return 'Rp ' + (value / 1000).toFixed(0) +
                                                'rb';
                                        }
                                        return 'Rp ' + value;
                                    }
                                },
                                border: {
                                    display: false
                                }
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
