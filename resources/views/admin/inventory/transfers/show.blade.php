@extends('admin.admin_master')

@section('title', 'Transfer Details - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('inventory.transfers.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-900 transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $transfer->transfer_no }}</h1>
                <p class="text-sm text-gray-500">Scheduled for {{ $transfer->transfer_date->format('M d, Y') }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @if ($transfer->status === 'draft')
                <form action="{{ route('inventory.transfers.finalize', $transfer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to finalize this transfer? This will record the stock movement in the ledger.')">
                    @csrf @method('PATCH')
                    <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-all shadow-sm active:scale-95 flex items-center gap-2">
                        <i data-lucide="truck" class="w-4 h-4"></i>
                        Finalize Transfer
                    </button>
                </form>
            @endif
            <button class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                <i data-lucide="printer" class="w-4 h-4 text-gray-400"></i>
                Gate Pass
            </button>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white p-6 rounded-xl border border-blue-100 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 p-3 opacity-10">
                <i data-lucide="log-out" class="w-12 h-12 text-blue-600"></i>
            </div>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">From Location</p>
            <p class="text-lg font-black text-blue-600">{{ $transfer->fromStore->name }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $transfer->fromStore->code ?? 'SCR-LOC' }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-green-100 shadow-sm relative overflow-hidden">
            <div class="absolute right-0 top-0 p-3 opacity-10">
                <i data-lucide="log-in" class="w-12 h-12 text-green-600"></i>
            </div>
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">To Location</p>
            <p class="text-lg font-black text-green-600">{{ $transfer->toStore->name }}</p>
            <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $transfer->toStore->code ?? 'DST-LOC' }}</p>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Transfer Status</p>
            @php
                $statusClasses = [
                    'draft' => 'bg-gray-100 text-gray-600 border-gray-200',
                    'pending' => 'bg-orange-100 text-orange-600 border-orange-200',
                    'transferred' => 'bg-green-600 text-white border-green-700',
                    'cancelled' => 'bg-red-100 text-red-600 border-red-200',
                ];
            @endphp
            <span class="px-3 py-1 text-[11px] font-black uppercase rounded shadow-sm {{ $statusClasses[$transfer->status] ?? 'bg-gray-100' }}">
                {{ $transfer->status }}
            </span>
        </div>

        <div class="bg-white p-6 rounded-xl border border-gray-100 shadow-sm relative overflow-hidden">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider mb-2">Handled By</p>
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-[#28A375]/10 flex items-center justify-center text-xs font-bold text-[#28A375]">
                    {{ strtoupper(substr($transfer->creator->name ?? 'A', 0, 1)) }}
                </div>
                <p class="text-sm font-bold text-gray-900">{{ $transfer->creator->name ?? 'System' }}</p>
            </div>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-gray-100 bg-gray-50/50">
            <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider">Transfer Manifest</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/30">
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider">SKU & Item Name</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Quantity to Move</th>
                        <th class="px-6 py-3 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Unit Type</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach ($transfer->items as $trfItem)
                    <tr class="hover:bg-gray-50/50 transition-all">
                        <td class="px-6 py-4">
                            <div>
                                <div class="text-sm font-bold text-gray-900">{{ $trfItem->item->name }}</div>
                                <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $trfItem->item->sku }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-sm font-black text-gray-900">{{ number_format($trfItem->quantity) }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="text-[10px] font-bold text-gray-400 uppercase">{{ $trfItem->item->unit->short_name ?? 'Units' }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if ($transfer->notes)
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
        <h3 class="text-sm font-bold text-gray-900 uppercase tracking-wider mb-2 flex items-center gap-2">
            <i data-lucide="message-square" class="w-4 h-4 text-gray-400"></i>
            Notes & Remarks
        </h3>
        <p class="text-sm text-gray-600 leading-relaxed italic">
            {{ $transfer->notes }}
        </p>
    </div>
    @endif
</div>
@endsection
