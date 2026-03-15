@extends('admin.admin_master')

@section('title', 'View Shipment - XaliyePro')

@section('admin')
<div class="space-y-6 text-sm">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
             <a href="{{ route('sales.receipts.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-600 transition-all shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                 <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Shipment: <span class="text-blue-500 font-black">{{ $receipt->delivery_no }}</span></h1>
                 <div class="flex items-center gap-2 mt-0.5">
                    <span class="text-[10px] font-black uppercase text-gray-400 tracking-widest">Source Order:</span>
                    <a href="{{ route('sales.orders.show', $receipt->sales_order_id) }}" class="text-[10px] font-black uppercase text-[#28A375] hover:underline">{{ $receipt->salesOrder->order_no ?? 'Standalone' }}</a>
                 </div>
            </div>
        </div>
        <div class="flex items-center gap-3">
             <button type="button" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm">
                <i data-lucide="printer" class="w-4 h-4 text-gray-400"></i>
                Print Lable
            </button>
             <button type="button" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm">
                <i data-lucide="mail" class="w-4 h-4 text-gray-400"></i>
                Email Customer
            </button>
             <div class="w-[1px] h-8 bg-gray-200 mx-1"></div>
             <a href="{{ route('sales.receipts.edit', $receipt->id) }}" class="px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-black transition-all shadow-lg shadow-gray-200">
                Modify Record
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
             <!-- Status / Metadata -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6 flex flex-wrap gap-12">
                <div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Shipment Date</div>
                    <div class="text-gray-900 font-bold flex items-center gap-2">
                        <i data-lucide="calendar" class="w-4 h-4 text-blue-500"></i>
                        {{ $receipt->delivery_date->format('M d, Y') }}
                    </div>
                </div>
                <div>
                    <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Origin Warehouse</div>
                    <div class="text-gray-900 font-bold flex items-center gap-2">
                        <i data-lucide="warehouse" class="w-4 h-4 text-orange-500"></i>
                        {{ $receipt->store->name ?? 'Primary Store' }}
                    </div>
                </div>
                <div>
                     <div class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1.5">Confirmation Status</div>
                     <span class="px-3 py-1 bg-[#28A375]/10 text-[#28A375] rounded-full border border-[#28A375]/10 font-black text-xs uppercase tracking-widest">
                        {{ $receipt->status }}
                     </span>
                </div>
            </div>

            <!-- Items -->
             <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-50 bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 tracking-tight uppercase text-[11px] tracking-[0.2em] text-gray-400">Shipment Manifest</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/20 border-b border-gray-100">
                                <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest">Product Details</th>
                                <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase tracking-widest text-center">Quantity Dispatched</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($receipt->items as $item)
                            <tr>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-gray-900 text-base leading-none mb-1.5">{{ $item->item->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest flex items-center gap-2">
                                        {{ $item->item->sku }}
                                        <div class="w-1 h-1 rounded-full bg-gray-300"></div>
                                        {{ $item->item->category->name ?? 'Inventory' }}
                                    </div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="inline-flex items-center gap-2 px-4 py-1.5 bg-blue-50 text-blue-600 rounded-lg font-black text-sm border border-blue-100/50">
                                        <i data-lucide="package" class="w-4 h-4"></i>
                                        {{ $item->quantity }} {{ $item->item->unit->name ?? 'PCS' }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

             <!-- Remarks -->
            @if ($receipt->notes)
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <h3 class="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em] mb-4">Dispatcher Remarks</h3>
                <p class="text-gray-600 leading-relaxed italic">{{ $receipt->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
             <!-- Customer Card -->
             <div class="bg-gray-900 text-white rounded-[2rem] p-8 shadow-2xl relative overflow-hidden group">
                <div class="relative z-10">
                    <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-white/30 mb-6">Consignee Details</h4>
                    <div class="flex flex-col items-center text-center mb-6">
                        <div class="w-16 h-16 bg-[#28A375] rounded-2xl flex items-center justify-center font-black text-2xl mb-4 group-hover:scale-110 transition-transform duration-500 shadow-xl shadow-green-900/40 border border-white/10">
                            {{ strtoupper(substr($receipt->customer->name, 0, 1)) }}
                        </div>
                        <div class="font-black text-xl tracking-tight leading-none mb-1">{{ $receipt->customer->name }}</div>
                        <div class="text-[10px] text-[#28A375] font-black uppercase tracking-widest opacity-80">Verified Customer</div>
                    </div>
                    <div class="space-y-4 pt-6 border-t border-white/5">
                        <div class="flex items-start gap-4 text-white/60">
                            <i data-lucide="mail" class="w-4 h-4 mt-0.5"></i>
                            <span class="text-xs font-medium">{{ $receipt->customer->email ?? '---' }}</span>
                        </div>
                        <div class="flex items-start gap-4 text-white/60">
                            <i data-lucide="phone" class="w-4 h-4 mt-0.5"></i>
                            <span class="text-xs font-medium">{{ $receipt->customer->phone ?? '---' }}</span>
                        </div>
                        <div class="flex items-start gap-4 text-white/60">
                             <i data-lucide="map-pin" class="w-4 h-4 mt-0.5"></i>
                             <address class="text-xs font-medium not-italic leading-relaxed">{{ $receipt->customer->address ?? 'No physical address found.' }}</address>
                        </div>
                    </div>
                </div>
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white/5 rounded-full blur-3xl"></div>
            </div>

            <!-- Signature / Verification box -->
             <div class="bg-white rounded-xl border-2 border-dashed border-gray-200 p-6 flex flex-col items-center text-center">
                 <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mb-3">
                    <i data-lucide="check-square" class="w-6 h-6 text-gray-300"></i>
                 </div>
                 <h4 class="font-bold text-gray-900 tracking-tight">Proof of Delivery</h4>
                 <p class="text-[11px] text-gray-400 mt-1 mb-4">Final customer signature required upon physical arrival of goods.</p>
                 <button class="w-full py-2 bg-gray-100 text-gray-400 font-black text-[10px] uppercase tracking-widest rounded-lg cursor-not-allowed">Awaiting Shipment</button>
            </div>
        </div>
    </div>
</div>
@endsection
