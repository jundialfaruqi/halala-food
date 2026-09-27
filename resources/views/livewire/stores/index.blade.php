<div class="space-y-6">
    <!-- Header Page & Tombol Tambah Toko -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200/80">
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
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-4 sm:gap-y-0 sm:divide-x sm:divide-slate-200">
        <div class="sm:pr-4">
            <label class="block text-sm font-bold text-slate-700 mb-1">Cari Toko / Pemilik / No. HP</label>
            <input type="text"
                   wire:model.live.debounce.300ms="search"
                   placeholder="Ketik nama toko atau no HP..."
                   class="input input-bordered w-full text-base rounded-xl focus:border-slate-900 bg-white shadow-xs" />
        </div>

        <div class="sm:pl-4">
            <label class="block text-sm font-bold text-slate-700 mb-1">Filter Rute Keliling</label>
            <select wire:model.live="routeFilter" class="select select-bordered w-full text-base rounded-xl focus:border-slate-900 bg-white shadow-xs">
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
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="openEditModal({{ $store->id }})" class="btn btn-sm bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-lg px-3 border border-slate-300">
                                        Edit
                                    </button>
                                    <button wire:click="confirmDelete({{ $store->id }}, '{{ addslashes($store->name) }}')"
                                            class="btn btn-sm bg-slate-100 hover:bg-rose-50 text-rose-600 hover:text-rose-700 font-bold rounded-lg px-3 border border-slate-300 transition active:scale-95">
                                        Hapus
                                    </button>
                                </div>
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
                {{ $stores->links('pagination.tailwind') }}
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
                            @error('phone') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <label class="block text-sm font-bold text-slate-800">Rute / Jalur Keliling</label>
                            @if($routes->count() > 0)
                                <span class="text-xs text-slate-500">Pilih dari rute ada atau ketik baru</span>
                            @endif
                        </div>
                        <input type="text" 
                               wire:model="route" 
                               list="existing-routes-list"
                               placeholder="Misal: Panam, Hangtuah, Rute Pasar..." 
                               class="input input-bordered w-full text-base rounded-xl focus:border-slate-900 bg-white" />
                        <datalist id="existing-routes-list">
                            @foreach($routes as $r)
                                <option value="{{ $r }}">{{ $r }}</option>
                            @endforeach
                        </datalist>

                        @if($routes->count() > 0)
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                <span class="text-xs font-semibold text-slate-500">Pilih Rute:</span>
                                @foreach($routes as $r)
                                    <button type="button" 
                                            wire:click="$set('route', '{{ addslashes($r) }}')" 
                                            class="inline-flex items-center gap-1 text-xs px-2.5 py-1 rounded-lg border transition-all cursor-pointer {{ $route === $r ? 'bg-slate-900 text-white font-bold border-slate-900 shadow-xs' : 'bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium border-slate-200' }}">
                                        @if($route === $r)
                                            <span class="text-emerald-400 font-bold">✓</span>
                                        @endif
                                        <span>{{ $r }}</span>
                                    </button>
                                @endforeach
                            </div>
                        @endif
                        @error('route') <span class="text-xs text-red-600 font-semibold mt-1 block">{{ $message }}</span> @enderror
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

    <!-- Modal Konfirmasi Hapus Toko (Apple UI) -->
    <x-confirm-delete-modal
        :show="$showDeleteModal"
        title="Hapus Toko Mitra?"
        :item-name="$deletingName"
        message="Apakah Anda yakin ingin menghapus data toko ini? Riwayat titipan barang dan rekap keuangan sebelumnya tetap tersimpan rapi."
        confirm-action="deleteStore"
        confirm-text="Ya, Hapus Toko"
    />
</div>
