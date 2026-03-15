<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Store;
use App\Services\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockTransferController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $query = StockTransfer::with(['fromStore', 'toStore', 'creator']);

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('transfer_no', 'like', "%{$search}%")
                    ->orWhereHas('fromStore', function (Builder $sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('toStore', function (Builder $sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $transfers = $query->latest()->paginate(15);

        $stats = [
            'total_transfers' => StockTransfer::count(),
            'total_draft' => StockTransfer::where('status', 'draft')->count(),
            'total_completed' => StockTransfer::where('status', 'transferred')->count(),
            'items_moved' => StockTransferItem::whereHas('transfer', function ($q) {
                $q->where('status', 'transferred');
            })->sum('quantity'),
        ];

        return view('admin.inventory.transfers.index', compact('transfers', 'stats'));
    }

    public function create()
    {
        $stores = Store::all();
        $nextNum = $this->inventory->generateReference('TRF', 'stock_transfers');

        return view('admin.inventory.transfers.create', compact('stores', 'nextNum'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'transfer_no' => 'required|unique:stock_transfers',
            'transfer_date' => 'required|date',
            'from_store_id' => 'required|exists:stores,id|different:to_store_id',
            'to_store_id' => 'required|exists:stores,id',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
        ]);

        try {
            $transfer = $this->inventory->createTransfer($request->all());

            return redirect()->route('inventory.transfers.index')->with([
                'message' => 'Transfer created successfully as draft.',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error creating transfer: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function show($id)
    {
        $transfer = StockTransfer::with(['items.item', 'fromStore', 'toStore', 'creator'])->findOrFail($id);

        return view('admin.inventory.transfers.show', compact('transfer'));
    }

    public function finalize($id): RedirectResponse
    {
        $transfer = StockTransfer::findOrFail($id);
        if ($transfer->status === 'finalized') {
            return back()->with(['message' => 'Transfer already finalized', 'alert-type' => 'warning']);
        }

        try {
            DB::transaction(function () use ($transfer) {
                $inventoryService = app(InventoryService::class);
                foreach ($transfer->items as $txItem) {
                    $item = Item::findOrFail($txItem->item_id);

                    // OUT from Source
                    $inventoryService->adjustStock(
                        $item,
                        -$txItem->quantity,
                        'OUT',
                        'TX_OUT: '.$transfer->transfer_no,
                        'Transfer to '.$transfer->toStore->name,
                        $transfer->from_branch_id,
                        $transfer->from_store_id
                    );

                    // IN to Destination
                    $inventoryService->adjustStock(
                        $item,
                        $txItem->quantity,
                        'IN',
                        'TX_IN: '.$transfer->transfer_no,
                        'Transfer from '.$transfer->fromStore->name,
                        $transfer->to_branch_id,
                        $transfer->to_store_id
                    );
                }
                $transfer->update(['status' => 'finalized']);
            });

            return redirect()->route('inventory.transfers.index')->with([
                'message' => 'Stock Transfer finalized and inventory updated',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with(['message' => 'Error: '.$e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function edit($id): View
    {
        $transfer = StockTransfer::with('items')->findOrFail($id);
        if ($transfer->status === 'finalized') {
            abort(403, 'Cannot edit finalized transfer.');
        }
        $items = Item::where('status', 'active')->get();
        $branches = Branch::all();
        $stores = Store::all();

        return view('admin.inventory.transfers.edit', compact('transfer', 'items', 'branches', 'stores'));
    }

    public function destroy($id): RedirectResponse
    {
        $transfer = StockTransfer::findOrFail($id);
        if ($transfer->status === 'finalized') {
            return back()->with(['message' => 'Cannot delete finalized transfer.', 'alert-type' => 'error']);
        }

        $transfer->items()->delete();
        $transfer->delete();

        return redirect()->route('inventory.transfers.index')->with([
            'message' => 'Stock Transfer deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
