<?php

use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\Store;

use function Pest\Laravel\get;

test('can access printable invoice page for consignment', function () {
    $store = Store::create([
        'name' => 'Toko Mitra Berkah',
        'owner_name' => 'Bpk. Ahmad',
        'phone' => '08123456789',
        'address' => 'Jl. Raya No. 123',
        'route' => 'Rute Kota',
    ]);

    $product = Product::create([
        'name' => 'Merry Wijen Gurih',
        'unit' => 'pcs',
        'consignment_price' => 12000,
        'retail_price' => 15000,
        'stock_ready' => 50,
    ]);

    $consignment = Consignment::create([
        'consignment_number' => 'KNS-202609-001',
        'store_id' => $store->id,
        'drop_date' => '2026-09-27',
        'status' => 'active',
        'payment_status' => 'unpaid',
    ]);

    ConsignmentItem::create([
        'consignment_id' => $consignment->id,
        'product_id' => $product->id,
        'quantity_dropped' => 20,
        'price_per_item' => 12000,
    ]);

    $response = get(route('consignments.print', $consignment->id));

    $response->assertOk()
        ->assertSee('SURAT TITIP BARANG')
        ->assertSee('Toko Mitra Berkah')
        ->assertSee('KNS-202609-001')
        ->assertSee('Merry Wijen Gurih')
        ->assertSee('20')
        ->assertSee('Rp 240.000');
});
