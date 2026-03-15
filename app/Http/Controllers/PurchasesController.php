<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePurchaseBillRequest;
use App\Models\Branch;
use App\Models\GoodsReceipt;
use App\Models\Item;
use App\Models\PurchaseBill;
use App\Models\PurchaseBillItem;
use App\Models\Store;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Services\AccountingService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PurchasesController extends Controller
{
    public function bills(Request $request): View
    {
        $query = PurchaseBill::query()->with(['vendor', 'branch', 'store']);

        // Apply filters
        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('bill_no', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function (Builder $vq) use ($search) {
                        $vq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->status && $request->status !== 'All Status') {
            $query->where('status', strtolower($request->status));
        }

        $bills = $query->orderBy('bill_date', 'desc')->paginate(15)->withQueryString();

        // Stats
        $stats = [
            'total_amount' => PurchaseBill::sum('grand_total'),
            'total_count' => PurchaseBill::count(),
            'paid_amount' => PurchaseBill::sum('paid_amount'),
            'paid_count' => PurchaseBill::where('status', 'paid')->count(),
            'unpaid_amount' => PurchaseBill::whereIn('status', ['unpaid', 'partially_paid', 'overdue'])->sum('balance_amount'),
            'unpaid_count' => PurchaseBill::whereIn('status', ['unpaid', 'partially_paid', 'overdue'])->count(),
            'overdue_amount' => PurchaseBill::where('status', 'overdue')->sum('balance_amount'),
            'overdue_count' => PurchaseBill::where('status', 'overdue')->count(),
        ];

        return view('admin.purchases.purchase_bills', compact('bills', 'stats'));
    }

    public function create(Request $request): View
    {
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $stores = Store::where('status', 'active')->orderBy('name', 'asc')->get();

        /** @var \App\Models\PurchaseBill $lastBill */
        $lastBill = PurchaseBill::orderBy('id', 'desc')->first();
        $nextId = $lastBill ? $lastBill->id + 1 : 1;
        $billNo = 'BILL-'.date('Y').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        $selectedReceipt = null;
        if ($request->has('receipt_id')) {
            $selectedReceipt = GoodsReceipt::with(['items.item', 'vendor', 'order'])->find($request->receipt_id);
        }

        return view('admin.purchases.create_bill', compact('vendors', 'items', 'billNo', 'branches', 'stores', 'selectedReceipt'));
    }

    public function store(StorePurchaseBillRequest $request): RedirectResponse
    {

        try {
            return DB::transaction(function () use ($request) {
                $subtotal = 0;
                $totalTax = 0;

                // Calculate Totals
                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    $subtotal += $itemSub;
                    $totalTax += $itemTax;
                }

                // Discount
                $discountVal = $request->discount_val ?? 0;
                $discountAmt = $request->discount_type === 'percent'
                    ? ($subtotal + $totalTax) * ($discountVal / 100)
                    : $discountVal;

                $grandTotal = ($subtotal + $totalTax) - $discountAmt;
                $status = $request->action === 'draft' ? 'draft' : 'unpaid';

                /** @var \App\Models\PurchaseBill $bill */
                $bill = PurchaseBill::create([
                    'bill_no' => $request->bill_no,
                    'reference_no' => $request->reference_no,
                    'vendor_id' => $request->vendor_id,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                    'bill_date' => $request->bill_date,
                    'due_date' => $request->due_date,
                    'payment_terms' => $request->payment_terms,
                    'total_amount' => $subtotal,
                    'tax_amount' => $totalTax,
                    'discount_amount' => $discountAmt,
                    'grand_total' => $grandTotal,
                    'paid_amount' => 0,
                    'balance_amount' => $grandTotal,
                    'status' => $status,
                    'notes' => $request->notes,
                    'goods_receipt_id' => $request->goods_receipt_id,
                    'purchase_order_id' => $request->purchase_order_id,
                    'created_by' => auth()->id(),
                ]);

                // Create Items
                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    PurchaseBillItem::create([
                        'purchase_bill_id' => $bill->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_amount' => $itemTax,
                        'amount' => $itemSub + $itemTax,
                    ]);
                }

                // Automated Accounting Posting
                if ($status !== 'draft') {
                    app(AccountingService::class)->postPurchaseBill($bill);
                }

                return redirect()->route('purchases.bills.index')->with([
                    'message' => 'Purchase bill created successfully'.($status !== 'draft' ? ' and posted to ledger.' : ''),
                    'title' => 'Bill Created',
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
        $bill = PurchaseBill::with(['vendor', 'items.item', 'branch', 'store'])->findOrFail($id);

        return view('admin.purchases.show_bill', compact('bill'));
    }

    public function edit($id): View
    {
        $bill = PurchaseBill::with('items')->findOrFail($id);
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $items = Item::where('type', 'product')->where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $stores = Store::where('status', 'active')->orderBy('name', 'asc')->get();

        return view('admin.purchases.edit_bill', compact('bill', 'vendors', 'items', 'branches', 'stores'));
    }

    public function update(StorePurchaseBillRequest $request, $id): RedirectResponse
    {
        $bill = PurchaseBill::findOrFail($id);

        try {
            return DB::transaction(function () use ($request, $bill) {
                $subtotal = 0;
                $totalTax = 0;

                // Calculate Totals
                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    $subtotal += $itemSub;
                    $totalTax += $itemTax;
                }

                // Discount
                $discountVal = $request->discount_val ?? 0;
                $discountAmt = $request->discount_type === 'percent'
                    ? ($subtotal + $totalTax) * ($discountVal / 100)
                    : $discountVal;

                $grandTotal = ($subtotal + $totalTax) - $discountAmt;

                // If the bill was already partially paid, we keep that, but update balance
                $paidAmount = $bill->paid_amount;
                $balanceAmount = $grandTotal - $paidAmount;

                $status = $bill->status;
                if ($request->action !== 'draft' && $status === 'draft') {
                    $status = 'unpaid';
                }

                $bill->update([
                    'reference_no' => $request->reference_no,
                    'vendor_id' => $request->vendor_id,
                    'branch_id' => $request->branch_id,
                    'store_id' => $request->store_id,
                    'bill_date' => $request->bill_date,
                    'due_date' => $request->due_date,
                    'payment_terms' => $request->payment_terms,
                    'total_amount' => $subtotal,
                    'tax_amount' => $totalTax,
                    'discount_amount' => $discountAmt,
                    'grand_total' => $grandTotal,
                    'balance_amount' => max(0, $balanceAmount),
                    'status' => $status,
                    'notes' => $request->notes,
                ]);

                // Update Items - Delete old and create new is safer for complicated forms
                $bill->items()->delete();
                foreach ($request->items as $item) {
                    $itemSub = $item['quantity'] * $item['unit_price'];
                    $taxRate = $item['tax_rate'] ?? 0;
                    $itemTax = $itemSub * ($taxRate / 100);

                    PurchaseBillItem::create([
                        'purchase_bill_id' => $bill->id,
                        'item_id' => $item['item_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_amount' => $itemTax,
                        'amount' => $itemSub + $itemTax,
                    ]);
                }

                return redirect()->route('purchases.bills.index')->with([
                    'message' => 'Purchase bill updated successfully',
                    'title' => 'Bill Updated',
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
        try {
            $bill = PurchaseBill::findOrFail($id);

            // Prevent deleting bills that have payments linked
            $paymentCount = VendorPayment::where('purchase_bill_id', $bill->id)->count();
            if ($paymentCount > 0) {
                return redirect()->route('purchases.bills.index')->with([
                    'message' => "Cannot delete this bill — it has {$paymentCount} payment(s) recorded against it. Delete the payments first.",
                    'title' => 'Deletion Blocked',
                    'alert-type' => 'error',
                ]);
            }

            return DB::transaction(function () use ($bill) {
                $bill->items()->delete();
                $bill->delete();

                return redirect()->route('purchases.bills.index')->with([
                    'message' => 'Purchase bill deleted successfully',
                    'title' => 'Bill Deleted',
                    'alert-type' => 'success',
                ]);
            });
        } catch (Exception $e) {
            return redirect()->route('purchases.bills.index')->with([
                'message' => 'Error deleting bill. Please try again.',
                'title' => 'Error',
                'alert-type' => 'error',
            ]);
        }
    }
}
