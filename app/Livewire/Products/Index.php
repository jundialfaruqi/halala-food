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

    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $deletingName = '';

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
        ], [
            'name.required' => 'Nama produk makanan wajib diisi.',
            'name.string' => 'Nama produk harus berupa teks.',
            'name.max' => 'Nama produk maksimal 255 karakter.',
            'unit.required' => 'Satuan produk wajib diisi (contoh: bungkus/toples/pcs).',
            'unit.string' => 'Satuan produk harus berupa teks.',
            'unit.max' => 'Satuan produk maksimal 50 karakter.',
            'consignment_price.required' => 'Harga titip toko wajib diisi.',
            'consignment_price.numeric' => 'Harga titip toko harus berupa angka.',
            'consignment_price.min' => 'Harga titip toko tidak boleh kurang dari 0.',
            'retail_price.required' => 'Harga jual rekomendasi wajib diisi.',
            'retail_price.numeric' => 'Harga jual rekomendasi harus berupa angka.',
            'retail_price.min' => 'Harga jual rekomendasi tidak boleh kurang dari 0.',
            'stock_ready.required' => 'Jumlah stok siap antar wajib diisi.',
            'stock_ready.integer' => 'Jumlah stok siap antar harus berupa bilangan bulat.',
            'stock_ready.min' => 'Jumlah stok siap antar tidak boleh kurang dari 0.',
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
        $this->dispatch('toast', message: 'Data produk berhasil disimpan.');
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->deletingId = $id;
        $this->deletingName = $name;
        $this->showDeleteModal = true;
    }

    public function deleteProduct(): void
    {
        if ($this->deletingId) {
            $product = Product::find($this->deletingId);
            if ($product) {
                $name = $product->name;
                $product->delete();
                $this->dispatch('toast', message: "Produk '{$name}' berhasil dihapus.");
            }
        }
        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->deletingName = '';
    }

    public function render(): View
    {
        $products = Product::withCount('recipes')->where('is_active', true)->get();

        return view('livewire.products.index', [
            'products' => $products,
        ]);
    }
}
