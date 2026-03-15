<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\Store;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesOrderController extends Controller
{
    public function index(): View
    {
        $orders = SalesOrder::with(['customer', 'branch'])->latest()->paginate(10);

        $stats = [
            'total_amount' => SalesOrder::where('status', '!=', 'cancelled')->sum('grand_total'),
            'total_count' => SalesOrder::count(),
            'pending_count' => SalesOrder::whereIn('status', ['confirmed', 'processing'])->count(),
            'delivered_count' => SalesOrder::where('status', 'delivered')->count(),
            'invoiced_count' => SalesOrder::where('status', 'invoiced')->count(),
            'draft_count' => SalesOrder::where('status', 'draft')->count(),
        ];

        return view('admin.sales.orders.index', compact('orders', 'stats'));
    }

    public function create(): View
    {
        $customers = Customer::all();
        $items = Item::all();
        $branches = Branch::all();
        $stores = Store::all();

        // Generate Order Number
        /** @var \App\Models\SalesOrder $lastOrder */
        $lastOrder = SalesOrder::latest()->first();
        $nextId = $lastOrder ? $lastOrder->id + 1 : 1;
        $orderNo = 'SO-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return view('admin.sales.orders.create', compact('customers', 'items', 'branches', 'stores', 'orderNo'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_no' => 'required|unique:sales_orders,order_no',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            /** @var \App\Models\SalesOrder $order */
            $order = SalesOrder::create([
                'order_no' => $request->order_no,
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'status' => $request->action === 'draft' ? 'draft' : 'confirmed',
                'total_amount' => 0,
                'tax_amount' => 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'grand_total' => 0,
                'reference_no' => $request->reference_no,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'branch_id' => $request->branch_id,
                'created_by' => Auth::id(),
            ]);

            $totalAmount = 0;
            $totalTax = 0;

            foreach ($request->items as $itemData) {
                $amount = $itemData['quantity'] * $itemData['unit_price'];
                $taxRate = $itemData['tax_rate'] ?? 0;
                $taxAmount = $amount * ($taxRate / 100);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'amount' => $amount + $taxAmount,
                ]);

                $totalAmount += $amount;
                $totalTax += $taxAmount;
            }

            $discount = $request->discount_type === 'percent'
                ? ($totalAmount + $totalTax) * ($request->discount_amount / 100)
                : ($request->discount_amount ?? 0);

            $grandTotal = ($totalAmount + $totalTax) - $discount;

            $order->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $totalTax,
                'discount_amount' => $discount,
                'grand_total' => $grandTotal,
            ]);

            DB::commit();

            return redirect()->route('sales.orders.index')->with('success', 'Sales Order created successfully');

        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function show($id): View
    {
        $order = SalesOrder::with(['items.item', 'customer', 'branch', 'creator'])->findOrFail($id);

        return view('admin.sales.orders.show', compact('order'));
    }

    public function edit($id): View
    {
        $order = SalesOrder::with('items')->findOrFail($id);
        $customers = Customer::all();
        $items = Item::all();
        $branches = Branch::all();
        $stores = Store::all();

        return view('admin.sales.orders.edit', compact('order', 'customers', 'items', 'branches', 'stores'));
    }

    public function update(Request $request, $id): RedirectResponse
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'order_date' => 'required|date',
            'items' => 'required|array|min:1',
        ]);

        try {
            DB::beginTransaction();
            $order = SalesOrder::findOrFail($id);

            // Delete old items
            $order->items()->delete();

            $totalAmount = 0;
            $totalTax = 0;

            foreach ($request->items as $itemData) {
                $amount = $itemData['quantity'] * $itemData['unit_price'];
                $taxRate = $itemData['tax_rate'] ?? 0;
                $taxAmount = $amount * ($taxRate / 100);

                SalesOrderItem::create([
                    'sales_order_id' => $order->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'amount' => $amount + $taxAmount,
                ]);

                $totalAmount += $amount;
                $totalTax += $taxAmount;
            }

            $discount = $request->discount_type === 'percent'
                ? ($totalAmount + $totalTax) * ($request->discount_amount / 100)
                : ($request->discount_amount ?? 0);

            $grandTotal = ($totalAmount + $totalTax) - $discount;

            $order->update([
                'customer_id' => $request->customer_id,
                'order_date' => $request->order_date,
                'delivery_date' => $request->delivery_date,
                'status' => $request->status ?? $order->status,
                'total_amount' => $totalAmount,
                'tax_amount' => $totalTax,
                'discount_amount' => $discount,
                'grand_total' => $grandTotal,
                'reference_no' => $request->reference_no,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'branch_id' => $request->branch_id,
            ]);

            DB::commit();

            return redirect()->route('sales.orders.index')->with('success', 'Sales Order updated successfully');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function destroy($id): RedirectResponse
    {
        try {
            $order = SalesOrder::findOrFail($id);
            $order->items()->delete();
            $order->delete();

            return redirect()->route('sales.orders.index')->with('success', 'Sales Order deleted successfully');
        } catch (Exception $e) {
            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
