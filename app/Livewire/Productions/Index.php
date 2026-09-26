<?php

namespace App\Livewire\Productions;

use App\Models\Product;
use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Catat Produksi Makanan')]
class Index extends Component
{
    use WithPagination;

    public bool $showModal = false;

    public string $production_date = '';

    public ?int $product_id = null;

    public int $quantity_produced = 0;

    public string $notes = '';

    public function mount(): void
    {
        $this->production_date = Carbon::now()->format('Y-m-d');
        $firstProduct = Product::where('is_active', true)->first();
        $this->product_id = $firstProduct?->id;
    }

    public function openCreateModal(): void
    {
        $this->production_date = Carbon::now()->format('Y-m-d');
        $this->quantity_produced = 0;
        $this->notes = '';
        $this->showModal = true;
    }

    public function save(): void
    {
        $this->validate([
            'production_date' => 'required|date',
            'product_id' => 'required|exists:products,id',
            'quantity_produced' => 'required|integer|min:1',
        ], [
            'production_date.required' => 'Tanggal masak / produksi wajib diisi.',
            'production_date.date' => 'Format tanggal produksi tidak valid.',
            'product_id.required' => 'Silakan pilih produk makanan yang dibuat.',
            'product_id.exists' => 'Produk makanan yang dipilih tidak ditemukan.',
            'quantity_produced.required' => 'Jumlah produk yang dibuat wajib diisi.',
            'quantity_produced.integer' => 'Jumlah produk yang dibuat harus berupa bilangan bulat.',
            'quantity_produced.min' => 'Jumlah produk yang dibuat minimal 1 pcs.',
        ]);

        $product = Product::with('recipes.rawMaterial')->findOrFail($this->product_id);

        // Validasi ketersediaan stok bahan baku
        $insufficientMaterials = [];
        foreach ($product->recipes as $recipe) {
            $needed = $recipe->quantity_needed * $this->quantity_produced;
            if ($recipe->rawMaterial->stock < $needed) {
                $insufficientMaterials[] = "{$recipe->rawMaterial->name} (Butuh {$needed} {$recipe->rawMaterial->unit}, Tersedia {$recipe->rawMaterial->stock} {$recipe->rawMaterial->unit})";
            }
        }

        if (! empty($insufficientMaterials)) {
            $this->addError('quantity_produced', 'Stok bahan baku tidak mencukupi: '.implode(', ', $insufficientMaterials));

            return;
        }

        DB::transaction(function () use ($product) {
            // 1. Catat log produksi
            Production::create([
                'production_date' => $this->production_date,
                'product_id' => $this->product_id,
                'quantity_produced' => $this->quantity_produced,
                'notes' => $this->notes,
            ]);

            // 2. Tambah stok produk jadi
            $product->increment('stock_ready', $this->quantity_produced);

            // 3. Potong stok bahan baku otomatis sesuai resep
            foreach ($product->recipes as $recipe) {
                $needed = $recipe->quantity_needed * $this->quantity_produced;
                $recipe->rawMaterial->decrement('stock', $needed);
            }
        });

        $this->showModal = false;
        $this->dispatch('toast', message: "Berhasil mencatat produksi {$this->quantity_produced} {$product->unit} {$product->name}. Stok produk jadi bertambah & bahan baku terpotong.");
    }

    public function render(): View
    {
        $productions = Production::with('product')
            ->orderBy('production_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        $products = Product::with('recipes.rawMaterial')->where('is_active', true)->get();
        $selectedProduct = $products->firstWhere('id', $this->product_id);

        return view('livewire.productions.index', [
            'productions' => $productions,
            'products' => $products,
            'selectedProduct' => $selectedProduct,
        ]);
    }
}
