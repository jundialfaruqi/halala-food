<x-layouts.app title="Dashboard">
    <div class="space-y-8">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-base-content">
                    Ringkasan Toko
                </h1>
                <p class="text-sm text-base-content/60 mt-0.5">
                    Pantau kinerja penjualan, pesanan masuk, dan performa menu hari ini.
                </p>
            </div>
            <div class="flex items-center gap-2.5">
                <button class="btn btn-sm btn-ghost border border-base-300 rounded-xl gap-2 font-medium">
                    <x-icon name="download" class="text-base" />
                    <span>Ekspor Laporan</span>
                </button>
                <button class="btn btn-sm btn-primary rounded-xl gap-2 shadow-sm font-medium">
                    <x-icon name="plus" class="text-base" />
                    <span>Tambah Menu Baru</span>
                </button>
            </div>
        </div>

        <!-- Metric Stat Cards (Apple Style - No Badges, No Icon BG) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Card 1 -->
            <div class="card bg-base-100 border border-base-300/70 shadow-xs rounded-2xl p-5 hover:border-base-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-base-content/60">Total Pendapatan</span>
                    <x-icon name="currency-dollar" class="text-xl text-primary" />
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold tracking-tight text-base-content">Rp 14.850.000</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-success font-medium">
                        <x-icon name="trending-up" class="text-sm" />
                        <span>+12.5% dari minggu lalu</span>
                    </div>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="card bg-base-100 border border-base-300/70 shadow-xs rounded-2xl p-5 hover:border-base-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-base-content/60">Pesanan Hari Ini</span>
                    <x-icon name="shopping-cart" class="text-xl text-secondary" />
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold tracking-tight text-base-content">128 Pesanan</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-success font-medium">
                        <x-icon name="trending-up" class="text-sm" />
                        <span>+8 pesanan baru</span>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="card bg-base-100 border border-base-300/70 shadow-xs rounded-2xl p-5 hover:border-base-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-base-content/60">Menu Terjual</span>
                    <x-icon name="salad" class="text-xl text-accent" />
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold tracking-tight text-base-content">342 Porsi</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-base-content/50 font-medium">
                        <x-icon name="clock" class="text-sm" />
                        <span>Rata-rata 28 porsi/jam</span>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="card bg-base-100 border border-base-300/70 shadow-xs rounded-2xl p-5 hover:border-base-300 transition-all">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold text-base-content/60">Pelanggan Aktif</span>
                    <x-icon name="users" class="text-xl text-primary" />
                </div>
                <div class="mt-3">
                    <div class="text-2xl font-bold tracking-tight text-base-content">94 Pelanggan</div>
                    <div class="flex items-center gap-1.5 mt-1 text-xs text-success font-medium">
                        <x-icon name="arrow-up-right" class="text-sm" />
                        <span>+18% pelanggan setia</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Dashboard Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left 2 Cols: Recent Orders Table -->
            <div class="lg:col-span-2 card bg-base-100 border border-base-300/70 shadow-xs rounded-2xl p-6">
                <div class="flex items-center justify-between mb-5">
                    <div>
                        <h2 class="text-base font-bold text-base-content">Pesanan Terbaru</h2>
                        <p class="text-xs text-base-content/60">Daftar transaksi pesanan masuk secara real-time</p>
                    </div>
                    <button class="btn btn-xs btn-ghost gap-1 text-primary hover:bg-primary/10 rounded-lg">
                        <span>Lihat Semua</span>
                        <x-icon name="arrow-right" class="text-xs" />
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="table table-sm w-full">
                        <thead>
                            <tr class="border-b border-base-300/60 text-[11px] uppercase tracking-wider text-base-content/50">
                                <th class="py-3">No. Pesanan</th>
                                <th class="py-3">Pelanggan</th>
                                <th class="py-3">Menu</th>
                                <th class="py-3">Total</th>
                                <th class="py-3 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="text-xs divide-y divide-base-300/40">
                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="font-mono font-semibold text-base-content">#HL-2026-089</td>
                                <td>
                                    <div class="font-medium text-base-content">Ahmad Fauzi</div>
                                    <div class="text-[11px] text-base-content/50">Meja 04 • Dine In</div>
                                </td>
                                <td>
                                    <span>Nasi Kebuli Kambing (x2)</span>
                                </td>
                                <td class="font-semibold text-base-content">Rp 170.000</td>
                                <td class="text-right">
                                    <button class="btn btn-ghost btn-square btn-xs text-base-content/60 hover:text-base-content">
                                        <x-icon name="dots-vertical" class="text-sm" />
                                    </button>
                                </td>
                            </tr>

                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="font-mono font-semibold text-base-content">#HL-2026-088</td>
                                <td>
                                    <div class="font-medium text-base-content">Siti Rahma</div>
                                    <div class="text-[11px] text-base-content/50">Take Away</div>
                                </td>
                                <td>
                                    <span>Shawarma Ayam Spesial (x3)</span>
                                </td>
                                <td class="font-semibold text-base-content">Rp 105.000</td>
                                <td class="text-right">
                                    <button class="btn btn-ghost btn-square btn-xs text-base-content/60 hover:text-base-content">
                                        <x-icon name="dots-vertical" class="text-sm" />
                                    </button>
                                </td>
                            </tr>

                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="font-mono font-semibold text-base-content">#HL-2026-087</td>
                                <td>
                                    <div class="font-medium text-base-content">Budi Santoso</div>
                                    <div class="text-[11px] text-base-content/50">Meja 09 • Dine In</div>
                                </td>
                                <td>
                                    <span>Nasi Mandhi Ayam (x1), Es Teh (x2)</span>
                                </td>
                                <td class="font-semibold text-base-content">Rp 65.000</td>
                                <td class="text-right">
                                    <button class="btn btn-ghost btn-square btn-xs text-base-content/60 hover:text-base-content">
                                        <x-icon name="dots-vertical" class="text-sm" />
                                    </button>
                                </td>
                            </tr>

                            <tr class="hover:bg-base-200/40 transition-colors">
                                <td class="font-mono font-semibold text-base-content">#HL-2026-086</td>
                                <td>
                                    <div class="font-medium text-base-content">Dewi Lestari</div>
                                    <div class="text-[11px] text-base-content/50">Delivery Order</div>
                                </td>
                                <td>
                                    <span>Kebab Daging Jumbo (x2)</span>
                                </td>
                                <td class="font-semibold text-base-content">Rp 80.000</td>
                                <td class="text-right">
                                    <button class="btn btn-ghost btn-square btn-xs text-base-content/60 hover:text-base-content">
                                        <x-icon name="dots-vertical" class="text-sm" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Right 1 Col: Quick Actions & Popular Menu -->
            <div class="space-y-6">
                <!-- Popular Items -->
                <div class="card bg-base-100 border border-base-300/70 shadow-xs rounded-2xl p-6">
                    <h2 class="text-base font-bold text-base-content mb-4">Menu Terlaris Hari Ini</h2>
                    <div class="space-y-3.5">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <x-icon name="bowl" class="text-lg text-primary" />
                                <div>
                                    <p class="text-xs font-semibold text-base-content">Nasi Kebuli Kambing</p>
                                    <p class="text-[11px] text-base-content/50">76 Porsi terjual</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-base-content">Rp 85.000</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <x-icon name="flame" class="text-lg text-secondary" />
                                <div>
                                    <p class="text-xs font-semibold text-base-content">Shawarma Daging Sapi</p>
                                    <p class="text-[11px] text-base-content/50">54 Porsi terjual</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-base-content">Rp 38.000</span>
                        </div>

                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <x-icon name="cup" class="text-lg text-accent" />
                                <div>
                                    <p class="text-xs font-semibold text-base-content">Teh Rempah Arab</p>
                                    <p class="text-[11px] text-base-content/50">42 Gelas terjual</p>
                                </div>
                            </div>
                            <span class="text-xs font-bold text-base-content">Rp 15.000</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Help / Status -->
                <div class="card bg-base-100 border border-base-300/70 shadow-xs rounded-2xl p-6">
                    <h2 class="text-base font-bold text-base-content mb-2">Akses Cepat</h2>
                    <p class="text-xs text-base-content/60 mb-4">Pintasan navigasi untuk operasional kasir & dapur</p>
                    <div class="grid grid-cols-2 gap-2">
                        <button class="btn btn-sm btn-ghost border border-base-300 rounded-xl justify-start gap-2 text-xs font-medium">
                            <x-icon name="device-desktop" class="text-base text-base-content/70" />
                            <span>POS Kasir</span>
                        </button>
                        <button class="btn btn-sm btn-ghost border border-base-300 rounded-xl justify-start gap-2 text-xs font-medium">
                            <x-icon name="receipt" class="text-base text-base-content/70" />
                            <span>Struk Pembayaran</span>
                        </button>
                        <button class="btn btn-sm btn-ghost border border-base-300 rounded-xl justify-start gap-2 text-xs font-medium">
                            <x-icon name="archive" class="text-base text-base-content/70" />
                            <span>Stok Bahan</span>
                        </button>
                        <button class="btn btn-sm btn-ghost border border-base-300 rounded-xl justify-start gap-2 text-xs font-medium">
                            <x-icon name="printer" class="text-base text-base-content/70" />
                            <span>Cetak Laporan</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
