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
<body class="font-sans antialiased bg-slate-100/70 text-slate-900 min-h-screen text-base selection:bg-slate-900 selection:text-white">
    <div class="drawer lg:drawer-open min-h-screen">
        <input id="app-drawer" type="checkbox" class="drawer-toggle" />

        <!-- Main Content Area -->
        <div class="drawer-content flex flex-col min-h-screen">
            <!-- Apple-style Clean Topbar -->
            <header class="navbar sticky top-0 z-30 bg-white/90 backdrop-blur-xl border-b border-slate-200/80 px-4 lg:px-8 h-18 transition-all">
                <!-- Left: Mobile Drawer Toggle & Breadcrumb/Title -->
                <div class="navbar-start gap-3 flex items-center">
                    <label for="app-drawer" class="btn btn-ghost btn-square btn-md lg:hidden text-slate-700" aria-label="Buka Menu">
                        <x-icon name="menu-2" class="text-2xl" />
                    </label>

                    <div class="flex items-center gap-2 text-base">
                        <span class="text-slate-400 hidden sm:inline-block">{{ config('app.name', 'Halala Food') }}</span>
                        <span class="text-slate-300 hidden sm:inline-block">/</span>
                        <span class="font-bold text-slate-900 text-lg">{{ $title ?? 'Dashboard' }}</span>
                    </div>
                </div>

                <!-- Right: Status / User Info -->
                <div class="navbar-end gap-3">
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <p class="text-sm font-bold text-slate-900 leading-tight">Admin Pembukuan</p>
                            <p class="text-xs text-slate-500">Usaha Keluarga</p>
                        </div>
                        <div class="w-10 h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center font-bold text-sm select-none">
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
            <footer class="p-6 text-center text-sm text-slate-400 border-t border-slate-200">
                &copy; {{ date('Y') }} {{ config('app.name', 'Halala Food') }} — Sistem Pembukuan & Inventori Konsinyasi
            </footer>
        </div>

        <!-- Apple-style Clean Sidebar Drawer -->
        <div class="drawer-side z-40">
            <label for="app-drawer" aria-label="Tutup Menu" class="drawer-overlay"></label>
            <aside class="w-72 min-h-full bg-white border-r border-slate-200 flex flex-col justify-between select-none">
                <div>
                    <!-- Sidebar Header: Mac window controls & Brand -->
                    <div class="h-18 px-6 flex items-center justify-between border-b border-slate-200/80">
                        <div class="flex items-center gap-3">
                            <div class="hidden lg:flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-slate-300 inline-block"></span>
                            </div>

                            <a href="{{ route('dashboard') }}" class="flex items-center gap-2 font-bold text-lg tracking-tight text-slate-900">
                                <span>{{ config('app.name', 'Halala Food') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Sidebar Navigation Menu (Large Text, High Contrast, Full Width) -->
                    <nav class="py-4 space-y-6">
                        <!-- Group 1: Menu Utama -->
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

                        <!-- Group 2: Produk & Stok -->
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

                        <!-- Group 3: Keuangan -->
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

                <!-- Sidebar Footer: Toko Aktif info -->
                <div class="p-6 border-t border-slate-200">
                    <div class="text-sm text-slate-500">
                        <p class="font-bold text-slate-800">Usaha Makanan Keluarga</p>
                        <p class="text-xs text-slate-400 mt-0.5">Versi 1.0 • Offline Ready</p>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
