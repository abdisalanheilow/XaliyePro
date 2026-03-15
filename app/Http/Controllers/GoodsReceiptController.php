<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreGoodsReceiptRequest;
use App\Models\Branch;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\Store;
use App\Models\Vendor;
use App\Services\InventoryService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodsReceiptController extends Controller
{
    public function index(Request $request): View
    {
        $query = GoodsReceipt::with(['vendor', 'order', 'receiver']);

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('receipt_no', 'like', "%{$search}%")
                    ->orWhere('delivery_challan_no', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function (Builder $vq) use ($search) {
                        $vq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $receipts = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.purchases.receipts.index', compact('receipts'));
    }

    public function create(): View
    {
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('type', 'product')->where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->get();
        $stores = Store::where('status', 'active')->get();

        /** @var \App\Models\GoodsReceipt $lastReceipt */
        $lastReceipt = GoodsReceipt::orderBy('id', 'desc')->first();
        $nextId = $lastReceipt ? $lastReceipt->id + 1 : 1;
        $receiptNo = 'GRN-'.date('Y').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.purchases.receipts.create', compact('vendors', 'items', 'branches', 'stores', 'receiptNo'));
    }

    public function createFromOrder($order_id): View
    {
        $order = PurchaseOrder::with(['vendor', 'items.item'])->findOrFail($order_id);

        /** @var \App\Models\GoodsReceipt $lastReceipt */
        $lastReceipt = GoodsReceipt::orderBy('id', 'desc')->first();
        $nextId = $lastReceipt ? $lastReceipt->id + 1 : 1;
        $receiptNo = 'GRN-'.date('Y').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.purchases.receipts.create_from_order', compact('order', 'receiptNo'));
    }

    public function store(StoreGoodsReceiptRequest $request): RedirectResponse
    {

        try {
            return DB::transaction(function () use ($request) {
                /** @var \App\Models\GoodsReceipt $receipt */
                $receipt = GoodsReceipt::create([
                    'receipt_no' => $request->receipt_no,
                    'purchase_order_id' => $request->purchase_order_id,
                    'vendor_id' => $request->vendor_id,
                    'received_date' => $request->received_date,
                    'delivery_challan_no' => $request->delivery_challan_no,
                    'received_by' => $request->received_by,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                    'type' => 'product',
                    'status' => 'received',
                    'notes' => $request->notes,
                ]);

                // Fetch vendor name once (prevent N+1 query inside loop)
                $vendorName = $request->vendor_id ? Vendor::find($request->vendor_id)?->name ?? '' : '';
                $inventoryService = app(InventoryService::class);

                foreach ($request->items as $item) {
                    GoodsReceiptItem::create([
                        'goods_receipt_id' => $receipt->id,
                        'item_id' => $item['item_id'],
                        'ordered_qty' => $item['ordered_qty'] ?? 0,
                        'received_qty' => $item['received_qty'],
                        'rejected_qty' => $item['rejected_qty'] ?? 0,
                        'quality_status' => $item['quality_status'] ?? 'passed',
                    ]);

                    // Update Stock via InventoryService
                    $stockItem = Item::find($item['item_id']);
                    if ($stockItem && $stockItem->type == 'product') {
                        $receivedStock = ($item['received_qty'] - ($item['rejected_qty'] ?? 0));
                        $inventoryService->adjustStock(
                            $stockItem,
                            $receivedStock,
                            'IN',
                            'GRN: '.$receipt->receipt_no,
                            'Received from Vendor '.$vendorName,
                            $receipt->branch_id,
                            $receipt->store_id
                        );
                    }
                }

                // If created from PO, update PO status
                if ($request->purchase_order_id) {
                    $order = PurchaseOrder::find($request->purchase_order_id);
                    $order->update(['status' => 'received']); // Simple logic for now, could be 'partially_received'
                }

                return redirect()->route('purchases.receipts.index')->with([
                    'message' => 'Goods Receipt Note (GRN) created and stock updated.',
                    'alert-type' => 'success',
                ]);
            });
        } catch (Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function show($id): View
    {
        $receipt = GoodsReceipt::with(['vendor', 'order', 'items.item', 'receiver', 'branch', 'store'])->findOrFail($id);

        return view('admin.purchases.receipts.show', compact('receipt'));
    }

    public function edit($id): View
    {
        $receipt = GoodsReceipt::with('items')->findOrFail($id);
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('type', 'product')->where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->get();
        $stores = Store::where('status', 'active')->get();

        return view('admin.purchases.receipts.edit', compact('receipt', 'vendors', 'items', 'branches', 'stores'));
    }

    public function update(StoreGoodsReceiptRequest $request, $id): RedirectResponse
    {
        $receipt = GoodsReceipt::findOrFail($id);

        try {
            return DB::transaction(function () use ($request, $receipt) {
                // Reverse old stock impacts
                $inventoryService = app(InventoryService::class);
                foreach ($receipt->items as $oldItem) {
                    $stockItem = Item::find($oldItem->item_id);
                    if ($stockItem && $stockItem->type == 'product') {
                        $oldNetQty = ($oldItem->received_qty - ($oldItem->rejected_qty ?? 0));
                        $inventoryService->adjustStock(
                            $stockItem,
                            -$oldNetQty,
                            'OUT',
                            'GRN_UPD_REV: '.$receipt->receipt_no,
                            'Reversal for GRN Update',
                            $receipt->branch_id,
                            $receipt->store_id
                        );
                    }
                }

                $receipt->update([
                    'purchase_order_id' => $request->purchase_order_id,
                    'vendor_id' => $request->vendor_id,
                    'received_date' => $request->received_date,
                    'delivery_challan_no' => $request->delivery_challan_no,
                    'received_by' => $request->received_by,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                    'notes' => $request->notes,
                ]);

                $receipt->items()->delete();
                $vendorName = $request->vendor_id ? Vendor::find($request->vendor_id)?->name ?? '' : '';

                foreach ($request->items as $item) {
                    GoodsReceiptItem::create([
                        'goods_receipt_id' => $receipt->id,
                        'item_id' => $item['item_id'],
                        'ordered_qty' => $item['ordered_qty'] ?? 0,
                        'received_qty' => $item['received_qty'],
                        'rejected_qty' => $item['rejected_qty'] ?? 0,
                        'quality_status' => $item['quality_status'] ?? 'passed',
                    ]);

                    // Update Stock again
                    $stockItem = Item::find($item['item_id']);
                    if ($stockItem && $stockItem->type == 'product') {
                        $receivedStock = ($item['received_qty'] - ($item['rejected_qty'] ?? 0));
                        $inventoryService->adjustStock(
                            $stockItem,
                            $receivedStock,
                            'IN',
                            'GRN_UPD: '.$receipt->receipt_no,
                            'Received from Vendor '.$vendorName.' (Updated)',
                            $receipt->branch_id,
                            $receipt->store_id
                        );
                    }
                }

                return redirect()->route('purchases.receipts.index')->with([
                    'message' => 'Goods Receipt updated and stock adjusted.',
                    'alert-type' => 'success',
                ]);
            });
        } catch (Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $receipt = GoodsReceipt::findOrFail($id);

        try {
            return DB::transaction(function () use ($receipt) {
                $inventoryService = app(InventoryService::class);
                
                // Reverse all stock impacts
                foreach ($receipt->items as $item) {
                    $stockItem = Item::find($item->item_id);
                    if ($stockItem && $stockItem->type == 'product') {
                        $netQty = ($item->received_qty - ($item->rejected_qty ?? 0));
                        $inventoryService->adjustStock(
                            $stockItem,
                            -$netQty,
                            'OUT',
                            'GRN_DEL: '.$receipt->receipt_no,
                            'Stock reversal due to GRN deletion',
                            $receipt->branch_id,
                            $receipt->store_id
                        );
                    }
                }

                $receipt->items()->delete();
                $receipt->delete();

                return redirect()->route('purchases.receipts.index')->with([
                    'message' => 'Goods Receipt deleted and stock reversed.',
                    'alert-type' => 'success',
                ]);
            });
        } catch (Exception $e) {
            return back()->with([
                'message' => 'Error: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }
}
