<?php

namespace App\Livewire\Accounting;

use App\Models\JournalEntry;
use App\Models\JournalItem;
use Carbon\Carbon;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Jurnal Umum Akuntansi')]
class Journals extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $dateRange = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingDateRange(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'dateRange']);
        $this->resetPage();
    }

    public function setQuickDate(string $period): void
    {
        if ($period === 'today') {
            $today = Carbon::today()->format('Y-m-d');
            $this->dateRange = $today.' - '.$today;
        } elseif ($period === 'this_month') {
            $start = Carbon::now()->startOfMonth()->format('Y-m-d');
            $end = Carbon::now()->endOfMonth()->format('Y-m-d');
            $this->dateRange = $start.' - '.$end;
        } elseif ($period === 'all') {
            $this->dateRange = '';
        }
        $this->resetPage();
    }

    public function render(): View
    {
        $startDate = '';
        $endDate = '';
        if ($this->dateRange) {
            $dates = explode(' - ', $this->dateRange);
            $startDate = trim($dates[0] ?? '');
            $endDate = trim($dates[1] ?? $startDate);
        }

        $query = JournalEntry::with(['items.account'])
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where('entry_number', 'like', $term)
                    ->orWhere('notes', 'like', $term)
                    ->orWhereHas('items.account', function ($sub) use ($term) {
                        $sub->where('name', 'like', $term)->orWhere('code', 'like', $term);
                    });
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate]);
            })
            ->orderBy('entry_date', 'desc')
            ->orderBy('id', 'desc');

        $entries = $query->paginate(15);

        // Calculate totals for currently filtered period
        $summaryQuery = JournalEntry::query()
            ->when($this->search, function ($q) {
                $term = '%'.$this->search.'%';
                $q->where('entry_number', 'like', $term)
                    ->orWhere('notes', 'like', $term)
                    ->orWhereHas('items.account', function ($sub) use ($term) {
                        $sub->where('name', 'like', $term)->orWhere('code', 'like', $term);
                    });
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entry_date', [$startDate, $endDate]);
            });

        $entryIds = $summaryQuery->pluck('id');
        $totalDebit = (float) JournalItem::whereIn('journal_entry_id', $entryIds)->sum('debit');
        $totalCredit = (float) JournalItem::whereIn('journal_entry_id', $entryIds)->sum('credit');

        return view('livewire.accounting.journals', [
            'entries' => $entries,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
        ]);
    }
}
