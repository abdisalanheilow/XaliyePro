@extends('admin.admin_master')

@section('title', 'Goods Receipt ' . $receipt->receipt_no . ' - XaliyePro')

@section('admin')
    <div class="space-y-6 flex flex-col items-center">
        <!-- Breadcrumbs & Actions -->
        <div class="w-full max-w-5xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <nav class="flex items-center gap-2 text-xs font-medium text-gray-500 mb-2">
                    <a href="{{ route('purchases.receipts.index') }}" class="hover:text-[#28A375] transition-colors">Purchases</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <a href="{{ route('purchases.receipts.index') }}" class="hover:text-[#28A375] transition-colors">Goods Receipts</a>
                    <i data-lucide="chevron-right" class="w-3 h-3"></i>
                    <span class="text-gray-900">#{{ $receipt->receipt_no }}</span>
                </nav>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $receipt->receipt_no }}</h1>
                    <span class="px-3 py-1 text-xs font-bold uppercase rounded-full border bg-green-50 text-green-600 border-green-100">
                        {{ $receipt->status }}
                    </span>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-200 text-gray-700 rounded-lg text-sm font-semibold hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                    Print GRN
                </button>
                @if (!$receipt->bill)
                <a href="{{ route('purchases.bills.create', ['receipt_id' => $receipt->id]) }}" class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    Create Vendor Bill
                </a>
                @endif
            </div>
        </div>

        <!-- Document Viewer Box -->
        <div class="w-full max-w-5xl bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col print:shadow-none print:border-none min-h-[800px]">
            <!-- Top Ribbon -->
            <div class="h-2 bg-[#28A375]"></div>
            
            <div class="p-8 md:p-12 space-y-12 flex-1 relative">
                <!-- Watermark -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none opacity-[0.03]">
                    <i data-lucide="package-check" class="w-[400px] h-[400px]"></i>
                </div>
                
                <!-- Document Header -->
                <div class="flex flex-col md:flex-row justify-between gap-8 relative z-10">
                    <div>
                        <div class="flex items-center gap-2 mb-4">
                            <div class="w-8 h-8 bg-[#28A375] rounded-lg text-white flex items-center justify-center font-black text-xs uppercase italic">ERP</div>
                            <span class="font-black text-xl tracking-tighter text-gray-900">DREAMS<span class="text-[#28A375]">ERP</span></span>
                        </div>
                        <div class="space-y-1">
                            <p class="text-sm font-bold text-gray-900">XaliyePro Global</p>
                            <p class="text-xs text-gray-500">Logistics & Warehousing Dept.</p>
                            <p class="text-xs text-gray-500">Nairobi, Kenya</p>
                        </div>
                    </div>
                    <div class="text-left md:text-right space-y-4">
                        <h2 class="text-3xl font-black text-gray-900 uppercase tracking-tighter leading-none">GOODS RECEIPT NOTE</h2>
                        <div class="space-y-1">
                            <div class="flex justify-between md:justify-end md:gap-8">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">GRN No:</span>
                                <span class="text-xs font-bold text-gray-900">#{{ $receipt->receipt_no }}</span>
                            </div>
                            <div class="flex justify-between md:justify-end md:gap-8">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Receipt Date:</span>
                                <span class="text-xs font-bold text-gray-900">{{ \Carbon\Carbon::parse($receipt->received_date)->format('d M, Y') }}</span>
                            </div>
                            <div class="flex justify-between md:justify-end md:gap-8">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Linked PO:</span>
                                @if ($receipt->order)
                                    <span class="text-xs font-bold text-[#28A375]">{{ $receipt->order->order_no }}</span>
                                @else
                                    <span class="text-xs font-bold text-gray-400 italic">None</span>
                                @endif
                            </div>
                            <div class="flex justify-between md:justify-end md:gap-8">
                                <span class="text-xs font-bold text-gray-400 uppercase tracking-widest">Delivery Challan:</span>
                                <span class="text-xs font-bold text-gray-900">{{ $receipt->delivery_challan_no ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-12 py-8 border-y border-gray-50 relative z-10">
                    <div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Vendor Details</h3>
                        <div class="space-y-2">
                            <p class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $receipt->vendor->name }}</p>
                            @if ($receipt->vendor->address)
                                <p class="text-xs text-gray-500 leading-relaxed">{{ $receipt->vendor->address }}</p>
                            @endif
                            <div class="flex items-center gap-2 text-xs text-gray-500 pt-2">
                                <i data-lucide="phone" class="w-3 h-3"></i>
                                <span>{{ $receipt->vendor->phone ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Storage Destination</h3>
                        <div class="space-y-2">
                            <p class="text-sm font-black text-gray-900 uppercase tracking-tight">{{ $receipt->branch->name ?? 'Main Branch' }}</p>
                            <p class="text-sm font-black text-gray-600 uppercase tracking-tight">{{ $receipt->store->name ?? 'Primary Store' }}</p>
                            <div class="flex items-center gap-2 text-xs text-gray-500 pt-2">
                                <i data-lucide="user" class="w-3 h-3"></i>
                                <span>Received By: {{ $receipt->receiver->name ?? 'System' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Items Details -->
                <div class="overflow-x-auto -mx-8 md:-mx-12 lg:mx-0 relative z-10">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-900 text-white">
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest w-16">#</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest">Item Received</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-center">Ordered</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-center text-[#DCFCE7]">Accepted</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-center text-red-300">Rejected</th>
                                <th class="px-8 py-4 text-[10px] font-black uppercase tracking-widest text-right">QC Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white/80 backdrop-blur-sm">
                            @foreach ($receipt->items as $index => $item)
                            <tr class="group hover:bg-gray-50/50 transition-colors">
                                <td class="px-8 py-5 text-sm font-bold text-gray-400">{{ $index + 1 }}</td>
                                <td class="px-8 py-5">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-bold text-gray-900 uppercase tracking-tight">{{ $item->item->name }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium">SKU: {{ $item->item->sku ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-sm font-bold text-gray-500">{{ $item->ordered_qty > 0 ? number_format($item->ordered_qty, 2) : '-' }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-sm font-black text-gray-900 bg-green-50 px-2 py-1 rounded">{{ number_format($item->received_qty, 2) }}</span>
                                </td>
                                <td class="px-8 py-5 text-center">
                                    <span class="text-sm font-bold {{ $item->rejected_qty > 0 ? 'text-red-500' : 'text-gray-400' }}">{{ number_format($item->rejected_qty, 2) }}</span>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    @php
                                        $qcColors = [
                                            'passed' => 'text-green-600',
                                            'failed' => 'text-red-600',
                                            'partially_failed' => 'text-orange-600',
                                        ];
                                        $qcColor = $qcColors[$item->quality_status] ?? 'text-gray-500';
                                    @endphp
                                    <span class="text-xs font-black uppercase tracking-wider {{ $qcColor }}">{{ str_replace('_', ' ', $item->quality_status) }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-8">
                    <h3 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-1.5">Note/Instructions</h3>
                     <p class="text-xs text-gray-500 leading-relaxed italic">{{ $receipt->notes ?? 'No additional notes provided for this receipt.' }}</p>
                </div>
            </div>

            <!-- Authentic Footer -->
            <div class="bg-gray-50/50 p-8 md:p-12 border-t border-gray-100 mt-auto">
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-8">
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Carrier/Driver</p>
                        <div class="h-10 border-b-2 border-dashed border-gray-300 max-w-[150px]"></div>
                        <p class="text-[10px] font-medium text-gray-400 mt-2">Signature & Date</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Receiver (Warehouse)</p>
                        <div class="h-10 border-b-2 border-dashed border-gray-300 max-w-[150px] relative">
                            <span class="absolute bottom-1 left-0 font-cursive text-lg text-gray-900 -rotate-3">{{ $receipt->receiver->name ?? '' }}</span>
                        </div>
                        <p class="text-[10px] font-medium text-gray-400 mt-2">Signature & Date</p>
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
