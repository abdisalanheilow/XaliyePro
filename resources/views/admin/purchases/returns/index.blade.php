@extends('admin.admin_master')

@section('title', 'Purchase Returns - XaliyePro')

@section('admin')
<div class="space-y-6">
    <!-- Page Title -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Purchase Returns</h1>
            <p class="text-sm text-gray-500 mt-1">Manage and track product returns to vendors</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 inline-flex items-center gap-2 transition-all">
                <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                Export
            </button>
            <a href="{{ route('purchases.returns.create') }}" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm">
                <i data-lucide="plus" class="w-4 h-4"></i>
                New Return
            </a>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#28A375]"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Total Returns</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $stats['total_count'] }} transactions</p>
                </div>
                <div class="w-12 h-12 bg-[#28A375]/10 rounded-lg flex items-center justify-center transition-colors group-hover:bg-[#28A375]/20">
                    <i data-lucide="rotate-ccw" class="w-6 h-6 text-[#28A375]"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-blue-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">This Month</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['month_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Current month total</p>
                </div>
                <div class="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-blue-100">
                    <i data-lucide="bar-chart-3" class="w-6 h-6 text-blue-600"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-yellow-400"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Today</p>
                    <p class="text-3xl font-bold text-gray-900">${{ number_format($stats['today_amount'], 2) }}</p>
                    <p class="text-xs text-gray-500 mt-1">Today's returns</p>
                </div>
                <div class="w-12 h-12 bg-yellow-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-yellow-100">
                    <i data-lucide="refresh-cw" class="w-6 h-6 text-yellow-600"></i>
                </div>
            </div>
        </div>

        <div class="relative bg-white rounded-xl p-5 border border-gray-200 overflow-hidden group">
            <div class="absolute left-0 top-0 bottom-0 w-1 bg-purple-500"></div>
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-sm text-gray-600 mb-2">Avg. Return</p>
                    <p class="text-3xl font-bold text-gray-900">${{ $stats['total_count'] > 0 ? number_format($stats['total_amount'] / $stats['total_count'], 2) : '0.00' }}</p>
                    <p class="text-xs text-gray-500 mt-1">Per transaction</p>
                </div>
                <div class="w-12 h-12 bg-purple-50 rounded-lg flex items-center justify-center transition-colors group-hover:bg-purple-100">
                    <i data-lucide="sigma" class="w-6 h-6 text-purple-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter -->
    <div class="bg-white rounded-xl border border-gray-200 p-4 shadow-sm">
        <form action="{{ route('purchases.returns.index') }}" method="GET" class="flex flex-col md:flex-row items-center gap-4">
            <div class="flex-1 w-full relative">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3.5 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search by return number or vendor..." class="w-full pl-11 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm transition-all focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:bg-white">
            </div>
            <button type="submit" class="p-2.5 bg-[#28A375] text-white rounded-lg hover:bg-[#229967] transition-colors">
                <i data-lucide="filter" class="w-5 h-5"></i>
            </button>
        </form>
    </div>

    <!-- Returns Table -->
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Return #</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Vendor</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Reference</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @if (count($returns) > 0)
                        @foreach ($returns as $return)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-[#28A375]">{{ $return->return_no }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-lg flex items-center justify-center text-white text-xs font-bold shadow-sm">
                                        {{ strtoupper(substr($return->vendor->name ?? 'V', 0, 1)) }}
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ $return->vendor->name ?? 'Unknown Vendor' }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                @if ($return->bill)
                                    <span class="text-xs font-bold text-gray-600 block">Bill: {{ $return->bill->bill_no }}</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 uppercase tracking-tighter">Independent</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $return->return_date->format('M d, Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-900">${{ number_format($return->grand_total, 2) }}</span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                    <a href="{{ route('purchases.returns.show', $return->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#28A375]">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('purchases.returns.edit', $return->id) }}" class="p-1.5 hover:bg-white hover:shadow-sm border border-transparent hover:border-gray-200 rounded-lg transition-all text-gray-400 hover:text-[#F59E0B]">
                                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                                    </a>
                                    <form action="{{ route('purchases.returns.destroy', $return->id) }}" method="POST" class="inline">
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
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center opacity-50">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 border border-gray-100 shadow-sm">
                                    <i data-lucide="rotate-ccw" class="w-8 h-8 text-gray-300"></i>
                                </div>
                                <h3 class="text-sm font-bold text-gray-900 tracking-tight">No returns found</h3>
                                <p class="text-xs text-gray-500 mt-1">Try adjusting your search or create a new return</p>
                            </div>
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
        
        @if (count($returns) > 0)
        <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
            {{ $returns->links() }}
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
            text: "This action will permanently delete this return record. You cannot undo this!",
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
