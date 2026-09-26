<?php

namespace App\Livewire\Reports;

use App\Models\CashTransaction;
use App\Models\Consignment;
use App\Models\ConsignmentItem;
use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Title('Laporan & Trend Penjualan')]
class Index extends Component
{
    public string $periodPreset = '30d'; // '7d', '30d', 'this_month', 'last_month', 'this_year', 'custom'

    public string $dateRange = '';

    public string $groupBy = 'daily'; // 'daily', 'weekly', 'monthly'

    public string $metric = 'revenue'; // 'revenue', 'quantity', 'profit'

    public string $selectedProductId = 'all';

    public string $selectedRoute = 'all';

    public string $selectedStoreId = 'all';

    public function mount(): void
    {
        Carbon::setLocale('id');
        $this->applyPreset('30d');
    }

    public function setPeriodPreset(string $preset): void
    {
        $this->applyPreset($preset);
    }

    public function setMetric(string $metric): void
    {
        $this->metric = in_array($metric, ['revenue', 'quantity', 'profit']) ? $metric : 'revenue';
    }

    public function setGroupBy(string $group): void
    {
        $this->groupBy = in_array($group, ['daily', 'weekly', 'monthly']) ? $group : 'daily';
    }

    public function updatedDateRange(): void
    {
        if ($this->dateRange) {
            $this->periodPreset = 'custom';
        }
    }

    public function updatedSelectedRoute(): void
    {
        // Reset specific store filter if route changes
        $this->selectedStoreId = 'all';
    }

    public function resetFilters(): void
    {
        $this->selectedProductId = 'all';
        $this->selectedRoute = 'all';
        $this->selectedStoreId = 'all';
        $this->metric = 'revenue';
        $this->applyPreset('30d');
    }

