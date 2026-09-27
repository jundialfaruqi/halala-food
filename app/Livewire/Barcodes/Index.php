<?php

namespace App\Livewire\Barcodes;

use App\Models\Product;
use App\Models\Store;
use App\Models\StoreProductBarcode;
use App\Services\BarcodeService;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Cetak Barcode & Label Toko')]
class Index extends Component
{
    use WithPagination;

    #[Url]
    public string $activeTab = 'list'; // 'list' or 'print'

    #[Url]
    public string $search = '';

    #[Url]
    public string $storeFilter = '';

    #[Url]
    public string $productFilter = '';

    // Form Modal State
    public bool $showModal = false;

    public ?int $editingId = null;

    public ?int $store_id = null;

    public ?int $product_id = null;

    public string $barcode = '';

    public string $barcode_type = 'CODE128';

    public string $store_sku = '';

    public string $custom_product_name = '';

    public ?float $custom_price = null;

    public string $notes = '';

    // Delete Modal State (Apple UI)
    public bool $showDeleteModal = false;

    public ?int $deletingId = null;

    public string $deletingName = '';

    // Print Studio State
    #[Url]
    public string $printMode = 'single'; // 'single', 'store_all', 'batch'

    #[Url]
    public ?int $selectedBarcodeId = null;

    public ?int $selectedStoreId = null;

    public int $singleCopies = 24;

    public array $batchRows = [];

    // Paper & Label Template Settings
    public string $paperTemplate = 'a4_3x8'; // 'a4_3x8' (24 labels), 'a4_4x10' (40 labels), 'a4_2x6' (12 labels), 'tj_108', 'tj_107', 'custom'

    public int $columns = 3;

    public int $rows = 8;

    public float $labelWidthMm = 65.0;

    public float $labelHeightMm = 35.0;

    public float $marginTopMm = 8.0;

    public float $marginLeftMm = 8.0;

    public float $gapXMm = 3.0;

    public float $gapYMm = 2.0;

    // Display Toggles for Stickers
    public bool $showProductName = true;

    public bool $showStoreName = true;

    public bool $showPrice = true;

    public bool $showBarcodeText = true;

    public bool $showCutBorders = true;

    public int $barcodeHeight = 36;

