@extends('admin.admin_master')

@section('title', 'Stock On Hand - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock On Hand</h1>
                <p class="text-sm text-gray-500">Real-time inventory levels across all locations</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all active:scale-95">
                    <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                    Export
                </button>
                <a href="{{ route('items.index') }}"
                    class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Item
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Items -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-blue-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Stocked Items</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_items']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-gray-400">Unique SKUs tracking</p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i data-lucide="package" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Stock Value -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-emerald-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Inventory Value</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">${{ number_format($stats['total_stock_value'], 2) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-emerald-500">Current asset value</p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-red-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Out of Stock</p>
                    <h3 class="text-2xl font-bold text-red-600 tracking-tight">{{ number_format($stats['out_of_stock']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-red-500">Requires reordering</p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-100">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Low Stock -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-orange-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Low Stock Alert</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['low_stock']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-orange-500">Below reorder point</p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-100">
                    <i data-lucide="trending-down" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Filter & Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <form action="{{ route('inventory.on_hand') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    <!-- Search Item -->
                    <div class="relative">
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1 ml-1">Search Item</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                placeholder="Name or SKU..."
                                class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none w-full transition-all">
                            <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-2.5"></i>
                        </div>
                    </div>

                    <!-- Filter by Branch -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1 ml-1">Branch</label>
                        <select name="branch_id" onchange="this.form.submit()" 
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                            <option value="">All Branches</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Filter by Store -->
                    <div>
                        <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1 ml-1">Store / Warehouse</label>
                        <select name="store_id" onchange="this.form.submit()" 
                            class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                            <option value="">All Stores</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" {{ request('store_id') == $store->id ? 'selected' : '' }}>
                                    {{ $store->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Reset -->
                    <div class="flex items-end">
                        <a href="{{ route('inventory.on_hand') }}" 
                            class="px-4 py-2 text-sm font-bold text-gray-400 hover:text-gray-600 transition-all flex items-center gap-2">
                            <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                            Reset Filters
                        </a>
                    </div>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Item Information</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Location</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Cost Price</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">On Hand</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Inventory Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if (count($items) > 0)
                            @foreach ($items as $item)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:bg-white transition-colors">
                                            <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 leading-none mb-1">{{ $item->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                                                {{ $item->sku }} <span class="mx-1 text-gray-200">|</span> {{ $item->category->name ?? 'Uncategorized' }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs text-gray-700 font-bold bg-gray-50 px-2 py-0.5 rounded border border-gray-100">{{ $item->store->name ?? 'Default Store' }}</span>
                                        <span class="text-[9px] text-gray-400 uppercase font-black tracking-tighter mt-1">{{ $item->branch->name ?? 'Main Branch' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-bold text-gray-600">${{ number_format($item->cost_price, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-gray-900 tracking-tighter">{{ number_format($item->current_stock) }} <span class="text-[10px] text-gray-400 font-bold">{{ $item->unit->short_name ?? 'Units' }}</span></span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        @php
                                            if($item->current_stock <= 0) {
                                                $statusClass = 'bg-red-50 text-red-600 border-red-100';
                                                $statusText = 'Out of Stock';
                                            } elseif($item->current_stock <= $item->reorder_level) {
                                                $statusClass = 'bg-orange-50 text-orange-600 border-orange-100';
                                                $statusText = 'Low Stock';
                                            } else {
                                                $statusClass = 'bg-green-50 text-green-600 border-green-100';
                                                $statusText = 'In Stock';
                                            }
                                        @endphp
                                        <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded shadow-sm border {{ $statusClass }} tracking-wide">
                                            {{ $statusText }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-emerald-600 tracking-tighter">${{ number_format($item->current_stock * $item->cost_price, 2) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-50">
                                    <div class="w-16 h-16 bg-gray-50 text-gray-300 rounded-full flex items-center justify-center mb-4 border border-gray-100 shadow-sm">
                                        <i data-lucide="package-search" class="w-8 h-8"></i>
                                    </div>
                                    <h4 class="text-lg font-black text-gray-900 tracking-tight">No Items Found</h4>
                                    <p class="text-xs text-gray-500 max-w-[200px] mt-1">
                                        @if (request('search') || request('branch_id') || request('store_id'))
                                            Try adjusting your filters to find what you're looking for.
                                        @else
                                            Start adding items to track your inventory levels.
                                        @endif
                                    </p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                {{ $items->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
