<div x-data="{
    toasts: [],
    add(message, type = 'success') {
        if (!message) return;
        const id = Date.now() + Math.random();
        this.toasts.push({ id, message, type });
        setTimeout(() => this.remove(id), 3500);
    },
    remove(id) {
        this.toasts = this.toasts.filter(t => t.id !== id);
    }
}"
x-init="
    @if(session()->has('message'))
        $nextTick(() => add(@js(session('message'))));
    @endif
    @if(session()->has('success'))
        $nextTick(() => add(@js(session('success'))));
    @endif
"
@toast.window="add($event.detail.message || $event.detail, $event.detail.type || 'success')"
@notify.window="add($event.detail.message || $event.detail, $event.detail.type || 'success')"
class="toast toast-top toast-end z-[9999] p-4 space-y-2 pointer-events-none fixed top-4 right-4">
    <template x-for="t in toasts" :key="t.id">
        <div x-show="true"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-2 scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 translate-y-0 scale-100"
             x-transition:leave-end="opacity-0 translate-y-2 scale-95"
             class="pointer-events-auto flex items-center justify-between gap-3 bg-slate-900 text-white px-5 py-4 rounded-2xl shadow-2xl border border-slate-700 min-w-[320px] max-w-md">
            <div class="flex items-center gap-3">
                <x-icon name="circle-check" class="text-xl text-emerald-400 shrink-0" />
                <span class="text-base font-semibold text-white leading-snug" x-text="t.message"></span>
            </div>
            <button @click="remove(t.id)" class="text-slate-400 hover:text-white text-xl leading-none p-1 font-bold shrink-0" aria-label="Tutup">
                &times;
            </button>
        </div>
    </template>
</div>
