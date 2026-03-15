<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesInvoice;
use App\Models\SalesReturn;
use App\Models\SalesReturnItem;
use App\Models\Store;
use App\Services\InventoryService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesReturnController extends Controller
{
    public function index()
    {
        $returns = SalesReturn::with(['customer', 'invoice'])->latest()->paginate(10);

        $stats = [
            'total_amount' => SalesReturn::sum('grand_total'),
            'total_count' => SalesReturn::count(),
            'today_count' => SalesReturn::whereDate('return_date', now())->count(),
        ];

        return view('admin.sales.returns.index', compact('returns', 'stats'));
    }

    public function create(Request $request)
    {
        $customers = Customer::all();
        $invoices = SalesInvoice::latest()->get();
        $branches = Branch::all();
        $items = Item::all();
        $stores = Store::all();

        // Generate Return Number
        /** @var \App\Models\SalesReturn $lastReturn */
        $lastReturn = SalesReturn::latest()->first();
        $nextId = $lastReturn ? $lastReturn->id + 1 : 1;
        $returnNo = 'SR-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $selectedInvoice = null;
        if ($request->has('invoice_id')) {
            $selectedInvoice = SalesInvoice::with('items')->find($request->invoice_id);
        }

        return view('admin.sales.returns.create', compact('customers', 'invoices', 'branches', 'items', 'stores', 'returnNo', 'selectedInvoice'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'return_no' => 'required|unique:sales_returns,return_no',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            /** @var \App\Models\SalesReturn $return */
            $return = SalesReturn::create([
                'return_no' => $request->return_no,
                'customer_id' => $request->customer_id,
                'sales_invoice_id' => $request->sales_invoice_id,
                'return_date' => $request->return_date,
                'total_amount' => 0,
                'tax_amount' => 0,
                'grand_total' => 0,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'branch_id' => $request->branch_id,
                'created_by' => Auth::id(),
            ]);

            $totalAmount = 0;
            $totalTax = 0;

            foreach ($request->items as $itemData) {
                $amount = $itemData['quantity'] * $itemData['unit_price'];
                $taxRate = $itemData['tax_rate'] ?? 0;
                $taxAmount = $amount * ($taxRate / 100);

                SalesReturnItem::create([
                    'sales_return_id' => $return->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'amount' => $amount + $taxAmount,
                ]);

                $totalAmount += $amount;
                $totalTax += $taxAmount;

                // Restore Stock via InventoryService
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    app(InventoryService::class)->adjustStock(
                        $item,
                        $itemData['quantity'],
                        'IN',
                        'SR: '.$return->return_no,
                        'Sales Return from Customer: '.($return->customer->name ?? 'N/A'),
                        $request->branch_id,
                        $request->store_id ?? $item->store_id
                    );
                }
            }

            $return->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $totalTax,
                'grand_total' => $totalAmount + $totalTax,
            ]);

            DB::commit();

            return redirect()->route('sales.returns.index')->with('success', 'Sales Return processed and stock updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error processing return: '.$e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        $return = SalesReturn::with(['items.item', 'customer', 'invoice', 'branch'])->findOrFail($id);

        return view('admin.sales.returns.show', compact('return'));
    }

    public function edit($id)
    {
        $return = SalesReturn::with('items')->findOrFail($id);
        $customers = Customer::all();
        $invoices = SalesInvoice::latest()->get();
        $branches = Branch::all();
        $items = Item::all();
        $stores = Store::all();

        return view('admin.sales.returns.edit', compact('return', 'customers', 'invoices', 'branches', 'items', 'stores'));
    }

    public function update(Request $request, $id)
    {
        $return = SalesReturn::findOrFail($id);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'return_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $inventoryService = app(InventoryService::class);

            // Reverse old stock impacts
            foreach ($return->items as $oldItem) {
                $item = Item::find($oldItem->item_id);
                if ($item) {
                    $inventoryService->adjustStock(
                        $item,
                        -$oldItem->quantity,
                        'OUT',
                        'SR_UPD_REV: '.$return->return_no,
                        'Stock reversal for Sales Return Update',
                        $return->branch_id,
                        $return->store_id ?? $item->store_id
                    );
                }
            }

            $return->update([
                'customer_id' => $request->customer_id,
                'sales_invoice_id' => $request->sales_invoice_id,
                'return_date' => $request->return_date,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'branch_id' => $request->branch_id,
                'store_id' => $request->store_id,
            ]);

            $return->items()->delete();

            $totalAmount = 0;
            $totalTax = 0;

            foreach ($request->items as $itemData) {
                $amount = $itemData['quantity'] * $itemData['unit_price'];
                $taxRate = $itemData['tax_rate'] ?? 0;
                $taxAmount = $amount * ($taxRate / 100);

                SalesReturnItem::create([
                    'sales_return_id' => $return->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'amount' => $amount + $taxAmount,
                ]);

                $totalAmount += $amount;
                $totalTax += $taxAmount;

                // Adjust Stock again
                $item = Item::find($itemData['item_id']);
                if ($item) {
                    $inventoryService->adjustStock(
                        $item,
                        $itemData['quantity'],
                        'IN',
                        'SR_UPD: '.$return->return_no,
                        'Sales Return from Customer: '.($return->customer->name ?? 'N/A').' (Updated)',
                        $request->branch_id,
                        $request->store_id ?? $item->store_id
                    );
                }
            }

            $return->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $totalTax,
                'grand_total' => $totalAmount + $totalTax,
            ]);

            DB::commit();

            return redirect()->route('sales.returns.index')->with('success', 'Sales Return updated and stock adjusted successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error updating return: '.$e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $return = SalesReturn::findOrFail($id);

        try {
            DB::beginTransaction();

            $inventoryService = app(InventoryService::class);

            // Reverse stock impacts
            foreach ($return->items as $item) {
                $stockItem = Item::find($item->item_id);
                if ($stockItem) {
                    $inventoryService->adjustStock(
                        $stockItem,
                        -$item->quantity,
                        'OUT',
                        'SR_DEL: '.$return->return_no,
                        'Stock reversal for Sales Return Deletion',
                        $return->branch_id,
                        $return->store_id ?? $stockItem->store_id
                    );
                }
            }

            $return->items()->delete();
            $return->delete();

            DB::commit();

            return redirect()->route('sales.returns.index')->with('success', 'Sales Return deleted and stock reversed.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error deleting return: '.$e->getMessage());
        }
    }
}
