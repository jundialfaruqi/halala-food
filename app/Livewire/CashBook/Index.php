<?php

namespace App\Livewire\CashBook;

use App\Models\Account;
use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Buku Kas & Keuangan')]
class Index extends Component
{
    use WithPagination;

    // Filters
    #[Url]
    public string $search = '';

    #[Url]
    public string $typeFilter = 'all'; // 'all', 'income', 'expense', 'prive', 'personal_expense'

    #[Url]
    public string $accountFilter = '';

    #[Url]
    public string $dateRange = '';

    public bool $showTransactionModal = false;

    public bool $showConfirmTransactionModal = false;

    public bool $showAccountModal = false;

    // Transaction Form
    public string $transaction_date = '';

    public ?int $account_id = null;

    public string $type = 'expense'; // 'income', 'expense', 'prive', 'personal_expense'

    public string $category = '';

    public float $amount = 0;

    public string $description = '';

    // Account Form
    public string $account_name = '';

    public string $account_type = 'business';

    public float $initial_balance = 0;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingAccountFilter(): void
    {
        $this->resetPage();
    }

    public function updatingDateRange(): void
    {
        $this->resetPage();
    }

    public function resetFilters(): void
    {
        $this->reset(['search', 'typeFilter', 'accountFilter', 'dateRange']);
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

    public function mount(): void
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        $firstAccount = Account::first();
        $this->account_id = $firstAccount?->id;
    }

    public function openAccountModal(): void
    {
        $this->reset(['account_name', 'account_type', 'initial_balance']);
        $this->account_type = 'business';
        $this->showAccountModal = true;
    }

    public function saveAccount(): void
    {
        $this->validate([
            'account_name' => 'required|string|max:255',
            'account_type' => 'required|in:business,personal',
            'initial_balance' => 'required|numeric|min:0',
        ], [
            'account_name.required' => 'Nama akun kas / rekening wajib diisi.',
            'account_name.string' => 'Nama akun kas harus berupa teks.',
            'account_name.max' => 'Nama akun kas maksimal 255 karakter.',
            'account_type.required' => 'Jenis akun kas wajib dipilih.',
            'account_type.in' => 'Pilihan jenis akun kas tidak valid.',
            'initial_balance.required' => 'Saldo awal wajib diisi (bisa diisi 0 jika baru).',
            'initial_balance.numeric' => 'Saldo awal harus berupa angka.',
            'initial_balance.min' => 'Saldo awal tidak boleh kurang dari 0.',
        ]);

        Account::create([
            'name' => $this->account_name,
            'type' => $this->account_type,
            'balance' => $this->initial_balance,
        ]);

        $this->showAccountModal = false;
        $this->dispatch('toast', message: 'Akun kas baru berhasil ditambahkan.');
    }

