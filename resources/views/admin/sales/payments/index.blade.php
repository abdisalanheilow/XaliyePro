@extends('admin.admin_master')

@section('title', 'Customer Payments - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Customer Payments</h1>
                <p class="text-sm text-gray-500">Track and manage customer collections</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                    <i data-lucide="upload" class="w-4 h-4 text-gray-400"></i>
                    Import
                </button>
                <button class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                    <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                    Export
                </button>
                <a href="{{ route('sales.payments.create') }}"
                    class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Record Payment
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Collections -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-[#28A375]">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Collections</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">${{ number_format($stats['total_amount'], 2) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="trending-up" class="w-3 h-3 text-green-500"></i>
                        <p class="text-[11px] font-bold text-green-500">+12% <span class="text-gray-400 font-medium ml-1">vs last month</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-[#28A375] rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                    <i data-lucide="banknote" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Today's Collections -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-blue-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Today's Total</p>
                    <h3 class="text-2xl font-bold text-blue-600 tracking-tight">${{ number_format($stats['today_amount'], 2) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-blue-500">{{ $stats['today_count'] }} <span class="text-gray-400 font-medium ml-1">Transactions</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i data-lucide="calendar" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Transaction Count -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-purple-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Transactions</p>
                    <h3 class="text-2xl font-bold text-purple-600 tracking-tight">{{ number_format($stats['total_count']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-purple-500">Total entries <span class="text-gray-400 font-medium ml-1">updated</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-100">
                    <i data-lucide="hash" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Payment Success -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-green-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Payment Success</p>
                    <h3 class="text-2xl font-bold text-[#28A375] tracking-tight">100%</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="shield-check" class="w-3 h-3 text-green-500"></i>
                        <p class="text-[11px] font-bold text-green-500">Verified <span class="text-gray-400 font-medium ml-1">Secure</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                    <i data-lucide="shield-check" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative group">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#28A375] transition-colors"></i>
                <input type="text" placeholder="Search by payment no, customer, ref..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] focus:ring-0 transition-all outline-none">
            </div>
            <select class="px-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] transition-all outline-none min-w-[150px]">
                <option value="">All Methods</option>
                <option value="Cash">Cash</option>
                <option value="Bank Transfer">Bank Transfer</option>
                <option value="Check">Check</option>
            </select>
            <select class="px-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] transition-all outline-none min-w-[150px]">
                <option value="">All Branches</option>
            </select>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Payment Ledger</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Payment No</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Method</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Amount</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if ($payments->count() > 0)
                            @foreach ($payments as $payment)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900 tracking-tight">{{ $payment->payment_no }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-[11px] font-bold text-[#28A375] shadow-sm">
                                            {{ strtoupper(substr($payment->customer->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $payment->customer->name }}</div>
                                            <div class="text-[11px] text-gray-400 font-medium tracking-tight">CUS-{{ str_pad($payment->customer->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 font-medium">{{ $payment->customer->email ?? '---' }}</div>
                                    <div class="text-[11px] text-gray-400 font-medium">{{ $payment->customer->phone ?? '---' }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-md text-[10px] font-bold uppercase">{{ $payment->payment_method }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-[#28A375] tracking-tighter">${{ number_format($payment->amount ?? 0, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <button class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-[#28A375] transition-all">
                                            <i data-lucide="printer" class="w-4 h-4"></i>
                                        </button>
                                        <button class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-[#28A375] transition-all">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-4 bg-gray-50 rounded-full mb-4">
                                            <i data-lucide="banknote" class="w-12 h-12 text-gray-300"></i>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-900 tracking-tight">No Payments Found</h4>
                                        <p class="text-sm text-gray-500 max-w-[200px] mt-1">Record money received from your customers</p>
                                        <a href="{{ route('sales.payments.create') }}" class="mt-4 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold shadow-sm">Record Payment</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                {{ $payments->links() }}
            </div>
        </div>
    </div>
@endsection
