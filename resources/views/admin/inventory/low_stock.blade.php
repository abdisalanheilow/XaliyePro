@extends('admin.admin_master')

@section('title', 'Low Stock Alerts - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Low Stock Alerts</h1>
                <p class="text-sm text-gray-500">Items that have reached or dropped below reorder levels</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.orders.create') }}" 
                    class="px-4 py-2.5 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-all flex items-center gap-2 shadow-sm shadow-red-100 active:scale-95">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    Bulk Purchase Order
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Alerts -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-red-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Items at Risk</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_alerts']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-red-500">Requires attention</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center shadow-lg shadow-red-100">
                    <i data-lucide="alert-triangle" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Out of Stock -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-slate-900">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Out of Stock</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['out_of_stock']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-gray-500">Zero inventory</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-slate-900 rounded-xl flex items-center justify-center shadow-lg shadow-slate-200">
                    <i data-lucide="package-x" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Reorder Value -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-emerald-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Est. Reorder Cost</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">${{ number_format($stats['reorder_value'], 2) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-emerald-500">To reach min. levels</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                    <i data-lucide="banknote" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Categories -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-blue-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Affected Cats.</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['unique_categories']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-blue-500">Categories impacted</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i data-lucide="layout-grid" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-red-100 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 bg-red-50/10 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="text-lg font-bold text-red-900 tracking-tight flex items-center gap-2">
                    <i data-lucide="alert-circle" class="w-5 h-5"></i>
                    Critical Stock Ledger
                </h3>
                
                <!-- Search -->
                <form action="{{ route('inventory.low_stock') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search items to reorder..."
                        class="pl-10 pr-4 py-2 bg-white border border-red-100 rounded-lg text-sm focus:ring-2 focus:ring-red-500 focus:border-transparent outline-none w-full md:w-80 transition-all shadow-sm">
                    <i data-lucide="search" class="w-4 h-4 text-red-300 absolute left-3 top-3"></i>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Item Details</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Current Stock</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Reorder Level</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Shortage</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Severity</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(count($items) > 0)
                            @foreach ($items as $item)
                        <tr class="hover:bg-red-50/20 transition-all group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-white border border-red-50 flex items-center justify-center text-red-400 shadow-sm group-hover:border-red-200 transition-all">
                                        <i data-lucide="package" class="w-5 h-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 tracking-tight leading-none mb-1">{{ $item->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">
                                             {{ $item->sku }} <span class="mx-1 text-gray-200">|</span> {{ $item->category?->name ?? 'No Category' }}
                                         </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-black {{ $item->current_stock <= 0 ? 'text-red-600 animate-pulse' : 'text-orange-600' }}">
                                    {{ number_format($item->current_stock) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-600">{{ number_format($item->reorder_level) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex flex-col items-end">
                                    <span class="text-sm font-black text-gray-900">{{ number_format($item->reorder_level - $item->current_stock) }}</span>
                                    <span class="text-[9px] text-gray-400 font-bold uppercase">Units needed</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex justify-center">
                                    @php
                                        $isCritical = $item->current_stock <= 0;
                                        $sevClass = $isCritical ? 'bg-red-600 text-white shadow-red-100' : 'bg-orange-500 text-white shadow-orange-100';
                                        $sevText = $isCritical ? 'Critical' : 'Low';
                                    @endphp
                                    <span class="px-2.5 py-1 text-[9px] font-black uppercase rounded shadow-lg {{ $sevClass }}">
                                        {{ $sevText }}
                                    </span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('purchases.orders.create', ['item_id' => $item->id]) }}" 
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white border border-gray-200 text-[#28A375] rounded-lg text-[10px] font-black uppercase hover:bg-[#28A375] hover:text-white hover:border-[#28A375] transition-all shadow-sm active:scale-95">
                                    <i data-lucide="shopping-cart" class="w-3 h-3"></i>
                                    Reorder
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center opacity-50">
                                    <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mb-4 border border-emerald-100 shadow-sm">
                                        <i data-lucide="shield-check" class="w-8 h-8"></i>
                                    </div>
                                    <h4 class="text-lg font-black text-gray-900 tracking-tight">System Optimized</h4>
                                    <p class="text-xs text-gray-500 max-w-[200px] mt-1">
                                        @if (request('search'))
                                            No reorder alerts match your search.
                                        @else
                                            No items are currently below reorder levels. Great job!
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
