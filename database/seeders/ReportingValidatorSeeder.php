<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\Item;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\Store;
use App\Models\Vendor;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ReportingValidatorSeeder extends Seeder
{
    public function run()
    {
        $accounting = new AccountingService;
        $inventory = new InventoryService;
        /** @var \App\Models\CompanySetting $settings */
        $settings = CompanySetting::first();

        if (! $settings) {
            $this->command->error('Company settings not found. Run base seeders first.');

            return;
        }

        /** @var \App\Models\Branch $branch */
        $branch = Branch::first() ?? Branch::create(['name' => 'Main Branch', 'code' => 'BR001']);

        /** @var \App\Models\Store $store */
        $store = Store::first() ?? Store::create(['name' => 'Main Warehouse', 'branch_id' => $branch->id]);

        // 1. Create Items if not exist
        $items = [
            ['name' => 'Laptop Pro', 'sku' => 'LAP001', 'type' => 'product', 'cost_price' => 1000, 'selling_price' => 1500, 'track_inventory' => true],
            ['name' => 'Wireless Mouse', 'sku' => 'MOU001', 'type' => 'product', 'cost_price' => 20, 'selling_price' => 45, 'track_inventory' => true],
            ['name' => 'LED Monitor 27"', 'sku' => 'MON001', 'type' => 'product', 'cost_price' => 200, 'selling_price' => 350, 'track_inventory' => true],
            ['name' => 'Software License', 'sku' => 'SOFT001', 'type' => 'service', 'cost_price' => 0, 'selling_price' => 299, 'track_inventory' => false],
        ];

        foreach ($items as $itemData) {
            Item::firstOrCreate(['sku' => $itemData['sku']], array_merge($itemData, [
                'slug' => Str::slug($itemData['name']),
                'category_id' => 1, // Assume 1 is valid or first
                'unit_id' => 1,
                'status' => 'active',
            ]));
        }

        $all_items = Item::all();

        // 2. Create Customers and Vendors
        for ($i = 1; $i <= 5; $i++) {
            Customer::firstOrCreate(['customer_id' => 'CUST-00'.$i], [
                'name' => 'Test Customer '.$i,
                'email' => 'customer'.$i.'@example.com',
                'phone' => '123456789'.$i,
                'type' => 'individual',
                'status' => 'active',
            ]);
        }
        $customers = Customer::all();

        for ($i = 1; $i <= 3; $i++) {
            Vendor::firstOrCreate(['vendor_id' => 'VEND-00'.$i], [
                'name' => 'Test Vendor '.$i,
                'email' => 'vendor'.$i.'@example.com',
                'phone' => '987654321'.$i,
                'status' => 'active',
            ]);
        }
        $vendors = Vendor::all();

        // 3. Generate Transactions for 12 months
        $now = Carbon::now();

        for ($i = 11; $i >= 0; $i--) {
            $currentMonth = (clone $now)->subMonths($i);
            $this->command->info('Seeding data for: '.$currentMonth->format('M Y'));

            // a. Random Purchases
            foreach ($vendors as $vendor) {
                $item = $all_items->where('type', 'product')->random();
                $qty = rand(10, 50);
                $total = $qty * $item->cost_price;
                $tax = $total * 0.16;

                /** @var \App\Models\PurchaseBill $bill */
                $bill = PurchaseBill::create([
                    'bill_no' => 'BILL-'.$currentMonth->format('Ym').'-'.$vendor->id.'-'.rand(100, 999),
                    'vendor_id' => $vendor->id,
                    'bill_date' => (clone $currentMonth)->startOfMonth()->addDays(rand(1, 10)),
                    'total_amount' => $total,
                    'tax_amount' => $tax,
                    'grand_total' => $total + $tax,
                    'balance_amount' => $total + $tax,
                    'status' => 'unpaid',
                    'branch_id' => $branch->id,
                    'store_id' => $store->id,
                    'created_by' => 1,
                ]);

                // Create items
                $bill->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $item->cost_price,
                    'tax_amount' => $tax,
                    'amount' => $total,
                ]);

                // Adjust stock
                $inventory->adjustStock($item, $qty, 'IN', $bill->bill_no, "Purchase from {$vendor->name}", $branch->id, $store->id);

                // Post to Accounting
                $accounting->postPurchaseBill($bill);

                // Maybe pay some bills
                if (rand(0, 1)) {
                    $payAmt = $bill->grand_total * (rand(50, 100) / 100);
                    $accounting->recordVendorPayment([
                        'payment_no' => 'VP-'.uniqid(),
                        'vendor_id' => $vendor->id,
                        'purchase_bill_id' => $bill->id,
                        'payment_date' => $bill->bill_date->addDays(5),
                        'amount' => $payAmt,
                        'payment_method' => 'Bank Transfer',
                        'account_id' => $settings->default_bank_account_id,
                        'branch_id' => $branch->id,
                    ]);
                }
            }

            // b. Random Sales
            foreach ($customers as $customer) {
                $item = $all_items->random();
                if ($item->type === 'product' && $item->current_stock < 5) {
                    continue;
                }

                $qty = rand(1, 5);
                $total = $qty * $item->selling_price;
                $tax = $total * 0.16;

                /** @var \App\Models\SalesInvoice $invoice */
                $invoice = SalesInvoice::create([
                    'invoice_no' => 'INV-'.$currentMonth->format('Ym').'-'.$customer->id.'-'.rand(100, 999),
                    'customer_id' => $customer->id,
                    'invoice_date' => (clone $currentMonth)->startOfMonth()->addDays(rand(11, 28)),
                    'total_amount' => $total,
                    'tax_amount' => $tax,
                    'grand_total' => $total + $tax,
                    'balance_amount' => $total + $tax,
                    'status' => 'unpaid',
                    'branch_id' => $branch->id,
                    'created_by' => 1,
                ]);

                $invoice->items()->create([
                    'item_id' => $item->id,
                    'quantity' => $qty,
                    'unit_price' => $item->selling_price,
                    'tax_amount' => $tax,
                    'amount' => $total,
                ]);

                // Outward Movement for products
                if ($item->type === 'product') {
                    $inventory->adjustStock($item, -$qty, 'OUT', $invoice->invoice_no, "Sale to {$customer->name}", $branch->id, $store->id);
                }

                // Post to Accounting
                $accounting->postSalesInvoice($invoice);

                // Maybe pay some invoices
                if (rand(0, 1)) {
                    $payAmt = $invoice->grand_total * (rand(50, 100) / 100);
                    $accounting->recordCustomerPayment([
                        'payment_no' => 'CP-'.uniqid(),
                        'customer_id' => $customer->id,
                        'sales_invoice_id' => $invoice->id,
                        'payment_date' => $invoice->invoice_date->addDays(2),
                        'amount' => $payAmt,
                        'payment_method' => 'Cash',
                        'account_id' => $settings->default_cash_account_id,
                    ]);
                }
            }

            // c. Random Expenses (Journal Entries)
            $expenseAmt = rand(500, 2000);
            $accounting->createJournalEntry([
                'date' => (clone $currentMonth)->endOfMonth(),
                'description' => 'Monthly Expenses - '.$currentMonth->format('M Y'),
                'branch_id' => $branch->id,
                'lines' => [
                    ['account_id' => $settings->default_expense_account_id ?? 18, 'debit' => $expenseAmt, 'credit' => 0, 'description' => 'Office Rent'],
                    ['account_id' => $settings->default_bank_account_id, 'debit' => 0, 'credit' => $expenseAmt, 'description' => 'Paid from Bank'],
                ],
            ]);
        }
    }
}
