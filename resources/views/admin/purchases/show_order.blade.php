@extends('admin.admin_master')

@section('title', 'Purchase Order ' . $order->order_no . ' - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Breadcrumbs & Actions -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2">
                    <a href="{{ route('purchases.orders.index') }}" class="hover:text-[#28A375] transition-colors">Purchases</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ route('purchases.orders.index') }}" class="hover:text-[#28A375] transition-colors">Orders</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-gray-900">#{{ $order->order_no }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $order->order_no }}</h1>
                    @php
                        $statusClasses = [
                            'pending' => 'bg-yellow-50 text-yellow-600 border-yellow-100',
                            'received' => 'bg-green-50 text-green-600 border-green-100',
                            'draft' => 'bg-gray-50 text-gray-600 border-gray-100',
                            'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                        ];
                        $statusClass = $statusClasses[strtolower($order->status)] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                    @endphp
                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-full border {{ $statusClass }}">
                        {{ $order->status }}
                    </span>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Print
                </button>
                <a href="{{ route('purchases.orders.edit', $order->id) }}" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                    Edit
                </a>
                @if ($order->status !== 'received' && $order->status !== 'cancelled')
                <a href="{{ route('purchases.receipts.create_from_order', $order->id) }}" class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm">
                    <i data-lucide="truck" class="w-4 h-4"></i>
                    Receive Goods
                </a>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main Content: Invoice Layout -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden min-h-[800px] flex flex-col print:shadow-none print:border-none">
                    <!-- Invoice Ribbon -->
                    <div class="h-2 bg-[#28A375]"></div>
                    
                    <div class="p-8 md:p-12 space-y-12 flex-1">
                        <!-- Top Header: CompanyInfo & OrderMeta -->
                        <div class="flex flex-col md:flex-row justify-between gap-8">
                            <div>
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-8 h-8 bg-[#28A375] rounded-lg text-white flex items-center justify-center font-black text-xs uppercase italic">ERP</div>
                                    <span class="font-black text-xl tracking-tighter text-gray-900">DREAMS<span class="text-[#28A375]">ERP</span></span>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm font-bold text-gray-900">XaliyePro Global Industries</p>
                                    <p class="text-xs text-gray-500">123 Business Avenue, Suite 100</p>
                                    <p class="text-xs text-gray-500">Nairobi, Kenya</p>
                                    <p class="text-xs text-gray-500">+254 700 000 000</p>
                                </div>
                            </div>
                            <div class="text-left md:text-right space-y-4">
                                <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter leading-none">PURCHASE ORDER</h2>
                                <div class="space-y-1">
                                    <div class="flex justify-between md:justify-end md:gap-8">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Order No:</span>
                                        <span class="text-xs font-bold text-gray-900">#{{ $order->order_no }}</span>
                                    </div>
                                    <div class="flex justify-between md:justify-end md:gap-8">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Order Date:</span>
                                        <span class="text-xs font-bold text-gray-900">{{ $order->order_date->format('d M, Y') }}</span>
                                    </div>
                                    @if ($order->expected_date)
                                    <div class="flex justify-between md:justify-end md:gap-8">
                                        <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Expected Date:</span>
                                        <span class="text-xs font-bold text-gray-900">{{ $order->expected_date->format('d M, Y') }}</span>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Address Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 py-8 border-y border-gray-50">
                            <div>
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Vendor Information</h3>
                                <div class="space-y-2">
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $order->vendor->name }}</p>
                                    @if ($order->vendor->address)
                                        <p class="text-xs text-gray-500 leading-relaxed">{{ $order->vendor->address }}</p>
                                    @else
                                        <p class="text-xs italic text-gray-400">No address provided</p>
                                    @endif
                                    <div class="flex flex-col gap-1 pt-2">
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <i data-lucide="phone" class="w-3 h-3"></i>
                                            <span>{{ $order->vendor->phone ?? 'N/A' }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs text-gray-500">
                                            <i data-lucide="mail" class="w-3 h-3"></i>
                                            <span>{{ $order->vendor->email ?? 'N/A' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Shipping / Branch Address</h3>
                                <div class="space-y-2">
                                    <p class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $order->branch->name ?? 'Main Headquarters' }}</p>
                                    <p class="text-xs text-gray-500 leading-relaxed">
                                        {{ $order->branch->address ?? 'XaliyePro Industrial Park, Building A1' }}<br>
                                        {{ $order->store->name ?? 'Primary Logistics Center' }}
                                    </p>
                                    <div class="flex items-center gap-2 text-xs text-gray-500 pt-2">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i>
                                        <span>Official Company Location</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Items Table -->
                        <div class="overflow-x-auto -mx-8 md:-mx-12 lg:mx-0">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-900 text-white">
                                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest">Item Description</th>
                                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-center">Qty</th>
                                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-right">Unit Price</th>
                                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-right">Tax</th>
                                        <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    @foreach ($order->items as $item)
                                    <tr class="group">
                                        <td class="px-8 py-5">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-bold text-gray-900 uppercase tracking-tight">{{ $item->item->name }}</span>
                                                <span class="text-[10px] text-gray-400 font-medium">SKU: {{ $item->item->sku ?? 'N/A' }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 text-center">
                                            <span class="text-sm font-bold text-gray-600">{{ number_format($item->quantity, 2) }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-sm font-bold text-gray-600">${{ number_format($item->unit_price, 2) }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-sm font-bold text-gray-400">${{ number_format($item->tax_amount, 2) }}</span>
                                        </td>
                                        <td class="px-8 py-5 text-right">
                                            <span class="text-sm font-black text-gray-900">${{ number_format($item->amount, 2) }}</span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Footer Totals -->
                        <div class="flex flex-col md:flex-row justify-between gap-12 pt-8">
                            <div class="flex-1 max-w-sm space-y-4">
                                <div>
                                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5">Note/Instructions</h3>
                                    <p class="text-xs text-gray-500 leading-relaxed italic">{{ $order->notes ?? 'No special instructions provided for this order.' }}</p>
                                </div>
                                @if ($order->status === 'cancelled')
                                <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                                    <p class="text-xs text-red-600 font-bold">This order has been cancelled.</p>
                                </div>
                                @endif
                            </div>
                            <div class="w-full md:w-80 space-y-4">
                                <div class="bg-gray-50/50 p-6 rounded-2xl space-y-3">
                                    <div class="flex justify-between items-center text-gray-500 text-xs font-bold uppercase tracking-wider">
                                        <span>Subtotal</span>
                                        <span class="text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-gray-500 text-xs font-bold uppercase tracking-wider">
                                        <span>Tax Total</span>
                                        <span class="text-gray-900">${{ number_format($order->tax_amount, 2) }}</span>
                                    </div>
                                    @if ($order->discount_amount > 0)
                                    <div class="flex justify-between items-center text-red-500 text-xs font-bold uppercase tracking-wider">
                                        <span>Discount</span>
                                        <span>-${{ number_format($order->discount_amount, 2) }}</span>
                                    </div>
                                    @endif
                                    <div class="h-px bg-gray-200 my-2"></div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-sm font-black text-gray-900 uppercase tracking-tighter">Grand Total</span>
                                        <span class="text-2xl font-black text-[#28A375] tracking-tighter">${{ number_format($order->grand_total, 2) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Footer -->
                    <div class="bg-gray-50 px-12 py-8 flex justify-between items-center">
                        <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest italic">Generated by XaliyePro Pro Systems</p>
                        <div class="flex gap-4">
                             <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400"><i data-lucide="globe" class="w-4 h-4"></i></div>
                             <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-400"><i data-lucide="instagram" class="w-4 h-4"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar Info -->
            <div class="space-y-6">
                <!-- Order Status Card -->
                <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Additional Info</h3>
                    <div class="space-y-4">
                        <div class="flex justify-between md:justify-start md:gap-8 border-b border-gray-50 pb-2">
                            <span class="text-sm text-gray-500">Terms:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $order->payment_terms)) }}</span>
                        </div>
                        <div class="flex justify-between md:justify-start md:gap-8 border-b border-gray-50 pb-2">
                            <span class="text-sm text-gray-500">Branch:</span>
                            <span class="text-sm font-semibold text-gray-900">{{ $order->branch->name ?? 'Main Branch' }}</span>
                        </div>
                        <div class="flex justify-between md:justify-start md:gap-8 border-b border-gray-50 pb-2">
                            <span class="text-sm text-gray-500">Store:</span>
                            <span class="text-sm font-semibold text-[#28A375]">{{ $order->store->name ?? 'Main Store' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Audit Trail -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-100">
                        <h3 class="text-sm font-bold text-gray-900">Activity Log</h3>
                    </div>
                    <div class="p-6 space-y-6">
                        <div class="flex gap-4 relative">
                            <div class="absolute left-[11px] top-6 bottom-[-24px] w-px bg-gray-100"></div>
                            <div class="w-6 h-6 rounded-full bg-green-50 flex items-center justify-center z-10">
                                <i data-lucide="plus" class="w-3 h-3 text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900 uppercase tracking-tight">Order Created</p>
                                <p class="text-[11px] text-gray-500 mt-0.5">Created by {{ $order->user->name ?? 'System' }}</p>
                                <p class="text-[10px] text-gray-400 mt-1 uppercase font-black tracking-widest">{{ $order->created_at->format('d M Y, h:i A') }}</p>
                            </div>
                        </div>
                        <!-- You can add more logs dynamically here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
</script>
@endpush
