<div class="space-y-8">
    <!-- Header Page & Tombol Tambah Bahan -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200">
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

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
                        @foreach($materials as $mat)
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
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Kolom Kanan: Pengaturan Resep Produk (1 Span) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-6 space-y-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900">Formulasi Resep Produk</h2>
                <p class="text-sm text-slate-500">Komposisi bahan per 1 pcs produk</p>
            </div>

            <div class="space-y-4">
                @foreach($products as $product)
                    <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-3">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-base text-slate-900">{{ $product->name }}</h3>
                            <button wire:click="openRecipeModal({{ $product->id }})" class="text-xs font-bold text-slate-900 underline hover:text-black">
                                Atur Resep
                            </button>
                        </div>

                        <div class="space-y-1 text-sm text-slate-600">
                            @forelse($product->recipes as $r)
                                <div class="flex justify-between">
                                    <span>{{ $r->rawMaterial->name }}:</span>
                                    <span class="font-mono font-bold text-slate-900">{{ $r->quantity_needed }} {{ $r->rawMaterial->unit }}</span>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">Belum ada resep yang diatur.</p>
                            @endforelse
                        </div>
                    </div>
                @endforeach
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
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Stok Saat Ini <span class="text-red-500">*</span></label>
                            <input type="number" step="0.01" wire:model="stock" class="input input-bordered w-full font-mono font-bold text-base rounded-xl focus:border-slate-900" />
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Batas Minimal Stok</label>
                            <input type="number" step="0.01" wire:model="min_stock" placeholder="Peringatan jika < batas" class="input input-bordered w-full font-mono font-bold text-base rounded-xl focus:border-slate-900" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Estimasi Harga Beli / Satuan (Rp)</label>
                            <input type="number" wire:model="cost_per_unit" class="input input-bordered w-full font-mono font-bold text-base rounded-xl focus:border-slate-900" />
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
                        <p class="text-xs text-slate-500">Produk: {{ $currentProduct?->name }} (per 1 pcs)</p>
                    </div>
                    <button wire:click="$set('showRecipeModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="saveRecipe" class="space-y-4">
                    <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                        @foreach($recipeRows as $idx => $row)
                            <div class="flex items-center gap-2 p-2 bg-slate-50 rounded-xl border border-slate-200">
                                <div class="flex-1">
                                    <select wire:model="recipeRows.{{ $idx }}.raw_material_id" class="select select-bordered select-sm w-full font-medium">
                                        <option value="">-- Pilih Bahan --</option>
                                        @foreach($materials as $m)
                                            <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->unit }})</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="w-24">
                                    <input type="number" step="0.0001" min="0.0001" placeholder="Qty" wire:model="recipeRows.{{ $idx }}.quantity_needed" class="input input-sm input-bordered w-full font-mono text-center font-bold" />
                                </div>

                                <button type="button" wire:click="removeRecipeRow({{ $idx }})" class="btn btn-sm btn-ghost btn-square text-red-500">
                                    &times;
                                </button>
                            </div>
                        @endforeach
                    </div>

                    <button type="button" wire:click="addRecipeRow" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold w-full rounded-xl border border-slate-300">
                        + Tambah Komposisi Bahan
                    </button>

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
