@extends('admin.admin_master')

@section('title', 'Cash Flow Statement')

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
                    <h2 class="text-2xl font-bold text-gray-900">Cash Flow Statement</h2>
                    <p class="text-sm text-gray-500 mt-1">Indirect Method - Cash Activities</p>
                </div>
                <div class="flex items-center gap-3">
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
            <form method="GET" action="{{ route('reports.cash-flow') }}"
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
                    <div class="pt-7">
                        <button type="submit"
                            class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center gap-2 max-h-[42px]">
                            <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                            Update
                        </button>
                    </div>
                </div>
            </form>

            <!-- Cash Flow Report -->
            <div class="bg-white rounded-lg border border-gray-200 print-full-width">
                <!-- Report Header -->
                <div class="border-b border-gray-200 px-8 py-8 text-center text-gray-900">
                    <h1 class="text-2xl font-bold">{{ $settings?->company_name ?? 'XaliyePro' }}</h1>
                    <h2 class="text-xl font-bold mt-2 uppercase tracking-wide">Statement of Cash Flows</h2>
                    <p class="text-sm text-gray-600 mt-1">For the Period: {{ date('F d, Y', strtotime($fromDate)) }} -
                        {{ date('F d, Y', strtotime($toDate)) }}
                    </p>
                </div>

                <!-- Content -->
                <div class="p-8 max-w-4xl mx-auto">
                    <!-- OPERATING ACTIVITIES -->
                    <div class="mb-10">
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4 uppercase">Cash
                            Flows from Operating Activities</h3>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center px-4">
                                <span class="text-sm text-gray-700">Net Income</span>
                                <span class="text-sm font-semibold text-gray-900">${{ number_format($netIncome, 2) }}</span>
                            </div>

                            <div class="px-4 py-2 bg-gray-50/50 rounded-lg">
                                <p class="text-xs font-bold text-gray-500 mb-2 uppercase tracking-wider">Adjustments to
                                    reconcile net income to net cash</p>
                                <div class="space-y-2">
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Depreciation and Amortization</span>
                                        <span
                                            class="text-sm font-medium text-gray-900">{{ $depreciation >= 0 ? '$' . number_format($depreciation, 2) : '-$' . number_format(abs($depreciation), 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Change in Accounts Receivable</span>
                                        <span
                                            class="text-sm font-medium {{ $arMovement > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $arMovement > 0 ? '-$' . number_format($arMovement, 2) : '$' . number_format(abs($arMovement), 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Change in Inventory</span>
                                        <span
                                            class="text-sm font-medium {{ $inventoryMovement > 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $inventoryMovement > 0 ? '-$' . number_format($inventoryMovement, 2) : '$' . number_format(abs($inventoryMovement), 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Change in Accounts Payable</span>
                                        <span
                                            class="text-sm font-medium {{ $apMovement < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $apMovement >= 0 ? '$' . number_format($apMovement, 2) : '-$' . number_format(abs($apMovement), 2) }}
                                        </span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm text-gray-600">Change in Taxes Payable</span>
                                        <span
                                            class="text-sm font-medium {{ $taxPayableMovement < 0 ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $taxPayableMovement >= 0 ? '$' . number_format($taxPayableMovement, 2) : '-$' . number_format(abs($taxPayableMovement), 2) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex justify-between items-center px-4 pt-2 border-t border-gray-100">
                                <span class="text-sm font-bold text-gray-900">Net Cash from Operating Activities</span>
                                <span
                                    class="text-sm font-bold text-gray-900">${{ number_format($netOperatingCash, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- INVESTING ACTIVITIES -->
                    <div class="mb-10">
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4 uppercase">Cash
                            Flows from Investing Activities</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center px-4">
                                <span class="text-sm text-gray-700">Capital Expenditures (Fixed Assets)</span>
                                <span
                                    class="text-sm font-medium {{ $fixedAssetMovement > 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $fixedAssetMovement > 0 ? '-$' . number_format($fixedAssetMovement, 2) : '$' . number_format(abs($fixedAssetMovement), 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center px-4 pt-2 border-t border-gray-100">
                                <span class="text-sm font-bold text-gray-900">Net Cash from Investing Activities</span>
                                <span
                                    class="text-sm font-bold text-gray-900">${{ number_format($netInvestingCash, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- FINANCING ACTIVITIES -->
                    <div class="mb-10">
                        <h3 class="text-base font-bold text-gray-900 border-b border-gray-200 pb-2 mb-4 uppercase">Cash
                            Flows from Financing Activities</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center px-4">
                                <span class="text-sm text-gray-700">Proceeds from Loans / Debt Payments</span>
                                <span
                                    class="text-sm font-medium {{ $loanMovement < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $loanMovement >= 0 ? '$' . number_format($loanMovement, 2) : '-$' . number_format(abs($loanMovement), 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center px-4">
                                <span class="text-sm text-gray-700">Equity Contributions / Draws</span>
                                <span
                                    class="text-sm font-medium {{ $equityMovement < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $equityMovement >= 0 ? '$' . number_format($equityMovement, 2) : '-$' . number_format(abs($equityMovement), 2) }}
                                </span>
                            </div>
                            <div class="flex justify-between items-center px-4 pt-2 border-t border-gray-100">
                                <span class="text-sm font-bold text-gray-900">Net Cash from Financing Activities</span>
                                <span
                                    class="text-sm font-bold text-gray-900">${{ number_format($netFinancingCash, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- SUMMARY -->
                    <div class="bg-gray-900 rounded-xl p-8 text-white">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-gray-700 pb-4">
                                <span class="text-base font-medium opacity-80">Net Increase/Decrease in Cash</span>
                                <span
                                    class="text-xl font-bold {{ $netCashChange >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                    {{ $netCashChange >= 0 ? '$' . number_format($netCashChange, 2) : '-$' . number_format(abs($netCashChange), 2) }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center">
                                <span class="text-sm opacity-80">Cash at Beginning of Period</span>
                                <span class="text-sm font-semibold">${{ number_format($cashAtStart, 2) }}</span>
                            </div>

                            <div class="flex justify-between items-center pt-4 border-t border-gray-700">
                                <div>
                                    <span class="text-lg font-bold">CASH AT END OF PERIOD</span>
                                    <p class="text-xs opacity-60 mt-1 italic">Reconciled balance across all accounts</p>
                                </div>
                                <span class="text-2xl font-bold text-[#28A375]">${{ number_format($cashAtEnd, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Integrity Check -->
                    @if (abs($cashAtEnd - $actualCashAtEnd) > 0.01)
                        <div class="mt-6 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3 no-print">
                            <i data-lucide="alert-circle" class="w-5 h-5 text-red-600"></i>
                            <p class="text-xs text-red-700">Discrepancy detected: The calculated cash flow does not match the
                                actual balance sheet cash by ${{ number_format(abs($cashAtEnd - $actualCashAtEnd), 2) }}.
                                Ensure all cash transactions are correctly categorized.</p>
                        </div>
                    @endif

                    <!-- Footer -->
                    <div class="mt-12 pt-6 border-t border-gray-200 text-center">
                        <p class="text-xs text-gray-500">Report generated on {{ date('F d, Y \a\t h:i A') }}</p>
                        <p class="text-xs font-medium text-gray-600 mt-1">{{ $settings?->company_name ?? 'XaliyePro' }} -
                            Inventory Management System</p>
                    </div>
                </div>
            </div>
        </main>
    </div>
@endsection
