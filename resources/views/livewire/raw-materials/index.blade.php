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
        <button wire:click="openMaterialModal" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm">
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
                                    {{ $mat->stock }} <span class="text-xs font-normal text-slate-500">{{ $mat->unit }}</span>
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-600">
                                    {{ $mat->min_stock }} {{ $mat->unit }}
                                </td>
                                <td class="py-4 px-4 font-mono text-slate-700">
                                    Rp {{ number_format($mat->cost_per_unit, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="openMaterialModal({{ $mat->id }})" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg px-3 border border-slate-300">
                                            Edit
                                        </button>
                                        <button wire:click="deleteMaterial({{ $mat->id }})"
                                                wire:confirm="Apakah Anda yakin ingin menghapus bahan baku '{{ $mat->name }}'?"
                                                class="btn btn-sm bg-slate-100 hover:bg-red-50 text-red-600 hover:text-red-700 font-bold rounded-lg px-3 border border-slate-300">
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="max-w-md mx-auto space-y-4">
                                        <div class="w-14 h-14 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                                            <x-icon name="archive" class="text-3xl" />
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-xl text-slate-900">Belum Ada Bahan Baku</h3>
                                            <p class="text-sm text-slate-500 mt-1 leading-relaxed">
                                                Tambahkan bahan mentah seperti tepung, wijen, minyak, atau telur untuk mulai mengontrol stok gudang dan resep produksi.
                                            </p>
                                        </div>
                                        <div class="pt-2">
                                            <button wire:click="openMaterialModal" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6 text-sm shadow-sm gap-2">
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
        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Formulasi Resep Produk</h2>
                <p class="text-sm text-slate-500">Kebutuhan takaran bahan baku per 1 satuan produk jadi</p>
            </div>

            <div class="space-y-4">
                @forelse($products as $product)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-base text-slate-900">{{ $product->name }}</h3>
                                <span class="text-xs text-slate-500">Takaran per 1 {{ $product->unit }}</span>
                            </div>
                            <button wire:click="openRecipeModal({{ $product->id }})" class="text-xs font-bold text-slate-800 hover:text-black border-b border-slate-400 hover:border-slate-900 pb-0.5 transition-colors cursor-pointer">
                                Atur Resep
                            </button>
                        </div>

                        <div class="divide-y divide-slate-200/70 text-sm text-slate-600">
                            @forelse($product->recipes as $r)
                                @php
                                    $itemCost = $r->quantity_needed * ($r->rawMaterial->cost_per_unit ?? 0);
                                @endphp
                                <div class="flex items-center justify-between gap-2 text-xs sm:text-sm py-1.5 first:pt-0 last:pb-0">
                                    <span class="text-slate-700 truncate min-w-0" title="{{ $r->rawMaterial->name }}">
                                        {{ $r->rawMaterial->name }}
                                    </span>
                                    <div class="shrink-0 flex items-center gap-1.5 whitespace-nowrap font-mono text-right">
                                        <span class="font-bold text-slate-900 text-xs sm:text-sm">
                                            {{ $r->quantity_needed }} {{ $r->rawMaterial->unit }}
                                        </span>
                                        @if(($r->rawMaterial->cost_per_unit ?? 0) > 0)
                                            <span class="text-[11px] text-slate-500 bg-slate-200/70 px-1.5 py-0.5 rounded font-medium">
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
                        @if($product->recipes->isNotEmpty())
                            @php
                                $totalMaterialCost = $product->material_cost;
                                $grossProfit = $product->consignment_price - $totalMaterialCost;
                                $marginPercent = $product->consignment_price > 0 ? round(($grossProfit / $product->consignment_price) * 100, 1) : 0;
                            @endphp
                            <div class="pt-2 border-t border-slate-200/80 space-y-1">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 shrink-0">Total Modal (HPP)</span>
                                    <span class="text-base font-extrabold font-mono text-slate-900 shrink-0 whitespace-nowrap text-right">
                                        Rp {{ number_format($totalMaterialCost, 0, ',', '.') }}
                                    </span>
                                </div>

                                @if($product->consignment_price > 0)
                                    <div class="flex items-center justify-between gap-2 text-xs text-slate-500 pt-0.5">
                                        <span class="shrink-0 whitespace-nowrap">Titip: <strong>Rp {{ number_format($product->consignment_price, 0, ',', '.') }}</strong></span>
                                        <span class="font-bold shrink-0 whitespace-nowrap {{ $grossProfit >= 0 ? 'text-emerald-700' : 'text-rose-600' }}">
                                            Margin: Rp {{ number_format($grossProfit, 0, ',', '.') }} ({{ $marginPercent }}%)
                                        </span>
                                    </div>
                                @endif

                                <p class="text-[11px] text-slate-400 italic pt-0.5">
                                    * Per 1 {{ $product->unit }}
                                </p>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="py-8 text-center space-y-3">
                        <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto">
                            <x-icon name="package" class="text-xl" />
                        </div>
                        <p class="text-sm text-slate-500">Belum ada produk untuk diatur resepnya.</p>
                        <a href="{{ route('products.index') }}" class="btn btn-sm bg-slate-900 text-white font-bold rounded-lg px-4">
                            + Tambah Produk Dulu
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Modal Form Bahan Baku -->
    @if($showMaterialModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">
                        {{ $materialId ? 'Update Bahan Baku' : 'Tambah Bahan Baku Baru' }}
                    </h3>
                    <button wire:click="$set('showMaterialModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="saveMaterial" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nama Bahan Baku <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="Misal: Wijen Putih" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        @error('name') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="unit" placeholder="kg / gram / pcs / liter" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                            @error('unit') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Stok Saat Ini <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" wire:model="stock" class="input input-bordered w-full font-mono font-bold text-base rounded-xl focus:border-slate-900" />
                            @error('stock') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Batas Minimal Stok</label>
                            <input type="number" step="0.01" wire:model="min_stock" placeholder="Peringatan jika < batas" class="input input-bordered w-full font-mono font-bold text-base rounded-xl focus:border-slate-900" />
                            @error('min_stock') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Estimasi Harga Beli / Satuan (Rp)</label>
                            <x-currency-input model="cost_per_unit" />
                            @error('cost_per_unit') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showMaterialModal', false)" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan Bahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Form Resep Produk -->
    @if($showRecipeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">Atur Resep Makanan</h3>
                        <p class="text-xs text-slate-500">
                            Takaran bahan untuk membuat <strong>1 {{ $currentProduct?->unit ?: 'satuan' }} {{ $currentProduct?->name }}</strong>
                        </p>
                    </div>
                    <button wire:click="$set('showRecipeModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
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
                        @foreach($recipeRows as $idx => $row)
                            @php
                                $selectedMat = $materials->firstWhere('id', $row['raw_material_id'] ?? null);
                                $rowCost = ($selectedMat && !empty($row['quantity_needed'])) ? ((float)$row['quantity_needed'] * (float)$selectedMat->cost_per_unit) : 0;
                            @endphp
                            <div class="p-2.5 bg-slate-50 rounded-xl border border-slate-200 space-y-1.5">
                                <div class="flex items-center gap-2">
                                    <div class="flex-1">
                                        <select wire:model.live="recipeRows.{{ $idx }}.raw_material_id" class="select select-bordered select-sm w-full font-medium text-sm">
                                            <option value="">-- Pilih Bahan --</option>
                                            @foreach($materials as $m)
                                                <option value="{{ $m->id }}">
                                                    {{ $m->name }} ({{ $m->unit }}) @if($m->cost_per_unit > 0) - Rp {{ number_format($m->cost_per_unit, 0, ',', '.') }}/{{ $m->unit }} @endif
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="w-24">
                                        <input type="number" step="0.0001" min="0.0001" placeholder="Qty" wire:model.live.debounce.250ms="recipeRows.{{ $idx }}.quantity_needed" class="input input-sm input-bordered w-full font-mono text-center font-bold text-sm" />
                                    </div>

                                    <button type="button" wire:click="removeRecipeRow({{ $idx }})" class="btn btn-sm btn-ghost btn-square text-red-500 hover:bg-red-50">
                                        &times;
                                    </button>
                                </div>

                                @if($rowCost > 0 && $selectedMat)
                                    <div class="flex justify-between items-center text-[11px] text-slate-500 px-1">
                                        <span>{{ (float)$row['quantity_needed'] }} {{ $selectedMat->unit }} × Rp {{ number_format($selectedMat->cost_per_unit, 0, ',', '.') }}</span>
                                        <span class="font-mono font-bold text-slate-800">Rp {{ number_format($rowCost, 0, ',', '.') }}</span>
                                    </div>
                                @endif

                                @error('recipeRows.'.$idx.'.raw_material_id')
                                    <span class="text-xs text-red-600 font-semibold block">{{ $message }}</span>
                                @enderror
                                @error('recipeRows.'.$idx.'.quantity_needed')
                                    <span class="text-xs text-red-600 font-semibold block">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addRecipeRow" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold w-full rounded-xl border border-slate-300">
                        + Tambah Komposisi Bahan
                    </button>

                    <!-- Realtime Total Modal Bahan in Modal -->
                    <div class="bg-slate-900 text-white p-3.5 rounded-xl space-y-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-300 uppercase tracking-wider">Total Modal Bahan (HPP)</span>
                            <span class="text-lg font-bold font-mono text-emerald-400">
                                Rp {{ number_format($this->modalMaterialCost, 0, ',', '.') }}
                                <span class="text-xs font-normal text-slate-300">/ {{ $currentProduct?->unit }}</span>
                            </span>
                        </div>
                        @if($currentProduct && $currentProduct->consignment_price > 0)
                            @php
                                $modalGrossProfit = $currentProduct->consignment_price - $this->modalMaterialCost;
                                $modalMarginPct = round(($modalGrossProfit / $currentProduct->consignment_price) * 100, 1);
                            @endphp
                            <div class="flex items-center justify-between text-xs text-slate-300 pt-1 border-t border-slate-800">
                                <span>Harga Titip: Rp {{ number_format($currentProduct->consignment_price, 0, ',', '.') }}</span>
                                <span class="font-semibold {{ $modalGrossProfit >= 0 ? 'text-emerald-300' : 'text-rose-400' }}">
                                    Margin: Rp {{ number_format($modalGrossProfit, 0, ',', '.') }} ({{ $modalMarginPct }}%)
                                </span>
                            </div>
                        @endif
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showRecipeModal', false)" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan Resep
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
