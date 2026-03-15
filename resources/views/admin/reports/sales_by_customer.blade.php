@extends('admin.admin_master')

@section('title', 'Sales by Customer')

@section('admin')
    <div class="px-8 py-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-6 no-print">
            <div>
                <h2 class="text-2xl font-bold text-gray-900">Sales by Customer</h2>
                <p class="text-sm text-gray-500 mt-1">Detailed breakdown of customer purchasing behavior</p>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()"
                    class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Print
                </button>
            </div>
        </div>

        <!-- Desktop Grid View (Rich Aesthetic Card Style) -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if(count($customers) > 0)
                @foreach ($customers as $customer)
                <div
                    class="bg-white rounded-2xl border border-gray-200 p-6 hover:shadow-lg transition-all border-l-4 border-l-blue-600">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 bg-blue-100 text-blue-700 rounded-xl flex items-center justify-center font-bold text-lg">
                                {{ substr($customer->name, 0, 1) }}
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 truncate max-w-[150px]">{{ $customer->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $customer->city ?? 'No City' }},
                                    {{ $customer->country ?? '' }}</p>
                            </div>
                        </div>
                        <span
                            class="px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-[11px] font-bold uppercase">{{ $customer->status }}</span>
                    </div>

                    <div class="space-y-3 pt-4 border-t border-gray-100">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Total Lifetime Sales</span>
                            <span
                                class="text-base font-bold text-gray-900">${{ number_format($customer->total_sales, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">Outstanding Balance</span>
                            <span class="text-sm font-semibold {{ $customer->balance_amount > 0 ? 'text-red-600' : 'text-gray-900' }}">
                                ${{ number_format($customer->balance_amount, 2) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-3">
                        <div class="bg-gray-50 p-3 rounded-xl text-center">
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Credit Limit</p>
                            <p class="text-sm font-bold text-gray-800">${{ number_format($customer->credit_limit, 2) }}</p>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-xl text-center">
                            <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Payment Term</p>
                            <p class="text-sm font-bold text-gray-800">{{ $customer->payment_terms_label }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="col-span-full py-20 text-center">
                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="users" class="w-10 h-10 text-gray-300"></i>
                    </div>
                    <p class="text-gray-500">No customer records found matching the criteria.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
