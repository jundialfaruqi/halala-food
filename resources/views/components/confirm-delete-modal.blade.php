@props([
    'show' => false,
    'title' => 'Konfirmasi Hapus Data',
    'message' => 'Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.',
    'itemName' => null,
    'confirmAction' => 'delete',
    'cancelAction' => "\$set('showDeleteModal', false)",
    'confirmText' => 'Ya, Hapus',
])

@if($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-xs p-4 transition-all duration-200"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        <div class="bg-white w-full max-w-sm sm:max-w-md rounded-3xl p-6 sm:p-7 border border-slate-200/80 shadow-2xl space-y-4 text-center transform transition-all duration-200"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.outside="wire.set('showDeleteModal', false)">

            <!-- Icon -->
            <div class="mx-auto w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl ring-8 ring-rose-50/70 shadow-xs">
                <x-icon name="trash" class="text-2xl" />
            </div>

            <!-- Title & Description -->
            <div class="space-y-2">
                <h3 class="text-xl font-bold tracking-tight text-slate-900">
                    {{ $title }}
                </h3>
                @if($itemName)
                    <div class="inline-block max-w-full truncate px-3.5 py-1 bg-slate-100 text-slate-900 font-bold text-sm rounded-xl border border-slate-200/70">
                        {{ $itemName }}
                    </div>
                @endif
                <p class="text-sm text-slate-500 leading-relaxed px-2">
                    {{ $message }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-3 pt-3">
                <button type="button"
                        wire:click="{{ $cancelAction }}"
                        class="btn btn-md bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-base rounded-xl px-5 flex-1 border-0 shadow-none transition active:scale-95">
                    Batal
                </button>
                <button type="button"
                        wire:click="{{ $confirmAction }}"
                        wire:loading.attr="disabled"
                        class="btn btn-md bg-rose-600 hover:bg-rose-700 text-white font-bold text-base rounded-xl px-5 flex-1 border-0 shadow-xs transition active:scale-95 gap-2">
                    <span wire:loading.remove wire:target="{{ $confirmAction }}">{{ $confirmText }}</span>
                    <span wire:loading wire:target="{{ $confirmAction }}" class="loading loading-spinner loading-sm"></span>
                    <span wire:loading wire:target="{{ $confirmAction }}">Menghapus...</span>
                </button>
            </div>
        </div>
    </div>
@endif
