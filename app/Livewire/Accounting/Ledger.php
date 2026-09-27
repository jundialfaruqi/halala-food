<?php

namespace App\Livewire\Accounting;

use App\Models\ChartOfAccount;
use App\Models\JournalItem;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Title('Buku Besar Akuntansi')]
class Ledger extends Component
{
    #[Url]
    public ?int $selectedAccountId = null;

    #[Url]
    public string $dateRange = '';

    public function mount(): void
    {
        if (! $this->selectedAccountId) {
            $first = ChartOfAccount::orderBy('code')->first();
            $this->selectedAccountId = $first?->id;
        }
    }

    public function initDefaultAccounts(): void
    {
        AccountingService::ensureChartOfAccountsExist();
        $first = ChartOfAccount::orderBy('code')->first();
        $this->selectedAccountId = $first?->id;
        session()->flash('success', 'Bagan Akun perkiraan standar berhasil diinisialisasi!');
    }

    public function setQuickDate(string $period): void
    {
        if ($period === 'this_month') {
            $start = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end = Carbon::now()->endOfMonth()->format('Y-m-d');
            $this->dateRange = $start.' - '.$end;
        } elseif ($period === 'all') {
            $this->dateRange = '';
        }
    }

    public function render(): View
    {
        $accounts = ChartOfAccount::orderBy('code')->get();

        if ($this->selectedAccountId && ! $accounts->contains('id', $this->selectedAccountId)) {
            $this->selectedAccountId = $accounts->first()?->id;
        }

        $currentAccount = $this->selectedAccountId
            ? $accounts->firstWhere('id', $this->selectedAccountId)
            : $accounts->first();

        $startDate = '';
        $endDate = '';
        if ($this->dateRange) {
            $dates = explode(' - ', $this->dateRange);
            $startDate = trim($dates[0] ?? '');
            $endDate = trim($dates[1] ?? $startDate);
        }

        $items = collect();
        $totalDebit = 0;
        $totalCredit = 0;
        $runningBalance = 0;

        if ($currentAccount) {
            $query = JournalItem::with('journalEntry')
                ->where('chart_of_account_id', $currentAccount->id)
                ->whereHas('journalEntry', function ($q) use ($startDate, $endDate) {
                    if ($startDate && $endDate) {
                        $q->whereBetween('entry_date', [$startDate, $endDate]);
                    }
                })
                ->join('journal_entries', 'journal_items.journal_entry_id', '=', 'journal_entries.id')
                ->orderBy('journal_entries.entry_date', 'asc')
                ->orderBy('journal_entries.id', 'asc')
                ->select('journal_items.*');

            $items = $query->get();

            $totalDebit = (float) $items->sum('debit');
            $totalCredit = (float) $items->sum('credit');

            if ($currentAccount->normal_balance === 'debit') {
                $runningBalance = $totalDebit - $totalCredit;
            } else {
                $runningBalance = $totalCredit - $totalDebit;
            }
        }

        return view('livewire.accounting.ledger', [
            'accounts' => $accounts,
            'currentAccount' => $currentAccount,
            'items' => $items,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'runningBalance' => $runningBalance,
        ]);
    }
}
