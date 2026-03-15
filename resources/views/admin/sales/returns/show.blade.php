@extends('admin.admin_master')

@section('title', 'View Sales Return - XaliyePro')

@section('admin')
<div class="space-y-6 text-sm">
    <!-- Header Section -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('sales.returns.index') }}" class="p-2 bg-white border border-gray-200 rounded-lg text-gray-400 hover:text-gray-600 transition-all shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Sales Return: <span class="text-red-500">{{ $return->return_no }}</span></h1>
                <p class="text-xs text-gray-500 font-black uppercase tracking-widest mt-0.5">Linked to: <span class="text-blue-500">{{ $return->invoice->invoice_no ?? 'Standalone' }}</span></p>
            </div>
        </div>
        <div class="flex items-center gap-3">
             <button type="button" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all border-b-2 border-b-gray-400">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Print Voucher
            </button>
            <a href="{{ route('sales.returns.edit', $return->id) }}" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-blue-600 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all border-b-2 border-b-blue-500">
                <i data-lucide="edit" class="w-4 h-4"></i>
                Modify
            </a>
            <div class="h-10 w-[1px] bg-gray-200 mx-1"></div>
            <div class="flex flex-col items-end">
                <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">Grand Credit</div>
                <div class="text-xl font-black text-red-600 tracking-tighter">${{ number_format($return->grand_total, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Return Reason Alert -->
            <div class="p-4 bg-orange-50 border-l-4 border-orange-400 rounded-lg flex items-start gap-3">
                <div class="p-1.5 bg-orange-100 rounded text-orange-600">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <h4 class="font-bold text-orange-800 tracking-tight">Return Reason: {{ $return->reason ?? 'Not Specified' }}</h4>
                    <p class="text-[13px] text-orange-700 mt-1 italic">{{ $return->notes ?? 'No additional clinical notes provided for this return.' }}</p>
                </div>
            </div>

            <!-- Items Table Card -->
             <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden border-t-4 border-t-red-500">
                <div class="p-5 bg-gray-50/50 flex items-center justify-between">
                    <h3 class="font-bold text-gray-900 tracking-tight uppercase text-[11px] tracking-[0.2em] text-gray-500">Returned Item Details</h3>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-50/30 border-b border-gray-100">
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase">Item Description</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-center">Returned Qty</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">Unit Price</th>
                                <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase text-right">Credit Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($return->items as $item)
                            <tr>
                                <td class="px-6 py-5">
                                    <div class="font-bold text-gray-900 tracking-tight text-[15px]">{{ $item->item->name }}</div>
                                    <div class="text-[10px] text-gray-400 font-black uppercase tracking-widest">{{ $item->item->sku }}</div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 bg-red-50 text-red-600 rounded-full font-black text-xs border border-red-100">
                                        {{ $item->quantity }} {{ $item->item->unit->name ?? 'units' }}
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-right font-bold text-gray-600 tabular-nums">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-5 text-right font-black text-gray-900 tracking-tighter text-[16px] tabular-nums">${{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-900 text-white">
                            <tr>
                                <td colspan="3" class="px-6 py-5 text-right text-[11px] font-black uppercase tracking-[0.3em] text-gray-500">Net Credit Total</td>
                                <td class="px-6 py-5 text-right font-black text-red-400 text-xl tracking-tighter tabular-nums">${{ number_format($return->grand_total, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Inventory Log Info (Decorative/Functional) -->
            <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm flex items-center justify-between border-l-4 border-l-[#28A375]">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                        <i data-lucide="package-check" class="w-6 h-6 text-[#28A375]"></i>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-900 tracking-tight">Stock Restoration Status</h4>
                        <p class="text-[11px] font-medium text-gray-500">Items have been checked back into: <span class="font-bold text-gray-700">Main Warehouse</span></p>
                    </div>
                </div>
                <div class="px-3 py-1 bg-[#28A375]/10 text-[#28A375] rounded border border-[#28A375]/10 font-black text-[9px] uppercase tracking-widest">AUTO: RESTORED</div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Customer Card -->
             <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-gray-100 flex items-center justify-between">
                    <h3 class="font-black text-[10px] text-gray-400 uppercase tracking-[0.2em]">Customer Entity</h3>
                </div>
                <div class="p-5">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-gray-900 text-white rounded-lg flex items-center justify-center font-black text-lg shadow-lg">
                            {{ strtoupper(substr($return->customer->name, 0, 1)) }}
                        </div>
                        <div>
                             <div class="font-black text-gray-900 text-[16px] tracking-tight leading-tight">{{ $return->customer->name }}</div>
                             <span class="text-[11px] text-blue-500 font-bold tracking-widest uppercase">Member Account</span>
                        </div>
                    </div>
                    <div class="space-y-4 pt-4 border-t border-gray-50">
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Email Channel</span>
                            <span class="text-gray-900 font-bold tracking-tight">{{ $return->customer->email ?? 'no-email-address' }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-1">Phone Line</span>
                            <span class="text-gray-900 font-bold tracking-tight">{{ $return->customer->phone ?? 'unlisted number' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Processing Meta -->
            <div class="bg-gray-50 rounded-xl border-2 border-dashed border-gray-200 p-5 space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Log Date</span>
                    <span class="font-bold text-gray-900">{{ $return->return_date->format('M d, Y') }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Ledger Branch</span>
                    <span class="font-bold text-gray-900">{{ $return->branch->name ?? 'Head Office' }}</span>
                </div>
                 <div class="flex items-center justify-between">
                    <span class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Handler</span>
                    <span class="font-bold text-gray-900 text-[#28A375]">{{ $return->createdBy->name ?? 'Admin User' }}</span>
                </div>
            </div>

            <!-- Financial Impact Summary -->
            <div class="p-5 bg-red-600 rounded-xl shadow-lg shadow-red-200 text-white relative overflow-hidden">
                <div class="relative z-10">
                    <h4 class="text-[10px] font-black uppercase tracking-widest opacity-60 mb-2">Liability Impact</h4>
                    <div class="text-xs font-medium opacity-90 leading-relaxed">
                        This return will decrease the customer's total receivable balance by <span class="font-black underlineDecoration-red-400 italic">${{ number_format($return->grand_total, 2) }}</span>.
                    </div>
                </div>
                <i data-lucide="info" class="absolute -right-4 -bottom-4 w-24 h-24 text-white opacity-5 rotate-12"></i>
            </div>
        </div>
    </div>
</div>
@endsection