    protected function applyPreset(string $preset): void
    {
        $this->periodPreset = $preset;
        $now = Carbon::now();

        switch ($preset) {
            case '7d':
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                $this->groupBy = 'daily';
                break;
            case '30d':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $this->groupBy = 'daily';
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $this->groupBy = 'daily';
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth();
                $end = $now->copy()->subMonth()->endOfMonth();
                $this->groupBy = 'daily';
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $this->groupBy = 'monthly';
                break;
            default:
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $this->groupBy = 'daily';
                break;
        }

        $this->dateRange = $start->format('Y-m-d').' - '.$end->format('Y-m-d');
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    protected function resolveDateBoundaries(): array
    {
        $defaultStart = Carbon::now()->subDays(29)->startOfDay();
        $defaultEnd = Carbon::now()->endOfDay();

        if (empty($this->dateRange)) {
            return [$defaultStart, $defaultEnd];
        }

        $parts = explode(' - ', $this->dateRange);
        try {
            $startDate = isset($parts[0]) && trim($parts[0]) !== ''
                ? Carbon::parse(trim($parts[0]))->startOfDay()
                : $defaultStart;
            $endDate = isset($parts[1]) && trim($parts[1]) !== ''
                ? Carbon::parse(trim($parts[1]))->endOfDay()
                : (isset($parts[0]) ? Carbon::parse(trim($parts[0]))->endOfDay() : $defaultEnd);

            if ($startDate->gt($endDate)) {
                return [$endDate->copy()->startOfDay(), $startDate->copy()->endOfDay()];
            }

            return [$startDate, $endDate];
        } catch (\Exception) {
            return [$defaultStart, $defaultEnd];
        }
    }

    public function render(): View
    {
        Carbon::setLocale('id');
        [$startDate, $endDate] = $this->resolveDateBoundaries();

        $startStr = $startDate->format('Y-m-d');
        $endStr = $endDate->format('Y-m-d');

        // Master lists for filters
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $routes = Store::whereNotNull('route')->where('route', '!=', '')->distinct()->pluck('route')->sort()->values();
        $storesQuery = Store::where('is_active', true);
        if ($this->selectedRoute !== 'all') {
            $storesQuery->where('route', $this->selectedRoute);
        }
        $stores = $storesQuery->orderBy('name')->get();

        // Query Consignments for the chart & reports
        $consignmentsQuery = Consignment::with(['items.product.recipes.rawMaterial', 'store'])
            ->where('status', 'completed')
            ->whereBetween('settlement_date', [$startStr, $endStr]);

        if ($this->selectedStoreId !== 'all') {
            $consignmentsQuery->where('store_id', $this->selectedStoreId);
        } elseif ($this->selectedRoute !== 'all') {
            $consignmentsQuery->whereHas('store', fn ($q) => $q->where('route', $this->selectedRoute));
        }

        $consignments = $consignmentsQuery->get();

        // -------------------------------------------------------------
        // Compute Chart Buckets & Aggregate Metrics
        // -------------------------------------------------------------
        $chartLabels = [];
        $chartValues = [];
        $chartTooltips = [];
        $buckets = [];

        if ($this->groupBy === 'monthly') {
            // Group month-by-month
            $cur = $startDate->copy()->startOfMonth();
            $last = $endDate->copy()->endOfMonth();
            while ($cur->lte($last)) {
                $bKey = $cur->format('Y-m');
                $buckets[$bKey] = [
                    'label' => $cur->isoFormat('MMM Y'),
                    'tooltip' => $cur->isoFormat('MMMM Y'),
                    'value' => 0.0,
                    'count' => 0,
                ];
                $cur->addMonth();
            }

            foreach ($consignments as $c) {
                $cDate = Carbon::parse($c->settlement_date);
                $bKey = $cDate->format('Y-m');
                if (! isset($buckets[$bKey])) {
                    continue;
                }

                $val = $this->calculateConsignmentMetric($c);
                $buckets[$bKey]['value'] += $val;
                if ($val > 0) {
                    $buckets[$bKey]['count']++;
                }
            }
        } elseif ($this->groupBy === 'weekly') {
            // Group week-by-week
            $period = CarbonPeriod::create($startDate->copy()->startOfWeek(), '1 week', $endDate->copy()->endOfWeek());
            foreach ($period as $wDate) {
                $bKey = $wDate->format('o-W');
                $weekStart = $wDate->copy()->startOfWeek();
                $weekEnd = $wDate->copy()->endOfWeek();
                $buckets[$bKey] = [
                    'label' => $weekStart->isoFormat('D MMM').' - '.$weekEnd->isoFormat('D MMM'),
                    'tooltip' => 'Minggu ke-'.$wDate->format('W').' ('.$weekStart->isoFormat('D MMM').' - '.$weekEnd->isoFormat('D MMMM Y').')',
                    'value' => 0.0,
                    'count' => 0,
                ];
            }

            foreach ($consignments as $c) {
                $cDate = Carbon::parse($c->settlement_date);
                $bKey = $cDate->format('o-W');
                if (! isset($buckets[$bKey])) {
                    continue;
                }

                $val = $this->calculateConsignmentMetric($c);
                $buckets[$bKey]['value'] += $val;
                if ($val > 0) {
                    $buckets[$bKey]['count']++;
                }
            }
        } else {
            // Group daily
            $period = CarbonPeriod::create($startDate, '1 day', $endDate);
            foreach ($period as $dDate) {
                $bKey = $dDate->format('Y-m-d');
                $buckets[$bKey] = [
                    'label' => $dDate->isoFormat('D MMM'),
                    'tooltip' => $dDate->isoFormat('dddd, D MMMM Y'),
                    'value' => 0.0,
                    'count' => 0,
                ];
            }

            foreach ($consignments as $c) {
                if (! $c->settlement_date) {
                    continue;
                }
                $bKey = Carbon::parse($c->settlement_date)->format('Y-m-d');
                if (! isset($buckets[$bKey])) {
                    continue;
                }

                $val = $this->calculateConsignmentMetric($c);
                $buckets[$bKey]['value'] += $val;
                if ($val > 0) {
                    $buckets[$bKey]['count']++;
                }
            }
        }

        foreach ($buckets as $b) {
            $chartLabels[] = $b['label'];
            $chartValues[] = round($b['value'], 2);
            $chartTooltips[] = $b['tooltip'];
        }

        // Summary Statistics for Chart
        $totalMetricVal = array_sum($chartValues);
        $peakMetricVal = count($chartValues) > 0 ? max($chartValues) : 0;
        $peakIdx = count($chartValues) > 0 ? array_search($peakMetricVal, $chartValues) : false;
        $peakLabel = ($peakIdx !== false && isset($chartTooltips[$peakIdx])) ? $chartTooltips[$peakIdx] : '-';

        $activePointsCount = count(array_filter($chartValues, fn ($v) => $v > 0));
        $avgMetricVal = $activePointsCount > 0 ? $totalMetricVal / $activePointsCount : 0;
        $transactionsCount = $consignments->count();

        // -------------------------------------------------------------
        // Financial & Breakdown Reports for the Selected Period
        // -------------------------------------------------------------
        $totalSales = $consignments->sum('total_net_received');

        $otherIncome = CashTransaction::where('type', 'income')
            ->whereNull('consignment_id')
            ->whereBetween('transaction_date', [$startStr, $endStr])
            ->sum('amount');

        $totalIncome = $totalSales + $otherIncome;

        $businessExpenses = CashTransaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startStr, $endStr])
            ->get();

