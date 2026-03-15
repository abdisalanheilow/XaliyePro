@extends('admin.admin_master')

@section('title', 'Edit Delivery Note - XaliyePro')

@section('admin')
<form action="{{ route('sales.receipts.update', $receipt->id) }}" method="POST" id="deliveryNoteForm" x-data="{ isSaving: false }" @submit="isSaving = true">
    @csrf
    @method('PUT')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center justify-between">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('sales.receipts.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Shipment: <span class="text-blue-600">{{ $receipt->delivery_no }}</span></h1>
                </div>
                <p class="text-sm text-gray-500 font-medium tracking-tight">Modify shipment records or update delivery status</p>
            </div>
            <div class="flex items-center gap-3">
                 <button type="submit" :disabled="isSaving" class="px-6 py-2.5 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-black transition-all flex items-center gap-2 shadow-lg shadow-gray-200 disabled:opacity-50 active:scale-95">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                             <i data-lucide="save" class="w-4 h-4"></i>
                             Update Record
                        </div>
                    </template>
                     <template x-if="isSaving">
                        <div class="flex items-center gap-2">
                             <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Updating...
                        </div>
                    </template>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main column -->
            <div class="lg:col-span-2 space-y-6 text-sm">
                <!-- Info -->
                 <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm">
                    <div class="flex items-center gap-2 mb-8">
                         <div class="p-2 bg-blue-50 rounded-lg">
                            <i data-lucide="edit-3" class="w-5 h-5 text-blue-500"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 tracking-tight">Record Modification</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                             <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Customer Entity</label>
                             <input type="text" value="{{ $receipt->customer->name }}" readonly class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-gray-500 font-bold focus:outline-none">
                             <input type="hidden" name="customer_id" value="{{ $receipt->customer_id }}">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Shipment Date <span class="text-red-500">*</span></label>
                            <input type="date" name="delivery_date" value="{{ $receipt->delivery_date->format('Y-m-d') }}" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none">
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Dispatcher Hub</label>
                             <select name="store_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none font-bold">
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" {{ $receipt->store_id == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Current Status</label>
                            <select name="status" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none font-black text-blue-600 uppercase tracking-widest text-[11px]">
                                <option value="pending" {{ $receipt->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="shipped" {{ $receipt->status == 'shipped' ? 'selected' : '' }}>Shipped</option>
                                <option value="delivered" {{ $receipt->status == 'delivered' ? 'selected' : '' }}>Delivered</option>
                                <option value="cancelled" {{ $receipt->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Manifest -->
                 <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                        <h3 class="font-black text-[10px] text-gray-400 uppercase tracking-[0.2em]">Shipment Manifest</h3>
                    </div>
                    <div class="overflow-x-auto">
                         <table class="w-full text-left">
                            <thead class="bg-gray-50/20 shadow-sm">
                                <tr>
                                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase">Item Description</th>
                                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase text-center">Qty Fixed</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach ($receipt->items as $idx => $item)
                                <tr>
                                    <td class="px-6 py-5">
                                        <div class="font-bold text-gray-900">{{ $item->item->name }}</div>
                                        <div class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">{{ $item->item->sku }}</div>
                                        <input type="hidden" name="items[{{ $idx }}][item_id]" value="{{ $item->item_id }}">
                                    </td>
                                    <td class="px-6 py-5">
                                         <input type="number" name="items[{{ $idx }}][quantity]" value="{{ $item->delivered_qty }}" required class="w-24 mx-auto block px-3 py-2 border border-gray-300 rounded-lg text-center font-black focus:ring-2 focus:ring-[#28A375] outline-none">
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <div class="p-6 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <h4 class="text-[11px] font-black uppercase text-gray-400 tracking-widest mb-4">Internal Tracking</h4>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 opacity-60">Dispatcher ID</label>
                            <input type="text" value="{{ $receipt->deliverer->name ?? 'System' }}" readonly class="w-full bg-transparent border-b border-gray-200 py-1 text-gray-900 font-bold focus:outline-none">
                        </div>
                        <div>
                             <label class="block text-[10px] font-black text-gray-500 uppercase tracking-widest mb-1.5 opacity-60">Log Reference</label>
                             <input type="text" value="{{ $receipt->delivery_no }}" readonly class="w-full bg-transparent border-b border-gray-200 py-1 text-blue-500 font-black focus:outline-none">
                        </div>
                    </div>
                </div>

                 <div class="bg-blue-600 rounded-xl p-6 text-white shadow-xl shadow-blue-100 relative overflow-hidden">
                    <div class="relative z-10">
                        <h4 class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">Note on Edits</h4>
                        <div class="text-[11px] leading-relaxed opacity-90 font-medium">
                            Changing quantities here currently <span class="font-bold underline decoration-white-400">will not</span> automatically sync back to main inventory. Please use Adjustments for manual correction.
                        </div>
                    </div>
                    <i data-lucide="info" class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-5 rotate-12"></i>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
