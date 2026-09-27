@if ($paginator->hasPages())
    <nav role="navigation" aria-label="{{ __('Pagination Navigation') }}" class="flex items-center justify-between">
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
                    <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                        <span>Sebelumnya</span>
                    </a>
                @endif
            </div>

            <div class="text-xs font-semibold text-slate-500">
                <span>Hal {{ $paginator->currentPage() }} / {{ $paginator->lastPage() }}</span>
            </div>

            <div>
                @if ($paginator->hasMorePages())
                    <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex items-center gap-1.5 px-3.5 py-2 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-50 border border-slate-200 rounded-xl shadow-xs transition active:scale-95">
                        <span>Berikutnya</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </a>
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
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-full shadow-xs transition active:scale-95" aria-label="Halaman Sebelumnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </a>
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
                                    <span class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-sm font-bold text-white bg-slate-900 rounded-full shadow-xs select-none" aria-current="page">
                                        {{ $page }}
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-sm font-semibold text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-full shadow-xs transition active:scale-95" aria-label="Menuju halaman {{ $page }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                    {{-- Tombol Berikutnya --}}
                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center justify-center w-9 h-9 min-w-9 min-h-9 shrink-0 text-slate-700 bg-white hover:bg-slate-100 border border-slate-200 rounded-full shadow-xs transition active:scale-95" aria-label="Halaman Berikutnya">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
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
