<?php

use App\Livewire\Accounting\FinancialStatements;
use App\Livewire\Accounting\Journals;
use App\Livewire\Accounting\Ledger;
use App\Livewire\CashBook\Index as CashBookIndex;
use App\Livewire\Consignments\Form as ConsignmentForm;
use App\Livewire\Productions\Index as ProductionsIndex;
use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\Consignment;
use App\Models\JournalEntry;
use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use App\Models\Store;
use App\Models\User;
use Database\Seeders\AccountingSeeder;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(AccountingSeeder::class);
    actingAs(User::factory()->create());
});

test('full real food business flow: capital -> materials -> production -> consignment drops -> settlements -> balance sheet & profit/loss', function () {
    // 1. SETUP AKUN KAS USAHA
    $kasTunai = Account::create([
        'name' => 'Kas Tunai Usaha',
        'type' => 'business',
        'balance' => 0,
    ]);

    // STEP 1: Setoran Modal Awal Rp 2.500.000
    Livewire::test(CashBookIndex::class)
        ->set('type', 'income')
        ->set('account_id', $kasTunai->id)
        ->set('category', 'Setoran Modal')
        ->set('amount', 2500000)
        ->set('transaction_date', now()->format('Y-m-d'))
        ->set('description', 'Setoran modal awal usaha')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    expect((float) $kasTunai->fresh()->balance)->toBe(2500000.0);

    // Verifikasi Jurnal Modal Masuk (Dr. 1-1001 Rp 2.5M, Cr. 3-1000 Rp 2.5M)
    $journalModal = JournalEntry::where('notes', 'like', '%Setoran modal awal usaha%')->first();
    expect($journalModal)->not->toBeNull();
    expect($journalModal->items->firstWhere('debit', '>', 0)->chart_of_account_id)
        ->toBe(ChartOfAccount::where('code', '1-1001')->first()->id);
    expect($journalModal->items->firstWhere('credit', '>', 0)->chart_of_account_id)
        ->toBe(ChartOfAccount::where('code', '3-1000')->first()->id);

    // STEP 2: Pembelian Bahan Baku Rp 1.669.000
    Livewire::test(CashBookIndex::class)
        ->set('type', 'expense')
        ->set('account_id', $kasTunai->id)
        ->set('category', 'Pembelian Bahan Baku')
        ->set('amount', 1669000)
        ->set('transaction_date', now()->format('Y-m-d'))
        ->set('description', 'Beli kacang 14kg, susu 6kg, gula 8kg, mentega 2kg, pouch 200pcs, stiker 200pcs, wrap 2000pcs')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    expect((float) $kasTunai->fresh()->balance)->toBe(831000.0);

    // Verifikasi Jurnal Belanja Bahan (Dr. 1-1300 Rp 1.669.000, Cr. 1-1001 Rp 1.669.000)
    $journalBahan = JournalEntry::where('notes', 'like', '%Beli kacang 14kg%')->first();
    expect($journalBahan)->not->toBeNull();
    expect($journalBahan->items->firstWhere('debit', '>', 0)->chart_of_account_id)
        ->toBe(ChartOfAccount::where('code', '1-1300')->first()->id);
    expect($journalBahan->items->firstWhere('credit', '>', 0)->chart_of_account_id)
        ->toBe(ChartOfAccount::where('code', '1-1001')->first()->id);

    // STEP 3: Setup Master Bahan Baku & Resep untuk 200 Pouch (Harga Riil Pasar 2026)
    $kacang = RawMaterial::create(['name' => 'Kacang Tanah Sangrai', 'unit' => 'gram', 'stock' => 14000.0, 'cost_per_unit' => 45.0]);
    $susu = RawMaterial::create(['name' => 'Susu Bubuk Full Cream', 'unit' => 'gram', 'stock' => 6000.0, 'cost_per_unit' => 90.0]);
    $gula = RawMaterial::create(['name' => 'Gula Pasir Kristal', 'unit' => 'gram', 'stock' => 8000.0, 'cost_per_unit' => 18.0]);
    $mentega = RawMaterial::create(['name' => 'Mentega / Margarin', 'unit' => 'gram', 'stock' => 2000.0, 'cost_per_unit' => 47.5]);
    $pouch = RawMaterial::create(['name' => 'Standing Pouch Klip (150g)', 'unit' => 'pcs', 'stock' => 200.0, 'cost_per_unit' => 700.0]);
    $stiker = RawMaterial::create(['name' => 'Stiker Label Kemasan', 'unit' => 'pcs', 'stock' => 200.0, 'cost_per_unit' => 300.0]);
    $wrap = RawMaterial::create(['name' => 'Plastik Seal Satuan', 'unit' => 'pcs', 'stock' => 2000.0, 'cost_per_unit' => 30.0]);

    $product = Product::create([
        'name' => 'Ting Ting Susu (Pouch 150g / 10 pcs)',
        'unit' => 'bungkus',
        'consignment_price' => 12500,
        'retail_price' => 15000,
        'stock_ready' => 0,
        'is_active' => true,
    ]);

    ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $kacang->id, 'quantity_needed' => 70.0]);
    ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $susu->id, 'quantity_needed' => 30.0]);
    ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $gula->id, 'quantity_needed' => 40.0]);
    ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $mentega->id, 'quantity_needed' => 10.0]);
    ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $pouch->id, 'quantity_needed' => 1.0]);
    ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $stiker->id, 'quantity_needed' => 1.0]);
    ProductRecipe::create(['product_id' => $product->id, 'raw_material_id' => $wrap->id, 'quantity_needed' => 10.0]);

    // Material cost per 1 pouch should be exactly Rp 8.345
    expect($product->fresh()->material_cost)->toBe(8345.0);

    // STEP 4: Produksi 200 Pouch
    Livewire::test(ProductionsIndex::class)
        ->set('product_id', $product->id)
        ->set('quantity_produced', '200')
        ->set('production_date', now()->format('Y-m-d'))
        ->set('notes', 'Batch 200 pouch Ting Ting Susu')
        ->call('save')
        ->assertHasNoErrors();

    expect((int) $product->fresh()->stock_ready)->toBe(200);
    expect((float) $kacang->fresh()->stock)->toBe(0.0);
    expect((float) $susu->fresh()->stock)->toBe(0.0);
    expect((float) $pouch->fresh()->stock)->toBe(0.0);

    // Verifikasi Jurnal Produksi (Dr. 1-1400 Rp 1.669.000, Cr. 1-1300 Rp 1.669.000)
    $journalProd = JournalEntry::where('reference_type', 'production')->first();
    expect($journalProd)->not->toBeNull();
    expect((float) $journalProd->total_debit)->toBe(1669000.0);
    expect((float) $journalProd->total_credit)->toBe(1669000.0);
    expect($journalProd->items->firstWhere('debit', '>', 0)->account->code)->toBe('1-1400');
    expect($journalProd->items->firstWhere('credit', '>', 0)->account->code)->toBe('1-1300');

    // CEK LAPORAN SEBELUM KONSINYASI:
    // Persediaan Bahan = 0, Persediaan Jadi = 1.669.000, Kas = 831.000 => Total Aset = 2.500.000
    // Modal = 2.500.000, Laba = 0 => Total Ekuitas = 2.500.000 (BALANCED)
    Livewire::test(FinancialStatements::class)
        ->assertViewHas('totalRevenue', 0.0)
        ->assertViewHas('totalCogs', 0.0)
        ->assertViewHas('netIncome', 0.0)
        ->assertViewHas('totalAssets', 2500000.0)
        ->assertViewHas('totalEquity', 2500000.0)
        ->assertViewHas('totalLiabilitiesAndEquity', 2500000.0);

    // STEP 5: Drop 100 Pouch ke Toko 1 & 100 Pouch ke Toko 2
    $toko1 = Store::create(['name' => 'Pusat Oleh-Oleh Barokah', 'is_active' => true]);
    $toko2 = Store::create(['name' => 'Toko Snack Berkah Jaya', 'is_active' => true]);

    Livewire::test(ConsignmentForm::class)
        ->set('store_id', $toko1->id)
        ->set('drop_date', now()->format('Y-m-d'))
        ->set('items.0.quantity_dropped', '100')
        ->call('saveDrop')
        ->assertHasNoErrors();

    expect((int) $product->fresh()->stock_ready)->toBe(100);

    Livewire::test(ConsignmentForm::class)
        ->set('store_id', $toko2->id)
        ->set('drop_date', now()->format('Y-m-d'))
        ->set('items.0.quantity_dropped', '100')
        ->call('saveDrop')
        ->assertHasNoErrors();

    expect((int) $product->fresh()->stock_ready)->toBe(0);

    // STEP 6: Pelunasan/Setoran Toko 1 (100 pouch laku @ Rp 12.500 = Rp 1.250.000)
    $consignment1 = Consignment::where('store_id', $toko1->id)->first();
    Livewire::test(ConsignmentForm::class, ['consignment' => $consignment1])
        ->set('settlement_date', now()->format('Y-m-d'))
        ->set('account_id', $kasTunai->id)
        ->set('items.0.quantity_remaining', '0')
        ->set('items.0.quantity_returned', '0')
        ->set('amount_paid', '1250000')
        ->call('saveSettlement')
        ->assertHasNoErrors();

    // Kas bertambah jadi 831.000 + 1.250.000 = 2.081.000
    expect((float) $kasTunai->fresh()->balance)->toBe(2081000.0);

    // STEP 7: Pelunasan/Setoran Toko 2 (100 pouch laku @ Rp 12.500 = Rp 1.250.000)
    $consignment2 = Consignment::where('store_id', $toko2->id)->first();
    Livewire::test(ConsignmentForm::class, ['consignment' => $consignment2])
        ->set('settlement_date', now()->format('Y-m-d'))
        ->set('account_id', $kasTunai->id)
        ->set('items.0.quantity_remaining', '0')
        ->set('items.0.quantity_returned', '0')
        ->set('amount_paid', '1250000')
        ->call('saveSettlement')
        ->assertHasNoErrors();

    // Kas akhir bertambah jadi 2.081.000 + 1.250.000 = 3.331.000
    expect((float) $kasTunai->fresh()->balance)->toBe(3331000.0);

    // STEP 8: Verifikasi Akuntansi Akhir
    // 1. Laba Rugi:
    //    - Pendapatan Penjualan: Rp 2.500.000
    //    - HPP: Rp 1.669.000
    //    - Laba Bersih: Rp 831.000
    // 2. Neraca:
    //    - Kas Tunai: Rp 3.331.000
    //    - Persediaan Bahan: Rp 0
    //    - Persediaan Jadi: Rp 0
    //    - Total Aset: Rp 3.331.000
    //    - Total Kewajiban: Rp 0
    //    - Modal Pemilik: Rp 2.500.000
    //    - Laba Berjalan: Rp 831.000
    //    - Total Ekuitas: Rp 3.331.000
    //    - Total Kewajiban & Ekuitas: Rp 3.331.000 (100% BALANCE)
    Livewire::test(FinancialStatements::class)
        ->assertViewHas('totalRevenue', 2500000.0)
        ->assertViewHas('totalCogs', 1669000.0)
        ->assertViewHas('grossProfit', 831000.0)
        ->assertViewHas('totalExpense', 0.0)
        ->assertViewHas('netIncome', 831000.0)
        ->assertViewHas('totalAssets', 3331000.0)
        ->assertViewHas('totalLiabilities', 0.0)
        ->assertViewHas('cumulativeNetIncome', 831000.0)
        ->assertViewHas('totalEquity', 3331000.0)
        ->assertViewHas('totalLiabilitiesAndEquity', 3331000.0);

    // Verifikasi Buku Besar (Ledger) Akun Kas Tunai Usaha
    $kasAccount = ChartOfAccount::where('code', '1-1001')->first();
    Livewire::test(Ledger::class)
        ->set('selectedAccountId', $kasAccount->id)
        ->assertViewHas('totalDebit', 5000000.0) // 2.5M modal + 1.25M toko1 + 1.25M toko2
        ->assertViewHas('totalCredit', 1669000.0) // 1.669M belanja bahan
        ->assertViewHas('runningBalance', 3331000.0);

    // Verifikasi Jurnal Umum (Journals)
    // 2.5M (Modal) + 1.669M (Bahan) + 1.669M (Produksi) + 2.0845M (Toko 1) + 2.0845M (Toko 2) = 10.007.000
    Livewire::test(Journals::class)
        ->assertViewHas('totalDebit', 10007000.0)
        ->assertViewHas('totalCredit', 10007000.0);
});
