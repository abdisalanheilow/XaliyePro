@extends('admin.admin_master')

@section('title', 'Receive Goods from PO - XaliyePro')

@section('admin')
<form action="{{ route('purchases.receipts.store') }}" method="POST">
    @csrf
    <input type="hidden" name="purchase_order_id" value="{{ $order->id }}">
    <input type="hidden" name="vendor_id" value="{{ $order->vendor_id }}">
    <input type="hidden" name="received_by" value="{{ auth()->id() }}">

    <div class="space-y-6">
         <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('purchases.orders.show', $order->id) }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Receive Goods</h1>
                </div>
                <p class="text-sm text-gray-500">Creating GRN for Purchase Order <strong>{{ $order->order_no }}</strong></p>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] flex items-center gap-2 shadow-lg shadow-green-500/20 transition-all active:scale-95">
                    <i data-lucide="check-square" class="w-4 h-4"></i>
                    Validate Receipt
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <div class="lg:col-span-2 space-y-6">
                <!-- Receipt Details -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-black text-gray-900 uppercase tracking-widest mb-6 border-b border-gray-50 pb-4">Receipt Information</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">GRN Number</label>
                            <input type="text" name="receipt_no" value="{{ $receiptNo }}" readonly class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm font-bold text-gray-600">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Received Date *</label>
                            <input type="date" name="received_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Delivery Challan #</label>
                            <input type="text" name="delivery_challan_no" placeholder="e.g. DC-99120" class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                        </div>
                    </div>

                    <div class="mt-8">
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Line Item Verification</h3>
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="w-full text-left">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Item</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Ordered</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Received</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">QC Failed</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach ($order->items as $index => $item)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-4">
                                            <input type="hidden" name="items[{{ $index }}][item_id]" value="{{ $item->item_id }}">
                                            <span class="text-sm font-bold text-gray-900 block">{{ $item->item->name }}</span>
                                            <span class="text-[10px] text-gray-400 tabular-nums">SKU: {{ $item->item->sku }}</span>
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <input type="hidden" name="items[{{ $index }}][ordered_qty]" value="{{ $item->quantity }}">
                                            <span class="text-xs font-black text-gray-400">{{ number_format($item->quantity, 2) }}</span>
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" name="items[{{ $index }}][received_qty]" value="{{ $item->quantity }}" step="0.01" class="w-24 mx-auto px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-center focus:ring-2 focus:ring-[#28A375]">
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" name="items[{{ $index }}][rejected_qty]" value="0" step="0.01" class="w-24 mx-auto px-3 py-1.5 border border-gray-200 rounded-lg text-sm font-bold text-center text-red-600 focus:ring-2 focus:ring-red-500">
                                        </td>
                                        <td class="px-4 py-4">
                                            <select name="items[{{ $index }}][quality_status]" class="w-full px-2 py-1.5 border border-gray-200 rounded-lg text-[10px] font-bold uppercase tracking-tight focus:ring-2 focus:ring-[#28A375]">
                                                <option value="passed" class="text-green-600">PASSED</option>
                                                <option value="partially_failed" class="text-orange-600">PARTIAL</option>
                                                <option value="failed" class="text-red-600">REJECTED</option>
                                            </select>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <!-- Logistics Info -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-50 pb-4 mb-6">Warehouse Assignment</h3>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Destination Branch</label>
                            <div class="p-3 bg-gray-50 rounded-lg border border-gray-100 text-sm font-bold text-gray-600">
                                {{ $order->branch->name ?? 'Primary Branch' }}
                                <input type="hidden" name="branch_id" value="{{ $order->branch_id }}">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Storage Location (Store) *</label>
                            <select name="store_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                                <option value="{{ $order->store_id }}">{{ $order->store->name ?? 'Default Store' }}</option>
                                <!-- Map other stores if needed -->
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-widest text-xs">Internal Notes</h3>
                    <textarea name="notes" rows="4" placeholder="Any shipping damages or notes from the carrier..." class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
