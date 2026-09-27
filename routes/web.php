<?php

use App\Http\Controllers\BarcodePrintController;
use App\Http\Controllers\ConsignmentPrintController;
use App\Http\Controllers\FinancialStatementPrintController;
use App\Livewire\Accounting\FinancialStatements as AccountingFinancialStatements;
use App\Livewire\Accounting\Journals as AccountingJournals;
use App\Livewire\Accounting\Ledger as AccountingLedger;
use App\Livewire\Barcodes\Index as BarcodeIndex;
use App\Livewire\CashBook\Index as CashBookIndex;
use App\Livewire\Consignments\Form as ConsignmentForm;
use App\Livewire\Consignments\Index as ConsignmentIndex;
use App\Livewire\Dashboard;
use App\Livewire\Productions\Index as ProductionIndex;
use App\Livewire\Products\Index as ProductIndex;
use App\Livewire\RawMaterials\Index as RawMaterialIndex;
use App\Livewire\Reports\Index as ReportIndex;
use App\Livewire\Stores\Index as StoreIndex;
use Illuminate\Support\Facades\Route;

Route::get('/', Dashboard::class)->name('dashboard');
Route::get('/consignments', ConsignmentIndex::class)->name('consignments.index');
Route::get('/consignments/create', ConsignmentForm::class)->name('consignments.create');
Route::get('/consignments/{consignment}', ConsignmentForm::class)->name('consignments.edit');
Route::get('/consignments/{consignment}/print', [ConsignmentPrintController::class, 'show'])->name('consignments.print');
Route::get('/stores', StoreIndex::class)->name('stores.index');
Route::get('/products', ProductIndex::class)->name('products.index');
Route::get('/barcodes', BarcodeIndex::class)->name('barcodes.index');
Route::get('/barcodes/print', [BarcodePrintController::class, 'show'])->name('barcodes.print');
Route::get('/barcodes/export-pdf', [BarcodePrintController::class, 'downloadPdf'])->name('barcodes.export-pdf');
Route::get('/barcodes/export-html', [BarcodePrintController::class, 'downloadHtml'])->name('barcodes.export-html');
Route::get('/productions', ProductionIndex::class)->name('productions.index');
Route::get('/raw-materials', RawMaterialIndex::class)->name('raw-materials.index');
Route::get('/cash-book', CashBookIndex::class)->name('cash-book.index');
Route::get('/reports', ReportIndex::class)->name('reports.index');

// Akuntansi Formal
Route::get('/accounting/journals', AccountingJournals::class)->name('accounting.journals');
Route::get('/accounting/ledger', AccountingLedger::class)->name('accounting.ledger');
Route::get('/accounting/financial-statements', AccountingFinancialStatements::class)->name('accounting.financial-statements');
Route::get('/accounting/financial-statements/print', [FinancialStatementPrintController::class, 'show'])->name('accounting.financial-statements.print');
Route::get('/accounting/financial-statements/export-pdf', [FinancialStatementPrintController::class, 'downloadPdf'])->name('accounting.financial-statements.export-pdf');
