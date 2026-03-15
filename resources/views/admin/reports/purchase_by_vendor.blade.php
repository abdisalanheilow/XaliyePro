@extends('admin.admin_master')

@section('title', 'Purchases by Vendor')

@section('admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Purchases by Vendor</h2>
                    <p class="text-sm text-gray-500 mt-1">Sourcing breakdown and vendor reliance analysis</p>
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
            <form method="GET" action="{{ route('reports.purchases-by-vendor') }}" class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm no-print">
                <div class="flex items-end gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <button type="submit" class="px-6 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967]">Update</button>
                </div>
            </form>

            <!-- Vendor Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase">Vendor Name</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-500 uppercase">Bills Count</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase">Total Purchased</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase">Total Paid</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase">Balance</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($vendorData as $vd)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $vd->vendor->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-center text-sm font-medium text-gray-600">{{ $vd->bill_count }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">${{ number_format($vd->total_amount, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-green-600">${{ number_format($vd->total_paid, 2) }}</td>
                            <td class="px-6 py-4 text-right text-sm font-bold text-red-600">${{ number_format($vd->total_amount - $vd->total_paid, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
@endsection