        $totalExpenses = $businessExpenses->sum('amount');
        $netProfit = $totalIncome - $totalExpenses;

        $totalPrive = CashTransaction::where('type', 'prive')
            ->whereBetween('transaction_date', [$startStr, $endStr])
            ->sum('amount');

        // Store Performance
        $storePerformances = Store::with(['consignments' => function ($q) use ($startStr, $endStr) {
            $q->where('status', 'completed')
                ->whereBetween('settlement_date', [$startStr, $endStr]);
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

        // Product Breakdown
        $productSalesQuery = ConsignmentItem::with('product')
            ->whereHas('consignment', function ($q) use ($startStr, $endStr) {
                $q->where('status', 'completed')
                    ->whereBetween('settlement_date', [$startStr, $endStr]);
            });

        if ($this->selectedProductId !== 'all') {
            $productSalesQuery->where('product_id', $this->selectedProductId);
        }

        $productSales = $productSalesQuery->get()
            ->groupBy('product_id')
            ->map(function ($items) {
                $product = $items->first()->product;
                $totalQtySold = $items->sum('quantity_sold');
                $totalRupiah = $items->sum('subtotal');
                $materialCost = $product ? $product->material_cost * $totalQtySold : 0;
                $grossMargin = $totalRupiah - $materialCost;

                return [
                    'name' => $product?->name ?? 'Produk Dihapus',
                    'unit' => $product?->unit ?? 'pcs',
                    'qty_sold' => $totalQtySold,
                    'total_rupiah' => $totalRupiah,
                    'material_cost' => $materialCost,
                    'gross_margin' => $grossMargin,
                ];
            })->sortByDesc('total_rupiah');

        $this->dispatch('update-sales-chart', [
            'labels' => $chartLabels,
            'values' => $chartValues,
            'tooltips' => $chartTooltips,
            'metric' => $this->metric,
        ]);

        $periodLabel = match ($this->periodPreset) {
            '7d' => '7 Hari Terakhir ('.$startDate->isoFormat('D MMM').' – '.$endDate->isoFormat('D MMM Y').')',
            '30d' => '30 Hari Terakhir ('.$startDate->isoFormat('D MMM').' – '.$endDate->isoFormat('D MMM Y').')',
            'this_month' => 'Bulan Ini ('.$startDate->isoFormat('MMMM Y').')',
            'last_month' => 'Bulan Lalu ('.$startDate->isoFormat('MMMM Y').')',
            'this_year' => 'Tahun Ini ('.$startDate->isoFormat('Y').')',
            default => 'Kustom ('.$startDate->isoFormat('D MMM Y').' – '.$endDate->isoFormat('D MMM Y').')',
        };

        return view('livewire.reports.index', [
            'periodLabel' => $periodLabel,
            'products' => $products,
            'routes' => $routes,
            'stores' => $stores,
            'chartLabels' => $chartLabels,
            'chartValues' => $chartValues,
            'chartTooltips' => $chartTooltips,
            'chartSummary' => [
                'total' => $totalMetricVal,
                'average' => $avgMetricVal,
                'peak' => $peakMetricVal,
                'peakLabel' => $peakLabel,
                'transactions' => $transactionsCount,
            ],
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

    /**
     * Calculate metric contribution for a consignment record.
     */
    protected function calculateConsignmentMetric(Consignment $consignment): float
    {
        $filteredItems = $consignment->items;
        if ($this->selectedProductId !== 'all') {
            $filteredItems = $filteredItems->where('product_id', (int) $this->selectedProductId);
        }

        if ($filteredItems->isEmpty()) {
            return 0.0;
        }

        return match ($this->metric) {
            'revenue' => (float) $filteredItems->sum('subtotal'),
            'quantity' => (float) $filteredItems->sum('quantity_sold'),
            'profit' => (float) $filteredItems->sum(function ($item) {
                $materialCost = $item->product?->material_cost ?? 0;
                $marginPerUnit = (float) $item->price_per_item - (float) $materialCost;

                return max(0, $marginPerUnit) * (int) $item->quantity_sold;
            }),
            default => (float) $filteredItems->sum('subtotal'),
        };
    }
}
