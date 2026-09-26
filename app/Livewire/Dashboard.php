<?php

namespace App\Livewire;

use App\Models\Account;
use App\Models\Consignment;
use App\Models\Product;
use App\Models\RawMaterial;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Dashboard Ringkasan Usaha')]
class Dashboard extends Component
{
    public function render(): View
    {
        // 1. Saldo Kas
        $totalBusinessCash = Account::where('type', 'business')->sum('balance');
        $totalPersonalCash = Account::where('type', 'personal')->sum('balance');

        // 2. Konsinyasi Aktif (Sedang dititip di toko)
        $activeConsignmentsCount = Consignment::where('status', 'active')->count();

        // 3. Estimasi Nilai Titipan yang beredar di toko
        $activeConsignments = Consignment::with('items')->where('status', 'active')->get();
        $totalGoodsInStoresValue = $activeConsignments->sum(function ($consignment) {
            return $consignment->items->sum(function ($item) {
                return $item->quantity_dropped * $item->price_per_item;
            });
        });

        // 4. Toko yang sudah lebih dari 7 hari belum dikunjungi
        $dueConsignments = Consignment::with('store')
            ->where('status', 'active')
            ->where('drop_date', '<=', Carbon::now()->subDays(7)->toDateString())
            ->orderBy('drop_date', 'asc')
            ->take(5)
            ->get();

        // 5. Stok Produk Jadi Siap Antar
        $products = Product::where('is_active', true)->get();

        // 6. Bahan Baku yang stoknya di bawah batas minimal
        $lowStockMaterials = RawMaterial::whereColumn('stock', '<=', 'min_stock')->get();

        // 7. Total Toko Mitra
        $totalStoresCount = Store::where('is_active', true)->count();

        return view('livewire.dashboard', [
            'totalBusinessCash' => $totalBusinessCash,
            'totalPersonalCash' => $totalPersonalCash,
            'activeConsignmentsCount' => $activeConsignmentsCount,
            'totalGoodsInStoresValue' => $totalGoodsInStoresValue,
            'dueConsignments' => $dueConsignments,
            'products' => $products,
            'lowStockMaterials' => $lowStockMaterials,
            'totalStoresCount' => $totalStoresCount,
        ]);
    }
}
