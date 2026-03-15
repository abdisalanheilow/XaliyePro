<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Item;
use App\Models\StockMove;
use App\Models\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventoryController extends Controller
{
    public function onHand(Request $request): View
    {
        $query = Item::with(['category', 'unit', 'store', 'branch'])
            ->where('track_inventory', true);

        // Search by Item Name or SKU
        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by Branch
        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by Store
        if ($request->store_id) {
            $query->where('store_id', $request->store_id);
        }

        $items = $query->orderBy('name')->paginate(15);

        $stats = [
            'total_items' => Item::where('track_inventory', true)->count(),
            'total_stock_value' => Item::where('track_inventory', true)->sum(DB::raw('current_stock * cost_price')),
            'out_of_stock' => Item::where('current_stock', '<=', 0)->where('track_inventory', true)->count(),
            'low_stock' => Item::whereColumn('current_stock', '<=', 'reorder_level')->where('track_inventory', true)->count(),
        ];

        $branches = Branch::all();
        $stores = Store::all();

        return view('admin.inventory.on_hand', compact('items', 'stats', 'branches', 'stores'));
    }

    public function movements(Request $request): View
    {
        $query = StockMove::with(['item', 'store', 'creator']);

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('remarks', 'like', "%{$search}%")
                    ->orWhereHas('item', function (Builder $iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%")
                            ->orWhere('sku', 'like', "%{$search}%");
                    });
            });
        }

        $movements = $query->latest()->paginate(20);

        $stats = [
            'total_moves' => StockMove::count(),
            'stock_in' => StockMove::where('quantity', '>', 0)->count(),
            'stock_out' => StockMove::where('quantity', '<', 0)->count(),
            'total_volume' => StockMove::sum('quantity'),
        ];

        return view('admin.inventory.movements', compact('movements', 'stats'));
    }

    public function lowStock(Request $request): View
    {
        $query = Item::with(['category', 'unit', 'store', 'branch'])
            ->where('track_inventory', true)
            ->whereColumn('current_stock', '<=', 'reorder_level');

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $items = $query->orderBy('current_stock', 'asc')->paginate(15);

        $stats = [
            'total_alerts' => Item::where('track_inventory', true)
                ->whereColumn('current_stock', '<=', 'reorder_level')
                ->count(),
            'out_of_stock' => Item::where('track_inventory', true)
                ->where('current_stock', '<=', 0)
                ->count(),
            'reorder_value' => Item::where('track_inventory', true)
                ->whereColumn('current_stock', '<=', 'reorder_level')
                ->selectRaw('SUM((reorder_level - current_stock) * cost_price) as total')
                ->value('total') ?? 0,
            'unique_categories' => Item::where('track_inventory', true)
                ->whereColumn('current_stock', '<=', 'reorder_level')
                ->distinct('category_id')
                ->count('category_id'),
        ];

        return view('admin.inventory.low_stock', compact('items', 'stats'));
    }
}
