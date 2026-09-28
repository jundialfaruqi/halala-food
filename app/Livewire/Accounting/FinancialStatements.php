<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Laporan Keuangan Akuntansi')]
class FinancialStatements extends Component
{
    #[Url]
    public string $activeTab = 'income_statement'; // 'income_statement' or 'balance_sheet'

    #[Url]
    public string $periodPreset = 'this_month'; // 'this_month', 'this_year', 'all'

    public function setTab(string $tab): void
    {
        $this->activeTab = in_array($tab, ['income_statement', 'balance_sheet']) ? $tab : 'income_statement';
    }

    public function setPeriod(string $preset): void
    {
        $this->periodPreset = in_array($preset, ['this_month', 'this_year', 'all']) ? $preset : 'this_month';
    }

    public function render(): View
    {
        $startDate = null;
        $endDate = null;

        if ($this->periodPreset === 'this_month') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($this->periodPreset === 'this_year') {
            $startDate = Carbon::now()->startOfYear()->format('Y-m-d');
            $endDate = Carbon::now()->endOfYear()->format('Y-m-d');
        }

        $accounts = ChartOfAccount::with(['journalItems' => function ($q) use ($startDate, $endDate) {
            $q->when($startDate && $endDate, function ($sub) use ($startDate, $endDate) {
                $sub->whereHas('journalEntry', function ($j) use ($startDate, $endDate) {
                    $j->whereBetween('entry_date', [$startDate, $endDate]);
                });
            });
        }])->get();

        // 1. Laba Rugi Accounts
        $revenueAccounts = $accounts->where('type', 'revenue');
        $cogsAccounts = $accounts->where('type', 'cogs');
        $expenseAccounts = $accounts->where('type', 'expense');

        $totalRevenue = (float) $revenueAccounts->sum('balance');
        $totalCogs = (float) $cogsAccounts->sum('balance');
        $grossProfit = $totalRevenue - $totalCogs;
        $totalExpense = (float) $expenseAccounts->sum('balance');
        $netIncome = $grossProfit - $totalExpense;

        // 2. Neraca Accounts (Aset, Kewajiban, Ekuitas)
        // Untuk Neraca, saldo adalah kumulatif sampai tanggal akhir
        $balanceSheetAccounts = ChartOfAccount::with(['journalItems' => function ($q) use ($endDate) {
            $q->when($endDate, function ($sub) use ($endDate) {
                $sub->whereHas('journalEntry', function ($j) use ($endDate) {
                    $j->where('entry_date', '<=', $endDate);
                });
            });
        }])->get();

        $assetAccounts = $balanceSheetAccounts->where('type', 'asset');
        $liabilityAccounts = $balanceSheetAccounts->where('type', 'liability');
        $equityAccounts = $balanceSheetAccounts->where('type', 'equity');

        $totalAssets = (float) $assetAccounts->sum('balance');
        $totalLiabilities = (float) $liabilityAccounts->sum('balance');

        // Net income cumulative for equity
        $cumulativeRevenues = (float) $balanceSheetAccounts->where('type', 'revenue')->sum('balance');
        $cumulativeCogs = (float) $balanceSheetAccounts->where('type', 'cogs')->sum('balance');
        $cumulativeExpenses = (float) $balanceSheetAccounts->where('type', 'expense')->sum('balance');
        $cumulativeNetIncome = ($cumulativeRevenues - $cumulativeCogs) - $cumulativeExpenses;

        $totalEquityWithoutIncome = (float) $equityAccounts->sum(function ($acc) {
            return $acc->normal_balance === 'debit' ? -$acc->balance : $acc->balance;
        });
        $totalEquity = $totalEquityWithoutIncome + $cumulativeNetIncome;
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return view('livewire.accounting.financial-statements', [
            'revenueAccounts' => $revenueAccounts,
            'cogsAccounts' => $cogsAccounts,
            'expenseAccounts' => $expenseAccounts,
            'totalRevenue' => $totalRevenue,
            'totalCogs' => $totalCogs,
            'grossProfit' => $grossProfit,
            'totalExpense' => $totalExpense,
            'netIncome' => $netIncome,
            'assetAccounts' => $assetAccounts,
            'liabilityAccounts' => $liabilityAccounts,
            'equityAccounts' => $equityAccounts,
            'totalAssets' => $totalAssets,
            'totalLiabilities' => $totalLiabilities,
            'cumulativeNetIncome' => $cumulativeNetIncome,
            'totalEquity' => $totalEquity,
            'totalLiabilitiesAndEquity' => $totalLiabilitiesAndEquity,
        ]);
    }
}
