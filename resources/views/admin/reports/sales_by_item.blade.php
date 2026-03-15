@extends('admin.admin_master')

@section('title', 'Sales by Item')

@section('admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <!-- Top Navigation -->
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Sales by Item</h2>
                    <p class="text-sm text-gray-500 mt-1">Breakdown of item movement and performance for the selected period</p>
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
            <!-- Filters -->
            <form method="GET" action="{{ route('reports.sales-by-item') }}"
                class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm no-print">
                <div class="flex flex-col lg:flex-row items-end gap-4">
                    <div class="flex-1 w-full lg:w-auto">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div class="flex-1 w-full lg:w-auto">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}"
                            class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <button type="submit"
                        class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i>
                        Update
                    </button>
                </div>
            </form>

            @php
                $totalUnits = $productSales->sum('total_qty');
                $totalRevenue = $productSales->sum('total_value');
                $topItem = $productSales->first();
            @endphp

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-[#28A375]">
                            <i data-lucide="package" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Units Sold</p>
                            <h3 class="text-2xl font-bold text-gray-900">{{ number_format($totalUnits) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-blue-600">
                            <i data-lucide="dollar-sign" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Revenue</p>
                            <h3 class="text-2xl font-bold text-gray-900">${{ number_format($totalRevenue, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-purple-50 rounded-xl flex items-center justify-center text-purple-600">
                            <i data-lucide="trophy" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Top Performing Item</p>
                            <h3 class="text-lg font-bold text-gray-900 truncate">{{ $topItem->item->name ?? 'N/A' }}</h3>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Item Sales Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden print-full-width">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-bold text-gray-900">Item Performance ({{ date('M d, Y', strtotime($fromDate)) }} -
                        {{ date('M d, Y', strtotime($toDate)) }})</h3>
                </div>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Item Info</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Qty Sold</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Value</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Avg Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(count($productSales) > 0)
                            @foreach ($productSales as $ps)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 bg-gray-100 text-[#28A375] rounded-xl flex items-center justify-center font-bold text-xs">
                                                {{ substr($ps->item->name, 0, 2) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $ps->item->name }}</p>
                                                <p class="text-xs text-gray-500 font-medium tracking-tight">SKU: {{ $ps->item->sku ?? 'N/A' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap">
                                        <span
                                            class="px-2.5 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-bold uppercase tracking-wider border border-gray-200">
                                            {{ $ps->item->category->name ?? 'Uncategorized' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <span class="text-sm font-bold text-gray-900">{{ number_format($ps->total_qty, 0) }}</span>
                                        <span class="text-xs text-gray-400 font-medium ml-1">units</span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <span class="text-sm font-bold text-gray-900">${{ number_format($ps->total_value, 2) }}</span>
                                    </td>
                                    <td class="px-6 py-5 whitespace-nowrap text-right">
                                        <span class="text-sm font-semibold text-gray-600">
                                            ${{ $ps->total_qty > 0 ? number_format($ps->total_value / $ps->total_qty, 2) : '0.00' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-3">
                                        <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center text-gray-200">
                                            <i data-lucide="package-search" class="w-8 h-8"></i>
                                        </div>
                                        <p class="text-gray-500 italic text-sm">No sales were recorded for items in this period.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
                
                @if ($productSales->count() > 0)
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-gray-900">GRAND TOTAL</span>
                        <span class="text-lg font-bold text-[#28A375]">${{ number_format($totalRevenue, 2) }}</span>
                    </div>
                </div>
                @endif
            </div>

            <!-- Report Footer -->
            <div class="mt-12 pt-6 border-t border-gray-200 text-center">
                <p class="text-sm text-gray-500">Report generated on {{ date('F d, Y \a\t h:i A') }}</p>
                <p class="text-sm font-medium text-gray-600 mt-1">{{ $settings?->company_name ?? 'XaliyePro' }} -
                    Inventory Management System</p>
            </div>
        </main>
    </div>
@endsection
