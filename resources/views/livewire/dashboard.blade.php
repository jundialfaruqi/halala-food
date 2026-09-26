<div class="space-y-8">
    <!-- Header Ringkasan & Tombol Aksi Cepat -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Ringkasan Usaha Halala Food
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Data terpusat untuk titip jual toko, stok makanan, dan pembukuan kas.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('consignments.create') }}" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm">
                <x-icon name="plus" class="text-xl" />
                <span>Titip Barang Baru</span>
            </a>
            <a href="{{ route('productions.index') }}" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 border border-slate-300 font-bold text-base rounded-xl px-4 gap-2">
                <x-icon name="tools-kitchen-2" class="text-xl" />
                <span>Catat Produksi</span>
            </a>
        </div>
    </div>

    <!-- Metrik Utama (Angka Besar & Jelas) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Kas Usaha -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Kas Usaha</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2 font-mono">
                Rp {{ number_format($totalBusinessCash, 0, ',', '.') }}
            </p>
            <p class="text-sm text-slate-500 mt-1">Kas tunai tagihan & rekening usaha</p>
        </div>

        <!-- Titipan Aktif di Toko -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Titipan di Toko</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2 font-mono">
                {{ $activeConsignmentsCount }} Toko
            </p>
            <p class="text-sm text-slate-500 mt-1">Estimasi nilai: Rp {{ number_format($totalGoodsInStoresValue, 0, ',', '.') }}</p>
        </div>

        <!-- Kas Pribadi / Keluarga (Terpisah) -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Kas Pribadi / Keluarga</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2 font-mono">
                Rp {{ number_format($totalPersonalCash, 0, ',', '.') }}
            </p>
            <p class="text-sm text-slate-500 mt-1">Uang belanja dapur & rumah tangga</p>
        </div>

        <!-- Total Mitra Toko -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Toko Mitra</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2 font-mono">
                {{ $totalStoresCount }} Toko
            </p>
            <p class="text-sm text-slate-500 mt-1">Aktif menjalin kerja sama titip jual</p>
        </div>
    </div>

    <!-- Grid 2 Kolom: Kunjungan Toko & Stok Produk/Bahan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Kolom Kiri (2 Span): Toko Yang Sudah Waktunya Dikunjungi / Ditagih -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200">
            <div class="flex items-center justify-between pb-4 border-b border-slate-200">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Perlu Kunjungan & Tagihan Toko</h2>
                    <p class="text-sm text-slate-500">Daftar toko yang sudah lebih dari 7 hari sejak barang dititip</p>
                </div>
                <a href="{{ route('consignments.index') }}" class="text-base font-bold text-slate-900 hover:underline">
                    Lihat Semua
                </a>
            </div>

            @if($dueConsignments->count() > 0)
                <div class="overflow-x-auto mt-4">
                    <table class="table w-full text-base">
                        <thead>
                            <tr class="border-b border-slate-200 text-slate-500 font-bold text-sm uppercase tracking-wider">
                                <th class="py-3 px-2">Nama Toko</th>
                                <th class="py-3 px-2">Rute</th>
                                <th class="py-3 px-2">Tgl Titip</th>
                                <th class="py-3 px-2 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($dueConsignments as $consignment)
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-4 px-2">
                                        <p class="font-bold text-slate-900 text-lg">{{ $consignment->store->name }}</p>
                                        <p class="text-sm text-slate-500">{{ $consignment->store->phone ?? 'Tanpa nomor telepon' }}</p>
                                    </td>
                                    <td class="py-4 px-2 text-slate-700 font-medium">
                                        {{ $consignment->store->route ?? '-' }}
                                    </td>
                                    <td class="py-4 px-2 font-mono text-slate-700">
                                        {{ $consignment->drop_date->format('d M Y') }}
                                        <span class="block text-xs text-slate-500">({{ $consignment->drop_date->diffForHumans() }})</span>
                                    </td>
                                    <td class="py-4 px-2 text-right">
                                        <a href="{{ route('consignments.edit', $consignment->id) }}" class="btn btn-sm bg-slate-900 hover:bg-black text-white font-bold rounded-lg px-4">
                                            Cek Sisa / Tagih
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-8 text-center text-slate-500">
                    <x-icon name="circle-check" class="text-3xl text-slate-400 mb-2" />
                    <p class="font-medium text-base">Semua toko saat ini dalam jadwal kunjungan yang aman.</p>
                </div>
            @endif
        </div>

        <!-- Kolom Kanan (1 Span): Stok Produk Jadi & Peringatan Bahan Baku -->
        <div class="space-y-6">
            <!-- Stok Produk Siap Antar -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Stok Produk Siap Antar</h2>
                    <a href="{{ route('products.index') }}" class="text-sm font-bold text-slate-900 hover:underline">Kelola</a>
                </div>

                <div class="space-y-4">
                    @foreach($products as $product)
                        <div class="flex items-center justify-between py-2 border-b border-slate-100 last:border-0">
                            <div>
                                <p class="font-bold text-slate-900 text-base">{{ $product->name }}</p>
                                <p class="text-xs text-slate-500">Titip: Rp {{ number_format($product->consignment_price, 0, ',', '.') }}</p>
                            </div>
                            <div class="text-right">
                                <span class="font-mono font-bold text-lg text-slate-900">{{ $product->stock_ready }}</span>
                                <span class="text-xs text-slate-500 block">{{ $product->unit }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Bahan Baku Menipis -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200 mb-4">
                    <h2 class="text-lg font-bold text-slate-900">Peringatan Bahan Baku</h2>
                    <a href="{{ route('raw-materials.index') }}" class="text-sm font-bold text-slate-900 hover:underline">Lihat Semua</a>
                </div>

                @if($lowStockMaterials->count() > 0)
                    <div class="space-y-3">
                        @foreach($lowStockMaterials as $mat)
                            <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex items-center justify-between">
                                <div>
                                    <p class="font-bold text-slate-900 text-sm">{{ $mat->name }}</p>
                                    <p class="text-xs text-slate-500">Min. stok: {{ $mat->min_stock }} {{ $mat->unit }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="font-mono font-bold text-base text-slate-900">{{ $mat->stock }}</span>
                                    <span class="text-xs text-slate-500"> {{ $mat->unit }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-sm text-slate-500">Semua persediaan bahan baku masih dalam batas aman.</p>
                @endif
            </div>
        </div>
    </div>
</div>
