@extends('admin.admin_master')

@section('title', 'Reports Dashboard')

@section('admin')
    <div class="px-8 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Reports Dashboard</h2>
                <p class="text-sm text-gray-500 mt-1">Access all your business reports and analytics</p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                    <i data-lucide="calendar" class="w-4 h-4"></i>
                    This Month
                </button>
                <button
                    class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Export All
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
            <!-- Total Sales -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">Total Sales</p>
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_sales'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-6 h-6 text-[#28A375]"></i>
                    </div>
                </div>
            </div>

            <!-- Total Purchases -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">Total Purchases</p>
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_purchases'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-indigo-50 rounded-lg flex items-center justify-center">
                        <i data-lucide="shopping-bag" class="w-6 h-6 text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <!-- Stock Value -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">Stock Value</p>
                        <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['stock_value'], 0) }}</p>
                    </div>
                    <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center">
                        <i data-lucide="box" class="w-6 h-6 text-purple-600"></i>
                    </div>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-start justify-between mb-3">
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">Low Stock Alerts</p>
                        <p class="text-3xl font-bold {{ $stats['low_stock_count'] > 0 ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $stats['low_stock_count'] }}
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-6 h-6 text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Reports -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5 shadow-sm">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-[#28A375] rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="calculator" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Financial Reports</h3>
                    <p class="text-sm text-gray-600 mt-1">View detailed financial statements and analysis</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Profit & Loss Statement -->
                <a href="{{ route('reports.profit-loss') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Profit & Loss Statement</p>
                        <p class="text-xs text-gray-600 mt-1">Income and expenses summary</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-[#28A375] group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- Balance Sheet -->
                <a href="{{ route('reports.balance-sheet') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Balance Sheet</p>
                        <p class="text-xs text-gray-600 mt-1">Assets, liabilities, and equity</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-[#28A375] group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- Cash Flow Statement -->
                <a href="{{ route('reports.cash-flow') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Cash Flow Statement</p>
                        <p class="text-xs text-gray-600 mt-1">Cash inflows and outflows</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- Trial Balance -->
                <a href="{{ route('reports.trial-balance') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Trial Balance</p>
                        <p class="text-xs text-gray-600 mt-1">Account balances verification</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-[#28A375] group-hover:translate-x-1 transition-all"></i>
                </a>

                <!-- Bank Reconciliation -->
                <a href="{{ route('accounting.reconciliation.index') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Bank Reconciliation</p>
                        <p class="text-xs text-gray-600 mt-1">Match bank statements with GL</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-[#28A375] group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Sales & Revenue -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5 shadow-sm">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-blue-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="trending-up" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Sales & Revenue</h3>
                    <p class="text-sm text-gray-600 mt-1">Track sales performance and revenue trends</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('reports.sales-summary') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Sales Summary</p>
                        <p class="text-xs text-gray-600 mt-1">Overall sales performance</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="{{ route('reports.sales-by-customer') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Sales by Customer</p>
                        <p class="text-xs text-gray-600 mt-1">Customer wise sales analysis</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="{{ route('reports.sales-by-item') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Sales by Item</p>
                        <p class="text-xs text-gray-600 mt-1">Item performance analysis</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="{{ route('reports.revenue-trends') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Revenue Trends</p>
                        <p class="text-xs text-gray-600 mt-1">Revenue growth analysis</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-blue-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Inventory Reports -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5 shadow-sm">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-purple-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="box" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Inventory Reports</h3>
                    <p class="text-sm text-gray-600 mt-1">Monitor stock levels and inventory movement</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('reports.stock-on-hand') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Stock on Hand</p>
                        <p class="text-xs text-gray-600 mt-1">Current inventory levels</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="{{ route('reports.stock-movement') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Stock Movements</p>
                        <p class="text-xs text-gray-600 mt-1">Inventory transactions</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="{{ route('reports.inventory-valuation') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Inventory Valuation</p>
                        <p class="text-xs text-gray-600 mt-1">Stock value analysis</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="{{ route('reports.low-stock') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Low Stock Alert</p>
                        <p class="text-xs text-gray-600 mt-1">Items below reorder level</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-purple-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Purchase Reports -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5 shadow-sm">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-indigo-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Purchase Reports</h3>
                    <p class="text-sm text-gray-600 mt-1">Analyze procurement costs and vendor activity</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('reports.purchase-summary') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Purchase Summary</p>
                        <p class="text-xs text-gray-600 mt-1">Overall purchasing activity</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="{{ route('reports.purchases-by-vendor') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Purchases by Vendor</p>
                        <p class="text-xs text-gray-600 mt-1">Vendor-wise sourcing analysis</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-indigo-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Accounts Receivable -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5 shadow-sm">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-orange-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="users" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Accounts Receivable</h3>
                    <p class="text-sm text-gray-600 mt-1">Track customer payments and outstanding invoices</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('reports.customer-statement') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Customer Statement</p>
                        <p class="text-xs text-gray-600 mt-1">Detailed customer ledger</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-orange-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="#"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Aged Receivables</p>
                        <p class="text-xs text-gray-600 mt-1">Aging analysis of receivables</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-orange-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Accounts Payable -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 mb-5 shadow-sm">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-red-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="shopping-cart" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Accounts Payable</h3>
                    <p class="text-sm text-gray-600 mt-1">Manage vendor payments and bills</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('reports.vendor-statement') }}"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Vendor Statement</p>
                        <p class="text-xs text-gray-600 mt-1">Detailed vendor ledger</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-red-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="#"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Aged Payables</p>
                        <p class="text-xs text-gray-600 mt-1">Aging analysis of payables</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-red-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>

        <!-- Tax Reports -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
            <div class="flex items-start gap-4 mb-6">
                <div class="w-12 h-12 bg-green-600 rounded-xl flex items-center justify-center flex-shrink-0">
                    <i data-lucide="receipt" class="w-6 h-6 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Tax Reports</h3>
                    <p class="text-sm text-gray-600 mt-1">Tax calculations and compliance reports</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="#"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Tax Summary</p>
                        <p class="text-xs text-gray-600 mt-1">Overall tax collection</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all"></i>
                </a>
                <a href="#"
                    class="group flex items-center justify-between p-4 rounded-lg border border-gray-200 hover:bg-gray-50 hover:border-gray-300 transition-all cursor-pointer">
                    <div>
                        <p class="text-sm font-bold text-gray-900">Tax Liability</p>
                        <p class="text-xs text-gray-600 mt-1">Tax payment obligations</p>
                    </div>
                    <i data-lucide="arrow-right"
                        class="w-5 h-5 text-gray-400 group-hover:text-green-500 group-hover:translate-x-1 transition-all"></i>
                </a>
            </div>
        </div>
    </div>
@endsection
