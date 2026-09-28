<?php

namespace App\Http\Controllers;

use App\Models\ChartOfAccount;
use App\Services\FinancialStatementPdfService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class FinancialStatementPrintController extends Controller
{
    /**
     * Display printable clean financial statement without dashboard layout.
     */
    public function show(Request $request): View
    {
        $data = $this->prepareFinancialData($request);

        return view('accounting.print', $data);
    }

    /**
     * Download crisp vector A4 PDF file directly to device.
     */
    public function downloadPdf(Request $request): Response
    {
        $data = $this->prepareFinancialData($request);
        $pdfContent = FinancialStatementPdfService::generate($data);

        $isLabaRugi = $data['activeTab'] === 'income_statement';
        $reportName = $isLabaRugi ? 'Laporan-Laba-Rugi-HalalaFood' : 'Neraca-Keuangan-HalalaFood';
        $dateStr = date('Y-m-d');
        $filename = "{$reportName}-{$dateStr}.pdf";

        return response($pdfContent, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    /**
     * Prepare data for financial statements.
     *
     * @return array<string, mixed>
     */
    protected function prepareFinancialData(Request $request): array
    {
        $tab = $request->query('tab', 'income_statement');
        $tab = in_array($tab, ['income_statement', 'balance_sheet']) ? $tab : 'income_statement';

        $period = $request->query('period', 'this_month');
        $period = in_array($period, ['this_month', 'this_year', 'all']) ? $period : 'this_month';

        $startDate = null;
        $endDate = null;

        if ($period === 'this_month') {
            $startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
            $endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
        } elseif ($period === 'this_year') {
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

        // 2. Neraca Accounts
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

        $cumulativeRevenues = (float) $balanceSheetAccounts->where('type', 'revenue')->sum('balance');
        $cumulativeCogs = (float) $balanceSheetAccounts->where('type', 'cogs')->sum('balance');
        $cumulativeExpenses = (float) $balanceSheetAccounts->where('type', 'expense')->sum('balance');
        $cumulativeNetIncome = ($cumulativeRevenues - $cumulativeCogs) - $cumulativeExpenses;

        $totalEquityWithoutIncome = (float) $equityAccounts->sum(function ($acc) {
            return $acc->normal_balance === 'debit' ? -$acc->balance : $acc->balance;
        });
        $totalEquity = $totalEquityWithoutIncome + $cumulativeNetIncome;
        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        return [
            'activeTab' => $tab,
            'periodPreset' => $period,
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
        ];
    }
}
