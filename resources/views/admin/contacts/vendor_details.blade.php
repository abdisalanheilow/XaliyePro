@extends('admin.admin_master')

@section('title', 'View Vendor - XaliyePro')

@section('admin')
    <div x-data="{
                        showEditModal: false,
                        isSaving: false,
                        currentVendor: {
                            id: '{{ $vendor->id }}',
                            vendor_id: '{{ $vendor->vendor_id }}',
                            name: '{{ $vendor->name }}',
                            email: '{{ $vendor->email }}',
                            phone: '{{ $vendor->phone }}',
                            type: '{{ $vendor->type }}',
                            status: '{{ $vendor->status }}',
                            address: '{{ $vendor->address }}'
                        }
                    }">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('contacts.vendors.index') }}"
                    class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $vendor->name }}</h1>
                    <p class="text-gray-500 mt-0.5 text-sm">Vendor Profile — <span
                            class="font-medium text-gray-700">{{ $vendor->vendor_id }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="px-3 py-1.5 {{ $vendor->status == 'active' ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-red-100 text-red-700' }} text-xs font-bold rounded-lg uppercase tracking-wider">
                    {{ str_replace('_', ' ', $vendor->status) }}
                </span>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    New Purchase
                </button>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-gray-900 text-white rounded-lg text-sm font-bold hover:bg-gray-800 transition-all">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    Pay Vendor
                </button>
            </div>
        </div>

        <!-- Vendor Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Column: Personnel & Invoices -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Contact Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                            <i data-lucide="truck" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Vendor Information</h2>
                            <p class="text-xs text-gray-500">Core details about this supplier</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Vendor
                                    ID</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $vendor->vendor_id }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Company
                                    Name</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $vendor->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email
                                    Address</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $vendor->email ?: 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone
                                    Number</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $vendor->phone ?: 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Tax ID /
                                    TIN</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $vendor->tax_id ?: 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Address</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    @if ($vendor->address || $vendor->city || $vendor->country)
                                        {{ $vendor->address }}
                                        @if ($vendor->address && ($vendor->city || $vendor->country))<br>@endif
                                        {{ implode(', ', array_filter([$vendor->city, $vendor->country])) }}
                                    @else
                                        No address provided
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Purchases Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="shopping-bag" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Recent Purchase Orders</h2>
                                <p class="text-xs text-gray-500">Latest procurement history</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs font-bold text-[#28A375] hover:underline">View All</a>
                    </div>
                    <div class="p-5">
                        @if ($vendor->purchaseOrders->count() > 0)
                            <div class="space-y-4">
                                @foreach ($vendor->purchaseOrders as $order)
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $order->status === 'received' ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-gray-100 text-gray-500' }}">
                                            <i data-lucide="file-text" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('purchases.orders.show', $order->id) }}" class="text-sm font-bold text-gray-900 hover:text-[#28A375]">{{ $order->order_no }}</a>
                                            <p class="text-xs text-gray-500">{{ $order->order_date->format('M d, Y') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-gray-900">${{ number_format($order->grand_total, 2) }}</p>
                                        <p class="text-[10px] font-bold uppercase tracking-wider {{ $order->status === 'received' ? 'text-[#16A34A]' : 'text-amber-600' }}">{{ $order->status }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3">
                                    <i data-lucide="file-x" class="w-6 h-6 text-gray-300"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900">No Purchase Orders Found</h4>
                                <p class="text-xs text-gray-500 mt-1 max-w-[240px]">No recent procurement activity for this vendor.</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Remittance History Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="history" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Payments & Remittances</h2>
                                <p class="text-xs text-gray-500">Latest successful payouts</p>
                            </div>
                        </div>
                        <a href="#" class="text-xs font-bold text-[#28A375] hover:underline">View All</a>
                    </div>
                    <div class="p-5">
                        @if ($vendor->vendorPayments->count() > 0)
                            <div class="space-y-4">
                                @foreach ($vendor->vendorPayments as $payment)
                                <div class="flex items-center justify-between p-3 rounded-xl border border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 bg-emerald-50 text-emerald-600 rounded-lg flex items-center justify-center">
                                            <i data-lucide="receipt" class="w-4 h-4"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $payment->payment_no }}</p>
                                            <p class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }} &bull; {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-black text-emerald-600">${{ number_format($payment->amount, 2) }}</p>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-8 text-center bg-gray-50 rounded-xl border-2 border-dashed border-gray-200">
                                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-sm mb-3">
                                    <i data-lucide="banknote" class="w-6 h-6 text-gray-300"></i>
                                </div>
                                <h4 class="text-sm font-bold text-gray-900">No Payment History</h4>
                                <p class="text-xs text-gray-500 mt-1 max-w-[240px]">No payment records found for this vendor.</p>
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
                            <h2 class="text-lg font-semibold text-gray-900">Financial Profile</h2>
                            <p class="text-xs text-gray-500">Procurement overview</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Vendor Type</label>
                            <p class="mt-1 text-sm font-medium text-gray-900 tracking-wide capitalize">{{ $vendor->type }}
                            </p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Payment
                                Terms</label>
                            <p class="mt-1 text-sm font-medium text-gray-900">{{ $vendor->payment_terms_label }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Credit Limit</label>
                            <p class="mt-1 text-sm font-bold text-gray-900">
                                ${{ number_format($vendor->credit_limit ?? 0, 2) }}</p>
                        </div>
                        <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Member Since</label>
                            <div class="flex items-center gap-2 mt-1">
                                <i data-lucide="calendar" class="w-4 h-4 text-[#28A375]"></i>
                                <span
                                    class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($vendor->created_at)->format('M d, Y') }}</span>
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
                                <h2 class="text-lg font-semibold text-gray-900">Vendor Notes</h2>
                                <p class="text-xs text-gray-500">Internal procurement notes</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            @if ($vendor->notes)
                                <p class="text-sm font-medium text-gray-700 leading-relaxed">{{ $vendor->notes }}</p>
                            @else
                                <p class="text-sm text-gray-500 italic">No internal notes available for this vendor.</p>
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
                    <span class="text-2xl font-bold text-[#28A375]">{{ $vendor->initials }}</span>
                </div>
                <div>
                    <p class="text-lg font-bold text-gray-900">{{ $vendor->name }}</p>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $vendor->vendor_id }}</p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="trending-up" class="w-5 h-5 text-emerald-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-gray-900">${{ number_format($vendor->total_purchases_val ?? 0, 2) }}</p>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Total Purchases</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 {{ ($vendor->total_payable_val ?? 0) > 0 ? 'bg-red-100' : 'bg-gray-100' }} rounded-lg flex items-center justify-center">
                        <i data-lucide="alert-circle" class="w-5 h-5 {{ ($vendor->total_payable_val ?? 0) > 0 ? 'text-red-500' : 'text-gray-500' }}"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold {{ ($vendor->total_payable_val ?? 0) > 0 ? 'text-red-600' : 'text-gray-900' }}">
                    ${{ number_format($vendor->total_payable_val ?? 0, 2) }}</p>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Total Payable</p>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5 text-amber-600"></i>
                    </div>
                </div>
                <p class="text-2xl font-bold text-amber-600">${{ number_format($vendor->pending_orders_val ?? 0, 2) }}</p>
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wider mt-1">Pending Orders</p>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="flex items-center gap-6 text-xs text-gray-400">
            <div class="flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>Created: {{ $vendor->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <span>Last Updated: {{ $vendor->updated_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>
    </div>
@endsection
