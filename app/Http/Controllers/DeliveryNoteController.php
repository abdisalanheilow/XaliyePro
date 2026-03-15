<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DeliveryNote;
use App\Models\DeliveryNoteItem;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\Store;
use App\Services\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DeliveryNoteController extends Controller
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    public function index(): View
    {
        $receipts = DeliveryNote::with(['customer', 'salesOrder'])->latest()->paginate(10);

        $stats = [
            'total_count' => DeliveryNote::count(),
            'shipped_count' => DeliveryNote::where('status', 'shipped')->count(),
            'delivered_count' => DeliveryNote::where('status', 'delivered')->count(),
            'pending_count' => DeliveryNote::where('status', 'pending')->count(),
        ];

        return view('admin.sales.receipts.index', compact('receipts', 'stats'));
    }

    public function create(): View
    {
        $customers = Customer::all();
        $orders = SalesOrder::whereIn('status', ['confirmed', 'processing'])->get();
        $items = Item::where('type', 'product')->get();
        $stores = Store::all();

        return view('admin.sales.receipts.create', compact('customers', 'orders', 'stores', 'items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            DB::beginTransaction();

            /** @var \App\Models\DeliveryNote $deliveryNote */
            $deliveryNote = DeliveryNote::create([
                'delivery_no' => $request->delivery_no ?? 'DN-'.time(),
                'sales_order_id' => $request->sales_order_id,
                'customer_id' => $request->customer_id,
                'delivery_date' => $request->delivery_date,
                'status' => 'shipped',
                'notes' => $request->notes,
                'store_id' => $request->store_id,
                'branch_id' => Auth::user()->branch_id ?? 1,
                'delivered_by' => Auth::id(),
            ]);

            foreach ($request->items as $itemData) {
                DeliveryNoteItem::create([
                    'delivery_note_id' => $deliveryNote->id,
                    'item_id' => $itemData['item_id'],
                    'ordered_qty' => $itemData['quantity'], // Assuming for now
                    'delivered_qty' => $itemData['quantity'],
                ]);

                // Adjust Stock
                $item = Item::findOrFail($itemData['item_id']);
                $this->inventoryService->adjustStock(
                    $item,
                    -$itemData['quantity'],
                    'SALE',
                    $deliveryNote->delivery_no,
                    'Delivery for Order #'.($deliveryNote->salesOrder->order_no ?? 'Direct'),
                    $deliveryNote->branch_id,
                    $deliveryNote->store_id
                );
            }

            // Update Sales Order Status if applicable
            if ($deliveryNote->sales_order_id) {
                SalesOrder::where('id', $deliveryNote->sales_order_id)->update(['status' => 'shipped']);
            }

            DB::commit();

            return redirect()->route('sales.receipts.index')->with('success', 'Delivery Note created and stock updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function show(int $id): View
    {
        $receipt = DeliveryNote::with(['items.item', 'customer', 'salesOrder', 'store'])->findOrFail($id);

        return view('admin.sales.receipts.show', compact('receipt'));
    }

    public function edit(int $id): View
    {
        $receipt = DeliveryNote::with('items')->findOrFail($id);
        $customers = Customer::all();
        $orders = SalesOrder::all();
        $items = Item::where('type', 'product')->get();
        $stores = Store::all();

        return view('admin.sales.receipts.edit', compact('receipt', 'customers', 'orders', 'items', 'stores'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'delivery_date' => 'required|date',
            'store_id' => 'required|exists:stores,id',
            'items' => 'required|array|min:1',
        ]);

        $receipt = DeliveryNote::findOrFail($id);

        try {
            DB::beginTransaction();
            
            // For now, keep it simple. Real ERP would reverse stock and re-apply.
            $receipt->update([
                'customer_id' => $request->customer_id,
                'delivery_date' => $request->delivery_date,
                'store_id' => $request->store_id,
                'notes' => $request->notes,
            ]);

            DB::commit();
            return redirect()->route('sales.receipts.index')->with('success', 'Delivery Note updated successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        $receipt = DeliveryNote::with('items')->findOrFail($id);

        try {
            DB::beginTransaction();

            // Reverse Stock Movement
            foreach ($receipt->items as $dnItem) {
                $item = Item::findOrFail($dnItem->item_id);
                $this->inventoryService->adjustStock(
                    $item,
                    $dnItem->delivered_qty,
                    'CANCEL_SALE',
                    $receipt->delivery_no,
                    'Reversal of delivery note #'.$receipt->delivery_no,
                    $receipt->branch_id,
                    $receipt->store_id
                );
            }

            $receipt->delete();
            DB::commit();

            return redirect()->route('sales.receipts.index')->with('success', 'Delivery Note deleted and stock reversed');
        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
