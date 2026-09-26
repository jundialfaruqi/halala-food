<div class="space-y-8">
    <!-- Header Page & Tombol Aksi Kas -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Buku Kas & Keuangan
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Pemisahan tegas antara uang hasil usaha makanan dan kebutuhan belanja pribadi keluarga.
            </p>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <button wire:click="openAccountModal" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-4 text-sm border border-slate-300">
                + Tambah Akun Kas
            </button>
            <button wire:click="openTransactionModal('income')" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-4 text-sm">
                + Pemasukan
            </button>
            <button wire:click="openTransactionModal('expense')" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-4 text-sm border border-slate-300">
                - Biaya Usaha
            </button>
            <button wire:click="openTransactionModal('prive')" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-4 text-sm border border-slate-300">
                ⇄ Tarik Prive Keluarga
            </button>
        </div>
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
                        <th class="py-4 px-6">Tanggal</th>
                        <th class="py-4 px-4">Jenis & Kategori</th>
                        <th class="py-4 px-4">Akun Kas</th>
                        <th class="py-4 px-4">Keterangan</th>
                        <th class="py-4 px-6 text-right">Nominal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900 text-sm">
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
                            <td class="py-4 px-6 text-right font-mono font-extrabold text-lg {{ $trx->type === 'income' ? 'text-slate-900' : 'text-slate-600' }}">
                                {{ $trx->type === 'income' ? '+' : '-' }} Rp {{ number_format($trx->amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 text-base">
                                Belum ada transaksi kas yang dicatat.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Transaksi Kas -->
    @if($showTransactionModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">
                        Catat Transaksi Kas
                    </h3>
                    <button wire:click="$set('showTransactionModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="saveTransaction" class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Tanggal Transaksi <span class="text-red-500">*</span></label>
                            <input type="date" wire:model="transaction_date" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                            @error('transaction_date') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Jenis Transaksi <span class="text-red-500">*</span></label>
                            <select wire:model.live="type" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900">
                                <option value="expense">Pengeluaran Usaha</option>
                                <option value="income">Pemasukan Usaha</option>
                                <option value="prive">Tarik Uang untuk Keluarga (Prive)</option>
                                <option value="personal_expense">Pengeluaran Pribadi</option>
                            </select>
                            @error('type') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Pilih Rekening / Kas <span class="text-red-500">*</span></label>
                        <select wire:model="account_id" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900">
                            @foreach($accounts as $acc)
                                <option value="{{ $acc->id }}">
                                    {{ $acc->name }} (Saldo: Rp {{ number_format($acc->balance, 0, ',', '.') }})
                                </option>
                            @endforeach
                        </select>
                        @error('account_id') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Kategori / Pos Pengeluaran <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="category" placeholder="Misal: Beli Bahan Baku, Bensin, Belanja Dapur..." class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        @error('category') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nominal Uang (Rp) <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="amount" min="1" placeholder="Nominal rupiah..." class="input input-bordered w-full font-mono font-bold text-xl rounded-xl focus:border-slate-900" />
                        @error('amount') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Keterangan Tambahan</label>
                        <textarea wire:model="description" rows="2" placeholder="Catatan transaksi..." class="textarea textarea-bordered w-full text-base rounded-xl focus:border-slate-900"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showTransactionModal', false)" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan Transaksi
                        </button>
                    </div>
                </form>
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

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Saldo Awal (Rp)</label>
                        <input type="number" wire:model="initial_balance" min="0" placeholder="0" class="input input-bordered w-full font-mono font-bold text-xl rounded-xl focus:border-slate-900" />
                        @error('initial_balance') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
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
