<?php

namespace App\Livewire\CashBook;

use App\Models\Account;
use App\Models\CashTransaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Title('Buku Kas & Keuangan')]
class Index extends Component
{
    use WithPagination;

    public bool $showTransactionModal = false;

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
        ]);

        Account::create([
            'name' => $this->account_name,
            'type' => $this->account_type,
            'balance' => $this->initial_balance,
        ]);

        $this->showAccountModal = false;
        session()->flash('message', 'Akun kas baru berhasil ditambahkan.');
    }

    public function openTransactionModal(string $type = 'expense'): void
    {
        $this->transaction_date = Carbon::now()->format('Y-m-d');
        $this->type = $type;
        $this->category = $this->getDefaultCategory($type);
        $this->amount = 0;
        $this->description = '';
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

    public function saveTransaction(): void
    {
        $this->validate([
            'transaction_date' => 'required|date',
            'account_id' => 'required|exists:accounts,id',
            'type' => 'required|in:income,expense,prive,personal_expense',
            'category' => 'required|string|max:100',
            'amount' => 'required|numeric|min:1',
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

        $this->showTransactionModal = false;
        session()->flash('message', 'Transaksi berhasil dicatat ke buku kas.');
    }

    public function render(): View
    {
        $accounts = Account::orderBy('type')->orderBy('name')->get();
        $transactions = CashTransaction::with('account')
            ->orderBy('transaction_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(20);

        $totalBusinessBalance = $accounts->where('type', 'business')->sum('balance');
        $totalPersonalBalance = $accounts->where('type', 'personal')->sum('balance');

        return view('livewire.cash-book.index', [
            'accounts' => $accounts,
            'transactions' => $transactions,
            'totalBusinessBalance' => $totalBusinessBalance,
            'totalPersonalBalance' => $totalPersonalBalance,
        ]);
    }
}
