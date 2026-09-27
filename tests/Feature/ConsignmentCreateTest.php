<?php

use App\Livewire\Consignments\Form;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    actingAs(User::factory()->create());
});

test('consignment create form can save drop when quantity is filled', function () {
    $store = Store::create([
        'name' => 'Toko Mitra Berkah',
        'is_active' => true,
    ]);

    $productA = Product::create([
        'name' => 'Makaroni Pedas',
        'unit' => 'toples',
        'stock_ready' => 50,
        'cost_price' => 10000,
        'consignment_price' => 15000,
        'store_selling_price' => 18000,
        'is_active' => true,
    ]);

    $productB = Product::create([
        'name' => 'Keripik Singkong',
        'unit' => 'pcs',
        'stock_ready' => 30,
        'cost_price' => 5000,
        'consignment_price' => 8000,
        'store_selling_price' => 10000,
        'is_active' => true,
    ]);

    Livewire::test(Form::class)
        ->set('store_id', $store->id)
        ->set('drop_date', now()->format('Y-m-d'))
        ->set('items.0.quantity_dropped', '5')
        ->set('items.1.quantity_dropped', '')
        ->call('saveDrop')
        ->assertHasNoErrors()
        ->assertRedirect(route('consignments.index'));

    assertDatabaseHas('consignments', [
        'store_id' => $store->id,
        'status' => 'active',
    ]);

    assertDatabaseHas('consignment_items', [
        'product_id' => $productA->id,
        'quantity_dropped' => 5,
    ]);

    expect($productA->fresh()->stock_ready)->toBe(45);
});
