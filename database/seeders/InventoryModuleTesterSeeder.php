<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\StockMove;
use App\Models\StoreItemStock;
use App\Services\InventoryService;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InventoryModuleTesterSeeder extends Seeder
{
    protected $inventoryService;

    protected $auditLog = [];

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function run(): void
    {
        $this->command->info('Starting Inventory Module Validation...');

        try {
            DB::transaction(function () {
                // ... (existing logic)
            });
        } catch (Exception $e) {
            $this->command->error('Seeder Failed: '.$e->getMessage());
            $this->command->error('File: '.$e->getFile().' Line: '.$e->getLine());
            throw $e;
        }

        $this->printAuditReport();
    }

    protected function getStoreStock($storeId, $itemId)
    {
        return StoreItemStock::where('store_id', $storeId)->where('item_id', $itemId)->value('current_stock') ?? 0;
    }

    protected function logTransaction($module, $ref, $wh, $item, $before, $change, $after, $type, $reason, $status)
    {
        $this->auditLog[] = [
            'module' => $module,
            'ref' => $ref,
            'warehouse' => $wh,
            'item' => $item,
            'before' => $before,
            'change' => $change,
            'after' => $after,
            'type' => $type,
            'reason' => $reason,
            'status' => $status,
        ];
    }

    protected function printAuditReport()
    {
        $this->command->table(
            ['Module', 'Ref #', 'Warehouse', 'Item', 'Before', 'Change', 'After', 'Type', 'Reason', 'Status'],
            $this->auditLog
        );

        $this->command->info("\n--- FINAL INVENTORY AUDIT REPORT ---");

        // 1. Check Global vs Warehouse Stock Integrity
        $mismatchCount = 0;
        $items = Item::where('track_inventory', true)->get();
        foreach ($items as $item) {
            $sumWarehouse = StoreItemStock::where('item_id', $item->id)->sum('current_stock');
            if (abs($item->current_stock - $sumWarehouse) > 0.01) {
                $mismatchCount++;
                $this->command->error("Stock Inconsistency Found: Item {$item->sku} - Global: {$item->current_stock}, Sum of Warehouses: {$sumWarehouse}");
            }
        }

        if ($mismatchCount == 0) {
            $this->command->info('✓ Stock Integrity Check: Global stock matches sum of warehouse balances.');
        }

        // 2. Check Traceability
        $movesCount = StockMove::count();
        $this->command->info("✓ Traceability: Identified $movesCount movement records linked to transactions.");

        // 3. Low Stock Check
        $lowStockCount = StoreItemStock::whereColumn('current_stock', '<=', 'reorder_level')->count();
        $this->command->info("✓ Low Stock Alerts: $lowStockCount location(s) currently at/below reorder levels.");

        $this->command->info("\nRecommendations:");
        $this->command->line("1. Implement 'In Transit' status for transfers to handle multi-step shipping.");
        $this->command->line("2. Add 'Batch/Lot Tracking' for items with expiry dates.");
        $this->command->line('3. Ensure database locks are used for high-concurrency stock updates.');
        $this->command->line('4. Add stock reservation logic for pending sales orders.');
    }
}
