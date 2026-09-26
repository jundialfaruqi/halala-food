<div class="space-y-6">
    <!-- Header Page & Tombol Tambah Toko -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-white p-6 rounded-2xl border border-slate-200">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-slate-900">
                Daftar Toko Mitra
            </h1>
            <p class="text-base text-slate-600 mt-1">
                Data toko yang bekerja sama untuk titip jual makanan olahan Halala Food.
            </p>
        </div>
        <button wire:click="openCreateModal" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold text-base rounded-xl px-5 gap-2 shadow-sm">
            <x-icon name="plus" class="text-xl" />
            <span>Tambah Toko Baru</span>
        </button>
    </div>

    <!-- Filter & Pencarian -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Cari Toko / Pemilik / No. HP</label>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Ketik nama toko atau no HP..."
                   class="input input-bordered w-full text-base rounded-xl focus:border-slate-900 bg-slate-50 focus:bg-white" />
        </div>

        <div>
            <label class="block text-sm font-bold text-slate-700 mb-1">Filter Rute Keliling</label>
            <select wire:model.live="routeFilter" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 bg-slate-50 focus:bg-white">
                <option value="">Semua Rute</option>
                @foreach($routes as $r)
                    <option value="{{ $r }}">{{ $r }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabel Daftar Toko -->
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="table w-full text-base">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200 text-slate-600 font-bold text-sm uppercase tracking-wider">
                        <th class="py-4 px-6">Nama Toko & Pemilik</th>
                        <th class="py-4 px-4">Kontak & Alamat</th>
                        <th class="py-4 px-4">Rute Wilayah</th>
                        <th class="py-4 px-4">Titipan Aktif</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($stores as $store)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <p class="font-bold text-lg text-slate-900 leading-tight">{{ $store->name }}</p>
                                <p class="text-sm text-slate-500">Pemilik: {{ $store->owner_name ?: '-' }}</p>
                            </td>
                            <td class="py-4 px-4">
                                <p class="font-mono text-base text-slate-800">{{ $store->phone ?: '-' }}</p>
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ $store->address ?: '-' }}</p>
                            </td>
                            <td class="py-4 px-4 font-medium text-slate-800">
                                {{ $store->route ?: 'Belum diatur' }}
                            </td>
                            <td class="py-4 px-4 font-mono font-bold text-base text-slate-900">
                                {{ $store->consignments_count }} Titipan Aktif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <button wire:click="openEditModal({{ $store->id }})" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg px-4 border border-slate-300">
                                    Edit Toko
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500 text-base">
                                Belum ada data toko. Klik tombol "Tambah Toko Baru" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($stores->hasPages())
            <div class="p-4 border-t border-slate-200">
                {{ $stores->links() }}
            </div>
        @endif
    </div>

    <!-- Modal Form Tambah / Edit Toko -->
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div class="bg-white w-full max-w-lg rounded-2xl p-6 border border-slate-200 shadow-2xl space-y-5">
                <div class="flex items-center justify-between pb-3 border-b border-slate-200">
                    <h3 class="text-xl font-bold text-slate-900">
                        {{ $storeId ? 'Edit Data Toko' : 'Tambah Toko Baru' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-700 font-bold text-xl">
                        &times;
                    </button>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Nama Toko <span class="text-red-500">*</span></label>
                        <input type="text" wire:model="name" placeholder="Misal: Toko Rezeki Baru" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        @error('name') <span class="text-xs text-red-600 font-semibold">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">Nama Pemilik</label>
                            <input type="text" wire:model="owner_name" placeholder="Misal: Pak Haji Ahmad" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-800 mb-1">No. HP / WhatsApp</label>
                            <input type="text" wire:model="phone" placeholder="0812xxxx" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Rute / Jalur Keliling</label>
                        <input type="text" wire:model="route" placeholder="Misal: Rute Pasar Besar, Rute Barat" class="input input-bordered w-full text-base rounded-xl focus:border-slate-900" />
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Alamat Lengkap</label>
                        <textarea wire:model="address" rows="2" placeholder="Alamat toko..." class="textarea textarea-bordered w-full text-base rounded-xl focus:border-slate-900"></textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-800 mb-1">Catatan Khusus</label>
                        <textarea wire:model="notes" rows="2" placeholder="Misal: Buka jam 7 pagi, titip di kasir..." class="textarea textarea-bordered w-full text-base rounded-xl focus:border-slate-900"></textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-3 border-t border-slate-200">
                        <button type="button" wire:click="$set('showModal', false)" class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl px-5 border border-slate-300">
                            Batal
                        </button>
                        <button type="submit" class="btn btn-md bg-slate-900 hover:bg-black text-white font-bold rounded-xl px-6">
                            Simpan Data Toko
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
