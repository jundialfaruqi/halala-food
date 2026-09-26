<?php

namespace App\Livewire\RawMaterials;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Bahan Baku & Resep')]
class Index extends Component
{
    public bool $showMaterialModal = false;

    public bool $showRecipeModal = false;

    public ?int $materialId = null;

    // Material Form
    public string $name = '';

    public string $unit = 'kg';

    public float $stock = 0;

    public float $min_stock = 0;

    public float $cost_per_unit = 0;

    // Recipe Form
    public ?int $selectedProductId = null;

    public array $recipeRows = [];

    public function openMaterialModal(?RawMaterial $material = null): void
    {
        if ($material && $material->exists) {
            $this->materialId = $material->id;
            $this->name = $material->name;
            $this->unit = $material->unit;
            $this->stock = (float) $material->stock;
            $this->min_stock = (float) $material->min_stock;
            $this->cost_per_unit = (float) $material->cost_per_unit;
        } else {
            $this->reset(['materialId', 'name', 'unit', 'stock', 'min_stock', 'cost_per_unit']);
            $this->unit = 'kg';
        }
        $this->showMaterialModal = true;
    }

    public function saveMaterial(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'cost_per_unit' => 'nullable|numeric|min:0',
        ]);

        RawMaterial::updateOrCreate(
            ['id' => $this->materialId],
            [
                'name' => $this->name,
                'unit' => $this->unit,
                'stock' => $this->stock,
                'min_stock' => $this->min_stock,
                'cost_per_unit' => $this->cost_per_unit,
            ]
        );

        $this->showMaterialModal = false;
        session()->flash('message', 'Data bahan baku berhasil disimpan.');
    }

    public function openRecipeModal(int $productId): void
    {
        $this->selectedProductId = $productId;
        $product = Product::with('recipes.rawMaterial')->findOrFail($productId);

        $this->recipeRows = [];
        foreach ($product->recipes as $r) {
            $this->recipeRows[] = [
                'raw_material_id' => $r->raw_material_id,
                'quantity_needed' => (float) $r->quantity_needed,
            ];
        }

        if (empty($this->recipeRows)) {
            $this->recipeRows[] = ['raw_material_id' => '', 'quantity_needed' => 0];
        }

        $this->showRecipeModal = true;
    }

    public function addRecipeRow(): void
    {
        $this->recipeRows[] = ['raw_material_id' => '', 'quantity_needed' => 0];
    }

    public function removeRecipeRow(int $index): void
    {
        unset($this->recipeRows[$index]);
        $this->recipeRows = array_values($this->recipeRows);
    }

    public function saveRecipe(): void
    {
        $this->validate([
            'recipeRows.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipeRows.*.quantity_needed' => 'required|numeric|min:0.0001',
        ]);

        ProductRecipe::where('product_id', $this->selectedProductId)->delete();

        foreach ($this->recipeRows as $row) {
            ProductRecipe::create([
                'product_id' => $this->selectedProductId,
                'raw_material_id' => $row['raw_material_id'],
                'quantity_needed' => $row['quantity_needed'],
            ]);
        }

        $this->showRecipeModal = false;
        session()->flash('message', 'Resep produk berhasil diperbarui.');
    }

    public function render(): View
    {
        $materials = RawMaterial::orderBy('name')->get();
        $products = Product::with('recipes.rawMaterial')->where('is_active', true)->get();
        $currentProduct = $this->selectedProductId ? Product::find($this->selectedProductId) : null;

        return view('livewire.raw-materials.index', [
            'materials' => $materials,
            'products' => $products,
            'currentProduct' => $currentProduct,
        ]);
    }
}
