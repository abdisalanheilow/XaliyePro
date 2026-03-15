@extends('admin.admin_master')

@section('title', 'Purchase Summary')

@section('admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Purchase Summary</h2>
                    <p class="text-sm text-gray-500 mt-1">Total purchasing activity for the selected period</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.print()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-6">
            <!-- Filters -->
            <form method="GET" action="{{ route('reports.purchase-summary') }}" class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm no-print">
                <div class="flex flex-col lg:flex-row items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-600">
                    </div>
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="filter" class="w-4 h-4"></i> Update
                    </button>
                </div>
            </form>

            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Total Purchases</p>
                    <h3 class="text-3xl font-black text-gray-900">${{ number_format($totalPurchases, 2) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-2">Gross amount of all bills</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Net Amount Paid</p>
                    <h3 class="text-3xl font-black text-green-600">${{ number_format($totalPaid, 2) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-2">Settled through payments</p>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Outstanding Balance</p>
                    <h3 class="text-3xl font-black text-red-600">${{ number_format($totalBalance, 2) }}</h3>
                    <p class="text-[10px] text-gray-400 mt-2">Unpaid bills amount</p>
                </div>
            </div>

            <!-- Visualization could go here -->
            <div class="bg-sky-50 border border-sky-100 rounded-xl p-4 mb-6 flex items-center gap-3 no-print">
                <i data-lucide="info" class="w-5 h-5 text-sky-600"></i>
                <p class="text-sm text-sky-800 font-medium">This report includes all purchase bills except those in 'cancelled' status.</p>
            </div>
        </main>
    </div>
@endsection
