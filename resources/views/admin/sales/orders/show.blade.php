@extends('admin.admin_master')

@section('title', 'View Sales Order - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.orders.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-600 transition-all">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Sales Order: <span class="text-[#28A375]">{{ $order->order_no }}</span></h1>
                <p class="text-sm text-gray-500">View detailed information and fulfillment status</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                <i data-lucide="printer" class="w-4 h-4 text-gray-400"></i>
                Print
            </button>
            <a href="{{ route('sales.orders.edit', $order->id) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-blue-600 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                <i data-lucide="edit" class="w-4 h-4"></i>
                Edit Order
            </a>
            @if (in_array($order->status, ['confirmed', 'processing']))
                <a href="{{ route('sales.receipts.create', ['order_id' => $order->id]) }}" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                    Fulfill Order
                </a>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Info -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Order Details Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden text-sm">
                <div class="p-6 border-b border-gray-50 flex items-center justify-between bg-gray-50/50">
                    <h3 class="font-bold text-gray-900 tracking-tight uppercase text-xs">Items Details</h3>
                    <span class="px-2.5 py-1 font-bold rounded-md bg-blue-50 text-blue-600 border border-blue-100 uppercase text-[10px] tracking-wider">{{ $order->status }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/30">
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase">Item</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-center">Qty</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">Unit Price</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($order->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 tracking-tight">{{ $item->item->name }}</div>
                                    <div class="text-[11px] text-gray-400 font-medium tracking-tighter">SKU: {{ $item->item->sku }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 tracking-tighter">${{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 border-gray-100">
                                <td colspan="3" class="px-6 py-4 text-right font-bold text-gray-500">Subtotal</td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 tracking-tighter">${{ number_format($order->total_amount, 2) }}</td>
                            </tr>
                            @if ($order->tax_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2 text-right font-bold text-gray-500">Tax</td>
                                <td class="px-6 py-2 text-right font-black text-gray-900 tracking-tighter">${{ number_format($order->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if ($order->discount_amount > 0)
                            <tr>
                                <td colspan="3" class="px-6 py-2 text-right font-bold text-red-500">Discount</td>
                                <td class="px-6 py-2 text-right font-black text-red-600 tracking-tighter">-${{ number_format($order->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-gray-50/50">
                                <td colspan="3" class="px-6 py-5 text-right font-black text-gray-900 uppercase">Grand Total</td>
                                <td class="px-6 py-5 text-right font-black text-[#28A375] text-lg tracking-tighter">${{ number_format($order->grand_total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Notes & Terms -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i data-lucide="sticky-note" class="w-3 h-3 text-[#28A375]"></i>
                        Order Notes
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed italic">{{ $order->notes ?? 'No internal notes provided for this order.' }}</p>
                </div>
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4 flex items-center gap-2">
                        <i data-lucide="file-text" class="w-3 h-3 text-blue-500"></i>
                        Terms & Conditions
                    </h3>
                    <p class="text-sm text-gray-600 leading-relaxed">{{ $order->terms ?? 'Standard payment terms apply.' }}</p>
                </div>
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6 text-sm">
            <!-- Customer Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-50 bg-[#28A375]/5">
                    <h3 class="font-bold text-gray-900 flex items-center gap-2">
                        <i data-lucide="user" class="w-4 h-4 text-[#28A375]"></i>
                        Customer Information
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-[#28A375]/10 flex items-center justify-center font-black text-[#28A375] text-lg">
                            {{ strtoupper(substr($order->customer->name, 0, 1)) }}
                        </div>
                        <div>
                            <div class="font-bold text-gray-900 text-base leading-tight">{{ $order->customer->name }}</div>
                            <div class="text-[11px] text-gray-400 font-bold uppercase tracking-widest">CUS-{{ str_pad($order->customer->id, 4, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>
                    <div class="space-y-3 pt-4 border-t border-gray-50">
                        <div class="flex items-start gap-3">
                            <i data-lucide="mail" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                            <div class="text-gray-600 font-medium">{{ $order->customer->email ?? '---' }}</div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i data-lucide="phone" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                            <div class="text-gray-600 font-medium">{{ $order->customer->phone ?? '---' }}</div>
                        </div>
                        <div class="flex items-start gap-3">
                            <i data-lucide="map-pin" class="w-4 h-4 text-gray-400 mt-0.5"></i>
                            <div class="text-gray-600 font-medium leading-relaxed">{{ $order->customer->address ?? '---' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Meta Information -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4">
                <div class="flex items-center justify-between pb-3 border-b border-gray-50">
                    <span class="text-gray-400 font-bold text-[11px] uppercase tracking-wider">Order Date</span>
                    <span class="font-bold text-gray-900">{{ $order->order_date->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-gray-50">
                    <span class="text-gray-400 font-bold text-[11px] uppercase tracking-wider">Branch</span>
                    <span class="font-bold text-gray-900">{{ $order->branch->name ?? 'Main Branch' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-400 font-bold text-[11px] uppercase tracking-wider">Created By</span>
                    <div class="text-right">
                        <div class="font-bold text-gray-900">{{ $order->creator->name ?? 'System' }}</div>
                        <div class="text-[10px] text-gray-400 font-medium uppercase">{{ $order->created_at->format('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>

             <!-- Quick Actions -->
             <div class="bg-gray-900 rounded-xl p-5 shadow-lg shadow-gray-200">
                <h4 class="text-white font-bold text-sm mb-4">Linked Documents</h4>
                <div class="space-y-2">
                    <div class="flex items-center justify-between p-2.5 bg-gray-800 rounded-lg group cursor-pointer hover:bg-gray-700 transition-all border border-gray-700/50">
                        <div class="flex items-center gap-2">
                             <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                             <span class="text-xs text-gray-300 font-bold tracking-tight">Delivery Notes</span>
                        </div>
                        <span class="text-xs text-blue-400 font-black">{{ $order->deliveryNotes->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between p-2.5 bg-gray-800 rounded-lg group cursor-pointer hover:bg-gray-700 transition-all border border-gray-700/50">
                        <div class="flex items-center gap-2">
                             <div class="w-2 h-2 rounded-full bg-orange-500"></div>
                             <span class="text-xs text-gray-300 font-bold tracking-tight">Invoices</span>
                        </div>
                        <span class="text-xs text-orange-400 font-black">{{ $order->invoices->count() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
