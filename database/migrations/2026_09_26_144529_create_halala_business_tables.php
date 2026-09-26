<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Toko Mitra
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('owner_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('route')->nullable()->index(); // misal: Rute Timur, Rute Pasar, Rute Kota
            $table->decimal('commission_rate', 5, 2)->default(0.00); // persentase bagi hasil toko jika ada
            $table->boolean('is_active')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 2. Produk Jadi (Merry Wijen, Bumbu Pecel, Ting-Ting Susu)
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('unit')->default('pcs'); // pcs, toples, bungkus
            $table->decimal('consignment_price', 12, 2)->default(0.00); // harga setor/titip ke toko
            $table->decimal('retail_price', 12, 2)->default(0.00); // harga ecer rekomendasi
            $table->integer('stock_ready')->default(0); // stok siap jual di gudang
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Bahan Baku
        Schema::create('raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Wijen, Gula Pasir, Kacang, Susu Kental Manis, Plastik
            $table->string('unit')->default('kg'); // kg, gram, pcs, liter
            $table->decimal('stock', 12, 2)->default(0.00);
            $table->decimal('min_stock', 12, 2)->default(0.00); // batas peringatan stok menipis
            $table->decimal('cost_per_unit', 12, 2)->default(0.00); // harga beli terakhir
            $table->timestamps();
        });

        // 4. Resep / Bill of Material (BOM) per 1 Pcs Produk Jadi
        Schema::create('product_recipes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignId('raw_material_id')->constrained('raw_materials')->cascadeOnDelete();
            $table->decimal('quantity_needed', 10, 4); // jumlah bahan yang terpakai per 1 pcs produk jadi
            $table->timestamps();
        });

        // 5. Riwayat Produksi
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->date('production_date');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity_produced');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 6. Transaksi Konsinyasi / Titip Jual
        Schema::create('consignments', function (Blueprint $table) {
            $table->id();
            $table->string('consignment_number')->unique();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->date('drop_date');
            $table->date('settlement_date')->nullable(); // tanggal cek sisa & penagihan
            $table->string('status')->default('active'); // active (sedang dititip), completed (selesai hitung), cancelled
            $table->string('payment_status')->default('unpaid'); // unpaid, partial, paid
            $table->decimal('total_sold_amount', 14, 2)->default(0.00); // total nilai rupiah terjual
            $table->decimal('total_commission', 14, 2)->default(0.00); // potongan toko jika ada
            $table->decimal('total_net_received', 14, 2)->default(0.00); // uang bersih yang harus disetor toko
            $table->decimal('amount_paid', 14, 2)->default(0.00); // uang tunai yang sudah diterima
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        // 7. Rincian Item Konsinyasi
        Schema::create('consignment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('consignment_id')->constrained('consignments')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity_dropped'); // jumlah pcs titip awal
            $table->decimal('price_per_item', 12, 2); // harga per pcs saat transaksi
            $table->integer('quantity_remaining')->nullable(); // sisa saat dicek
            $table->integer('quantity_returned')->default(0); // retur/rusak/expired ditarik pulang
            $table->integer('quantity_sold')->default(0); // laku terjual
            $table->decimal('subtotal', 14, 2)->default(0.00); // quantity_sold * price_per_item
            $table->timestamps();
        });

        // 8. Rekening / Akun Kas (Kas Usaha vs Kas Pribadi)
        Schema::create('accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // misal: Kas Tunai Usaha, BCA Usaha, Kas Pribadi / Keluarga
            $table->string('type')->default('business'); // business, personal
            $table->decimal('balance', 14, 2)->default(0.00);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 9. Buku Kas & Transaksi Keuangan
        Schema::create('cash_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('transaction_date');
            $table->foreignId('account_id')->constrained('accounts')->cascadeOnDelete();
            $table->string('type'); // income (pemasukan usaha), expense (pengeluaran usaha), prive (tarik uang usaha untuk pribadi), personal_expense (pengeluaran kas pribadi)
            $table->string('category'); // Penjualan Toko, Beli Bahan, Bensin/Transport, Listrik Usaha, Kebutuhan Rumah Tangga, dll.
            $table->decimal('amount', 14, 2);
            $table->foreignId('consignment_id')->nullable()->constrained('consignments')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cash_transactions');
        Schema::dropIfExists('accounts');
        Schema::dropIfExists('consignment_items');
        Schema::dropIfExists('consignments');
        Schema::dropIfExists('productions');
        Schema::dropIfExists('product_recipes');
        Schema::dropIfExists('raw_materials');
        Schema::dropIfExists('products');
        Schema::dropIfExists('stores');
    }
};
