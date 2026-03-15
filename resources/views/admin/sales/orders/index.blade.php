@extends('admin.admin_master')

@section('title', 'Sales Orders - XaliyePro')

@section('admin')
    <div class="space-y-6">
        <!-- Header Section -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Sales Orders</h1>
                <p class="text-sm text-gray-500">Manage your customer orders and fulfillment</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                    <i data-lucide="upload" class="w-4 h-4 text-gray-400"></i>
                    Import
                </button>
                <button type="button" class="px-4 py-2 bg-white border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-all">
                    <i data-lucide="download" class="w-4 h-4 text-gray-400"></i>
                    Export
                </button>
                <a href="{{ route('sales.orders.create') }}"
                    class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Order
                </a>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Orders -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-[#28A375]">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Orders</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['total_count']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="trending-up" class="w-3 h-3 text-green-500"></i>
                        <p class="text-[11px] font-bold text-green-500">+4.2% <span class="text-gray-400 font-medium ml-1">vs last month</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-[#28A375] rounded-xl flex items-center justify-center shadow-lg shadow-green-100">
                    <i data-lucide="shopping-bag" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Active Orders -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-blue-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Active Orders</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['pending_count']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <i data-lucide="trending-up" class="w-3 h-3 text-blue-500"></i>
                        <p class="text-[11px] font-bold text-blue-500">+2.1% <span class="text-gray-400 font-medium ml-1">vs last month</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-blue-500 rounded-xl flex items-center justify-center shadow-lg shadow-blue-100">
                    <i data-lucide="refresh-cw" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Total Revenue -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-purple-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Total Revenue</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">${{ number_format($stats['total_amount'], 2) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-green-500"><i data-lucide="trending-up" class="inline w-3 h-3"></i> +12% <span class="text-gray-400 font-medium ml-1">from last month</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-purple-500 rounded-xl flex items-center justify-center shadow-lg shadow-purple-100">
                    <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                </div>
            </div>

            <!-- Invoiced -->
            <div class="bg-white p-5 rounded-xl border border-gray-100 shadow-sm flex items-center justify-between border-l-4 border-orange-500">
                <div>
                    <p class="text-sm font-medium text-gray-500 mb-1">Invoiced</p>
                    <h3 class="text-2xl font-bold text-gray-900 tracking-tight">{{ number_format($stats['invoiced_count']) }}</h3>
                    <div class="flex items-center gap-1 mt-1">
                        <p class="text-[11px] font-bold text-[#28A375]">Live status <span class="text-gray-400 font-medium ml-1">Updated</span></p>
                    </div>
                </div>
                <div class="w-12 h-12 bg-orange-500 rounded-xl flex items-center justify-center shadow-lg shadow-orange-100">
                    <i data-lucide="file-check" class="w-6 h-6 text-white"></i>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-[300px] relative group">
                <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-[#28A375] transition-colors"></i>
                <input type="text" placeholder="Search by order no, customer..." class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] focus:ring-0 transition-all outline-none">
            </div>
            <select class="px-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] transition-all outline-none min-w-[150px]">
                <option value="">All Status</option>
                <option value="draft">Draft</option>
                <option value="confirmed">Confirmed</option>
                <option value="shipped">Shipped</option>
                <option value="delivered">Delivered</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <select class="px-4 py-2.5 bg-gray-50 border border-transparent rounded-lg text-sm focus:bg-white focus:border-[#28A375] transition-all outline-none min-w-[150px]">
                <option value="">All Branches</option>
            </select>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h3 class="text-lg font-bold text-gray-900 tracking-tight">Recent Orders</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Order No</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider">Contact</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Grand Total</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-6 py-4 text-[11px] font-bold text-gray-400 uppercase tracking-wider text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @if(count($orders) > 0)
                            @foreach ($orders as $order)
                            <tr class="hover:bg-gray-50/50 transition-all group">
                                <td class="px-6 py-4">
                                    <span class="text-sm font-bold text-gray-900 tracking-tight">{{ $order->order_no }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $order->order_date->format('M d, Y') }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-green-50 flex items-center justify-center text-[11px] font-bold text-[#28A375] shadow-sm">
                                             {{ strtoupper(substr($order->customer?->name ?? 'NA', 0, 2)) }}
                                         </div>
                                         <div>
                                             <div class="text-sm font-bold text-gray-900">{{ $order->customer?->name ?? 'No Customer' }}</div>
                                             <div class="text-[11px] text-gray-400 font-medium">CUS-{{ $order->customer ? str_pad($order->customer->id, 4, '0', STR_PAD_LEFT) : '0000' }}</div>
                                         </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-600 font-medium">{{ $order->customer?->email ?? '---' }}</div>
                                     <div class="text-[11px] text-gray-400 font-medium">{{ $order->customer?->phone ?? '---' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <span class="text-sm font-black text-gray-900 tracking-tighter">${{ number_format($order->grand_total, 2) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex justify-center">
                                        @php
                                            $statusClasses = [
                                                'draft' => 'bg-gray-50 text-gray-600 border-gray-100',
                                                'confirmed' => 'bg-blue-50 text-blue-600 border-blue-100',
                                                'shipped' => 'bg-purple-50 text-purple-600 border-purple-100',
                                                'delivered' => 'bg-green-50 text-green-600 border-green-100',
                                                'invoiced' => 'bg-orange-50 text-orange-600 border-orange-100',
                                                'cancelled' => 'bg-red-50 text-red-600 border-red-100',
                                            ];
                                            $statusClass = $statusClasses[strtolower($order->status)] ?? 'bg-gray-50 text-gray-600 border-gray-100';
                                        @endphp
                                        <span class="px-2.5 py-1 text-[11px] font-bold uppercase rounded-md border {{ $statusClass }} tracking-wide">
                                            {{ $order->status }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <a href="{{ route('sales.orders.show', $order->id) }}" class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-[#28A375] transition-all">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <a href="{{ route('sales.orders.edit', $order->id) }}" class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-blue-600 transition-all">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                        <button onclick="confirmDelete('{{ route('sales.orders.destroy', $order->id) }}')" class="p-2 hover:bg-white hover:shadow-md rounded-lg text-gray-400 hover:text-red-600 transition-all">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="p-4 bg-gray-50 rounded-full mb-4">
                                            <i data-lucide="file-text" class="w-12 h-12 text-gray-300"></i>
                                        </div>
                                        <h4 class="text-lg font-bold text-gray-900 tracking-tight">No Orders Found</h4>
                                        <p class="text-sm text-gray-500 max-w-[200px] mt-1">Start by creating your first customer order</p>
                                        <a href="{{ route('sales.orders.create') }}" class="mt-4 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold shadow-sm">Add New Order</a>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 bg-gray-50/30 border-t border-gray-100">
                {{ $orders->links() }}
            </div>
        </div>
    </div>
@endsection
