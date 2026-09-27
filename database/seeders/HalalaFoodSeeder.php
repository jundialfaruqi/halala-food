<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Production;
use App\Models\Product;
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
            'balance' => 873000,
            'description' => 'Uang tunai operasional harian dan hasil jemput tagihan toko',
        ]);

        $bcaUsaha = Account::create([
            'name' => 'BCA Rekening Usaha',
            'type' => 'business',
            'balance' => 2000000,
            'description' => 'Rekening bank utama modal usaha dan simpanan laba',
        ]);

        $kasPribadi = Account::create([
            'name' => 'Kas Kebutuhan Pribadi / Keluarga',
            'type' => 'personal',
            'balance' => 1500000,
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

        // 3. Master Bahan Baku Kemasan Riil Pasar (Total Modal Belanja Rp 1.627.000)
        $kacang = RawMaterial::create([
            'name' => 'Kacang Tanah Sangrai',
            'unit' => 'bungkus',
            'stock' => 14.0,
            'min_stock' => 3.0,
            'cost_per_unit' => 33000, // Bungkus 1 kg
        ]);

        $susu = RawMaterial::create([
            'name' => 'Susu Bubuk Full Cream',
            'unit' => 'box',
            'stock' => 15.0,
            'min_stock' => 3.0,
            'cost_per_unit' => 38000, // Box 400 gram
        ]);

        $gula = RawMaterial::create([
            'name' => 'Gula Pasir Kristal',
            'unit' => 'bungkus',
            'stock' => 8.0,
            'min_stock' => 2.0,
            'cost_per_unit' => 17500, // Bungkus 1 kg
        ]);

        $mentega = RawMaterial::create([
            'name' => 'Mentega / Margarin',
            'unit' => 'bungkus',
            'stock' => 10.0,
            'min_stock' => 2.0,
            'cost_per_unit' => 9500, // Bungkus 200 gram
        ]);

        $pouch = RawMaterial::create([
            'name' => 'Standing Pouch Klip (150g)',
            'unit' => 'pak',
            'stock' => 4.0,
            'min_stock' => 1.0,
            'cost_per_unit' => 45000, // Pak isi 50 pcs
        ]);

        $stiker = RawMaterial::create([
            'name' => 'Stiker Label Kemasan Halala',
            'unit' => 'lembar',
            'stock' => 5.0,
            'min_stock' => 1.0,
            'cost_per_unit' => 18000, // Lembar A3 isi 40 stiker
        ]);

        $wrap = RawMaterial::create([
            'name' => 'Plastik Seal Satuan (Dalam)',
            'unit' => 'pak',
            'stock' => 2.0,
            'min_stock' => 1.0,
            'cost_per_unit' => 45000, // Pak isi 1.000 lembar
        ]);

        // 4. Master Produk Jadi (Ting Ting Susu 150g isi 10 pcs)
        $tingTingSusu = Product::create([
            'name' => 'Ting Ting Susu (Pouch 150g / 10 pcs)',
            'unit' => 'bungkus',
            'consignment_price' => 12500, // Harga setor titip ke toko
            'retail_price' => 15000, // Harga ecer toko ke pembeli (Toko untung Rp 2.500)
            'stock_ready' => 200, // Hasil produksi 200 pouch
            'description' => 'Camilan manis gurih khas dengan kacang sangrai renyah dan susu gurih, kemasan pouch 150 gram isi 10 butir.',
            'is_active' => true,
        ]);

        // 5. Resep Produk (BOM per 1 Pouch 150g)
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $kacang->id, 'quantity_needed' => 0.0700]); // 70g dari 1 bungkus 1kg
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $susu->id, 'quantity_needed' => 0.0750]); // 30g dari 1 box 400g
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $gula->id, 'quantity_needed' => 0.0400]); // 40g dari 1 bungkus 1kg
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $mentega->id, 'quantity_needed' => 0.0500]); // 10g dari 1 bungkus 200g
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $pouch->id, 'quantity_needed' => 0.0200]); // 1 pcs dari 1 pak isi 50
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $stiker->id, 'quantity_needed' => 0.0250]); // 1 stiker dari 1 lembar A3 isi 40
        ProductRecipe::create(['product_id' => $tingTingSusu->id, 'raw_material_id' => $wrap->id, 'quantity_needed' => 0.0050]); // 10 lembar wrap dari 1 pak isi 1000

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
            'amount' => 1627000,
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
                ['account_code' => '1-1300', 'debit' => 1627000, 'credit' => 0, 'memo' => 'Persediaan Bahan Baku (Kacang, Susu, Gula, Pouch)'],
                ['account_code' => '1-1001', 'debit' => 0, 'credit' => 1627000, 'memo' => 'Pengeluaran Kas Tunai untuk Belanja Bahan'],
            ],
            referenceType: 'material_purchase',
            referenceId: 1
        );

        // C. Jurnal Pemakaian Bahan & Penyelesaian Produksi 200 Pouch
        AccountingService::postEntry(
            date: $prodDate,
            notes: 'Penyelesaian Produksi 200 Pouch Ting Ting Susu (HPP: Rp 8.135/pouch)',
            items: [
                ['account_code' => '1-1400', 'debit' => 1627000, 'credit' => 0, 'memo' => 'Persediaan Produk Jadi Siap Jual (200 Pouch)'],
                ['account_code' => '1-1300', 'debit' => 0, 'credit' => 1627000, 'memo' => 'Pemakaian Bahan Baku untuk Produksi'],
            ],
            referenceType: 'production',
            referenceId: 1
        );
    }
}
