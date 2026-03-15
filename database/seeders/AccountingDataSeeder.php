<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\BankStatement;
use App\Models\BankStatementLine;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Item;
use App\Models\JournalEntry;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\Store;
use App\Models\User;
use App\Models\Vendor;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountingDataSeeder extends Seeder
{
    public function run()
    {
        if (! Auth::check()) {
            $user = User::first() ?? User::create([
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => bcrypt('password'),
            ]);
            Auth::login($user);
        }

        // Clean up previous test data to allow re-running
        DB::table('purchase_bill_items')->whereIn('purchase_bill_id', function ($q) {
            $q->select('id')->from('purchase_bills')->where('bill_no', 'like', '%TEST%');
        })->delete();
        DB::table('purchase_bills')->where('bill_no', 'like', '%TEST%')->delete();

        DB::table('sales_invoice_items')->whereIn('sales_invoice_id', function ($q) {
            $q->select('id')->from('sales_invoices')->where('invoice_no', 'like', '%TEST%');
        })->delete();
        DB::table('sales_invoices')->where('invoice_no', 'like', '%TEST%')->delete();

        DB::table('vendor_payments')->where('payment_no', 'like', '%TEST%')->delete();
        DB::table('customer_payments')->where('payment_no', 'like', '%TEST%')->delete();

        DB::table('journal_items')->whereIn('journal_entry_id', function ($q) {
            $q->select('id')->from('journal_entries')->where('reference', 'like', '%TEST%');
        })->delete();
        DB::table('journal_entries')->where('reference', 'like', '%TEST%')->delete();

        DB::table('stock_adjustment_items')->whereIn('stock_adjustment_id', function ($q) {
            $q->select('id')->from('stock_adjustments')->where('adjustment_no', 'like', '%TEST%');
        })->delete();
        DB::table('stock_adjustments')->where('adjustment_no', 'like', '%TEST%')->delete();

        DB::table('bank_statement_lines')->whereIn('bank_statement_id', function ($q) {
            $q->select('id')->from('bank_statements')->where('statement_no', 'like', '%TEST%');
        })->delete();
        DB::table('bank_statements')->where('statement_no', 'like', '%TEST%')->delete();

        // 1. Ensure COA and Settings are there
        $this->call(AccountingModuleSeeder::class);

        $accounting = app(AccountingService::class);
        $inventory = app(InventoryService::class);

        // Get basic data
        /** @var \App\Models\Vendor $vendor */
        $vendor = Vendor::first();
        /** @var \App\Models\Customer $customer */
        $customer = Customer::first();
        /** @var \App\Models\Item $item */
        $item = Item::where('type', 'product')->first();
        /** @var \App\Models\Branch $branch */
        $branch = Branch::first();
        /** @var \App\Models\Store $store */
        $store = Store::first();

        if (! $vendor || ! $customer || ! $item || ! $branch || ! $store) {
            if ($this->command) {
                $this->command->error('Basic data missing. Run CoreERPSystemSeeder first.');
            } else {
                echo "Basic data missing. Run CoreERPSystemSeeder first.\n";
            }

            return;
        }

        // --- PURCHASE FLOW ---
        if ($this->command) {
            $this->command->info('Seeding Purchase Flow Accounting...');
        } else {
            echo "Seeding Purchase Flow Accounting...\n";
        }

        /** @var \App\Models\PurchaseBill $bill */
        $bill = PurchaseBill::create([
            'bill_no' => 'BILL-TEST-001',
            'vendor_id' => $vendor->id,
            'branch_id' => $branch->id,
            'store_id' => $store->id,
            'bill_date' => now()->subDays(5),
            'due_date' => now()->addDays(25),
            'total_amount' => 1000,
            'tax_amount' => 150,
            'grand_total' => 1150,
            'paid_amount' => 0,
            'balance_amount' => 1150,
            'status' => 'unpaid',
            'created_by' => 1,
        ]);

        $accounting->postPurchaseBill($bill);

        // Record Payment
        /** @var \App\Models\Account $bankAccount */
        $bankAccount = Account::where('code', '1010')->first(); // Cash/Bank
        $accounting->recordVendorPayment([
            'payment_no' => 'VPMT-TEST-001',
            'vendor_id' => $vendor->id,
            'purchase_bill_id' => $bill->id,
            'payment_date' => now()->subDays(2),
            'amount' => 500,
            'payment_method' => 'Bank Transfer',
            'account_id' => $bankAccount->id,
            'branch_id' => $branch->id,
        ]);

        // --- SALES FLOW ---
        if ($this->command) {
            $this->command->info('Seeding Sales Flow Accounting...');
        } else {
            echo "Seeding Sales Flow Accounting...\n";
        }

        /** @var \App\Models\SalesInvoice $invoice */
        $invoice = SalesInvoice::create([
            'invoice_no' => 'INV-TEST-001',
            'customer_id' => $customer->id,
            'branch_id' => $branch->id,
            'invoice_date' => now()->subDays(4),
            'due_date' => now()->addDays(26),
            'total_amount' => 2000,
            'tax_amount' => 300,
            'grand_total' => 2300,
            'balance_amount' => 2300,
            'status' => 'unpaid',
            'created_by' => 1,
        ]);

        $accounting->postSalesInvoice($invoice);

        // Record Partial Payment
        $accounting->recordCustomerPayment([
            'payment_no' => 'PAY-IN-TEST-001',
            'customer_id' => $customer->id,
            'sales_invoice_id' => $invoice->id,
            'payment_date' => now()->subDays(1),
            'amount' => 1000,
            'payment_method' => 'Cash',
            'account_id' => $bankAccount->id,
        ]);

        // --- INVENTORY ADJUSTMENT ---
        if ($this->command) {
            $this->command->info('Seeding Inventory Adjustment Accounting...');
        } else {
            echo "Seeding Inventory Adjustment Accounting...\n";
        }

        $adjustment = $inventory->createAdjustment([
            'adjustment_no' => 'ADJ-TEST-001',
            'adjustment_date' => now(),
            'store_id' => $store->id,
            'reason' => 'Damaged Goods',
            'items' => [
                [
                    'item_id' => $item->id,
                    'quantity' => max(0, $item->current_stock - 5),
                ],
            ],
        ]);

        $inventory->finalizeAdjustment($adjustment);

        // --- BANK RECONCILIATION ---
        if ($this->command) {
            $this->command->info('Seeding Bank Reconciliation Data...');
        } else {
            echo "Seeding Bank Reconciliation Data...\n";
        }

        /** @var \App\Models\BankStatement $bankStatement */
        $bankStatement = BankStatement::create([
            'account_id' => $bankAccount->id,
            'statement_no' => 'STMT-TEST-001',
            'start_date' => now()->subMonth()->startOfMonth(),
            'end_date' => now()->subMonth()->endOfMonth(),
            'opening_balance' => 5000,
            'closing_balance' => 4500,
            'status' => 'partial',
            'created_by' => Auth::id(),
        ]);

        BankStatementLine::create([
            'bank_statement_id' => $bankStatement->id,
            'date' => now()->subDays(15),
            'reference' => 'VPMT-TEST-001',
            'description' => 'Vendor Payment to '.$vendor->name,
            'amount' => -500,
            'is_reconciled' => false,
        ]);

        if ($this->command) {
            $this->command->info('Accounting test data seeded successfully.');
            $this->command->info('Journal Entries created: '.JournalEntry::count());
        } else {
            echo "Accounting test data seeded successfully.\n";
            echo 'Journal Entries created: '.JournalEntry::count()."\n";
        }
    }
}
