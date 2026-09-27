<?php

use App\Livewire\Accounting\FinancialStatements;
use App\Livewire\Accounting\Journals;
use App\Livewire\Accounting\Ledger;
use App\Livewire\CashBook\Index as CashBookIndex;
use App\Livewire\Consignments\Form as ConsignmentForm;
use App\Livewire\Productions\Index as ProductionsIndex;
use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\ChartOfAccount;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
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

test('chart of accounts are properly initialized', function () {
    expect(ChartOfAccount::count())->toBeGreaterThanOrEqual(15);
    expect(ChartOfAccount::where('code', '1-1001')->exists())->toBeTrue();
    expect(ChartOfAccount::where('code', '4-1000')->exists())->toBeTrue();
    expect(ChartOfAccount::where('code', '5-1000')->exists())->toBeTrue();
});

test('cash transaction automatically records balanced journal entry', function () {
    $account = Account::create([
        'name' => 'Kas Tunai Usaha',
        'type' => 'business',
        'balance' => 500000,
    ]);

    Livewire::test(CashBookIndex::class)
        ->set('type', 'expense')
        ->set('account_id', $account->id)
        ->set('category', 'Bensin & Operasional Motor')
        ->set('amount', 50000)
        ->set('transaction_date', now()->format('Y-m-d'))
        ->set('description', 'Beli bensin motor antar titipan')
        ->call('saveTransaction')
        ->assertHasNoErrors();

    $transaction = CashTransaction::latest('id')->first();
    expect($transaction)->not->toBeNull();

    $journal = JournalEntry::where('reference_type', 'cash_transaction')
        ->where('reference_id', $transaction->id)
        ->first();

    expect($journal)->not->toBeNull();
    expect((float) $journal->total_debit)->toBe(50000.0);
    expect((float) $journal->total_credit)->toBe(50000.0);
    expect($journal->items)->toHaveCount(2);

    // Debit: Beban Bensin & Transportasi (6-1001), Credit: Kas Tunai Usaha (1-1001)
    $debitItem = $journal->items->where('debit', '>', 0)->first();
    $creditItem = $journal->items->where('credit', '>', 0)->first();

    expect($debitItem->account->code)->toBe('6-1001');
    expect($creditItem->account->code)->toBe('1-1001');
});

test('production automatically records balanced raw materials to finished goods journal entry', function () {
    $flour = RawMaterial::create([
        'name' => 'Tepung Terigu',
        'unit' => 'kg',
        'stock' => 50,
        'cost_per_unit' => 12000,
    ]);

    $product = Product::create([
        'name' => 'Roti Manis',
        'unit' => 'pcs',
        'stock_ready' => 0,
        'cost_price' => 5000,
        'consignment_price' => 8000,
        'store_selling_price' => 10000,
        'is_active' => true,
    ]);

    ProductRecipe::create([
        'product_id' => $product->id,
        'raw_material_id' => $flour->id,
        'quantity_needed' => 0.1, // 0.1 kg per pcs = Rp 1.200 per pcs
    ]);

    Livewire::test(ProductionsIndex::class)
        ->set('product_id', $product->id)
        ->set('quantity_produced', '10')
        ->set('production_date', now()->format('Y-m-d'))
        ->set('notes', 'Batch Pagi')
        ->call('save')
        ->assertHasNoErrors();

    // Total raw material cost: 10 * 0.1 * 12000 = 12,000
    $journal = JournalEntry::where('reference_type', 'production')->first();
    expect($journal)->not->toBeNull();
    expect((float) $journal->total_debit)->toBe(12000.0);
    expect((float) $journal->total_credit)->toBe(12000.0);

    // Debit: Persediaan Produk Jadi (1-1400), Credit: Persediaan Bahan Baku (1-1300)
    $debitItem = $journal->items->where('debit', '>', 0)->first();
    $creditItem = $journal->items->where('credit', '>', 0)->first();

    expect($debitItem->account->code)->toBe('1-1400');
    expect($creditItem->account->code)->toBe('1-1300');
});

test('consignment settlement automatically records balanced revenue, cogs, inventory, and receivables journal entry', function () {
    $store = Store::create([
        'name' => 'Toko Barokah',
        'is_active' => true,
    ]);

    $singkong = RawMaterial::create([
        'name' => 'Singkong Mentah',
        'unit' => 'kg',
        'stock' => 50,
        'cost_per_unit' => 5000,
    ]);

    $product = Product::create([
        'name' => 'Keripik Singkong',
        'unit' => 'bks',
        'stock_ready' => 20,
        'consignment_price' => 8000,
        'retail_price' => 10000,
        'is_active' => true,
    ]);

    ProductRecipe::create([
        'product_id' => $product->id,
        'raw_material_id' => $singkong->id,
        'quantity_needed' => 1, // 1 kg = Rp 5.000 modal per bungkus
    ]);

    $consignment = Consignment::create([
        'consignment_number' => 'CSG-20260901-001',
        'store_id' => $store->id,
        'drop_date' => now()->subDays(3)->format('Y-m-d'),
        'status' => 'active',
    ]);

    ConsignmentItem::create([
        'consignment_id' => $consignment->id,
        'product_id' => $product->id,
        'quantity_dropped' => 10,
        'price_per_item' => 8000,
    ]);

    // Perform settlement via Livewire Form
    // Dropped 10. Remaining: 2. Returned/Damaged: 1. Sold: 7.
    // Sold Revenue: 7 * 8000 = 56,000.
    // Settled Cash received: 50,000 (with 6,000 as remaining receivable).
    // Sold HPP: 7 * 5000 = 35,000.
    // Returned/Damaged Loss: 1 * 5000 = 5,000.
    $cashAccount = Account::create([
        'name' => 'Kas Tunai Usaha',
        'type' => 'business',
        'balance' => 0,
    ]);

    Livewire::test(ConsignmentForm::class, ['consignment' => $consignment])
        ->set('settlement_date', now()->format('Y-m-d'))
        ->set('account_id', $cashAccount->id)
        ->set('items.0.quantity_remaining', '2')
        ->set('items.0.quantity_returned', '1')
        ->set('amount_paid', '50000')
        ->set('notes', 'Toko bayar 50rb, sisa 6rb minggu depan')
        ->call('saveSettlement')
        ->assertHasNoErrors()
        ->assertRedirect(route('consignments.index'));

    $journal = JournalEntry::where('reference_type', 'consignment')
        ->where('reference_id', $consignment->id)
        ->first();

    expect($journal)->not->toBeNull();
    // Debit side: Kas Tunai (50,000) + Piutang (6,000) + HPP (35,000) + Kerugian Retur (5,000) = 96,000
    // Credit side: Pendapatan Penjualan (56,000) + Persediaan Produk Jadi (40,000) = 96,000
    expect((float) $journal->total_debit)->toBe(96000.0);
    expect((float) $journal->total_credit)->toBe(96000.0);
});

test('accounting screens render successfully with clear data', function () {
    Livewire::test(Journals::class)
        ->assertStatus(200)
        ->assertSee('Jurnal Umum');

    Livewire::test(Ledger::class)
        ->assertStatus(200)
        ->assertSee('Buku Besar');

    Livewire::test(FinancialStatements::class)
        ->assertStatus(200)
        ->assertSee('Laporan Laba Rugi')
        ->set('activeTab', 'balance_sheet')
        ->assertSee('NERACA KEUANGAN (BALANCE SHEET)');
});
