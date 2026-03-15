@extends('admin.admin_master')

@section('title', 'Low Stock Report')

@section('admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Low Stock Alert</h2>
                    <p class="text-sm text-gray-500 mt-1">Items that have reached or dropped below reorder level</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.print()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-6">
            <div class="bg-white rounded-2xl border border-red-100 shadow-sm overflow-hidden">
                <div class="p-6 bg-red-50/50 border-b border-red-100 flex items-center justify-between no-print">
                    <h3 class="font-bold text-red-900">Items Requiring Attention</h3>
                    <form method="GET" action="{{ route('reports.low-stock') }}" class="flex gap-2">
                        <select name="store_id" class="px-3 py-1.5 border border-gray-300 rounded text-xs focus:ring-1 focus:ring-red-500">
                            <option value="">All Warehouses</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="px-3 py-1.5 bg-red-600 text-white rounded text-xs font-bold hover:bg-red-700">Filter</button>
                    </form>
                </div>
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase">Item</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase">Warehouse</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase">Current Stock</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase">Reorder Level</th>
                            <th class="px-6 py-4 text-center text-[10px] font-bold text-gray-500 uppercase">Status</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase no-print">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(count($lowStocks) > 0)
                            @foreach ($lowStocks as $stock)
                                @php /** @var \App\Models\StoreItemStock $stock */ @endphp
                                <tr class="hover:bg-red-50/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-bold text-gray-900">{{ $stock->item->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-medium">SKU: {{ $stock->item->sku }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-xs font-semibold text-gray-600">{{ $stock->store->name }}</td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-bold text-red-600">{{ number_format($stock->current_stock) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="text-sm font-semibold text-gray-600">{{ number_format($stock->reorder_level) }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        @if ($stock->current_stock <= 0)
                                            <span class="px-2 py-0.5 bg-red-600 text-white rounded text-[10px] font-bold">Out of Stock</span>
                                        @else
                                            <span class="px-2 py-0.5 bg-orange-100 text-orange-700 rounded text-[10px] font-bold">Low Stock</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right no-print">
                                        <a href="{{ route('purchases.orders.create') }}?item_id={{ $stock->item_id }}" class="text-blue-600 hover:text-blue-800 text-xs font-bold transition-colors">Order Now</a>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center gap-2">
                                        <div class="w-12 h-12 bg-green-50 rounded-full flex items-center justify-center text-green-600">
                                            <i data-lucide="check-circle" class="w-6 h-6"></i>
                                        </div>
                                        <p class="text-gray-500 font-medium italic">Great! All items are above reorder level.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </main>
    </div>
@endsection
