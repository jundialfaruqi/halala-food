<div class="space-y-6 max-w-4xl mx-auto">
    <!-- Header Page -->
    <div class="flex items-center justify-between gap-4 pb-6 border-b border-slate-200/80">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                @if($isEdit)
                    Cek Sisa & Penagihan Toko
                @else
                    Catat Titip Barang Baru
                @endif
            </h1>
            <p class="text-base text-slate-600 mt-1">
                @if($isEdit)
                    Toko: <span class="font-bold text-slate-900">{{ $consignment->store->name }}</span> ({{ $consignment->consignment_number }})
                @else
                    Pilih toko dan masukkan jumlah produk yang dititipkan hari ini.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2.5">
            @if($isEdit)
                <a href="{{ route('consignments.print', $consignment->id) }}" target="_blank" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-4 text-sm shadow-sm flex items-center gap-2">
                    <x-icon name="printer" class="text-lg" />
                    <span>Cetak Surat Titip</span>
                </a>
            @endif
            <a href="{{ route('consignments.index') }}" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl px-4 text-base border border-slate-200 shadow-sm">
                Kembali
            </a>
        </div>
    </div>

    @if(!$isEdit)
        <!-- Form: Titip Barang Baru -->
        <form wire:submit="saveDrop" class="space-y-6">
            <!-- Informasi Toko & Tanggal -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-5">
                <h2 class="text-lg font-bold text-slate-900 pb-2 border-b border-slate-200">
                    1. Informasi Toko & Tanggal
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Pilih Toko -->
                    <div>
                        <label class="block text-base font-bold text-slate-800 mb-1">
                            Pilih Toko Mitra <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="store_id" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 bg-slate-50 focus:bg-white h-12">
                            <option value="">-- Pilih Salah Satu Toko --</option>
                            @foreach($stores as $s)
                                <option value="{{ $s->id }}">
                                    {{ $s->name }} ({{ $s->route ?? 'Tanpa Rute' }})
                                </option>
                            @endforeach
                        </select>
                        @error('store_id') <span class="text-sm font-semibold text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Tanggal Titip -->
                    <div>
                        <label class="block text-base font-bold text-slate-800 mb-1">
                            Tanggal Titip Barang <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="drop_date" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900 bg-slate-50 focus:bg-white h-12" />
                        @error('drop_date') <span class="text-sm font-semibold text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Jumlah Barang yang Dititipkan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-5">
                <h2 class="text-lg font-bold text-slate-900 pb-2 border-b border-slate-200">
                    2. Jumlah Makanan yang Dititipkan
                </h2>

                <div class="space-y-6 pt-2">
                    @forelse($items as $index => $item)
                        <div x-data="{
                                initialStock: {{ (int) ($item['stock_ready'] ?? 0) }},
                                dropped: {{ (int) ($item['quantity_dropped'] ?? 0) }},
                                unit: {{ json_encode($item['unit'] ?? 'pcs') }},
                                get remainingStock() {
                                    const qty = parseInt(this.dropped, 10);
                                    const used = isNaN(qty) ? 0 : qty;
                                    return this.initialStock - used;
                                }
                            }"
                            class="relative flex flex-col sm:flex-row sm:items-center justify-between p-4 pt-5 rounded-xl border gap-4 transition-all duration-150"
                            :class="{
                                'bg-red-50/50 border-red-300': remainingStock < 0,
                                'bg-emerald-50/30 border-emerald-300': remainingStock >= 0 && dropped > 0,
                                'bg-slate-50 border-slate-200': dropped <= 0 || isNaN(parseInt(dropped))
                            }">
                            <!-- Floating Badge di Atas Border Card Item -->
                            <div class="absolute -top-3 left-4 flex items-center z-10">
                                <span x-show="remainingStock > 0 && (!dropped || dropped == 0)"
                                      class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 shadow-xs ring-2 ring-white">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                    Stok Siap: {{ $item['stock_ready'] }} {{ $item['unit'] ?? 'pcs' }}
                                </span>

                                <span x-show="remainingStock > 0 && dropped > 0"
                                      x-cloak
                                      class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-emerald-600 text-white border border-emerald-700 shadow-xs ring-2 ring-white">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span>Sisa Stok: <strong x-text="remainingStock + ' ' + unit"></strong> <span class="font-normal opacity-90">(Awal: {{ $item['stock_ready'] }})</span></span>
                                </span>

                                <span x-show="remainingStock === 0"
                                      x-cloak
                                      class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-amber-500 text-white border border-amber-600 shadow-xs ring-2 ring-white">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span>Stok Pas Habis (0 <span x-text="unit"></span>)</span>
                                </span>

                                <span x-show="remainingStock < 0"
                                      x-cloak
                                      class="inline-flex items-center gap-1.5 px-3 py-0.5 rounded-full text-xs font-bold bg-red-600 text-white border border-red-700 shadow-xs ring-2 ring-white animate-pulse">
                                    <span class="w-1.5 h-1.5 rounded-full bg-white"></span>
                                    <span>Melebihi Stok! (Kurang <strong x-text="Math.abs(remainingStock) + ' ' + unit"></strong>)</span>
                                </span>
                            </div>

                            <div class="flex-1 space-y-1">
                                <p class="text-lg font-bold text-slate-900">{{ $item['product_name'] }}</p>
                                <p class="text-sm font-medium text-slate-500">
                                    Harga Titip: <strong class="text-slate-800">Rp {{ number_format($item['price_per_item'], 0, ',', '.') }}</strong> / {{ $item['unit'] ?? 'pcs' }}
                                </p>
                            </div>

                            <div class="flex flex-col items-end">
                                <div class="flex items-center gap-3">
                                    <label class="text-sm font-bold text-slate-700">Jumlah Titip:</label>
                                    <div class="w-32">
                                        <input type="number"
                                               wire:model="items.{{ $index }}.quantity_dropped"
                                               x-model.number="dropped"
                                               min="0"
                                               class="input input-bordered w-full text-center font-bold text-lg rounded-xl h-12 bg-white focus:border-slate-900 transition-colors"
                                               :class="{ 'border-red-500 text-red-700 focus:border-red-600': remainingStock < 0 }" />
                                    </div>
                                    <span class="text-sm font-bold text-slate-600 min-w-10">{{ $item['unit'] ?? 'pcs' }}</span>
                                </div>
                                @error('items.'.$index.'.quantity_dropped')
                                    <span class="text-xs text-red-600 font-semibold mt-1">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    @empty
                        <div class="p-6 bg-slate-50 rounded-xl text-center">
                            <p class="text-base text-slate-600 font-medium">Belum ada data produk makanan.</p>
                            <a href="{{ route('products.index') }}" class="btn btn-sm bg-slate-900 text-white font-bold rounded-lg mt-2">
                                + Buat Produk Dulu
                            </a>
                        </div>
                    @endforelse
                </div>
                @error('items') <span class="text-sm font-semibold text-red-600 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Catatan Tambahan -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200">
                <label class="block text-base font-bold text-slate-800 mb-1">Catatan Tambahan (Opsional)</label>
                <textarea wire:model="notes" rows="2" placeholder="Misal: Ditaruh di rak dekat kasir..." class="textarea textarea-bordered w-full text-base rounded-xl focus:border-slate-900 bg-slate-50 focus:bg-white"></textarea>
            </div>

            <!-- Tombol Simpan -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" class="btn btn-lg bg-slate-900 hover:bg-black text-white font-bold text-lg rounded-xl px-8 shadow-sm">
                    Simpan & Catat Titipan
                </button>
            </div>
        </form>

    @else
        <!-- Form: Audit / Cek Sisa & Penagihan Uang Toko -->
        <form wire:submit="saveSettlement" class="space-y-6">
            <!-- Rincian Cek Sisa Barang di Toko -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-5">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-3 border-b border-slate-200 gap-2">
                    <div>
                        <h2 class="text-xl font-bold text-slate-900">Hitung Sisa di Rak Toko</h2>
                        <p class="text-sm text-slate-500">Masukkan sisa fisik barang di toko, sistem akan menghitung yang laku.</p>
                    </div>
                    <div>
                        <label class="text-xs font-bold text-slate-500 uppercase block">Tanggal Jemput/Cek</label>
                        <input type="date" wire:model="settlement_date" class="input input-sm input-bordered font-mono font-bold rounded-lg text-slate-900" />
                        @error('settlement_date') <span class="text-xs font-semibold text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-base">
                        <thead>
                            <tr class="bg-slate-50 text-slate-600 font-bold text-sm uppercase">
                                <th class="py-3 px-3">Nama Menu</th>
                                <th class="py-3 px-3 text-center">Titip Awal</th>
                                <th class="py-3 px-3 text-center">Sisa di Toko</th>
                                <th class="py-3 px-3 text-center">Retur/Rusak</th>
                                <th class="py-3 px-3 text-center">Laku Terjual</th>
                                <th class="py-3 px-3 text-right">Subtotal Uang</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($items as $index => $item)
                                <tr>
                                    <td class="py-4 px-3 font-bold text-slate-900 text-lg">
                                        {{ $item['product_name'] }}
                                        <span class="block text-xs font-normal text-slate-500">@ Rp {{ number_format($item['price_per_item'], 0, ',', '.') }} / {{ $item['unit'] ?? 'pcs' }}</span>
                                    </td>
                                    <td class="py-4 px-3 text-center font-mono font-bold text-lg text-slate-600">
                                        {{ $item['quantity_dropped'] }} <span class="text-xs font-normal text-slate-500">{{ $item['unit'] ?? 'pcs' }}</span>
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <input type="number"
                                               wire:model.live="items.{{ $index }}.quantity_remaining"
                                               min="0"
                                               max="{{ $item['quantity_dropped'] }}"
                                               class="input input-bordered w-24 text-center font-mono font-bold text-lg rounded-xl h-11 border-slate-300 focus:border-slate-900" />
                                    </td>
                                    <td class="py-4 px-3 text-center">
                                        <input type="number"
                                               wire:model.live="items.{{ $index }}.quantity_returned"
                                               min="0"
                                               max="{{ $item['quantity_dropped'] }}"
                                               class="input input-bordered w-20 text-center font-mono font-bold text-lg rounded-xl h-11 border-slate-300 focus:border-slate-900" />
                                    </td>
                                    <td class="py-4 px-3 text-center font-mono font-bold text-xl text-slate-900">
                                        {{ $item['quantity_sold'] }} <span class="text-xs font-normal text-slate-500">{{ $item['unit'] ?? 'pcs' }}</span>
                                    </td>
                                    <td class="py-4 px-3 text-right font-mono font-bold text-lg text-slate-900">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold text-lg border-t-2 border-slate-300">
                                <td colspan="5" class="py-4 px-3 text-right text-slate-800">TOTAL HASIL PENJUALAN:</td>
                                <td class="py-4 px-3 text-right font-mono text-2xl text-slate-900">
                                    Rp {{ number_format($totalSoldAmount, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Pembayaran & Akun Kas Masuk -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-5">
                <h2 class="text-xl font-bold text-slate-900 pb-2 border-b border-slate-200">
                    Penerimaan Pembayaran dari Toko
                </h2>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <!-- Akun Kas Masuk -->
                    <div>
                        <label class="block text-base font-bold text-slate-800 mb-1">
                            Masukkan ke Akun Kas <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="account_id" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 bg-slate-50 focus:bg-white h-12">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">{{ $acc->name }} (Saldo: Rp {{ number_format($acc->balance, 0, ',', '.') }})</option>
                            @endforeach
                        </select>
                        @error('account_id') <span class="text-sm font-semibold text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Jumlah Uang Yang Diterima -->
                    <div>
                        <label class="block text-base font-bold text-slate-800 mb-1">
                            Jumlah Uang Yang Disetor Toko (Rp) <span class="text-red-500">*</span>
                        </label>
                        <x-currency-input model="amount_paid" size="text-xl" class="h-12 bg-slate-50 focus-within:bg-white" />
                        @error('amount_paid') <span class="text-sm font-semibold text-red-600 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Tombol Selesaikan -->
            <div class="flex justify-end gap-3 pt-2">
                <button type="submit" class="btn btn-lg bg-slate-900 hover:bg-black text-white font-bold text-lg rounded-xl px-8 shadow-sm">
                    Selesaikan Tagihan & Catat ke Kas
                </button>
            </div>
        </form>
    @endif
</div>
