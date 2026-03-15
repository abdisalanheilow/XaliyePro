@extends('admin.admin_master')

@section('title', 'New Delivery Note - XaliyePro')

@section('admin')
<form action="{{ route('sales.receipts.store') }}" method="POST" id="deliveryNoteForm" x-data="{ isSaving: false }" @submit="isSaving = true">
    @csrf
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('sales.receipts.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">New Delivery Note</h1>
                </div>
                <p class="text-sm text-gray-500 font-medium">Create a delivery note to document stock movement to customers</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="isSaving" class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-lg shadow-green-100 disabled:opacity-50">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                             <i data-lucide="truck" class="w-4 h-4"></i>
                             Confirm Shipment
                        </div>
                    </template>
                     <template x-if="isSaving">
                        <div class="flex items-center gap-2">
                             <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </div>
                    </template>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Order info -->
            <div class="lg:col-span-2 space-y-6 text-sm">
                <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm">
                    <div class="flex items-center gap-2 mb-8">
                         <div class="p-2 bg-blue-50 rounded-lg">
                            <i data-lucide="info" class="w-5 h-5 text-blue-500"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Shipment Details</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Customer <span class="text-red-500">*</span></label>
                            <select name="customer_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none transition-all">
                                <option value="">Select Customer</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Source Order</label>
                            <select name="sales_order_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none transition-all">
                                <option value="">Standalone Delivery</option>
                                @foreach ($orders as $order)
                                    <option value="{{ $order->id }}" {{ request('order_id') == $order->id ? 'selected' : '' }}>{{ $order->order_no }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Delivery Date <span class="text-red-500">*</span></label>
                            <input type="date" name="delivery_date" value="{{ date('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none transition-all">
                        </div>
                         <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Dispatcher Warehouse <span class="text-red-500">*</span></label>
                            <select name="store_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none transition-all font-bold">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Items Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/10">
                         <h2 class="text-[11px] font-black text-gray-400 uppercase tracking-[0.2em]">Shipping Items</h2>
                         <button type="button" onclick="addShipmentItem()" class="text-[#28A375] font-black text-[11px] uppercase tracking-widest hover:underline">+ Add Entry Manually</button>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left">
                            <thead class="bg-gray-50/50">
                                <tr>
                                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Item Name</th>
                                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider w-40 text-center">Shipping Qty</th>
                                    <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider w-16 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="shipmentItemsBody">
                                <tr class="border-b border-gray-50 hover:bg-gray-50/20 transition-all">
                                    <td class="px-6 py-4">
                                        <select name="items[0][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none border-transparent bg-gray-50 focus:bg-white transition-all">
                                            <option value="">Choose item...</option>
                                            @foreach ($items ?? [] as $item)
                                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-6 py-4">
                                        <input type="number" name="items[0][quantity]" value="1" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center font-black focus:ring-2 focus:ring-[#28A375] outline-none border-transparent bg-gray-50 focus:bg-white transition-all">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                         <button type="button" onclick="this.closest('tr').remove()" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6 text-sm">
                <div class="bg-gray-900 rounded-xl p-6 shadow-xl space-y-6 border border-gray-800">
                    <h3 class="text-white font-black text-[10px] uppercase tracking-[0.3em] opacity-40">Documentation</h3>
                    <div>
                        <label class="block text-white/60 font-bold text-[11px] uppercase tracking-widest mb-2">Delivery Note No.</label>
                        <input type="text" name="delivery_no" value="DN-{{ date('ymd') }}-{{ rand(100, 999) }}" readonly class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white font-black focus:outline-none">
                    </div>
                    <div>
                         <label class="block text-white/60 font-bold text-[11px] uppercase tracking-widest mb-2">Internal Remarks</label>
                         <textarea name="notes" rows="3" class="w-full bg-gray-800 border border-gray-700 rounded-lg px-4 py-2.5 text-white/80 focus:outline-none focus:ring-1 focus:ring-[#28A375]" placeholder="Packing instructions, delicate items..."></textarea>
                    </div>
                </div>

                <div class="p-6 bg-[#28A375]/5 rounded-xl border border-dashed border-[#28A375]/30">
                    <div class="flex items-center gap-2 mb-2">
                        <i data-lucide="shield-check" class="w-4 h-4 text-[#28A375]"></i>
                        <h4 class="font-black text-[#28A375] text-[10px] uppercase tracking-wider">Stock Policy</h4>
                    </div>
                    <p class="text-[11px] text-gray-500 leading-relaxed font-bold">Finalizing this delivery note will <span class="text-red-500 underline">immediately decrement</span> item stock from the selected warehouse.</p>
                </div>
            </div>
        </div>
    </div>
</form>

<script>
    lucide.createIcons();
    let rowIdx = 1;
    function addShipmentItem() {
        const tbody = document.getElementById('shipmentItemsBody');
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-50 hover:bg-gray-50/20 transition-all';
        row.innerHTML = `
            <td class="px-6 py-4">
                <select name="items[${rowIdx}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none border-transparent bg-gray-50 focus:bg-white transition-all">
                    <option value="">Choose item...</option>
                    @foreach ($items ?? [] as $item)
                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-6 py-4">
                <input type="number" name="items[${rowIdx}][quantity]" value="1" min="1" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-center font-black focus:ring-2 focus:ring-[#28A375] outline-none border-transparent bg-gray-50 focus:bg-white transition-all">
            </td>
            <td class="px-6 py-4 text-center">
                 <button type="button" onclick="this.closest('tr').remove()" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        lucide.createIcons();
        rowIdx++;
    }
</script>
@endsection
