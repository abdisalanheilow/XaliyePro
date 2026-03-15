<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\StockAdjustment;
use App\Http\Requests\StockAdjustmentRequest;
use App\Models\StockMove;
use App\Models\Store;
use App\Services\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StockAdjustmentController extends Controller
{
    protected $inventory;

    public function __construct(InventoryService $inventory)
    {
        $this->inventory = $inventory;
    }

    public function index(Request $request)
    {
        $query = StockAdjustment::with(['store', 'creator']);

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('adjustment_no', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('store', function (Builder $sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $adjustments = $query->latest()->paginate(15);

        $stats = [
            'total_adjustments' => StockAdjustment::count(),
            'total_draft' => StockAdjustment::where('status', 'draft')->count(),
            'total_adjusted' => StockAdjustment::where('status', 'adjusted')->count(),
            'value_impact' => StockMove::where('type', 'ADJUST')->sum('total_cost'),
        ];

        return view('admin.inventory.adjustments.index', compact('adjustments', 'stats'));
    }

    public function create()
    {
        $stores = Store::all();
        $nextNum = $this->inventory->generateReference('ADJ');

        return view('admin.inventory.adjustments.create', compact('stores', 'nextNum'));
    }

    public function store(StockAdjustmentRequest $request)
    {
        try {
            $adjustment = $this->inventory->createAdjustment($request->validated());

            return redirect()->route('inventory.adjustments.index')->with([
                'message' => "Adjustment {$adjustment->adjustment_no} created as draft.",
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error creating adjustment: '.$e->getMessage(),
                'alert-type' => 'error',
            ]);
        }
    }

    public function show($id)
    {
        $adjustment = StockAdjustment::with(['items.item', 'store', 'creator'])->findOrFail($id);

        return view('admin.inventory.adjustments.show', compact('adjustment'));
    }

    public function finalize($id): RedirectResponse
    {
        $adjustment = StockAdjustment::with('items')->findOrFail($id);
        if ($adjustment->status === 'finalized') {
            return back()->with(['message' => 'Adjustment already finalized', 'alert-type' => 'warning']);
        }

        try {
            DB::transaction(function () use ($adjustment) {
                $inventoryService = app(InventoryService::class);
                foreach ($adjustment->items as $adjItem) {
                    $item = Item::findOrFail($adjItem->item_id);
                    $qty = $adjItem->adjustment_type === 'addition' ? $adjItem->quantity : -$adjItem->quantity;

                    $inventoryService->adjustStock(
                        $item,
                        $qty,
                        $adjItem->adjustment_type === 'addition' ? 'IN' : 'OUT',
                        'ADJ: '.$adjustment->adjustment_no,
                        'Manual Stock Adjustment - '.$adjustment->reason,
                        $adjustment->branch_id,
                        $adjustment->store_id
                    );
                }
                $adjustment->update(['status' => 'finalized']);
            });

            return redirect()->route('inventory.adjustments.index')->with([
                'message' => 'Stock Adjustment finalized and inventory updated',
                'alert-type' => 'success',
            ]);
        } catch (\Exception $e) {
            return back()->with(['message' => 'Error: '.$e->getMessage(), 'alert-type' => 'error']);
        }
    }

    public function edit($id): View
    {
        $adjustment = StockAdjustment::with('items')->findOrFail($id);
        if ($adjustment->status === 'finalized') {
            abort(403, 'Cannot edit finalized adjustment.');
        }
        $items = Item::where('status', 'active')->get();
        $branches = Branch::all();
        $stores = Store::all();

        return view('admin.inventory.adjustments.edit', compact('adjustment', 'items', 'branches', 'stores'));
    }

    public function destroy($id): RedirectResponse
    {
        $adjustment = StockAdjustment::findOrFail($id);
        if ($adjustment->status === 'finalized') {
            return back()->with(['message' => 'Cannot delete finalized adjustment.', 'alert-type' => 'error']);
        }

        $adjustment->items()->delete();
        $adjustment->delete();

        return redirect()->route('inventory.adjustments.index')->with([
            'message' => 'Stock Adjustment deleted successfully',
            'alert-type' => 'success',
        ]);
    }
}
