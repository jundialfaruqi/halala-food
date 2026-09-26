<div class="space-y-6">
    <!-- Header Page & Tombol Titip Baru -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Titip Jual / Konsinyasi Toko
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Catatan barang yang sedang dititipkan di toko mitra dan proses penagihan hasil penjualan.
            </p>
        </div>
        <a href="{{ route('consignments.create') }}" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm">
            <x-icon name="plus" class="text-xl" />
            <span>Titip Barang Baru</span>
        </a>
    </div>

    <!-- Filter & Pencarian (Besar & Mudah Dibaca) -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-3 gap-4">
        <!-- Pencarian Toko -->
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Cari Toko / No. Titipan</label>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Ketik nama toko..."
                   class="input input-bordered w-full text-base rounded-xl focus:border-slate-900 focus:outline-none bg-slate-50 focus:bg-white" />
        </div>

        <!-- Filter Rute Toko -->
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Pilih Rute / Wilayah</label>
            <select wire:model.live="routeFilter" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 focus:outline-none bg-slate-50 focus:bg-white">
                <option value="">Semua Rute</option>
                @foreach($routes as $route)
                    <option value="{{ $route }}">{{ $route }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter Status -->
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Status Titipan</label>
            <select wire:model.live="statusFilter" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 focus:outline-none bg-slate-50 focus:bg-white">
                <option value="active">Sedang Dititip (Aktif)</option>
                <option value="completed">Selesai / Sudah Ditagih</option>
                <option value="all">Semua Status</option>
            </select>
        </div>
    </div>

    <!-- Tabel Daftar Titipan -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-base">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-sm uppercase tracking-wider">
                        <th class="py-4 px-6">Toko Mitra</th>
                        <th class="py-4 px-4">Tanggal Titip</th>
                        <th class="py-4 px-4">Rincian Barang</th>
                        <th class="py-4 px-4">Status & Hasil</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($consignments as $consignment)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <!-- Kolom Toko -->
                            <td class="py-5 px-6">
                                <p class="font-bold text-lg text-slate-900 leading-tight">
                                    {{ $consignment->store->name }}
                                </p>
                                <p class="text-sm text-slate-500 mt-0.5">
                                    {{ $consignment->store->route ?? 'Tanpa Rute' }} • {{ $consignment->store->phone ?? '-' }}
                                </p>
                                <p class="text-xs font-mono text-slate-400 mt-1">
                                    {{ $consignment->consignment_number }}
                                </p>
                            </td>

                            <!-- Kolom Tanggal -->
                            <td class="py-5 px-4 font-mono">
                                <p class="font-bold text-slate-900 text-base">
                                    {{ $consignment->drop_date->format('d/m/Y') }}
                                </p>
                                <p class="text-xs text-slate-500">
                                    {{ $consignment->drop_date->diffForHumans() }}
                                </p>
                                @if($consignment->settlement_date)
                                    <p class="text-xs text-slate-400 mt-1">
                                        Cek: {{ $consignment->settlement_date->format('d/m/Y') }}
                                    </p>
                                @endif
                            </td>

                            <!-- Kolom Rincian Barang -->
                            <td class="py-5 px-4">
                                <div class="space-y-1">
                                    @foreach($consignment->items as $item)
                                        <div class="text-sm text-slate-800">
                                            <span class="font-bold">{{ $item->product->name }}:</span>
                                            Titip {{ $item->quantity_dropped }} pcs
                                            @if($item->quantity_remaining !== null)
                                                <span class="text-slate-500">(Sisa {{ $item->quantity_remaining }} • Laku {{ $item->quantity_sold }})</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>

                            <!-- Kolom Status & Uang -->
                            <td class="py-5 px-4">
                                @if($consignment->status === 'active')
                                    <p class="font-bold text-base text-slate-800">Sedang Dititip</p>
                                    <p class="text-xs text-slate-500 mt-0.5">Belum dihitung sisa</p>
                                @else
                                    <p class="font-bold text-lg text-slate-900 font-mono">
                                        Rp {{ number_format($consignment->total_net_received, 0, ',', '.') }}
                                    </p>
                                    <p class="text-xs text-slate-500 font-medium">
                                        Status: {{ $consignment->payment_status === 'paid' ? 'Lunas Diterima' : 'Sebagian/Belum Lunas' }}
                                    </p>
                                @endif
                            </td>

                            <!-- Kolom Aksi -->
                            <td class="py-5 px-6 text-right">
                                @if($consignment->status === 'active')
                                    <a href="{{ route('consignments.edit', $consignment->id) }}" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-5 text-sm shadow-sm">
                                        Cek Sisa & Tagih
                                    </a>
                                @else
                                    <a href="{{ route('consignments.edit', $consignment->id) }}" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-4 text-sm border border-slate-300">
                                        Lihat Rincian
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 text-base">
                                Tidak ada data titipan yang cocok dengan filter yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($consignments->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $consignments->links() }}
            </div>
        @endif
    </div>
</div>
