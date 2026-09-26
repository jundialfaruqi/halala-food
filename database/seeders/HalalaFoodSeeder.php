<?php

namespace Database\Seeders;

use App\Models\Account;
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

        // 6. Transaksi Konsinyasi Aktif (Contoh: Titip 40 pcs di Toko Barokah Jaya tanggal 10)
        $consignment1 = Consignment::create([
            'consignment_number' => 'KNS-'.date('Ym').'-001',
            'store_id' => $toko1->id,
            'drop_date' => Carbon::now()->subDays(10)->toDateString(),
            'status' => 'active', // belum dicek/selesai
            'payment_status' => 'unpaid',
            'notes' => 'Titip 40 Merry Wijen & 20 Bumbu Pecel',
        ]);

        ConsignmentItem::create([
            'consignment_id' => $consignment1->id,
            'product_id' => $merryWijen->id,
            'quantity_dropped' => 40,
            'price_per_item' => 12000,
            'quantity_remaining' => null, // belum diinput
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
    }
}
