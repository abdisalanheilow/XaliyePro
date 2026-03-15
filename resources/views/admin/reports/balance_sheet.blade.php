@extends('admin.admin_master')

@section('title', 'Balance Sheet')

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
                width: 100% !important;
                border: none !important;
                box-shadow: none !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
    </style>

    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Balance Sheet</h2>
                    <p class="text-sm text-gray-500 mt-1">Assets, Liabilities, and Equity Statement</p>
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
            <form method="GET" action="{{ url('/reports/balance-sheet') }}" class="bg-white rounded-lg border border-gray-200 p-5 mb-6 no-print">
                <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Report Date</label>
                        <input type="date" name="report_date" value="{{ $reportDate }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Comparison Period</label>
                        <select name="comparison_period"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                            <option value="">No Comparison</option>
                            <option value="previous_month">Previous Month</option>
                            <option value="previous_quarter">Previous Quarter</option>
                            <option value="previous_year">Previous Year</option>
                        </select>
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Accounting Method</label>
                        <select name="accounting_method"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                            <option value="Accrual" {{ request('accounting_method', strtolower($settings->accounting_method ?? 'accrual')) == 'accrual' ? 'selected' : '' }}>Accrual</option>
                            <option value="Cash" {{ request('accounting_method', strtolower($settings->accounting_method ?? '')) == 'cash' ? 'selected' : '' }}>Cash</option>
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

            <!-- Balance Sheet Report -->
            <div class="bg-white rounded-lg border border-gray-200 print-full-width">
                <!-- Report Header -->
                <div class="border-b border-gray-200 px-8 py-6 text-center">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $settings?->company_name ?? 'XaliyePro' }}</h1>
                    <h2 class="text-xl font-bold text-gray-900 mt-2">BALANCE SHEET</h2>
                    <p class="text-sm text-gray-600 mt-1">As of {{ date('F d, Y', strtotime($reportDate)) }}</p>
                </div>

                <!-- Balance Sheet Content -->
                <div class="p-8">
                    <!-- ASSETS -->
                    <div class="mb-8">
                        <h3
                            class="text-lg font-bold text-gray-900 bg-gray-100 px-4 py-2 rounded-t-lg border-b border-gray-200">
                            ASSETS</h3>

                        <!-- Current Assets -->
                        <div class="mt-4">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Current Assets</h4>
                            @if(count($currentAssets) > 0)
                                @foreach ($currentAssets as $account)
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                    <span
                                        class="text-sm font-semibold text-gray-900">{{ $account->current_balance >= 0 ? '$' . number_format($account->current_balance, 2) : '($' . number_format(abs($account->current_balance), 2) . ')' }}</span>
                                </div>
                            @endforeach
                            @else
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-500 pl-4 italic">No current assets recorded</span>
                                </div>
                            @endif
                            <div
                                class="px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-gray-50 mt-1">
                                <span class="text-sm font-bold text-gray-900 pl-4">Total Current Assets</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ $totalCurrentAssets >= 0 ? '$' . number_format($totalCurrentAssets, 2) : '($' . number_format(abs($totalCurrentAssets), 2) . ')' }}</span>
                            </div>
                        </div>

                        <!-- Fixed Assets -->
                        <div class="mt-6">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Fixed Assets</h4>
                            @if(count($fixedAssets) > 0)
                                @foreach ($fixedAssets as $account)
                                    <div
                                        class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">{{ $account->current_balance >= 0 ? '$' . number_format($account->current_balance, 2) : '($' . number_format(abs($account->current_balance), 2) . ')' }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-500 pl-4 italic">No fixed assets recorded</span>
                                </div>
                            @endif
                            <div
                                class="px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-gray-50 mt-1">
                                <span class="text-sm font-bold text-gray-900 pl-4">Total Fixed Assets</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ $totalFixedAssets >= 0 ? '$' . number_format($totalFixedAssets, 2) : '($' . number_format(abs($totalFixedAssets), 2) . ')' }}</span>
                            </div>
                        </div>

                        <!-- Other Assets -->
                        <div class="mt-6">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Other Assets</h4>
                            @if(count($otherAssets) > 0)
                                @foreach ($otherAssets as $account)
                                    <div
                                        class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">{{ $account->current_balance >= 0 ? '$' . number_format($account->current_balance, 2) : '($' . number_format(abs($account->current_balance), 2) . ')' }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-500 pl-4 italic">No other assets recorded</span>
                                </div>
                            @endif
                            <div
                                class="px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-gray-50 mt-1">
                                <span class="text-sm font-bold text-gray-900 pl-4">Total Other Assets</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ $totalOtherAssets >= 0 ? '$' . number_format($totalOtherAssets, 2) : '($' . number_format(abs($totalOtherAssets), 2) . ')' }}</span>
                            </div>
                        </div>

                        <!-- Total Assets -->
                        <div class="mt-6 px-4 py-4 bg-[#28A375] bg-opacity-10 border-2 border-[#28A375] rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-[#28A375]">TOTAL ASSETS</span>
                                <span
                                    class="text-lg font-bold text-[#28A375]">{{ $totalAssets >= 0 ? '$' . number_format($totalAssets, 2) : '($' . number_format(abs($totalAssets), 2) . ')' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- LIABILITIES & EQUITY -->
                    <div class="mb-8">
                        <h3
                            class="text-lg font-bold text-gray-900 bg-gray-100 px-4 py-2 rounded-t-lg border-b border-gray-200">
                            LIABILITIES & EQUITY</h3>

                        <!-- Current Liabilities -->
                        <div class="mt-4">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Current Liabilities</h4>
                            @if(count($currentLiabilities) > 0)
                                @foreach ($currentLiabilities as $account)
                                    <div
                                        class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">{{ $account->current_balance >= 0 ? '$' . number_format($account->current_balance, 2) : '($' . number_format(abs($account->current_balance), 2) . ')' }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-500 pl-4 italic">No current liabilities recorded</span>
                                </div>
                            @endif
                            <div
                                class="px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-gray-50 mt-1">
                                <span class="text-sm font-bold text-gray-900 pl-4">Total Current Liabilities</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ $totalCurrentLiabilities >= 0 ? '$' . number_format($totalCurrentLiabilities, 2) : '($' . number_format(abs($totalCurrentLiabilities), 2) . ')' }}</span>
                            </div>
                        </div>

                        <!-- Long-term Liabilities -->
                        <div class="mt-6">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Long-term Liabilities</h4>
                            @if(count($longTermLiabilities) > 0)
                                @foreach ($longTermLiabilities as $account)
                                    <div
                                        class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">{{ $account->current_balance >= 0 ? '$' . number_format($account->current_balance, 2) : '($' . number_format(abs($account->current_balance), 2) . ')' }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-500 pl-4 italic">No long-term liabilities recorded</span>
                                </div>
                            @endif
                            <div
                                class="px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-gray-50 mt-1">
                                <span class="text-sm font-bold text-gray-900 pl-4">Total Long-term Liabilities</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ $totalLongTermLiabilities >= 0 ? '$' . number_format($totalLongTermLiabilities, 2) : '($' . number_format(abs($totalLongTermLiabilities), 2) . ')' }}</span>
                            </div>
                        </div>

                        <!-- Total Liabilities -->
                        <div class="mt-6 px-4 py-3 bg-gray-100 rounded-lg border border-gray-200">
                            <div class="flex items-center justify-between">
                                <span class="text-base font-bold text-gray-900">TOTAL LIABILITIES</span>
                                <span
                                    class="text-base font-bold text-gray-900">{{ $totalLiabilities >= 0 ? '$' . number_format($totalLiabilities, 2) : '($' . number_format(abs($totalLiabilities), 2) . ')' }}</span>
                            </div>
                        </div>

                        <!-- Equity -->
                        <div class="mt-8">
                            <h4 class="text-base font-bold text-gray-900 px-4 py-2 bg-gray-50">Equity</h4>
                            @if(count($equities) > 0)
                                @foreach ($equities as $account)
                                    <div
                                        class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                        <span class="text-sm text-gray-700 pl-4">{{ $account->name }}</span>
                                        <span
                                            class="text-sm font-semibold text-gray-900">{{ $account->current_balance >= 0 ? '$' . number_format($account->current_balance, 2) : '($' . number_format(abs($account->current_balance), 2) . ')' }}</span>
                                    </div>
                                @endforeach
                            @else
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-500 pl-4 italic">No equity accounts recorded</span>
                                </div>
                            @endif

                            @if ($currentYearProfit != 0)
                                <div
                                    class="px-4 py-2 flex items-center justify-between hover:bg-gray-50 border-b border-gray-100">
                                    <span class="text-sm text-gray-700 pl-4">Current Year Profit</span>
                                    <span
                                        class="text-sm font-semibold text-gray-900">{{ $currentYearProfit >= 0 ? '$' . number_format($currentYearProfit, 2) : '($' . number_format(abs($currentYearProfit), 2) . ')' }}</span>
                                </div>
                            @endif

                            <div
                                class="px-4 py-3 flex items-center justify-between border-t border-gray-200 bg-gray-50 mt-1">
                                <span class="text-sm font-bold text-gray-900 pl-4">Total Equity</span>
                                <span
                                    class="text-sm font-bold text-gray-900">{{ $totalEquity >= 0 ? '$' . number_format($totalEquity, 2) : '($' . number_format(abs($totalEquity), 2) . ')' }}</span>
                            </div>
                        </div>

                        <!-- Total Liabilities & Equity -->
                        <div class="mt-6 px-4 py-4 bg-[#28A375] bg-opacity-10 border-2 border-[#28A375] rounded-lg">
                            <div class="flex items-center justify-between">
                                <span class="text-lg font-bold text-[#28A375]">TOTAL LIABILITIES & EQUITY</span>
                                <span
                                    class="text-lg font-bold text-[#28A375]">{{ $totalLiabilitiesAndEquity >= 0 ? '$' . number_format($totalLiabilitiesAndEquity, 2) : '($' . number_format(abs($totalLiabilitiesAndEquity), 2) . ')' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Verification Notice -->
                    @if (round($totalAssets, 2) == round($totalLiabilitiesAndEquity, 2))
                        <div class="mt-10 p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold text-green-900">Balance Sheet is Balanced</p>
                                    <p class="text-sm text-green-800 mt-1">Total Assets matches Total Liabilities & Equity
                                        exactly
                                        ({{ $totalAssets >= 0 ? '$' . number_format($totalAssets, 2) : '($' . number_format(abs($totalAssets), 2) . ')' }})
                                        protecting your data integrity.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="mt-10 p-4 bg-red-50 border border-red-200 rounded-lg">
                            <div class="flex items-start gap-3">
                                <i data-lucide="alert-circle" class="w-5 h-5 text-red-600 mt-0.5"></i>
                                <div>
                                    <p class="text-sm font-bold text-red-900">Balance Sheet is Out of Balance</p>
                                    <p class="text-sm text-red-800 mt-1">Total Assets
                                        ({{ $totalAssets >= 0 ? '$' . number_format($totalAssets, 2) : '($' . number_format(abs($totalAssets), 2) . ')' }})
                                        do not match Total Liabilities & Equity
                                        ({{ $totalLiabilitiesAndEquity >= 0 ? '$' . number_format($totalLiabilitiesAndEquity, 2) : '($' . number_format(abs($totalLiabilitiesAndEquity), 2) . ')' }}).
                                        Difference:
                                        {{ '$' . number_format(abs(round($totalAssets - $totalLiabilitiesAndEquity, 2)), 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif

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
                            <p class="text-sm text-gray-700 leading-relaxed"><strong>Accounting Method:</strong> This
                                balance sheet has been prepared using the
                                {{ strtolower($settings->accounting_method ?? 'accrual') }} basis of accounting.</p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-blue-600">2</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 leading-relaxed"><strong>Inventory Valuation:</strong> Inventory
                                is valued using the {{ $settings->costing_method ?? 'FIFO (First-In-First-Out)' }} method.
                            </p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                            <span class="text-xs font-bold text-blue-600">3</span>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 leading-relaxed"><strong>Depreciation:</strong> Fixed assets are
                                depreciated using the straight-line method over their estimated useful lives.</p>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
