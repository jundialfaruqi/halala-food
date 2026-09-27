<?php

use App\Livewire\Barcodes\Index;
use App\Models\Product;
use App\Models\Store;
use App\Models\StoreProductBarcode;
use App\Models\User;
use App\Services\BarcodeService;
use Livewire\Livewire;
use Tests\TestCase;

/** @var TestCase $this */
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

    $pdfRes = $this->get(route('barcodes.export-pdf', [
        'mode' => 'single',
        'barcode_id' => $barcode->id,
        'template' => 'a4_3x8',
        'cols' => 3,
        'rows' => 8,
        'w' => 65.0,
        'h' => 34.0,
        'mt' => 5.0,
        'ml' => 6.0,
        'gx' => 2.5,
        'gy' => 1.5,
    ]));
    $pdfRes->assertSuccessful();
    $pdfRes->assertHeader('Content-Type', 'application/pdf');
    expect($pdfRes->getContent())->toStartWith('%PDF-1.4');
});

test('barcode pdf export works with various templates without clipping', function () {
    $store = Store::create([
        'name' => 'Toko Barokah Jaya Makmur Sentosa',
        'route' => 'Rute Timur',
    ]);
    $product = Product::create([
        'name' => 'Keripik Tempe Aneka Rasa Super Renyah 250gr',
        'unit' => 'bungkus',
        'retail_price' => 17500,
    ]);
    $barcode = StoreProductBarcode::create([
        'store_id' => $store->id,
        'product_id' => $product->id,
        'barcode' => '8997201948218',
        'barcode_type' => 'EAN13',
        'store_sku' => 'SKU-TB-001',
        'custom_price' => 18000,
    ]);

    // Test 4x10 template
    $res4x10 = $this->get(route('barcodes.export-pdf', [
        'mode' => 'single',
        'barcode_id' => $barcode->id,
        'cols' => 4,
        'rows' => 10,
        'w' => 48.0,
        'h' => 27.0,
        'mt' => 5.0,
        'ml' => 5.0,
        'gx' => 2.0,
        'gy' => 1.2,
        'copies' => 40,
    ]));
    $res4x10->assertSuccessful();
    expect($res4x10->getContent())->toStartWith('%PDF-1.4');

    // Test 5x8 Tom & Jerry 108 template
    $res5x8 = $this->get(route('barcodes.export-pdf', [
        'mode' => 'single',
        'barcode_id' => $barcode->id,
        'cols' => 5,
        'rows' => 8,
        'w' => 38.0,
        'h' => 18.0,
        'mt' => 5.0,
        'ml' => 5.0,
        'gx' => 2.0,
        'gy' => 1.5,
        'copies' => 40,
    ]));
    $res5x8->assertSuccessful();
    expect($res5x8->getContent())->toStartWith('%PDF-1.4');

    // Test oversized custom grid auto-scaling
    $resOversized = $this->get(route('barcodes.export-pdf', [
        'mode' => 'single',
        'barcode_id' => $barcode->id,
        'cols' => 4,
        'rows' => 10,
        'w' => 60.0,
        'h' => 35.0,
        'mt' => 15.0,
        'ml' => 15.0,
        'gx' => 5.0,
        'gy' => 5.0,
        'copies' => 40,
    ]));
    $resOversized->assertSuccessful();
    expect($resOversized->getContent())->toStartWith('%PDF-1.4');
});
