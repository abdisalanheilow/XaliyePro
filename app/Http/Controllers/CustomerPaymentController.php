<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerPaymentRequest;
use App\Models\Account;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\SalesInvoice;
use App\Services\AccountingService;
use Illuminate\Support\Facades\DB;

class CustomerPaymentController extends Controller
{
    public function index()
    {
        $payments = CustomerPayment::with(['customer', 'invoice'])->latest()->paginate(10);

        $stats = [
            'total_amount' => CustomerPayment::sum('amount'),
            'total_count' => CustomerPayment::count(),
            'today_count' => CustomerPayment::whereDate('payment_date', now())->count(),
            'today_amount' => CustomerPayment::whereDate('payment_date', now())->sum('amount'),
        ];

        return view('admin.sales.payments.index', compact('payments', 'stats'));
    }

    public function create()
    {
        $customers = Customer::all();
        $invoices = SalesInvoice::whereIn('status', ['unpaid', 'partially_paid'])->get();
        $accounts = Account::where('type', 'asset')
            ->where('status', 'active')
            ->whereIn('sub_type', ['bank', 'cash', 'mobile_money'])
            ->get();

        // Generate Payment Number
        /** @var \App\Models\CustomerPayment $lastPayment */
        $lastPayment = CustomerPayment::latest()->first();
        $nextId = $lastPayment ? $lastPayment->id + 1 : 1;
        $paymentNo = 'PAY-IN-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return view('admin.sales.payments.create', compact('customers', 'invoices', 'accounts', 'paymentNo'));
    }

    public function store(StoreCustomerPaymentRequest $request)
    {

        try {
            return DB::transaction(function () use ($request) {
                $payment = app(AccountingService::class)->recordCustomerPayment($request->all());

                return redirect()->route('sales.payments.index')->with([
                    'message' => 'Payment recorded successfully',
                    'alert-type' => 'success',
                ]);
            });
        } catch (\Exception $e) {
            return back()->with([
                'message' => 'Error recording payment. Please try again.',
                'alert-type' => 'error',
            ])->withInput();
        }
    }

    public function show($id)
    {
        $payment = CustomerPayment::with(['customer', 'invoice', 'account'])->findOrFail($id);

        return view('admin.sales.payments.show', compact('payment'));
    }

    public function destroy($id)
    {
        try {
            return DB::transaction(function () use ($id) {
                $payment = CustomerPayment::findOrFail($id);

                // Reverse invoice balance if linked
                if ($payment->sales_invoice_id) {
                    $invoice = SalesInvoice::findOrFail($payment->sales_invoice_id);
                    $invoice->paid_amount -= $payment->amount;
                    $invoice->balance_amount = $invoice->grand_total - $invoice->paid_amount;

                    if ($invoice->paid_amount <= 0) {
                        $invoice->status = 'unpaid';
                        $invoice->paid_amount = 0;
                    } else {
                        $invoice->status = 'partially_paid';
                    }
                    $invoice->save();
                }

                $payment->delete();

                return redirect()->route('sales.payments.index')->with([
                    'message' => 'Payment deleted and balances reversed.',
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
