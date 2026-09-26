<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HalalaFoodSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Akun Kas
        $kasTunai = Account::create([
            'name' => 'Kas Tunai Toko (Hasil Tagihan)',
            'type' => 'business',
            'balance' => 2500000,
            'description' => 'Uang tunai hasil keliling jemput tagihan toko',
        ]);

        $bcaUsaha = Account::create([
            'name' => 'BCA Rekening Usaha',
            'type' => 'business',
            'balance' => 12500000,
            'description' => 'Rekening utama modal & belanja bahan',
        ]);

        $kasPribadi = Account::create([
            'name' => 'Kas Kebutuhan Pribadi / Keluarga',
            'type' => 'personal',
            'balance' => 3000000,
            'description' => 'Dana rumah tangga, dapur, dan keperluan keluarga',
        ]);

        // 2. Produk Jadi
        $merryWijen = Product::create([
            'name' => 'Merry Wijen',
            'unit' => 'bungkus',
            'consignment_price' => 12000,
            'retail_price' => 15000,
            'stock_ready' => 150,
            'description' => 'Kue kering wijen renyah rasa manis gurih',
        ]);

        $bumbuPecel = Product::create([
            'name' => 'Bumbu Pecel Asli',
            'unit' => 'bungkus',
            'consignment_price' => 15000,
            'retail_price' => 18000,
            'stock_ready' => 100,
            'description' => 'Bumbu pecel kacang sangrai khas resep keluarga (250gr)',
        ]);

        $tingTingSusu = Product::create([
            'name' => 'Ting-Ting Susu',
            'unit' => 'bungkus',
            'consignment_price' => 10000,
            'retail_price' => 12500,
            'stock_ready' => 120,
            'description' => 'Permen ting-ting kacang karamel susu lembut',
        ]);

        // 3. Bahan Baku
        $wijen = RawMaterial::create([
            'name' => 'Wijen Putih',
            'unit' => 'kg',
            'stock' => 35.0,
            'min_stock' => 10.0,
            'cost_per_unit' => 45000,
        ]);

        $kacang = RawMaterial::create([
            'name' => 'Kacang Tanah Kupas',
            'unit' => 'kg',
            'stock' => 50.0,
            'min_stock' => 15.0,
            'cost_per_unit' => 32000,
        ]);

        $gula = RawMaterial::create([
            'name' => 'Gula Pasir',
            'unit' => 'kg',
            'stock' => 40.0,
            'min_stock' => 10.0,
            'cost_per_unit' => 17500,
        ]);

        $susu = RawMaterial::create([
            'name' => 'Susu Bubuk & SKM',
            'unit' => 'kg',
            'stock' => 20.0,
            'min_stock' => 5.0,
            'cost_per_unit' => 60000,
        ]);

        $rempah = RawMaterial::create([
            'name' => 'Rempah Cabai & Asam Pecel',
            'unit' => 'kg',
            'stock' => 15.0,
            'min_stock' => 5.0,
            'cost_per_unit' => 40000,
        ]);

        $plastik = RawMaterial::create([
            'name' => 'Plastik Standing Pouch',
            'unit' => 'pcs',
            'stock' => 500,
            'min_stock' => 100,
            'cost_per_unit' => 600,
        ]);

        $label = RawMaterial::create([
            'name' => 'Stiker Label Halala',
            'unit' => 'pcs',
            'stock' => 600,
            'min_stock' => 100,
            'cost_per_unit' => 300,
        ]);

        // 4. Resep
        // Merry Wijen
        ProductRecipe::create(['product_id' => $merryWijen->id, 'raw_material_id' => $wijen->id, 'quantity_needed' => 0.04]);
        ProductRecipe::create(['product_id' => $merryWijen->id, 'raw_material_id' => $gula->id, 'quantity_needed' => 0.02]);
        ProductRecipe::create(['product_id' => $merryWijen->id, 'raw_material_id' => $plastik->id, 'quantity_needed' => 1]);
        ProductRecipe::create(['product_id' => $merryWijen->id, 'raw_material_id' => $label->id, 'quantity_needed' => 1]);

        // Bumbu Pecel
        ProductRecipe::create(['product_id' => $bumbuPecel->id, 'raw_material_id' => $kacang->id, 'quantity_needed' => 0.15]);
        ProductRecipe::create(['product_id' => $bumbuPecel->id, 'raw_material_id' => $gula->id, 'quantity_needed' => 0.05]);
        ProductRecipe::create(['product_id' => $bumbuPecel->id, 'raw_material_id' => $rempah->id, 'quantity_needed' => 0.03]);
        ProductRecipe::create(['product_id' => $bumbuPecel->id, 'raw_material_id' => $plastik->id, 'quantity_needed' => 1]);
        ProductRecipe::create(['product_id' => $bumbuPecel->id, 'raw_material_id' => $label->id, 'quantity_needed' => 1]);

        // Ting-Ting Susu
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $kacang->id, 'quantity_needed' => 0.08]);
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $susu->id, 'quantity_needed' => 0.03]);
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $gula->id, 'quantity_needed' => 0.03]);
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $plastik->id, 'quantity_needed' => 1]);
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $label->id, 'quantity_needed' => 1]);

        // 5. Toko Mitra
        $toko1 = Store::create([
            'name' => 'Toko Barokah Jaya',
            'owner_name' => 'Ibu Hj. Aminah',
            'phone' => '081234567890',
            'address' => 'Jl. Pasar Kliwon No. 12',
            'route' => 'Rute Pasar Besar',
            'commission_rate' => 0,
            'notes' => 'Pembayaran tunai langsung saat jemput barang',
        ]);

        $toko2 = Store::create([
            'name' => 'Minimarket Rezeki',
            'owner_name' => 'Pak Joko',
            'phone' => '081398765432',
            'address' => 'Jl. Raya Barat No. 88',
            'route' => 'Rute Barat',
            'commission_rate' => 0,
            'notes' => 'Titip di rak depan kasir',
        ]);

        $toko3 = Store::create([
            'name' => 'Toko Snack Bu Hartati',
            'owner_name' => 'Bu Hartati',
            'phone' => '082155443322',
            'address' => 'Komplek Pertokoan Sentral Blok A3',
            'route' => 'Rute Kota',
            'commission_rate' => 0,
            'notes' => 'Laku cepat untuk Ting-Ting Susu',
        ]);

        $toko4 = Store::create([
            'name' => 'Toko Oleh-Oleh Asli',
            'owner_name' => 'Pak Hendra',
            'phone' => '085611223344',
            'address' => 'Jl. Pahlawan No. 45',
            'route' => 'Rute Wisata',
            'commission_rate' => 0,
            'notes' => 'Pengambilan tiap akhir pekan',
        ]);

        // 6. Transaksi Konsinyasi Aktif (Contoh: Titip di Toko Barokah Jaya)
        $consignment1 = Consignment::create([
            'consignment_number' => 'KNS-'.date('Ym').'-001',
            'store_id' => $toko1->id,
            'drop_date' => Carbon::now()->subDays(10)->toDateString(),
            'status' => 'active',
            'payment_status' => 'unpaid',
            'notes' => 'Titip 40 Merry Wijen & 20 Bumbu Pecel',
        ]);

        ConsignmentItem::create([
            'consignment_id' => $consignment1->id,
            'product_id' => $merryWijen->id,
            'quantity_dropped' => 40,
            'price_per_item' => 12000,
            'quantity_remaining' => null,
            'quantity_returned' => 0,
            'quantity_sold' => 0,
            'subtotal' => 0,
        ]);

        ConsignmentItem::create([
            'consignment_id' => $consignment1->id,
            'product_id' => $bumbuPecel->id,
            'quantity_dropped' => 20,
            'price_per_item' => 15000,
            'quantity_remaining' => null,
            'quantity_returned' => 0,
            'quantity_sold' => 0,
            'subtotal' => 0,
        ]);

        // 7. Transaksi Konsinyasi Selesai (Histori Penjualan untuk Trend Chart)
        $completedSamples = [
            ['store' => $toko1, 'days_ago' => 24, 'drop_days' => 31, 'items' => [[$merryWijen, 30, 25], [$tingTingSusu, 20, 18]]],
            ['store' => $toko2, 'days_ago' => 19, 'drop_days' => 26, 'items' => [[$bumbuPecel, 25, 22], [$merryWijen, 20, 19]]],
            ['store' => $toko3, 'days_ago' => 14, 'drop_days' => 21, 'items' => [[$tingTingSusu, 40, 38], [$merryWijen, 30, 28]]],
            ['store' => $toko4, 'days_ago' => 10, 'drop_days' => 17, 'items' => [[$bumbuPecel, 30, 28], [$tingTingSusu, 25, 25]]],
            ['store' => $toko1, 'days_ago' => 6,  'drop_days' => 13, 'items' => [[$merryWijen, 35, 32], [$bumbuPecel, 20, 18]]],
            ['store' => $toko2, 'days_ago' => 3,  'drop_days' => 9,  'items' => [[$tingTingSusu, 30, 28], [$merryWijen, 25, 24]]],
            ['store' => $toko3, 'days_ago' => 1,  'drop_days' => 7,  'items' => [[$merryWijen, 40, 39], [$bumbuPecel, 25, 24]]],
        ];

        foreach ($completedSamples as $idx => $sample) {
            $settleDate = Carbon::now()->subDays($sample['days_ago'])->toDateString();
            $dropDate = Carbon::now()->subDays($sample['drop_days'])->toDateString();

            $totalGross = 0;
            $itemsData = [];
            foreach ($sample['items'] as $it) {
                $prod = $it[0];
                $dropped = $it[1];
                $sold = $it[2];
                $subtotal = $sold * $prod->consignment_price;
                $totalGross += $subtotal;
                $itemsData[] = [
                    'product_id' => $prod->id,
                    'quantity_dropped' => $dropped,
                    'quantity_remaining' => $dropped - $sold,
                    'quantity_returned' => 0,
                    'quantity_sold' => $sold,
                    'price_per_item' => $prod->consignment_price,
                    'subtotal' => $subtotal,
                ];
            }

            $c = Consignment::create([
                'consignment_number' => 'KNS-'.date('Ym').'-'.str_pad((string) ($idx + 2), 3, '0', STR_PAD_LEFT),
                'store_id' => $sample['store']->id,
                'drop_date' => $dropDate,
                'settlement_date' => $settleDate,
                'status' => 'completed',
                'payment_status' => 'paid',
                'total_sold_amount' => $totalGross,
                'total_commission' => 0,
                'total_net_received' => $totalGross,
                'amount_paid' => $totalGross,
                'notes' => 'Tagihan selesai & lunas disetor ke kas',
            ]);

            foreach ($itemsData as $row) {
                $row['consignment_id'] = $c->id;
                ConsignmentItem::create($row);
            }

            // Catat transaksi kas pemasukan
            CashTransaction::create([
                'account_id' => $kasTunai->id,
                'type' => 'income',
                'category' => 'Setoran Konsinyasi',
                'amount' => $totalGross,
                'transaction_date' => $settleDate,
                'description' => "Setoran hasil titipan dari {$sample['store']->name} (#{$c->consignment_number})",
                'consignment_id' => $c->id,
            ]);
        }

        // Additional completed consignments over previous 60 days
        $moreSamples = [
            ['store' => $toko4, 'days_ago' => 55, 'drop_days' => 62, 'items' => [[$merryWijen, 30, 28], [$bumbuPecel, 20, 19]]],
            ['store' => $toko1, 'days_ago' => 48, 'drop_days' => 55, 'items' => [[$tingTingSusu, 45, 42], [$merryWijen, 25, 23]]],
            ['store' => $toko2, 'days_ago' => 42, 'drop_days' => 49, 'items' => [[$bumbuPecel, 30, 29], [$tingTingSusu, 20, 19]]],
            ['store' => $toko3, 'days_ago' => 38, 'drop_days' => 45, 'items' => [[$merryWijen, 40, 36], [$tingTingSusu, 35, 33]]],
            ['store' => $toko1, 'days_ago' => 32, 'drop_days' => 39, 'items' => [[$bumbuPecel, 35, 32], [$merryWijen, 30, 29]]],
            ['store' => $toko2, 'days_ago' => 28, 'drop_days' => 35, 'items' => [[$tingTingSusu, 50, 48], [$bumbuPecel, 20, 20]]],
            ['store' => $toko4, 'days_ago' => 22, 'drop_days' => 29, 'items' => [[$merryWijen, 35, 33], [$bumbuPecel, 25, 24]]],
            ['store' => $toko3, 'days_ago' => 16, 'drop_days' => 23, 'items' => [[$tingTingSusu, 40, 38], [$merryWijen, 30, 29]]],
            ['store' => $toko1, 'days_ago' => 8,  'drop_days' => 15, 'items' => [[$bumbuPecel, 30, 29], [$tingTingSusu, 25, 24]]],
            ['store' => $toko4, 'days_ago' => 4,  'drop_days' => 11, 'items' => [[$merryWijen, 50, 47], [$tingTingSusu, 30, 29]]],
            ['store' => $toko2, 'days_ago' => 1,  'drop_days' => 8,  'items' => [[$bumbuPecel, 25, 25], [$merryWijen, 35, 34]]],
        ];

        foreach ($moreSamples as $idx => $sample) {
            $settleDate = Carbon::now()->subDays($sample['days_ago'])->toDateString();
            $dropDate = Carbon::now()->subDays($sample['drop_days'])->toDateString();

            $totalGross = 0;
            $itemsData = [];
            foreach ($sample['items'] as $it) {
                $prod = $it[0];
                $dropped = $it[1];
                $sold = $it[2];
                $subtotal = $sold * $prod->consignment_price;
                $totalGross += $subtotal;
                $itemsData[] = [
                    'product_id' => $prod->id,
                    'quantity_dropped' => $dropped,
                    'quantity_remaining' => $dropped - $sold,
                    'quantity_returned' => 0,
                    'quantity_sold' => $sold,
                    'price_per_item' => $prod->consignment_price,
                    'subtotal' => $subtotal,
                ];
            }

            $c = Consignment::create([
                'consignment_number' => 'KNS-'.date('Ym').'-'.str_pad((string) ($idx + 10), 3, '0', STR_PAD_LEFT),
                'store_id' => $sample['store']->id,
                'drop_date' => $dropDate,
                'settlement_date' => $settleDate,
                'status' => 'completed',
                'payment_status' => 'paid',
                'total_sold_amount' => $totalGross,
                'total_commission' => 0,
                'total_net_received' => $totalGross,
                'amount_paid' => $totalGross,
                'notes' => 'Tagihan konsinyasi selesai',
            ]);

            foreach ($itemsData as $row) {
                $row['consignment_id'] = $c->id;
                ConsignmentItem::create($row);
            }

            CashTransaction::create([
                'account_id' => $kasTunai->id,
                'type' => 'income',
                'category' => 'Setoran Konsinyasi',
                'amount' => $totalGross,
                'transaction_date' => $settleDate,
                'description' => "Setoran hasil titipan dari {$sample['store']->name} (#{$c->consignment_number})",
                'consignment_id' => $c->id,
            ]);
        }

        // Biaya Operasional Sampel
        $expenseSamples = [
            ['days_ago' => 50, 'amount' => 650000, 'category' => 'Belanja Bahan Baku', 'desc' => 'Beli Wijen & Kacang Tanah karungan'],
            ['days_ago' => 35, 'amount' => 520000, 'category' => 'Belanja Bahan Baku', 'desc' => 'Beli Gula Pasir & Susu Bubuk'],
            ['days_ago' => 20, 'amount' => 450000, 'category' => 'Belanja Bahan Baku', 'desc' => 'Beli Tepung & Gula Pasir untuk produksi mingguan'],
            ['days_ago' => 12, 'amount' => 320000, 'category' => 'Belanja Kemasan', 'desc' => 'Beli Plastik standing pouch & stiker label Halala'],
            ['days_ago' => 5,  'amount' => 150000, 'category' => 'Operasional & Bensin', 'desc' => 'Bensin motor operasional keliling jemput tagihan'],
        ];

        foreach ($expenseSamples as $idx => $exp) {
            CashTransaction::create([
                'account_id' => $kasTunai->id,
                'type' => 'expense',
                'category' => $exp['category'],
                'amount' => $exp['amount'],
                'transaction_date' => Carbon::now()->subDays($exp['days_ago'])->toDateString(),
                'description' => $exp['desc'],
            ]);
        }
    }
}
