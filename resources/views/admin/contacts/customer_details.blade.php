@extends('admin.admin_master')

@section('title', 'View Customer - XaliyePro')

@section('admin')
    <div x-data="{
                    showEditModal: false,
                    isSaving: false,
                    currentCustomer: {
                        id: '{{ $customer->id }}',
                        customer_id: '{{ $customer->customer_id }}',
                        name: '{{ $customer->name }}',
                        email: '{{ $customer->email }}',
                        phone: '{{ $customer->phone }}',
                        type: '{{ $customer->type }}',
                        status: '{{ $customer->status }}',
                        address: '{{ $customer->address }}'
                    }
                }">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('contacts.customers.index') }}"
                    class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $customer->name }}</h1>
                    <p class="text-gray-500 mt-0.5 text-sm">Customer Profile — <span
                            class="font-medium text-gray-700">{{ $customer->customer_id }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="px-3 py-1.5 {{ $customer->status == 'active' ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-red-100 text-red-700' }} text-xs font-bold rounded-lg uppercase tracking-wider">
                    {{ str_replace('_', ' ', $customer->status) }}
                </span>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all">
                    <i data-lucide="file-text" class="w-4 h-4"></i>
                    New Invoice
                </button>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-gray-800 transition-all">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    Record Payment
                </button>
            </div>
        </div>

        <!-- Customer Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Column: Personnel & Invoices -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                            <i data-lucide="user" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                            <p class="text-xs text-gray-500">Core details about this customer</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer ID</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $customer->customer_id }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Full Name / Company</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $customer->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email Address</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $customer->email ?: 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone Number</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $customer->phone ?: 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Address</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    @if ($customer->address || $customer->city || $customer->country)
                                        {{ $customer->address }}
                                        @if ($customer->address && ($customer->city || $customer->country))<br>@endif
                                        {{ implode(', ', array_filter([$customer->city, $customer->country])) }}
                                    @else
                                        No address provided
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Invoices Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                                <i data-lucide="file-text" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Recent Invoices</h2>
                                <p class="text-xs text-gray-500">Latest billing history</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs font-bold text-[#28A375] hover:underline flex items-center gap-1">
                            View All <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </a>
                    </div>
                    <div class="p-0">
                        @if($customer->invoices->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Invoice No</th>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Total</th>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($customer->invoices as $invoice)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ $invoice->invoice_no }}</td>
                                                <td class="px-5 py-3 text-sm text-gray-600">{{ $invoice->invoice_date->format('M d, Y') }}</td>
                                                <td class="px-5 py-3 text-sm font-bold text-gray-900">${{ number_format($invoice->grand_total, 2) }}</td>
                                                <td class="px-5 py-3">
                                                    @php
                                                        $statusClasses = match($invoice->status) {
                                                            'paid' => 'bg-green-100 text-green-700',
                                                            'partially_paid' => 'bg-blue-100 text-blue-700',
                                                            'overdue' => 'bg-red-100 text-red-700',
                                                            default => 'bg-gray-100 text-gray-700'
                                                        };
                                                    @endphp
                                                    <span class="px-2 py-1 {{ $statusClasses }} text-[10px] font-bold rounded-md uppercase">
                                                        {{ str_replace('_', ' ', $invoice->status) }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8">
                                <div class="flex flex-col items-center justify-center py-8 text-center bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 border border-gray-100">
                                        <i data-lucide="file-x" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900">No Invoices Found</h4>
                                    <p class="text-xs text-gray-500 mt-1 max-w-[240px]">This customer hasn't been billed yet.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Payment History Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-orange-500 rounded-lg flex items-center justify-center shadow-sm shadow-orange-100">
                                <i data-lucide="credit-card" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Payment History</h2>
                                <p class="text-xs text-gray-500">Latest payment records</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs font-bold text-[#28A375] hover:underline flex items-center gap-1">
                            View All <i data-lucide="chevron-right" class="w-3 h-3"></i>
                        </a>
                    </div>
                    <div class="p-0">
                        @if($customer->payments->count() > 0)
                            <div class="overflow-x-auto">
                                <table class="w-full text-left">
                                    <thead class="bg-gray-50 border-b border-gray-100">
                                        <tr>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Payment No</th>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Amount</th>
                                            <th class="px-5 py-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Method</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100">
                                        @foreach($customer->payments as $payment)
                                            <tr class="hover:bg-gray-50 transition-colors">
                                                <td class="px-5 py-3 text-sm font-bold text-gray-900">{{ $payment->payment_no }}</td>
                                                <td class="px-5 py-3 text-sm text-gray-600">{{ $payment->payment_date->format('M d, Y') }}</td>
                                                <td class="px-5 py-3 text-sm font-bold text-emerald-600">${{ number_format($payment->amount, 2) }}</td>
                                                <td class="px-5 py-3 text-sm font-medium text-gray-700 capitalize">
                                                    {{ str_replace('_', ' ', $payment->payment_method) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="p-8">
                                <div class="flex flex-col items-center justify-center py-8 text-center bg-gray-50/50 rounded-xl border-2 border-dashed border-gray-200">
                                    <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3 border border-gray-100">
                                        <i data-lucide="banknote" class="w-6 h-6 text-gray-300"></i>
                                    </div>
                                    <h4 class="text-sm font-bold text-gray-900">No Payments Yet</h4>
                                    <p class="text-xs text-gray-500 mt-1 max-w-[240px]">No payment records found for this customer.</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Account & Profile Details -->
            <div class="space-y-6">
                <!-- Account Profile -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="briefcase" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Account Summary</h2>
                            <p class="text-xs text-gray-500">Financial profile</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer Type</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 tracking-wide capitalize">{{ $customer->type }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Payment Terms</label>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $customer->payment_terms_label }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Credit Limit</label>
                            <p class="mt-1 text-sm font-bold text-gray-900">${{ number_format($customer->credit_limit ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Preferred Contact</label>
                            <div class="flex items-center gap-2 mt-1">
                                <i data-lucide="{{ $customer->preferred_contact === 'phone' || $customer->preferred_contact === 'sms' ? 'phone' : 'mail' }}" class="w-4 h-4 text-blue-500"></i>
                                <span class="text-sm font-medium text-gray-900 capitalize">{{ $customer->preferred_contact }}</span>
                            </div>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Customer Since</label>
                            <div class="flex items-center gap-2 mt-1">
                                <i data-lucide="calendar" class="w-4 h-4 text-[#28A375]"></i>
                                <span class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($customer->created_at)->format('M d, Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="sticky-note" class="w-5 h-5 text-amber-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Notes</h2>
                                <p class="text-xs text-gray-500">Internal customer notes</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            @if ($customer->notes)
                                <p class="text-sm font-medium text-gray-700 leading-relaxed">{{ $customer->notes }}</p>
                            @else
                                <p class="text-sm text-gray-500 italic">No internal notes available for this customer.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4 hidden md:flex">
                <div class="w-14 h-14 bg-[#28A375]/10 rounded-xl flex items-center justify-center flex-shrink-0">
                    <span class="text-2xl font-bold text-[#28A375]">{{ $customer->initials }}</span>
                </div>
                <div>
                     <p class="text-lg font-bold text-gray-900">{{ $customer->name }}</p>
                     <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $customer->customer_id }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($customer->total_sales, 2) }}</p>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Total Sales</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 {{ $customer->balance > 0 ? 'bg-orange-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 {{ $customer->balance > 0 ? 'text-orange-500' : 'text-gray-500' }}"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold {{ $customer->balance > 0 ? 'text-orange-600' : 'text-gray-900' }}">${{ number_format($customer->balance, 2) }}</p>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Outstanding Balance</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-red-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-red-600">$0.00</p>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Overdue Amount</p>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="flex items-center gap-6 text-xs text-gray-400">
            <div class="flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>Created: {{ $customer->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <span>Last Updated: {{ $customer->updated_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>
    </div>
@endsection
