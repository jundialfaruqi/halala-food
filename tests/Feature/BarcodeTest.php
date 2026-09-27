<?php

use App\Livewire\Barcodes\Index;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreProductBarcode;
use App\Models\User;
use App\Services\BarcodeService;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('barcodes index page can be rendered', function () {
    $response = $this->get(route('barcodes.index'));

    $response->assertSuccessful();
});

test('barcode service generates valid svg for code128 and ean13', function () {
    $svg128 = BarcodeService::getSvg('MKR-001', 'CODE128');
    expect($svg128)->toContain('<svg')->toContain('</svg>');

    $svgEan = BarcodeService::getSvg('899720194821', 'EAN13');
    expect($svgEan)->toContain('<svg')->toContain('</svg>');
});

test('store barcode can be created via livewire component', function () {
    $store = Store::create([
        'name' => 'Toko Barokah Test',
        'route' => 'Rute Timur',
    ]);
    $product = Product::create([
        'name' => 'Merry Wijen Test',
        'unit' => 'bungkus',
        'retail_price' => 15000,
    ]);

    Livewire::test(Index::class)
        ->call('openCreateModal', $store->id, $product->id)
        ->set('barcode', '201948281023')
        ->set('barcode_type', 'CODE128')
        ->set('custom_product_name', 'MERRY WIJEN 200GR')
        ->set('custom_price', 16000)
        ->call('save')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('store_product_barcodes', [
        'store_id' => $store->id,
        'product_id' => $product->id,
        'barcode' => '201948281023',
        'custom_product_name' => 'MERRY WIJEN 200GR',
    ]);
});

test('barcode print page and standalone export can be accessed', function () {
    $store = Store::create([
        'name' => 'Toko Barokah Test',
        'route' => 'Rute Timur',
    ]);
    $product = Product::create([
        'name' => 'Merry Wijen Test',
        'unit' => 'bungkus',
        'retail_price' => 15000,
    ]);
    $barcode = StoreProductBarcode::create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'barcode' => '201948281023',
    ]);

    $printRes = $this->get(route('barcodes.print', ['mode' => 'single', 'barcode_id' => $barcode->id]));
    $printRes->assertSuccessful();

    $exportRes = $this->get(route('barcodes.export-html', ['mode' => 'single', 'barcode_id' => $barcode->id]));
    $exportRes->assertSuccessful();
    $exportRes->assertHeader('Content-Type', 'text/html; charset=utf-8');
});
