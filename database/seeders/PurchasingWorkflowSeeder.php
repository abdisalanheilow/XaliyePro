<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Category;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Item;
use App\Models\PurchaseBill;
use App\Models\PurchaseBillItem;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Store;
use App\Models\StoreItemStock;
use App\Models\Unit;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Services\AccountingService;
use App\Services\InventoryService;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PurchasingWorkflowSeeder extends Seeder
{
    protected $inventoryService;

    protected $accountingService;

    public function run(): void
    {
        $this->inventoryService = app(InventoryService::class);
        $this->accountingService = app(AccountingService::class);

        $user = User::first() ?? User::factory()->create();
        Auth::login($user);

        try {
            DB::transaction(function () use ($user) {
                // 1. Prerequisites
                $this->command->info('Creating Prerequisite Categories...');
                /** @var \App\Models\Category $catIndustrial */
                $catIndustrial = Category::firstOrCreate(
                    ['name' => 'Industrial Supplies'],
                    ['slug' => 'industrial-supplies', 'type' => 'product', 'status' => 'active']
                );
                /** @var \App\Models\Category $catRawMaterials */
                $catRawMaterials = Category::firstOrCreate(
                    ['name' => 'Raw Materials'],
                    ['slug' => 'raw-materials', 'type' => 'product', 'status' => 'active']
                );

                $this->command->info('Creating Prerequisite Units...');
                /** @var \App\Models\Unit $unitPcs */
                $unitPcs = Unit::firstOrCreate(
                    ['short_name' => 'pcs'],
                    ['name' => 'Pieces', 'status' => 'active']
                );
                /** @var \App\Models\Unit $unitKg */
                $unitKg = Unit::firstOrCreate(
                    ['short_name' => 'kg'],
                    ['name' => 'Kilogram', 'status' => 'active']
                );

                $this->command->info('Creating Prerequisite Branch...');
                /** @var \App\Models\Branch $branchMain */
                $branchMain = Branch::firstOrCreate(['name' => 'Main Branch'], [
                    'status' => 'active',
                    'code' => 'BR-HO',
                    'address' => 'Head Office, Business District',
                    'city' => 'Metropolis',
                    'state' => 'Central State',
                    'zip_code' => '10001',
                    'phone' => '555-0100',
                ]);

                $this->command->info('Creating Prerequisite Stores...');
                /** @var \App\Models\Store $storeCentral */
                $storeCentral = Store::firstOrCreate(['name' => 'Central Warehouse'], [
                    'branch_id' => $branchMain->id,
                    'code' => 'WH-CEN',
                    'status' => 'active',
                    'address' => 'Logistics Hub, Industrial Zone',
                    'phone' => '555-0101',
                    'manager_name' => 'John Warehouse',
                ]);

                /** @var \App\Models\Store $storeOffice */
                $storeOffice = Store::firstOrCreate(['name' => 'Office Warehouse'], [
                    'branch_id' => $branchMain->id,
                    'code' => 'WH-OFF',
                    'status' => 'active',
                    'address' => 'Office Building, Floor 1',
                    'phone' => '555-0102',
                    'manager_name' => 'Sarah Office',
                ]);

                $this->command->info('Creating Prerequisite Account...');
                /** @var \App\Models\Account $cashAccount */
                $cashAccount = Account::firstOrCreate(
                    ['code' => '1000'],
                    ['name' => 'Cash in Hand', 'type' => 'asset', 'status' => 'active']
                );

                // 2. Vendors (5)
                $this->command->info('Creating 5 Vendors...');
                $vendors = [
                    ['vendor_id' => 'V001', 'name' => 'Global Steels', 'email' => 'sales@globalsteels.com', 'payment_terms' => 'net_30', 'address' => '123 Industrial Way', 'city' => 'Metropolis', 'country' => 'USA', 'status' => 'active', 'type' => 'company'],
                    ['vendor_id' => 'V002', 'name' => 'Precision Tools Inc', 'email' => 'orders@precision.com', 'payment_terms' => 'due_on_receipt', 'address' => '456 Tech Park', 'city' => 'Tech City', 'country' => 'USA', 'status' => 'active', 'type' => 'company'],
                    ['vendor_id' => 'V003', 'name' => 'Eco Plastics', 'email' => 'supply@ecoplastics.com', 'payment_terms' => 'net_15', 'address' => '789 Green Rd', 'city' => 'Eco Town', 'country' => 'USA', 'status' => 'active', 'type' => 'company'],
                    ['vendor_id' => 'V004', 'name' => 'Fasteners Direct', 'email' => 'info@fasteners.com', 'payment_terms' => 'net_30', 'address' => '101 Bolt St', 'city' => 'Metropolis', 'country' => 'USA', 'status' => 'active', 'type' => 'company'],
                    ['vendor_id' => 'V005', 'name' => 'Paint & Coatings Ltd', 'email' => 'support@paintco.com', 'payment_terms' => 'net_15', 'address' => '202 Color Ave', 'city' => 'Art City', 'country' => 'USA', 'status' => 'active', 'type' => 'company'],
                ];
                $vendorModels = [];
                /** @var \App\Models\Vendor[] $vendorModels */
                foreach ($vendors as $v) {
                    $vendorModels[] = Vendor::firstOrCreate(['vendor_id' => $v['vendor_id']], $v);
                }

                // 3. Items (10)
                $this->command->info('Creating 10 Items...');
                $items = [
                    ['sku' => 'ITM-001', 'name' => 'Steel Rod 10mm', 'slug' => 'steel-rod-10mm', 'category_id' => $catRawMaterials->id, 'unit_id' => $unitPcs->id, 'cost_price' => 25.00, 'selling_price' => 35.00],
                    ['sku' => 'ITM-002', 'name' => 'Copper Sheet 2mm', 'slug' => 'copper-sheet-2mm', 'category_id' => $catRawMaterials->id, 'unit_id' => $unitKg->id, 'cost_price' => 120.00, 'selling_price' => 180.00],
                    ['sku' => 'ITM-003', 'name' => 'Drill Bit Set', 'slug' => 'drill-bit-set', 'category_id' => $catIndustrial->id, 'unit_id' => $unitPcs->id, 'cost_price' => 45.00, 'selling_price' => 75.00],
                    ['sku' => 'ITM-004', 'name' => 'Safety Goggles', 'slug' => 'safety-goggles', 'category_id' => $catIndustrial->id, 'unit_id' => $unitPcs->id, 'cost_price' => 5.00, 'selling_price' => 12.00],
                    ['sku' => 'ITM-005', 'name' => 'Industrial Glue 5L', 'slug' => 'industrial-glue-5l', 'category_id' => $catIndustrial->id, 'unit_id' => $unitPcs->id, 'cost_price' => 30.00, 'selling_price' => 50.00],
                    ['sku' => 'ITM-006', 'name' => 'M8 Bolts (Box 500)', 'slug' => 'm8-bolts-box-500', 'category_id' => $catIndustrial->id, 'unit_id' => $unitPcs->id, 'cost_price' => 15.00, 'selling_price' => 25.00],
                    ['sku' => 'ITM-007', 'name' => 'M10 Nuts (Box 500)', 'slug' => 'm10-nuts-box-500', 'category_id' => $catIndustrial->id, 'unit_id' => $unitPcs->id, 'cost_price' => 12.00, 'selling_price' => 20.00],
                    ['sku' => 'ITM-008', 'name' => 'Aluminum Profile 2m', 'slug' => 'aluminum-profile-2m', 'category_id' => $catRawMaterials->id, 'unit_id' => $unitPcs->id, 'cost_price' => 40.00, 'selling_price' => 60.00],
                    ['sku' => 'ITM-009', 'name' => 'Spray Paint White 400ml', 'slug' => 'spray-paint-white-400ml', 'category_id' => $catIndustrial->id, 'unit_id' => $unitPcs->id, 'cost_price' => 8.00, 'selling_price' => 15.00],
                    ['sku' => 'ITM-010', 'name' => 'Cleaning Solvent 20L', 'slug' => 'cleaning-solvent-20l', 'category_id' => $catIndustrial->id, 'unit_id' => $unitPcs->id, 'cost_price' => 55.00, 'selling_price' => 90.00],
                ];
                $itemModels = [];
                /** @var \App\Models\Item[] $itemModels */
                foreach ($items as $i) {
                    $itemModels[] = Item::firstOrCreate(['sku' => $i['sku']], array_merge($i, [
                        'type' => 'product', 'status' => 'active', 'track_inventory' => true, 'current_stock' => 0, 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id,
                    ]));
                }

                // 4. Workflow 1: FULL FLOW (PO -> GRN -> Bill -> Full Payment)
                $this->command->info('Simulating Workflow 1: Standard Full Cycle...');
                /** @var \App\Models\PurchaseOrder $po1 */
                $po1 = PurchaseOrder::firstOrCreate(['order_no' => 'PO-0001'], [
                    'vendor_id' => $vendorModels[0]->id, 'order_date' => Carbon::now()->subDays(10),
                    'expected_date' => Carbon::now()->subDays(8),
                    'total_amount' => 12500, 'grand_total' => 12500, 'status' => 'received', 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                    'payment_status' => 'paid', 'payment_terms' => 'net_30',
                ]);
                PurchaseOrderItem::updateOrCreate(['purchase_order_id' => $po1->id, 'item_id' => $itemModels[0]->id], ['quantity' => 500, 'unit_price' => 25.00, 'amount' => 12500]);

                /** @var \App\Models\GoodsReceipt $grn1 */
                $grn1 = GoodsReceipt::firstOrCreate(['receipt_no' => 'GRN-0001'], [
                    'purchase_order_id' => $po1->id, 'vendor_id' => $vendorModels[0]->id, 'received_date' => Carbon::now()->subDays(8),
                    'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'received_by' => $user->id, 'status' => 'received',
                ]);
                GoodsReceiptItem::updateOrCreate(['goods_receipt_id' => $grn1->id, 'item_id' => $itemModels[0]->id], ['ordered_qty' => 500, 'received_qty' => 500]);

                if (! DB::table('stock_moves')->where('reference', 'GRN-0001')->exists()) {
                    $this->inventoryService->adjustStock($itemModels[0], 500, 'IN', 'GRN-0001', 'Standard Full Cycle', $branchMain->id, $storeCentral->id);
                }

                /** @var \App\Models\PurchaseBill $bill1 */
                $bill1 = PurchaseBill::firstOrCreate(['bill_no' => 'BILL-1001'], [
                    'vendor_id' => $vendorModels[0]->id, 'bill_date' => Carbon::now()->subDays(8), 'due_date' => Carbon::now()->addDays(22),
                    'total_amount' => 12500, 'grand_total' => 12500, 'paid_amount' => 12500, 'balance_amount' => 0, 'status' => 'paid',
                    'purchase_order_id' => $po1->id, 'goods_receipt_id' => $grn1->id, 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                    'payment_terms' => 'net_30',
                ]);
                PurchaseBillItem::updateOrCreate(['purchase_bill_id' => $bill1->id, 'item_id' => $itemModels[0]->id], ['quantity' => 500, 'unit_price' => 25.00, 'amount' => 12500]);

                if (! VendorPayment::where('payment_no', 'VPMT-1001')->exists()) {
                    $this->accountingService->recordVendorPayment([
                        'payment_no' => 'VPMT-1001', 'vendor_id' => $vendorModels[0]->id, 'purchase_bill_id' => $bill1->id, 'payment_date' => Carbon::now()->subDays(7),
                        'amount' => 12500, 'payment_method' => 'Bank Transfer', 'account_id' => $cashAccount->id, 'branch_id' => $branchMain->id,
                    ]);
                }

                // 5. Workflow 2: PARTIAL DELIVERY (PO -> Partial GRN -> Bill for received)
                $this->command->info('Simulating Workflow 2: Partial Delivery...');
                // FIX: status must be one of ['draft', 'pending', 'received', 'cancelled']
                /** @var \App\Models\PurchaseOrder $po2 */
                $po2 = PurchaseOrder::firstOrCreate(['order_no' => 'PO-0002'], [
                    'vendor_id' => $vendorModels[1]->id, 'order_date' => Carbon::now()->subDays(5),
                    'expected_date' => Carbon::now()->subDays(3),
                    'total_amount' => 4500, 'grand_total' => 4500, 'status' => 'received', 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                    'payment_status' => 'unpaid', 'payment_terms' => 'due_on_receipt',
                ]);
                PurchaseOrderItem::updateOrCreate(['purchase_order_id' => $po2->id, 'item_id' => $itemModels[2]->id], ['quantity' => 100, 'unit_price' => 45.00, 'amount' => 4500]);

                /** @var \App\Models\GoodsReceipt $grn2 */
                $grn2 = GoodsReceipt::firstOrCreate(['receipt_no' => 'GRN-0002'], [
                    'purchase_order_id' => $po2->id, 'vendor_id' => $vendorModels[1]->id, 'received_date' => Carbon::now()->subDays(3),
                    'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'received_by' => $user->id, 'status' => 'received',
                ]);
                GoodsReceiptItem::updateOrCreate(['goods_receipt_id' => $grn2->id, 'item_id' => $itemModels[2]->id], ['ordered_qty' => 100, 'received_qty' => 60]);

                if (! DB::table('stock_moves')->where('reference', 'GRN-0002')->exists()) {
                    $this->inventoryService->adjustStock($itemModels[2], 60, 'IN', 'GRN-0002', 'Partial Delivery Step 1', $branchMain->id, $storeCentral->id);
                }

                /** @var \App\Models\PurchaseBill $bill2 */
                $bill2 = PurchaseBill::firstOrCreate(['bill_no' => 'BILL-1002'], [
                    'vendor_id' => $vendorModels[1]->id, 'bill_date' => Carbon::now()->subDays(2), 'due_date' => Carbon::now()->subDays(2),
                    'total_amount' => 2700, 'grand_total' => 2700, 'paid_amount' => 0, 'balance_amount' => 2700, 'status' => 'unpaid',
                    'purchase_order_id' => $po2->id, 'goods_receipt_id' => $grn2->id, 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                    'payment_terms' => 'due_on_receipt',
                ]);
                PurchaseBillItem::updateOrCreate(['purchase_bill_id' => $bill2->id, 'item_id' => $itemModels[2]->id], ['quantity' => 60, 'unit_price' => 45.00, 'amount' => 2700]);

                // 6. Workflow 3: PARTIAL PAYMENT (PO -> GRN -> Bill -> Multiple Payments)
                $this->command->info('Simulating Workflow 3: Multi-stage Payment...');
                /** @var \App\Models\PurchaseOrder $po3 */
                $po3 = PurchaseOrder::firstOrCreate(['order_no' => 'PO-0003'], [
                    'vendor_id' => $vendorModels[2]->id, 'order_date' => Carbon::now()->subDays(15),
                    'expected_date' => Carbon::now()->subDays(12),
                    'total_amount' => 12000, 'grand_total' => 12000, 'status' => 'received', 'branch_id' => $branchMain->id, 'store_id' => $storeOffice->id, 'created_by' => $user->id,
                    'payment_status' => 'partially_paid', 'payment_terms' => 'net_15',
                ]);
                PurchaseOrderItem::updateOrCreate(['purchase_order_id' => $po3->id, 'item_id' => $itemModels[1]->id], ['quantity' => 100, 'unit_price' => 120.00, 'amount' => 12000]);

                /** @var \App\Models\GoodsReceipt $grn4 */
                $grn4 = GoodsReceipt::firstOrCreate(['receipt_no' => 'GRN-0004'], [
                    'purchase_order_id' => $po3->id, 'vendor_id' => $vendorModels[2]->id, 'received_date' => Carbon::now()->subDays(12),
                    'branch_id' => $branchMain->id, 'store_id' => $storeOffice->id, 'received_by' => $user->id, 'status' => 'received',
                ]);
                GoodsReceiptItem::updateOrCreate(['goods_receipt_id' => $grn4->id, 'item_id' => $itemModels[1]->id], ['ordered_qty' => 100, 'received_qty' => 100]);

                if (! DB::table('stock_moves')->where('reference', 'GRN-0004')->exists()) {
                    $this->inventoryService->adjustStock($itemModels[1], 100, 'IN', 'GRN-0004', 'Direct to Office Store', $branchMain->id, $storeOffice->id);
                }

                /** @var \App\Models\PurchaseBill $bill3 */
                $bill3 = PurchaseBill::firstOrCreate(['bill_no' => 'BILL-1003'], [
                    'vendor_id' => $vendorModels[2]->id, 'bill_date' => Carbon::now()->subDays(10), 'due_date' => Carbon::now()->addDays(5),
                    'total_amount' => 12000, 'grand_total' => 12000, 'paid_amount' => 5000, 'balance_amount' => 7000, 'status' => 'partially_paid',
                    'purchase_order_id' => $po3->id, 'goods_receipt_id' => $grn4->id, 'branch_id' => $branchMain->id, 'store_id' => $storeOffice->id, 'created_by' => $user->id,
                    'payment_terms' => 'net_15',
                ]);
                PurchaseBillItem::updateOrCreate(['purchase_bill_id' => $bill3->id, 'item_id' => $itemModels[1]->id], ['quantity' => 100, 'unit_price' => 120.00, 'amount' => 12000]);

                if (! VendorPayment::where('payment_no', 'VPMT-1002')->exists()) {
                    $this->accountingService->recordVendorPayment([
                        'payment_no' => 'VPMT-1002', 'vendor_id' => $vendorModels[2]->id, 'purchase_bill_id' => $bill3->id, 'payment_date' => Carbon::now()->subDays(5),
                        'amount' => 5000, 'payment_method' => 'Cash', 'account_id' => $cashAccount->id, 'branch_id' => $branchMain->id,
                    ]);
                }

                // 7. Workflow 4: RETURN FLOW (PO -> GRN -> Bill -> Partial Return)
                $this->command->info('Simulating Workflow 4: Purchase Return Cycle...');
                /** @var \App\Models\PurchaseOrder $po4 */
                $po4 = PurchaseOrder::firstOrCreate(['order_no' => 'PO-0004'], [
                    'vendor_id' => $vendorModels[3]->id, 'order_date' => Carbon::now()->subDays(4),
                    'expected_date' => Carbon::now()->subDays(2),
                    'total_amount' => 750, 'grand_total' => 750, 'status' => 'received', 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                    'payment_status' => 'paid', 'payment_terms' => 'net_30',
                ]);
                PurchaseOrderItem::updateOrCreate(['purchase_order_id' => $po4->id, 'item_id' => $itemModels[5]->id], ['quantity' => 50, 'unit_price' => 15.00, 'amount' => 750]);

                /** @var \App\Models\GoodsReceipt $grn5 */
                $grn5 = GoodsReceipt::firstOrCreate(['receipt_no' => 'GRN-0005'], [
                    'purchase_order_id' => $po4->id, 'vendor_id' => $vendorModels[3]->id, 'received_date' => Carbon::now()->subDays(2),
                    'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'received_by' => $user->id, 'status' => 'received',
                ]);
                GoodsReceiptItem::updateOrCreate(['goods_receipt_id' => $grn5->id, 'item_id' => $itemModels[5]->id], ['ordered_qty' => 50, 'received_qty' => 50]);

                if (! DB::table('stock_moves')->where('reference', 'GRN-0005')->exists()) {
                    $this->inventoryService->adjustStock($itemModels[5], 50, 'IN', 'GRN-0005', 'Initial stock before return', $branchMain->id, $storeCentral->id);
                }

                /** @var \App\Models\PurchaseBill $bill4 */
                $bill4 = PurchaseBill::firstOrCreate(['bill_no' => 'BILL-1004'], [
                    'vendor_id' => $vendorModels[3]->id, 'bill_date' => Carbon::now()->subDays(1), 'due_date' => Carbon::now()->addDays(29),
                    'total_amount' => 750, 'grand_total' => 750, 'paid_amount' => 750, 'balance_amount' => 0, 'status' => 'paid',
                    'purchase_order_id' => $po4->id, 'goods_receipt_id' => $grn5->id, 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                    'payment_terms' => 'net_30',
                ]);
                PurchaseBillItem::updateOrCreate(['purchase_bill_id' => $bill4->id, 'item_id' => $itemModels[5]->id], ['quantity' => 50, 'unit_price' => 15.00, 'amount' => 750]);

                /** @var \App\Models\PurchaseReturn $return1 */
                $return1 = PurchaseReturn::firstOrCreate(['return_no' => 'PR-0001'], [
                    'purchase_bill_id' => $bill4->id, 'vendor_id' => $vendorModels[3]->id, 'return_date' => Carbon::now(),
                    'total_amount' => 150, 'grand_total' => 150, 'status' => 'completed', 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                ]);
                PurchaseReturnItem::updateOrCreate(['purchase_return_id' => $return1->id, 'item_id' => $itemModels[5]->id], ['quantity' => 10, 'unit_price' => 15.00, 'amount' => 150]);

                if (! DB::table('stock_moves')->where('reference', 'PR-0001')->exists()) {
                    $this->inventoryService->adjustStock($itemModels[5], -10, 'OUT', 'PR-0001', 'Damaged goods return', $branchMain->id, $storeCentral->id);
                }

                // 8. Workflow 5: OVERPAYMENT EDGE CASE (Multiple items)
                $this->command->info('Simulating Workflow 5: Overpayment / Multi-item PO...');
                /** @var \App\Models\PurchaseOrder $po5 */
                $po5 = PurchaseOrder::firstOrCreate(['order_no' => 'PO-0005'], [
                    'vendor_id' => $vendorModels[4]->id, 'order_date' => Carbon::now()->subDays(2),
                    'expected_date' => Carbon::now(),
                    'total_amount' => 1400, 'grand_total' => 1400, 'status' => 'draft', 'branch_id' => $branchMain->id, 'store_id' => $storeCentral->id, 'created_by' => $user->id,
                    'payment_status' => 'unpaid', 'payment_terms' => 'net_15',
                ]);
                PurchaseOrderItem::updateOrCreate(['purchase_order_id' => $po5->id, 'item_id' => $itemModels[8]->id], ['quantity' => 100, 'unit_price' => 8.00, 'amount' => 800]);
                PurchaseOrderItem::updateOrCreate(['purchase_order_id' => $po5->id, 'item_id' => $itemModels[4]->id], ['quantity' => 20, 'unit_price' => 30.00, 'amount' => 600]);

                // Final state sync simulation (Filling for missing behavior)
                $this->command->info('Syncing simulated global stock in Item table...');
                foreach ($itemModels as $itm) {
                    $totalStock = DB::table('stock_moves')->where('item_id', $itm->id)->sum('quantity');
                    $itm->update(['current_stock' => $totalStock]);

                    foreach ([$storeCentral->id, $storeOffice->id] as $sid) {
                        $storeStock = DB::table('stock_moves')->where('item_id', $itm->id)->where('store_id', $sid)->sum('quantity');
                        if ($storeStock != 0) {
                            StoreItemStock::updateOrCreate(
                                ['store_id' => $sid, 'item_id' => $itm->id],
                                ['current_stock' => $storeStock]
                            );
                        }
                    }
                }

                $this->command->info('Syncing simulated Vendor balances...');
                foreach ($vendorModels as $v) {
                    $purchases = DB::table('purchase_bills')->where('vendor_id', $v->id)->sum('grand_total');
                    $payments = DB::table('vendor_payments')->where('vendor_id', $v->id)->sum('amount');
                    $v->update([
                        'total_purchases' => $purchases,
                        'balance' => $purchases - $payments,
                    ]);
                }
            });
        } catch (\Throwable $e) {
            $this->command->error('SEEDING FAILED!');
            $this->command->error('Error Class: '.get_class($e));
            $this->command->error('Error Message: '.$e->getMessage());
            $this->command->error('File: '.$e->getFile().' (Line: '.$e->getLine().')');
            if ($e instanceof QueryException) {
                $this->command->info('SQL: '.$e->getSql());
                $this->command->info('Bindings: '.json_encode($e->getBindings()));
            }
            throw $e;
        }

        $this->command->info('Purchasing Workflow Seeder completed successfully!');
    }
}
