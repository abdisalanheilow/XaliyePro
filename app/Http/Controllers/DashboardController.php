<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Item;
use App\Models\PurchaseBill;
use App\Models\SalesInvoice;
use App\Models\SalesOrder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_sales' => SalesInvoice::where('status', '!=', 'cancelled')->sum('grand_total'),
            'total_orders' => SalesOrder::count(),
            'total_items' => Item::count(),
            'total_customers' => Customer::count(),
            'active_customers' => Customer::where('status', 'active')->count(),
            'total_purchases' => PurchaseBill::where('status', '!=', 'cancelled')->sum('grand_total'),
            'stock_value' => \App\Models\StoreItemStock::join('items', 'store_item_stocks.item_id', '=', 'items.id')
                ->selectRaw('SUM(store_item_stocks.current_stock * items.cost_price) as value')
                ->value('value') ?? 0,
            'low_stock_count' => Item::whereRaw('current_stock <= reorder_level')->count(),
        ];

        // Calculate Net Margin
        $stats['net_margin'] = $stats['total_sales'] > 0 
            ? round((($stats['total_sales'] - $stats['total_purchases']) / $stats['total_sales']) * 100, 1)
            : 0;

        // Revenue vs Expenses (Last 7 days)
        $revenueData = [];
        $expenseData = [];
        $labels = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('D');
            
            $revenueData[] = SalesInvoice::whereDate('invoice_date', $date)->where('status', '!=', 'cancelled')->sum('grand_total');
            $expenseData[] = PurchaseBill::whereDate('bill_date', $date)->where('status', '!=', 'cancelled')->sum('grand_total');
        }

        // Recent Activity
        $recentInvoices = SalesInvoice::with('customer')->latest()->limit(5)->get();

        // Sales by Category
        $categorySales = \App\Models\SalesInvoiceItem::join('items', 'sales_invoice_items.item_id', '=', 'items.id')
            ->join('categories', 'items.category_id', '=', 'categories.id')
            ->selectRaw('categories.name, SUM(sales_invoice_items.amount) as total')
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('total')
            ->limit(4)
            ->get();

        $categoryLabels = $categorySales->pluck('name')->toArray();
        $categoryData = $categorySales->pluck('total')->toArray();

        return view('admin.body.index', compact(
            'stats', 
            'labels', 
            'revenueData', 
            'expenseData', 
            'recentInvoices',
            'categoryLabels',
            'categoryData'
        ));
    }
}
