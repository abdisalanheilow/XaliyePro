@extends('admin.admin_master')

@section('title', 'Stock Transfers - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock Transfers</h1>
                <p class="text-sm text-gray-500">Internal movements between stores/warehouses</p>
            </div>
            <a href="{{ route('inventory.transfers.create') }}"
                class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i>
                New Transfer
            </a>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Transfers -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-indigo-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Transfers</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_transfers']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-gray-400">Logistics history</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-indigo-500 rounded-xl flex items-center justify-center shadow-lg shadow-indigo-100">
                    <i data-lucide="truck" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Draft -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-amber-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">In Draft</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_draft']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-amber-500">Ready for transit</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-amber-500 rounded-xl flex items-center justify-center shadow-lg shadow-amber-100">
                    <i data-lucide="clock" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Completed -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-emerald-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Completed</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_completed']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-emerald-500">Stock arrived</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                    <i data-lucide="package-check" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Items Moved -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-purple-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Units Relocated</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['items_moved']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-purple-500">Total volume moved</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-100">
                    <i data-lucide="move" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Transfer Ledger</h3>
                
                <!-- Search -->
                <form action="{{ route('inventory.transfers.index') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search by ID or store location..."
                        class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none w-full md:w-64 transition-all">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Transfer No</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">From Location</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">To Location</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(count($transfers) > 0)
                            @foreach ($transfers as $tra)
                        <tr class="hover:bg-gray-50/50 transition-all group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900 tracking-tight">{{ $tra->transfer_no }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium whitespace-nowrap">
                                {{ $tra->transfer_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded">{{ $tra->fromStore->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-700 text-xs font-bold rounded">{{ $tra->toStore->name }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    @php
                                        $statusClasses = [
                                            'draft' => 'bg-gray-100 text-gray-600 border-gray-200',
                                            'pending' => 'bg-orange-100 text-orange-600 border-orange-200',
                                            'transferred' => 'bg-green-100 text-green-600 border-green-200',
                                            'cancelled' => 'bg-red-100 text-red-600 border-red-200',
                                        ];
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md border {{ $statusClasses[$tra->status] ?? 'bg-gray-100 text-gray-600' }}">
                                        {{ $tra->status }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <a href="{{ route('inventory.transfers.show', $tra->id) }}" class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-[#28A375] transition-all">
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
                                    No stock transfers found.
                                @endif
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                {{ $transfers->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
