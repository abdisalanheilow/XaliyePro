@extends('admin.admin_master')

@section('title', 'Purchase Bills - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Bills</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and track all your vendor bills and payments</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 inline-flex items-center gap-2">
                <i data-lucide="download" class="w-4 h-4"></i>
                Export
            </button>
            <a href="{{ route('purchases.bills.create') }}" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                New Bill
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @include('admin.partials.stats_card', [
            'title' => 'Total Bills',
            'value' => '$' . number_format($stats['total_amount'], 2),
            'icon' => 'dollar-sign',
            'color' => '#28A375',
            'iconBg' => 'bg-[#28A375]',
            'iconShadow' => 'shadow-green-100',
            'subtitle' => $stats['total_count'] . ' bills'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Paid',
            'value' => '$' . number_format($stats['paid_amount'], 2),
            'icon' => 'file-check',
            'color' => '#10B981',
            'iconBg' => 'bg-green-500',
            'iconShadow' => 'shadow-green-100',
            'subtitle' => $stats['paid_count'] . ' bills'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Unpaid',
            'value' => '$' . number_format($stats['unpaid_amount'], 2),
            'icon' => 'clock',
            'color' => '#F59E0B',
            'iconBg' => 'bg-orange-500',
            'iconShadow' => 'shadow-orange-100',
            'subtitle' => $stats['unpaid_count'] . ' bills'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Overdue',
            'value' => '$' . number_format($stats['overdue_amount'], 2),
            'icon' => 'alert-circle',
            'color' => '#EF4444',
            'iconBg' => 'bg-red-500',
            'iconShadow' => 'shadow-red-100',
            'subtitle' => $stats['overdue_count'] . ' bills'
        ])
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form action="{{ route('purchases.bills.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 w-full relative">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by bill number or vendor..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:bg-white">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="flex-1 md:w-48 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:bg-white">
                    <option value="All Status">All Status</option>
                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="unpaid" {{ request('status') == 'unpaid' ? 'selected' : '' }}>Unpaid</option>
                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
                </select>
                <button type="submit" class="p-2.5 bg-[#28A375] text-white rounded-lg hover:bg-[#229967] transition-colors">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Bills Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto overflow-y-auto max-h-[calc(100vh-400px)] custom-scrollbar">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Bill #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Due Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if (count($bills) > 0)
                        @foreach ($bills as $bill)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-[#28A375]">{{ $bill->bill_no }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                    {{ $bill->vendor->initials ?? 'V' }}
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $bill->vendor->name ?? 'Unknown Vendor' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $bill->bill_date->format('M d, Y') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm text-gray-600">{{ $bill->due_date ? $bill->due_date->format('M d, Y') : '-' }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="text-sm font-bold text-gray-900">${{ number_format($bill->grand_total, 2) }}</span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusClasses = [
                                    'paid' => 'bg-green-50 text-green-700 ring-green-600/20',
                                    'unpaid' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                    'partially_paid' => 'bg-blue-50 text-blue-700 ring-blue-600/20',
                                    'overdue' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    'draft' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                                ];
                                $class = $statusClasses[strtolower($bill->status)] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold ring-1 ring-inset {{ $class }}">
                                {{ ucfirst(str_replace('_', ' ', $bill->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('purchases.bills.show', $bill->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#28A375]">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('purchases.bills.edit', $bill->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#F59E0B]">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </a>
                                <form action="{{ route('purchases.bills.destroy', $bill->id) }}" method="POST" class="inline delete-form">
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
                        <td colspan="7" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <i data-lucide="file-text" class="w-8 h-8 text-gray-300"></i>
                                </div>
                                <h3 class="text-sm font-semibold text-gray-900">No bills found</h3>
                                <p class="text-xs text-gray-500 mt-1">Try adjusting your search or filters</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            {{ $bills->links() }}
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(button) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This action will permanently delete this purchase bill. You cannot undo this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28A375',
            cancelButtonColor: '#EF4444',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel',
            background: '#ffffff',
            borderRadius: '1rem',
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

