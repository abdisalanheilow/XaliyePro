<?php

namespace App\Http\Controllers;
use App\Models\Account;
use App\Models\Category;
use App\Models\CompanySetting;
use App\Models\Customer;
use App\Models\CustomerPayment;
use App\Models\Item;
use App\Models\JournalItem;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\StockMove;
use App\Models\Store;
use App\Models\StoreItemStock;
use App\Models\User;
use App\Models\Vendor;
use App\Models\VendorPayment;
use App\Services\AccountingService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $accounting;

    public function __construct(AccountingService $accounting)
    {
        $this->accounting = $accounting;
    }

    public function dashboard(): View
    {
        $settings = CompanySetting::first();
        
        // Dynamic stats for the dashboard
        $stats = [
            'total_sales' => SalesInvoice::where('status', '!=', 'cancelled')->sum('grand_total'),
            'total_purchases' => PurchaseBill::where('status', '!=', 'cancelled')->sum('grand_total'),
            'total_customers' => Customer::count(),
            'total_vendors' => Vendor::count(),
            'stock_value' => (function() {
                /** @var object $res */
                $res = StoreItemStock::join('items', 'store_item_stocks.item_id', '=', 'items.id')
                    ->selectRaw('SUM(store_item_stocks.current_stock * items.cost_price) as value')
                    ->first();
                return $res->value ?? 0;
            })(),
            'low_stock_count' => Item::whereRaw('current_stock <= reorder_level')->count(),
        ];

        return view('admin.reports.dashboard', compact('settings', 'stats'));
    }

    public function balanceSheet(Request $request): View
    {
        $reportDate = $request->input('report_date', date('Y-m-d'));

        // Optimized bulk balance retrieval
        $allBalances = $this->accounting->getBalancesAsOf($reportDate);

        // Filter helper
        /** @var Collection $allBalances */
        $getAccountsByBalance = function (string $type) use ($allBalances): Collection {
            return $allBalances->filter(function ($account) use ($type) {
                return $account->type === $type && $account->current_balance != 0;
            });
        };

        // Retrieve Assets
        $assets = $getAccountsByBalance('asset');

        $currentAssets = $assets->filter(function ($account) {
            return in_array($account->sub_type, ['Bank and Cash', 'Accounts Receivable', 'Inventory', 'Prepayments', 'Current Asset']);
        });

        $fixedAssets = $assets->filter(function ($account) {
            return in_array($account->sub_type, ['Fixed Asset']);
        });

        $otherAssets = $assets->filter(function ($account) {
            return ! in_array($account->sub_type, ['Bank and Cash', 'Accounts Receivable', 'Inventory', 'Prepayments', 'Current Asset', 'Fixed Asset']);
        });

        $totalCurrentAssets = $currentAssets->sum('current_balance');
        $totalFixedAssets = $fixedAssets->sum('current_balance');
        $totalOtherAssets = $otherAssets->sum('current_balance');
        $totalAssets = $totalCurrentAssets + $totalFixedAssets + $totalOtherAssets;

        // Retrieve Liabilities
        $liabilities = $getAccountsByBalance('liability');

        $currentLiabilities = $liabilities->filter(function ($account) {
            return in_array($account->sub_type, ['Accounts Payable', 'Credit Card', 'Tax Payable', 'Payroll Payable', 'Current Liability']);
        });

        $longTermLiabilities = $liabilities->filter(function ($account) {
            return ! in_array($account->sub_type, ['Accounts Payable', 'Credit Card', 'Tax Payable', 'Payroll Payable', 'Current Liability']);
        });

        $totalCurrentLiabilities = $currentLiabilities->sum('current_balance');
        $totalLongTermLiabilities = $longTermLiabilities->sum('current_balance');
        $totalLiabilities = $totalCurrentLiabilities + $totalLongTermLiabilities;

        // Retrieve Equity
        $equities = $getAccountsByBalance('equity');

        $totalEquityExcludingProfit = $equities->sum('current_balance');

        // Calculate Current Year Profit (Revenue - Expense) up to the date
        $revenues = $getAccountsByBalance('revenue');
        $expenses = $getAccountsByBalance('expense');

        $totalRevenue = $revenues->sum('current_balance');
        $totalExpense = $expenses->sum('current_balance');

        $currentYearProfit = $totalRevenue - $totalExpense;

        $totalEquity = $totalEquityExcludingProfit + $currentYearProfit;

        $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;

        $settings = CompanySetting::first();

        return view('admin.reports.balance_sheet', compact(
            'currentAssets',
            'fixedAssets',
            'otherAssets',
            'totalCurrentAssets',
            'totalFixedAssets',
            'totalOtherAssets',
            'totalAssets',
            'currentLiabilities',
            'longTermLiabilities',
            'totalCurrentLiabilities',
            'totalLongTermLiabilities',
            'totalLiabilities',
            'equities',
            'totalEquityExcludingProfit',
            'currentYearProfit',
            'totalEquity',
            'totalLiabilitiesAndEquity',
            'settings',
            'reportDate'
        ));
    }

    public function profitLoss(Request $request): View
    {
        $settings = CompanySetting::first();

        // Report Filters
        $fromDate = $request->input('from_date', $request->input('start_date', date('Y-m-01')));
        $toDate = $request->input('to_date', $request->input('end_date', date('Y-m-d')));

        // Optimized bulk balance retrieval for period
        $periodBalances = $this->accounting->getBalancesForPeriod($fromDate, $toDate);

        // 1. REVENUE
        $revenueAccounts = $periodBalances->filter(fn (Account $a) => $a->type === 'revenue' && $a->period_balance != 0);

        $operatingRevenue = $revenueAccounts->filter(function ($a) {
            return in_array($a->sub_type, ['Operating Revenue', 'Service Income', 'Item Income']);
        });

        $discountsAndReturns = $revenueAccounts->filter(function ($a) {
            return in_array($a->sub_type, ['Discounts Given']);
        });

        $otherRevenue = $revenueAccounts->filter(function ($a) {
            return ! in_array($a->sub_type, ['Operating Revenue', 'Service Income', 'Item Income', 'Discounts Given']);
        });

        $totalOperatingRevenue = $operatingRevenue->sum('period_balance') - abs($discountsAndReturns->sum('period_balance'));
        $totalOtherRevenue = $otherRevenue->sum('period_balance');
        $totalRevenue = $totalOperatingRevenue + $totalOtherRevenue;

        // 2. COST OF GOODS SOLD
        $expenseAccounts = $periodBalances->filter(fn (Account $a) => $a->type === 'expense' && $a->period_balance != 0);

        $cogsAccounts = $expenseAccounts->filter(function ($a) {
            return str_contains(strtolower($a->sub_type), 'cogs') || str_contains(strtolower($a->name), 'cost of goods');
        });
        $totalCOGS = $cogsAccounts->sum('period_balance');

        // 3. GROSS PROFIT
        $grossProfit = $totalRevenue - $totalCOGS;
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // 4. OPERATING EXPENSES
        $allExpenses = $expenseAccounts->filter(function ($a) use ($cogsAccounts) {
            return ! $cogsAccounts->contains('id', $a->id);
        });

        // Use keywords or sub-type logic to split Selling vs Admin
        $sellingKeywords = ['advertis', 'market', 'commiss', 'deliver', 'selling'];
        $sellingExpenses = $allExpenses->filter(function (Account $a) use ($sellingKeywords) {
            $n = strtolower($a->name);
            foreach ($sellingKeywords as $kw) {
                if (str_contains($n, $kw)) {
                    return true;
                }
            }

            return false;
        });

        $adminExpenses = $allExpenses->filter(function (Account $a) use ($sellingExpenses) {
            return ! $sellingExpenses->contains('id', $a->id);
        });

        $totalSellingExpenses = $sellingExpenses->sum('period_balance');
        $totalAdminExpenses = $adminExpenses->sum('period_balance');
        $totalOperatingExpenses = $totalSellingExpenses + $totalAdminExpenses;

        // 5. OPERATING INCOME
        $operatingIncome = $grossProfit - $totalOperatingExpenses;
        $operatingMargin = $totalRevenue > 0 ? ($operatingIncome / $totalRevenue) * 100 : 0;

        // 7. NET INCOME BEFORE TAX
        $netIncomeBeforeTax = $operatingIncome;
        $incomeTaxExpense = $netIncomeBeforeTax > 0 ? $netIncomeBeforeTax * 0.30 : 0;
        $netIncome = $netIncomeBeforeTax - $incomeTaxExpense;
        $netProfitMargin = $totalRevenue > 0 ? ($netIncome / $totalRevenue) * 100 : 0;

        return view('admin.reports.profit_loss', compact(
            'settings',
            'fromDate',
            'toDate',
            'operatingRevenue',
            'discountsAndReturns',
            'otherRevenue',
            'totalOperatingRevenue',
            'totalOtherRevenue',
            'totalRevenue',
            'cogsAccounts',
            'totalCOGS',
            'grossProfit',
            'grossProfitMargin',
            'sellingExpenses',
            'adminExpenses',
            'totalSellingExpenses',
            'totalAdminExpenses',
            'totalOperatingExpenses',
            'operatingIncome',
            'operatingMargin',
            'netIncomeBeforeTax',
            'incomeTaxExpense',
            'netIncome',
            'netProfitMargin'
        ));
    }

    public function trialBalance(Request $request): View
    {
        $settings = CompanySetting::first();
        $reportDate = $request->input('report_date', date('Y-m-d'));

        // Optimized bulk retrieval
        $accounts = $this->accounting->getBalancesAsOf($reportDate)->sortBy('code');
        $trialBalanceData = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $balance = $account->current_balance;

            if ($balance == 0) {
                continue;
            }

            $dr = 0;
            $cr = 0;

            if (in_array($account->type, ['asset', 'expense'])) {
                if ($balance > 0) {
                    $dr = $balance;
                } else {
                    $cr = abs($balance);
                }
            } else {
                if ($balance > 0) {
                    $cr = $balance;
                } else {
                    $dr = abs($balance);
                }
            }

            $trialBalanceData[] = [
                'code' => $account->code,
                'name' => $account->name,
                'debit' => $dr,
                'credit' => $cr,
            ];

            $totalDebit += (float) $dr;
            $totalCredit += (float) $cr;
        }

        return view('admin.reports.trial_balance', compact('settings', 'reportDate', 'trialBalanceData', 'totalDebit', 'totalCredit'));
    }

    public function cashFlow(Request $request): View
    {
        $settings = CompanySetting::first();
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        // Optimized bulk retrieval for period
        $periodBalances = $this->accounting->getBalancesForPeriod($fromDate, $toDate);

        // Helper: get movement for period
        $getMovement = function ($subType) use ($periodBalances) {
            return $periodBalances->filter(fn ($a) => $a->sub_type === $subType)->sum('period_balance');
        };

        // 1. Net Income Calculation
        $revenue = $periodBalances->filter(fn ($a) => $a->type === 'revenue')->sum('period_balance');
        $expense = $periodBalances->filter(fn ($a) => $a->type === 'expense')->sum('period_balance');
        $netIncome = $revenue - $expense;

        // 2. Adjustments for Non-Cash Items (Depreciation)
        $depreciation = $getMovement('Depreciation');

        // 3. Changes in Working Capital
        $arMovement = $getMovement('Accounts Receivable');
        $inventoryMovement = $getMovement('Inventory');
        $apMovement = $getMovement('Accounts Payable');
        $taxPayableMovement = $getMovement('Tax Payable');

        $netOperatingCash = $netIncome + $depreciation - $arMovement - $inventoryMovement + $apMovement + $taxPayableMovement;

        // 4. Investing Activities
        $fixedAssetMovement = $getMovement('Fixed Asset');
        $netInvestingCash = -$fixedAssetMovement;

        // 5. Financing Activities
        $clMovement = $getMovement('Current Liability');
        $nclMovement = $getMovement('Non-current Liability');
        $loanMovement = $clMovement + $nclMovement;
        $equityMovement = $periodBalances->filter(fn ($a) => $a->type === 'equity')->sum('period_balance');
        $netFinancingCash = $loanMovement + $equityMovement;

        // 6. Cash Reconciliation
        $prevDay = date('Y-m-d', strtotime('-1 day', strtotime($fromDate)));
        $startBalances = $this->accounting->getBalancesAsOf($prevDay);
        $endBalances = $this->accounting->getBalancesAsOf($toDate);

        $cashAtStart = $startBalances->filter(fn ($a) => $a->sub_type === 'Bank and Cash')->sum('current_balance');
        $actualCashAtEnd = $endBalances->filter(fn ($a) => $a->sub_type === 'Bank and Cash')->sum('current_balance');

        $netCashChange = $netOperatingCash + $netInvestingCash + $netFinancingCash;
        $cashAtEnd = $cashAtStart + $netCashChange;

        return view('admin.reports.cash_flow', compact(
            'settings',
            'fromDate',
            'toDate',
            'netIncome',
            'depreciation',
            'arMovement',
            'inventoryMovement',
            'apMovement',
            'taxPayableMovement',
            'netOperatingCash',
            'netInvestingCash',
            'netFinancingCash',
            'fixedAssetMovement',
            'loanMovement',
            'equityMovement',
            'cashAtStart',
            'netCashChange',
            'cashAtEnd',
            'actualCashAtEnd'
        ));
    }

    public function revenueTrends(Request $request): View
    {
        $settings = CompanySetting::first();

        $data = [];
        $months = [];

        for ($i = 11; $i >= 0; $i--) {
            $monthDate = Carbon::now()->subMonths($i);
            $monthName = $monthDate->format('M Y');
            $months[] = $monthName;

            $revenue = SalesInvoice::whereYear('invoice_date', $monthDate->year)
                ->whereMonth('invoice_date', $monthDate->month)
                ->where('status', '!=', 'cancelled')
                ->sum('grand_total');

            $data[] = (float) $revenue;
        }

        return view('admin.reports.revenue_trends', compact('settings', 'data', 'months'));
    }

    public function salesSummary(Request $request): View
    {
        $settings = CompanySetting::first();
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        $totalSales = SalesInvoice::whereBetween('invoice_date', [$fromDate, $toDate])
            ->where('status', '!=', 'cancelled')
            ->sum('grand_total');

        $topItems = SalesInvoiceItem::whereHas('invoice', function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('invoice_date', [$fromDate, $toDate])->where('status', '!=', 'cancelled');
        })
            ->selectRaw('item_id, SUM(quantity) as qty, SUM(amount) as revenue')
            ->groupBy('item_id')
            ->orderBy('qty', 'DESC')
            ->with('item')
            ->limit(5)
            ->get();

        $topCustomers = Customer::withSum(['invoices' => function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('invoice_date', [$fromDate, $toDate])->where('status', '!=', 'cancelled');
        }], 'grand_total')
            ->orderBy('invoices_sum_grand_total', 'DESC')
            ->limit(5)
            ->get();

        return view('admin.reports.sales_summary', compact('settings', 'fromDate', 'toDate', 'totalSales', 'topItems', 'topCustomers'));
    }

    public function salesByCustomer(Request $request): View
    {
        $settings = CompanySetting::first();

        $customers = Customer::withSum(['invoices' => function ($q) {
            $q->where('status', '!=', 'cancelled');
        }], 'grand_total')
            ->withSum(['invoices' => function ($q) {
                $q->where('status', '!=', 'cancelled');
            }], 'balance_amount')
            ->get()
            ->map(function ($c) {
                $c->total_sales = $c->invoices_sum_grand_total ?? 0;
                $c->balance_amount = $c->invoices_sum_balance_amount ?? 0;

                return $c;
            });

        return view('admin.reports.sales_by_customer', compact('settings', 'customers'));
    }


    public function salesByItem(Request $request): View
    {
        $settings = CompanySetting::first();
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        $salesData = SalesInvoiceItem::whereHas('invoice', function ($q) use ($fromDate, $toDate) {
            $q->whereBetween('invoice_date', [$fromDate, $toDate])->where('status', '!=', 'cancelled');
        })
            ->selectRaw('item_id, SUM(quantity) as total_qty, SUM(amount) as total_revenue')
            ->groupBy('item_id')
            ->with('item.category')
            ->get();

        $totalRevenue = $salesData->sum('total_revenue');
        $totalQty = $salesData->sum('total_qty');
        $topItem = $salesData->sortByDesc('total_revenue')->first();

        return view('admin.reports.sales_by_item', compact('settings', 'fromDate', 'toDate', 'salesData', 'totalRevenue', 'totalQty', 'topItem'));
    }

    /**
     * Inventory: Stock On Hand
     */
    public function stockOnHand(Request $request): View
    {
        $settings = CompanySetting::first();
        $storeId = $request->input('store_id');
        $categoryId = $request->input('category_id');

        $query = StoreItemStock::with(['item.category', 'store']);

        if ($storeId) {
            $query->where('store_id', $storeId);
        }
        if ($categoryId) {
            $query->whereHas('item', fn ($q) => $q->where('category_id', $categoryId));
        }

        $stocks = $query->get()->filter(fn ($s) => $s->item && $s->item->track_inventory);
        $stores = Store::all();
        $categories = Category::all();

        return view('admin.reports.stock_on_hand', compact('settings', 'stocks', 'stores', 'categories', 'storeId', 'categoryId'));
    }

    /**
     * Inventory: Valuation
     */
    public function inventoryValuation(Request $request): View
    {
        $settings = CompanySetting::first();
        $storeId = $request->input('store_id');

        $query = Item::where('track_inventory', true)
            ->with(['category', 'stocks' => function ($q) use ($storeId) {
                if ($storeId) {
                    $q->where('store_id', $storeId);
                }
            }]);

        $items = $query->get();
        $stores = Store::all();

        $valuationData = $items->map(function ($item) {
            $qty = $item->stocks->sum('current_stock');

            return [
                'name' => $item->name,
                'sku' => $item->sku,
                'category' => $item->category->name ?? 'N/A',
                'qty' => $qty,
                'cost' => $item->cost_price,
                'valuation' => $qty * $item->cost_price,
            ];
        });

        $totalValuation = $valuationData->sum('valuation');

        return view('admin.reports.inventory_valuation', compact('settings', 'valuationData', 'totalValuation', 'stores', 'storeId'));
    }

    /**
     * Inventory: Stock Movement (Audit Trail)
     */
    public function stockMovement(Request $request): View
    {
        $settings = CompanySetting::first();
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));
        $itemId = $request->input('item_id');
        $storeId = $request->input('store_id');

        $query = StockMove::with(['item', 'store', 'creator'])
            ->whereBetween('created_at', [
                Carbon::parse($fromDate)->startOfDay(),
                Carbon::parse($toDate)->endOfDay(),
            ]);

        if ($itemId) {
            $query->where('item_id', $itemId);
        }
        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $movements = $query->orderBy('created_at', 'DESC')->get();
        $items = Item::where('track_inventory', true)->get();
        $stores = Store::all();

        return view('admin.reports.stock_movement', compact('settings', 'movements', 'items', 'stores', 'fromDate', 'toDate', 'itemId', 'storeId'));
    }

    /**
     * Inventory: Low Stock Report
     */
    public function lowStockReport(Request $request): View
    {
        $settings = CompanySetting::first();
        $storeId = $request->input('store_id');

        $query = StoreItemStock::with(['item', 'store'])
            ->whereRaw('current_stock <= reorder_level');

        if ($storeId) {
            $query->where('store_id', $storeId);
        }

        $lowStocks = $query->get();
        $stores = Store::all();

        return view('admin.reports.low_stock', compact('settings', 'lowStocks', 'stores', 'storeId'));
    }

    /**
     * Purchases: Summary
     */
    public function purchaseSummary(Request $request): View
    {
        $settings = CompanySetting::first();
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        $bills = PurchaseBill::whereBetween('bill_date', [$fromDate, $toDate])
            ->where('status', '!=', 'cancelled')
            ->get();

        $totalPurchases = $bills->sum('grand_total');
        $totalPaid = $bills->sum('paid_amount');
        $totalBalance = $bills->sum('balance_amount');

        return view('admin.reports.purchase_summary', compact('settings', 'fromDate', 'toDate', 'totalPurchases', 'totalPaid', 'totalBalance'));
    }

    /**
     * Purchases: By Vendor
     */
    public function purchasesByVendor(Request $request): View
    {
        $settings = CompanySetting::first();
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        $vendorData = PurchaseBill::whereBetween('bill_date', [$fromDate, $toDate])
            ->where('status', '!=', 'cancelled')
            ->selectRaw('vendor_id, COUNT(*) as bill_count, SUM(grand_total) as total_amount, SUM(paid_amount) as total_paid')
            ->groupBy('vendor_id')
            ->with('vendor')
            ->get();

        return view('admin.reports.purchase_by_vendor', compact('settings', 'fromDate', 'toDate', 'vendorData'));
    }

    /**
     * Financial: Customer Statement
     */
    public function customerStatement(Request $request): View
    {
        $settings = CompanySetting::first();
        $customerId = $request->input('customer_id');
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        $customer = $customerId ? Customer::find($customerId) : null;
        $transactions = collect();
        $openingBalance = 0;

        if ($customer) {
            // Opening Balance Logic
            $prevInvoices = SalesInvoice::where('customer_id', $customerId)
                ->where('invoice_date', '<', $fromDate)
                ->where('status', '!=', 'cancelled')
                ->sum('grand_total');
            $prevPayments = CustomerPayment::where('customer_id', $customerId)
                ->where('payment_date', '<', $fromDate)
                ->sum('amount');
            $openingBalance = $prevInvoices - $prevPayments;

            // Current Transactions
            $invoices = SalesInvoice::where('customer_id', $customerId)
                ->whereBetween('invoice_date', [$fromDate, $toDate])
                ->where('status', '!=', 'cancelled')
                ->get()->map(fn ($i) => [
                    'date' => $i->invoice_date,
                    'type' => 'Invoice',
                    'reference' => $i->invoice_no,
                    'debit' => $i->grand_total,
                    'credit' => 0,
                ]);

            $payments = CustomerPayment::where('customer_id', $customerId)
                ->whereBetween('payment_date', [$fromDate, $toDate])
                ->get()->map(fn ($p) => [
                    'date' => $p->payment_date,
                    'type' => 'Payment',
                    'reference' => $p->payment_no,
                    'debit' => 0,
                    'credit' => $p->amount,
                ]);

            $transactions = $invoices->concat($payments)->sortBy('date');
        }

        $customers = Customer::all();

        return view('admin.reports.customer_statement', compact('settings', 'customer', 'customers', 'fromDate', 'toDate', 'transactions', 'openingBalance', 'customerId'));
    }

    /**
     * Financial: Vendor Statement
     */
    public function vendorStatement(Request $request): View
    {
        $settings = CompanySetting::first();
        $vendorId = $request->input('vendor_id');
        $fromDate = $request->input('from_date', date('Y-m-01'));
        $toDate = $request->input('to_date', date('Y-m-d'));

        $vendor = $vendorId ? Vendor::find($vendorId) : null;
        $transactions = collect();
        $openingBalance = 0;

        if ($vendor) {
            $prevBills = PurchaseBill::where('vendor_id', $vendorId)
                ->where('bill_date', '<', $fromDate)
                ->where('status', '!=', 'cancelled')
                ->sum('grand_total');
            $prevPayments = VendorPayment::where('vendor_id', $vendorId)
                ->where('payment_date', '<', $fromDate)
                ->sum('amount');
            $openingBalance = $prevBills - $prevPayments;

            $bills = PurchaseBill::where('vendor_id', $vendorId)
                ->whereBetween('bill_date', [$fromDate, $toDate])
                ->where('status', '!=', 'cancelled')
                ->get()->map(fn ($b) => [
                    'date' => $b->bill_date,
                    'type' => 'Bill',
                    'reference' => $b->bill_no,
                    'debit' => $b->grand_total,
                    'credit' => 0,
                ]);

            $payments = VendorPayment::where('vendor_id', $vendorId)
                ->whereBetween('payment_date', [$fromDate, $toDate])
                ->get()->map(fn ($p) => [
                    'date' => $p->payment_date,
                    'type' => 'Payment',
                    'reference' => $p->payment_no,
                    'debit' => 0,
                    'credit' => $p->amount,
                ]);

            $transactions = $bills->concat($payments)->sortBy('date');
        }

        $vendors = Vendor::all();

        return view('admin.reports.vendor_statement', compact('settings', 'vendor', 'vendors', 'fromDate', 'toDate', 'transactions', 'openingBalance', 'vendorId'));
    }
}