    public function openTransactionModal(string $type = 'expense'): void
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        $this->type = $type;
        $this->category = $this->getDefaultCategory($type);
        $this->amount = 0;
        $this->description = '';
        $this->showConfirmTransactionModal = false;
        $this->showTransactionModal = true;
    }

    public function getDefaultCategory(string $type): string
    {
        return match ($type) {
            'income' => 'Penjualan Tambahan',
            'expense' => 'Pembelian Bahan Baku',
            'prive' => 'Pengambilan Uang Usaha untuk Keluarga (Prive)',
            'personal_expense' => 'Kebutuhan Dapur & Belanja Rumah',
            default => 'Operasional',
        };
    }

    public function updatedType(string $value): void
    {
        $this->category = $this->getDefaultCategory($value);
    }

    public function prepareTransactionConfirmation(): void
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense,prive,personal_expense',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
        ], [
            'transaction_date.required' => 'Tanggal transaksi kas wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'account_id.required' => 'Silakan pilih rekening atau kas yang digunakan.',
            'account_id.exists' => 'Rekening atau kas yang dipilih tidak ditemukan.',
            'type.required' => 'Jenis transaksi kas wajib dipilih.',
            'type.in' => 'Jenis transaksi tidak valid.',
            'category.required' => 'Kategori atau pos transaksi wajib diisi.',
            'category.string' => 'Kategori transaksi harus berupa teks.',
            'category.max' => 'Kategori transaksi maksimal 100 karakter.',
            'amount.required' => 'Nominal uang transaksi wajib diisi.',
            'amount.numeric' => 'Nominal uang harus berupa angka.',
            'amount.min' => 'Nominal uang transaksi minimal Rp 1.',
        ]);

        $this->showConfirmTransactionModal = true;
    }

    public function saveTransaction(): void
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense,prive,personal_expense',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
        ], [
            'transaction_date.required' => 'Tanggal transaksi kas wajib diisi.',
            'transaction_date.date' => 'Format tanggal transaksi tidak valid.',
            'account_id.required' => 'Silakan pilih rekening atau kas yang digunakan.',
            'account_id.exists' => 'Rekening atau kas yang dipilih tidak ditemukan.',
            'type.required' => 'Jenis transaksi kas wajib dipilih.',
            'type.in' => 'Jenis transaksi tidak valid.',
            'category.required' => 'Kategori atau pos transaksi wajib diisi.',
            'category.string' => 'Kategori transaksi harus berupa teks.',
            'category.max' => 'Kategori transaksi maksimal 100 karakter.',
            'amount.required' => 'Nominal uang transaksi wajib diisi.',
            'amount.numeric' => 'Nominal uang harus berupa angka.',
            'amount.min' => 'Nominal uang transaksi minimal Rp 1.',
        ]);

        DB::transaction(function () {
            $account = Account::findOrFail($this->account_id);

            // Update balance
            if ($this->type === 'income') {
                $account->increment('balance', $this->amount);
            } else {
                $account->decrement('balance', $this->amount);
            }

            // Simpan transaksi
            CashTransaction::create([
                'transaction_date' => $this->transaction_date,
                'account_id' => $this->account_id,
                'type' => $this->type,
                'category' => $this->category,
                'amount' => $this->amount,
                'description' => $this->description,
            ]);

            // Jika Prive: otomatis tambahkan saldo ke Kas Pribadi jika ada
            if ($this->type === 'prive') {
                $personalAccount = Account::where('type', 'personal')->first();
                if ($personalAccount && $personalAccount->id !== $account->id) {
                    $personalAccount->increment('balance', $this->amount);
                }
            }
        });

        $this->showConfirmTransactionModal = false;
        $this->showTransactionModal = false;
        $this->dispatch('toast', message: 'Transaksi berhasil dicatat ke buku kas.');
    }

    public function render(): View
    {
        $accounts = Account::orderBy('type')->orderBy('name')->get();

        $startDate = '';
        $endDate = '';
        if ($this->dateRange) {
            $dates = explode(' - ', $this->dateRange);
            $startDate = trim($dates[0] ?? '');
            $endDate = trim($dates[1] ?? $startDate);
        }

        $query = CashTransaction::with('account')
            ->when($this->search, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('category', 'like', '%'.$this->search.'%')
                        ->orWhere('description', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->typeFilter && $this->typeFilter !== 'all', function ($q) {
                $q->where('type', $this->typeFilter);
            })
            ->when($this->accountFilter, function ($q) {
                $q->where('account_id', $this->accountFilter);
            })
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('transaction_date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('transaction_date', '<=', $endDate);
            });

        // Filtered summary calculations
        $filteredIncome = (clone $query)->where('type', 'income')->sum('amount');
        $filteredExpense = (clone $query)->whereIn('type', ['expense', 'personal_expense'])->sum('amount');
        $filteredPrive = (clone $query)->where('type', 'prive')->sum('amount');

        $transactions = $query->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $totalBusinessBalance = $accounts->where('type', 'business')->sum('balance');
        $totalPersonalBalance = $accounts->where('type', 'personal')->sum('balance');

        $hasActiveFilters = $this->search !== '' || $this->typeFilter !== 'all' || $this->accountFilter !== '' || $this->dateRange !== '';

        return view('livewire.cash-book.index', [
            'accounts' => $accounts,
            'transactions' => $transactions,
            'totalBusinessBalance' => $totalBusinessBalance,
            'totalPersonalBalance' => $totalPersonalBalance,
            'filteredIncome' => $filteredIncome,
            'filteredExpense' => $filteredExpense,
            'filteredPrive' => $filteredPrive,
            'hasActiveFilters' => $hasActiveFilters,
        ]);
    }
}
