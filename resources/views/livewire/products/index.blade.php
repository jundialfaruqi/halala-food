<div class="space-y-6">
    <!-- Header Page & Tombol Tambah Produk -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Katalog Produk Jadi
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Daftar makanan yang diproduksi dan siap dititipkan ke toko mitra.
            </p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm">
            <x-icon name="plus" class="text-xl" />
            <span>Tambah Produk</span>
        </button>
    </div>

    <!-- Grid Kartu Produk Jadi -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($products as $product)
            <div class="bg-white rounded-2xl border border-slate-200 p-6 flex flex-col justify-between space-y-5 hover:border-slate-400 transition-all">
                <div class="space-y-3">
                    <div class="flex items-start justify-between">
                        <h2 class="text-xl font-bold text-slate-900 leading-tight">{{ $product->name }}</h2>
                        <span class="text-xs font-bold uppercase text-slate-400 font-mono">{{ $product->unit }}</span>
                    </div>

                    <p class="text-sm text-slate-500 leading-relaxed">
                        {{ $product->description ?: 'Makanan khas keluarga Halala Food' }}
                    </p>

                    <!-- Info Harga -->
                    <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-sm font-bold text-slate-600">Harga Titip Toko:</span>
                            <span class="text-base font-bold font-mono text-slate-900">Rp {{ number_format($product->consignment_price, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">Harga Jual Rekomendasi:</span>
                            <span class="text-sm font-mono text-slate-700">Rp {{ number_format($product->retail_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                    <div>
                        <p class="text-xs text-slate-400 font-bold uppercase">Stok Siap Antar</p>
                        <p class="text-2xl font-extrabold text-slate-900 font-mono">
                            {{ $product->stock_ready }} <span class="text-xs font-normal text-slate-500">{{ $product->unit }}</span>
                        </p>
                    </div>

                    <div class="flex items-center gap-2">
                        <button wire:click="openEditModal({{ $product->id }})" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg px-3 border border-slate-300">
                            Edit
                        </button>
                        <button wire:click="deleteProduct({{ $product->id }})"
                                wire:confirm="Apakah Anda yakin ingin menghapus produk '{{ $product->name }}'?"
                                class="btn btn-sm bg-slate-100 hover:bg-red-50 text-red-600 hover:text-red-700 font-bold rounded-lg px-3 border border-slate-300">
                            Hapus
                        </button>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Modal Form Tambah/Edit Produk -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">
                        {{ $productId ? 'Edit Data Produk' : 'Tambah Produk Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nama Produk <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="Misal: Merry Wijen" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        @error('name') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Satuan <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="unit" placeholder="bungkus / toples / pcs" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                            @error('unit') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Stok Siap Antar Saat Ini <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="stock_ready" min="0" class="input input-bordered w-full font-mono font-bold text-base rounded-xl focus:border-slate-900" />
                            @error('stock_ready') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Harga Titip Toko (Rp) <span class="text-red-500">*</span></label>
                            <x-currency-input model="consignment_price" />
                            @error('consignment_price') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Harga Jual Toko (Rp) <span class="text-red-500">*</span></label>
                            <x-currency-input model="retail_price" />
                            @error('retail_price') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Deskripsi Produk</label>
                        <textarea wire:model="description" rows="2" placeholder="Deskripsi makanan..." class="textarea textarea-bordered w-full text-base rounded-xl focus:border-slate-900"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan Produk
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
