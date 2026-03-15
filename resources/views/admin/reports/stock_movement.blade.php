@extends('admin.admin_master')

@section('title', 'Stock Movement')

@section('admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Stock Movement Audit</h2>
                    <p class="text-sm text-gray-500 mt-1">Detailed history of all inventory transactions</p>
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
            <form method="GET" action="{{ route('reports.stock-movement') }}" class="bg-white rounded-xl border border-gray-200 p-5 mb-6 shadow-sm no-print">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">From Date</label>
                        <input type="date" name="from_date" value="{{ $fromDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">To Date</label>
                        <input type="date" name="to_date" value="{{ $toDate }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Item</label>
                        <select name="item_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                            <option value="">All Items</option>
                            @foreach ($items as $item)
                                <option value="{{ $item->id }}" {{ $itemId == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex items-end">
                        <button type="submit" class="w-full px-6 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] inline-flex items-center justify-center gap-2">
                            <i data-lucide="filter" class="w-4 h-4"></i> Update
                        </button>
                    </div>
                </div>
            </form>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Date & Time</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Item</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-[10px] font-bold text-gray-500 uppercase tracking-wider">Warehouse</th>
                            <th class="px-6 py-4 text-right text-[10px] font-bold text-gray-500 uppercase tracking-wider">Qty</th>
                            <th class="px-6 py-4 text-left text-[10px] font-bold text-gray-500 uppercase tracking-wider">Ref/Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($movements as $m)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-600 font-medium">
                                {{ $m->created_at->format('M d, Y') }} <br>
                                <span class="text-[10px] text-gray-400 font-normal tracking-tight">{{ $m->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-gray-900">{{ $m->item->name }}</div>
                                <div class="text-[10px] text-gray-500 underline font-medium tracking-tight">SKU: {{ $m->item->sku }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeColor = match(strtolower($m->type)) {
                                        'in', 'purchase' => 'bg-green-100 text-green-700',
                                        'out', 'sale' => 'bg-blue-100 text-blue-700',
                                        'adjustment', 'adjust' => 'bg-yellow-100 text-yellow-700',
                                        default => 'bg-gray-100 text-gray-700'
                                    };
                                @endphp
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase {{ $typeColor }} border-b border-white/50">
                                    {{ $m->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs font-semibold text-gray-600">{{ $m->store->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-right font-bold {{ $m->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $m->quantity > 0 ? '+' : '' }}{{ number_format($m->quantity) }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-xs font-bold text-gray-900">{{ $m->reference ?? '-' }}</div>
                                <div class="text-[10px] text-gray-500 italic max-w-xs truncate">{{ $m->remarks }}</div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </main>
    </div>
@endsection
