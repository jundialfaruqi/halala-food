<?php

use App\Livewire\RawMaterials\Index as RawMaterialsIndex;
use App\Models\RawMaterial;
use App\Models\User;
use Database\Seeders\AccountingSeeder;
use Livewire\Livewire;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\seed;

beforeEach(function () {
    seed(AccountingSeeder::class);
    actingAs(User::factory()->create());
});

test('packaging converter calculator automatically converts bulk packages to recipe base units and unit cost', function () {
    // Skenario: Beli 14 bungkus kacang @ 1.000 gram seharga Rp 45.000 / bungkus
    Livewire::test(RawMaterialsIndex::class)
        ->call('openMaterialModal')
        ->set('name', 'Kacang Tanah Sangrai')
        ->call('toggleCalculator')
        ->set('calc_package_count', 14)
        ->set('calc_content_per_package', 1000)
        ->set('calc_base_unit', 'gram')
        ->set('calc_price_per_package', 45000)
        ->call('applyCalculator')
        ->assertSet('unit', 'gram')
        ->assertSet('stock', 14000.0)
        ->assertSet('cost_per_unit', 45.0)
        ->set('min_stock', 1000)
        ->call('saveMaterial')
        ->assertHasNoErrors();

    $material = RawMaterial::where('name', 'Kacang Tanah Sangrai')->first();
    expect($material)->not->toBeNull();
    expect($material->unit)->toBe('gram');
    expect((float) $material->stock)->toBe(14000.0);
    expect((float) $material->cost_per_unit)->toBe(45.0);
});

test('packaging converter calculator works for packaging materials in pcs', function () {
    // Skenario: Beli 4 pak standing pouch @ 50 pcs seharga Rp 35.000 / pak
    Livewire::test(RawMaterialsIndex::class)
        ->call('openMaterialModal')
        ->set('name', 'Standing Pouch Klip (150g)')
        ->call('toggleCalculator')
        ->set('calc_package_count', 4)
        ->set('calc_content_per_package', 50)
        ->set('calc_base_unit', 'pcs')
        ->set('calc_price_per_package', 35000)
        ->call('applyCalculator')
        ->assertSet('unit', 'pcs')
        ->assertSet('stock', 200.0)
        ->assertSet('cost_per_unit', 700.0)
        ->set('min_stock', 50)
        ->call('saveMaterial')
        ->assertHasNoErrors();

    $material = RawMaterial::where('name', 'Standing Pouch Klip (150g)')->first();
    expect($material)->not->toBeNull();
    expect($material->unit)->toBe('pcs');
    expect((float) $material->stock)->toBe(200.0);
    expect((float) $material->cost_per_unit)->toBe(700.0);
});
