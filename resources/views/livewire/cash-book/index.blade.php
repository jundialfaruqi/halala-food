<div class="space-y-6">
    <!-- Header Page -->
    <div class="pb-6 border-b border-slate-200/80">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
            Buku Kas & Keuangan
        </h1>
        <p class="text-base text-slate-600 mt-1">
            Pemisahan tegas antara uang hasil usaha makanan dan kebutuhan belanja pribadi keluarga.
        </p>
    </div>

    <!-- Tombol Aksi Kas -->
    <div class="flex flex-wrap items-center gap-2.5">
        <button wire:click="openAccountModal" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl px-4 text-base border border-slate-200 shadow-sm">
            + Tambah Akun Kas
        </button>
        <button wire:click="openTransactionModal()" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-4 text-base shadow-sm">
            + Transaksi Baru
        </button>
        <button wire:click="openTransactionModal('income')" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl px-4 text-base border border-slate-200 shadow-sm">
            + Pemasukan
        </button>
        <button wire:click="openTransactionModal('expense')" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl px-4 text-base border border-slate-200 shadow-sm">
            - Biaya Usaha
        </button>
        <button wire:click="openTransactionModal('prive')" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl px-4 text-base border border-slate-200 shadow-sm">
            ⇄ Tarik Prive Keluarga
        </button>
    </div>

    <!-- Kartu Saldo Kas (Usaha vs Pribadi) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Saldo Kas Usaha -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Total Saldo Kas Usaha</h2>
                    <p class="text-xs text-slate-500">Uang operasional & modal dagang</p>
                </div>
                <span class="text-xs font-bold uppercase text-slate-500">Murni Usaha</span>
            </div>

            <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-mono">
                Rp {{ number_format($totalBusinessBalance, 0, ',', '.') }}
            </p>

            <!-- Rincian Akun Usaha -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                @foreach($accounts->where('type', 'business') as $acc)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 font-medium">{{ $acc->name }}:</span>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Saldo Kas Pribadi / Keluarga -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200 space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                <div>
                    <h2 class="text-xl font-bold text-slate-900">Total Kas Pribadi / Keluarga</h2>
                    <p class="text-xs text-slate-500">Uang belanja dapur & rumah tangga</p>
                </div>
                <span class="text-xs font-bold uppercase text-slate-500">Non-Usaha</span>
            </div>

            <p class="text-3xl sm:text-4xl font-extrabold text-slate-900 font-mono">
                Rp {{ number_format($totalPersonalBalance, 0, ',', '.') }}
            </p>

            <!-- Rincian Akun Pribadi -->
            <div class="space-y-2 pt-2 border-t border-slate-100">
                @foreach($accounts->where('type', 'personal') as $acc)
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 font-medium">{{ $acc->name }}:</span>
                        <span class="font-mono font-bold text-slate-900">Rp {{ number_format($acc->balance, 0, ',', '.') }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Filter Bar Transaksi Kas (Unboxed Apple UI Style) -->
    <div class="space-y-3">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-y-4 lg:gap-y-0 lg:divide-x lg:divide-slate-200">
            <!-- 1. Pencarian Kategori / Keterangan -->
            <div class="lg:pr-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">Cari Kategori / Keterangan</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Misal: wijen, bensin, toko..."
                       class="input input-bordered w-full text-base rounded-xl focus:border-slate-900 bg-white" />
            </div>

            <!-- 2. Filter Jenis Transaksi -->
            <div class="lg:px-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">Jenis Transaksi</label>
                <select wire:model.live="typeFilter" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 bg-white">
                    <option value="all">Semua Jenis Transaksi</option>
                    <option value="income">Pemasukan Usaha (+)</option>
                    <option value="expense">Pengeluaran Usaha (-)</option>
                    <option value="prive">Tarik Prive Keluarga (⇄)</option>
                    <option value="personal_expense">Pengeluaran Pribadi (-)</option>
                </select>
            </div>

            <!-- 3. Filter Akun Kas -->
            <div class="lg:px-4">
                <label class="block text-sm font-bold text-slate-700 mb-1">Akun / Rekening Kas</label>
                <select wire:model.live="accountFilter" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 bg-white">
                    <option value="">Semua Rekening & Kas</option>
                    @foreach($accounts as $acc)
                        <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->type === 'business' ? 'Usaha' : 'Pribadi' }})</option>
                    @endforeach
                </select>
            </div>

            <!-- 4. Rentang Tanggal (1 Input Tunggal) -->
            <div class="lg:pl-4" wire:ignore
                 x-data="{
                    fp: null,
                    init() {
                        this.fp = flatpickr(this.$refs.picker, {
                            mode: 'range',
                            dateFormat: 'Y-m-d',
                            altInput: true,
                            altFormat: 'j M Y',
                            altInputClass: 'input input-bordered w-full text-base rounded-xl focus:border-slate-900 bg-white',
                            defaultDate: @js($dateRange ? explode(' - ', $dateRange) : null),
                            onClose: (selectedDates, dateStr) => {
                                $wire.set('dateRange', dateStr);
                            }
                        });
                        Livewire.hook('commit', () => {
                            let currentVal = @this.get('dateRange');
                            if (this.fp && currentVal !== this.fp.input.value) {
                                this.fp.setDate(currentVal ? currentVal.split(' - ') : null, false);
                            }
                        });
                    }
                 }">
                <label class="block text-sm font-bold text-slate-700 mb-1">Rentang Tanggal</label>
                <input x-ref="picker"
                       type="text"
                       placeholder="Pilih rentang tanggal..."
                       class="hidden" />
            </div>
        </div>

        <!-- Tombol Cepat Periode & Reset Filter -->
        <div class="flex flex-wrap items-center justify-between gap-3 pt-1">
            <div class="flex items-center gap-2">
                <span class="text-xs font-bold text-slate-500 uppercase">Periode Cepat:</span>
                <button type="button" wire:click="setQuickDate('today')" class="btn btn-xs bg-white hover:bg-slate-100 text-slate-800 font-semibold rounded-lg border border-slate-200 shadow-xs">
                    Hari Ini
                </button>
                <button type="button" wire:click="setQuickDate('this_month')" class="btn btn-xs bg-white hover:bg-slate-100 text-slate-800 font-semibold rounded-lg border border-slate-200 shadow-xs">
                    Bulan Ini
                </button>
                <button type="button" wire:click="setQuickDate('all')" class="btn btn-xs bg-white hover:bg-slate-100 text-slate-800 font-semibold rounded-lg border border-slate-200 shadow-xs">
                    Semua Waktu
                </button>
            </div>

            @if($hasActiveFilters)
                <button type="button" wire:click="resetFilters" class="text-xs font-bold text-red-600 hover:text-red-800 underline">
                    ✕ Reset Semua Filter
                </button>
            @endif
        </div>
    </div>

    <!-- Ringkasan Nominal Hasil Filter (Jika Filter Aktif) -->
    @if($hasActiveFilters)
        <div class="bg-slate-100 p-4 rounded-2xl border border-slate-200 flex flex-wrap items-center justify-between gap-4 text-sm font-medium">
            <div class="flex items-center gap-2 text-slate-600">
                <span class="font-bold text-slate-900">Hasil Filter:</span>
                <span>{{ $transactions->total() }} transaksi ditemukan</span>
            </div>
            <div class="flex flex-wrap items-center gap-6">
                <div>
                    <span class="text-slate-500">Pemasukan: </span>
                    <span class="font-mono font-bold text-slate-900">+ Rp {{ number_format($filteredIncome, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Pengeluaran Usaha/Pribadi: </span>
                    <span class="font-mono font-bold text-slate-900">- Rp {{ number_format($filteredExpense, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="text-slate-500">Prive Keluarga: </span>
                    <span class="font-mono font-bold text-slate-900">⇄ Rp {{ number_format($filteredPrive, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    @endif

    <!-- Tabel Riwayat Mutasi Kas -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-6 border-b border-slate-200">
            <h2 class="text-xl font-bold text-slate-900">Riwayat Mutasi Transaksi Kas</h2>
            <p class="text-sm text-slate-500">Catatan keluar masuk uang tunai & rekening</p>
        </div>

        <div class="overflow-x-auto">
            <table class="table w-full text-base">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-sm uppercase">
                        <th class="py-4 px-6 whitespace-nowrap">Tanggal</th>
                        <th class="py-4 px-4">Jenis & Kategori</th>
                        <th class="py-4 px-4">Akun Kas</th>
                        <th class="py-4 px-4">Keterangan</th>
                        <th class="py-4 px-6 text-right whitespace-nowrap">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm whitespace-nowrap">
                                {{ $trx->transaction_date->format('d/m/Y') }}
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-bold text-base text-slate-900">{{ $trx->category }}</p>
                                <p class="text-xs text-slate-500 uppercase font-semibold">
                                    @if($trx->type === 'income')
                                        Pemasukan Usaha
                                    @elseif($trx->type === 'expense')
                                        Pengeluaran Usaha
                                    @elseif($trx->type === 'prive')
                                        Tarik Prive Keluarga
                                    @else
                                        Pengeluaran Pribadi
                                    @endif
                                </p>
                            </td>
                            <td class="py-4 px-4 text-sm text-slate-700 font-medium">
                                {{ $trx->account->name }}
                            </td>
                            <td class="py-4 px-4 text-sm text-slate-600">
                                {{ $trx->description ?: '-' }}
                            </td>
                            <td class="py-4 px-6 text-right font-mono font-extrabold text-lg whitespace-nowrap {{ $trx->type === 'income' ? 'text-slate-900' : 'text-slate-600' }}">
                                {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 text-base">
                                @if($hasActiveFilters)
                                    Tidak ada transaksi kas yang sesuai dengan filter yang dipilih.
                                    <br>
                                    <button wire:click="resetFilters" class="btn btn-sm bg-slate-900 text-white font-bold rounded-lg mt-3">
                                        Reset Filter
                                    </button>
                                @else
                                    Belum ada transaksi kas yang dicatat.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $transactions->links('pagination.tailwind') }}
            </div>
        @endif
    </div>

    <!-- Modal Form Transaksi Kas -->
    <!-- Modal Form Transaksi Kas -->
    @if($showTransactionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
            <div class="bg-white w-full max-w-xl max-h-[90vh] flex flex-col rounded-2xl border border-slate-300 shadow-2xl overflow-hidden">
                <!-- Header Sticky -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 bg-slate-50 shrink-0">
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900">
                            Catat Transaksi Kas
                        </h3>
                        <p class="text-xs sm:text-sm text-slate-600 mt-0.5">Catat pemasukan atau pengeluaran kas usaha Anda</p>
                    </div>
                    <button wire:click="$set('showTransactionModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-2xl p-1 leading-none">
                        &times;
                    </button>
                </div>

                <!-- Form Body Scrollable -->
                <form wire:submit="prepareTransactionConfirmation" class="flex flex-col flex-1 overflow-hidden min-h-0">
                    <div class="p-6 overflow-y-auto space-y-5 flex-1">
                        
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                    Tanggal Transaksi <span class="text-red-600">*</span>
                                </label>
                                <input type="date" wire:model="transaction_date" class="input input-bordered w-full font-bold text-base h-12 rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900" />
                                @error('transaction_date') <span class="text-xs sm:text-sm text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                    Jenis Transaksi <span class="text-red-600">*</span>
                                </label>
                                <select wire:model.live="type" class="select select-bordered w-full font-bold text-base h-12 rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900">
                                    <option value="expense">Pengeluaran Usaha</option>
                                    <option value="income">Pemasukan Usaha</option>
                                    <option value="prive">Tarik Uang untuk Keluarga (Prive)</option>
                                    <option value="personal_expense">Pengeluaran Pribadi</option>
                                </select>
                                @error('type') <span class="text-xs sm:text-sm text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <div>
                            <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                Pilih Rekening / Kas <span class="text-red-600">*</span>
                            </label>
                            <select wire:model="account_id" class="select select-bordered w-full font-bold text-base h-12 rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900">
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">
                                        {{ $acc->name }} (Saldo: Rp {{ number_format($acc->balance, 0, ',', '.') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('account_id') <span class="text-xs sm:text-sm text-red-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Pilihan Kategori Cepat (Ukuran Besar & Sangat Jelas untuk Orang Tua) -->
                        <div class="bg-slate-50 p-4 rounded-2xl border border-slate-300 space-y-3">
                            <div>
                                <label class="block text-sm sm:text-base font-bold text-slate-900 mb-0.5">
                                    Pilih Kategori Transaksi <span class="text-red-600">*</span>
                                </label>
                                <p class="text-xs sm:text-sm text-slate-600">Klik salah satu tombol di bawah:</p>
                            </div>

                            <div class="flex flex-wrap gap-2 sm:gap-2.5">
                                @if($type === 'income')
                                    <button type="button" wire:click="$set('category', 'Setoran Modal')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Setoran Modal' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">💰</span>
                                        <span>Setoran Modal</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Pendapatan Penjualan')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Pendapatan Penjualan' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">📦</span>
                                        <span>Pendapatan Penjualan</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Pelunasan Piutang Toko')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Pelunasan Piutang Toko' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">🏪</span>
                                        <span>Pelunasan Piutang Toko</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Pinjaman Modal Usaha')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Pinjaman Modal Usaha' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">🏦</span>
                                        <span>Pinjaman Modal</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Pendapatan Lain-lain')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Pendapatan Lain-lain' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">✨</span>
                                        <span>Pendapatan Lain-lain</span>
                                    </button>
                                @elseif($type === 'expense')
                                    <button type="button" wire:click="$set('category', 'Belanja Bahan Baku')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Belanja Bahan Baku' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">🥜</span>
                                        <span>Belanja Bahan Baku</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Beli Kemasan & Stiker')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Beli Kemasan & Stiker' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">🏷️</span>
                                        <span>Kemasan & Stiker</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Biaya Listrik, Air & Gas')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Biaya Listrik, Air & Gas' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">⚡</span>
                                        <span>Listrik, Air & Gas</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Bensin & Transportasi')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Bensin & Transportasi' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">🛵</span>
                                        <span>Bensin & Transport</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Biaya Retur & Basi')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Biaya Retur & Basi' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">⚠️</span>
                                        <span>Retur & Basi</span>
                                    </button>
                                    <button type="button" wire:click="$set('category', 'Operasional Lainnya')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs {{ $category === 'Operasional Lainnya' ? 'bg-slate-900 text-white border-2 border-slate-900 ring-2 ring-slate-900/10' : 'bg-white hover:bg-slate-100 text-slate-900 border-2 border-slate-300' }}">
                                        <span class="text-base">📋</span>
                                        <span>Operasional Lainnya</span>
                                    </button>
                                @elseif($type === 'prive')
                                    <button type="button" wire:click="$set('category', 'Pengambilan Uang Usaha untuk Keluarga (Prive)')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs bg-slate-900 text-white border-2 border-slate-900">
                                        <span class="text-base">👨‍👩‍👧‍👦</span>
                                        <span>Uang untuk Keluarga (Prive)</span>
                                    </button>
                                @elseif($type === 'personal_expense')
                                    <button type="button" wire:click="$set('category', 'Kebutuhan Dapur & Belanja Rumah')"
                                        class="py-2.5 px-4 rounded-xl text-sm sm:text-base font-bold transition flex items-center gap-2 min-h-11 shadow-2xs bg-slate-900 text-white border-2 border-slate-900">
                                        <span class="text-base">🛒</span>
                                        <span>Kebutuhan Dapur & Belanja Rumah</span>
                                    </button>
                                @endif
                            </div>

                            <!-- Input Teks Kategori Terpilih -->
                            <div class="pt-2">
                                <label class="block text-xs sm:text-sm font-semibold text-slate-600 mb-1">
                                    Teks Kategori Terpilih (Bisa Diubah Manual Jika Perlu):
                                </label>
                                <input type="text" list="category-suggestions" wire:model="category"
                                    placeholder="Ketik nama kategori..."
                                    class="input input-bordered w-full text-base font-bold rounded-xl h-12 border-slate-300 focus:border-slate-900 bg-white text-slate-900" />
                            </div>

                            <!-- Datalist Saran -->
                            <datalist id="category-suggestions">
                                <option value="Setoran Modal">
                                <option value="Pendapatan Penjualan">
                                <option value="Pelunasan Piutang Toko">
                                <option value="Belanja Bahan Baku">
                                <option value="Beli Kemasan & Stiker">
                                <option value="Biaya Listrik, Air & Gas">
                                <option value="Bensin & Transportasi">
                                <option value="Biaya Retur & Basi">
                                <option value="Operasional Lainnya">
                                <option value="Pengambilan Uang Usaha untuk Keluarga (Prive)">
                                <option value="Kebutuhan Dapur & Belanja Rumah">
                            </datalist>

                            @error('category') <span class="text-xs sm:text-sm text-red-600 font-bold block mt-1">{{ $message }}</span> @enderror
                        </div>

                        <!-- Nominal Uang Transaksi (Live Thousands Formatting) -->
                        <div>
                            <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">
                                Nominal Uang (Rp) <span class="text-red-600">*</span>
                            </label>
                            <x-currency-input model="amount" size="text-xl" />
                            @error('amount') <span class="text-xs sm:text-sm text-red-600 font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-sm sm:text-base font-bold text-slate-900 mb-1.5">Keterangan Tambahan</label>
                            <textarea wire:model="description" rows="2" placeholder="Catatan transaksi (opsional)..." class="textarea textarea-bordered w-full font-medium text-base rounded-xl border-slate-300 focus:border-slate-900 bg-white text-slate-900"></textarea>
                        </div>
                    </div>

                    <!-- Footer Sticky -->
                    <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-200 bg-slate-50 shrink-0">
                        <button type="button" wire:click="$set('showTransactionModal', false)" class="btn btn-md bg-white hover:bg-slate-100 text-slate-800 font-bold rounded-xl px-5 border border-slate-300 shadow-2xs">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6 shadow-2xs">
                            Review & Simpan Transaksi →
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Modal Konfirmasi & Review Transaksi Kas (Clean Apple Monochrome Style) -->
    @if($showConfirmTransactionModal)
        @php
            $selectedAccount = $accounts->firstWhere('id', $account_id);
            $currentBalance = $selectedAccount?->balance ?? 0;
            $newBalance = $type === 'income' ? ($currentBalance + (float) $amount) : ($currentBalance - (float) $amount);
        @endphp
        <div class="fixed inset-0 z-60 flex items-center justify-center bg-black/50 p-4 backdrop-blur-xs">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <div>
                        <h3 class="text-xl font-bold text-slate-900">
                            Konfirmasi Transaksi Kas
                        </h3>
                        <p class="text-xs text-slate-500">Pastikan data berikut sudah sesuai sebelum disimpan</p>
                    </div>
                    <button wire:click="$set('showConfirmTransactionModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl cursor-pointer">
                        &times;
                    </button>
                </div>

                <!-- Big Nominal Focus Box -->
                <div class="p-4 bg-slate-50 rounded-xl border border-slate-200 text-center space-y-1">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">
                        @if($type === 'income')
                            Pemasukan Usaha
                        @elseif($type === 'expense')
                            Pengeluaran Usaha
                        @elseif($type === 'prive')
                            Tarik Prive Keluarga
                        @else
                            Pengeluaran Pribadi
                        @endif
                    </span>
                    <p class="text-3xl font-mono font-extrabold text-slate-900">
                        {{ $type === 'income' ? '+' : '-' }} Rp {{ number_format((float) $amount, 0, ',', '.') }}
                    </p>
                </div>

                <!-- Rincian Data Transaksi -->
                <div class="space-y-2.5 text-sm">
                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Tanggal:</span>
                        <span class="font-bold text-slate-900">
                            {{ $transaction_date ? \Carbon\Carbon::parse($transaction_date)->translatedFormat('d F Y') : '-' }}
                        </span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Rekening / Kas:</span>
                        <span class="font-bold text-slate-900">
                            {{ $selectedAccount?->name }}
                        </span>
                    </div>

                    <div class="flex justify-between py-1.5 border-b border-slate-100">
                        <span class="text-slate-500">Kategori / Pos:</span>
                        <span class="font-bold text-slate-900">{{ $category }}</span>
                    </div>

                    @if($description)
                        <div class="flex justify-between items-start py-1.5 border-b border-slate-100">
                            <span class="text-slate-500 shrink-0">Keterangan:</span>
                            <span class="font-medium text-slate-800 text-right ml-4">{{ $description }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between py-1.5 text-xs text-slate-500">
                        <span>Estimasi Saldo Baru {{ $selectedAccount?->name }}:</span>
                        <span class="font-mono font-bold text-slate-700 text-sm">
                            Rp {{ number_format($newBalance, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                @if($type === 'prive')
                    <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border border-slate-200">
                        Catatan: Penarikan ini akan memotong saldo Kas Usaha dan dialihkan ke Kas Pribadi keluarga.
                    </div>
                @endif

                <div class="flex justify-end items-center gap-3 pt-3 border-t border-slate-200">
                    <button type="button" wire:click="$set('showConfirmTransactionModal', false)" class="btn btn-md bg-white hover:bg-slate-100 text-slate-700 font-bold rounded-xl px-5 border border-slate-300 cursor-pointer">
                        Ubah Data
                    </button>
                    <button type="button" wire:click="saveTransaction" wire:loading.attr="disabled" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6 cursor-pointer">
                        <span wire:loading.remove wire:target="saveTransaction">Simpan Transaksi</span>
                        <span wire:loading wire:target="saveTransaction">Menyimpan...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Form Tambah Akun Kas -->
    @if($showAccountModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">
                        Tambah Akun Kas / Rekening Baru
                    </h3>
                    <button wire:click="$set('showAccountModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="saveAccount" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nama Akun / Rekening <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="account_name" placeholder="Misal: Kas Tunai Usaha, BCA Usaha, Kas Dapur Keluarga..." class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        @error('account_name') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Jenis Akun <span class="text-red-500">*</span></label>
                        <select wire:model="account_type" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900">
                            <option value="business">Kas Usaha (Operasional / Hasil Tagihan Toko)</option>
                            <option value="personal">Kas Pribadi (Kebutuhan Belanja Keluarga)</option>
                        </select>
                        @error('account_type') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <!-- Saldo Awal (Live Thousands Formatting) -->
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Saldo Awal (Rp)</label>
                        <x-currency-input model="initial_balance" size="text-xl" />
                        @error('initial_balance') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showAccountModal', false)" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
