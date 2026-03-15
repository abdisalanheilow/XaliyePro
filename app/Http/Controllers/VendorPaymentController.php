<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVendorPaymentRequest;
use App\Models\Account;
use App\Models\Branch;
use App\Models\PurchaseBill;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Services\AccountingService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorPaymentController extends Controller
{
    public function index(Request $request)
    {
        $query = VendorPayment::with(['vendor', 'bill', 'account', 'branch']);

        if ($request->search) {
            $search = $request->search;
            $query->whereNested(function (Builder $q) use ($search) {
                $q->where('payment_no', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function (Builder $vq) use ($search) {
                        $vq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);

        $stats = [
            'total_amount' => VendorPayment::sum('amount'),
            'total_count' => VendorPayment::count(),
            'today_amount' => VendorPayment::whereDate('payment_date', now())->sum('amount'),
            'month_amount' => VendorPayment::whereMonth('payment_date', now()->month)->sum('amount'),
        ];

        return view('admin.purchases.payments.index', compact('payments', 'stats'));
    }

    public function create(Request $request)
    {
        $vendors = Vendor::where('status', 'active')->orderBy('name', 'asc')->get();
        $branches = Branch::where('status', 'active')->orderBy('name', 'asc')->get();
        $accounts = Account::where('status', 'active')->whereIn('type', ['asset'])->get();

        $billId = $request->bill_id;
        $bill = $billId ? PurchaseBill::findOrFail($billId) : null;

        $bills = PurchaseBill::whereIn('status', ['unpaid', 'partially_paid', 'overdue'])->orderBy('bill_no', 'desc')->get();

        /** @var \App\Models\VendorPayment $lastPayment */
        $lastPayment = VendorPayment::orderBy('id', 'desc')->first();
        $nextId = $lastPayment ? $lastPayment->id + 1 : 1;
        $paymentNo = 'VPMT-'.date('Y').'-'.str_pad($nextId, 4, '0', STR_PAD_LEFT);

        return view('admin.purchases.payments.create', compact('vendors', 'branches', 'accounts', 'bill', 'bills', 'paymentNo'));
    }

    public function store(StoreVendorPaymentRequest $request)
    {

        try {
            return DB::transaction(function () use ($request) {
                $accountingService = app(AccountingService::class);

                $payment = $accountingService->recordVendorPayment([
                    'payment_no' => $request->payment_no,
                    'vendor_id' => $request->vendor_id,
                    'purchase_bill_id' => $request->purchase_bill_id,
                    'payment_date' => $request->payment_date,
                    'amount' => $request->amount,
                    'payment_method' => $request->payment_method,
                    'account_id' => $request->account_id,
                    'reference_no' => $request->reference_no,
                    'notes' => $request->notes,
                    'branch_id' => $request->branch_id,
                ]);

                return redirect()->route('purchases.payments.index')->with([
                    'message' => 'Payment recorded successfully',
                    'title' => 'Payment Recorded',
                    'alert-type' => 'success',
                ]);
            });
        } catch (\Exception $e) {
            return back()->withInput()->with([
                'message' => 'Error: '.$e->getMessage(),
                'title' => 'Error Occurred',
                'alert-type' => 'error',
            ]);
        }
    }

    public function show($id)
    {
        $payment = VendorPayment::with(['vendor', 'bill', 'account', 'branch'])->findOrFail($id);

        return view('admin.purchases.payments.show', compact('payment'));
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $payment = VendorPayment::findOrFail($id);

                // If linked to bill, reverse the totals
                if ($payment->purchase_bill_id) {
                    $bill = PurchaseBill::findOrFail($payment->purchase_bill_id);
                    $bill->paid_amount -= $payment->amount;
                    $bill->balance_amount = $bill->grand_total - $bill->paid_amount;

                    if ($bill->paid_amount <= 0) {
                        $bill->status = 'unpaid';
                        $bill->paid_amount = 0;
                    } else {
                        $bill->status = 'partially_paid';
                    }
                    $bill->save();
                }

                $payment->delete();

                return redirect()->route('purchases.payments.index')->with([
                    'message' => 'Payment deleted successfully',
                    'title' => 'Payment Deleted',
                    'alert-type' => 'success',
                ]);
            });
        } catch (\Exception $e) {
            return back()->with([
                'message' => 'Error deleting payment. Please try again.',
                'alert-type' => 'error',
            ]);
        }
    }
}
