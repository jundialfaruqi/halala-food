<?php

namespace App\Services;

use App\Models\CashTransaction;
use App\Models\ChartOfAccount;
use App\Models\Consignment;
use App\Models\JournalEntry;
use App\Models\Production;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Generate unique journal entry number: JU-YYYYMM-XXXX
     */
    public static function generateEntryNumber(string $date): string
    {
        $ym = Carbon::parse($date)->format('Ym');
        $count = JournalEntry::where('entry_number', 'like', "JU-{$ym}-%")->count() + 1;

        return 'JU-'.$ym.'-'.str_pad((string) $count, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Post a balanced journal entry
     *
     * @param  array<int, array{account_code: string, debit: float|int, credit: float|int, memo?: ?string}>  $items
     */
    public static function postEntry(
        string $date,
        string $notes,
        array $items,
        ?string $referenceType = null,
        ?int $referenceId = null,
        ?int $userId = null
    ): ?JournalEntry {
        // Filter out zero amount lines
        $validItems = array_values(array_filter($items, function ($item) {
            $debit = (float) ($item['debit'] ?? 0);
            $credit = (float) ($item['credit'] ?? 0);

            return $debit > 0 || $credit > 0;
        }));

        if (empty($validItems)) {
            return null;
        }

        $totalDebit = round(collect($validItems)->sum('debit'), 2);
        $totalCredit = round(collect($validItems)->sum('credit'), 2);

        if ($totalDebit !== $totalCredit) {
            // Jaga keseimbangan jurnal debet = kredit
            throw new \InvalidArgumentException("Jurnal tidak seimbang! Total Debet (Rp {$totalDebit}) != Total Kredit (Rp {$totalCredit})");
        }

        return DB::transaction(function () use ($date, $notes, $validItems, $referenceType, $referenceId, $userId) {
            // Jika update transaksi terkait, bersihkan entri sebelumnya
            if ($referenceType && $referenceId) {
                JournalEntry::where('reference_type', $referenceType)
                    ->where('reference_id', $referenceId)
                    ->delete();
            }

            $entry = JournalEntry::create([
                'entry_number' => self::generateEntryNumber($date),
                'entry_date' => $date,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'notes' => $notes,
                'created_by' => $userId ?? Auth::id(),
            ]);

            $accountsByCode = ChartOfAccount::all()->keyBy('code');

            foreach ($validItems as $row) {
                $code = $row['account_code'];
                $account = $accountsByCode->get($code);
                if (! $account) {
                    $account = ChartOfAccount::where('code', $code)->first();
                }

                if ($account) {
                    $entry->items()->create([
                        'chart_of_account_id' => $account->id,
                        'debit' => $row['debit'] ?? 0,
                        'credit' => $row['credit'] ?? 0,
                        'memo' => $row['memo'] ?? null,
                    ]);
                }
            }

            return $entry;
        });
    }

    /**
     * Auto journal for Cash Transactions (Buku Kas)
     */
    public static function recordCashTransaction(CashTransaction $transaction): ?JournalEntry
    {
        $amount = (float) $transaction->amount;
        if ($amount <= 0) {
            return null;
        }

        $account = $transaction->account;
        $isBank = $account && (
            str_contains(strtolower($account->name), 'bank') ||
            str_contains(strtolower($account->name), 'bca') ||
            str_contains(strtolower($account->name), 'bri') ||
            str_contains(strtolower($account->name), 'mandiri')
        );
        $cashCode = $isBank ? '1-1002' : '1-1001';

        $items = [];
        $notes = $transaction->description ?: "Transaksi Kas: {$transaction->category}";

        if ($transaction->type === 'expense') {
            $cat = strtolower($transaction->category);
            $expenseCode = '6-1099'; // default beban operasional

            if (str_contains($cat, 'bahan')) {
                $expenseCode = '1-1300'; // Pembelian bahan baku menambah Persediaan Bahan Baku
            } elseif (str_contains($cat, 'bensin') || str_contains($cat, 'transport') || str_contains($cat, 'ongkir')) {
                $expenseCode = '6-1001';
            } elseif (str_contains($cat, 'listrik') || str_contains($cat, 'air') || str_contains($cat, 'gas')) {
                $expenseCode = '6-1002';
            } elseif (str_contains($cat, 'kemasan') || str_contains($cat, 'toples') || str_contains($cat, 'plastik') || str_contains($cat, 'label') || str_contains($cat, 'stiker')) {
                $expenseCode = '6-1003';
            } elseif (str_contains($cat, 'rusak') || str_contains($cat, 'retur') || str_contains($cat, 'basi') || str_contains($cat, 'kadaluarsa')) {
                $expenseCode = '6-1004';
            }

            $items[] = ['account_code' => $expenseCode, 'debit' => $amount, 'credit' => 0, 'memo' => $transaction->category];
            $items[] = ['account_code' => $cashCode, 'debit' => 0, 'credit' => $amount, 'memo' => 'Kas Keluar: '.($account?->name ?? 'Kas')];
        } elseif ($transaction->type === 'income') {
            $cat = strtolower($transaction->category);
            $incomeCode = str_contains($cat, 'toko') || str_contains($cat, 'penjualan') ? '4-1000' : '4-2000';

            $items[] = ['account_code' => $cashCode, 'debit' => $amount, 'credit' => 0, 'memo' => 'Kas Masuk: '.($account?->name ?? 'Kas')];
            $items[] = ['account_code' => $incomeCode, 'debit' => 0, 'credit' => $amount, 'memo' => $transaction->category];
        } elseif ($transaction->type === 'prive') {
            $items[] = ['account_code' => '3-2000', 'debit' => $amount, 'credit' => 0, 'memo' => 'Penarikan Prive Pemilik'];
            $items[] = ['account_code' => $cashCode, 'debit' => 0, 'credit' => $amount, 'memo' => 'Pengambilan dari '.($account?->name ?? 'Kas Usaha')];
        } elseif ($transaction->type === 'personal_expense') {
            $items[] = ['account_code' => '3-2000', 'debit' => $amount, 'credit' => 0, 'memo' => 'Belanja Dapur / Rumah Tangga Pribadi'];
            $items[] = ['account_code' => '1-1100', 'debit' => 0, 'credit' => $amount, 'memo' => 'Kas Pribadi Keluar'];
        }

        return self::postEntry(
            $transaction->transaction_date->format('Y-m-d'),
            $notes,
            $items,
            'cash_transaction',
            $transaction->id
        );
    }

    /**
     * Auto journal for Production (Memasak / Mengubah Bahan Baku -> Produk Jadi)
     */
    public static function recordProduction(Production $production, float $materialCost): ?JournalEntry
    {
        if ($materialCost <= 0) {
            return null;
        }

        $items = [
            [
                'account_code' => '1-1400', // Persediaan Produk Jadi (+)
                'debit' => $materialCost,
                'credit' => 0,
                'memo' => "Produksi {$production->quantity_produced} {$production->product->unit} {$production->product->name}",
            ],
            [
                'account_code' => '1-1300', // Persediaan Bahan Baku (-)
                'debit' => 0,
                'credit' => $materialCost,
                'memo' => "Pemakaian Bahan Baku untuk {$production->product->name}",
            ],
        ];

        return self::postEntry(
            $production->production_date->format('Y-m-d'),
            "Catat Produksi {$production->product->name} ({$production->quantity_produced} {$production->product->unit})",
            $items,
            'production',
            $production->id
        );
    }

    /**
     * Auto journal for Consignment Settlement (Penagihan Toko Mitra Selesai)
     */
    public static function recordConsignmentSettlement(
        Consignment $consignment,
        float $totalSold,
        float $amountPaid,
        float $cogsCost,
        float $returnedLossCost = 0
    ): ?JournalEntry {
        if ($totalSold <= 0 && $returnedLossCost <= 0) {
            return null;
        }

        $date = $consignment->settlement_date ? $consignment->settlement_date->format('Y-m-d') : now()->format('Y-m-d');
        $storeName = $consignment->store->name;
        $items = [];

        // 1. Catat Penerimaan Kas & Piutang vs Pendapatan Penjualan
        if ($totalSold > 0) {
            if ($amountPaid > 0) {
                $items[] = [
                    'account_code' => '1-1001', // Kas Usaha
                    'debit' => min($amountPaid, $totalSold),
                    'credit' => 0,
                    'memo' => "Penerimaan Kas dari {$storeName}",
                ];
            }

            if ($amountPaid < $totalSold) {
                $unpaid = $totalSold - $amountPaid;
                $items[] = [
                    'account_code' => '1-1200', // Piutang Toko
                    'debit' => $unpaid,
                    'credit' => 0,
                    'memo' => "Sisa Piutang Toko {$storeName}",
                ];
            }

            $items[] = [
                'account_code' => '4-1000', // Pendapatan Penjualan Toko
                'debit' => 0,
                'credit' => $totalSold,
                'memo' => "Penjualan Titip Jual {$storeName} ({$consignment->consignment_number})",
            ];

            // 2. Catat HPP (Beban Pokok Penjualan) & Pengurangan Persediaan Produk Jadi
            if ($cogsCost > 0) {
                $items[] = [
                    'account_code' => '5-1000', // Beban Pokok Penjualan (HPP)
                    'debit' => $cogsCost,
                    'credit' => 0,
                    'memo' => "HPP Produk Terjual di {$storeName}",
                ];
                $items[] = [
                    'account_code' => '1-1400', // Persediaan Produk Jadi
                    'debit' => 0,
                    'credit' => $cogsCost,
                    'memo' => 'Pengurangan Stok Produk Jadi Terjual',
                ];
            }
        }

        // 3. Catat Kerugian Barang Retur/Rusak jika ada
        if ($returnedLossCost > 0) {
            $items[] = [
                'account_code' => '6-1004', // Beban Kerugian Barang Rusak
                'debit' => $returnedLossCost,
                'credit' => 0,
                'memo' => "Kerugian Produk Rusak/Retur dari {$storeName}",
            ];
            $items[] = [
                'account_code' => '1-1400', // Persediaan Produk Jadi
                'debit' => 0,
                'credit' => $returnedLossCost,
                'memo' => 'Pemusnahan/Penghapusan Produk Rusak',
            ];
        }

        return self::postEntry(
            $date,
            "Penagihan Toko {$storeName} ({$consignment->consignment_number})",
            $items,
            'consignment',
            $consignment->id
        );
    }

    /**
     * Ensure chart of accounts are initialized if database is clean.
     */
    public static function ensureChartOfAccountsExist(): void
    {
        if (ChartOfAccount::count() === 0) {
            $seeder = new \Database\Seeders\AccountingSeeder();
            $seeder->run();
        }
    }
}

