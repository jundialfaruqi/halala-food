<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                Jurnal Umum Akuntansi
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Catatan otomatis debet dan kredit dari seluruh transaksi usaha.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.ledger') }}" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 font-bold text-base rounded-xl px-4">
                Lihat Buku Besar
            </a>
            <a href="{{ route('accounting.financial-statements') }}" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-4">
                Laporan Keuangan
            </a>
        </div>
    </div>

    <!-- Ringkasan Keseimbangan Jurnal -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Debet</p>
            <p class="text-2xl sm:text-3xl font-extrabold font-mono text-slate-900 mt-1">
                Rp {{ number_format($totalDebit, 0, ',', '.') }}
            </p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-slate-200">
            <p class="text-sm font-bold text-slate-500 uppercase tracking-wider">Total Kredit</p>
            <p class="text-2xl sm:text-3xl font-extrabold font-mono text-slate-900 mt-1">
                Rp {{ number_format($totalCredit, 0, ',', '.') }}
            </p>
        </div>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-5 rounded-xl border border-slate-200 flex flex-col md:flex-row gap-4 justify-between items-stretch md:items-center">
        <!-- Pencarian -->
        <div class="flex-1 max-w-md">
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Cari nomor jurnal, akun, atau keterangan..."
                   class="input input-bordered w-full text-base rounded-xl h-11 bg-slate-50 focus:bg-white focus:border-slate-900" />
        </div>

        <!-- Filter Tanggal Cepat -->
        <div class="flex flex-wrap items-center gap-2">
            @php
                $todayVal = Carbon\Carbon::today()->format('Y-m-d').' - '.Carbon\Carbon::today()->format('Y-m-d');
                $monthVal = Carbon\Carbon::now()->startOfMonth()->format('Y-m-d').' - '.Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
                $isToday = $dateRange === $todayVal;
                $isMonth = $dateRange === $monthVal;
                $isAll = empty($dateRange);
            @endphp
            <button type="button"
                    wire:click="setQuickDate('today')"
                    class="btn btn-md {{ $isToday ? 'bg-slate-900 hover:bg-black text-white border-slate-900' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-300' }} rounded-xl font-bold text-base px-4 h-11 transition-all flex items-center gap-2">
                @if($isToday)
                    <x-icon name="check" class="text-lg text-white shrink-0" />
                @endif
                <span>Hari Ini</span>
            </button>
            <button type="button"
                    wire:click="setQuickDate('this_month')"
                    class="btn btn-md {{ $isMonth ? 'bg-slate-900 hover:bg-black text-white border-slate-900' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-300' }} rounded-xl font-bold text-base px-4 h-11 transition-all flex items-center gap-2">
                @if($isMonth)
                    <x-icon name="check" class="text-lg text-white shrink-0" />
                @endif
                <span>Bulan Ini</span>
            </button>
            <button type="button"
                    wire:click="setQuickDate('all')"
                    class="btn btn-md {{ $isAll ? 'bg-slate-900 hover:bg-black text-white border-slate-900' : 'bg-white hover:bg-slate-100 text-slate-700 border-slate-300' }} rounded-xl font-bold text-base px-4 h-11 transition-all flex items-center gap-2">
                @if($isAll)
                    <x-icon name="check" class="text-lg text-white shrink-0" />
                @endif
                <span>Semua</span>
            </button>
            @if(!empty($search) || !empty($dateRange))
                <button type="button" wire:click="resetFilters" class="btn btn-md btn-ghost text-slate-600 hover:text-slate-900 font-bold text-base h-11 px-3">
                    Reset
                </button>
            @endif
        </div>
    </div>

    <!-- Tabel Jurnal Umum -->
    <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-base">
                <thead>
                    <tr class="bg-slate-100 text-slate-700 font-bold text-sm uppercase tracking-wider border-b border-slate-200">
                        <th class="py-3 px-4 w-32">Tanggal</th>
                        <th class="py-3 px-4 w-40">No. Jurnal</th>
                        <th class="py-3 px-4">Keterangan & Akun Perkiraan</th>
                        <th class="py-3 px-4 text-right w-44">Debet (Rp)</th>
                        <th class="py-3 px-4 text-right w-44">Kredit (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    @forelse($entries as $entry)
                        <!-- Header Jurnal Entry -->
                        <tr class="bg-slate-50 font-bold border-t border-slate-200">
                            <td class="py-3 px-4 font-mono text-slate-800 align-top">
                                {{ $entry->entry_date->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-900 align-top">
                                {{ $entry->entry_number }}
                            </td>
                            <td colspan="3" class="py-3 px-4 text-slate-900 font-bold align-top">
                                {{ $entry->notes }}
                            </td>
                        </tr>

                        <!-- Baris Rincian Debet & Kredit -->
                        @foreach($entry->items as $item)
                            <tr class="hover:bg-slate-50/60 transition-colors">
                                <td class="py-2 px-4"></td>
                                <td class="py-2 px-4 font-mono text-xs text-slate-500">
                                    {{ $item->account->code }}
                                </td>
                                <td class="py-2 px-4 {{ $item->credit > 0 ? 'pl-10 text-slate-700' : 'text-slate-900 font-medium' }}">
                                    <span>{{ $item->account->name }}</span>
                                    @if($item->memo && $item->memo !== $entry->notes)
                                        <span class="block text-xs text-slate-500 font-normal mt-0.5">({{ $item->memo }})</span>
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-right font-mono font-bold text-slate-900">
                                    @if($item->debit > 0)
                                        {{ number_format($item->debit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-2 px-4 text-right font-mono font-bold text-slate-900">
                                    @if($item->credit > 0)
                                        {{ number_format($item->credit, 0, ',', '.') }}
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="5" class="py-10 text-center text-slate-500">
                                <p class="text-base font-medium">Belum ada catatan jurnal pada periode ini.</p>
                                <p class="text-sm text-slate-400 mt-1">Transaksi kas, produksi, dan penagihan toko akan otomatis tercatat di sini.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($entries->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
