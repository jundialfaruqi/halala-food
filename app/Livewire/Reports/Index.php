<?php

namespace App\Livewire\Reports;

use App\Models\CashTransaction;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan Laba Rugi & Performa Toko')]
class Index extends Component
{
    public string $dateRange = '';

    public function mount(): void
    {
        $start = Carbon::now()->startOfMonth()->format('Y-m-d');
        $end = Carbon::now()->endOfMonth()->format('Y-m-d');
        $this->dateRange = $start.' - '.$end;
    }

    public function render(): View
    {
        $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        if ($this->dateRange) {
            $dates = explode(' - ', $this->dateRange);
            $startDate = trim($dates[0] ?? $startDate);
            $endDate = trim($dates[1] ?? $startDate);
        }

        // 1. Total Penjualan Toko
        $totalSales = Consignment::where('status', 'completed')
            ->whereBetween('settlement_date', [$startDate, $endDate])
            ->sum('total_net_received');

        // Pemasukan kas usaha lain
        $otherIncome = CashTransaction::where('type', 'income')
            ->whereNull('consignment_id')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalIncome = $totalSales + $otherIncome;

        // 2. Biaya Operasional & Bahan Usaha
        $businessExpenses = CashTransaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->get();

        $totalExpenses = $businessExpenses->sum('amount');

        // 3. Laba Bersih Usaha
        $netProfit = $totalIncome - $totalExpenses;

        // 4. Pengambilan Pribadi / Keluarga (Prive)
        $totalPrive = CashTransaction::where('type', 'prive')
            ->whereBetween('transaction_date', [$this->startDate, $this->endDate])
            ->sum('amount');

        // 5. Performa Penjualan per Toko
        $storePerformances = Store::with(['consignments' => function ($q) {
            $q->where('status', 'completed')
                ->whereBetween('settlement_date', [$this->startDate, $this->endDate]);
        }])
            ->get()
            ->map(function ($store) {
                $totalSold = $store->consignments->sum('total_net_received');
                $consignmentsCount = $store->consignments->count();

                return [
                    'name' => $store->name,
                    'route' => $store->route,
                    'consignments_count' => $consignmentsCount,
                    'total_sold' => $totalSold,
                ];
            })
            ->filter(fn ($s) => $s['total_sold'] > 0 || $s['consignments_count'] > 0)
            ->sortByDesc('total_sold');

        // 6. Penjualan per Produk
        $productSales = ConsignmentItem::with('product')
            ->whereHas('consignment', function ($q) {
                $q->where('status', 'completed')
                    ->whereBetween('settlement_date', [$this->startDate, $this->endDate]);
            })
            ->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                $product = $items->first()->product;
                $totalQtySold = $items->sum('quantity_sold');
                $totalRupiah = $items->sum('subtotal');

                return [
                    'name' => $product->name,
                    'unit' => $product->unit,
                    'qty_sold' => $totalQtySold,
                    'total_rupiah' => $totalRupiah,
                ];
            });

        return view('livewire.reports.index', [
            'totalSales' => $totalSales,
            'otherIncome' => $otherIncome,
            'totalIncome' => $totalIncome,
            'businessExpenses' => $businessExpenses,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'totalPrive' => $totalPrive,
            'storePerformances' => $storePerformances,
            'productSales' => $productSales,
        ]);
    }
}
