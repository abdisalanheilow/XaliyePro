@extends('admin.admin_master')

@section('title', 'Sales Summary')

@section('admin')
    <div class="px-8 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 no-print">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Sales Summary</h2>
                <p class="text-sm text-gray-500 mt-1">Overall sales performance and top insights</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('reports.sales-summary') }}"
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

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-[#28A375] to-[#229967] rounded-2xl p-6 text-white shadow-lg overflow-hidden relative">
                <div class="relative z-10">
                    <p class="text-emerald-50 text-sm font-medium mb-1 uppercase tracking-wider">Total Sales (Period)</p>
                    <h3 class="text-3xl font-bold">${{ number_format($totalSales, 2) }}</h3>
                </div>
                <div class="absolute -right-4 -bottom-4 opacity-10">
                    <i data-lucide="dollar-sign" class="w-24 h-24"></i>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 bg-emerald-50 rounded-xl flex items-center justify-center">
                    <i data-lucide="package" class="w-7 h-7 text-[#28A375]"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm mb-0.5">Top Item</p>
                    <h4 class="text-xl font-bold text-gray-900">
                        {{ $topItems->first() ? $topItems->first()->item->name : 'N/A' }}
                    </h4>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 p-6 shadow-sm flex items-center gap-5">
                <div class="w-14 h-14 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-7 h-7 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-gray-500 text-sm mb-0.5">Primary Customer</p>
                    <h4 class="text-xl font-bold text-gray-900">
                        {{ $topCustomers->first() ? $topCustomers->first()->name : 'N/A' }}
                    </h4>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Top Items Table -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">Top Selling Items</h3>
                    <a href="{{ route('reports.sales-by-item') }}"
                        class="text-sm text-[#28A375] font-medium hover:underline">View All</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @if(count($topItems) > 0)
                        @foreach ($topItems as $tp)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-emerald-50 rounded-lg flex items-center justify-center">
                                        <i data-lucide="box" class="w-5 h-5 text-[#28A375]"></i>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $tp->item->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $tp->qty }} units sold</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-900">${{ number_format($tp->revenue, 2) }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="p-8 text-center text-gray-500 text-sm italic">No item movement recorded.</div>
                    @endif
                </div>
            </div>

            <!-- Top Customers Table -->
            <div class="bg-white rounded-2xl border border-gray-200 overflow-hidden shadow-sm">
                <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900">Leading Customers</h3>
                    <a href="{{ route('reports.sales-by-customer') }}"
                        class="text-sm text-[#28A375] font-medium hover:underline">View All</a>
                </div>
                <div class="divide-y divide-gray-100">
                    @if(count($topCustomers) > 0)
                        @foreach ($topCustomers as $tc)
                            <div class="px-6 py-4 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center font-bold text-xs">
                                        {{ substr($tc->name, 0, 2) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $tc->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $tc->city ?? 'Location N/A' }}</p>
                                    </div>
                                </div>
                                <span class="text-sm font-bold text-gray-900">${{ number_format($tc->total_sales, 2) }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="p-8 text-center text-gray-500 text-sm italic">No customer sales data available.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
