@extends('admin.admin_master')

@section('title', 'Stock Adjustments - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock Adjustments</h1>
                <p class="text-sm text-gray-500">Manual stock corrections and inventory reconciliation</p>
            </div>
            <a href="{{ route('inventory.adjustments.create') }}"
                class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i>
                New Adjustment
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Adjustments -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-blue-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Adjustments</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_adjustments']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-gray-400">All time records</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i data-lucide="clipboard-list" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Draft -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-orange-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Draft Records</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_draft']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-orange-500">Pending finalization</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-100">
                    <i data-lucide="file-text" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Adjusted -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-emerald-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Finalized</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_adjusted']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-emerald-500">Stock updated</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                    <i data-lucide="check-circle" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Value Impact -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-red-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Value Impact</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">${{ number_format(abs($stats['value_impact']), 2) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold {{ $stats['value_impact'] >= 0 ? 'text-emerald-500' : 'text-red-500' }}">
                            {{ $stats['value_impact'] >= 0 ? 'Net addition' : 'Net reduction' }}
                        </span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-100">
                    <i data-lucide="trending-down" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Adjustment History</h3>
                
                <!-- Search -->
                <form action="{{ route('inventory.adjustments.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by ID, store or reason..."
                        class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none w-full md:w-64 transition-all">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Adjustment No</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Store</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Reason</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if ($adjustments->count() > 0)
                            @foreach ($adjustments as $adj)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900 tracking-tight">{{ $adj->adjustment_no }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium whitespace-nowrap">
                                    {{ $adj->adjustment_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700 font-semibold">{{ $adj->store->name }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                    {{ $adj->reason }}
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        @php
                                            $statusClasses = [
                                                'draft' => 'bg-gray-100 text-gray-600 border-gray-200',
                                                'adjusted' => 'bg-green-100 text-green-600 border-green-200',
                                                'cancelled' => 'bg-red-100 text-red-600 border-red-200',
                                            ];
                                        @endphp
                                        <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md border {{ $statusClasses[$adj->status] ?? 'bg-gray-100 text-gray-600' }}">
                                            {{ $adj->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('inventory.adjustments.show', $adj->id) }}" class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-[#28A375] transition-all">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500 italic">
                                    @if (request('search'))
                                        No results found for "{{ request('search') }}"
                                    @else
                                        No stock adjustments found.
                                    @endif
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                {{ $adjustments->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
