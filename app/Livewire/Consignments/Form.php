<?php

namespace App\Livewire\Consignments;

use App\Models\Account;
use App\Models\CashTransaction;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Pencatatan Titip Jual')]
class Form extends Component
{
    public ?Consignment $consignment = null;

    public bool $isEdit = false;

    // Fields untuk Drop Baru
    public ?int $store_id = null;

    public string $drop_date = '';

    public string $notes = '';

    public array $items = [];

    // Fields untuk Audit / Cek Sisa & Tagihan
    public string $settlement_date = '';

    public ?int $account_id = null;

    public float $amount_paid = 0;

    public string $payment_status = 'paid';

    public function mount(?Consignment $consignment = null): void
    {
        if ($consignment && $consignment->exists) {
            $this->consignment = $consignment->load(['store', 'items.product']);
            $this->isEdit = true;
            $this->store_id = $consignment->store_id;
            $this->drop_date = $consignment->drop_date->format('Y-m-d');
            $this->settlement_date = $consignment->settlement_date ? $consignment->settlement_date->format('Y-m-d') : Carbon::now()->format('Y-m-d');
            $this->notes = $consignment->notes ?? '';
            $this->amount_paid = (float) $consignment->amount_paid;
            $this->payment_status = $consignment->payment_status;

            // Load items
            $this->items = [];
            foreach ($consignment->items as $item) {
                $this->items[] = [
                    'id' => $item->id,
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'quantity_dropped' => $item->quantity_dropped,
                    'price_per_item' => (float) $item->price_per_item,
                    'quantity_remaining' => $item->quantity_remaining ?? 0,
                    'quantity_returned' => $item->quantity_returned ?? 0,
                    'quantity_sold' => $item->quantity_sold ?? 0,
                    'subtotal' => (float) $item->subtotal,
                ];
            }

            // Default account
            $defaultAccount = Account::where('type', 'business')->first();
            $this->account_id = $defaultAccount?->id;

            $this->recalculateAudit();
        } else {
            $this->isEdit = false;
            $this->drop_date = Carbon::now()->format('Y-m-d');
            $this->settlement_date = Carbon::now()->format('Y-m-d');

            // Inisialisasi 3 produk utama langsung
            $products = Product::where('is_active', true)->get();
            $this->items = [];
            foreach ($products as $p) {
                $this->items[] = [
                    'product_id' => $p->id,
                    'product_name' => $p->name,
                    'quantity_dropped' => 0,
                    'price_per_item' => (float) $p->consignment_price,
                    'quantity_remaining' => 0,
                    'quantity_returned' => 0,
                    'quantity_sold' => 0,
                    'subtotal' => 0,
                ];
            }
        }
    }

    public function recalculateAudit(): void
    {
        $totalSold = 0;
        foreach ($this->items as $idx => $item) {
            $dropped = (int) ($item['quantity_dropped'] ?? 0);
            $remaining = (int) ($item['quantity_remaining'] ?? 0);
            $returned = (int) ($item['quantity_returned'] ?? 0);
            $price = (float) ($item['price_per_item'] ?? 0);

            // Sold cannot be negative
            $sold = max(0, $dropped - $remaining - $returned);
            $subtotal = $sold * $price;

            $this->items[$idx]['quantity_sold'] = $sold;
            $this->items[$idx]['subtotal'] = $subtotal;
            $totalSold += $subtotal;
        }

        if ($this->amount_paid == 0 || $this->amount_paid < $totalSold) {
            $this->amount_paid = $totalSold;
        }
    }

    public function updatedItems(): void
    {
        $this->recalculateAudit();
    }

