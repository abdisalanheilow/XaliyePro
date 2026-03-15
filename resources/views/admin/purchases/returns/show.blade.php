@extends('admin.admin_master')

@section('title', 'Purchase Return Details - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('purchases.returns.index') }}"
                class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 transition-all active:scale-95 shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $return->return_no }}</h1>
                <p class="text-sm text-gray-500">Purchase Return Details</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold bg-green-50 text-green-700 ring-1 ring-inset ring-green-600/20">
                Processed
            </span>
            <div class="h-8 w-px bg-gray-200 mx-2"></div>
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Print
            </button>
            <a href="{{ route('purchases.returns.edit', $return->id) }}" class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] flex items-center gap-2">
                <i data-lucide="edit-2" class="w-4 h-4"></i>
                Edit Return
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Items Table -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Returned Items</h2>
                    <span class="text-sm text-gray-500">{{ count($return->items) }} items returned</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Product</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Rate</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Tax</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($return->items as $item)
                            <tr>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900">{{ $item->item->name }}</span>
                                        <span class="text-xs text-gray-500">SKU: {{ $item->item->sku ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($item->quantity, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($item->unit_price, 2) }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">${{ number_format($item->tax_amount, 2) }}</td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">${{ number_format($item->amount, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Totals -->
                <div class="p-6 bg-gray-50 border-t border-gray-100">
                    <div class="flex justify-end">
                        <div class="w-full md:w-64 space-y-3">
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Subtotal</span>
                                <span class="text-sm font-semibold text-gray-900">${{ number_format($return->total_amount, 2) }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm text-gray-600">Tax</span>
                                <span class="text-sm font-semibold text-gray-900">${{ number_format($return->tax_amount, 2) }}</span>
                            </div>
                            <div class="pt-3 border-t border-gray-200 flex items-center justify-between">
                                <span class="text-base font-bold text-gray-900">Grand Total</span>
                                <span class="text-xl font-black text-[#28A375]">${{ number_format($return->grand_total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            @if ($return->notes)
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i data-lucide="file-text" class="w-4 h-4 text-[#28A375]"></i>
                    Notes / Reason for Return
                </h2>
                <p class="text-sm text-gray-600 leading-relaxed italic">{{ $return->notes }}</p>
            </div>
            @endif
        </div>

        <!-- Sidebar Details -->
        <div class="space-y-6">
            <!-- Vendor Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-4">Vendor Details</h2>
                <div class="flex items-center gap-4 mb-6 p-4 bg-gray-50 rounded-xl">
                    <div class="w-12 h-12 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-xl flex items-center justify-center text-white text-lg font-bold">
                        {{ substr($return->vendor->name ?? 'V', 0, 1) }}
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-900">{{ $return->vendor->name ?? 'Unknown Vendor' }}</h3>
                        <p class="text-xs text-gray-500">{{ $return->vendor->email ?? 'no-email@vendor.com' }}</p>
                    </div>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Phone</span>
                        <span class="font-medium text-gray-900">{{ $return->vendor->phone ?? '-' }}</span>
                    </div>
                    @if ($return->bill)
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-500">Original Bill</span>
                        <a href="{{ route('purchases.bills.show', $return->purchase_bill_id) }}" class="font-medium text-[#28A375] hover:underline">{{ $return->bill->bill_no }}</a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Logistics Info -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <h2 class="text-base font-bold text-gray-900 mb-4">Logistics</h2>
                <div class="space-y-4">
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Branch</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $return->branch->name ?? 'Main Branch' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Store / Warehouse</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $return->store->name ?? 'Default Store' }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 uppercase font-bold mb-1">Created By</p>
                        <p class="text-sm font-semibold text-gray-900">{{ $return->user->name ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
