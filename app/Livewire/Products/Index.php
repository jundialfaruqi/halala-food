<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Produk Jadi')]
class Index extends Component
{
    public bool $showModal = false;

    public ?int $productId = null;

    public string $name = '';

    public string $unit = 'bungkus';

    public float $consignment_price = 0;

    public float $retail_price = 0;

    public int $stock_ready = 0;

    public string $description = '';

    public function openCreateModal(): void
    {
        $this->reset(['productId', 'name', 'unit', 'consignment_price', 'retail_price', 'stock_ready', 'description']);
        $this->unit = 'bungkus';
        $this->showModal = true;
    }

    public function openEditModal(Product $product): void
    {
        $this->productId = $product->id;
        $this->name = $product->name;
        $this->unit = $product->unit;
        $this->consignment_price = (float) $product->consignment_price;
        $this->retail_price = (float) $product->retail_price;
        $this->stock_ready = (int) $product->stock_ready;
        $this->description = $product->description ?? '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'unit' => 'required|string|max:50',
            'consignment_price' => 'required|numeric|min:0',
            'retail_price' => 'required|numeric|min:0',
            'stock_ready' => 'required|integer|min:0',
        ]);

        Product::updateOrCreate(
            ['id' => $this->productId],
            [
                'name' => $this->name,
                'unit' => $this->unit,
                'consignment_price' => $this->consignment_price,
                'retail_price' => $this->retail_price,
                'stock_ready' => $this->stock_ready,
                'description' => $this->description,
                'is_active' => true,
            ]
        );

        $this->showModal = false;
        session()->flash('message', 'Data produk berhasil disimpan.');
    }

    public function deleteProduct(int $id): void
    {
        $product = Product::find($id);
        if ($product) {
            $name = $product->name;
            $product->delete();
            session()->flash('message', "Produk '{$name}' berhasil dihapus.");
        }
    }

    public function render(): View
    {
        $products = Product::withCount('recipes')->where('is_active', true)->get();

        return view('livewire.products.index', [
            'products' => $products,
        ]);
    }
}
