@extends('admin.admin_master')

@section('title', 'View Sales Invoice - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.invoices.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-600 transition-all shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Invoice: <span class="text-[#28A375]">{{ $invoice->invoice_no }}</span></h1>
                <p class="text-sm text-gray-500 font-medium">Customer: {{ $invoice->customer->name }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <div class="flex items-center bg-white border border-gray-200 rounded-lg p-0.5 shadow-sm">
                 <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-md transition-all">
                    <i data-lucide="printer" class="w-4 h-4"></i>
                </button>
                <button type="button" class="p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-50 rounded-md transition-all border-l border-gray-100">
                    <i data-lucide="download" class="w-4 h-4"></i>
                </button>
            </div>
            
            <a href="{{ route('sales.invoices.edit', $invoice->id) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-blue-600 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all border-l-4 border-l-blue-500">
                <i data-lucide="edit" class="w-4 h-4"></i>
                Edit Invoice
            </a>

            @if ($invoice->balance_amount > 0)
            <a href="{{ route('sales.payments.create', ['invoice_id' => $invoice->id]) }}" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-lg shadow-green-100 active:scale-95">
                <i data-lucide="credit-card" class="w-4 h-4"></i>
                Record Payment
            </a>
            @endif
        </div>
    </div>

    <!-- Stats summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
         <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Total Amount</p>
                <h3 class="text-2xl font-black text-gray-900 tracking-tighter">${{ number_format($invoice->grand_total, 2) }}</h3>
            </div>
            <div class="w-10 h-10 bg-gray-50 rounded-lg flex items-center justify-center">
                 <i data-lucide="dollar-sign" class="w-5 h-5 text-gray-400"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between">
            <div>
                <p class="text-[11px] font-bold text-gray-400 uppercase tracking-widest mb-1">Paid Amount</p>
                <h3 class="text-2xl font-black text-[#28A375] tracking-tighter">${{ number_format($invoice->paid_amount, 2) }}</h3>
            </div>
            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                 <i data-lucide="check-circle" class="w-5 h-5 text-[#28A375]"></i>
            </div>
        </div>
        <div class="bg-white p-5 rounded-xl border border-[#28A375]/10 shadow-sm flex items-center justify-between border-b-4 border-b-red-500">
            <div>
                <p class="text-[11px] font-bold text-red-400 uppercase tracking-widest mb-1">Balance Due</p>
                <h3 class="text-2xl font-black text-red-600 tracking-tighter">${{ number_format($invoice->balance_amount, 2) }}</h3>
            </div>
            <div class="w-10 h-10 bg-red-50 rounded-lg flex items-center justify-center">
                 <i data-lucide="alert-circle" class="w-5 h-5 text-red-500"></i>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-sm">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Items Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-50 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 tracking-tight uppercase text-xs">Line Items</h3>
                    <div class="flex items-center gap-2">
                        @php
                            $statusClasses = [
                                'unpaid' => 'bg-red-50 text-red-600 border-red-100',
                                'partially_paid' => 'bg-orange-50 text-orange-600 border-orange-100',
                                'paid' => 'bg-green-50 text-green-600 border-green-100',
                            ];
                            $statusClass = $statusClasses[strtolower($invoice->status)] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                        @endphp
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-black uppercase border {{ $statusClass }} tracking-widest">{{ $invoice->status }}</span>
                    </div>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/30">
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase">Item Description</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-center">Qty</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">Price</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">Tax</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($invoice->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="font-bold text-gray-900 tracking-tight">{{ $item->item->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-medium italic">{{ $item->description ?? 'No specific description' }}</div>
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-gray-700">{{ $item->quantity }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-right font-medium text-gray-400">
                                    <div class="text-[11px]">${{ number_format($item->tax_amount, 2) }}</div>
                                    <div class="text-[9px] font-black opacity-60">({{ $item->tax_rate }}%)</div>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 tracking-tighter">${{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50/20">
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-right font-bold text-gray-400 text-[11px] uppercase tracking-wider">Subtotal</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 tracking-tight">${{ number_format($invoice->total_amount, 2) }}</td>
                            </tr>
                            @if ($invoice->tax_amount > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-2 text-right font-bold text-gray-400 text-[11px] uppercase tracking-wider">Total Tax</td>
                                <td class="px-6 py-2 text-right font-bold text-gray-900 tracking-tight">${{ number_format($invoice->tax_amount, 2) }}</td>
                            </tr>
                            @endif
                            @if ($invoice->discount_amount > 0)
                            <tr>
                                <td colspan="4" class="px-6 py-2 text-right font-bold text-red-400 text-[11px] uppercase tracking-wider">Discount</td>
                                <td class="px-6 py-2 text-right font-bold text-red-600 tracking-tight">-${{ number_format($invoice->discount_amount, 2) }}</td>
                            </tr>
                            @endif
                            <tr class="bg-gray-900">
                                <td colspan="4" class="px-6 py-5 text-right font-black text-gray-400 text-xs uppercase tracking-[0.2em]">Grand Total</td>
                                <td class="px-6 py-5 text-right font-black text-[#28A375] text-xl tracking-tighter">${{ number_format($invoice->grand_total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Payment History Card -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-50 bg-[#28A375]/5 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 tracking-tight uppercase text-xs flex items-center gap-2">
                        <i data-lucide="receipt" class="w-4 h-4 text-[#28A375]"></i>
                        Payment History
                    </h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/50">
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Receipt No</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Date</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest">Method</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-widest text-right">Amount Paid</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @if(count($invoice->payments) > 0)
                                @foreach ($invoice->payments as $payment)
                            <tr class="hover:bg-gray-50/50 transition-all">
                                <td class="px-6 py-4 font-bold text-[#28A375] tracking-tight">{{ $payment->payment_no }}</td>
                                <td class="px-6 py-4 text-gray-600 font-medium">{{ $payment->payment_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                     <span class="px-2 py-0.5 rounded bg-gray-100 text-gray-600 font-bold text-[10px] uppercase">{{ $payment->payment_method }}</span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-gray-900 tracking-tighter">${{ number_format($payment->amount, 2) }}</td>
                            </tr>
                                @endforeach
                            @else
                            <tr>
                                <td colspan="4" class="px-6 py-8 text-center text-gray-400 italic">No payments recorded for this invoice yet.</td>
                            </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Details Sidebar -->
             <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-5 space-y-4 shadow-gray-200/50">
                <div class="flex items-center justify-between pb-3 border-b border-gray-50">
                    <span class="text-gray-400 font-bold text-[11px] uppercase tracking-widest">Invoice Date</span>
                    <span class="font-black text-gray-900">{{ $invoice->invoice_date?->format('F d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-gray-50">
                    <span class="text-gray-400 font-bold text-[11px] uppercase tracking-widest text-red-400">Due Date</span>
                    <span class="font-black text-red-600">{{ $invoice->due_date?->format('F d, Y') ?? 'N/A' }}</span>
                </div>
                <div class="flex items-center justify-between pb-3 border-b border-gray-50 text-blue-500">
                    <span class="text-[11px] font-bold uppercase tracking-widest opacity-60">Source Order</span>
                    <a href="{{ route('sales.orders.show', $invoice->sales_order_id) }}" class="font-black flex items-center gap-1 hover:underline">
                        {{ $invoice->salesOrder->order_no ?? 'Standalone' }}
                        <i data-lucide="external-link" class="w-3 h-3"></i>
                    </a>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-gray-400 font-bold text-[11px] uppercase tracking-widest">Created By</span>
                    <span class="font-black text-gray-800">{{ $invoice->creator->name ?? 'System' }}</span>
                </div>
            </div>

            <!-- Customer Sidebar -->
            <div class="bg-gray-900 text-white rounded-xl border border-gray-800 shadow-xl p-5 space-y-5">
                <h4 class="text-[10px] font-black uppercase tracking-[0.3em] text-gray-500 mb-2">Billing Information</h4>
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 bg-[#28A375] rounded-full flex items-center justify-center font-black text-xl shadow-lg shadow-green-900/40 border-2 border-white/10">
                        {{ strtoupper(substr($invoice->customer->name, 0, 1)) }}
                    </div>
                    <div>
                        <div class="font-black text-lg tracking-tight leading-tight">{{ $invoice->customer->name }}</div>
                        <div class="text-[10px] text-[#28A375] font-black uppercase tracking-widest mt-0.5">CUSTOMER ID: {{ str_pad($invoice->customer->id, 5, '0', STR_PAD_LEFT) }}</div>
                    </div>
                </div>
                <div class="space-y-4 pt-4 border-t border-gray-800">
                    <div class="flex items-start gap-3 group">
                        <i data-lucide="mail" class="w-4 h-4 text-gray-600 group-hover:text-[#28A375] transition-colors mt-0.5"></i>
                        <span class="text-xs text-gray-400 font-medium">{{ $invoice->customer->email ?? 'no-email@registered.com' }}</span>
                    </div>
                    <div class="flex items-start gap-3 group">
                        <i data-lucide="phone" class="w-4 h-4 text-gray-600 group-hover:text-blue-500 transition-colors mt-0.5"></i>
                        <span class="text-xs text-gray-400 font-medium">{{ $invoice->customer->phone ?? '--- --- ---' }}</span>
                    </div>
                    <div class="flex items-start gap-3 group">
                        <i data-lucide="map-pin" class="w-4 h-4 text-gray-600 group-hover:text-red-500 transition-colors mt-0.5"></i>
                        <address class="text-xs text-gray-400 font-medium not-italic leading-relaxed">{{ $invoice->customer->address ?? 'No physical address found on file.' }}</address>
                    </div>
                </div>
                <div class="pt-4 mt-4 border-t border-gray-800">
                     <div class="p-3 bg-gray-800/50 rounded-lg flex items-center justify-between border border-gray-700/50">
                        <span class="text-[10px] font-black text-gray-500 uppercase">Status</span>
                        @if ($invoice->balance_amount <= 0)
                            <div class="px-2 py-1 bg-[#28A375]/10 text-[#28A375] rounded border border-[#28A375]/20 font-black text-[9px] uppercase tracking-widest flex items-center gap-1">
                                <i data-lucide="shield-check" class="w-3 h-3"></i> Fully Paid
                            </div>
                        @else
                             <div class="px-2 py-1 bg-red-500/10 text-red-500 rounded border border-red-500/20 font-black text-[9px] uppercase tracking-widest">Outstanding</div>
                        @endif
                     </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
