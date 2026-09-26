@props([
    'consignment',
    'copyTitle' => 'LEMBAR TOKO MITRA',
    'badgeClass' => 'bg-slate-900 text-white',
    'totalQty' => 0,
    'totalValue' => 0,
    'compact' => false,
])

<div
    class="border border-slate-300 rounded-xl p-5 sm:p-6 bg-white text-slate-900 print:border-slate-400 print:p-4 print:rounded-lg">
    <!-- Header Dokumen -->
    <div class="flex items-start justify-between border-b-2 border-slate-900 pb-3 mb-4">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-extrabold text-base print:bg-black print:text-white shrink-0">
                HF
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-xl font-black tracking-tight text-slate-900 print:text-lg uppercase">
                        {{ config('app.name', 'Halala Food') }}
                    </h1>
                    <span
                        class="text-xs font-semibold px-2 py-0.5 rounded-full {{ $badgeClass }} print:border print:border-slate-800 print:text-slate-900 print:bg-transparent">
                        {{ $copyTitle }}
                    </span>
                </div>
                <p class="text-xs text-slate-600 print:text-[10px]">
                    Produksi & Distribusi Makanan Olahan • Sistem Titip Jual / Konsinyasi
                </p>
            </div>
        </div>

        <div class="text-right">
            <h2 class="text-sm font-black tracking-wider text-slate-900 uppercase">
                SURAT TITIP BARANG
            </h2>
            <p class="text-xs font-mono font-bold text-slate-700 print:text-[11px] mt-0.5">
                No: {{ $consignment->consignment_number }}
            </p>
        </div>
    </div>

    <!-- Informasi Toko & Informasi Transaksi -->
    <div
        class="grid grid-cols-2 gap-4 text-xs sm:text-sm print:text-[11px] mb-4 bg-slate-50 p-3 rounded-lg border border-slate-200 print:bg-transparent print:p-2 print:border-slate-300">
        <!-- Kolom Kiri: Toko Penerima -->
        <div class="space-y-1">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Toko Mitra Penerima:</p>
            <p class="font-extrabold text-slate-900 text-sm print:text-xs leading-tight">{{ $consignment->store->name }}
            </p>
            <p class="text-slate-600">
                <span class="font-medium">Pemilik / Kontak:</span> {{ $consignment->store->owner_name ?: '-' }}
                {{ $consignment->store->phone ? '(' . $consignment->store->phone . ')' : '' }}
            </p>
            <p class="text-slate-600 truncate">
                <span class="font-medium">Alamat / Rute:</span> {{ $consignment->store->address ?: '-' }} @if ($consignment->store->route)
                    <span class="font-semibold">({{ $consignment->store->route }})</span>
                @endif
            </p>
        </div>

        <!-- Kolom Kanan: Detail Waktu & Pengantar -->
        <div class="space-y-1 text-right sm:text-left">
            <p class="text-[10px] font-bold uppercase tracking-wider text-slate-500">Detail Pengantaran:</p>
            <p class="text-slate-800">
                <span class="font-semibold text-slate-600">Tanggal Antar:</span>
                <span
                    class="font-bold text-slate-900">{{ $consignment->drop_date->translatedFormat('l, d F Y') }}</span>
            </p>
            <p class="text-slate-800">
                <span class="font-semibold text-slate-600">Status Titipan:</span>
                <span
                    class="font-bold">{{ $consignment->status === 'active' ? 'Sedang Dititipkan' : 'Sudah Selesai Ditagih' }}</span>
            </p>
            @if ($consignment->notes)
                <p class="text-slate-600 italic">
                    <span class="font-medium not-italic">Catatan:</span> {{ $consignment->notes }}
                </p>
            @endif
        </div>
    </div>

    <!-- Tabel Rincian Barang Titipan -->
    <div class="overflow-x-auto mb-3">
        <table class="w-full text-left text-xs sm:text-sm print:text-[11px] border-collapse border border-slate-300">
            <thead>
                <tr class="bg-slate-100 text-slate-900 font-bold border-b border-slate-300 print:bg-slate-50">
                    <th class="py-1.5 px-3 border-r border-slate-300 w-10 text-center">No.</th>
                    <th class="py-1.5 px-3 border-r border-slate-300">Nama Makanan / Produk</th>
                    <th class="py-1.5 px-3 border-r border-slate-300 text-center w-20">Satuan</th>
                    <th class="py-1.5 px-3 border-r border-slate-300 text-center w-24 bg-slate-200/60 font-black">Jumlah
                        Titip</th>
                    <th class="py-1.5 px-3 border-r border-slate-300 text-right w-28">Harga Setor</th>
                    <th class="py-1.5 px-3 text-right w-32">Total Nilai</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
                @foreach ($consignment->items as $index => $item)
                    @php
                        $subtotalItem = (int) $item->quantity_dropped * (float) $item->price_per_item;
                    @endphp
                    <tr class="hover:bg-slate-50/50 print:hover:bg-transparent">
                        <td class="py-1.5 px-3 border-r border-slate-300 text-center font-mono">{{ $index + 1 }}
                        </td>
                        <td class="py-1.5 px-3 border-r border-slate-300 font-bold text-slate-900">
                            {{ $item->product->name }}
                        </td>
                        <td
                            class="py-1.5 px-3 border-r border-slate-300 text-center uppercase font-mono text-slate-600 text-[11px]">
                            {{ $item->product->unit ?? 'pcs' }}
                        </td>
                        <td
                            class="py-1.5 px-3 border-r border-slate-300 text-center font-bold text-slate-900 bg-slate-50/50 print:bg-transparent text-sm print:text-xs">
                            {{ $item->quantity_dropped }}
                        </td>
                        <td class="py-1.5 px-3 border-r border-slate-300 text-right font-mono text-slate-700">
                            Rp {{ number_format($item->price_per_item, 0, ',', '.') }}
                        </td>
                        <td class="py-1.5 px-3 text-right font-mono font-bold text-slate-900">
                            Rp {{ number_format($subtotalItem, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-slate-100 font-extrabold border-t-2 border-slate-400 print:bg-slate-50">
                    <td colspan="3"
                        class="py-2 px-3 text-right uppercase tracking-wider text-xs border-r border-slate-300">
                        TOTAL TITIPAN:
                    </td>
                    <td
                        class="py-2 px-3 text-center text-sm print:text-xs font-black border-r border-slate-300 bg-slate-200/50">
                        {{ $totalQty }} pcs
                    </td>
                    <td class="py-2 px-3 text-right font-mono text-xs border-r border-slate-300">
                        -
                    </td>
                    <td class="py-2 px-3 text-right font-mono text-sm print:text-xs font-black text-slate-900">
                        Rp {{ number_format($totalValue, 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <!-- Ketentuan & Catatan Kecil -->
    <div class="text-[10px] text-slate-500 print:text-[9px] mb-4 space-y-0.5 border-l-2 border-slate-300 pl-2">
        <p>• <strong>Ketentuan:</strong> Barang titipan telah diperiksa dan diserahkan dalam kondisi baik, segar, dan
            layak jual.</p>
        <p>• Pengecekan sisa barang dan penagihan hasil penjualan akan dihitung saat kunjungan jadwal berikutnya.</p>
    </div>

    <!-- Tanda Tangan Serah Terima (2 Pihak) -->
    <div class="grid grid-cols-2 gap-6 pt-2 border-t border-slate-200 text-xs print:text-[10px] text-center">
        <!-- Kolom Pihak Toko Mitra -->
        <div class="flex flex-col items-center justify-between min-h-18.75 print:min-h-16.25">
            <p class="font-bold text-slate-800">
                Yang Menerima (Pihak Toko Mitra)
            </p>
            <div class="w-40 border-b border-dashed border-slate-500 mt-10 print:mt-8"></div>
            <p class="text-slate-500 text-[10px] mt-0.5">
                ( {{ $consignment->store->owner_name ?: 'Nama & Cap Toko' }} )
            </p>
        </div>

        <!-- Kolom Pihak Pengantar Halala Food -->
        <div class="flex flex-col items-center justify-between min-h-18.75 print:min-h-16.25">
            <p class="font-bold text-slate-800">
                Yang Menyerahkan (Halala Food)
            </p>
            <div class="w-40 border-b border-dashed border-slate-500 mt-10 print:mt-8"></div>
            <p class="text-slate-500 text-[10px] mt-0.5">
                ( Pengantar / Petugas Halala )
            </p>
        </div>
    </div>
</div>
