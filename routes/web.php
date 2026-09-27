<?php

use App\Http\Controllers\BarcodePrintController;
use App\Http\Controllers\ConsignmentPrintController;
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
Route::get('/barcodes/export-html', [BarcodePrintController::class, 'downloadHtml'])->name('barcodes.export-html');
Route::get('/productions', ProductionIndex::class)->name('productions.index');
Route::get('/raw-materials', RawMaterialIndex::class)->name('raw-materials.index');
Route::get('/cash-book', CashBookIndex::class)->name('cash-book.index');
Route::get('/reports', ReportIndex::class)->name('reports.index');
