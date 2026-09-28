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
            <a href="{{ route('accounting.journals') }}"
                class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 font-bold text-base rounded-xl px-4">
                Kembali ke Jurnal Umum
            </a>
            <a href="{{ route('accounting.financial-statements') }}"
                class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-4">
                Laporan Keuangan
            </a>
        </div>
    </div>

    <!-- Panduan & Tips Praktis Membaca Buku Besar (Minimalis & Elegan) -->
    <div x-data="{ openGuide: false }"
        class="bg-slate-100/80 rounded-2xl border border-slate-200 p-4 sm:p-5 text-slate-800 shadow-2xs">
        <div class="flex items-center justify-between cursor-pointer select-none" @click="openGuide = !openGuide">
            <div class="flex items-center gap-3">
                <div
                    class="w-9 h-9 rounded-xl bg-white border border-slate-200 text-slate-700 flex items-center justify-center font-bold shrink-0 shadow-2xs">
                    <x-icon name="info-circle" class="text-xl text-slate-700" />
                </div>
                <div>
                    <h3 class="font-bold text-base text-slate-900">
                        Panduan Cara Membaca Buku Besar & Kolom Debet/Kredit
                    </h3>
                    <p class="text-sm text-slate-500 mt-0.5">
                        Klik di sini untuk melihat panduan singkat fungsi akun perkiraan dan arti kolom debet vs kredit.
                    </p>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-ghost btn-circle text-slate-600 shrink-0">
                <x-icon name="chevron-down" class="text-xl transition-transform duration-200" ::class="openGuide ? 'rotate-180' : ''" />
            </button>
        </div>

        <div x-show="openGuide" x-transition
            class="mt-4 pt-4 border-t border-slate-200 space-y-4 text-sm leading-relaxed">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Poin 1: Apa itu Buku Besar -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 space-y-1.5 shadow-2xs">
                    <h4 class="font-bold text-slate-900 flex items-center gap-2 text-sm">
                        <span
                            class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold">1</span>
                        <span>Apa Itu Buku Besar?</span>
                    </h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Buku Besar merangkum seluruh riwayat mutasi keluar-masuk khusus untuk <strong>satu akun yang
                            Anda pilih</strong> (misal hanya Kas Tunai atau hanya Penjualan) secara berurutan waktu.
                    </p>
                </div>

                <!-- Poin 2: Arti Debet & Kredit -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 space-y-1.5 shadow-2xs">
                    <h4 class="font-bold text-slate-900 flex items-center gap-2 text-sm">
                        <span
                            class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold">2</span>
                        <span>Arti Kolom Debet & Kredit</span>
                    </h4>
                    <ul class="text-sm text-slate-600 space-y-1 leading-relaxed">
                        <li>• <strong>Kas / Bahan / Biaya</strong>: <strong>Debet</strong> = Masuk/Tambah (+),
                            <strong>Kredit</strong> = Keluar/Kurang (-).
                        </li>
                        <li>• <strong>Penjualan / Modal / Utang</strong>: <strong>Kredit</strong> = Omset/Modal
                            Bertambah (+), <strong>Debet</strong> = Berkurang (-).</li>
                    </ul>
                </div>

                <!-- Poin 3: Cara Membaca Tabel -->
                <div class="bg-white p-4 rounded-xl border border-slate-200/90 space-y-1.5 shadow-2xs">
                    <h4 class="font-bold text-slate-900 flex items-center gap-2 text-sm">
                        <span
                            class="w-5 h-5 rounded-full bg-slate-900 text-white flex items-center justify-center text-xs font-bold">3</span>
                        <span>Kolom Saldo Berjalan</span>
                    </h4>
                    <p class="text-sm text-slate-600 leading-relaxed">
                        Kolom <strong>Saldo Berjalan</strong> di ujung kanan memperlihatkan sisa total uang/nilai bersih
                        akun tersebut setelah transaksi pada baris tersebut selesai dihitung.
                    </p>
                </div>
            </div>
        </div>
    </div>

    @if ($accounts->isEmpty())
        <!-- EMPTY STATE UTAMA: Jika Belum Ada Bagan Akun (Chart of Accounts) -->
        <div
            class="bg-white rounded-2xl border border-slate-200 p-10 sm:p-14 text-center max-w-2xl mx-auto shadow-xs space-y-5">
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
                <button wire:click="initDefaultAccounts" type="button"
                    class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 flex items-center gap-2 shadow-xs cursor-pointer">
                    <x-icon name="plus" class="text-lg" />
                    <span>Inisialisasi Akun Standar</span>
                </button>
                <a href="{{ route('accounting.journals') }}"
                    class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 border border-slate-300 font-bold text-base rounded-xl px-5">
                    Buka Jurnal Umum
                </a>
            </div>
        </div>
    @else
        <!-- Pemilihan Akun & Filter Periode (Telanjang / Tanpa Card Pembungkus) -->
        <div class="flex flex-col md:flex-row gap-4 justify-between items-stretch md:items-center">
            <!-- Pilih Akun -->
            <div class="flex-1 max-w-xl">
                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">
                    Pilih Akun Perkiraan:
                </label>
                <select wire:model.live="selectedAccountId"
                    class="select select-md select-bordered w-full text-base font-bold rounded-xl bg-white border-slate-300 focus:border-slate-900 shadow-2xs">
                    @php
                        $grouped = $accounts->groupBy('type');
                        $typeLabels = [
                            'asset' => '1. Harta & Kas Usaha (Aset)',
                            'liability' => '2. Kewajiban & Hutang (Liabilitas)',
                            'equity' => '3. Modal Usaha (Ekuitas)',
                            'revenue' => '4. Pendapatan & Penjualan (Omset)',
                            'cogs' => '5. Harga Pokok Penjualan (HPP)',
                            'expense' => '6. Biaya Operasional (Beban)',
                        ];
                    @endphp
                    @foreach ($typeLabels as $typeKey => $groupTitle)
                        @if (isset($grouped[$typeKey]) && $grouped[$typeKey]->isNotEmpty())
                            <optgroup label="{{ $groupTitle }}">
                                @foreach ($grouped[$typeKey] as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->code }} - {{ $acc->name }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endif
                    @endforeach
                </select>
            </div>

            <!-- Filter Periode Cepat (Apple-style Segmented Control) -->
            <div class="flex items-center gap-3 flex-wrap self-start md:self-end">
                @php
                    $monthVal =
                        Carbon\Carbon::now()->startOfMonth()->format('Y-m-d') .
                        ' - ' .
                        Carbon\Carbon::now()->endOfMonth()->format('Y-m-d');
                    $yearVal =
                        Carbon\Carbon::now()->startOfYear()->format('Y-m-d') .
                        ' - ' .
                        Carbon\Carbon::now()->endOfYear()->format('Y-m-d');
                    $isMonth = $dateRange === $monthVal;
                    $isYear = $dateRange === $yearVal;
                    $isAll = empty($dateRange);
                @endphp
                <div class="inline-flex p-1 bg-slate-200/80 rounded-2xl border border-slate-300/60 shadow-xs">
                    <button type="button" wire:click="setQuickDate('this_month')"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer flex items-center gap-1.5 {{ $isMonth ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <span>Bulan Ini</span>
                    </button>
                    <button type="button" wire:click="setQuickDate('this_year')"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer flex items-center gap-1.5 {{ $isYear ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <span>Tahun Ini</span>
                    </button>
                    <button type="button" wire:click="setQuickDate('all')"
                        class="px-4 py-2 rounded-xl text-xs sm:text-sm font-bold transition-all cursor-pointer flex items-center gap-1.5 {{ $isAll ? 'bg-white text-slate-900 shadow-sm' : 'text-slate-600 hover:text-slate-900' }}">
                        <span>Semua</span>
                    </button>
                </div>
            </div>
        </div>

        @if ($currentAccount)
            <!-- Penjelasan Konsep Akun yang Sedang Aktif Dipilih -->
            <div
                class="bg-white rounded-2xl border border-slate-200 p-4 sm:p-5 shadow-xs flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="space-y-1 flex-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="font-mono text-xs font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded-md">
                            {{ $currentAccount->code }}
                        </span>
                        <h2 class="text-lg sm:text-xl font-bold text-slate-900">
                            {{ $currentAccount->name }}
                        </h2>
                        @php
                            $typeName = match ($currentAccount->type) {
                                'asset' => 'Harta / Kas (Aset)',
                                'liability' => 'Kewajiban / Hutang',
                                'equity' => 'Modal Usaha (Ekuitas)',
                                'revenue' => 'Pendapatan / Omset',
                                'cogs' => 'HPP (Harga Pokok)',
                                'expense' => 'Biaya Operasional',
                                default => strtoupper($currentAccount->type),
                            };
                        @endphp
                        <span class="text-xs font-semibold text-slate-500">
                            • {{ $typeName }}
                        </span>
                    </div>
                    @if ($currentAccount->description)
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            {{ $currentAccount->description }}
                        </p>
                    @endif
                </div>

                <!-- Petunjuk Debet / Kredit untuk Akun Ini -->
                <div
                    class="bg-slate-50 px-4 py-2.5 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700 space-y-1 shrink-0">
                    <p>
                        <strong class="text-slate-900">Kolom Debet:</strong>
                        {{ $currentAccount->normal_balance === 'debit' ? 'Nilai / Uang Masuk (+)' : 'Pengurang Nilai (-)' }}
                    </p>
                    <p>
                        <strong class="text-slate-900">Kolom Kredit:</strong>
                        {{ $currentAccount->normal_balance === 'credit' ? 'Modal / Omset Bertambah (+)' : 'Nilai / Uang Keluar (-)' }}
                    </p>
                </div>
            </div>

            <!-- Ringkasan Akun -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Total Debet
                        {{ $currentAccount->normal_balance === 'debit' ? '(Masuk / Tambah)' : '(Pengurang)' }}
                    </p>
                    <p class="text-2xl sm:text-3xl font-extrabold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalDebit, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Total Kredit
                        {{ $currentAccount->normal_balance === 'debit' ? '(Keluar / Kurang)' : '(Bertambah / Masuk)' }}
                    </p>
                    <p class="text-2xl sm:text-3xl font-extrabold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($totalCredit, 0, ',', '.') }}
                    </p>
                </div>
                <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-xs">
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider">
                        Saldo Akhir (Saldo Bersih Terkini)
                    </p>
                    <p class="text-2xl sm:text-3xl font-extrabold font-mono text-slate-900 mt-1">
                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                    </p>
                </div>
            </div>

            <!-- Tabel Mutasi Buku Besar -->
            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden shadow-xs">
                <div class="overflow-x-auto">
                    <table class="table w-full text-base">
                        <thead>
                            <tr
                                class="bg-slate-100 text-slate-700 font-bold text-sm uppercase tracking-wider border-b border-slate-200">
                                <th class="py-3 px-4 w-32">Tanggal</th>
                                <th class="py-3 px-4 w-36">No. Jurnal</th>
                                <th class="py-3 px-4">Keterangan Transaksi</th>
                                <th class="py-3 px-4 text-right w-44">
                                    <span>Debet (Rp)</span>
                                    <span class="text-[11px] block font-normal text-slate-500 lowercase">
                                        {{ $currentAccount->normal_balance === 'debit' ? '(+) nilai masuk' : '(-) pengurang' }}
                                    </span>
                                </th>
                                <th class="py-3 px-4 text-right w-44">
                                    <span>Kredit (Rp)</span>
                                    <span class="text-[11px] block font-normal text-slate-500 lowercase">
                                        {{ $currentAccount->normal_balance === 'debit' ? '(-) nilai keluar' : '(+) nilai bertambah' }}
                                    </span>
                                </th>
                                <th class="py-3 px-4 text-right w-48">
                                    <span>Saldo Berjalan (Rp)</span>
                                    <span class="text-[11px] block font-normal text-slate-500 lowercase">
                                        akumulasi saldo
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @php
                                $calcBalance = (float) $openingBalance;
                            @endphp
                            @if ($openingBalance != 0)
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
                                        $calcBalance += (float) $item->debit - (float) $item->credit;
                                    } else {
                                        $calcBalance += (float) $item->credit - (float) $item->debit;
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
                                        <span
                                            class="font-medium">{{ $item->memo ?: $item->journalEntry->notes }}</span>
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
                                            <div
                                                class="w-12 h-12 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto text-slate-400">
                                                <x-icon name="clipboard-text" class="text-2xl" />
                                            </div>
                                            <div>
                                                <p class="text-base font-bold text-slate-800">Belum Ada Mutasi
                                                    Transaksi</p>
                                                <p class="text-sm text-slate-500 mt-1">
                                                    @if (!empty($dateRange))
                                                        Tidak ada transaksi debet maupun kredit pada akun <span
                                                            class="font-bold text-slate-700">{{ $currentAccount->name }}</span>
                                                        untuk periode <span class="font-bold text-slate-700">"Bulan
                                                            Ini"</span>.
                                                    @else
                                                        Akun <span
                                                            class="font-bold text-slate-700">{{ $currentAccount->name }}</span>
                                                        belum memiliki pergerakan transaksi debet maupun kredit.
                                                    @endif
                                                </p>
                                            </div>
                                            @if (!empty($dateRange))
                                                <div class="pt-1">
                                                    <button type="button" wire:click="setQuickDate('all')"
                                                        class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg border border-slate-300">
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
