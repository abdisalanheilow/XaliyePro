<?php

namespace App\Services;

use App\Models\Item;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMove;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\StoreItemStock;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    /**
     * Create a new stock adjustment.
     */
    public function createAdjustment(array $data): StockAdjustment
    {
        return DB::transaction(function () use ($data) {
            /** @var \App\Models\StockAdjustment $adjustment */
            $adjustment = StockAdjustment::create([
                'adjustment_no' => $data['adjustment_no'],
                'adjustment_date' => $data['adjustment_date'],
                'store_id' => $data['store_id'],
                'reason' => $data['reason'],
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $itemData) {
                $item = Item::findOrFail($itemData['item_id']);

                StockAdjustmentItem::create([
                    'stock_adjustment_id' => $adjustment->id,
                    'item_id' => $itemData['item_id'],
                    'quantity_before' => $item->current_stock,
                    'quantity_after' => $itemData['quantity'],
                    'adjustment_quantity' => $itemData['quantity'] - $item->current_stock,
                    'unit_cost' => $item->cost_price ?? 0,
                ]);
            }

            return $adjustment;
        });
    }

    /**
     * Finalize and post an adjustment to stock.
     */
    public function finalizeAdjustment(StockAdjustment $adjustment): void
    {
        if ($adjustment->status !== 'draft') {
            throw new \Exception('Only draft adjustments can be finalized.');
        }

        DB::transaction(function () use ($adjustment) {
            $adjustment->load('items.item');

            foreach ($adjustment->items as $adjItem) {
                $this->adjustStock(
                    $adjItem->item,
                    $adjItem->adjustment_quantity,
                    'ADJUST',
                    $adjustment->adjustment_no,
                    $adjustment->reason,
                    $adjItem->item->branch_id,
                    $adjustment->store_id
                );
            }

            $adjustment->update(['status' => 'adjusted']);

            // Automated Accounting Posting
            app(AccountingService::class)->postStockAdjustment($adjustment);
        });
    }

    /**
     * Core method to adjust stock and record movement.
     */
    public function adjustStock(Item $item, float $quantity, string $type, string $reference, ?string $remarks = null, ?int $branchId = null, ?int $storeId = null): void
    {
        if ($item->type !== 'product') {
            return;
        }

        $targetStoreId = $storeId ?? $item->store_id;

        if (! $targetStoreId) {
            throw new \Exception('A warehouse (store_id) is required for inventory adjustments.');
        }

        // Get or Create per-warehouse stock record
        /** @var \App\Models\StoreItemStock $storeStock */
        $storeStock = StoreItemStock::firstOrCreate(
            ['store_id' => $targetStoreId, 'item_id' => $item->id],
            ['current_stock' => 0]
        );

        // Prevent negative stock for outgoing movements (if negative stock is not allowed)
        if ($quantity < 0 && ($storeStock->current_stock + $quantity) < 0) {
            throw new \Exception("Insufficient stock for item: {$item->name} in warehouse. Available: {$storeStock->current_stock}");
        }

        // Update Store Item Stock
        $storeStock->increment('current_stock', $quantity);

        // Update Item Global Current Stock atomically
        $item->increment('current_stock', $quantity);

        // Record the Movement
        StockMove::create([
            'item_id' => $item->id,
            'type' => $type,
            'quantity' => $quantity,
            'unit_cost' => $item->cost_price ?? 0,
            'total_cost' => $quantity * ($item->cost_price ?? 0),
            'reference' => $reference,
            'remarks' => $remarks,
            'branch_id' => $branchId ?? $item->branch_id,
            'store_id' => $targetStoreId,
            'created_by' => Auth::id(),
        ]);
    }

    /**
     * Create a new stock transfer.
     */
    public function createTransfer(array $data): StockTransfer
    {
        return DB::transaction(function () use ($data) {
            /** @var \App\Models\StockTransfer $transfer */
            $transfer = StockTransfer::create([
                'transfer_no' => $data['transfer_no'],
                'transfer_date' => $data['transfer_date'],
                'from_store_id' => $data['from_store_id'],
                'to_store_id' => $data['to_store_id'],
                'notes' => $data['notes'] ?? null,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            foreach ($data['items'] as $itemData) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                ]);
            }

            return $transfer;
        });
    }

    /**
     * Finalize and post a stock transfer.
     */
    public function finalizeTransfer(StockTransfer $transfer): void
    {
        if ($transfer->status !== 'draft') {
            throw new \Exception('Only draft transfers can be finalized.');
        }

        DB::transaction(function () use ($transfer) {
            $transfer->load(['items.item', 'fromStore', 'toStore']);

            foreach ($transfer->items as $trfItem) {
                $item = $trfItem->item;
                $quantity = $trfItem->quantity;

                // Source Store (OUT)
                $this->adjustStock(
                    $item,
                    -$quantity,
                    'TRANSFER',
                    $transfer->transfer_no,
                    'Transfer to '.$transfer->toStore->name,
                    $item->branch_id,
                    $transfer->from_store_id
                );

                // Destination Store (IN)
                $this->adjustStock(
                    $item,
                    $quantity,
                    'TRANSFER',
                    $transfer->transfer_no,
                    'Transfer from '.$transfer->fromStore->name,
                    $item->branch_id,
                    $transfer->to_store_id
                );
            }

            $transfer->update(['status' => 'transferred']);
        });
    }

    /**
     * Generate a safe unique reference number.
     */
    public function generateReference(string $prefix, string $table = 'stock_adjustments'): string
    {
        $lastId = DB::table($table)->max('id') ?? 0;

        return $prefix.'-'.str_pad($lastId + 1, 6, '0', STR_PAD_LEFT);
    }
}
