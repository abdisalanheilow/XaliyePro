@extends('admin.admin_master')

@section('title', 'Adjustment Details - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.adjustments.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-900 transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $adjustment->adjustment_no }}</h1>
                <p class="text-sm text-gray-500">Details for adjustment on {{ $adjustment->adjustment_date->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if ($adjustment->status === 'draft')
                <form action="{{ route('inventory.adjustments.finalize', $adjustment->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to finalize this adjustment? This will update the inventory stock levels.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all shadow-sm active:scale-95 flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Finalize & Update Stock
                    </button>
                </form>
            @endif
            <button class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                <i data-lucide="printer" class="w-4 h-4 text-gray-400"></i>
                Print
            </button>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Status</p>
            @php
                $statusClasses = [
                    'draft' => 'bg-gray-100 text-gray-600 border-gray-200',
                    'adjusted' => 'bg-green-100 text-green-600 border-green-200',
                    'cancelled' => 'bg-red-100 text-red-600 border-red-200',
                ];
            @endphp
            <span class="px-2.5 py-1 text-[10px] font-bold uppercase rounded-md border {{ $statusClasses[$adjustment->status] ?? 'bg-gray-100' }}">
                {{ $adjustment->status }}
            </span>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Store Location</p>
            <p class="text-sm font-bold text-gray-900">{{ $adjustment->store->name }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Reason</p>
            <p class="text-sm font-bold text-gray-900">{{ $adjustment->reason }}</p>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-1">Created By</p>
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-[10px] font-bold text-gray-500">
                    {{ strtoupper(substr($adjustment->creator->name ?? 'A', 0, 1)) }}
                </div>
                <p class="text-sm font-bold text-gray-900">{{ $adjustment->creator->name ?? 'System' }}</p>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Adjustment Lines</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/30">
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Item Details</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Old Quantity</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">New Quantity</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Difference</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Unit Cost</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Impact Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($adjustment->items as $adjItem)
                    <tr class="hover:bg-gray-50/50 transition-all">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $adjItem->item->name }}</div>
                                <div class="text-[10px] text-gray-400 font-bold">SKU: {{ $adjItem->item->sku }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-medium text-gray-500">
                            {{ number_format($adjItem->quantity_before) }}
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-900">
                            {{ number_format($adjItem->quantity_after) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black {{ $adjItem->adjustment_quantity >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $adjItem->adjustment_quantity >= 0 ? '+' : '' }}{{ number_format($adjItem->adjustment_quantity) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm font-bold text-gray-600">
                            ${{ number_format($adjItem->unit_cost, 2) }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black {{ ($adjItem->adjustment_quantity * $adjItem->unit_cost) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($adjItem->adjustment_quantity * $adjItem->unit_cost, 2) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50/50 border-t border-gray-100">
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-right text-[11px] font-bold text-gray-400 uppercase">Total Value Impact</td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-lg font-black text-gray-900">
                                ${{ number_format($adjustment->items->sum(fn($i) => $i->adjustment_quantity * $i->unit_cost), 2) }}
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Notes -->
    @if ($adjustment->notes)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-3">Notes / Remarks</h3>
        <p class="text-sm text-gray-600 italic leading-relaxed">
            "{{ $adjustment->notes }}"
        </p>
    </div>
    @endif
</div>
@endsection
