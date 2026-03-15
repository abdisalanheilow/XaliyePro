@extends('admin.admin_master')

@section('title', 'Goods Receipts (GRN) - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Goods Receipts (GRN)</h1>
            <p class="text-sm text-gray-500 mt-1">Track physical arrival of goods and warehouse stock updates</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 inline-flex items-center gap-2 transition-all">
                <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                Export
            </button>
            <a href="{{ route('purchases.receipts.create') }}" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm">
                <i data-lucide="package-plus" class="w-4 h-4"></i>
                New Goods Receipt
            </a>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#28A375]"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Total Shipments</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $receipts->total() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total recorded GRNs</p>
                </div>
                <div class="w-12 h-12 bg-[#28A375]/10 rounded-lg flex items-center justify-center transition-colors group-hover:bg-[#28A375]/20">
                    <i data-lucide="truck" class="w-6 h-6 text-[#28A375]"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Items Received</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $receipts->total() }}</p>
                    <p class="text-xs text-gray-500 mt-1">Confirmed in stock</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-blue-100">
                    <i data-lucide="file-check" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-400"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Pending Bill</p>
                    <p class="text-3xl font-bold text-gray-900">0</p>
                    <p class="text-xs text-gray-500 mt-1">Awaiting invoicing</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-yellow-100">
                    <i data-lucide="landmark" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-red-400"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Rejected</p>
                    <p class="text-3xl font-bold text-gray-900">0</p>
                    <p class="text-xs text-gray-500 mt-1">Quality check failed</p>
                </div>
                <div class="w-12 h-12 bg-red-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-red-100">
                    <i data-lucide="shield-x" class="w-6 h-6 text-red-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form action="{{ route('purchases.receipts.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 w-full relative">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by Receipt #, Vendor or Challan..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:bg-white">
            </div>
            <button type="submit" class="p-2.5 bg-[#28A375] text-white rounded-lg hover:bg-[#229967] transition-colors">
                <i data-lucide="filter" class="w-5 h-5"></i>
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">GRN#</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">PO Reference</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if (count($receipts) > 0)
                        @foreach ($receipts as $receipt)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-[#28A375]">{{ $receipt->receipt_no }}</span>
                                @if ($receipt->delivery_challan_no)
                                <span class="text-[10px] text-gray-400 block font-medium">Challan: {{ $receipt->delivery_challan_no }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr($receipt->vendor->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $receipt->vendor->name ?? 'Unknown' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($receipt->order)
                                    <a href="{{ route('purchases.orders.show', $receipt->order->id) }}" class="text-xs font-bold text-[#28A375] hover:underline flex items-center gap-1">
                                        <i data-lucide="external-link" class="w-3 h-3"></i>
                                        {{ $receipt->order->order_no }}
                                    </a>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 uppercase">Direct</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $receipt->received_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20 uppercase">
                                    {{ ucfirst($receipt->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <a href="{{ route('purchases.receipts.show', $receipt->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#28A375]">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    @if (!$receipt->bill)
                                    <a href="{{ route('purchases.bills.create', ['receipt_id' => $receipt->id]) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-blue-600" title="Create Vendor Bill">
                                        <i data-lucide="file-text" class="w-4 h-4"></i>
                                    </a>
                                    @endif
                                    <button class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-gray-900">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center opacity-50">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100 shadow-sm">
                                    <i data-lucide="package" class="w-8 h-8 text-gray-300"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 tracking-tight">No receipts found</h3>
                                <p class="text-xs text-gray-500 mt-1">No goods receipts have been recorded yet</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if ($receipts->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $receipts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
