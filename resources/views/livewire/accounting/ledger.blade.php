<div class="space-y-6">
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900">
                Buku Besar Akuntansi
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Rincian mutasi debet, kredit, dan saldo akhir per akun perkiraan.
            </p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('accounting.journals') }}" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 font-bold text-base rounded-xl px-4">
                Kembali ke Jurnal Umum
            </a>
            <a href="{{ route('accounting.financial-statements') }}" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-4">
                Laporan Keuangan
            </a>
        </div>
    </div>

    @if($accounts->isEmpty())
        <!-- EMPTY STATE UTAMA: Jika Belum Ada Bagan Akun (Chart of Accounts) -->
        <div class="bg-white rounded-2xl border border-slate-200 p-10 sm:p-14 text-center max-w-2xl mx-auto shadow-xs space-y-5">
            <div class="w-20 h-20 bg-slate-100 rounded-3xl flex items-center justify-center mx-auto text-slate-400">
                <x-icon name="list-details" class="text-4xl" />
            </div>
            <div class="space-y-2">
                <h3 class="text-xl sm:text-2xl font-bold text-slate-900">
                    Belum Ada Data Buku Besar
                </h3>
                <p class="text-base text-slate-600 max-w-md mx-auto leading-relaxed">
                    Bagan akun perkiraan (Chart of Accounts) belum dibuat atau database baru saja di-reset.
                </p>
            </div>
            <div class="pt-2 flex flex-wrap justify-center items-center gap-3">
                <button wire:click="initDefaultAccounts" type="button" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 flex items-center gap-2 shadow-xs cursor-pointer">
                    <x-icon name="plus" class="text-lg" />
                    <span>Inisialisasi Akun Standar</span>
                </button>
                <a href="{{ route('accounting.journals') }}" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 font-bold text-base rounded-xl px-5">
                    Buka Jurnal Umum
                </a>
            </div>
        </div>
    @else
        <!-- Pemilihan Akun & Filter -->
        <div class="bg-white p-5 rounded-xl border border-slate-200 grid grid-cols-1 md:grid-cols-2 gap-4 items-center">
            <!-- Pilih Akun -->
            <div>
                <label class="block text-sm font-bold text-slate-700 uppercase tracking-wider mb-1">Pilih Akun Perkiraan:</label>
                <select wire:model.live="selectedAccountId" class="select select-bordered w-full text-base font-bold rounded-xl h-12 bg-slate-50 focus:bg-white focus:border-slate-900">
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">
                            {{ $acc->code }} - {{ $acc->name }} ({{ strtoupper($acc->type) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter Periode Cepat -->
            <div class="flex flex-wrap items-center justify-start md:justify-end gap-2 pt-2 md:pt-6">
                @php
                    $isMonth = !empty($dateRange);
                    $isAll = empty($dateRange);
                @endphp
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
                    <span>Semua Periode</span>
                </button>
            </div>
        </div>

        @if($currentAccount)
            <!-- Ringkasan Akun -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-xl border border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Debet Masuk</p>
                    <p class="text-2xl font-extrabold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalDebit, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">Total Kredit Keluar</p>
                    <p class="text-2xl font-extrabold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalCredit, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-slate-200">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Saldo Akhir (Saldo Normal: {{ strtoupper($currentAccount->normal_balance) }})
                    </p>
                    <p class="text-2xl font-extrabold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Tabel Mutasi Buku Besar -->
            <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                <div class="p-4 bg-slate-50 border-b border-slate-200">
                    <h2 class="text-lg font-bold text-slate-900">
                        Buku Besar: {{ $currentAccount->code }} - {{ $currentAccount->name }}
                    </h2>
                    @if($currentAccount->description)
                        <p class="text-sm text-slate-500 mt-0.5">{{ $currentAccount->description }}</p>
                    @endif
                </div>

                <div class="overflow-x-auto">
                    <table class="table w-full text-base">
                        <thead>
                            <tr class="bg-slate-100 text-slate-700 font-bold text-sm uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3 px-4 w-32">Tanggal</th>
                                <th class="py-3 px-4 w-36">No. Jurnal</th>
                                <th class="py-3 px-4">Keterangan Transaksi</th>
                                <th class="py-3 px-4 text-right w-40">Debet (Rp)</th>
                                <th class="py-3 px-4 text-right w-40">Kredit (Rp)</th>
                                <th class="py-3 px-4 text-right w-44">Saldo Berjalan (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @php
                                $calcBalance = (float) $openingBalance;
                            @endphp
                            @if($openingBalance != 0)
                                <tr class="bg-slate-50/60 font-medium italic text-slate-700">
                                    <td class="py-3 px-4 font-mono text-xs text-slate-500">-</td>
                                    <td class="py-3 px-4 font-mono text-xs text-slate-500">SALDO-AWAL</td>
                                    <td class="py-3 px-4 text-slate-700 font-semibold">
                                        Saldo Awal Sebelum Periode Ini
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-400">-</td>
                                    <td class="py-3 px-4 text-right font-mono text-slate-400">-</td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                        Rp {{ number_format($openingBalance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endif
                            @forelse($items as $item)
                                @php
                                    if ($currentAccount->normal_balance === 'debit') {
                                        $calcBalance += ((float) $item->debit - (float) $item->credit);
                                    } else {
                                        $calcBalance += ((float) $item->credit - (float) $item->debit);
                                    }
                                @endphp
                                <tr class="hover:bg-slate-50 transition-colors">
                                    <td class="py-3 px-4 font-mono text-slate-700 text-sm">
                                        {{ $item->journalEntry->entry_date->format('d/m/Y') }}
                                    </td>
                                    <td class="py-3 px-4 font-mono text-slate-900 font-bold text-sm">
                                        {{ $item->journalEntry->entry_number }}
                                    </td>
                                    <td class="py-3 px-4 text-slate-800">
                                        <span class="font-medium">{{ $item->memo ?: $item->journalEntry->notes }}</span>
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                        {{ $item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                        {{ $item->credit > 0 ? number_format($item->credit, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                        {{ number_format($calcBalance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="py-14 text-center">
                                        <div class="max-w-md mx-auto space-y-3">
                                            <div class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400">
                                                <x-icon name="clipboard-text" class="text-2xl" />
                                            </div>
                                            <div>
                                                <p class="text-base font-bold text-slate-800">Belum Ada Mutasi Transaksi</p>
                                                <p class="text-sm text-slate-500 mt-1">
                                                    @if(!empty($dateRange))
                                                        Tidak ada transaksi debet maupun kredit pada akun <span class="font-bold text-slate-700">{{ $currentAccount->name }}</span> untuk periode <span class="font-bold text-slate-700">"Bulan Ini"</span>.
                                                    @else
                                                        Akun <span class="font-bold text-slate-700">{{ $currentAccount->name }}</span> belum memiliki pergerakan transaksi debet maupun kredit.
                                                    @endif
                                                </p>
                                            </div>
                                            @if(!empty($dateRange))
                                                <div class="pt-1">
                                                    <button type="button" wire:click="setQuickDate('all')" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg border border-slate-300">
                                                        Tampilkan Semua Periode
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="bg-slate-50 font-bold text-base border-t-2 border-slate-300">
                                <td colspan="3" class="py-3 px-4 text-right text-slate-800">TOTAL MUTASI:</td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($totalDebit, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($totalCredit, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4 text-right font-mono font-bold text-slate-900">
                                    Rp {{ number_format($runningBalance, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        @endif
    @endif
</div>
