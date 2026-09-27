<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200/80">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Barcode & Label Toko
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Kelola kode barcode dari toko mitra & buat lembaran stiker A4 siap cetak / simpan ke flashdisk untuk
                percetakan.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="openCreateModal"
                class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm cursor-pointer">
                <x-icon name="plus" class="text-xl" />
                <span>Tambah Barcode Toko</span>
            </button>
        </div>
    </div>

    <!-- Apple-style Segmented Tab Switcher -->
    <div class="flex items-center justify-between flex-wrap gap-4">
        <div class="inline-flex p-1 bg-slate-200/80 rounded-2xl border border-slate-300/60 shadow-xs">
            <button type="button" wire:click="$set('activeTab', 'list')"
                class="px-5 py-2 rounded-xl text-sm font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'list' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <x-icon name="tags" class="text-lg" />
                <span>Daftar Barcode Toko</span>
                <span
                    class="px-2 py-0.5 rounded-full text-xs font-mono {{ $activeTab === 'list' ? 'bg-slate-100 text-slate-800' : 'bg-slate-300/60 text-slate-700' }}">
                    {{ $stats['total_barcodes'] }}
                </span>
            </button>
            <button type="button" wire:click="$set('activeTab', 'print')"
                class="px-5 py-2 rounded-xl text-sm font-bold transition-all cursor-pointer flex items-center gap-2 {{ $activeTab === 'print' ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                <x-icon name="printer" class="text-lg" />
                <span>Studio Cetak & Export Flashdisk</span>
            </button>
        </div>

        @if ($activeTab === 'list')
            <!-- Quick Summary Stats -->
            <div class="flex items-center gap-4 text-xs sm:text-sm font-semibold text-slate-600">
                <span
                    class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <strong>{{ $stats['total_stores'] }}</strong> Toko Terdaftar
                </span>
                <span
                    class="inline-flex items-center gap-1.5 bg-white px-3 py-1.5 rounded-xl border border-slate-200 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                    <strong>{{ $stats['total_products'] }}</strong> Produk Tercover
                </span>
            </div>
        @endif
    </div>

    <!-- TAB 1: MANAJEMEN BARCODE TOKO -->
    @if ($activeTab === 'list')
        <div class="space-y-6">
            <!-- Filter & Pencarian Barcode -->
            <div
                class="p-4 sm:p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="relative flex-1">
                        <x-icon name="search"
                            class="absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg" />
                        <input type="text" wire:model.live.debounce.300ms="search"
                            placeholder="Cari kode barcode, nama produk, toko, atau SKU..."
                            class="input input-md input-bordered w-full pl-10 text-sm sm:text-base rounded-xl focus:border-slate-900" />
                    </div>

                    <div class="w-full sm:w-56">
                        <select wire:model.live="storeFilter"
                            class="select select-md select-bordered w-full text-sm font-medium rounded-xl">
                            <option value="">Semua Toko Mitra</option>
                            @foreach ($stores as $st)
                                <option value="{{ $st->id }}">{{ $st->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="w-full sm:w-56">
                        <select wire:model.live="productFilter"
                            class="select select-md select-bordered w-full text-sm font-medium rounded-xl">
                            <option value="">Semua Produk</option>
                            @foreach ($products as $pr)
                                <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if ($search || $storeFilter || $productFilter)
                    <button wire:click="$set('search', ''); $set('storeFilter', ''); $set('productFilter', '');"
                        class="text-xs font-bold text-rose-600 hover:text-rose-800 underline self-center">
                        Reset Filter
                    </button>
                @endif
            </div>

            <!-- Grid Kartu Barcode -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5">
                @forelse($barcodes as $item)
                    <div
                        class="bg-white rounded-2xl border border-slate-200/80 shadow-xs hover:shadow-md transition p-5 flex flex-col justify-between space-y-4">
                        <!-- Top: Toko & Produk Info -->
                        <div class="space-y-2.5">
                            <div class="flex items-center justify-between gap-2">
                                <span
                                    class="px-2.5 py-1 bg-slate-100 text-slate-800 font-bold text-xs rounded-lg border border-slate-200/80 truncate max-w-[65%]">
                                    {{ $item->store->name }}
                                </span>
                                <span class="text-xs font-mono font-bold text-slate-400 uppercase">
                                    {{ $item->barcode_type ?: 'CODE128' }}
                                </span>
                            </div>

                            <div>
                                <h3 class="font-bold text-lg text-slate-900 leading-snug">
                                    {{ $item->display_name }}
                                </h3>
                                @if ($item->custom_product_name && $item->custom_product_name !== $item->product->name)
                                    <p class="text-xs text-slate-500">
                                        Produk Asli: {{ $item->product->name }}
                                    </p>
                                @endif
                            </div>

                            @if ($item->store_sku || $item->display_price > 0)
                                <div class="flex items-center justify-between text-xs pt-1 border-t border-slate-100">
                                    <span class="text-slate-500">
                                        SKU/PLU: <strong
                                            class="text-slate-800 font-mono">{{ $item->store_sku ?: '-' }}</strong>
                                    </span>
                                    <span class="text-slate-900 font-bold font-mono text-sm">
                                        Rp {{ number_format($item->display_price, 0, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Center: Crisp SVG Barcode Graphic -->
                        <div
                            class="bg-slate-50 border border-slate-200/70 rounded-xl p-3 flex flex-col items-center justify-center min-h-22.5">
                            <div class="w-full max-w-60 h-14 flex items-center justify-center">
                                {!! \App\Services\BarcodeService::getSvg($item->barcode, $item->barcode_type, 38, 1.4, true) !!}
                            </div>
                        </div>

                        @if ($item->notes)
                            <p
                                class="text-xs text-slate-500 bg-amber-50/70 border border-amber-200/60 rounded-lg p-2 leading-relaxed">
                                <strong class="text-amber-800">Catatan:</strong> {{ $item->notes }}
                            </p>
                        @endif

                        <!-- Bottom Actions -->
                        <div class="flex items-center gap-2 pt-2 border-t border-slate-100">
                            <button type="button" wire:click="quickPrintBarcode({{ $item->id }})"
                                class="btn btn-sm bg-slate-900 hover:bg-black text-white font-bold rounded-xl flex-1 gap-1.5 shadow-2xs cursor-pointer">
                                <x-icon name="printer" class="text-base" />
                                <span>Cetak Label Ini</span>
                            </button>

                            <button type="button" wire:click="openEditModal({{ $item->id }})"
                                class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-3 border border-slate-300">
                                Edit
                            </button>

                            <button type="button"
                                wire:click="confirmDelete({{ $item->id }}, '{{ addslashes($item->display_name . ' (' . $item->barcode . ')') }}')"
                                class="btn btn-sm bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 font-bold rounded-xl px-3 border border-slate-300 transition">
                                Hapus
                            </button>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-16 text-center bg-white rounded-2xl border border-slate-200/80 p-8 space-y-4">
                        <div
                            class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto text-3xl">
                            <x-icon name="tags" class="text-3xl" />
                        </div>
                        <div class="max-w-md mx-auto">
                            <h3 class="font-bold text-xl text-slate-900">Belum Ada Barcode Toko</h3>
                            <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                                Jika ada toko mitra (seperti supermarket/minimarket) yang memberikan kode barcode khusus
                                untuk produk Halala Food, daftarkan di sini agar bisa dicetak rapi.
                            </p>
                        </div>
                        <button wire:click="openCreateModal"
                            class="btn btn-md bg-slate-900 text-white font-bold rounded-xl px-6 gap-2">
                            <x-icon name="plus" class="text-lg" />
                            <span>Tambah Barcode Pertama</span>
                        </button>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="pt-2">
                {{ $barcodes->links() }}
            </div>
        </div>
    @endif

    <!-- TAB 2: STUDIO CETAK & EXPORT FLASHDISK -->
    @if ($activeTab === 'print')
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <!-- PANEL KONTROL KIRI (5 Cols) -->
            <div class="lg:col-span-5 space-y-6">
                <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-5 sm:p-6 space-y-5">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Pengaturan Lembar Cetak A4</h2>
                        <p class="text-xs text-slate-500">Konfigurasi layout stiker sebelum dicetak atau disimpan ke
                            flashdisk</p>
                    </div>

                    <!-- 1. Pilihan Mode Cetak -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">1. Mode
                            Cetak</label>
                        <div class="grid grid-cols-3 gap-2">
                            <button type="button" wire:click="$set('printMode', 'single')"
                                class="p-2.5 rounded-xl border text-xs font-bold text-center transition cursor-pointer {{ $printMode === 'single' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                1 Barcode Penuh
                            </button>
                            <button type="button" wire:click="$set('printMode', 'store_all')"
                                class="p-2.5 rounded-xl border text-xs font-bold text-center transition cursor-pointer {{ $printMode === 'store_all' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                Semua di Toko
                            </button>
                            <button type="button" wire:click="$set('printMode', 'batch')"
                                class="p-2.5 rounded-xl border text-xs font-bold text-center transition cursor-pointer {{ $printMode === 'batch' ? 'bg-slate-900 text-white border-slate-900' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                Campuran / Batch
                            </button>
                        </div>
                    </div>

                    <!-- 2. Target Barcode Sesuai Mode -->
                    @if ($printMode === 'single')
                        <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/70">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1">Pilih Barcode Toko &
                                    Produk</label>
                                <select wire:model.live="selectedBarcodeId"
                                    class="select select-bordered select-sm w-full font-medium text-sm">
                                    @foreach ($allBarcodes as $b)
                                        <option value="{{ $b->id }}">
                                            {{ $b->store->name }} — {{ $b->display_name }} ({{ $b->barcode }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1">Jumlah Stiker Dicetak
                                    (Pcs)</label>
                                <div class="flex items-center gap-2">
                                    <input type="number" min="1"
                                        wire:model.live.debounce.250ms="singleCopies"
                                        class="input input-sm input-bordered w-28 font-mono font-bold text-sm" />
                                    <span class="text-xs text-slate-500">
                                        = ± {{ ceil($singleCopies / max(1, $columns * $rows)) }} Lembar A4
                                    </span>
                                </div>
                            </div>
                        </div>
                    @elseif($printMode === 'store_all')
                        <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/70">
                            <div>
                                <label class="block text-xs font-bold text-slate-800 mb-1">Pilih Toko Mitra</label>
                                <select wire:model.live="selectedStoreId"
                                    class="select select-bordered select-sm w-full font-medium text-sm">
                                    @foreach ($stores as $st)
                                        <option value="{{ $st->id }}">{{ $st->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <p class="text-xs text-slate-500">
                                Seluruh produk yang terdaftar pada toko ini akan disusun rata dalam 1 lembar A4.
                            </p>
                        </div>
                    @elseif($printMode === 'batch')
                        <div class="space-y-3 bg-slate-50 p-4 rounded-xl border border-slate-200/70">
                            <label class="block text-xs font-bold text-slate-800">Daftar Produk & Jumlah Stiker</label>
                            <div class="space-y-2 max-h-56 overflow-y-auto pr-1">
                                @foreach ($batchRows as $idx => $row)
                                    <div
                                        class="flex items-center gap-2 bg-white p-2 rounded-lg border border-slate-200">
                                        <select wire:model.live="batchRows.{{ $idx }}.barcode_id"
                                            class="select select-bordered select-xs flex-1 text-xs font-medium">
                                            <option value="">-- Pilih Barcode --</option>
                                            @foreach ($allBarcodes as $b)
                                                <option value="{{ $b->id }}">
                                                    {{ $b->store->name }} - {{ $b->display_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div class="w-18">
                                            <input type="number" min="1"
                                                wire:model.live.debounce.250ms="batchRows.{{ $idx }}.qty"
                                                class="input input-xs input-bordered w-full font-mono text-center font-bold"
                                                placeholder="Qty" />
                                        </div>
                                        <button type="button" wire:click="removeBatchRow({{ $idx }})"
                                            class="btn btn-xs btn-ghost btn-square text-red-500">
                                            &times;
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addBatchRow"
                                class="btn btn-xs bg-white hover:bg-slate-100 text-slate-800 font-bold w-full rounded-lg border border-slate-300">
                                + Tambah Item Barcode
                            </button>
                        </div>
                    @endif

                    <!-- 3. Template Ukuran Kertas Stiker -->
                    <div class="space-y-2">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">2. Template
                            Kertas Stiker Label</label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <button type="button" wire:click="setPaperTemplate('a4_3x8')"
                                class="p-2.5 rounded-xl border text-left font-semibold transition cursor-pointer {{ $paperTemplate === 'a4_3x8' ? 'bg-slate-900 text-white border-slate-900 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                <p class="text-xs">Grid 3 × 8 (24 Label)</p>
                                <p class="text-[10px] opacity-75">Ukuran ~65 × 35 mm</p>
                            </button>
                            <button type="button" wire:click="setPaperTemplate('a4_4x10')"
                                class="p-2.5 rounded-xl border text-left font-semibold transition cursor-pointer {{ $paperTemplate === 'a4_4x10' ? 'bg-slate-900 text-white border-slate-900 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                <p class="text-xs">Grid 4 × 10 (40 Label)</p>
                                <p class="text-[10px] opacity-75">Ukuran ~48 × 28 mm</p>
                            </button>
                            <button type="button" wire:click="setPaperTemplate('a4_2x6')"
                                class="p-2.5 rounded-xl border text-left font-semibold transition cursor-pointer {{ $paperTemplate === 'a4_2x6' ? 'bg-slate-900 text-white border-slate-900 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                <p class="text-xs">Grid 2 × 6 (12 Label)</p>
                                <p class="text-[10px] opacity-75">Ukuran ~95 × 45 mm (Besar)</p>
                            </button>
                            <button type="button" wire:click="setPaperTemplate('tj_108')"
                                class="p-2.5 rounded-xl border text-left font-semibold transition cursor-pointer {{ $paperTemplate === 'tj_108' ? 'bg-slate-900 text-white border-slate-900 font-bold' : 'bg-slate-50 text-slate-700 border-slate-200 hover:bg-slate-100' }}">
                                <p class="text-xs">Tom & Jerry No. 108</p>
                                <p class="text-[10px] opacity-75">Grid 5 × 8 (40 Label)</p>
                            </button>
                        </div>
                    </div>

                    <!-- 4. Elemen Tampilan Stiker -->
                    <div class="space-y-2 pt-2 border-t border-slate-100">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">3. Elemen Pada
                            Setiap Stiker</label>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" wire:model.live="showProductName"
                                    class="checkbox checkbox-xs rounded checkbox-primary" />
                                <span>Nama Produk</span>
                            </label>
                            <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" wire:model.live="showStoreName"
                                    class="checkbox checkbox-xs rounded checkbox-primary" />
                                <span>Nama Toko Mitra</span>
                            </label>
                            <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" wire:model.live="showPrice"
                                    class="checkbox checkbox-xs rounded checkbox-primary" />
                                <span>Harga Jual Toko</span>
                            </label>
                            <label class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer">
                                <input type="checkbox" wire:model.live="showBarcodeText"
                                    class="checkbox checkbox-xs rounded checkbox-primary" />
                                <span>Kode Angka Barcode</span>
                            </label>
                            <label
                                class="flex items-center gap-2 font-medium text-slate-700 cursor-pointer col-span-2">
                                <input type="checkbox" wire:model.live="showCutBorders"
                                    class="checkbox checkbox-xs rounded checkbox-primary" />
                                <span>Garis Bantu Potong / Gunting (Dashed)</span>
                            </label>
                        </div>
                    </div>

                    <!-- 5. Tombol Export Flashdisk & Cetak -->
                    @php
                        $printQuery = [
                            'mode' => $printMode,
                            'barcode_id' => $selectedBarcodeId,
                            'store_id' => $selectedStoreId,
                            'copies' => $singleCopies,
                            'template' => $paperTemplate,
                            'cols' => $columns,
                            'rows' => $rows,
                            'w' => $labelWidthMm,
                            'h' => $labelHeightMm,
                            'mt' => $marginTopMm,
                            'ml' => $marginLeftMm,
                            'gx' => $gapXMm,
                            'gy' => $gapYMm,
                            'show_prod' => $showProductName ? 1 : 0,
                            'show_store' => $showStoreName ? 1 : 0,
                            'show_price' => $showPrice ? 1 : 0,
                            'show_text' => $showBarcodeText ? 1 : 0,
                            'show_border' => $showCutBorders ? 1 : 0,
                            'bh' => $barcodeHeight,
                            'batch' => array_map(function ($r) {
                                return ['id' => $r['barcode_id'], 'qty' => $r['qty']];
                            }, $batchRows),
                        ];
                    @endphp

                    <div class="space-y-2.5 pt-4 border-t border-slate-200">
                        <!-- Primary Actions: Download PDF & Flashdisk HTML (Stacked 2 Baris) -->
                        <div class="space-y-2">
                            <a href="{{ route('barcodes.print', array_merge($printQuery, ['autodownload_pdf' => 1])) }}" target="_blank"
                                class="btn btn-md bg-rose-700 hover:bg-rose-800 text-white font-bold w-full rounded-xl gap-2 shadow-xs cursor-pointer text-base">
                                <x-icon name="file-type-pdf" class="text-xl" />
                                <span>Simpan Dokumen PDF (.pdf)</span>
                            </a>
                            <a href="{{ route('barcodes.export-html', $printQuery) }}"
                                class="btn btn-md bg-emerald-700 hover:bg-emerald-800 text-white font-bold w-full rounded-xl gap-2 shadow-xs cursor-pointer text-base">
                                <x-icon name="device-usb" class="text-xl" />
                                <span>Simpan File ke Flashdisk (.html)</span>
                            </a>
                        </div>

                        <!-- Secondary: Buka Halaman Print A4 & Cetak -->
                        <div class="grid grid-cols-2 gap-2">
                            <a href="{{ route('barcodes.print', $printQuery) }}" target="_blank"
                                class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl border border-slate-300 gap-1.5 cursor-pointer">
                                <x-icon name="external-link" class="text-sm" />
                                <span>Buka Lembar A4</span>
                            </a>
                            <a href="{{ route('barcodes.print', array_merge($printQuery, ['autoprint' => 1])) }}" target="_blank"
                                class="btn btn-sm bg-slate-900 hover:bg-black text-white font-bold rounded-xl gap-1.5 cursor-pointer shadow-xs">
                                <x-icon name="printer" class="text-sm" />
                                <span>Cetak Sekarang</span>
                            </a>
                        </div>
                        <p class="text-[11px] text-slate-500 text-center leading-relaxed">
                            💡 Format <strong>.pdf</strong> atau <strong>.html</strong> siap langsung dibawa ke tempat percetakan dan dicetak pada kertas stiker A4.
                        </p>
                    </div>

                </div>
            </div>

            <!-- PREVIEW KERTAS A4 KANAN (7 Cols) -->
            <div class="lg:col-span-7 space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900">Preview Lembar A4</h2>
                        <p class="text-xs text-slate-500">
                            Total <strong>{{ count($printableLabels) }} Stiker</strong> • Format Grid
                            {{ $columns }} × {{ $rows }} ({{ $columns * $rows }} per lembar)
                        </p>
                    </div>
                    <div class="flex items-center gap-2">
                        @if (count($printableLabels) > $columns * $rows)
                            <span class="badge badge-sm bg-amber-100 text-amber-800 font-bold border-amber-200">
                                {{ ceil(count($printableLabels) / ($columns * $rows)) }} Lembar A4
                            </span>
                        @endif
                        <span
                            class="badge badge-sm bg-white text-slate-700 font-mono font-bold border-slate-200 shadow-2xs">
                            Proporsi A4 Pas
                        </span>
                    </div>
                </div>

                <!-- A4 Sheet Container Preview (No Scroll, Responsive Fit) -->
                <div
                    class="bg-slate-200/90 rounded-3xl p-3 sm:p-5 border border-slate-300/80 flex flex-col items-center justify-center">
                    <div
                        class="w-full max-w-lg bg-white rounded-2xl shadow-xl border border-slate-200/80 p-3 sm:p-4 select-none aspect-210/297 flex flex-col">

                        @php
                            $sheetCapacity = max(1, $columns * $rows);
                            $firstSheetLabels = array_slice($printableLabels, 0, $sheetCapacity);

                            if ($columns >= 5 || $labelHeightMm <= 20) {
                                // 5x8 Tom & Jerry 108 (38x18mm)
                                $pvStoreClass = 'text-[5.5px] sm:text-[6.5px]';
                                $pvTitleClass = 'text-[6.5px] sm:text-[7.5px]';
                                $pvPriceClass = 'text-[6px] sm:text-[7px]';
                                $pvSkuClass = 'text-[5px] sm:text-[6px]';
                                $pvSvgMaxH = 'max-h-3 sm:max-h-4.5';
                            } elseif ($rows >= 10 || $columns >= 4 || $labelHeightMm <= 30) {
                                // 4x10 (48x28mm) / 4x6
                                $pvStoreClass = 'text-[6.5px] sm:text-[7.5px]';
                                $pvTitleClass = 'text-[7.5px] sm:text-[8.5px]';
                                $pvPriceClass = 'text-[7px] sm:text-[8px]';
                                $pvSkuClass = 'text-[5.5px] sm:text-[6.5px]';
                                $pvSvgMaxH = 'max-h-4.5 sm:max-h-6';
                            } else {
                                // 3x8 / 2x6
                                $pvStoreClass = 'text-[7.5px] sm:text-[8.5px]';
                                $pvTitleClass = 'text-[8.5px] sm:text-[9.5px]';
                                $pvPriceClass = 'text-[8px] sm:text-[9px]';
                                $pvSkuClass = 'text-[6.5px] sm:text-[7.5px]';
                                $pvSvgMaxH = 'max-h-6 sm:max-h-8';
                            }
                        @endphp

                        @if (!empty($firstSheetLabels))
                            <div class="w-full h-full grid"
                                style="grid-template-columns: repeat({{ $columns }}, minmax(0, 1fr)); grid-template-rows: repeat({{ $rows }}, minmax(0, 1fr)); gap: 3px;">
                                @foreach ($firstSheetLabels as $idx => $label)
                                    <div
                                        class="w-full h-full bg-white {{ $showCutBorders ? 'border border-dashed border-slate-300' : '' }} rounded-xs p-0.5 sm:p-1 flex flex-col justify-between items-center text-center overflow-hidden">

                                        <!-- Header Stiker -->
                                        <div class="w-full space-y-0 leading-none shrink-0">
                                            @if ($showStoreName)
                                                <p
                                                    class="{{ $pvStoreClass }} uppercase tracking-wider font-bold text-slate-500 truncate leading-tight">
                                                    {{ $label['item']->store->name }}
                                                </p>
                                            @endif
                                            @if ($showProductName)
                                                <p
                                                    class="{{ $pvTitleClass }} font-bold text-slate-900 leading-tight truncate">
                                                    {{ $label['item']->display_name }}
                                                </p>
                                            @endif
                                        </div>

                                        <!-- Barcode Graphic Vector -->
                                        <div
                                            class="w-full flex-1 flex items-center justify-center my-0.5 overflow-hidden min-h-0">
                                            <div
                                                class="w-full h-full flex items-center justify-center {{ $pvSvgMaxH }}">
                                                {!! $label['svg'] !!}
                                            </div>
                                        </div>

                                        <!-- Footer Stiker -->
                                        @if ($showPrice && $label['item']->display_price > 0)
                                            <div
                                                class="w-full flex items-center justify-between {{ $pvPriceClass }} font-mono font-bold text-slate-900 border-t border-slate-100 pt-0.5 leading-none shrink-0">
                                                @if ($label['item']->store_sku)
                                                    <span
                                                        class="{{ $pvSkuClass }} text-slate-500 font-normal truncate max-w-[40%]">
                                                        {{ $label['item']->store_sku }}
                                                    </span>
                                                @else
                                                    <span></span>
                                                @endif
                                                <span class="font-extrabold ml-auto whitespace-nowrap">
                                                    Rp {{ number_format($label['item']->display_price, 0, ',', '.') }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div
                                class="w-full h-full flex flex-col items-center justify-center text-center text-slate-400 p-8 space-y-2">
                                <x-icon name="tags" class="text-4xl opacity-50" />
                                <p class="font-semibold text-sm">Pilih barcode atau tambahkan barcode toko terlebih
                                    dahulu.</p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    @endif

    <!-- MODAL TAMBAH / EDIT BARCODE TOKO -->
    @if ($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">
                        {{ $editingId ? 'Edit Barcode Toko' : 'Tambah Barcode Toko Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)"
                        class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">
                        &times;
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Toko Mitra <span
                                    class="text-red-500">*</span></label>
                            <select wire:model="store_id"
                                class="select select-bordered w-full text-base rounded-xl focus:border-slate-900">
                                <option value="">-- Pilih Toko --</option>
                                @foreach ($stores as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                            @error('store_id')
                                <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Produk Jadi <span
                                    class="text-red-500">*</span></label>
                            <select wire:model.live="product_id"
                                class="select select-bordered w-full text-base rounded-xl focus:border-slate-900">
                                <option value="">-- Pilih Produk --</option>
                                @foreach ($products as $pr)
                                    <option value="{{ $pr->id }}">{{ $pr->name }}</option>
                                @endforeach
                            </select>
                            @error('product_id')
                                <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">
                            Kode Barcode dari Toko <span class="text-red-500">*</span>
                        </label>
                        <input type="text" wire:model.live.debounce.250ms="barcode"
                            placeholder="Contoh: 201948281023 atau 899720194821"
                            class="input input-bordered w-full font-mono font-bold text-base rounded-xl focus:border-slate-900" />
                        @error('barcode')
                            <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span>
                        @enderror

                        <!-- Live Barcode Preview in Modal -->
                        @if (!empty(trim($barcode)))
                            <div
                                class="mt-2.5 p-3 bg-slate-50 border border-slate-200 rounded-xl flex flex-col items-center justify-center">
                                <span class="text-[10px] font-bold text-slate-400 uppercase mb-1">Preview Barcode
                                    Realtime:</span>
                                <div class="w-56 h-12 flex items-center justify-center">
                                    {!! \App\Services\BarcodeService::getSvg($barcode, $barcode_type, 36, 1.4, true) !!}
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Format Barcode</label>
                            <select wire:model.live="barcode_type"
                                class="select select-bordered w-full text-base rounded-xl">
                                <option value="CODE128">Code 128 (Umum / Alphanumeric)</option>
                                <option value="EAN13">EAN-13 (13 Digit Standar Retail)</option>
                                <option value="AUTO">Otomatis</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Kode SKU/PLU Toko
                                (Opsional)</label>
                            <input type="text" wire:model="store_sku" placeholder="Misal: PLU-088"
                                class="input input-bordered w-full font-mono text-base rounded-xl" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Nama Khusus di Toko
                                (Opsional)</label>
                            <input type="text" wire:model="custom_product_name"
                                placeholder="Misal: WIJEN MERRY 200G"
                                class="input input-bordered w-full text-base rounded-xl" />
                            <p class="text-[11px] text-slate-400 mt-0.5">Kosongkan jika sama dengan nama produk.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Harga Jual Toko (Rp)</label>
                            <x-currency-input model="custom_price" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Catatan Tambahan (Opsional)</label>
                        <input type="text" wire:model="notes"
                            placeholder="Misal: Tempel stiker di kanan atas kemasan"
                            class="input input-bordered w-full text-sm rounded-xl" />
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showModal', false)"
                            class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6 cursor-pointer">
                            Simpan Barcode
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- MODAL KONFIRMASI HAPUS (APPLE UI) -->
    <x-confirm-delete-modal :show="$showDeleteModal" title="Hapus Barcode Toko?" :item-name="$deletingName"
        message="Apakah Anda yakin ingin menghapus data barcode toko ini? Anda dapat menambahkannya kembali kapan saja jika toko memerlukannya."
        confirm-action="deleteBarcode" confirm-text="Ya, Hapus Barcode" />
</div>
