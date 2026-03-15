@extends('admin.admin_master')

@section('title', 'Vendor Payments - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Vendor Payments</h1>
            <p class="text-sm text-gray-500 mt-1">Track and manage outgoing payments to vendors (Payment-Out)</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 inline-flex items-center gap-2 transition-all">
                <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                Export
            </button>
            <a href="{{ route('purchases.payments.create') }}" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                Record Payment
            </a>
        </div>
    </div>

    <!-- Stats Dashboard -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#28A375]"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Total Paid</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_count'] }} payments</p>
                </div>
                <div class="w-12 h-12 bg-[#28A375]/10 rounded-lg flex items-center justify-center transition-colors group-hover:bg-[#28A375]/20">
                    <i data-lucide="circle-dollar-sign" class="w-6 h-6 text-[#28A375]"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Paid This Month</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['month_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Reflects current month</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-blue-100">
                    <i data-lucide="calendar" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-400"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Paid Today</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['today_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Today's transactions</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-yellow-100">
                    <i data-lucide="zap" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-purple-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Avg. Payment</p>
                    <p class="text-3xl font-bold text-gray-900">${{ $stats['total_count'] > 0 ? number_format($stats['total_amount'] / $stats['total_count'], 2) : '0.00' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Per transaction</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-purple-100">
                    <i data-lucide="trending-up" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form action="{{ route('purchases.payments.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 w-full relative">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by payment number or vendor..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:bg-white">
            </div>
            <button type="submit" class="p-2.5 bg-[#28A375] text-white rounded-lg hover:bg-[#229967] transition-colors">
                <i data-lucide="filter" class="w-5 h-5"></i>
            </button>
        </form>
    </div>

    <!-- Payments Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Payment #</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date/Method</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Account</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if (count($payments) > 0)
                        @foreach ($payments as $payment)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-[#28A375]">{{ $payment->payment_no }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr($payment->vendor->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $payment->vendor->name ?? 'Unknown Vendor' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-sm text-gray-900 font-medium">{{ $payment->payment_date->format('M d, Y') }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-wider">{{ $payment->payment_method }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-xs font-semibold text-gray-600">{{ $payment->account->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold text-gray-900">{{ $payment->currency ?? '$' }}{{ number_format($payment->amount, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <a href="{{ route('purchases.payments.show', $payment->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#28A375]">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('purchases.payments.destroy', $payment->id) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDelete(this)" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#EF4444]">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @else
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                            <div class="flex flex-col items-center justify-center opacity-50">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100 shadow-sm">
                                    <i data-lucide="credit-card" class="w-8 h-8 text-gray-300"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 tracking-tight">No payments found</h3>
                                <p class="text-xs text-gray-500 mt-1">Record your first vendor payment to get started</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        @if ($payments->hasPages())
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Deleting this payment will undo the balance reduction on the associated bill. Deletion cannot be reversed!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28A375',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'rounded-3xl',
                confirmButton: 'rounded-lg px-6 py-2.5',
                cancelButton: 'rounded-lg px-6 py-2.5'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                button.closest('form').submit();
            }
        });
    }
</script>
@endpush
