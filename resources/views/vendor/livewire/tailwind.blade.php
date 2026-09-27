@php
if (! isset($scrollTo)) {
    $scrollTo = 'body';
}

$scrollIntoViewJsSnippet = ($scrollTo !== false)
    ? <<<JS
       (\$el.closest('{$scrollTo}') || document.querySelector('{$scrollTo}')).scrollIntoView()
    JS
    : '';
@endphp

<div>
    @if ($paginator->hasPages())
        <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center justify-between">
            {{-- Mobile Pagination View --}}
            <div class="flex items-center justify-between w-full sm:hidden">
                <div>
                    @if ($paginator->onFirstPage())
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-slate-400 bg-slate-100 border border-slate-200/60 rounded-xl cursor-not-allowed select-none">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Sebelumnya</span>
                        </span>
                    @else
                        <button type="button"
                                wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition active:scale-95">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                            <span>Sebelumnya</span>
                        </button>
                    @endif
                </div>

                <div class="text-xs font-semibold text-slate-500">
                    <span>Hal {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
                </div>

                <div>
                    @if ($paginator->hasMorePages())
                        <button type="button"
                                wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                wire:loading.attr="disabled"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition active:scale-95">
                            <span>Berikutnya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </button>
                    @else
                        <span class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-slate-400 bg-slate-100 border border-slate-200/60 rounded-xl cursor-not-allowed select-none">
                            <span>Berikutnya</span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </span>
                    @endif
                </div>
            </div>

            {{-- Desktop Pagination View --}}
            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between gap-4">
                <div>
                    <p class="text-sm text-slate-600">
                        <span>Menampilkan</span>
                        <span class="font-bold text-slate-900">{{ $paginator->firstItem() }}</span>
                        <span>sampai</span>
                        <span class="font-bold text-slate-900">{{ $paginator->lastItem() }}</span>
                        <span>dari</span>
                        <span class="font-bold text-slate-900">{{ $paginator->total() }}</span>
                        <span>data</span>
                    </p>
                </div>

                <div>
                    <div class="flex items-center gap-1.5 shrink-0">
                        {{-- Tombol Sebelumnya --}}
                        @if ($paginator->onFirstPage())
                            <span class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-slate-300 bg-slate-50 border border-slate-200/60 rounded-full cursor-not-allowed select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </span>
                        @else
                            <button type="button"
                                    wire:click="previousPage('{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-full shadow-xs transition active:scale-95"
                                    aria-label="Halaman Sebelumnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                </svg>
                            </button>
                        @endif

                        {{-- Pagination Elements --}}
                        @foreach ($elements as $element)
                            {{-- "Three Dots" Separator --}}
                            @if (is_string($element))
                                <span class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-xs font-bold text-slate-400 select-none">
                                    {{ $element }}
                                </span>
                            @endif

                            {{-- Array Of Links --}}
                            @if (is_array($element))
                                @foreach ($element as $page => $url)
                                    @if ($page == $paginator->currentPage())
                                        <span wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}"
                                              class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-sm font-bold text-white bg-slate-900 rounded-full shadow-xs select-none">
                                            {{ $page }}
                                        </span>
                                    @else
                                        <button type="button"
                                                wire:key="paginator-{{ $paginator->getPageName() }}-page{{ $page }}"
                                                wire:click="gotoPage({{ $page }}, '{{ $paginator->getPageName() }}')"
                                                x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                                class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-full shadow-xs transition active:scale-95"
                                                aria-label="Menuju halaman {{ $page }}">
                                            {{ $page }}
                                        </button>
                                    @endif
                                @endforeach
                            @endif
                        @endforeach

                        {{-- Tombol Berikutnya --}}
                        @if ($paginator->hasMorePages())
                            <button type="button"
                                    wire:click="nextPage('{{ $paginator->getPageName() }}')"
                                    x-on:click="{{ $scrollIntoViewJsSnippet }}"
                                    class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-full shadow-xs transition active:scale-95"
                                    aria-label="Halaman Berikutnya">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </button>
                        @else
                            <span class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-slate-300 bg-slate-50 border border-slate-200/60 rounded-full cursor-not-allowed select-none" aria-hidden="true">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </nav>
    @endif
</div>
