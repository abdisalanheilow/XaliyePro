@extends('admin.admin_master')

@section('title', 'Profit & Loss Statement')

@section('admin')
    <style>
        @media print {

            .layout-sidebar,
            .layout-header,
            .no-print {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .print-full-width {
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
            }
        }
    </style>

    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Profit & Loss Statement</h2>
                    <p class="text-sm text-gray-500 mt-1">Income Statement - Revenue and Expenses</p>
                </div>
                <div class="flex items-center gap-3">
                    <button
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="share-2" class="w-4 h-4"></i>
                        Share
                    </button>
                    <button onclick="window.print()"
                        class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i>
                        Print
                    </button>
                    <button
                        class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center gap-2">
                        <i data-lucide="download" class="w-4 h-4"></i>
                        Export
                    </button>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 overflow-y-auto px-8 py-6">
            <!-- Report Filters -->
            <form method="GET" action="{{ route('reports.profit-loss') }}"
                class="bg-white rounded-lg border border-gray-200 p-5 mb-6 no-print">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Accounting Method</label>
                        <select name="accounting_method"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                            <option value="Accrual" {{ request('accounting_method', strtolower($settings->accounting_method ?? 'accrual')) == 'accrual' ? 'selected' : '' }}>
                                Accrual</option>
                            <option value="Cash" {{ request('accounting_method', strtolower($settings->accounting_method ?? '')) == 'cash' ? 'selected' : '' }}>
                                Cash</option>
                        </select>
                    </div>
                    <div class="pt-7">
                        <button type="submit"
                            class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center gap-2 max-h-[42px]">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            Update
                        </button>
                    </div>
                </div>
            </form>

            <!-- Profit & Loss Statement Report -->
            <div class="bg-white rounded-lg border border-gray-200 print-full-width">
                <!-- Report Header -->
                <div class="border-b border-gray-200 px-8 py-6 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $settings?->company_name ?? 'XaliyePro' }}</h1>
                    <h2 class="text-xl font-bold text-gray-900 mt-2">PROFIT & LOSS STATEMENT</h2>
                    <p class="text-sm text-gray-600 mt-1">For the Period: {{ date('F d, Y', strtotime($fromDate)) }} -
                        {{ date('F d, Y', strtotime($toDate)) }}
                    </p>
                </div>

                <!-- P&L Content -->
                <div class="p-8">
                    <!-- REVENUE -->
                    <div class="mb-8">
                        <h3
                            class="text-lg font-bold text-gray-900 bg-gray-100 px-4 py-2 rounded-t-lg border-b border-gray-200">
                            REVENUE</h3>

                        <!-- Operating Revenue -->
                        <div class="mt-4">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Operating Revenue</h4>
                            @if (count($operatingRevenue) > 0)
                                @foreach ($operatingRevenue as $account)
                                    <div class="px-4 py-2 flex items-center justify-between hover:bg-gray-50">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">${{ number_format($account->period_balance, 2) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-2 text-sm text-gray-400 italic pl-8">No operating revenue data</div>
                            @endif

                            @if ($discountsAndReturns->count() > 0)
                                @foreach ($discountsAndReturns as $account)
                                    <div class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 text-red-600">
                                        <span class="text-sm pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold">(${{ number_format(abs($account->period_balance), 2) }})</span>
                                    </div>
                                @endforeach
                            @endif

                            <div class="px-4 py-2 flex items-center justify-between border-t border-gray-200 bg-gray-50">
                                <span class="text-sm font-bold text-gray-900 pl-4">Net Operating Revenue</span>
                                <span
                                    class="text-sm font-bold text-gray-900">${{ number_format($totalOperatingRevenue, 2) }}</span>
                            </div>
                        </div>

                        <!-- Other Revenue -->
                        @if ($otherRevenue->count() > 0)
                            <div class="mt-4">
                                <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Other Revenue</h4>
                                @foreach ($otherRevenue as $account)
                                    <div class="px-4 py-2 flex items-center justify-between hover:bg-gray-50">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">${{ number_format($account->period_balance, 2) }}</span>
                                    </div>
                                @endforeach
                                <div class="px-4 py-2 flex items-center justify-between border-t border-gray-200 bg-gray-50">
                                    <span class="text-sm font-bold text-gray-900 pl-4">Total Other Revenue</span>
                                    <span
                                        class="text-sm font-bold text-gray-900">${{ number_format($totalOtherRevenue, 2) }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Total Revenue -->
                        <div class="mt-6 px-4 py-3 bg-blue-50 border-2 border-blue-500 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-blue-900">TOTAL REVENUE</span>
                                <span
                                    class="text-base font-bold text-blue-900">${{ number_format($totalRevenue, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- COST OF GOODS SOLD -->
                    <div class="mb-8">
                        <h3
                            class="text-lg font-bold text-gray-900 bg-gray-100 px-4 py-2 rounded-t-lg border-b border-gray-200">
                            COST OF GOODS SOLD</h3>

                        <div class="mt-4">
                            @if (count($cogsAccounts) > 0)
                                @foreach ($cogsAccounts as $account)
                                    <div class="px-4 py-2 flex items-center justify-between hover:bg-gray-50">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">${{ number_format($account->period_balance, 2) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-2 text-sm text-gray-400 italic pl-8">No COGS movement</div>
                            @endif

                            <div class="px-4 py-2 flex items-center justify-between border-t border-gray-200 bg-gray-50">
                                <span class="text-sm font-bold text-gray-900">TOTAL COST OF GOODS SOLD</span>
                                <span class="text-sm font-bold text-gray-900">${{ number_format($totalCOGS, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- GROSS PROFIT -->
                    <div class="mb-8 px-4 py-3 bg-green-50 border-2 border-green-500 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold text-green-900">GROSS PROFIT</span>
                            <span class="text-base font-bold text-green-900">${{ number_format($grossProfit, 2) }}</span>
                        </div>
                        <p class="text-xs text-green-700 mt-1">Gross Profit Margin:
                            {{ number_format($grossProfitMargin, 1) }}%
                        </p>
                    </div>

                    <!-- OPERATING EXPENSES -->
                    <div class="mb-8">
                        <h3
                            class="text-lg font-bold text-gray-900 bg-gray-100 px-4 py-2 rounded-t-lg border-b border-gray-200">
                            OPERATING EXPENSES</h3>

                        <!-- Selling Expenses -->
                        @if ($sellingExpenses->count() > 0)
                            <div class="mt-4">
                                <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Selling Expenses</h4>
                                @foreach ($sellingExpenses as $account)
                                    <div class="px-4 py-2 flex items-center justify-between hover:bg-gray-50">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">${{ number_format($account->period_balance, 2) }}</span>
                                    </div>
                                @endforeach
                                <div class="px-4 py-2 flex items-center justify-between border-t border-gray-200 bg-gray-50">
                                    <span class="text-sm font-bold text-gray-900 pl-4">Total Selling Expenses</span>
                                    <span
                                        class="text-sm font-bold text-gray-900">${{ number_format($totalSellingExpenses, 2) }}</span>
                                </div>
                            </div>
                        @endif

                        <!-- Administrative Expenses -->
                        <div class="mt-4">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Administrative Expenses</h4>
                            @if (count($adminExpenses) > 0)
                                @foreach ($adminExpenses as $account)
                                    <div class="px-4 py-2 flex items-center justify-between hover:bg-gray-50">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">${{ number_format($account->period_balance, 2) }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div class="px-4 py-2 text-sm text-gray-400 italic pl-8">No admin expenses data</div>
                            @endif
                            <div class="px-4 py-2 flex items-center justify-between border-t border-gray-200 bg-gray-50">
                                <span class="text-sm font-bold text-gray-900 pl-4">Total Administrative Expenses</span>
                                <span
                                    class="text-sm font-bold text-gray-900">${{ number_format($totalAdminExpenses, 2) }}</span>
                            </div>
                        </div>

                        <!-- Total Operating Expenses -->
                        <div class="mt-6 px-4 py-3 bg-gray-100 rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-gray-900">TOTAL OPERATING EXPENSES</span>
                                <span
                                    class="text-base font-bold text-gray-900">${{ number_format($totalOperatingExpenses, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- OPERATING INCOME -->
                    <div class="mb-8 px-4 py-3 bg-purple-50 border-2 border-purple-500 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold text-purple-900">OPERATING INCOME</span>
                            <span
                                class="text-base font-bold text-purple-900">${{ number_format($operatingIncome, 2) }}</span>
                        </div>
                        <p class="text-xs text-purple-700 mt-1">Operating Margin:
                            {{ number_format($operatingMargin, 1) }}%
                        </p>
                    </div>

                    <!-- NET INCOME BEFORE TAX -->
                    <div class="mb-8 px-4 py-3 bg-orange-50 border-2 border-orange-500 rounded-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-base font-bold text-orange-900">NET INCOME BEFORE TAX</span>
                            <span
                                class="text-base font-bold text-orange-900">${{ number_format($netIncomeBeforeTax, 2) }}</span>
                        </div>
                    </div>

                    <!-- INCOME TAX -->
                    <div class="mb-8">
                        <div
                            class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border border-gray-200 rounded-lg">
                            <span class="text-sm font-bold text-gray-900">Income Tax Expense (Est. 30%)</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($incomeTaxExpense, 2) }}</span>
                        </div>
                    </div>

                    <!-- NET INCOME -->
                    <div class="px-4 py-6 bg-gradient-to-r from-[#28A375] to-[#229967] rounded-lg shadow-lg">
                        <div class="flex items-center justify-between">
                            <span class="text-2xl font-bold text-white">NET INCOME</span>
                            <span class="text-2xl font-bold text-white">${{ number_format($netIncome, 2) }}</span>
                        </div>
                        <p class="text-sm text-green-100 mt-2">Net Profit Margin: {{ number_format($netProfitMargin, 1) }}%
                        </p>
                    </div>

                    <!-- Key Metrics -->
                    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <p class="text-xs text-blue-600 font-semibold mb-1 uppercase">Gross Profit Margin</p>
                            <p class="text-2xl font-bold text-blue-900">{{ number_format($grossProfitMargin, 1) }}%</p>
                        </div>
                        <div class="bg-purple-50 border border-purple-200 rounded-lg p-4">
                            <p class="text-xs text-purple-600 font-semibold mb-1 uppercase">Operating Margin</p>
                            <p class="text-2xl font-bold text-purple-900">{{ number_format($operatingMargin, 1) }}%</p>
                        </div>
                        <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                            <p class="text-xs text-green-600 font-semibold mb-1 uppercase">Net Profit Margin</p>
                            <p class="text-2xl font-bold text-green-900">{{ number_format($netProfitMargin, 1) }}%</p>
                        </div>
                    </div>

                    <!-- Report Footer -->
                    <div class="mt-12 pt-6 border-t border-gray-200 text-center">
                        <p class="text-sm text-gray-500">Report generated on {{ date('F d, Y \a\t h:i A') }}</p>
                        <p class="text-sm font-medium text-gray-600 mt-1">{{ $settings?->company_name ?? 'XaliyePro' }} -
                            Inventory Management System</p>
                    </div>
                </div>
            </div>

            <!-- Notes Section -->
            <div class="bg-white rounded-lg border border-gray-200 p-6 mt-6 no-print">
                <h3 class="text-base font-bold text-gray-900 mb-4">Notes & Explanations</h3>
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-blue-600">1</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 leading-relaxed"><strong>Revenue Recognition:</strong> This
                                report
                                follows the {{ strtolower($settings->accounting_method ?? 'accrual') }} method of
                                accounting.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-blue-600">2</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 leading-relaxed"><strong>Inventory Valuation:</strong> Cost of
                                Goods Sold (COGS) assumes the {{ $settings->costing_method ?? 'FIFO' }} method where
                                applicable.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-blue-600">3</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 leading-relaxed"><strong>Taxation:</strong> Net Income is
                                estimated using a flat 30% corporate tax rate as a placeholder.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
