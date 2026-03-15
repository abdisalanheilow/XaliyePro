@extends('admin.admin_master')

@section('title', 'View Purchase Bill - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('purchases.bills.index') }}"
                class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 transition-all active:scale-95 shadow-sm">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $bill->bill_no }}</h1>
                <p class="text-sm text-gray-500">Purchase Bill Details — {{ ucfirst($bill->status) }}</p>
            </div>
        </div>
        <div class="flex items-center gap-3">
            @php
                $statusClasses = [
                    'paid' => 'bg-green-50 text-green-700 ring-green-600/20',
                    'unpaid' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                    'partially_paid' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                    'overdue' => 'bg-red-50 text-red-700 ring-red-600/20',
                    'draft' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                ];
                $statusClass = $statusClasses[strtolower($bill->status)] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
            @endphp
            <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-bold ring-1 ring-inset {{ $statusClass }}">
                {{ ucfirst(str_replace('_', ' ', $bill->status)) }}
            </span>
            <div class="h-8 w-px bg-gray-200 mx-2"></div>
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                <i data-lucide="printer" class="w-4 h-4"></i>
                Print
            </button>
            <button class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50 flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                PDF
            </button>
            <a href="{{ route('purchases.bills.edit', $bill->id) }}" class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] flex items-center gap-2">
                <i data-lucide="edit-2" class="w-4 h-4"></i>
                Edit Bill
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Bill Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Vendor & Bill Info -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-8">
                    <div class="flex flex-col md:flex-row justify-between gap-8 mb-10">
                        <!-- Company Info (From Settings) -->
                        <div class="space-y-4">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 bg-[#28A375] rounded-xl flex items-center justify-center">
                                    <i data-lucide="package" class="w-6 h-6 text-white"></i>
                                </div>
                                <span class="text-xl font-bold text-gray-900">XaliyePro</span>
                            </div>
                            <div class="text-sm text-gray-500 leading-relaxed">
                                <p>123 Business Avenue, Suite 500</p>
                                <p>Nairobi, Kenya</p>
                                <p>Phone: +254 700 000000</p>
                            </div>
                        </div>

                        <!-- Bill Summary -->
                        <div class="text-left md:text-right space-y-2">
                            <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight uppercase">BILL</h2>
                            <p class="text-sm font-bold text-[#28A375]">{{ $bill->bill_no }}</p>
                            <div class="space-y-1 mt-4">
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Date Issued</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $bill->bill_date->format('M d, Y') }}</p>
                            </div>
                            <div class="space-y-1">
                                <p class="text-xs text-gray-400 uppercase font-bold tracking-widest">Due Date</p>
                                <p class="text-sm font-semibold text-gray-900">{{ $bill->due_date ? $bill->due_date->format('M d, Y') : '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 py-8 border-y border-gray-100 mb-8">
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Bill To (Vendor)</h3>
                            <div class="space-y-1">
                                <p class="text-base font-bold text-gray-900">{{ $bill->vendor->name }}</p>
                                <p class="text-sm text-gray-500">{{ $bill->vendor->address }}</p>
                                <p class="text-sm text-gray-500">{{ $bill->vendor->city }}, {{ $bill->vendor->country }}</p>
                                <p class="text-sm text-gray-500">TIN: {{ $bill->vendor->tax_id ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-4">Additional Info</h3>
                            <div class="space-y-3">
                                <div class="flex justify-between md:justify-start md:gap-8 border-b border-gray-50 pb-2">
                                    <span class="text-sm text-gray-500">Reference:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $bill->reference_no ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between md:justify-start md:gap-8 border-b border-gray-50 pb-2">
                                    <span class="text-sm text-gray-500">Terms:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ ucfirst(str_replace('_', ' ', $bill->payment_terms)) }}</span>
                                </div>
                                <div class="flex justify-between md:justify-start md:gap-8 border-b border-gray-50 pb-2">
                                    <span class="text-sm text-gray-500">Branch:</span>
                                    <span class="text-sm font-semibold text-gray-900">{{ $bill->branch->name ?? 'Main Branch' }}</span>
                                </div>
                                <div class="flex justify-between md:justify-start md:gap-8 border-b border-gray-50 pb-2">
                                    <span class="text-sm text-gray-500">Store/Warehouse:</span>
                                    <span class="text-sm font-semibold text-[#28A375]">{{ $bill->store->name ?? 'Main Store' }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Line Items Table -->
                    <div class="mb-8">
                        <table class="w-full text-left">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-widest">#</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-widest">Product / Service</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Qty</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Price</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Tax (%)</th>
                                    <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-widest text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($bill->items as $index => $item)
                                <tr>
                                    <td class="px-4 py-4 text-sm text-gray-500">{{ $index + 1 }}</td>
                                    <td class="px-4 py-4">
                                        <p class="text-sm font-bold text-gray-900">{{ $item->item->name }}</p>
                                        <p class="text-xs text-gray-500 mt-0.5">{{ $item->item->sku ?? '' }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-sm text-gray-900 text-right font-medium">{{ number_format($item->quantity, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 text-right font-medium">${{ number_format($item->unit_price, 2) }}</td>
                                    <td class="px-4 py-4 text-sm text-gray-900 text-right font-medium">
                                        @php
                                            $taxPercent = ($item->amount - ($item->quantity * $item->unit_price)) / ($item->quantity * $item->unit_price) * 100;
                                        @endphp
                                        {{ round($taxPercent) }}%
                                    </td>
                                    <td class="px-4 py-4 text-sm font-bold text-gray-900 text-right">${{ number_format($item->amount, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Totals -->
                    <div class="flex justify-end pt-6 border-t border-gray-100">
                        <div class="w-full md:w-80 space-y-3">
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-semibold text-gray-900">${{ number_format($bill->total_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Tax Total</span>
                                <span class="font-semibold text-gray-900">${{ number_format($bill->tax_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-sm text-gray-600">
                                <span>Discount</span>
                                <span class="font-semibold text-red-500">-${{ number_format($bill->discount_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-200">
                                <span class="text-lg font-bold text-gray-900">Grand Total</span>
                                <span class="text-xl font-extrabold text-[#28A375]">${{ number_format($bill->grand_total, 2) }}</span>
                            </div>
                            
                            @if ($bill->paid_amount > 0)
                            <div class="flex justify-between text-sm text-gray-600 pt-3">
                                <span>Amount Paid</span>
                                <span class="font-semibold text-green-600">${{ number_format($bill->paid_amount, 2) }}</span>
                            </div>
                            <div class="flex justify-between text-lg font-bold text-gray-900">
                                <span>Balance Due</span>
                                <span class="text-red-600">${{ number_format($bill->balance_amount, 2) }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if ($bill->notes)
                <div class="bg-gray-50 p-8 border-t border-gray-100">
                    <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Notes & Remarks</h4>
                    <p class="text-sm text-gray-600 leading-relaxed italic">{{ $bill->notes }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Sidebar Info -->
        <div class="space-y-6">
            <!-- Payment Summary -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-lucide="credit-card" class="w-5 h-5 text-[#28A375]"></i>
                    Payment Details
                </h3>
                <div class="space-y-6">
                    <div class="bg-gray-50 rounded-xl p-5 border border-gray-100">
                        <p class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-1">Status</p>
                        <p class="text-lg font-extrabold {{ $bill->status === 'paid' ? 'text-green-600' : ($bill->status === 'overdue' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ ucfirst($bill->status) }}
                        </p>
                    </div>
                    
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-green-50 rounded-lg flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Last Payment Date</p>
                                <p class="text-sm font-bold text-gray-900">N/A</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-50 rounded-lg flex items-center justify-center">
                                <i data-lucide="wallet" class="w-5 h-5 text-blue-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-500 font-medium">Payment Method</p>
                                <p class="text-sm font-bold text-gray-900">N/A</p>
                            </div>
                        </div>
                    </div>

                    <button class="w-full py-3 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
                        <i data-lucide="plus-circle" class="w-4 h-4"></i>
                        Record Payment
                    </button>
                </div>
            </div>

            <!-- Audit Trail -->
            <div class="bg-white rounded-xl border border-gray-200 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i data-lucide="activity" class="w-5 h-5 text-[#28A375]"></i>
                    History Log
                </h3>
                <div class="space-y-6 relative before:absolute before:left-[11px] before:top-2 before:bottom-2 before:w-0.5 before:bg-gray-100">
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1.5 w-6 h-6 bg-[#28A375] rounded-full border-4 border-white shadow-sm"></div>
                        <p class="text-sm font-bold text-gray-900">Bill Created</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $bill->created_at->format('M d, Y') }} at {{ $bill->created_at->format('h:i A') }}</p>
                        <p class="text-xs text-[#28A375] font-bold mt-1">by System Admin</p>
                    </div>
                    @if ($bill->status !== 'draft')
                    <div class="relative pl-8">
                        <div class="absolute left-0 top-1.5 w-6 h-6 bg-yellow-400 rounded-full border-4 border-white shadow-sm"></div>
                        <p class="text-sm font-bold text-gray-900">Status Changed to {{ ucfirst($bill->status) }}</p>
                        <p class="text-xs text-gray-500 font-medium">{{ $bill->updated_at->format('M d, Y') }} at {{ $bill->updated_at->format('h:i A') }}</p>
                    </div>
                    @endif
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
