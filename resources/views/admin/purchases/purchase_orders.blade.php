@extends('admin.admin_master')

@section('title', 'Purchase Orders - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Page Title & Actions -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Orders</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and track your procurement orders and fulfillment</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 inline-flex items-center gap-2 transition-all">
                <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                Export
            </button>
            <a href="{{ route('purchases.orders.create') }}"
                class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                <i data-lucide="plus" class="w-4 h-4"></i>
                New Order
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        @include('admin.partials.stats_card', [
            'title' => 'Total Volume',
            'value' => '$' . number_format($stats['total_amount'], 2),
            'icon' => 'shopping-bag',
            'color' => '#28A375',
            'iconBg' => 'bg-[#28A375]',
            'iconShadow' => 'shadow-green-100',
            'subtitle' => $stats['total_count'] . ' total orders'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Pending',
            'value' => number_format($stats['pending_count']),
            'icon' => 'clock',
            'color' => '#F59E0B',
            'iconBg' => 'bg-orange-500',
            'iconShadow' => 'shadow-orange-100',
            'subtitle' => 'Awaiting fulfillment'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Received',
            'value' => number_format($stats['received_count']),
            'icon' => 'check-circle',
            'color' => '#10B981',
            'iconBg' => 'bg-green-500',
            'iconShadow' => 'shadow-green-100',
            'subtitle' => 'Complete orders'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Drafts',
            'value' => number_format($stats['draft_count']),
            'icon' => 'file-edit',
            'color' => '#6B7280',
            'iconBg' => 'bg-gray-500',
            'iconShadow' => 'shadow-gray-100',
            'subtitle' => 'Unsent orders'
        ])
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form action="{{ route('purchases.orders.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 w-full relative">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by PO number or vendor..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:bg-white">
            </div>
            <div class="flex items-center gap-3 w-full md:w-auto">
                <select name="status" onchange="this.form.submit()" class="flex-1 md:w-48 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:bg-white">
                    <option value="All Status">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Received</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
                <button type="submit" class="p-2.5 bg-[#28A375] text-white rounded-lg hover:bg-[#229967] transition-colors">
                    <i data-lucide="filter" class="w-5 h-5"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Order #</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Location</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if (count($orders) > 0)
                        @foreach ($orders as $order)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-[#28A375]">{{ $order->order_no }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr($order->vendor->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <div class="flex flex-col">
                                        <span class="text-sm font-semibold text-gray-900 leading-none mb-1">{{ $order->vendor->name ?? 'Unknown Vendor' }}</span>
                                        <span class="text-[10px] text-gray-400 font-medium tracking-tight">{{ $order->vendor->vendor_id ?? '' }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-gray-700">{{ $order->branch->name ?? 'Main Branch' }}</span>
                                    <span class="text-[10px] text-gray-400">{{ $order->store->name ?? 'Default Store' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $order->order_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">${{ number_format($order->grand_total, 2) }}</span>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $statusClasses = [
                                        'pending' => 'bg-yellow-50 text-yellow-700 ring-yellow-600/20',
                                        'received' => 'bg-green-50 text-green-700 ring-green-600/20',
                                        'draft' => 'bg-gray-100 text-gray-700 ring-gray-600/20',
                                        'cancelled' => 'bg-red-50 text-red-700 ring-red-600/20',
                                    ];
                                    $class = $statusClasses[strtolower($order->status)] ?? 'bg-gray-50 text-gray-700 ring-gray-600/20';
                                @endphp
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold ring-1 ring-inset uppercase {{ $class }}">
                                    {{ $order->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <a href="{{ route('purchases.orders.show', $order->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#28A375]">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('purchases.orders.edit', $order->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#F59E0B]">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('purchases.orders.destroy', $order->id) }}" method="POST" class="inline">
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
                            <div class="flex flex-col items-center justify-center opacity-50">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100 shadow-sm">
                                    <i data-lucide="shopping-basket" class="w-8 h-8 text-gray-300"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 tracking-tight">No Orders Found</h3>
                                <p class="text-xs text-gray-500 mt-1 max-w-[200px]">We couldn't find any purchase orders matching your criteria.</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>

        @if (count($orders) > 0)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $orders->links() }}
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
            text: "This action will permanently delete this purchase order. You cannot undo this!",
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

