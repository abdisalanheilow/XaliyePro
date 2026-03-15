<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseOrderRequest;
use App\Models\Branch;
use App\Models\Item;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Store;
use App\Models\Vendor;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchaseOrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = PurchaseOrder::query()->with(['vendor', 'branch', 'store']);

        // Apply filters
        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('order_no', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function (Builder $vq) use ($search) {
                        $vq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->status && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        $orders = $query->orderBy('order_date', 'desc')->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total_amount' => PurchaseOrder::sum('grand_total'),
            'total_count' => PurchaseOrder::count(),
            'pending_count' => PurchaseOrder::where('status', 'pending')->count(),
            'received_count' => PurchaseOrder::where('status', 'received')->count(),
            'draft_count' => PurchaseOrder::where('status', 'draft')->count(),
        ];

        return view('admin.purchases.purchase_orders', compact('orders', 'stats'));
    }

    public function create(Request $request): View
    {
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $stores = Store::where('status', 'active')->orderBy('name', 'asc')->get();

        /** @var \App\Models\PurchaseOrder $lastOrder */
        $lastOrder = PurchaseOrder::orderBy('id', 'desc')->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $orderNo = 'PO-'.date('Y').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.purchases.create_order', compact('vendors', 'items', 'orderNo', 'branches', 'stores'));
    }

    public function store(StorePurchaseOrderRequest $request): RedirectResponse
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

                $discountVal = $request->discount_val ?? 0;
                $discountAmt = $request->discount_type === 'percent'
                    ? ($subtotal + $totalTax) * ($discountVal / 100)
                    : $discountVal;

                $grandTotal = ($subtotal + $totalTax) - $discountAmt;
                $status = $request->action === 'draft' ? 'draft' : 'pending';

                /** @var \App\Models\PurchaseOrder $order */
                $order = PurchaseOrder::create([
                    'order_no' => $request->order_no,
                    'reference_no' => $request->reference_no,
                    'vendor_id' => $request->vendor_id,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                    'order_date' => $request->order_date,
                    'expected_date' => $request->expected_date,
                    'payment_terms' => $request->payment_terms,
                    'total_amount' => $subtotal,
                    'tax_amount' => $totalTax,
                    'discount_amount' => $discountAmt,
                    'grand_total' => $grandTotal,
                    'status' => $status,
                    'notes' => $request->notes,
                    'created_by' => auth()->id(),
                ]);

                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_amount' => $itemTax,
                        'amount' => $itemSub + $itemTax,
                    ]);
                }

                return redirect()->route('purchases.orders.index')->with([
                    'message' => 'Purchase order created successfully',
                    'title' => 'Order Created',
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
        $order = PurchaseOrder::with(['vendor', 'items.item', 'branch', 'store'])->findOrFail($id);

        return view('admin.purchases.show_order', compact('order'));
    }

    public function edit($id): View
    {
        $order = PurchaseOrder::with('items')->findOrFail($id);
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('type', 'product')->where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $stores = Store::where('status', 'active')->orderBy('name', 'asc')->get();

        return view('admin.purchases.edit_order', compact('order', 'vendors', 'items', 'branches', 'stores'));
    }

    public function update(StorePurchaseOrderRequest $request, $id): RedirectResponse
    {
        $order = PurchaseOrder::findOrFail($id);

        try {
            return DB::transaction(function () use ($request, $order) {
                $subtotal = 0;
                $totalTax = 0;

                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    $subtotal += $itemSub;
                    $totalTax += $itemTax;
                }

                $discountVal = $request->discount_val ?? 0;
                $discountAmt = $request->discount_type === 'percent'
                    ? ($subtotal + $totalTax) * ($discountVal / 100)
                    : $discountVal;

                $grandTotal = ($subtotal + $totalTax) - $discountAmt;

                $status = $order->status;
                if ($request->action !== 'draft' && $status === 'draft') {
                    $status = 'pending';
                }

                $order->update([
                    'reference_no' => $request->reference_no,
                    'vendor_id' => $request->vendor_id,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                    'order_date' => $request->order_date,
                    'expected_date' => $request->expected_date,
                    'payment_terms' => $request->payment_terms,
                    'total_amount' => $subtotal,
                    'tax_amount' => $totalTax,
                    'discount_amount' => $discountAmt,
                    'grand_total' => $grandTotal,
                    'status' => $status,
                    'notes' => $request->notes,
                ]);

                $order->items()->delete();
                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    PurchaseOrderItem::create([
                        'purchase_order_id' => $order->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_amount' => $itemTax,
                        'amount' => $itemSub + $itemTax,
                    ]);
                }

                return redirect()->route('purchases.orders.index')->with([
                    'message' => 'Purchase order updated successfully',
                    'title' => 'Order Updated',
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
        $order = PurchaseOrder::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('purchases.orders.index')->with([
            'message' => 'Purchase order deleted successfully',
            'title' => 'Order Deleted',
            'alert-type' => 'success',
        ]);
    }
}