    public function saveDrop(): void
    {
        $this->validate([
            'store_id' => 'required|exists:stores,id',
            'drop_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity_dropped' => 'required|integer|min:0',
        ], [
            'store_id.required' => 'Silakan pilih toko mitra tujuan titip barang.',
            'store_id.exists' => 'Toko mitra yang dipilih tidak ditemukan.',
            'drop_date.required' => 'Tanggal titip barang wajib diisi.',
            'drop_date.date' => 'Format tanggal titip tidak valid.',
            'items.required' => 'Daftar produk titipan tidak boleh kosong.',
            'items.min' => 'Daftar produk titipan minimal harus berisi 1 produk.',
            'items.*.product_id.required' => 'Produk makanan wajib dipilih.',
            'items.*.product_id.exists' => 'Produk makanan tidak valid.',
            'items.*.quantity_dropped.required' => 'Jumlah barang titip wajib diisi.',
            'items.*.quantity_dropped.integer' => 'Jumlah barang titip harus berupa bilangan bulat.',
            'items.*.quantity_dropped.min' => 'Jumlah barang titip tidak boleh negatif.',
        ]);

        // Pastikan minimal ada 1 produk dengan quantity > 0
        $totalItems = collect($this->items)->sum('quantity_dropped');
        if ($totalItems <= 0) {
            $this->addError('items', 'Harap isi jumlah barang yang dititipkan minimal 1 pcs pada salah satu produk.');

            return;
        }

        $newConsignmentId = null;
        DB::transaction(function () use (&$newConsignmentId) {
            $consignmentNumber = 'KNS-'.Carbon::parse($this->drop_date)->format('Ym').'-'.str_pad(Consignment::count() + 1, 3, '0', STR_PAD_LEFT);

            $consignment = Consignment::create([
                'consignment_number' => $consignmentNumber,
                'store_id' => $this->store_id,
                'drop_date' => $this->drop_date,
                'status' => 'active',
                'payment_status' => 'unpaid',
                'notes' => $this->notes,
            ]);

            $newConsignmentId = $consignment->id;

            foreach ($this->items as $itemData) {
                if ((int) $itemData['quantity_dropped'] > 0) {
                    $product = Product::find($itemData['product_id']);

                    ConsignmentItem::create([
                        'consignment_id' => $consignment->id,
                        'product_id' => $itemData['product_id'],
                        'quantity_dropped' => $itemData['quantity_dropped'],
                        'price_per_item' => $itemData['price_per_item'] ?? $product->consignment_price,
                        'quantity_remaining' => null,
                        'quantity_returned' => 0,
                        'quantity_sold' => 0,
                        'subtotal' => 0,
                    ]);

                    // Kurangi stok ready di gudang
                    $product->decrement('stock_ready', (int) $itemData['quantity_dropped']);
                }
            }
        });

        session()->flash('message', 'Catatan titip barang berhasil disimpan.');
        if ($newConsignmentId) {
            session()->flash('new_consignment_id', $newConsignmentId);
        }
        $this->redirect(route('consignments.index'), navigate: true);
    }

    public function saveSettlement(): void
    {
        $this->validate([
            'settlement_date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'amount_paid' => 'required|numeric|min:0',
        ], [
            'settlement_date.required' => 'Tanggal jemput / penagihan toko wajib diisi.',
            'settlement_date.date' => 'Format tanggal jemput tidak valid.',
            'account_id.required' => 'Silakan pilih kas penerima uang tagihan toko.',
            'account_id.exists' => 'Akun kas yang dipilih tidak ditemukan.',
            'amount_paid.required' => 'Jumlah uang yang disetor toko wajib diisi.',
            'amount_paid.numeric' => 'Jumlah uang yang disetor harus berupa angka.',
            'amount_paid.min' => 'Jumlah uang yang disetor tidak boleh kurang dari 0.',
        ]);

        $this->recalculateAudit();

        DB::transaction(function () {
            $totalSold = collect($this->items)->sum('subtotal');

            // Update consignment items
            foreach ($this->items as $itemData) {
                $item = ConsignmentItem::find($itemData['id']);
                if ($item) {
                    $item->update([
                        'quantity_remaining' => (int) $itemData['quantity_remaining'],
                        'quantity_returned' => (int) $itemData['quantity_returned'],
                        'quantity_sold' => (int) $itemData['quantity_sold'],
                        'subtotal' => (float) $itemData['subtotal'],
                    ]);
                }
            }

            // Update Consignment status
            $this->consignment->update([
                'settlement_date' => $this->settlement_date,
                'status' => 'completed',
                'total_sold_amount' => $totalSold,
                'total_net_received' => $totalSold,
                'amount_paid' => $this->amount_paid,
                'payment_status' => $this->amount_paid >= $totalSold ? 'paid' : ($this->amount_paid > 0 ? 'partial' : 'unpaid'),
                'notes' => $this->notes,
            ]);

            // Catat uang masuk ke Buku Kas jika ada pembayaran
            if ($this->amount_paid > 0) {
                $account = Account::find($this->account_id);
                $account->increment('balance', $this->amount_paid);

                CashTransaction::create([
                    'transaction_date' => $this->settlement_date,
                    'account_id' => $this->account_id,
                    'type' => 'income',
                    'category' => 'Penjualan Toko Mitra',
                    'amount' => $this->amount_paid,
                    'consignment_id' => $this->consignment->id,
                    'description' => 'Hasil tagihan toko '.$this->consignment->store->name.' ('.$this->consignment->consignment_number.')',
                ]);
            }
        });

        session()->flash('message', 'Penagihan toko berhasil diselesaikan dan uang telah masuk ke Buku Kas.');
        $this->redirect(route('consignments.index'), navigate: true);
    }

    public function render(): View
    {
        $stores = Store::where('is_active', true)->orderBy('name')->get();
        $accounts = Account::where('type', 'business')->get();

        $totalSoldAmount = collect($this->items)->sum('subtotal');

        return view('livewire.consignments.form', [
            'stores' => $stores,
            'accounts' => $accounts,
            'totalSoldAmount' => $totalSoldAmount,
        ]);
    }
}
