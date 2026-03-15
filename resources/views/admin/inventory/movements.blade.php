@extends('admin.admin_master')

@section('title', 'Stock Movements - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Stock Movements</h1>
                <p class="text-sm text-gray-500">Chronological ledger of all inventory activities</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2.5 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all active:scale-95">
                    <i data-lucide="printer" class="w-4 h-4 text-gray-400"></i>
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Transactions -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-slate-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Total Activities</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_moves']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-gray-400">Recorded movements</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-slate-500 rounded-xl flex items-center justify-center shadow-lg shadow-slate-100">
                    <i data-lucide="activity" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Stock In -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-emerald-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Stock Inbound</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['stock_in']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-emerald-500">Additions to stock</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-emerald-500 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-100">
                    <i data-lucide="plus-square" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Stock Out -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-rose-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Stock Outbound</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['stock_out']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-rose-500">Deductions from stock</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-rose-500 rounded-xl flex items-center justify-center shadow-lg shadow-rose-100">
                    <i data-lucide="minus-square" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Total Volume -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-cyan-500">
                <div>
                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Net Flow Volume</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_volume']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <span class="text-[10px] font-bold text-cyan-500">Cumulative unit change</span>
                    </div>
                </div>
                <div class="w-12 h-12 bg-cyan-500 rounded-xl flex items-center justify-center shadow-lg shadow-cyan-100">
                    <i data-lucide="layers-2" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Movement Ledger</h3>
                
                <!-- Search -->
                <form action="{{ route('inventory.movements') }}" method="GET" class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" 
                        placeholder="Search SKU, reference, notes..."
                        class="pl-10 pr-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none w-full md:w-80 transition-all">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                </form>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Date & Time</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Item Details</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Quantity</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Reference</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">User</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if (count($movements) > 0)
                            @foreach ($movements as $move)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-6 py-4">
                                    <div class="flex flex-col items-center">
                                        <span class="text-xs font-bold text-gray-900">{{ $move->created_at->format('M d, Y') }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium tracking-tighter">{{ $move->created_at->format('H:i A') }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-gray-50 flex items-center justify-center border border-gray-100 group-hover:bg-white group-hover:shadow-sm transition-all">
                                            <i data-lucide="package" class="w-5 h-5 text-gray-400"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 leading-tight">{{ $move->item->name }}</div>
                                            <div class="text-[10px] text-gray-400 font-black tracking-widest">{{ $move->item->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $typeColors = [
                                            'IN' => 'text-emerald-700 bg-emerald-50 border-emerald-100',
                                            'OUT' => 'text-rose-700 bg-rose-50 border-rose-100',
                                            'ADJUST' => 'text-blue-700 bg-blue-50 border-blue-100',
                                            'TRANSFER' => 'text-amber-700 bg-amber-50 border-amber-100',
                                            'PURCHASE' => 'text-indigo-700 bg-indigo-50 border-indigo-100',
                                            'SALE' => 'text-purple-700 bg-purple-50 border-purple-100',
                                        ];
                                        $colorClass = $typeColors[$move->type] ?? 'text-gray-600 bg-gray-50 border-gray-100';
                                    @endphp
                                    <span class="px-2.5 py-1 text-[10px] font-black uppercase rounded shadow-sm border {{ $colorClass }}">
                                        {{ $move->type }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black {{ $move->quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                        {{ $move->quantity > 0 ? '+' : '' }}{{ number_format($move->quantity) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5 px-2 py-1 bg-gray-50 rounded-lg text-xs font-bold text-gray-600 border border-gray-100">
                                        <i data-lucide="map-pin" class="w-3 h-3 text-gray-400"></i>
                                        {{ $move->store->name ?? 'Primary Whse' }}
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-xs font-bold text-gray-900 font-mono bg-slate-50 px-2 py-1 rounded border border-slate-100">
                                        {{ $move->reference ?? '---' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-7 h-7 rounded-full bg-[#28A375]/10 flex items-center justify-center text-[10px] font-bold text-[#28A375] border border-[#28A375]/10">
                                            {{ strtoupper(substr($move->creator->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div class="flex flex-col">
                                            <span class="text-[11px] font-bold text-gray-700">{{ $move->creator->name ?? 'System' }}</span>
                                            <span class="text-[9px] text-gray-400 font-medium">Logged by</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                        <tr>
                            <td colspan="7" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center gap-2 text-gray-400 opacity-50">
                                    <i data-lucide="database" class="w-12 h-12"></i>
                                    <p class="text-sm font-medium italic">
                                        @if (request('search'))
                                            No movements match "{{ request('search') }}"
                                        @else
                                            No activities recorded in the ledger yet.
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
                {{ $movements->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
@endsection
