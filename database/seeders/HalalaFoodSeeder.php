<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\Production;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\Store;
use App\Models\StoreProductBarcode;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class HalalaFoodSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Rekening & Akun Kas
        $kasTunai = Account::create([
            'name' => 'Kas Tunai Usaha (Kasir / Hasil Tagihan)',
            'type' => 'business',
            'balance' => 831000, // Rp 2.500.000 (Modal) - Rp 1.669.000 (Belanja Bahan)
            'description' => 'Uang tunai operasional harian dan hasil jemput tagihan toko',
        ]);

        $bcaUsaha = Account::create([
            'name' => 'BCA Rekening Usaha',
            'type' => 'business',
            'balance' => 0,
            'description' => 'Rekening bank utama modal usaha dan simpanan laba',
        ]);

        $kasPribadi = Account::create([
            'name' => 'Kas Kebutuhan Pribadi / Keluarga',
            'type' => 'personal',
            'balance' => 0,
            'description' => 'Dana rumah tangga dan keperluan pribadi keluarga',
        ]);

        // 2. Master Toko Mitra (2 Toko Konsinyasi)
        $toko1 = Store::create([
            'name' => 'Pusat Oleh-Oleh Barokah',
            'owner_name' => 'Ibu Hj. Aminah',
            'phone' => '081234567890',
            'address' => 'Jl. Raya Pasar Besar No. 12',
            'route' => 'Rute Pasar Kota',
            'commission_rate' => 0,
            'is_active' => true,
            'notes' => 'Rak display kaca dekat kasir utama. Jadwal cek tagihan tiap hari Sabtu.',
        ]);

        $toko2 = Store::create([
            'name' => 'Toko Snack Berkah Jaya',
            'owner_name' => 'Pak Bambang',
            'phone' => '085798765432',
            'address' => 'Jl. Ahmad Yani No. 45',
            'route' => 'Rute Jalur Utama',
            'commission_rate' => 0,
            'is_active' => true,
            'notes' => 'Rak khusus oleh-oleh makanan khas. Jadwal cek tagihan tiap hari Minggu.',
        ]);

        // 3. Master Bahan Baku Satuan Manusiawi (gram & pcs) - Total Belanja Rp 1.669.000 (Harga Riil Pasar 2026)
        $kacang = RawMaterial::create([
            'name' => 'Kacang Tanah Sangrai',
            'unit' => 'gram',
            'stock' => 0.0, // Dibeli 14.000 gram, habis terpakai produksi 200 pouch
            'min_stock' => 1000.0,
            'cost_per_unit' => 45.0, // Rp 45 / gram (Rp 45.000 / bungkus 1kg)
        ]);

        $susu = RawMaterial::create([
            'name' => 'Susu Bubuk Full Cream',
            'unit' => 'gram',
            'stock' => 0.0, // Dibeli 6.000 gram, habis terpakai produksi 200 pouch
            'min_stock' => 1000.0,
            'cost_per_unit' => 90.0, // Rp 90 / gram (Rp 90.000 / bungkus 1kg)
        ]);

        $gula = RawMaterial::create([
            'name' => 'Gula Pasir Kristal',
            'unit' => 'gram',
            'stock' => 0.0, // Dibeli 8.000 gram, habis terpakai produksi 200 pouch
            'min_stock' => 1000.0,
            'cost_per_unit' => 18.0, // Rp 18 / gram (Rp 18.000 / bungkus 1kg)
        ]);

        $mentega = RawMaterial::create([
            'name' => 'Mentega / Margarin',
            'unit' => 'gram',
            'stock' => 0.0, // Dibeli 2.000 gram, habis terpakai produksi 200 pouch
            'min_stock' => 500.0,
            'cost_per_unit' => 47.5, // Rp 47,5 / gram (Rp 9.500 / bungkus 200g)
        ]);

        $pouch = RawMaterial::create([
            'name' => 'Standing Pouch Klip (150g)',
            'unit' => 'pcs',
            'stock' => 0.0, // Dibeli 200 pcs, habis terpakai kemas 200 pouch
            'min_stock' => 50.0,
            'cost_per_unit' => 700.0, // Rp 700 / pcs (Rp 35.000 / pak 50 pcs)
        ]);

        $stiker = RawMaterial::create([
            'name' => 'Stiker Label Kemasan Halala',
            'unit' => 'pcs',
            'stock' => 0.0, // Dibeli 200 pcs, habis terpakai label 200 pouch
            'min_stock' => 50.0,
            'cost_per_unit' => 300.0, // Rp 300 / pcs (Rp 12.000 / lembar A3 40 pcs)
        ]);

        $wrap = RawMaterial::create([
            'name' => 'Plastik Seal Satuan (Dalam)',
            'unit' => 'pcs',
            'stock' => 0.0, // Dibeli 2.000 pcs, habis terpakai 2.000 butir ting-ting
            'min_stock' => 500.0,
            'cost_per_unit' => 30.0, // Rp 30 / pcs (Rp 30.000 / pak 1.000 pcs)
        ]);

        // 4. Master Produk Jadi (Ting Ting Susu 150g isi 10 pcs)
        $tingTingSusu = Product::create([
            'name' => 'Ting Ting Susu (Pouch 150g / 10 pcs)',
            'unit' => 'bungkus',
            'consignment_price' => 12500, // Harga setor titip ke toko
            'retail_price' => 15000, // Harga ecer toko ke pembeli (Toko untung Rp 2.500)
            'stock_ready' => 0, // Diproduksi 200 pouch, seluruhnya (200) sudah dititip ke 2 toko mitra
            'description' => 'Camilan manis gurih khas dengan kacang sangrai renyah dan susu gurih, kemasan pouch 150 gram isi 10 butir.',
            'is_active' => true,
        ]);

        // 5. Resep Produk Nyata (BOM per 1 Pouch 150g)
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $kacang->id, 'quantity_needed' => 70.0]); // 70 gram @ Rp 45 = Rp 3.150
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $susu->id, 'quantity_needed' => 30.0]); // 30 gram @ Rp 90 = Rp 2.700
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $gula->id, 'quantity_needed' => 40.0]); // 40 gram @ Rp 18 = Rp 720
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $mentega->id, 'quantity_needed' => 10.0]); // 10 gram @ Rp 47.5 = Rp 475
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $pouch->id, 'quantity_needed' => 1.0]); // 1 pcs @ Rp 700 = Rp 700
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $stiker->id, 'quantity_needed' => 1.0]); // 1 pcs @ Rp 300 = Rp 300
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $wrap->id, 'quantity_needed' => 10.0]); // 10 pcs @ Rp 30 = Rp 300
        // Total HPP per 1 Pouch = Rp 8.345

        // 6. Catat Riwayat Produksi Batch 200 Pouch
        $prodDate = Carbon::now()->subDays(2)->toDateString();
        Production::create([
            'production_date' => $prodDate,
            'product_id' => $tingTingSusu->id,
            'quantity_produced' => 200,
            'notes' => 'Batch 1 - Produksi perdana 200 pouch Ting Ting Susu untuk titipan 2 toko mitra',
        ]);

        // 7. Master Barcode Toko Khusus
        StoreProductBarcode::create([
            'store_id' => $toko1->id,
            'product_id' => $tingTingSusu->id,
            'barcode' => '899202615001',
            'barcode_type' => 'CODE128',
            'store_sku' => 'TTS-BAROKAH-150',
            'custom_product_name' => 'TING TING SUSU 150GR',
            'custom_price' => 15000,
            'notes' => 'Barcode kasir Pusat Oleh-Oleh Barokah',
        ]);

        StoreProductBarcode::create([
            'store_id' => $toko2->id,
            'product_id' => $tingTingSusu->id,
            'barcode' => '899202615002',
            'barcode_type' => 'CODE128',
            'store_sku' => 'TTS-BERKAH-150',
            'custom_product_name' => 'TING TING SUSU POUCH',
            'custom_price' => 15000,
            'notes' => 'Barcode kasir Toko Snack Berkah Jaya',
        ]);

        // 8. Transaksi Arus Kas (Buku Kas)
        // A. Pemasukan Modal Awal
        $txModal = CashTransaction::create([
            'account_id' => $kasTunai->id,
            'type' => 'income',
            'category' => 'Setoran Modal',
            'amount' => 2500000,
            'transaction_date' => Carbon::now()->subDays(5)->toDateString(),
            'description' => 'Setoran modal awal usaha produksi Ting Ting Susu',
        ]);

        // B. Pengeluaran Belanja Bahan Baku & Kemasan
        $txBahan = CashTransaction::create([
            'account_id' => $kasTunai->id,
            'type' => 'expense',
            'category' => 'Belanja Bahan Baku',
            'amount' => 1669000,
            'transaction_date' => Carbon::now()->subDays(4)->toDateString(),
            'description' => 'Pembelian bahan baku (Kacang, Susu, Gula, Mentega) & Kemasan (Pouch, Label, Wrap) untuk 200 pouch',
        ]);

        // 9. Transaksi Konsinyasi / Titip Jual (100 Pouch ke Toko 1 & 100 Pouch ke Toko 2)
        $consignment1 = Consignment::create([
            'consignment_number' => 'KNS-'.date('Ym').'-001',
            'store_id' => $toko1->id,
            'drop_date' => Carbon::now()->subDay()->toDateString(),
            'status' => 'active',
            'payment_status' => 'unpaid',
            'total_sold_amount' => 0,
            'total_commission' => 0,
            'total_net_received' => 0,
            'amount_paid' => 0,
            'notes' => 'Pengiriman pertama 100 pouch Ting Ting Susu',
        ]);

        ConsignmentItem::create([
            'consignment_id' => $consignment1->id,
            'product_id' => $tingTingSusu->id,
            'quantity_dropped' => 100,
            'price_per_item' => 12500,
            'quantity_remaining' => null,
            'quantity_returned' => 0,
            'quantity_sold' => 0,
            'subtotal' => 0,
        ]);

        $consignment2 = Consignment::create([
            'consignment_number' => 'KNS-'.date('Ym').'-002',
            'store_id' => $toko2->id,
            'drop_date' => Carbon::now()->subDay()->toDateString(),
            'status' => 'active',
            'payment_status' => 'unpaid',
            'total_sold_amount' => 0,
            'total_commission' => 0,
            'total_net_received' => 0,
            'amount_paid' => 0,
            'notes' => 'Pengiriman pertama 100 pouch Ting Ting Susu',
        ]);

        ConsignmentItem::create([
            'consignment_id' => $consignment2->id,
            'product_id' => $tingTingSusu->id,
            'quantity_dropped' => 100,
            'price_per_item' => 12500,
            'quantity_remaining' => null,
            'quantity_returned' => 0,
            'quantity_sold' => 0,
            'subtotal' => 0,
        ]);

        // 10. Jurnal Akuntansi Berpasangan Otomatis
        // A. Jurnal Setoran Modal Awal
        AccountingService::postEntry(
            date: Carbon::now()->subDays(5)->toDateString(),
            notes: 'Penyetoran Modal Awal Usaha Pemilik',
            items: [
                ['account_code' => '1-1001', 'debit' => 2500000, 'credit' => 0, 'memo' => 'Penerimaan Kas Tunai Usaha'],
                ['account_code' => '3-1000', 'debit' => 0, 'credit' => 2500000, 'memo' => 'Setoran Modal Pemilik'],
            ],
            referenceType: 'initial_capital',
            referenceId: 1
        );

        // B. Jurnal Pembelian Bahan Baku
        AccountingService::postEntry(
            date: Carbon::now()->subDays(4)->toDateString(),
            notes: 'Pembelian Bahan Baku & Kemasan Produksi 200 Pouch',
            items: [
                ['account_code' => '1-1300', 'debit' => 1669000, 'credit' => 0, 'memo' => 'Persediaan Bahan Baku (Kacang, Susu, Gula, Pouch)'],
                ['account_code' => '1-1001', 'debit' => 0, 'credit' => 1669000, 'memo' => 'Pengeluaran Kas Tunai untuk Belanja Bahan'],
            ],
            referenceType: 'material_purchase',
            referenceId: 1
        );

        // C. Jurnal Pemakaian Bahan & Penyelesaian Produksi 200 Pouch
        AccountingService::postEntry(
            date: $prodDate,
            notes: 'Penyelesaian Produksi 200 Pouch Ting Ting Susu (HPP: Rp 8.345/pouch)',
            items: [
                ['account_code' => '1-1400', 'debit' => 1669000, 'credit' => 0, 'memo' => 'Persediaan Produk Jadi Siap Jual (200 Pouch)'],
                ['account_code' => '1-1300', 'debit' => 0, 'credit' => 1669000, 'memo' => 'Pemakaian Bahan Baku untuk Produksi'],
            ],
            referenceType: 'production',
            referenceId: 1
        );
    }
}
