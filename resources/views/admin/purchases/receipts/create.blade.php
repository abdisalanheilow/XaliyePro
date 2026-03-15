@extends('admin.admin_master')

@section('title', 'Receive New Goods - XaliyePro')

@section('admin')
<form action="{{ route('purchases.receipts.store') }}" method="POST">
    @csrf
    <input type="hidden" name="received_by" value="{{ auth()->id() }}">

    <div class="space-y-6">
         <!-- Header -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('purchases.receipts.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Direct Goods Receipt</h1>
                </div>
                <p class="text-sm text-gray-500">Creating a GRN without a Purchase Order.</p>
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
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Vendor *</label>
                            <select name="vendor_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                                <option value="">Select Vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                                @endforeach
                            </select>
                        </div>
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
                        <div class="flex items-center justify-between mb-4 border-t border-gray-50 pt-8">
                            <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest">Line Items</h3>
                            <button type="button" onclick="addReceiptLine()" class="text-xs font-bold text-white bg-[#28A375] px-3 py-1.5 rounded hover:bg-[#229967] transition-colors shadow-sm">
                                + Add Item
                            </button>
                        </div>
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="w-full text-left" id="receiptLinesTable">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest w-64">Item</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">Received</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest text-center">QC Failed</th>
                                        <th class="px-4 py-3 text-[10px] font-black text-gray-400 uppercase tracking-widest">Status</th>
                                        <th class="w-10"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50" id="receiptLinesBody">
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-4 py-3">
                                            <select name="items[0][item_id]" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                                                <option value="">Select Product...</option>
                                                @foreach ($items as $item)
                                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="items[0][received_qty]" value="1" min="0.01" step="0.01" class="w-24 mx-auto px-3 py-2 border border-gray-200 rounded-lg text-sm font-bold text-center focus:ring-2 focus:ring-[#28A375]">
                                        </td>
                                        <td class="px-4 py-3">
                                            <input type="number" name="items[0][rejected_qty]" value="0" min="0" step="0.01" class="w-24 mx-auto px-3 py-2 border border-gray-200 rounded-lg text-sm font-bold text-center text-red-600 focus:ring-2 focus:ring-red-500">
                                        </td>
                                        <td class="px-4 py-3">
                                            <select name="items[0][quality_status]" class="w-full px-2 py-2 border border-gray-200 rounded-lg text-[10px] font-bold uppercase tracking-tight focus:ring-2 focus:ring-[#28A375]">
                                                <option value="passed" class="text-green-600">PASSED</option>
                                                <option value="partially_failed" class="text-orange-600">PARTIAL</option>
                                                <option value="failed" class="text-red-600">REJECTED</option>
                                            </select>
                                        </td>
                                        <td class="px-4 py-3 text-right">
                                            <button type="button" onclick="removeReceiptLine(this)" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg hover:text-red-600 transition-colors">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 border-b border-gray-50 pb-4 mb-6">Warehouse Assignment</h3>
                    
                    <div class="space-y-4">
                        <div x-data="{
                            selectedBranch: '',
                            stores: [
                                @foreach ($stores as $store)
                                { id: '{{ $store->id }}', name: '{{ $store->name }}', branch_id: '{{ $store->branch_id }}' },
                                @endforeach
                            ]
                        }">
                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Destination Branch</label>
                            <select name="branch_id" x-model="selectedBranch" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] mb-4">
                                <option value="">Select Branch</option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>

                            <label class="block text-xs font-bold text-gray-500 uppercase mb-2">Storage Location (Store) *</label>
                            <select name="store_id" required class="w-full px-4 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                                <option value="">Select Store</option>
                                <template x-for="store in stores.filter(s => s.branch_id == selectedBranch)" :key="store.id">
                                    <option :value="store.id" x-text="store.name"></option>
                                </template>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-sm font-bold text-gray-900 mb-4 uppercase tracking-widest text-xs">Internal Notes</h3>
                    <textarea name="notes" rows="4" placeholder="Reason for direct GRN without PO, shipping damages, etc." class="w-full px-4 py-3 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] resize-none"></textarea>
                </div>
            </div>
        </div>
    </div>
</form>

<!-- Template for new JS rows -->
<template id="receiptLineTemplate">
    <tr class="hover:bg-gray-50/50 transition-colors">
        <td class="px-4 py-3">
            <select name="items[{idx}][item_id]" required class="w-full px-3 py-2 border border-gray-200 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                <option value="">Select Product...</option>
                @foreach ($items as $item)
                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                @endforeach
            </select>
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[{idx}][received_qty]" value="1" min="0.01" step="0.01" class="w-24 mx-auto px-3 py-2 border border-gray-200 rounded-lg text-sm font-bold text-center focus:ring-2 focus:ring-[#28A375]">
        </td>
        <td class="px-4 py-3">
            <input type="number" name="items[{idx}][rejected_qty]" value="0" min="0" step="0.01" class="w-24 mx-auto px-3 py-2 border border-gray-200 rounded-lg text-sm font-bold text-center text-red-600 focus:ring-2 focus:ring-red-500">
        </td>
        <td class="px-4 py-3">
            <select name="items[{idx}][quality_status]" class="w-full px-2 py-2 border border-gray-200 rounded-lg text-[10px] font-bold uppercase tracking-tight focus:ring-2 focus:ring-[#28A375]">
                <option value="passed" class="text-green-600">PASSED</option>
                <option value="partially_failed" class="text-orange-600">PARTIAL</option>
                <option value="failed" class="text-red-600">REJECTED</option>
            </select>
        </td>
        <td class="px-4 py-3 text-right">
            <button type="button" onclick="removeReceiptLine(this)" class="p-1.5 text-red-400 hover:bg-red-50 rounded-lg hover:text-red-600 transition-colors">
                <i data-lucide="trash-2" class="w-4 h-4"></i>
            </button>
        </td>
    </tr>
</template>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    
    let lineIdx = 1;
    function addReceiptLine() {
        const tbody = document.getElementById('receiptLinesBody');
        const template = document.getElementById('receiptLineTemplate').innerHTML;
        const newHtml = template.replace(/{idx}/g, lineIdx++);
        
        tbody.insertAdjacentHTML('beforeend', newHtml);
        lucide.createIcons();
    }
    
    function removeReceiptLine(btn) {
        if(document.querySelectorAll('#receiptLinesBody tr').length > 1) {
            btn.closest('tr').remove();
            reindexLines();
        }
    }
    
    function reindexLines() {
        const rows = document.querySelectorAll('#receiptLinesBody tr');
        rows.forEach((row, index) => {
            row.querySelector('select[name$="[item_id]"]').name = `items[${index}][item_id]`;
            row.querySelector('input[name$="[received_qty]"]').name = `items[${index}][received_qty]`;
            row.querySelector('input[name$="[rejected_qty]"]').name = `items[${index}][rejected_qty]`;
            row.querySelector('select[name$="[quality_status]"]').name = `items[${index}][quality_status]`;
        });
        lineIdx = rows.length;
    }
</script>
@endpush
