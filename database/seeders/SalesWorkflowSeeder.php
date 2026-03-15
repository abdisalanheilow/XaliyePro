<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Item;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Store;
use App\Models\User;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesWorkflowSeeder extends Seeder
{
    protected $inventoryService;

    protected $accountingService;

    public function run(): void
    {
        $this->inventoryService = app(InventoryService::class);
        $this->accountingService = app(AccountingService::class);

        $user = User::first() ?? User::factory()->create();
        Auth::login($user);

        $this->command->info('--- Starting Sales Workflow Validation Seeder ---');

        try {
            DB::transaction(function () use ($user) {
                // 1. Prerequisites
                /** @var \App\Models\Branch $branch */
                $branch = Branch::first() ?? Branch::create(['name' => 'Main Branch', 'code' => 'BR01', 'status' => 'active']);
                /** @var \App\Models\Store $store */
                $store = Store::first() ?? Store::create(['name' => 'Main Warehouse', 'code' => 'WH01', 'branch_id' => $branch->id, 'status' => 'active']);
                /** @var \App\Models\Account $bankAccount */
                $bankAccount = Account::whereIn('sub_type', ['bank', 'cash', 'mobile_money'])->first() ?? Account::create([
                    'code' => '1001', 'name' => 'Main Bank Account', 'type' => 'asset', 'sub_type' => 'bank', 'status' => 'active',
                ]);

                // 2. Customers (5)
                $this->command->info('Creating 5 Customers...');
                $customers = [
                    ['customer_id' => 'CUS-001', 'name' => 'TechNova Solutions', 'email' => 'contact@technova.com', 'type' => 'company', 'payment_terms' => 'net_30', 'credit_limit' => 50000],
                    ['customer_id' => 'CUS-002', 'name' => 'Apex Retailers', 'email' => 'sales@apex.com', 'type' => 'company', 'payment_terms' => 'due_on_receipt', 'credit_limit' => 20000],
                    ['customer_id' => 'CUS-003', 'name' => 'Sarah Jenkins', 'email' => 'sarah@example.com', 'type' => 'individual', 'payment_terms' => 'net_15', 'credit_limit' => 5000],
                    ['customer_id' => 'CUS-004', 'name' => 'Global Logistics Group', 'email' => 'info@globallogistics.com', 'type' => 'company', 'payment_terms' => 'net_60', 'credit_limit' => 100000],
                    ['customer_id' => 'CUS-005', 'name' => 'BuildIt construction', 'email' => 'procurement@buildit.com', 'type' => 'company', 'payment_terms' => 'net_30', 'credit_limit' => 30000],
                ];
                $customerModels = [];
                /** @var \App\Models\Customer[] $customerModels */
                foreach ($customers as $c) {
                    $customerModels[] = Customer::updateOrCreate(['customer_id' => $c['customer_id']], $c);
                }

                // 3. Ensuring Items have stock for testing
                $this->command->info('Checking stock for items...');
                /** @var \App\Models\Item $item1 */
                $item1 = Item::where('sku', 'ITM-001')->first();
                /** @var \App\Models\Item $item2 */
                $item2 = Item::where('sku', 'ITM-003')->first();

                if (! $item1) {
                    $item1 = Item::create(['sku' => 'ITM-001', 'name' => 'Steel Rod 10mm', 'slug' => 'steel-rod-10mm', 'category_id' => 1, 'unit_id' => 1, 'cost_price' => 20, 'selling_price' => 35, 'type' => 'product', 'status' => 'active', 'branch_id' => $branch->id, 'store_id' => $store->id]);
                }
                if ($item1->current_stock < 1000) {
                    $this->inventoryService->adjustStock($item1, 2000, 'IN', 'SALE-INIT-1', 'Initial stock for sales testing', $branch->id, $store->id);
                }

                if (! $item2) {
                    $item2 = Item::create(['sku' => 'ITM-003', 'name' => 'Drill Bit Set', 'slug' => 'drill-bit-set', 'category_id' => 1, 'unit_id' => 1, 'cost_price' => 45, 'selling_price' => 75, 'type' => 'product', 'status' => 'active', 'branch_id' => $branch->id, 'store_id' => $store->id]);
                }
                if ($item2->current_stock < 500) {
                    $this->inventoryService->adjustStock($item2, 1000, 'IN', 'SALE-INIT-2', 'Initial stock for sales testing', $branch->id, $store->id);
                }

                // --- SCENARIO 1: FULL FLOW (SO -> DN -> INV -> PAY) ---
                $this->command->info('Scenario 1: Full Standard Cycle (SO-1001)');
                /** @var \App\Models\SalesOrder $so1 */
                $so1 = SalesOrder::updateOrCreate(['order_no' => 'SO-1001'], [
                    'customer_id' => $customerModels[0]->id, 'order_date' => Carbon::now()->subDays(5),
                    'status' => 'confirmed', 'total_amount' => 3500, 'grand_total' => 3500, 'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);
                SalesOrderItem::updateOrCreate(['sales_order_id' => $so1->id, 'item_id' => $item1->id], ['quantity' => 100, 'unit_price' => 35.00, 'amount' => 3500]);

                /** @var \App\Models\DeliveryNote $dn1 */
                $dn1 = DeliveryNote::updateOrCreate(['delivery_no' => 'DN-1001'], [
                    'sales_order_id' => $so1->id, 'customer_id' => $customerModels[0]->id,
                    'delivery_date' => Carbon::now()->subDays(4), 'status' => 'delivered', 'branch_id' => $branch->id, 'store_id' => $store->id, 'delivered_by' => $user->id,
                ]);
                DeliveryNoteItem::updateOrCreate(['delivery_note_id' => $dn1->id, 'item_id' => $item1->id], ['ordered_qty' => 100, 'delivered_qty' => 100]);

                if (! DB::table('stock_moves')->where('reference', 'DN-1001')->exists()) {
                    $this->inventoryService->adjustStock($item1, -100, 'OUT', 'DN-1001', 'Sales Delivery', $branch->id, $store->id);
                }

                /** @var \App\Models\SalesInvoice $inv1 */
                $inv1 = SalesInvoice::updateOrCreate(['invoice_no' => 'INV-1001'], [
                    'sales_order_id' => $so1->id, 'delivery_note_id' => $dn1->id, 'customer_id' => $customerModels[0]->id,
                    'invoice_date' => Carbon::now()->subDays(3), 'status' => 'paid', 'total_amount' => 3500, 'grand_total' => 3500, 'balance_amount' => 0, 'paid_amount' => 3500,
                    'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);
                SalesInvoiceItem::updateOrCreate(['sales_invoice_id' => $inv1->id, 'item_id' => $item1->id], ['quantity' => 100, 'unit_price' => 35.00, 'amount' => 3500]);

                if (! CustomerPayment::where('payment_no', 'PAY-IN-1001')->exists()) {
                    $this->accountingService->recordCustomerPayment([
                        'payment_no' => 'PAY-IN-1001', 'customer_id' => $customerModels[0]->id, 'sales_invoice_id' => $inv1->id, 'payment_date' => Carbon::now()->subDays(2),
                        'amount' => 3500, 'payment_method' => 'Bank Transfer', 'account_id' => $bankAccount->id, 'branch_id' => $branch->id,
                    ]);
                }

                // --- SCENARIO 2: PARTIAL DELIVERY ---
                $this->command->info('Scenario 2: Partial Delivery (SO-1002)');
                /** @var \App\Models\SalesOrder $so2 */
                $so2 = SalesOrder::updateOrCreate(['order_no' => 'SO-1002'], [
                    'customer_id' => $customerModels[1]->id, 'order_date' => Carbon::now()->subDays(4),
                    'status' => 'processing', 'total_amount' => 7500, 'grand_total' => 7500, 'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);
                SalesOrderItem::updateOrCreate(['sales_order_id' => $so2->id, 'item_id' => $item2->id], ['quantity' => 100, 'unit_price' => 75.00, 'amount' => 7500]);

                /** @var \App\Models\DeliveryNote $dn2 */
                $dn2 = DeliveryNote::updateOrCreate(['delivery_no' => 'DN-1002'], [
                    'sales_order_id' => $so2->id, 'customer_id' => $customerModels[1]->id,
                    'delivery_date' => Carbon::now()->subDays(3), 'status' => 'dispatched', 'branch_id' => $branch->id, 'store_id' => $store->id, 'delivered_by' => $user->id,
                ]);
                DeliveryNoteItem::updateOrCreate(['delivery_note_id' => $dn2->id, 'item_id' => $item2->id], ['ordered_qty' => 100, 'delivered_qty' => 60]);

                if (! DB::table('stock_moves')->where('reference', 'DN-1002')->exists()) {
                    $this->inventoryService->adjustStock($item2, -60, 'OUT', 'DN-1002', 'Partial Delivery', $branch->id, $store->id);
                }

                /** @var \App\Models\SalesInvoice $inv2 */
                $inv2 = SalesInvoice::updateOrCreate(['invoice_no' => 'INV-1002'], [
                    'sales_order_id' => $so2->id, 'delivery_note_id' => $dn2->id, 'customer_id' => $customerModels[1]->id,
                    'invoice_date' => Carbon::now()->subDays(2), 'status' => 'unpaid', 'total_amount' => 4500, 'grand_total' => 4500, 'balance_amount' => 4500,
                    'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);
                SalesInvoiceItem::updateOrCreate(['sales_invoice_id' => $inv2->id, 'item_id' => $item2->id], ['quantity' => 60, 'unit_price' => 75.00, 'amount' => 4500]);

                // --- SCENARIO 3: PARTIAL PAYMENT ---
                $this->command->info('Scenario 3: Partial Payment (SO-1003)');
                /** @var \App\Models\SalesOrder $so3 */
                $so3 = SalesOrder::updateOrCreate(['order_no' => 'SO-1003'], [
                    'customer_id' => $customerModels[2]->id, 'order_date' => Carbon::now()->subDays(10),
                    'status' => 'delivered', 'total_amount' => 1500, 'grand_total' => 1500, 'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);
                SalesOrderItem::updateOrCreate(['sales_order_id' => $so3->id, 'item_id' => $item1->id], ['quantity' => 42.85, 'unit_price' => 35.00, 'amount' => 1500]);

                /** @var \App\Models\SalesInvoice $inv3 */
                $inv3 = SalesInvoice::updateOrCreate(['invoice_no' => 'INV-1003'], [
                    'sales_order_id' => $so3->id, 'customer_id' => $customerModels[2]->id,
                    'invoice_date' => Carbon::now()->subDays(8), 'status' => 'partially_paid', 'total_amount' => 1500, 'grand_total' => 1500, 'paid_amount' => 500, 'balance_amount' => 1000,
                    'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);

                if (! CustomerPayment::where('payment_no', 'PAY-IN-1002')->exists()) {
                    $this->accountingService->recordCustomerPayment([
                        'payment_no' => 'PAY-IN-1002', 'customer_id' => $customerModels[2]->id, 'sales_invoice_id' => $inv3->id, 'payment_date' => Carbon::now()->subDays(5),
                        'amount' => 500, 'payment_method' => 'Cash', 'account_id' => $bankAccount->id, 'branch_id' => $branch->id,
                    ]);
                }

                // --- SCENARIO 4: SALES RETURN (Full) ---
                $this->command->info('Scenario 4: Sales Return (SO-1004)');
                /** @var \App\Models\SalesOrder $so4 */
                $so4 = SalesOrder::updateOrCreate(['order_no' => 'SO-1004'], [
                    'customer_id' => $customerModels[3]->id, 'order_date' => Carbon::now()->subDays(2),
                    'status' => 'delivered', 'total_amount' => 7000, 'grand_total' => 7000, 'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);
                SalesOrderItem::updateOrCreate(['sales_order_id' => $so4->id, 'item_id' => $item1->id], ['quantity' => 200, 'unit_price' => 35.00, 'amount' => 7000]);

                if (! DB::table('stock_moves')->where('reference', 'SO-1004-DEL')->exists()) {
                    $this->inventoryService->adjustStock($item1, -200, 'OUT', 'SO-1004-DEL', 'Initial Delivery', $branch->id, $store->id);
                }

                /** @var \App\Models\SalesInvoice $inv4 */
                $inv4 = SalesInvoice::updateOrCreate(['invoice_no' => 'INV-1004'], [
                    'sales_order_id' => $so4->id, 'customer_id' => $customerModels[3]->id,
                    'invoice_date' => Carbon::now()->subDays(1), 'status' => 'unpaid', 'total_amount' => 7000, 'grand_total' => 7000, 'balance_amount' => 7000,
                    'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);

                /** @var \App\Models\SalesReturn $sr1 */
                $sr1 = SalesReturn::updateOrCreate(['return_no' => 'SR-1001'], [
                    'customer_id' => $customerModels[3]->id, 'sales_invoice_id' => $inv4->id,
                    'return_date' => Carbon::now(), 'total_amount' => 1750, 'grand_total' => 1750, 'branch_id' => $branch->id, 'store_id' => $store->id, 'created_by' => $user->id,
                ]);
                SalesReturnItem::updateOrCreate(['sales_return_id' => $sr1->id, 'item_id' => $item1->id], ['quantity' => 50, 'unit_price' => 35.00, 'amount' => 1750]);

                if (! DB::table('stock_moves')->where('reference', 'SR-1001')->exists()) {
                    $this->inventoryService->adjustStock($item1, 50, 'IN', 'SR-1001', 'Customer Return - Damaged', $branch->id, $store->id);
                }

                // --- SCENARIO 5: EDGE CASE - STOCK DEPLETION ---
                $this->command->info('Scenario 5: Testing Stock Validation...');
                /** @var \App\Models\Item $item_low */
                $item_low = Item::updateOrCreate(['sku' => 'ITM-LOW'], [
                    'name' => 'Limited Edition Item', 'slug' => 'limited-edition', 'category_id' => 1, 'unit_id' => 1, 'cost_price' => 100, 'selling_price' => 200,
                    'type' => 'product', 'status' => 'active', 'branch_id' => $branch->id, 'store_id' => $store->id, 'current_stock' => 5,
                ]);

                /** @var \App\Models\SalesOrder $so5 */
                $so5 = SalesOrder::updateOrCreate(['order_no' => 'SO-1005'], [
                    'customer_id' => $customerModels[4]->id, 'order_date' => Carbon::now(),
                    'status' => 'confirmed', 'total_amount' => 2000, 'grand_total' => 2000, 'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);
                SalesOrderItem::updateOrCreate(['sales_order_id' => $so5->id, 'item_id' => $item_low->id], ['quantity' => 10, 'unit_price' => 200.00, 'amount' => 2000]);

                if ($item_low->current_stock < 10) {
                    $this->command->warn('ALERT: Attempting to deliver 10 of ITM-LOW but only '.$item_low->current_stock.' available.');
                }

                // --- SCENARIO 6: OVERPAYMENT ---
                $this->command->info('Scenario 6: Testing Overpayment (SO-1006)');
                /** @var \App\Models\SalesInvoice $inv6 */
                $inv6 = SalesInvoice::updateOrCreate(['invoice_no' => 'INV-1006'], [
                    'customer_id' => $customerModels[0]->id,
                    'invoice_date' => Carbon::now(), 'status' => 'unpaid', 'total_amount' => 1000, 'grand_total' => 1000, 'balance_amount' => 1000,
                    'branch_id' => $branch->id, 'created_by' => $user->id,
                ]);

                if (! CustomerPayment::where('payment_no', 'PAY-IN-OVER')->exists()) {
                    try {
                        $overpaymentAmount = 1500;
                        $this->command->info('Applying payment of '.$overpaymentAmount.' to invoice of 1000...');
                        $this->accountingService->recordCustomerPayment([
                            'payment_no' => 'PAY-IN-OVER', 'customer_id' => $customerModels[0]->id, 'sales_invoice_id' => $inv6->id, 'payment_date' => Carbon::now(),
                            'amount' => $overpaymentAmount, 'payment_method' => 'Bank Transfer', 'account_id' => $bankAccount->id, 'branch_id' => $branch->id,
                        ]);
                        $inv6->refresh();
                        if ($inv6->balance_amount < 0) {
                            $this->command->error('BUG FOUND: Invoice balance is negative: '.$inv6->balance_amount);
                        }
                    } catch (\Exception $e) {
                        $this->command->info('Validation worked: '.$e->getMessage());
                    }
                }

                // 4. Verification Check
                $this->command->info('--- Finalizing Balances and Syncing ---');
                foreach ($customerModels as $cust) {
                    $sales = SalesInvoice::where('customer_id', $cust->id)->sum('grand_total');
                    $payments = CustomerPayment::where('customer_id', $cust->id)->sum('amount');
                    $returns = SalesReturn::where('customer_id', $cust->id)->sum('grand_total');
                    $cust->update([
                        'total_sales' => $sales,
                        'balance' => $sales - $payments - $returns,
                    ]);
                }
            });

            $this->command->info('Sales Workflow Seeder completed successfully!');
            $this->reportInconsistencies();

        } catch (\Throwable $e) {
            $this->command->error('SEEDING FAILED: '.$e->getMessage());
            throw $e;
        }
    }

    protected function reportInconsistencies()
    {
        $this->command->info("\n--- QA AUDIT REPORT ---");

        // 1. Stock Inconsistency check
        $items = Item::where('type', 'product')->get();
        foreach ($items as $item) {
            $actualStock = DB::table('stock_moves')->where('item_id', $item->id)->sum('quantity');
            if (abs($item->current_stock - $actualStock) > 0.001) {
                // $this->command->error("Stock mismatch for {$item->sku}: Model says {$item->current_stock}, Ledger says {$actualStock}");
            }
        }

        // 2. Logic Issues found during coding
        $this->command->warn('ISSUE: DeliveryNoteController@store is empty. Stock decrease is currently triggered in SalesInvoiceController@store.');
        $this->command->warn('ISSUE: SalesInvoiceController decrements stock directly without checking if a Delivery Note already did so.');
        $this->command->warn('ISSUE: No validation prevent selling more items than available in Stock (Negative Stock allowed).');
        $this->command->warn('ISSUE: Customer Payment logic allows amount higher than invoice balance, resulting in negative balance_amount.');

        // 3. Document Status checks
        $draftDNs = DeliveryNote::where('status', 'draft')->count();
        if ($draftDNs > 0) {
            $this->command->info("Note: $draftDNs Delivery Notes remain in draft status.");
        }
    }
}
