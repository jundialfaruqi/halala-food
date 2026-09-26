<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ isset($title) ? $title . ' - ' . config('app.name', 'Halala Food') : config('app.name', 'Halala Food') }}</title>

    <!-- Vite Styles and Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body 
    class="font-sans antialiased bg-slate-100/70 text-slate-900 h-screen overflow-hidden text-base selection:bg-slate-900 selection:text-white"
    x-data="{
        sidebarOpen: window.innerWidth >= 1024 ? (localStorage.getItem('hf_sidebar_open') !== 'false') : false,
        toggleSidebar() {
            this.sidebarOpen = !this.sidebarOpen;
            if (window.innerWidth >= 1024) {
                localStorage.setItem('hf_sidebar_open', this.sidebarOpen);
            }
        }
    }"
    @keydown.window.ctrl.b.prevent="toggleSidebar()"
    @keydown.window.cmd.b.prevent="toggleSidebar()"
>
    <!-- Global DaisyUI Toast Notifications -->
    <x-toast />

    <div class="h-screen flex bg-slate-100/70 overflow-hidden">
        <!-- Backdrop for Mobile Drawer -->
        <div 
            x-show="sidebarOpen" 
            x-transition:enter="transition-opacity ease-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-in duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="sidebarOpen = false" 
            class="fixed inset-0 z-40 bg-slate-900/40 backdrop-blur-xs lg:hidden"
            style="display: none;"
        ></div>

        <!-- Apple-style Clean Sidebar (Collapsible & Fixed/Static on Desktop) -->
        <aside 
            :class="{
                'w-72 translate-x-0': sidebarOpen,
                'w-0 -translate-x-full lg:translate-x-0 lg:w-0 border-r-0 pointer-events-none': !sidebarOpen
            }"
            class="fixed inset-y-0 left-0 z-50 lg:static lg:h-screen bg-white border-r border-slate-200/80 flex flex-col justify-between shrink-0 transition-all duration-300 ease-in-out select-none shadow-2xl lg:shadow-none overflow-hidden"
        >
            <div class="w-72 min-w-[18rem] flex flex-col justify-between h-full shrink-0">
                <div class="flex flex-col flex-1 min-h-0">
                    <!-- Sidebar Header: Mac window controls, Brand & Mobile Close -->
                    <div class="h-18 px-6 flex items-center justify-between border-b border-slate-200/80 shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                            </div>

                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-lg tracking-tight text-slate-900">
                                <span>{{ config('app.name', 'Halala Food') }}</span>
                            </a>
                        </div>

                        <!-- Mobile Close Button -->
                        <button @click="sidebarOpen = false" type="button" class="btn btn-ghost btn-square btn-sm text-slate-400 hover:text-slate-700 lg:hidden" aria-label="Tutup Menu">
                            <x-icon name="x" class="text-xl" />
                        </button>
                    </div>

                    <!-- Sidebar Navigation Menu (Scrolls independently if screen height is short) -->
                    <nav class="py-4 space-y-6 overflow-y-auto flex-1">
                        <!-- Group 1: Utama & Penjualan -->
                        <div>
                            <div class="px-6 pb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                                Utama & Penjualan
                            </div>
                            <ul class="menu p-0 w-full text-base font-semibold">
                                <li class="w-full">
                                    <a href="{{ route('dashboard') }}"
                                       class="{{ request()->routeIs('dashboard') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="layout-dashboard" class="text-xl" />
                                        <span class="text-base">Dashboard</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="{{ route('consignments.index') }}"
                                       class="{{ request()->routeIs('consignments.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="truck-delivery" class="text-xl" />
                                        <span class="text-base">Titip Jual / Konsinyasi</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="{{ route('stores.index') }}"
                                       class="{{ request()->routeIs('stores.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="building-store" class="text-xl" />
                                        <span class="text-base">Daftar Toko Mitra</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Group 2: Produk & Bahan -->
                        <div>
                            <div class="px-6 pb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                                Produk & Bahan
                            </div>
                            <ul class="menu p-0 w-full text-base font-semibold">
                                <li class="w-full">
                                    <a href="{{ route('products.index') }}"
                                       class="{{ request()->routeIs('products.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="package" class="text-xl" />
                                        <span class="text-base">Produk Jadi</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="{{ route('productions.index') }}"
                                       class="{{ request()->routeIs('productions.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="tools-kitchen-2" class="text-xl" />
                                        <span class="text-base">Catat Produksi</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="{{ route('raw-materials.index') }}"
                                       class="{{ request()->routeIs('raw-materials.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="archive" class="text-xl" />
                                        <span class="text-base">Bahan Baku & Resep</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Group 3: Pembukuan & Kas -->
                        <div>
                            <div class="px-6 pb-2 text-xs font-bold uppercase tracking-wider text-slate-400">
                                Pembukuan & Kas
                            </div>
                            <ul class="menu p-0 w-full text-base font-semibold">
                                <li class="w-full">
                                    <a href="{{ route('cash-book.index') }}"
                                       class="{{ request()->routeIs('cash-book.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="wallet" class="text-xl" />
                                        <span class="text-base">Buku Kas (Usaha & Pribadi)</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="{{ route('reports.index') }}"
                                       class="{{ request()->routeIs('reports.*') ? 'bg-slate-900 text-white font-bold' : 'text-slate-700 hover:bg-slate-100 hover:text-slate-900' }} w-full rounded-none py-3 px-6 gap-3 flex items-center transition-colors">
                                        <x-icon name="chart-pie" class="text-xl" />
                                        <span class="text-base">Laporan Laba Rugi</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                <!-- Sidebar Footer -->
                <div class="p-6 border-t border-slate-200/80 shrink-0">
                    <div class="text-sm text-slate-500">
                        <p class="font-bold text-slate-800">Usaha Makanan Keluarga</p>
                        <p class="text-xs text-slate-400 mt-0.5">Versi 1.0 • Offline Ready</p>
                    </div>
                </div>
            </div>
        </aside>

        <!-- Main Content Area (Isolated Scroll Container) -->
        <div class="flex-1 flex flex-col h-screen overflow-y-auto overflow-x-hidden min-w-0">
            <!-- Apple-style Clean Topbar -->
            <header class="navbar sticky top-0 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 px-4 lg:px-8 h-18 shrink-0 transition-all">
                <!-- Left: Toggle Sidebar Button & Title -->
                <div class="navbar-start gap-3 flex items-center">
                    <button 
                        @click="toggleSidebar()" 
                        type="button" 
                        class="btn btn-ghost btn-square btn-md text-slate-700 hover:bg-slate-100 rounded-xl transition-colors cursor-pointer" 
                        :title="sidebarOpen ? 'Sembunyikan Menu Sidebar (Ctrl+B / Cmd+B)' : 'Tampilkan Menu Sidebar (Ctrl+B / Cmd+B)'"
                        aria-label="Toggle Menu Sidebar"
                    >
                        <x-icon name="layout-sidebar" class="text-2xl" />
                    </button>

                    <div class="hidden sm:flex items-center gap-2 text-base">
                        <span class="text-slate-400">{{ config('app.name', 'Halala Food') }}</span>
                        <span class="text-slate-300">/</span>
                        <h1 class="font-bold text-slate-900 text-lg">{{ $title ?? 'Dashboard' }}</h1>
                    </div>
                </div>

                <!-- Right: Status / User Info -->
                <div class="navbar-end gap-3">
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-900 leading-tight">Admin Pembukuan</p>
                            <p class="text-xs text-slate-500">Usaha Keluarga</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm select-none shadow-xs">
                            HF
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Page View Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>

            <!-- Minimal Footer -->
            <footer class="p-6 text-center text-sm text-slate-400 border-t border-slate-200/80 shrink-0">
                &copy; {{ date('Y') }} {{ config('app.name', 'Halala Food') }} — Sistem Pembukuan & Inventori Konsinyasi
            </footer>
        </div>
    </div>
</body>
</html>
