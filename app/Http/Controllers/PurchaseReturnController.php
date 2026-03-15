<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseReturnRequest;
use App\Models\Branch;
use App\Models\Item;
use App\Models\PurchaseBill;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\Store;
use App\Models\Vendor;
use App\Services\InventoryService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseReturnController extends Controller
{
    public function index(Request $request): View
    {
        $query = PurchaseReturn::with(['vendor', 'bill', 'branch', 'store']);

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('return_no', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function (Builder $vq) use ($search) {
                        $vq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $returns = $query->orderBy('return_date', 'desc')->paginate(15);

        $stats = [
            'total_amount' => PurchaseReturn::sum('grand_total'),
            'total_count' => PurchaseReturn::count(),
            'today_amount' => PurchaseReturn::whereDate('return_date', now())->sum('grand_total'),
            'month_amount' => PurchaseReturn::whereMonth('return_date', now()->month)->sum('grand_total'),
        ];

        return view('admin.purchases.returns.index', compact('returns', 'stats'));
    }

    public function create(): View
    {
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('type', 'product')->where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $stores = Store::where('status', 'active')->orderBy('name', 'asc')->get();
        $bills = PurchaseBill::where('status', '!=', 'draft')->orderBy('bill_no', 'desc')->get();

        $lastReturn = PurchaseReturn::orderBy('id', 'desc')->first();
        /** @var \App\Models\PurchaseReturn $lastReturn */
        $nextId = $lastReturn ? $lastReturn->id + 1 : 1;
        $returnNo = 'PR-'.date('Y').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.purchases.returns.create', compact('vendors', 'items', 'branches', 'stores', 'bills', 'returnNo'));
    }

    public function store(StorePurchaseReturnRequest $request): RedirectResponse
    {

        try {
            return DB::transaction(function () use ($request) {
                $subtotal = 0;
                $totalTax = 0;

                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    $subtotal += $itemSub;
                    $totalTax += $itemTax;
                }

                $grandTotal = $subtotal + $totalTax;

                /** @var \App\Models\PurchaseReturn $return */
                $return = PurchaseReturn::create([
                    'return_no' => $request->return_no,
                    'purchase_bill_id' => $request->purchase_bill_id,
                    'vendor_id' => $request->vendor_id,
                    'return_date' => $request->return_date,
                    'reference_no' => $request->reference_no,
                    'total_amount' => $subtotal,
                    'tax_amount' => $totalTax,
                    'grand_total' => $grandTotal,
                    'notes' => $request->notes,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                    'created_by' => auth()->id(),
                ]);

                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    PurchaseReturnItem::create([
                        'purchase_return_id' => $return->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_amount' => $itemTax,
                        'amount' => $itemSub + $itemTax,
                    ]);

                    // ERP Logic: Deduct stock and log StockMove via InventoryService
                    $stockItem = Item::find($item['item_id']);
                    if ($stockItem && $stockItem->type == 'product') {
                        $vendorName = $request->vendor_id ? Vendor::find($request->vendor_id)->name : '';
                        app(InventoryService::class)->adjustStock(
                            $stockItem,
                            -$item['quantity'],
                            'OUT',
                            'PR: '.$return->return_no,
                            'Purchase Return to Vendor '.$vendorName,
                            $request->branch_id,
                            $request->store_id
                        );
                    }
                }

                return redirect()->route('purchases.returns.index')->with([
                    'message' => 'Purchase return created successfully',
                    'title' => 'Return Created',
                    'alert-type' => 'success',
                ]);
            });
        } catch (Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'title' => 'Error Occurred',
                'alert-type' => 'error',
            ]);
        }
    }

    public function show($id): View
    {
        $return = PurchaseReturn::with(['vendor', 'bill', 'items.item', 'branch', 'store'])->findOrFail($id);

        return view('admin.purchases.returns.show', compact('return'));
    }

    public function edit($id): View
    {
        $return = PurchaseReturn::with('items')->findOrFail($id);
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('type', 'product')->where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $stores = Store::where('status', 'active')->orderBy('name', 'asc')->get();
        $bills = PurchaseBill::where('status', '!=', 'draft')->orderBy('bill_no', 'desc')->get();

        return view('admin.purchases.returns.edit', compact('return', 'vendors', 'items', 'branches', 'stores', 'bills'));
    }

    public function update(StorePurchaseReturnRequest $request, $id): RedirectResponse
    {
        $return = PurchaseReturn::findOrFail($id);

        try {
            return DB::transaction(function () use ($request, $return) {
                $subtotal = 0;
                $totalTax = 0;

                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    $subtotal += $itemSub;
                    $totalTax += $itemTax;
                }

                $grandTotal = $subtotal + $totalTax;

                $return->update([
                    'purchase_bill_id' => $request->purchase_bill_id,
                    'vendor_id' => $request->vendor_id,
                    'return_date' => $request->return_date,
                    'reference_no' => $request->reference_no,
                    'total_amount' => $subtotal,
                    'tax_amount' => $totalTax,
                    'grand_total' => $grandTotal,
                    'notes' => $request->notes,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                ]);

                $return->items()->delete();

                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    PurchaseReturnItem::create([
                        'purchase_return_id' => $return->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_amount' => $itemTax,
                        'amount' => $itemSub + $itemTax,
                    ]);
                }

                return redirect()->route('purchases.returns.index')->with([
                    'message' => 'Purchase return updated successfully',
                    'title' => 'Return Updated',
                    'alert-type' => 'success',
                ]);
            });
        } catch (Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'title' => 'Error Occurred',
                'alert-type' => 'error',
            ]);
        }
    }

    public function destroy($id): RedirectResponse
    {
        $return = PurchaseReturn::findOrFail($id);
        $return->items()->delete();
        $return->delete();

        return redirect()->route('purchases.returns.index')->with([
            'message' => 'Purchase return deleted successfully',
            'title' => 'Return Deleted',
            'alert-type' => 'success',
        ]);
    }
}
