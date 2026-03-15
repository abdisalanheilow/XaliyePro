@extends('admin.admin_master')

@section('title', 'Sales Invoices - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sales Invoices</h1>
                <p class="text-sm text-gray-500">Manage billing and customer receivables</p>
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
                <a href="{{ route('sales.invoices.create') }}"
                    class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Invoice
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Invoices',
                'value' => number_format($stats['total_count']),
                'icon' => 'file-text',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'trendValue' => '+12.5%',
                'subtitle' => 'vs last month'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Invoiced',
                'value' => '$' . number_format($stats['total_amount'], 2),
                'icon' => 'check-circle',
                'color' => '#28A375',
                'iconBg' => 'bg-[#28A375]',
                'iconShadow' => 'shadow-green-100',
                'trendValue' => '+8.2%',
                'subtitle' => 'vs last month'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Receivables',
                'value' => '$' . number_format($stats['unpaid_balance'], 2),
                'icon' => 'alert-circle',
                'color' => '#EF4444',
                'iconBg' => 'bg-red-500',
                'iconShadow' => 'shadow-red-100',
                'trendValue' => '-2.4%',
                'trendColor' => 'text-red-500',
                'trendIcon' => 'trending-down',
                'subtitle' => 'vs last month'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Pending Invoices',
                'value' => number_format($stats['total_count'] - $stats['paid_count']),
                'icon' => 'clock',
                'color' => '#F59E0B',
                'iconBg' => 'bg-orange-500',
                'iconShadow' => 'shadow-orange-100',
                'subtitle' => $stats['partially_paid_count'] . ' Partially paid'
            ])
        </div>

        <!-- Filters Section -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative group">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#28A375] transition-colors"></i>
                <input type="text" placeholder="Search by invoice no, customer..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] focus:ring-0 transition-all outline-none">
            </div>
            <select class="px-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] transition-all outline-none min-w-[150px]">
                <option value="">All Status</option>
                <option value="unpaid">Unpaid</option>
                <option value="partially_paid">Partially Paid</option>
                <option value="paid">Paid</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select class="px-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] transition-all outline-none min-w-[150px]">
                <option value="">All Branches</option>
            </select>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Invoice History</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Invoice No</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Balance</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if (count($invoices) > 0)
                            @foreach ($invoices as $invoice)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900 tracking-tight">{{ $invoice->invoice_no }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-blue-50 flex items-center justify-center text-[11px] font-bold text-blue-600 shadow-sm">
                                            {{ strtoupper(substr($invoice->customer->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $invoice->customer->name }}</div>
                                            <div class="text-[11px] text-gray-400 font-medium tracking-tight">CUS-{{ str_pad($invoice->customer->id, 4, '0', STR_PAD_LEFT) }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 font-medium">{{ $invoice->customer->email ?? '---' }}</div>
                                    <div class="text-[11px] text-gray-400 font-medium">{{ $invoice->customer->phone ?? '---' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-gray-900 tracking-tighter">${{ number_format($invoice->grand_total, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-red-600 tracking-tighter">${{ number_format($invoice->balance_amount, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        @php
                                            $statusClasses = [
                                                'unpaid' => 'bg-red-50 text-red-600 border-red-100',
                                                'partially_paid' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                'paid' => 'bg-green-50 text-green-600 border-green-100',
                                                'cancelled' => 'bg-gray-50 text-gray-600 border-gray-100',
                                            ];
                                            $statusClass = $statusClasses[strtolower($invoice->status)] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                        @endphp
                                        <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded-md border {{ $statusClass }} tracking-wide">
                                            {{ $invoice->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('sales.invoices.show', $invoice->id) }}" class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-[#28A375] transition-all">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('sales.invoices.edit', $invoice->id) }}" class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-blue-600 transition-all">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-red-600 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
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
                                        <i data-lucide="file-text" class="w-12 h-12 text-gray-300"></i>
                                    </div>
                                    <h4 class="text-lg font-bold text-gray-900 tracking-tight">No Invoices Found</h4>
                                    <p class="text-sm text-gray-500 max-w-[200px] mt-1">Generate your first invoice to start tracking payments</p>
                                    <a href="{{ route('sales.invoices.create') }}" class="mt-4 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold shadow-sm">Add New Invoice</a>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
@endsection
