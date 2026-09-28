<div class="space-y-8">
    <!-- Header Page & Tombol Tambah Bahan -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200/80">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Bahan Baku & Resep Makanan
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Kelola persediaan bahan mentah dan atur komposisi resep per produk untuk pemotongan otomatis.
            </p>
        </div>
        <button wire:click="openMaterialModal"
            class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm">
            <x-icon name="plus" class="text-xl" />
            <span>Tambah Bahan Baku</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-start">
        <!-- Kolom Kiri: Tabel Stok Bahan Baku (2 Span) -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 overflow-hidden">
            <div class="p-6 border-b border-slate-200 flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Persediaan Bahan Baku di Gudang</h2>
                    <p class="text-sm text-slate-500">Stok riil bahan mentah untuk masak</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="table w-full text-base">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-sm uppercase">
                            <th class="py-4 px-6">Nama Bahan</th>
                            <th class="py-4 px-4">Stok Saat Ini</th>
                            <th class="py-4 px-4">Batas Min. Stok</th>
                            <th class="py-4 px-4">Harga Beli / Unit</th>
                            <th class="py-4 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($materials as $mat)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-bold text-lg text-slate-900">
                                    {{ $mat->name }}
                                </td>
                                <td class="py-4 px-4 font-mono font-extrabold text-xl text-slate-900">
                                    {{ $mat->stock }} <span
                                        class="text-xs font-normal text-slate-500">{{ $mat->unit }}</span>
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-600">
                                    {{ $mat->min_stock }} {{ $mat->unit }}
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-700">
                                    Rp {{ number_format($mat->cost_per_unit, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openMaterialModal({{ $mat->id }})"
                                            class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg px-3 border border-slate-300">
                                            Edit
                                        </button>
                                        <button wire:click="confirmDelete({{ $mat->id }}, '{{ addslashes($mat->name) }}')"
                                            class="btn btn-sm bg-slate-100 hover:bg-rose-50 text-slate-700 hover:text-rose-600 font-bold rounded-lg px-3 border border-slate-300 transition">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="max-w-md mx-auto space-y-4">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                            <x-icon name="archive" class="text-3xl" />
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-xl text-slate-900">Belum Ada Bahan Baku</h3>
                                            <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                                                Tambahkan bahan mentah seperti tepung, wijen, minyak, atau telur untuk
                                                mulai mengontrol stok gudang dan resep produksi.
                                            </p>
                                        </div>
                                        <div class="pt-2">
                                            <button wire:click="openMaterialModal"
                                                class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6 text-sm shadow-sm gap-2">
                                                <x-icon name="plus" class="text-lg" />
                                                <span>Tambah Bahan Baku Pertama</span>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan: Pengaturan Resep Produk (1 Span) -->
        <div class="space-y-4">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Formulasi Resep Produk</h2>
                <p class="text-sm text-slate-500">Kebutuhan takaran bahan baku per 1 satuan produk jadi</p>
            </div>

            <div class="space-y-4">
                @forelse($products as $product)
                    <div class="p-5 bg-white rounded-2xl border border-slate-200/80 shadow-xs space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">{{ $product->name }}</h3>
                                <span class="text-xs text-slate-500">Takaran per 1 {{ $product->unit }}</span>
                            </div>
                            <button wire:click="openRecipeModal({{ $product->id }})"
                                class="text-xs font-bold text-slate-800 hover:text-black border-b border-slate-400 hover:border-slate-900 pb-0.5 transition-colors cursor-pointer">
                                Atur Resep
                            </button>
                        </div>

                        <div class="divide-y divide-slate-100 text-sm text-slate-600">
                            @forelse($product->recipes as $r)
                                @php
                                    $itemCost = $r->quantity_needed * ($r->rawMaterial->cost_per_unit ?? 0);
                                @endphp
                                <div
                                    class="flex items-center justify-between gap-2 text-xs sm:text-sm py-1.5 first:pt-0 last:pb-0">
                                    <span class="text-slate-700 truncate min-w-0" title="{{ $r->rawMaterial->name }}">
                                        {{ $r->rawMaterial->name }}
                                    </span>
                                    <div
                                        class="shrink-0 flex items-center gap-1.5 whitespace-nowrap font-mono text-right">
                                        <span class="font-bold text-slate-900 text-xs sm:text-sm">
                                            {{ $r->quantity_needed }} {{ $r->rawMaterial->unit }}
                                        </span>
                                        @if (($r->rawMaterial->cost_per_unit ?? 0) > 0)
                                            <span
                                                class="text-[11px] text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded font-medium">
                                                Rp {{ number_format($itemCost, 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic py-1">Belum ada resep yang diatur.</p>
                            @endforelse
                        </div>

                        <!-- Total Modal Bahan (HPP) & Margin -->
                        @if ($product->recipes->isNotEmpty())
                            @php
                                $totalMaterialCost = $product->material_cost;
                                $grossProfit = $product->consignment_price - $totalMaterialCost;
                                $marginPercent =
                                    $product->consignment_price > 0
                                        ? round(($grossProfit / $product->consignment_price) * 100, 1)
                                        : 0;
                            @endphp
                            <div class="pt-2 border-t border-slate-100 space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span
                                        class="text-xs font-bold uppercase tracking-wider text-slate-500 shrink-0">Total
                                        Modal (HPP)</span>
                                    <span
                                        class="text-base font-extrabold font-mono text-slate-900 shrink-0 whitespace-nowrap text-right">
                                        Rp {{ number_format($totalMaterialCost, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if ($product->consignment_price > 0)
                                    <div class="flex items-center justify-between gap-2 text-xs text-slate-500 pt-0.5">
                                        <span class="shrink-0 whitespace-nowrap">Titip: <strong>Rp
                                                {{ number_format($product->consignment_price, 0, ',', '.') }}</strong></span>
                                        <span
                                            class="font-bold shrink-0 whitespace-nowrap {{ $grossProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                            Margin: Rp {{ number_format($grossProfit, 0, ',', '.') }}
                                            ({{ $marginPercent }}%)
                                        </span>
                                    </div>
                                @endif

                                {{-- <p class="text-[11px] text-slate-400 italic pt-0.5">
                                    * Per 1 {{ $product->unit }}
                                </p> --}}
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center space-y-3 bg-white rounded-2xl border border-slate-200/80 p-6">
                        <div
                            class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <x-icon name="package" class="text-xl" />
                        </div>
                        <p class="text-sm text-slate-500">Belum ada produk untuk diatur resepnya.</p>
                        <a href="{{ route('products.index') }}"
                            class="btn btn-sm bg-slate-900 text-white font-bold rounded-lg px-4">
                            + Tambah Produk Dulu
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Form Bahan Baku -->
    @if ($showMaterialModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white w-full max-w-xl max-h-[90vh] flex flex-col rounded-2xl border border-slate-300 shadow-2xl overflow-hidden">
                <!-- Header Sticky -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            {{ $materialId ? 'Edit Data Bahan Baku' : 'Tambah Bahan Baku Baru' }}
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Isi data bahan baku untuk stok & perhitungan resep</p>
                    </div>
                    <button wire:click="$set('showMaterialModal', false)"
                        class="text-slate-400 hover:text-slate-700 font-bold text-2xl p-1 leading-none">
                        &times;
                    </button>
                </div>

                <!-- Form Bahan Baku Body (Scrollable) -->
                <form wire:submit="saveMaterial" class="flex flex-col flex-1 overflow-hidden min-h-0">
                    <div class="p-6 overflow-y-auto space-y-5 flex-1">
                        
                        <!-- Nama Bahan Baku -->
                        <div>
                            <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                Nama Bahan Baku <span class="text-red-600">*</span>
                            </label>
                            <input type="text" wire:model="name" placeholder="Misal: Kacang Tanah Sangrai"
                                class="input input-bordered w-full text-base font-semibold h-12 rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900" />
                            @error('name')
                                <span class="text-xs sm:text-sm text-red-600 font-bold block mt-1">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Panel Bantuan Hitung Kemasan Otomatis (Desain Ramah Orang Tua / High Contrast) -->
                        <div class="bg-slate-100 rounded-2xl p-4 border border-slate-300 space-y-3">
                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <h4 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                                        <x-icon name="calculator" class="text-lg text-slate-700" />
                                        <span>Bantu Hitung Satuan Otomatis</span>
                                    </h4>
                                    <p class="text-xs sm:text-sm text-slate-600 mt-0.5">
                                        Gunakan jika membeli per bungkus / pak / sachet di pasar
                                    </p>
                                </div>
                                <button type="button" wire:click="toggleCalculator"
                                    class="btn btn-sm {{ $showCalculator ? 'bg-slate-900 text-white hover:bg-black' : 'bg-white text-slate-900 hover:bg-slate-200 border-slate-300' }} rounded-xl font-bold px-3.5 shrink-0 shadow-2xs">
                                    {{ $showCalculator ? 'Tutup' : 'Buka' }}
                                </button>
                            </div>

                            <!-- Panel Form Kalkulator Multi-Baris -->
                            @if($showCalculator)
                                <div class="pt-3 border-t border-slate-300 space-y-3">
                                    
                                    <!-- Baris 1: Jumlah Kemasan -->
                                    <div class="bg-white p-3.5 rounded-xl border border-slate-300 space-y-1.5">
                                        <label class="font-bold text-slate-900 text-sm sm:text-base flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-black shrink-0">1</span>
                                            <span>Jumlah Kemasan yang Dibeli</span>
                                        </label>
                                        <p class="text-xs text-slate-600 pl-8">Berapa bungkus / pak / sachet yang dibeli?</p>
                                        <div class="pl-8">
                                            <div class="flex items-center w-full rounded-xl border border-slate-300 focus-within:border-slate-900 overflow-hidden bg-white h-11 shadow-2xs">
                                                <input type="number" step="any" min="0" wire:model.live="calc_package_count" placeholder="Contoh: 14"
                                                    class="w-full font-mono font-bold text-base text-slate-900 px-3.5 h-full bg-transparent outline-none focus:outline-none" />
                                                <span class="bg-slate-100 text-slate-900 font-bold text-xs sm:text-sm px-3.5 h-full flex items-center border-l border-slate-300 select-none shrink-0">
                                                    kemasan
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Baris 2: Isi Bersih per Kemasan & Satuan Dasar -->
                                    <div class="bg-white p-3.5 rounded-xl border border-slate-300 space-y-1.5">
                                        <label class="font-bold text-slate-900 text-sm sm:text-base flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-black shrink-0">2</span>
                                            <span>Isi Bersih per 1 Kemasan</span>
                                        </label>
                                        <p class="text-xs text-slate-600 pl-8">Berapa bobot atau isi dalam 1 bungkusnya?</p>
                                        <div class="grid grid-cols-12 gap-2 pl-8">
                                            <div class="col-span-7">
                                                <input type="number" step="any" min="0" wire:model.live="calc_content_per_package" placeholder="Contoh: 1000"
                                                    class="input input-bordered w-full font-mono font-bold text-base text-slate-900 rounded-xl h-11 bg-white border-slate-300 focus:border-slate-900" />
                                            </div>
                                            <div class="col-span-5">
                                                <select wire:model.live="calc_base_unit" class="select select-bordered w-full rounded-xl font-bold text-sm h-11 bg-white border-slate-300 text-slate-900 focus:border-slate-900">
                                                    <option value="gram">gram (g)</option>
                                                    <option value="pcs">pcs / lbr</option>
                                                    <option value="ml">ml / cc</option>
                                                    <option value="kg">kg</option>
                                                </select>
                                            </div>
                                        </div>
                                        @error('calc_content_per_package')
                                            <span class="text-xs sm:text-sm text-red-600 font-bold block pl-8">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Baris 3: Harga Beli per Kemasan -->
                                    <div class="bg-white p-3.5 rounded-xl border border-slate-300 space-y-1.5">
                                        <label class="font-bold text-slate-900 text-sm sm:text-base flex items-center gap-2">
                                            <span class="w-6 h-6 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-black shrink-0">3</span>
                                            <span>Harga Beli per 1 Kemasan di Pasar</span>
                                        </label>
                                        <p class="text-xs text-slate-600 pl-8">Harga per bungkus sesuai nota belanja</p>
                                        <div class="pl-8" x-data="{
                                            displayValue: '',
                                            rawAmount: @entangle('calc_price_per_package').live,
                                            formatCurrency(val) {
                                                if (val === null || val === undefined || val === '') return '';
                                                let numStr = String(val).replace(/\D/g, '');
                                                if (!numStr) return '';
                                                return new Intl.NumberFormat('id-ID').format(numStr);
                                            },
                                            updateValue(e) {
                                                let digits = e.target.value.replace(/\D/g, '');
                                                this.rawAmount = digits ? parseFloat(digits) : null;
                                                this.displayValue = digits ? new Intl.NumberFormat('id-ID').format(digits) : '';
                                            },
                                            init() {
                                                if (this.rawAmount) this.displayValue = this.formatCurrency(this.rawAmount);
                                                $watch('rawAmount', (val) => {
                                                    if (val === null || val === undefined || val === '' || val == 0) {
                                                        if (this.displayValue !== '' && (val === null || val === undefined || val === '')) {
                                                            this.displayValue = '';
                                                        }
                                                    } else {
                                                        let formatted = this.formatCurrency(val);
                                                        if (this.displayValue !== formatted) {
                                                            this.displayValue = formatted;
                                                        }
                                                    }
                                                });
                                            }
                                        }">
                                            <div class="flex items-center w-full rounded-xl border border-slate-300 focus-within:border-slate-900 overflow-hidden bg-white h-11 shadow-2xs">
                                                <span class="bg-slate-100 text-slate-900 font-bold text-sm sm:text-base px-3.5 h-full flex items-center border-r border-slate-300 select-none shrink-0">
                                                    Rp
                                                </span>
                                                <input 
                                                    type="text" 
                                                    inputmode="numeric"
                                                    x-model="displayValue" 
                                                    @input="updateValue($event)"
                                                    placeholder="Contoh: 45.000"
                                                    class="w-full font-mono font-bold text-base text-slate-900 px-3.5 h-full bg-transparent outline-none focus:outline-none" />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Hasil Perhitungan Jelas & Tulisan Besar -->
                                    @php
                                        $cPkgCount = (float)($calc_package_count ?? 0);
                                        $cContent = (float)($calc_content_per_package ?? 0);
                                        $cPrice = (float)($calc_price_per_package ?? 0);

                                        $autoStock = $cPkgCount * $cContent;
                                        $autoCostPerUnit = $cContent > 0 ? round($cPrice / $cContent, 4) : 0;
                                        $autoTotalSpending = $cPkgCount * $cPrice;
                                    @endphp

                                    @if($cContent > 0)
                                        <div class="p-4 bg-white rounded-xl border-2 border-slate-900 space-y-3 shadow-xs">
                                            <div class="text-sm font-bold text-slate-900 border-b border-slate-200 pb-2 flex items-center justify-between">
                                                <span>Hasil Perhitungan:</span>
                                                <span class="text-xs font-bold text-slate-700 bg-slate-100 px-2.5 py-1 rounded-lg">
                                                    {{ $cPkgCount }} bungkus &times; {{ number_format($cContent, 0, ',', '.') }} {{ $calc_base_unit }}
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-2 gap-3">
                                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200">
                                                    <span class="text-xs sm:text-sm font-semibold text-slate-600 block">Total Stok:</span>
                                                    <span class="font-mono text-lg sm:text-xl font-black text-slate-900 block mt-0.5">
                                                        {{ number_format($autoStock, 0, ',', '.') }} <span class="text-sm font-bold text-slate-700">{{ $calc_base_unit }}</span>
                                                    </span>
                                                </div>
                                                <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 text-right">
                                                    <span class="text-xs sm:text-sm font-semibold text-slate-600 block">Harga Modal / Satuan:</span>
                                                    <span class="font-mono text-lg sm:text-xl font-black text-slate-900 block mt-0.5">
                                                        Rp {{ number_format($autoCostPerUnit, 2, ',', '.') }} <span class="text-xs font-normal text-slate-600">/ {{ $calc_base_unit }}</span>
                                                    </span>
                                                </div>
                                            </div>

                                            @if($autoTotalSpending > 0)
                                                <div class="flex justify-between items-center bg-slate-100 px-3.5 py-2 rounded-xl text-xs sm:text-sm">
                                                    <span class="text-slate-700 font-semibold">Total Uang Belanja (Nota):</span>
                                                    <span class="font-mono font-bold text-slate-900 text-base">Rp {{ number_format($autoTotalSpending, 0, ',', '.') }}</span>
                                                </div>
                                            @endif

                                            <button type="button" wire:click="applyCalculator"
                                                class="btn bg-slate-900 hover:bg-black text-white font-bold w-full rounded-xl h-11 text-sm sm:text-base gap-2 shadow-xs">
                                                <x-icon name="check" class="text-lg" />
                                                <span>Gunakan Hasil Ini ke Form</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Baris Input Satuan & Stok -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                    Satuan Resep <span class="text-red-600">*</span>
                                </label>
                                <input type="text" wire:model="unit" placeholder="gram / pcs / ml"
                                    class="input input-bordered w-full font-bold text-base h-12 rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900" />
                                
                                <!-- Pilihan Satuan Preset Cepat -->
                                <div class="flex items-center gap-1.5 mt-2 flex-wrap">
                                    <span class="text-xs text-slate-500 font-medium">Pilihan:</span>
                                    <button type="button" wire:click="$set('unit', 'gram')"
                                        class="btn btn-xs {{ $unit === 'gram' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200' }} rounded-lg font-bold">gram</button>
                                    <button type="button" wire:click="$set('unit', 'pcs')"
                                        class="btn btn-xs {{ $unit === 'pcs' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200' }} rounded-lg font-bold">pcs</button>
                                    <button type="button" wire:click="$set('unit', 'ml')"
                                        class="btn btn-xs {{ $unit === 'ml' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200' }} rounded-lg font-bold">ml</button>
                                    <button type="button" wire:click="$set('unit', 'kg')"
                                        class="btn btn-xs {{ $unit === 'kg' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-700 border-slate-300 hover:bg-slate-200' }} rounded-lg font-bold">kg</button>
                                </div>
                                @error('unit')
                                    <span class="text-xs sm:text-sm text-red-600 font-bold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                    Stok Saat Ini <span class="text-red-600">*</span>
                                </label>
                                <input type="number" step="any" min="0" wire:model="stock"
                                    class="input input-bordered w-full font-mono font-bold text-base h-12 rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900" />
                                <span class="text-xs text-slate-600 mt-1 block">Dalam satuan <strong>{{ $unit ?: 'unit' }}</strong></span>
                                @error('stock')
                                    <span class="text-xs sm:text-sm text-red-600 font-bold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Baris Input Batas Minimal Stok & Harga Beli Satuan -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                    Batas Minimal Stok <span class="text-red-600">*</span>
                                </label>
                                <input type="number" step="any" min="0" wire:model="min_stock"
                                    placeholder="Contoh: 1000"
                                    class="input input-bordered w-full font-mono font-bold text-base h-12 rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900" />
                                <span class="text-xs text-slate-600 mt-1 block">Peringatan jika stok menipis</span>
                                @error('min_stock')
                                    <span class="text-xs sm:text-sm text-red-600 font-bold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                    Harga Beli per Satuan (Rp)
                                </label>
                                <div class="flex items-center w-full rounded-xl border border-slate-300 focus-within:border-slate-900 overflow-hidden bg-white h-12 shadow-2xs">
                                    <span class="bg-slate-100 text-slate-900 font-bold text-base px-4 h-full flex items-center border-r border-slate-300 select-none shrink-0">
                                        Rp
                                    </span>
                                    <input type="number" step="any" min="0" wire:model="cost_per_unit" placeholder="0"
                                        class="w-full font-mono font-bold text-base text-slate-900 px-3.5 h-full bg-transparent outline-none focus:outline-none" />
                                </div>
                                <span class="text-xs text-slate-600 mt-1 block">Biaya modal per 1 <strong>{{ $unit ?: 'satuan' }}</strong></span>
                                @error('cost_per_unit')
                                    <span class="text-xs sm:text-sm text-red-600 font-bold block mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Footer Sticky -->
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
                        <button type="button" wire:click="$set('showMaterialModal', false)"
                            class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl px-5 border border-slate-300 shadow-2xs">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6 shadow-2xs">
                            Simpan Bahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Form Resep Produk -->
    @if ($showRecipeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Atur Resep Makanan</h3>
                        <p class="text-xs text-slate-500">
                            Takaran bahan untuk membuat <strong>1 {{ $currentProduct?->unit ?: 'satuan' }}
                                {{ $currentProduct?->name }}</strong>
                        </p>
                    </div>
                    <button wire:click="$set('showRecipeModal', false)"
                        class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="saveRecipe" class="space-y-4">
                    @error('recipeRows')
                        <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-xs text-red-600 font-semibold">
                            {{ $message }}
                        </div>
                    @enderror

                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        @foreach ($recipeRows as $idx => $row)
                            @php
                                $selectedMat = $materials->firstWhere('id', $row['raw_material_id'] ?? null);
                                $rowCost =
                                    $selectedMat && !empty($row['quantity_needed'])
                                        ? (float) $row['quantity_needed'] * (float) $selectedMat->cost_per_unit
                                        : 0;
                            @endphp
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <select wire:model.live="recipeRows.{{ $idx }}.raw_material_id"
                                            class="select select-bordered select-sm w-full font-medium text-sm">
                                            <option value="">-- Pilih Bahan --</option>
                                            @foreach ($materials as $m)
                                                <option value="{{ $m->id }}">
                                                    {{ $m->name }} ({{ $m->unit }}) @if ($m->cost_per_unit > 0)
                                                        - Rp
                                                        {{ number_format($m->cost_per_unit, 0, ',', '.') }}/{{ $m->unit }}
                                                    @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-24">
                                        <input type="number" step="0.0001" min="0.0001" placeholder="Qty"
                                            wire:model.live.debounce.250ms="recipeRows.{{ $idx }}.quantity_needed"
                                            class="input input-sm input-bordered w-full font-mono text-center font-bold text-sm" />
                                    </div>

                                    <button type="button" wire:click="removeRecipeRow({{ $idx }})"
                                        class="btn btn-sm btn-ghost btn-square text-red-500 hover:bg-red-50">
                                        &times;
                                    </button>
                                </div>

                                @if ($rowCost > 0 && $selectedMat)
                                    <div class="flex justify-between items-center text-[11px] text-slate-500 px-1">
                                        <span>{{ (float) $row['quantity_needed'] }} {{ $selectedMat->unit }} × Rp
                                            {{ number_format($selectedMat->cost_per_unit, 0, ',', '.') }}</span>
                                        <span class="font-mono font-bold text-slate-800">Rp
                                            {{ number_format($rowCost, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @error('recipeRows.' . $idx . '.raw_material_id')
                                    <span class="text-xs text-red-600 font-semibold block">{{ $message }}</span>
                                @enderror
                                @error('recipeRows.' . $idx . '.quantity_needed')
                                    <span class="text-xs text-red-600 font-semibold block">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addRecipeRow"
                        class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold w-full rounded-xl border border-slate-300">
                        + Tambah Komposisi Bahan
                    </button>

                    <!-- Realtime Total Modal Bahan in Modal -->
                    <div class="bg-slate-900 text-white p-3.5 rounded-xl space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-300 uppercase tracking-wider">Total Modal Bahan
                                (HPP)</span>
                            <span class="text-lg font-bold font-mono text-emerald-400">
                                Rp {{ number_format($this->modalMaterialCost, 0, ',', '.') }}
                                <span class="text-xs font-normal text-slate-300">/ {{ $currentProduct?->unit }}</span>
                            </span>
                        </div>
                        @if ($currentProduct && $currentProduct->consignment_price > 0)
                            @php
                                $modalGrossProfit = $currentProduct->consignment_price - $this->modalMaterialCost;
                                $modalMarginPct = round(
                                    ($modalGrossProfit / $currentProduct->consignment_price) * 100,
                                    1,
                                );
                            @endphp
                            <div
                                class="flex items-center justify-between text-xs text-slate-300 pt-1 border-t border-slate-800">
                                <span>Harga Titip: Rp
                                    {{ number_format($currentProduct->consignment_price, 0, ',', '.') }}</span>
                                <span
                                    class="font-semibold {{ $modalGrossProfit >= 0 ? 'text-emerald-300' : 'text-rose-400' }}">
                                    Margin: Rp {{ number_format($modalGrossProfit, 0, ',', '.') }}
                                    ({{ $modalMarginPct }}%)
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showRecipeModal', false)"
                            class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit"
                            class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan Resep
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi Hapus Bahan Baku (Apple UI) -->
    <x-confirm-delete-modal 
        :show="$showDeleteModal"
        title="Hapus Bahan Baku?"
        :item-name="$deletingName"
        message="Apakah Anda yakin ingin menghapus data bahan baku ini? Pastikan bahan ini tidak sedang digunakan pada resep aktif."
        confirm-action="deleteMaterial"
        confirm-text="Ya, Hapus Bahan"
    />
</div>
