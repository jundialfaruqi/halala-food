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
<body class="font-sans antialiased bg-base-200/50 text-base-content min-h-screen">
    <div class="drawer lg:drawer-open min-h-screen">
        <input id="app-drawer" type="checkbox" class="drawer-toggle" />

        <!-- Main Content Area -->
        <div class="drawer-content flex flex-col min-h-screen">
            <!-- Apple-style Glass Header -->
            <header class="navbar sticky top-0 z-30 bg-base-100/80 backdrop-blur-xl border-b border-base-300/70 px-4 lg:px-6 h-16 transition-all">
                <!-- Left: Mobile Drawer Toggle & Breadcrumb/Title -->
                <div class="navbar-start gap-2 flex items-center">
                    <label for="app-drawer" class="btn btn-ghost btn-square btn-sm lg:hidden text-base-content" aria-label="Toggle Sidebar">
                        <x-icon name="layout-sidebar" class="text-xl" />
                    </label>

                    <div class="hidden sm:flex items-center gap-2 text-sm">
                        <span class="text-base-content/50">{{ config('app.name', 'Halala Food') }}</span>
                        <x-icon name="chevron-right" class="text-xs text-base-content/30" />
                        <span class="font-semibold text-base-content">{{ $title ?? 'Dashboard' }}</span>
                    </div>
                </div>

                <!-- Center: Apple-style Search Bar -->
                <div class="navbar-center hidden md:flex w-full max-w-sm">
                    <div class="relative w-full">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-base-content/40">
                            <x-icon name="search" class="text-base" />
                        </div>
                        <input type="text"
                               placeholder="Cari menu, pesanan, atau fitur... (⌘K)"
                               class="input input-sm w-full pl-9 pr-12 bg-base-200/60 hover:bg-base-200 focus:bg-base-100 border border-base-300/60 focus:border-primary rounded-xl text-sm transition-all focus:outline-none" />
                        <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center pointer-events-none">
                            <kbd class="kbd kbd-xs bg-base-100 border border-base-300/80 text-[10px] text-base-content/50">⌘K</kbd>
                        </div>
                    </div>
                </div>

                <!-- Right: Action Icons & Profile -->
                <div class="navbar-end gap-1 sm:gap-2">
                    <!-- Notification Button (No background on icon, no badge) -->
                    <button class="btn btn-ghost btn-square btn-sm text-base-content/70 hover:text-base-content" aria-label="Notifikasi">
                        <x-icon name="bell" class="text-lg" />
                    </button>

                    <!-- Settings / Help Button -->
                    <button class="btn btn-ghost btn-square btn-sm text-base-content/70 hover:text-base-content" aria-label="Bantuan">
                        <x-icon name="help-circle" class="text-lg" />
                    </button>

                    <!-- Divider -->
                    <div class="divider divider-horizontal mx-0.5 h-6 self-center"></div>

                    <!-- User Profile Dropdown -->
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="flex items-center gap-2 pl-1 pr-2 py-1 cursor-pointer select-none focus:outline-none">
                            <div class="avatar placeholder">
                                <div class="bg-primary/10 text-primary rounded-lg w-7 h-7 flex items-center justify-center font-bold text-xs leading-none">
                                    <span>HF</span>
                                </div>
                            </div>
                            <span class="text-xs font-semibold hidden sm:inline-block">Admin</span>
                            <x-icon name="chevron-down" class="text-xs text-base-content/50" />
                        </div>
                        <ul tabindex="0" class="dropdown-content menu menu-sm z-50 p-2 shadow-xl bg-base-100 border border-base-300 rounded-2xl w-56 mt-2">
                            <li class="menu-title px-3 py-2 text-xs font-bold text-base-content/40">Akun Saya</li>
                            <li>
                                <a href="#" class="rounded-lg gap-2.5 py-2">
                                    <x-icon name="user" class="text-base text-base-content/70" />
                                    <span>Profil Pengguna</span>
                                </a>
                            </li>
                            <li>
                                <a href="#" class="rounded-lg gap-2.5 py-2">
                                    <x-icon name="adjustments" class="text-base text-base-content/70" />
                                    <span>Preferensi</span>
                                </a>
                            </li>
                            <div class="divider my-1"></div>
                            <li>
                                <a href="#" class="rounded-lg gap-2.5 py-2 text-error hover:bg-error/10">
                                    <x-icon name="logout" class="text-base" />
                                    <span>Keluar</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <!-- Main Page View Content -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8 max-w-7xl w-full mx-auto">
                {{ $slot }}
            </main>

            <!-- Minimal Footer -->
            <footer class="p-4 text-center text-xs text-base-content/40 border-t border-base-300/40">
                &copy; {{ date('Y') }} {{ config('app.name', 'Halala Food') }}. All rights reserved.
            </footer>
        </div>

        <!-- Apple-style Sidebar Drawer -->
        <div class="drawer-side z-40">
            <label for="app-drawer" aria-label="close sidebar" class="drawer-overlay"></label>
            <aside class="w-64 min-h-full bg-base-100 lg:bg-base-100/90 lg:backdrop-blur-xl border-r border-base-300/70 flex flex-col justify-between">
                <div>
                    <!-- Sidebar Header with Apple window dots & Logo -->
                    <div class="h-16 px-5 flex items-center justify-between border-b border-base-300/50">
                        <div class="flex items-center gap-3">
                            <!-- Apple Window Controls Aesthetic -->
                            <div class="hidden lg:flex items-center gap-1.5">
                                <span class="w-3 h-3 rounded-full bg-[#FF5F56] border border-[#E0443E]/40 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-[#FFBD2E] border border-[#DEA123]/40 inline-block"></span>
                                <span class="w-3 h-3 rounded-full bg-[#27C93F] border border-[#1AAB29]/40 inline-block"></span>
                            </div>

                            <a href="{{ url('/') }}" class="flex items-center gap-2 font-bold text-base tracking-tight text-base-content ml-1">
                                <x-icon name="tools-kitchen-2" class="text-xl text-primary" />
                                <span>{{ config('app.name', 'Halala Food') }}</span>
                            </a>
                        </div>
                    </div>

                    <!-- Sidebar Navigation Menu -->
                    <nav class="py-4 space-y-6">
                        <!-- Menu Group: Utama -->
                        <div>
                            <div class="px-5 pb-1.5 text-[11px] font-bold uppercase tracking-wider text-base-content/40">
                                Menu Utama
                            </div>
                            <ul class="menu menu-sm p-0 w-full font-medium">
                                <li class="w-full">
                                    <a href="{{ url('/') }}" class="{{ request()->is('/') ? 'active bg-primary/10 text-primary font-semibold border-r-2 border-primary' : 'text-base-content/80 hover:bg-base-200/70 hover:text-base-content' }} w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="layout-dashboard" class="text-lg" />
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="#" class="text-base-content/80 hover:bg-base-200/70 hover:text-base-content w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="clipboard-list" class="text-lg" />
                                        <span>Daftar Pesanan</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="#" class="text-base-content/80 hover:bg-base-200/70 hover:text-base-content w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="salad" class="text-lg" />
                                        <span>Katalog Menu</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Menu Group: Manajemen Resto -->
                        <div>
                            <div class="px-5 pb-1.5 text-[11px] font-bold uppercase tracking-wider text-base-content/40">
                                Manajemen
                            </div>
                            <ul class="menu menu-sm p-0 w-full font-medium">
                                <li class="w-full">
                                    <a href="#" class="text-base-content/80 hover:bg-base-200/70 hover:text-base-content w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="category" class="text-lg" />
                                        <span>Kategori</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="#" class="text-base-content/80 hover:bg-base-200/70 hover:text-base-content w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="users" class="text-lg" />
                                        <span>Pelanggan</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="#" class="text-base-content/80 hover:bg-base-200/70 hover:text-base-content w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="chart-bar" class="text-lg" />
                                        <span>Laporan Penjualan</span>
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <!-- Menu Group: Preferensi -->
                        <div>
                            <div class="px-5 pb-1.5 text-[11px] font-bold uppercase tracking-wider text-base-content/40">
                                Pengaturan
                            </div>
                            <ul class="menu menu-sm p-0 w-full font-medium">
                                <li class="w-full">
                                    <a href="#" class="text-base-content/80 hover:bg-base-200/70 hover:text-base-content w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="settings" class="text-lg" />
                                        <span>Pengaturan Toko</span>
                                    </a>
                                </li>
                                <li class="w-full">
                                    <a href="#" class="text-base-content/80 hover:bg-base-200/70 hover:text-base-content w-full rounded-none py-2.5 px-5 gap-3 flex items-center transition-colors">
                                        <x-icon name="shield-check" class="text-lg" />
                                        <span>Keamanan & Akses</span>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </nav>
                </div>

                <!-- Sidebar Footer: Apple-style User Switcher -->
                <div class="p-3 border-t border-base-300/50">
                    <div class="flex items-center justify-between p-2 rounded-xl bg-base-200/50 hover:bg-base-200 transition-all cursor-pointer">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <div class="avatar placeholder">
                                <div class="bg-neutral text-neutral-content rounded-lg w-8 h-8 flex items-center justify-center font-bold text-xs leading-none">
                                    <span>HF</span>
                                </div>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-base-content truncate">Restoran Halala</p>
                                <p class="text-[10px] text-base-content/50 truncate">admin@halalafood.id</p>
                            </div>
                        </div>
                        <x-icon name="selector" class="text-base text-base-content/40" />
                    </div>
                </div>
            </aside>
        </div>
    </div>
</body>
</html>
