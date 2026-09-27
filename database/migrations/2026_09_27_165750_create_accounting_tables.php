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
        // 1. Bagan Akun (Chart of Accounts)
        Schema::create('chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g. 1-1001, 1-1300, 4-1000
            $table->string('name'); // e.g. Kas Tunai Usaha, Persediaan Bahan Baku
            $table->string('type'); // asset, liability, equity, revenue, cogs, expense
            $table->string('normal_balance'); // debit, credit
            $table->boolean('is_system')->default(false);
            $table->text('description')->nullable();
            $table->timestamps();
        });

        // 2. Header Jurnal Umum (Journal Entries)
        Schema::create('journal_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number')->unique(); // e.g. JU-202609-001
            $table->date('entry_date');
            $table->string('reference_type')->nullable()->index(); // consignment, production, cash_transaction, manual
            $table->unsignedBigInteger('reference_id')->nullable()->index();
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        // 3. Baris Rincian Jurnal (Journal Items)
        Schema::create('journal_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('journal_entry_id')->constrained('journal_entries')->cascadeOnDelete();
            $table->foreignId('chart_of_account_id')->constrained('chart_of_accounts')->cascadeOnDelete();
            $table->decimal('debit', 14, 2)->default(0.00);
            $table->decimal('credit', 14, 2)->default(0.00);
            $table->string('memo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('journal_items');
        Schema::dropIfExists('journal_entries');
        Schema::dropIfExists('chart_of_accounts');
    }
};
