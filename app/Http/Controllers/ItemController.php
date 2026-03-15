<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Branch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Item;
use App\Models\Store;
use App\Models\Unit;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ItemController extends Controller
{
    public function index(Request $request): View
    {
        $query = Item::with(['category', 'brand', 'unit', 'branch', 'store']);

        // Search
        if ($request->search) {
            $search = $request->search;
            $query->whereAny(['name', 'sku', 'barcode'], 'like', "%{$search}%");
        }

        // Filters
        if ($request->category && $request->category !== 'All Categories') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }

        if ($request->status && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        if ($request->type && $request->type !== 'All Types') {
            $query->where('type', strtolower($request->type));
        }

        $items = $query->latest()->paginate(15);

        $categories = Category::where('status', 'active')->get();
        $brands = Brand::where('status', 'active')->get();
        $units = Unit::where('status', 'active')->get();
        $accounts = Account::where('status', 'active')->get();
        $branches = Branch::where('status', 'active')->get();
        $stores = Store::where('status', 'active')->get();

        $stats = [
            'total' => Item::count(),
            'products' => Item::where('type', 'product')->count(),
            'services' => Item::where('type', 'service')->count(),
            'total_value' => (function() {
                /** @var object $res */
                $res = Item::where('type', 'product')->selectRaw('SUM(current_stock * cost_price) as value')->first();
                return $res->value ?? 0;
            })(),
        ];

        return view('admin.items.index', compact('items', 'categories', 'brands', 'units', 'stats', 'accounts', 'branches', 'stores'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:items',
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'type' => 'required|in:product,service',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'opening_stock' => 'nullable|numeric|min:0',
            'reorder_level' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'nullable|numeric|min:0',
            'track_inventory' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'store_id' => 'nullable|exists:stores,id',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'sales_account_id' => 'nullable|exists:accounts,id',
            'purchase_account_id' => 'nullable|exists:accounts,id',
            'inventory_asset_account_id' => 'nullable|exists:accounts,id',
            'cogs_account_id' => 'nullable|exists:accounts,id',
        ]);

        if (empty($validated['sku'])) {
            $validated['sku'] = strtoupper(Str::random(8));
        }

        $validated['slug'] = Str::slug($request->name).'-'.Str::random(5);

        if ($validated['type'] === 'service') {
            $validated['opening_stock'] = 0;
            $validated['current_stock'] = 0;
            $validated['reorder_level'] = 0;
            $validated['reorder_quantity'] = 0;
            $validated['track_inventory'] = false;
            $validated['brand_id'] = null;
            $validated['barcode'] = null;
        } else {
            $validated['opening_stock'] = $request->opening_stock ?? 0;
            $validated['current_stock'] = $request->opening_stock ?? 0;
            $validated['track_inventory'] = (bool) $request->track_inventory;
            $validated['reorder_level'] = $request->reorder_level ?? 0;
            $validated['reorder_quantity'] = $request->reorder_quantity ?? 0;
        }

        $validated['cost_price'] = $request->cost_price ?? 0;
        $validated['tax_rate'] = $request->tax_rate ?? 0;
        $validated['status'] = $request->status ?? 'active';

        Item::create($validated);

        return redirect()->back()->with([
            'message' => 'Item Added Successfully',
            'title' => 'Item Created',
            'alert-type' => 'success',
        ]);
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:items,sku,'.$id,
            'barcode' => 'nullable|string|max:100',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'type' => 'required|in:product,service',
            'cost_price' => 'nullable|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'tax_rate' => 'nullable|numeric|min:0',
            'unit_id' => 'nullable|exists:units,id',
            'reorder_level' => 'nullable|numeric|min:0',
            'reorder_quantity' => 'nullable|numeric|min:0',
            'track_inventory' => 'nullable|boolean',
            'location' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'store_id' => 'nullable|exists:stores,id',
            'description' => 'nullable|string',
            'status' => 'nullable|in:active,inactive',
            'sales_account_id' => 'nullable|exists:accounts,id',
            'purchase_account_id' => 'nullable|exists:accounts,id',
            'inventory_asset_account_id' => 'nullable|exists:accounts,id',
            'cogs_account_id' => 'nullable|exists:accounts,id',
        ]);

        if (empty($validated['sku'])) {
            $validated['sku'] = strtoupper(Str::random(8));
        }

        if ($validated['type'] === 'service') {
            $validated['reorder_level'] = 0;
            $validated['reorder_quantity'] = 0;
            $validated['track_inventory'] = false;
            $validated['brand_id'] = null;
            $validated['barcode'] = null;
        } else {
            $validated['track_inventory'] = (bool) $request->track_inventory;
            $validated['reorder_level'] = $request->reorder_level ?? 0;
            $validated['reorder_quantity'] = $request->reorder_quantity ?? 0;
        }

        $validated['cost_price'] = $request->cost_price ?? 0;
        $validated['tax_rate'] = $request->tax_rate ?? 0;

        $item->update($validated);

        return redirect()->back()->with([
            'message' => 'Item Updated Successfully',
            'title' => 'Item Updated',
            'alert-type' => 'success',
        ]);
    }

    public function destroy($id): RedirectResponse
    {
        $item = Item::findOrFail($id);

        // Cannot delete if there are transactions (e.g., stock moves)
        if ($item->stockMoves()->exists() || $item->current_stock > 0) {
            return redirect()->back()->with([
                'message' => 'Cannot delete item with existing transactions or stock. Please mark it as Inactive instead.',
                'title' => 'Deletion Denied',
                'alert-type' => 'error',
            ]);
        }

        $item->delete();

        return redirect()->back()->with([
            'message' => 'Item Deleted Successfully',
            'title' => 'Item Deleted',
            'alert-type' => 'success',
        ]);
    }

    public function details($id): View
    {
        $item = Item::with(['category', 'brand', 'unit'])->findOrFail($id);

        return view('admin.items.item_details', compact('item'));
    }

    public function template(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="items_template.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'Type (product/service)',
                'Name',
                'SKU (Optional)',
                'Barcode (Optional)',
                'Category ID',
                'Brand ID (Optional)',
                'Cost Price',
                'Selling Price',
                'Tax Rate',
                'Unit ID (Optional)',
                'Opening Stock',
                'Reorder Level',
                'Reorder Quantity',
                'Track Inventory (1/0)',
                'Description',
                'Status (active/inactive)',
            ]);

            fputcsv($file, [
                'product',
                'iPhone 15 Pro',
                'IPH-15PRO-128',
                '1959490001',
                '1',
                '',
                '899.00',
                '999.00',
                '10.00',
                '',
                '50',
                '10',
                '20',
                '1',
                'Latest smartphone model',
                'active',
            ]);

            fputcsv($file, [
                'service',
                'Software Installation',
                'SOFT-INST',
                '',
                '2',
                '',
                '0.00',
                '50.00',
                '0.00',
                '',
                '0',
                '0',
                '0',
                '0',
                'Standard software install',
                'active',
            ]);

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function export(): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="items.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, [
                'ID',
                'Type',
                'Name',
                'SKU',
                'Barcode',
                'Category ID',
                'Brand ID',
                'Cost Price',
                'Selling Price',
                'Tax Rate',
                'Unit ID',
                'Opening Stock',
                'Current Stock',
                'Reorder Level',
                'Reorder Quantity',
                'Track Inventory',
                'Description',
                'Status',
            ]);

            Item::chunk(100, function ($items) use ($file) {
                foreach ($items as $item) {
                    fputcsv($file, [
                        $item->id,
                        $item->type,
                        $item->name,
                        $item->sku,
                        $item->barcode,
                        $item->category_id,
                        $item->brand_id,
                        $item->cost_price,
                        $item->selling_price,
                        $item->tax_rate,
                        $item->unit_id,
                        $item->opening_stock,
                        $item->current_stock,
                        $item->reorder_level,
                        $item->reorder_quantity,
                        $item->track_inventory ? '1' : '0',
                        $item->description,
                        $item->status,
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();

        if (($handle = fopen($path, 'r')) !== false) {
            $header = fgetcsv($handle);
            $importedCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                if (count($row) >= 16 && ! empty(trim($row[1])) && ! empty(trim($row[4])) && ! empty(trim($row[7]))) {
                    $type = trim(strtolower($row[0]));
                    $type = in_array($type, ['product', 'service']) ? $type : 'product';
                    $name = trim($row[1]);
                    $sku = empty(trim($row[2])) ? strtoupper(Str::random(8)) : trim($row[2]);
                    $barcode = trim($row[3]);
                    $categoryId = trim($row[4]);
                    $brandId = ! empty(trim($row[5])) ? trim($row[5]) : null;
                    $costPrice = trim($row[6]);
                    $sellingPrice = trim($row[7]);
                    $taxRate = ! empty(trim($row[8])) ? trim($row[8]) : 0;
                    $unitId = ! empty(trim($row[9])) ? trim($row[9]) : null;
                    $openingStock = ! empty(trim($row[10])) ? trim($row[10]) : 0;
                    $reorderLevel = ! empty(trim($row[11])) ? trim($row[11]) : 0;
                    $reorderQuantity = ! empty(trim($row[12])) ? trim($row[12]) : 0;
                    $trackInventory = trim($row[13]) === '1' ? true : false;
                    $description = trim($row[14]);
                    $status = trim(strtolower($row[15]));
                    $status = in_array($status, ['active', 'inactive']) ? $status : 'active';

                    Item::updateOrCreate(
                        ['sku' => $sku],
                        [
                            'type' => $type,
                            'name' => $name,
                            'slug' => Str::slug($name).'-'.Str::random(5),
                            'barcode' => $barcode,
                            'category_id' => $categoryId,
                            'brand_id' => ($type === 'product' ? $brandId : null),
                            'cost_price' => $costPrice ?: 0,
                            'selling_price' => $sellingPrice,
                            'tax_rate' => $taxRate,
                            'unit_id' => $unitId,
                            'opening_stock' => ($type === 'product' ? ($openingStock ?: 0) : 0),
                            'current_stock' => ($type === 'product' ? ($openingStock ?: 0) : 0),
                            'reorder_level' => ($type === 'product' ? ($reorderLevel ?: 0) : 0),
                            'reorder_quantity' => ($type === 'product' ? ($reorderQuantity ?: 0) : 0),
                            'track_inventory' => ($type === 'product' ? $trackInventory : false),
                            'description' => $description,
                            'status' => $status,
                        ]
                    );

                    $importedCount++;
                }
            }

            fclose($handle);
        }

        return redirect()->back()->with([
            'message' => "Successfully imported $importedCount items.",
            'title' => 'Import Complete',
            'alert-type' => 'success',
        ]);
    }

    public function search(Request $request): JsonResponse
    {
        $search = $request->input('q');
        $items = Item::with(['unit'])
            ->where('track_inventory', true)
            ->whereAny(['name', 'sku'], 'like', "%{$search}%")
            ->limit(10)
            ->get();

        return response()->json($items);
    }
}
