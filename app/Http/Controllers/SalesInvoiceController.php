<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSalesInvoiceRequest;
use App\Models\Branch;
use App\Models\Customer;
use App\Models\Item;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\Store;
use App\Services\AccountingService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SalesInvoiceController extends Controller
{
    public function index(): View
    {
        $invoices = SalesInvoice::with(['customer', 'branch'])->latest()->paginate(10);

        $stats = [
            'total_amount' => SalesInvoice::sum('grand_total'),
            'total_count' => SalesInvoice::count(),
            'paid_count' => SalesInvoice::where('status', 'paid')->count(),
            'unpaid_balance' => SalesInvoice::sum('balance_amount'),
            'partially_paid_count' => SalesInvoice::where('status', 'partially_paid')->count(),
        ];

        return view('admin.sales.invoices.index', compact('invoices', 'stats'));
    }

    public function create(Request $request): View
    {
        $customers = Customer::all();
        $items = Item::all();
        $branches = Branch::all();
        $stores = Store::all();
        $orders = SalesOrder::whereIn('status', ['confirmed', 'processing'])->get();

        // Generate Invoice Number
        /** @var \App\Models\SalesInvoice $lastInvoice */
        $lastInvoice = SalesInvoice::latest()->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $invoiceNo = 'INV-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        // Optional: Pre-fill if creating from Order
        $selectedOrder = null;
        if ($request->has('order_id')) {
            $selectedOrder = SalesOrder::with('items')->find($request->order_id);
        }

        return view('admin.sales.invoices.create', compact('customers', 'items', 'branches', 'stores', 'invoiceNo', 'orders', 'selectedOrder'));
    }

    public function store(StoreSalesInvoiceRequest $request): RedirectResponse
    {

        try {
            DB::beginTransaction();

            /** @var \App\Models\SalesInvoice $invoice */
            $invoice = SalesInvoice::create([
                'invoice_no' => $request->invoice_no,
                'sales_order_id' => $request->sales_order_id,
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'status' => $request->action === 'draft' ? 'draft' : 'unpaid',
                'total_amount' => 0, // Will update after items
                'tax_amount' => 0,
                'discount_amount' => $request->discount_amount ?? 0,
                'grand_total' => 0,
                'balance_amount' => 0,
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

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'amount' => $amount + $taxAmount,
                    'description' => $itemData['description'] ?? null,
                ]);

                $totalAmount += $amount;
                $totalTax += $taxAmount;

                // Stock movement is now handled by DeliveryNote
            }

            $discount = $request->discount_type === 'percent'
                ? ($totalAmount + $totalTax) * ($request->discount_amount / 100)
                : ($request->discount_amount ?? 0);

            $grandTotal = ($totalAmount + $totalTax) - $discount;

            $invoice->update([
                'total_amount' => $totalAmount,
                'tax_amount' => $totalTax,
                'grand_total' => $grandTotal,
                'balance_amount' => $grandTotal,
            ]);

            // If created from Order, update Order status
            if ($request->sales_order_id) {
                SalesOrder::find($request->sales_order_id)->update(['status' => 'invoiced']);
            }

            // Automated Accounting Posting
            if ($invoice->status !== 'draft') {
                app(AccountingService::class)->postSalesInvoice($invoice);
            }

            DB::commit();

            return redirect()->route('sales.invoices.index')->with('success', 'Invoice created successfully'.($invoice->status !== 'draft' ? ' and posted to ledger.' : ''));

        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: '.$e->getMessage())->withInput();
        }
    }

    public function show($id): View
    {
        $invoice = SalesInvoice::with(['items.item', 'customer', 'branch', 'creator', 'payments'])->findOrFail($id);

        return view('admin.sales.invoices.show', compact('invoice'));
    }

    public function edit($id): View
    {
        $invoice = SalesInvoice::with('items')->findOrFail($id);
        $customers = Customer::all();
        $items = Item::all();
        $branches = Branch::all();
        $stores = Store::all();
        $orders = SalesOrder::all();

        return view('admin.sales.invoices.edit', compact('invoice', 'customers', 'items', 'branches', 'stores', 'orders'));
    }

    public function update(Request $request, int $id): RedirectResponse
    {
        $invoice = SalesInvoice::findOrFail($id);

        $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|numeric|min:0.01',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            // Delete old items
            $invoice->items()->delete();

            $totalAmount = 0;
            $totalTax = 0;

            foreach ($request->items as $itemData) {
                $amount = $itemData['quantity'] * $itemData['unit_price'];
                $taxRate = $itemData['tax_rate'] ?? 0;
                $taxAmount = $amount * ($taxRate / 100);

                SalesInvoiceItem::create([
                    'sales_invoice_id' => $invoice->id,
                    'item_id' => $itemData['item_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'tax_rate' => $taxRate,
                    'tax_amount' => $taxAmount,
                    'amount' => $amount + $taxAmount,
                    'description' => $itemData['description'] ?? null,
                ]);

                $totalAmount += $amount;
                $totalTax += $taxAmount;
            }

            $discount = $request->discount_type === 'percent'
                ? ($totalAmount + $totalTax) * ($request->discount_amount / 100)
                : ($request->discount_amount ?? 0);

            $grandTotal = ($totalAmount + $totalTax) - $discount;

            $status = $request->action === 'draft' ? 'draft' : ($invoice->paid_amount > 0 ? ($invoice->paid_amount >= $grandTotal ? 'paid' : 'partially_paid') : 'unpaid');

            $invoice->update([
                'customer_id' => $request->customer_id,
                'invoice_date' => $request->invoice_date,
                'due_date' => $request->due_date,
                'status' => $status,
                'total_amount' => $totalAmount,
                'tax_amount' => $totalTax,
                'discount_amount' => $discount,
                'grand_total' => $grandTotal,
                'balance_amount' => $grandTotal - $invoice->paid_amount,
                'reference_no' => $request->reference_no,
                'notes' => $request->notes,
                'terms' => $request->terms,
                'branch_id' => $request->branch_id,
            ]);

            if ($status !== 'draft') {
                app(AccountingService::class)->postSalesInvoice($invoice);
            }

            DB::commit();

            return redirect()->route('sales.invoices.index')->with('success', 'Invoice updated successfully');

        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            DB::beginTransaction();
            $invoice = SalesInvoice::findOrFail($id);

            if ($invoice->paid_amount > 0) {
                throw new Exception('Cannot delete invoice with payments. Delete payments first.');
            }

            $invoice->items()->delete();
            $invoice->delete();

            DB::commit();

            return redirect()->route('sales.invoices.index')->with('success', 'Invoice deleted successfully');
        } catch (Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Error: '.$e->getMessage());
        }
    }
}
