@extends('admin.admin_master')
@section('admin')
@php
    $categoryTotal = array_sum($categoryData);
    $categoryColors = ['#28A375', '#2563eb', '#9333ea', '#ea580c'];
@endphp
<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Dashboard</h1>
    <p class="text-gray-500 mt-1">Welcome back! Here's what's happening with your business.</p>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <!-- Total Revenue -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-[#28A375] p-6">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-2">Total Revenue</p>
                <h3 class="text-3xl font-bold text-gray-900">${{ number_format($stats['total_sales'] ?? 0, 2) }}</h3>
            </div>
            <div class="w-14 h-14 bg-[#28A375] rounded-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="dollar-sign" class="w-7 h-7 text-white"></i>
            </div>
        </div>
        <div class="flex items-center gap-1 text-sm">
            <i data-lucide="trending-up" class="w-4 h-4 text-green-600"></i>
            <span class="text-green-600 font-medium">+12.5%</span>
            <span class="text-gray-500">vs last month</span>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-blue-600 p-6">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-2">Total Orders</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_orders'] ?? 0) }}</h3>
            </div>
            <div class="w-14 h-14 bg-blue-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="shopping-cart" class="w-7 h-7 text-white"></i>
            </div>
        </div>
        <div class="flex items-center gap-1 text-sm">
            <i data-lucide="trending-up" class="w-4 h-4 text-green-600"></i>
            <span class="text-green-600 font-medium">+8.2%</span>
            <span class="text-gray-500">vs last month</span>
        </div>
    </div>

    <!-- Total Products -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-purple-600 p-6">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-2">Total Products</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_items'] ?? 0) }}</h3>
            </div>
            <div class="w-14 h-14 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="package" class="w-7 h-7 text-white"></i>
            </div>
        </div>
        <div class="flex items-center gap-1 text-sm">
            <i data-lucide="trending-up" class="w-4 h-4 text-green-600"></i>
            <span class="text-green-600 font-medium">+4.3%</span>
            <span class="text-gray-500">vs last month</span>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 border-l-4 border-l-orange-600 p-6">
        <div class="flex items-start justify-between mb-3">
            <div class="flex-1">
                <p class="text-sm text-gray-600 mb-2">Total Customers</p>
                <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total_customers'] ?? 0) }}</h3>
            </div>
            <div class="w-14 h-14 bg-orange-600 rounded-lg flex items-center justify-center flex-shrink-0">
                <i data-lucide="users" class="w-7 h-7 text-white"></i>
            </div>
        </div>
        <div class="flex items-center gap-1 text-sm">
            <i data-lucide="trending-down" class="w-4 h-4 text-red-600"></i>
            <span class="text-red-600 font-medium">-2.4%</span>
            <span class="text-gray-500">vs last month</span>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    <!-- Revenue & Expenses Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-gray-900">Revenue & Expenses</h3>
            <select class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                <option>Last 7 days</option>
            </select>
        </div>
        <div class="h-64">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Sales by Category Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="font-bold text-gray-900 mb-4">Sales by Category</h3>
        <div class="flex items-center justify-center h-64">
            <canvas id="categoryChart"></canvas>
        </div>
        <!-- Legend -->
        <div class="mt-4 grid grid-cols-2 gap-3">
            @if(count($categoryLabels) > 0)
                @foreach($categoryLabels as $label)
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 rounded-full" style="background-color: {{ $categoryColors[$loop->index] ?? '#6b7280' }}"></div>
                    <span class="text-sm text-gray-600">{{ $label }}</span>
                    <span class="text-sm font-medium text-gray-900 ml-auto">{{ $categoryTotal > 0 ? number_format(($categoryData[$loop->index] / $categoryTotal) * 100, 1) : 0 }}%</span>
                </div>
                @endforeach
            @else
                <div class="col-span-2 text-center py-4 text-gray-400 text-sm">No category data available</div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <div class="p-6 border-b border-gray-100">
        <h3 class="font-bold text-gray-900">Recent Activity</h3>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-gray-50 border-b border-gray-200">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Activity</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                @if(count($recentInvoices) > 0)
                    @foreach($recentInvoices as $invoice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900">New Sale Invoice #{{ $invoice->invoice_no }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ \Carbon\Carbon::parse($invoice->invoice_date)->format('M d, Y') }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">${{ number_format($invoice->grand_total, 2) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2.5 py-1 rounded-full text-xs font-medium {{ strtolower($invoice->status) == 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($invoice->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-500 italic">No recent activity found.</td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($labels) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($revenueData) !!},
                    borderColor: '#28A375',
                    backgroundColor: 'rgba(40, 163, 117, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Expenses',
                    data: {!! json_encode($expenseData) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'bottom'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });

        // Category Pie Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryLabels) !!},
                datasets: [{
                    data: {!! json_encode($categoryData) !!},
                    backgroundColor: [
                        '#28A375',
                        '#2563eb',
                        '#9333ea',
                        '#ea580c'
                    ],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    });
</script>
@endpush
@endsection
