<?php

namespace App\Services;

use App\Models\Account;
use App\Models\CompanySetting;
use App\Models\CustomerPayment;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\StockAdjustment;
use App\Models\VendorPayment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    /**
     * Get balances for all accounts as of a specific date in a single optimized query.
     */
    public function getBalancesAsOf(string $date): Collection
    {
        $accounts = Account::all();

        $activity = JournalItem::query()
            ->whereHas('entry', function ($query) use ($date) {
                $query->where('status', 'posted')
                    ->whereDate('date', '<=', $date);
            })
            ->select('account_id')
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        return $accounts->map(function ($account) use ($activity) {
            $row = $activity->get($account->id);
            $debit = $row ? $row->total_debit : 0;
            $credit = $row ? $row->total_credit : 0;

            if (in_array($account->type, ['asset', 'expense'])) {
                $balance = $account->opening_balance + $debit - $credit;
            } else {
                $balance = $account->opening_balance + $credit - $debit;
            }

            $account->current_balance = (string) $balance;

            return $account;
        });
    }

    /**
     * Get net activity (movement) for all accounts for a period.
     */
    public function getBalancesForPeriod(string $startDate, string $endDate): Collection
    {
        $accounts = Account::all();

        $activity = JournalItem::query()
            ->whereHas('entry', function ($query) use ($startDate, $endDate) {
                $query->where('status', 'posted')
                    ->whereDate('date', '>=', $startDate)
                    ->whereDate('date', '<=', $endDate);
            })
            ->select('account_id')
            ->selectRaw('SUM(debit) as total_debit, SUM(credit) as total_credit')
            ->groupBy('account_id')
            ->get()
            ->keyBy('account_id');

        return $accounts->map(function ($account) use ($activity) {
            $row = $activity->get($account->id);
            $debit = $row ? $row->total_debit : 0;
            $credit = $row ? $row->total_credit : 0;

            if (in_array($account->type, ['revenue', 'expense'])) {
                $balance = ($account->type === 'expense') ? ($debit - $credit) : ($credit - $debit);
            } else {
                $balance = in_array($account->type, ['asset']) ? ($debit - $credit) : ($credit - $debit);
            }

            $account->period_balance = $balance;

            return $account;
        });
    }

    /**
     * Create a journal entry and its items, ensuring balances are synced.
     */
    public function createJournalEntry(array $data): JournalEntry
    {
        return DB::transaction(function () use ($data) {
            $totalDebit = collect($data['lines'])->sum('debit');
            $totalCredit = collect($data['lines'])->sum('credit');

            if (round($totalAmount = (float) $totalDebit, 2) !== round((float) $totalCredit, 2)) {
                throw new \Exception('Journal entry must be balanced (Debits: '.$totalDebit.', Credits: '.$totalCredit.')');
            }

            $reference = $data['reference'] ?? 'JE-'.str_pad(JournalEntry::count() + 1, 6, '0', STR_PAD_LEFT);

            $entry = JournalEntry::create([
                'date' => $data['date'] ?? now(),
                'reference' => $reference,
                'description' => $data['description'],
                'status' => $data['status'] ?? 'posted',
                'total_amount' => $totalAmount,
                'user_id' => Auth::id(),
                'branch_id' => $data['branch_id'] ?? null,
            ]);

            foreach ($data['lines'] as $line) {
                if ($line['debit'] == 0 && $line['credit'] == 0) {
                    continue;
                }

                $entry->items()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'],
                    'credit' => $line['credit'],
                    'description' => $line['description'] ?? null,
                ]);
            }

            $accountIds = collect($data['lines'])->pluck('account_id')->unique();
            foreach (Account::whereIn('id', $accountIds)->get() as $account) {
                $account->syncBalance();
            }

            return $entry;
        });
    }

    /**
     * RECORD Methods (Functional ERP logic)
     */
    public function recordVendorPayment(array $data): VendorPayment
    {
        return DB::transaction(function () use ($data) {
            $payment = VendorPayment::create([
                'payment_no' => $data['payment_no'],
                'vendor_id' => $data['vendor_id'],
                'purchase_bill_id' => $data['purchase_bill_id'] ?? null,
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'account_id' => $data['account_id'],
                'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? null,
                'branch_id' => $data['branch_id'],
                'created_by' => Auth::id(),
            ]);

            if (isset($data['purchase_bill_id']) && $data['purchase_bill_id']) {
                $bill = PurchaseBill::findOrFail($data['purchase_bill_id']);
                $bill->paid_amount += $data['amount'];
                $bill->balance_amount = max(0, $bill->grand_total - $bill->paid_amount);

                if ($bill->balance_amount <= 0.001) {
                    $bill->status = 'paid';
                } else {
                    $bill->status = 'partially_paid';
                }
                $bill->save();
            }

            // Post to Ledger
            $this->postVendorPayment($payment);

            return $payment;
        });
    }

    public function recordCustomerPayment(array $data): CustomerPayment
    {
        return DB::transaction(function () use ($data) {
            $payment = CustomerPayment::create([
                'payment_no' => $data['payment_no'],
                'customer_id' => $data['customer_id'],
                'sales_invoice_id' => $data['sales_invoice_id'] ?? null,
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'],
                'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? null,
                'account_id' => $data['account_id'],
                'created_by' => Auth::id(),
            ]);

            if (isset($data['sales_invoice_id']) && $data['sales_invoice_id']) {
                $invoice = SalesInvoice::findOrFail($data['sales_invoice_id']);
                $invoice->paid_amount += $data['amount'];
                $invoice->balance_amount = max(0, $invoice->grand_total - $invoice->paid_amount);

                if ($invoice->balance_amount <= 0.001) {
                    $invoice->status = 'paid';
                } else {
                    $invoice->status = 'partially_paid';
                }
                $invoice->save();
            }

            // Post to Ledger
            $this->postCustomerPayment($payment);

            return $payment;
        });
    }

    /**
     * POSTING Methods (Automated Accounting)
     */
    public function postPurchaseBill(PurchaseBill $bill): JournalEntry
    {
        /** @var \App\Models\CompanySetting $settings */
        $settings = CompanySetting::first();
        if (! $settings->default_inventory_account_id || ! $settings->default_ap_account_id) {
            throw new \Exception('Accounting mappings (Inventory/AP) not configured.');
        }

        return $this->createJournalEntry([
            'date' => $bill->bill_date,
            'reference' => $bill->bill_no,
            'description' => "Purchase Bill: {$bill->bill_no} - Vendor: {$bill->vendor->name}",
            'branch_id' => $bill->branch_id,
            'lines' => [
                [
                    'account_id' => $settings->default_inventory_account_id,
                    'debit' => $bill->total_amount,
                    'credit' => 0,
                    'description' => "Inventory Purchase: {$bill->bill_no}",
                ],
                [
                    'account_id' => $settings->default_input_vat_account_id ?? $settings->default_ap_account_id,
                    'debit' => $bill->tax_amount,
                    'credit' => 0,
                    'description' => "Input Tax: {$bill->bill_no}",
                ],
                [
                    'account_id' => $settings->default_ap_account_id,
                    'debit' => 0,
                    'credit' => $bill->grand_total,
                    'description' => "Payable: {$bill->bill_no}",
                ],
            ],
        ]);
    }

    public function postVendorPayment(VendorPayment $payment): JournalEntry
    {
        /** @var \App\Models\CompanySetting $settings */
        $settings = CompanySetting::first();
        if (! $settings->default_ap_account_id) {
            throw new \Exception('Accounting mapping (AP) not configured.');
        }

        return $this->createJournalEntry([
            'date' => $payment->payment_date,
            'reference' => $payment->payment_no,
            'description' => "Payment to Vendor: {$payment->vendor->name} - Ref: {$payment->payment_no}",
            'branch_id' => $payment->branch_id,
            'lines' => [
                [
                    'account_id' => $settings->default_ap_account_id,
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'description' => "Debit AP: {$payment->payment_no}",
                ],
                [
                    'account_id' => $payment->account_id ?? $settings->default_bank_account_id,
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'description' => "Credit Asset: {$payment->payment_no}",
                ],
            ],
        ]);
    }

    public function postSalesInvoice(SalesInvoice $invoice): JournalEntry
    {
        /** @var \App\Models\CompanySetting $settings */
        $settings = CompanySetting::first();
        if (! $settings->default_ar_account_id || ! $settings->default_sales_income_account_id) {
            throw new \Exception('Accounting mapping (AR/Sales) not configured.');
        }

        return $this->createJournalEntry([
            'date' => $invoice->invoice_date,
            'reference' => $invoice->invoice_no,
            'description' => "Sales Invoice: {$invoice->invoice_no} - Customer: {$invoice->customer->name}",
            'branch_id' => $invoice->branch_id,
            'lines' => [
                [
                    'account_id' => $settings->default_ar_account_id,
                    'debit' => $invoice->grand_total,
                    'credit' => 0,
                    'description' => "Receivable: {$invoice->invoice_no}",
                ],
                [
                    'account_id' => $settings->default_sales_income_account_id,
                    'debit' => 0,
                    'credit' => $invoice->total_amount,
                    'description' => "Sales Revenue: {$invoice->invoice_no}",
                ],
                [
                    'account_id' => $settings->default_output_vat_account_id ?? $settings->default_ar_account_id,
                    'debit' => 0,
                    'credit' => $invoice->tax_amount,
                    'description' => "Output Tax: {$invoice->invoice_no}",
                ],
            ],
        ]);
    }

    public function postCustomerPayment(CustomerPayment $payment): JournalEntry
    {
        /** @var \App\Models\CompanySetting $settings */
        $settings = CompanySetting::first();
        if (! $settings->default_ar_account_id) {
            throw new \Exception('Accounting mapping (AR) not configured.');
        }

        return $this->createJournalEntry([
            'date' => $payment->payment_date,
            'reference' => $payment->payment_no,
            'description' => "Payment from Customer: {$payment->customer->name} - Ref: {$payment->payment_no}",
            'branch_id' => $payment->branch_id,
            'lines' => [
                [
                    'account_id' => $payment->account_id ?? $settings->default_bank_account_id,
                    'debit' => $payment->amount,
                    'credit' => 0,
                    'description' => "Debit Asset: {$payment->payment_no}",
                ],
                [
                    'account_id' => $settings->default_ar_account_id,
                    'debit' => 0,
                    'credit' => $payment->amount,
                    'description' => "Credit AR: {$payment->payment_no}",
                ],
            ],
        ]);
    }

    public function postStockAdjustment(StockAdjustment $adjustment): JournalEntry
    {
        /** @var \App\Models\CompanySetting $settings */
        $settings = CompanySetting::first();
        if (! $settings->default_inventory_account_id || ! $settings->default_stock_adjustment_account_id) {
            throw new \Exception('Accounting mapping (Inventory/Adjustment) not configured.');
        }

        $adjustment->load('items.item');
        $totalValue = 0;
        foreach ($adjustment->items as $item) {
            $totalValue += $item->adjustment_quantity * ($item->item->cost_price ?? 0);
        }

        $isPositive = $totalValue >= 0;
        $absAmount = abs((float) $totalValue);

        return $this->createJournalEntry([
            'date' => $adjustment->adjustment_date,
            'reference' => $adjustment->adjustment_no,
            'description' => "Stock Adjustment: {$adjustment->adjustment_no} - Reason: {$adjustment->reason}",
            'lines' => [
                [
                    'account_id' => $settings->default_inventory_account_id,
                    'debit' => $isPositive ? $absAmount : 0,
                    'credit' => ! $isPositive ? $absAmount : 0,
                    'description' => 'Inventory Impact',
                ],
                [
                    'account_id' => $settings->default_stock_adjustment_account_id,
                    'debit' => ! $isPositive ? $absAmount : 0,
                    'credit' => $isPositive ? $absAmount : 0,
                    'description' => 'Adjustment Loss/Gain',
                ],
            ],
        ]);
    }

    public function updateJournalEntry(JournalEntry $entry, array $data): JournalEntry
    {
        return DB::transaction(function () use ($entry, $data) {
            $totalDebit = collect($data['lines'])->sum('debit');
            $totalCredit = collect($data['lines'])->sum('credit');

            if (round($totalAmount = (float) $totalDebit, 2) !== round((float) $totalCredit, 2)) {
                throw new \Exception('Journal entry must be balanced (Debits: '.$totalDebit.', Credits: '.$totalCredit.')');
            }

            // Track old accounts for balance syncing
            $oldAccountIds = $entry->items()->pluck('account_id')->unique();

            $entry->update([
                'date' => $data['date'] ?? $entry->date,
                'description' => $data['description'] ?? $entry->description,
                'status' => $data['status'] ?? $entry->status,
                'total_amount' => $totalAmount,
            ]);

            // Replace items
            $entry->items()->delete();
            foreach ($data['lines'] as $line) {
                if (($line['debit'] ?? 0) == 0 && ($line['credit'] ?? 0) == 0) {
                    continue;
                }

                $entry->items()->create([
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                    'description' => $line['description'] ?? null,
                ]);
            }

            $newAccountIds = collect($data['lines'])->pluck('account_id')->unique();
            $allAccountIds = $oldAccountIds->merge($newAccountIds)->unique();

            foreach (Account::whereIn('id', $allAccountIds)->get() as $account) {
                $account->syncBalance();
            }

            return $entry;
        });
    }

    /**
     * General maintenance and deletion logic
     */
    public function deleteJournalEntry(JournalEntry $entry): void
    {
        DB::transaction(function () use ($entry) {
            $accountIds = $entry->items()->pluck('account_id');
            $entry->items()->delete();
            $entry->delete();

            foreach (Account::whereIn('id', $accountIds)->get() as $account) {
                $account->syncBalance();
            }
        });
    }
}
