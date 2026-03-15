<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\PurchaseBill;
use App\Models\PurchaseOrder;
use App\Models\Vendor;
use Illuminate\Contracts\View\View;

class PurchaseDashboardController extends Controller
{
    public function index(): View
    {
        // 1. KPI Calculations
        $pendingReceipts = PurchaseOrder::where('status', 'pending')->count();
        $pendingReceiptValue = PurchaseOrder::where('status', 'pending')->sum('grand_total');

        $unpaidBills = PurchaseBill::whereIn('status', ['unpaid', 'partially_paid', 'overdue'])->count();
        $unpaidBillAmount = PurchaseBill::whereIn('status', ['unpaid', 'partially_paid', 'overdue'])->sum('balance_amount');

        $criticalStockCount = Item::where('type', 'product')
            ->whereRaw('current_stock <= reorder_level')
            ->count();

        // 2. Top Vendors (By Spend)
        $topVendors = Vendor::select('vendors.id', 'vendors.name')
            ->withSum('purchaseBills as total_spend', 'grand_total')
            ->withCount(['purchaseOrders as success_orders' => function ($q) {
                $q->where('status', 'received');
            }])
            ->withCount('purchaseOrders as total_orders')
            ->orderByDesc('total_spend')
            ->limit(3)
            ->get();

        // Map success percentage
        $topVendors->map(function ($vendor) {
            $vendor->success_rate = $vendor->total_orders > 0
                ? round(($vendor->success_orders / $vendor->total_orders) * 100)
                : 100;

            return $vendor;
        });

        return view('admin.purchases.dashboard', compact(
            'pendingReceipts',
            'pendingReceiptValue',
            'unpaidBills',
            'unpaidBillAmount',
            'criticalStockCount',
            'topVendors'
        ));
    }
}
