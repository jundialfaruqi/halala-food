<div class="space-y-6">
    <!-- Header Page & Tombol Catat Produksi -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Catatan Produksi Makanan
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Catat proses masak/bikin makanan. Stok produk jadi akan bertambah dan stok bahan baku otomatis terpotong sesuai resep.
            </p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm">
            <x-icon name="plus" class="text-xl" />
            <span>Catat Produksi Baru</span>
        </button>
    </div>

    <!-- Tabel Riwayat Produksi -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-base">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-sm uppercase tracking-wider">
                        <th class="py-4 px-6">Tanggal Produksi</th>
                        <th class="py-4 px-4">Nama Makanan</th>
                        <th class="py-4 px-4">Jumlah Jadi</th>
                        <th class="py-4 px-6">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($productions as $prod)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 text-base">
                                {{ $prod->production_date->format('d F Y') }}
                                <span class="block text-xs font-normal text-slate-500">{{ $prod->production_date->diffForHumans() }}</span>
                            </td>
                            <td class="py-4 px-4 font-bold text-lg text-slate-900">
                                {{ $prod->product->name }}
                            </td>
                            <td class="py-4 px-4 font-mono font-extrabold text-xl text-slate-900">
                                + {{ $prod->quantity_produced }} {{ $prod->product->unit }}
                            </td>
                            <td class="py-4 px-6 text-sm text-slate-500">
                                {{ $prod->notes ?: '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-12 text-center text-slate-500 text-base">
                                Belum ada riwayat produksi. Klik tombol "Catat Produksi Baru" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($productions->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $productions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Catat Produksi -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">
                        Catat Produksi Hari Ini
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Tanggal Masak/Produksi <span class="text-red-500">*</span></label>
                        <input type="date" wire:model="production_date" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        @error('production_date') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Pilih Produk Makanan <span class="text-red-500">*</span></label>
                        <select wire:model.live="product_id" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900">
                            @foreach($products as $p)
                                <option value="{{ $p->id }}">{{ $p->name }} (Stok saat ini: {{ $p->stock_ready }} {{ $p->unit }})</option>
                            @endforeach
                        </select>
                        @error('product_id') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Jumlah Yang Dibuat/Jadi (pcs) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model.live="quantity_produced" min="1" placeholder="Misal: 50" class="input input-bordered w-full font-mono font-bold text-xl rounded-xl focus:border-slate-900" />
                        @error('quantity_produced') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Estimasi Pemotongan Bahan Baku Sesuai Resep -->
                    @if($selectedProduct && $selectedProduct->recipes->count() > 0)
                        <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 space-y-2">
                            <p class="text-xs font-bold uppercase text-slate-500">Bahan Baku Yang Akan Terpotong Otomatis:</p>
                            <div class="space-y-1">
                                @foreach($selectedProduct->recipes as $recipe)
                                    @php
                                        $needed = $recipe->quantity_needed * ($quantity_produced ?: 0);
                                        $isEnough = $recipe->rawMaterial->stock >= $needed;
                                    @endphp
                                    <div class="flex items-center justify-between text-sm">
                                        <span class="text-slate-700 font-medium">{{ $recipe->rawMaterial->name }}:</span>
                                        <span class="font-mono {{ $isEnough ? 'text-slate-900 font-bold' : 'text-red-600 font-bold' }}">
                                            {{ $needed }} {{ $recipe->rawMaterial->unit }}
                                            <span class="text-xs font-normal text-slate-500">(Stok: {{ $recipe->rawMaterial->stock }})</span>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Catatan Tambahan</label>
                        <textarea wire:model="notes" rows="2" placeholder="Misal: Batch pagi, rasa extra renyah..." class="textarea textarea-bordered w-full text-base rounded-xl focus:border-slate-900"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan & Potong Bahan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