    public function mount(): void
    {
        $firstBarcode = StoreProductBarcode::first();
        if ($firstBarcode) {
            $this->selectedBarcodeId = $firstBarcode->id;
            $this->selectedStoreId = $firstBarcode->store_id;
        }

        $this->applyPaperTemplate('a4_3x8');

        if (empty($this->batchRows)) {
            $this->batchRows[] = [
                'barcode_id' => $firstBarcode ? $firstBarcode->id : '',
                'qty' => 12,
            ];
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStoreFilter(): void
    {
        $this->resetPage();
    }

    public function updatingProductFilter(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(?int $storeId = null, ?int $productId = null): void
    {
        $this->reset(['editingId', 'store_id', 'product_id', 'barcode', 'barcode_type', 'store_sku', 'custom_product_name', 'custom_price', 'notes']);
        $this->barcode_type = 'CODE128';

        if ($storeId) {
            $this->store_id = $storeId;
        }
        if ($productId) {
            $this->product_id = $productId;
        }

        $this->showModal = true;
    }

    public function openEditModal(StoreProductBarcode $item): void
    {
        $this->editingId = $item->id;
        $this->store_id = $item->store_id;
        $this->product_id = $item->product_id;
        $this->barcode = $item->barcode;
        $this->barcode_type = $item->barcode_type ?: 'CODE128';
        $this->store_sku = $item->store_sku ?? '';
        $this->custom_product_name = $item->custom_product_name ?? '';
        $this->custom_price = $item->custom_price !== null ? (float) $item->custom_price : null;
        $this->notes = $item->notes ?? '';
        $this->showModal = true;
    }

    public function updatedProductId(?int $val): void
    {
        if ($val && empty($this->custom_price)) {
            $prod = Product::find($val);
            if ($prod && $prod->retail_price > 0) {
                $this->custom_price = (float) $prod->retail_price;
            }
        }
    }

    public function save(): void
    {
        $this->validate([
            'store_id' => 'required|exists:stores,id',
            'product_id' => 'required|exists:products,id',
            'barcode' => 'required|string|max:100',
            'barcode_type' => 'required|in:CODE128,EAN13,AUTO',
            'store_sku' => 'nullable|string|max:100',
            'custom_product_name' => 'nullable|string|max:255',
            'custom_price' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ], [
            'store_id.required' => 'Pilih toko mitra pemberi kode barcode.',
            'product_id.required' => 'Pilih produk jadi yang dipasangi barcode ini.',
            'barcode.required' => 'Kode barcode wajib diisi sesuai dari toko.',
        ]);

        StoreProductBarcode::updateOrCreate(
            ['id' => $this->editingId],
            [
                'store_id' => $this->store_id,
                'product_id' => $this->product_id,
                'barcode' => trim($this->barcode),
                'barcode_type' => $this->barcode_type,
                'store_sku' => $this->store_sku ?: null,
                'custom_product_name' => $this->custom_product_name ?: null,
                'custom_price' => $this->custom_price,
                'notes' => $this->notes ?: null,
            ]
        );

        $this->showModal = false;
        $this->dispatch('toast', message: 'Barcode toko berhasil disimpan.');
    }

    public function confirmDelete(int $id, string $name): void
    {
        $this->deletingId = $id;
        $this->deletingName = $name;
        $this->showDeleteModal = true;
    }

    public function deleteBarcode(): void
    {
        if ($this->deletingId) {
            $item = StoreProductBarcode::find($this->deletingId);
            if ($item) {
                $code = $item->barcode;
                $item->delete();
                $this->dispatch('toast', message: "Barcode '{$code}' berhasil dihapus.");
            }
        }

        $this->showDeleteModal = false;
        $this->deletingId = null;
        $this->deletingName = '';
    }

    public function quickPrintBarcode(int $barcodeId): void
    {
        $this->selectedBarcodeId = $barcodeId;
        $item = StoreProductBarcode::find($barcodeId);
        if ($item) {
            $this->selectedStoreId = $item->store_id;
        }
        $this->printMode = 'single';
        $this->activeTab = 'print';
        $this->singleCopies = ($this->columns * $this->rows);
    }

    public function setPaperTemplate(string $template): void
    {
        $this->paperTemplate = $template;
        $this->applyPaperTemplate($template);
    }

    public function applyPaperTemplate(string $template): void
    {
        switch ($template) {
            case 'a4_3x8': // 24 label A4
                $this->columns = 3;
                $this->rows = 8;
                $this->labelWidthMm = 65.0;
                $this->labelHeightMm = 35.0;
                $this->marginTopMm = 8.0;
                $this->marginLeftMm = 8.0;
                $this->gapXMm = 3.0;
                $this->gapYMm = 2.0;
                $this->barcodeHeight = 34;
                break;
            case 'a4_4x10': // 40 label A4 (kecil)
                $this->columns = 4;
                $this->rows = 10;
                $this->labelWidthMm = 48.0;
                $this->labelHeightMm = 28.0;
                $this->marginTopMm = 6.0;
                $this->marginLeftMm = 6.0;
                $this->gapXMm = 2.0;
                $this->gapYMm = 1.5;
                $this->barcodeHeight = 22;
                break;
            case 'a4_2x6': // 12 label A4 (besar)
                $this->columns = 2;
                $this->rows = 6;
                $this->labelWidthMm = 95.0;
                $this->labelHeightMm = 45.0;
                $this->marginTopMm = 12.0;
                $this->marginLeftMm = 10.0;
                $this->gapXMm = 4.0;
                $this->gapYMm = 3.0;
                $this->barcodeHeight = 44;
                break;
            case 'tj_108': // Tom & Jerry No. 108 (5x8 = 40)
                $this->columns = 5;
                $this->rows = 8;
                $this->labelWidthMm = 38.0;
                $this->labelHeightMm = 18.0;
                $this->marginTopMm = 5.0;
                $this->marginLeftMm = 5.0;
                $this->gapXMm = 2.0;
                $this->gapYMm = 1.5;
                $this->barcodeHeight = 15;
                break;
            case 'tj_107': // Tom & Jerry No. 107 (4x6 = 24)
                $this->columns = 4;
                $this->rows = 6;
                $this->labelWidthMm = 50.0;
                $this->labelHeightMm = 19.0;
                $this->marginTopMm = 6.0;
                $this->marginLeftMm = 5.0;
                $this->gapXMm = 2.0;
                $this->gapYMm = 2.0;
                $this->barcodeHeight = 16;
                break;
        }

        if ($this->printMode === 'single') {
            $this->singleCopies = $this->columns * $this->rows;
        }
    }

    public function addBatchRow(): void
    {
        $firstBarcode = StoreProductBarcode::first();
        $this->batchRows[] = [
            'barcode_id' => $firstBarcode ? $firstBarcode->id : '',
            'qty' => 12,
        ];
    }

    public function removeBatchRow(int $index): void
    {
        unset($this->batchRows[$index]);
        $this->batchRows = array_values($this->batchRows);
    }

    /**
     * Build the printable list of label items according to active print mode.
     *
     * @return array<int, array{item: StoreProductBarcode, svg: string}>
     */
    public function getPrintableLabelsProperty(): array
    {
        $labels = [];

        if ($this->printMode === 'single' && $this->selectedBarcodeId) {
            $item = StoreProductBarcode::with(['store', 'product'])->find($this->selectedBarcodeId);
            if ($item) {
                $svg = BarcodeService::getSvg(
                    $item->barcode,
                    $item->barcode_type,
                    $this->barcodeHeight,
                    1.4,
                    $this->showBarcodeText
                );

                $count = max(1, $this->singleCopies);
                for ($i = 0; $i < $count; $i++) {
                    $labels[] = [
                        'item' => $item,
                        'svg' => $svg,
                    ];
                }
            }
        } elseif ($this->printMode === 'store_all' && $this->selectedStoreId) {
            $items = StoreProductBarcode::with(['store', 'product'])
                ->where('store_id', $this->selectedStoreId)
                ->get();

            $perItemCount = max(1, (int) floor(($this->columns * $this->rows) / max(1, $items->count())));
            foreach ($items as $item) {
                $svg = BarcodeService::getSvg(
                    $item->barcode,
                    $item->barcode_type,
                    $this->barcodeHeight,
                    1.4,
                    $this->showBarcodeText
                );
                for ($i = 0; $i < $perItemCount; $i++) {
                    $labels[] = [
                        'item' => $item,
                        'svg' => $svg,
                    ];
                }
            }
        } elseif ($this->printMode === 'batch') {
            $allBarcodes = StoreProductBarcode::with(['store', 'product'])->get()->keyBy('id');
            foreach ($this->batchRows as $row) {
                $bId = $row['barcode_id'] ?? null;
                $qty = (int) ($row['qty'] ?? 0);
                if ($bId && isset($allBarcodes[$bId]) && $qty > 0) {
                    $item = $allBarcodes[$bId];
                    $svg = BarcodeService::getSvg(
                        $item->barcode,
                        $item->barcode_type,
                        $this->barcodeHeight,
                        1.4,
                        $this->showBarcodeText
                    );
                    for ($i = 0; $i < $qty; $i++) {
                        $labels[] = [
                            'item' => $item,
                            'svg' => $svg,
                        ];
                    }
                }
            }
        }

        return $labels;
    }

    public function render(): View
    {
        $stores = Store::orderBy('name')->get();
        $products = Product::where('is_active', true)->orderBy('name')->get();

        $barcodesQuery = StoreProductBarcode::with(['store', 'product'])
            ->when($this->search, function ($q) {
                $q->where('barcode', 'like', '%'.$this->search.'%')
                    ->orWhere('custom_product_name', 'like', '%'.$this->search.'%')
                    ->orWhere('store_sku', 'like', '%'.$this->search.'%')
                    ->orWhereHas('product', function ($sq) {
                        $sq->where('name', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('store', function ($sq) {
                        $sq->where('name', 'like', '%'.$this->search.'%');
                    });
            })
            ->when($this->storeFilter, function ($q) {
                $q->where('store_id', $this->storeFilter);
            })
            ->when($this->productFilter, function ($q) {
                $q->where('product_id', $this->productFilter);
            })
            ->orderBy('store_id')
            ->orderBy('product_id');

        $barcodes = $barcodesQuery->paginate(12);

        $allBarcodes = StoreProductBarcode::with(['store', 'product'])->get();

        $stats = [
            'total_barcodes' => StoreProductBarcode::count(),
            'total_stores' => StoreProductBarcode::distinct('store_id')->count('store_id'),
            'total_products' => StoreProductBarcode::distinct('product_id')->count('product_id'),
        ];

        return view('livewire.barcodes.index', [
            'barcodes' => $barcodes,
            'allBarcodes' => $allBarcodes,
            'stores' => $stores,
            'products' => $products,
            'stats' => $stats,
            'printableLabels' => $this->printableLabels,
        ]);
    }
}
