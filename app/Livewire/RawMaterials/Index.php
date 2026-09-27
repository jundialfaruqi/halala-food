<?php

namespace App\Livewire\RawMaterials;

use App\Models\Product;
use App\Models\ProductRecipe;
use App\Models\RawMaterial;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Bahan Baku & Resep')]
class Index extends Component
{
    public bool $showMaterialModal = false;

    public bool $showRecipeModal = false;

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $deletingName = '';

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
        ], [
            'name.required' => 'Nama bahan baku wajib diisi.',
            'name.string' => 'Nama bahan baku harus berupa teks.',
            'name.max' => 'Nama bahan baku maksimal 255 karakter.',
            'unit.required' => 'Satuan bahan baku wajib diisi (contoh: kg/gram/pcs/liter).',
            'unit.string' => 'Satuan bahan baku harus berupa teks.',
            'unit.max' => 'Satuan bahan baku maksimal 50 karakter.',
            'stock.required' => 'Jumlah stok saat ini wajib diisi.',
            'stock.numeric' => 'Jumlah stok harus berupa angka.',
            'stock.min' => 'Jumlah stok tidak boleh kurang dari 0.',
            'min_stock.required' => 'Batas minimal stok wajib diisi.',
            'min_stock.numeric' => 'Batas minimal stok harus berupa angka.',
            'min_stock.min' => 'Batas minimal stok tidak boleh kurang dari 0.',
            'cost_per_unit.numeric' => 'Harga beli per satuan harus berupa angka.',
            'cost_per_unit.min' => 'Harga beli per satuan tidak boleh kurang dari 0.',
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
        $this->dispatch('toast', message: 'Data bahan baku berhasil disimpan.');
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->deletingId = $id;
        $this->deletingName = $name;
        $this->showDeleteModal = true;
    }

    public function deleteMaterial(): void
    {
        if ($this->deletingId) {
            $material = RawMaterial::find($this->deletingId);
            if ($material) {
                $name = $material->name;
                $material->delete();
                $this->dispatch('toast', message: "Bahan baku '{$name}' berhasil dihapus.");
            }
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->deletingName = '';
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
            'recipeRows' => 'required|array|min:1',
            'recipeRows.*.raw_material_id' => 'required|exists:raw_materials,id',
            'recipeRows.*.quantity_needed' => 'required|numeric|min:0.0001',
        ], [
            'recipeRows.required' => 'Resep harus memiliki minimal 1 bahan baku.',
            'recipeRows.min' => 'Resep harus memiliki minimal 1 bahan baku.',
            'recipeRows.*.raw_material_id.required' => 'Silakan pilih bahan baku pada daftar resep.',
            'recipeRows.*.raw_material_id.exists' => 'Bahan baku yang dipilih tidak valid atau sudah dihapus.',
            'recipeRows.*.quantity_needed.required' => 'Takaran bahan baku wajib diisi.',
            'recipeRows.*.quantity_needed.numeric' => 'Takaran bahan baku harus berupa angka.',
            'recipeRows.*.quantity_needed.min' => 'Takaran bahan baku minimal 0.0001.',
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
        $this->dispatch('toast', message: 'Resep produk berhasil diperbarui.');
    }

    #[Computed]
    public function modalMaterialCost(): float
    {
        $total = 0;
        $materialsById = RawMaterial::all()->keyBy('id');
        foreach ($this->recipeRows as $row) {
            $matId = $row['raw_material_id'] ?? null;
            $qty = (float) ($row['quantity_needed'] ?? 0);
            if ($matId && isset($materialsById[$matId])) {
                $total += $qty * (float) $materialsById[$matId]->cost_per_unit;
            }
        }

        return $total;
    }

    public function render(): View
    {
        $materials = RawMaterial::orderBy('name')->get();
        $products = Product::with('recipes.rawMaterial')->where('is_active', true)->get();
        $currentProduct = $this->selectedProductId ? Product::with('recipes.rawMaterial')->find($this->selectedProductId) : null;

        return view('livewire.raw-materials.index', [
            'materials' => $materials,
            'products' => $products,
            'currentProduct' => $currentProduct,
        ]);
    }
}
