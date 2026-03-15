@extends('admin.admin_master')

@section('title', 'Inventory Valuation')

@section('admin')
    <div class="flex-1 flex flex-col overflow-hidden">
        <header class="bg-white border-b border-gray-200 px-8 py-5 no-print">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Inventory Valuation</h2>
                    <p class="text-sm text-gray-500 mt-1">Total asset value of current stock</p>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="window.print()" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors inline-flex items-center gap-2">
                        <i data-lucide="printer" class="w-4 h-4"></i> Print
                    </button>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto px-8 py-6">
            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
                    <div class="flex items-center gap-4">
                        <div class="w-12 h-12 bg-green-50 rounded-xl flex items-center justify-center text-[#28A375]">
                            <i data-lucide="calculator" class="w-6 h-6"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Total Asset Value</p>
                            <h3 class="text-2xl font-bold text-gray-900">${{ number_format($totalValuation, 2) }}</h3>
                        </div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm no-print">
                    <form method="GET" action="{{ route('reports.inventory-valuation') }}">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter by Warehouse</label>
                        <div class="flex gap-3">
                            <select name="store_id" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375]">
                                <option value="">Global (All Warehouses)</option>
                                @foreach ($stores as $store)
                                    <option value="{{ $store->id }}" {{ $storeId == $store->id ? 'selected' : '' }}>{{ $store->name }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967]">Filter</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <table class="w-full border-collapse">
                    <thead>
                        <tr class="text-left bg-gray-50 border-b border-gray-200">
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Item Details</th>
                            <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">On Hand Qty</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Unit Cost</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Total Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($valuationData as $data)
                            @if ($data['qty'] != 0)
                            <tr class="hover:bg-gray-50/50 transition-colors">
                                <td class="px-6 py-4 font-bold text-gray-900 text-sm">
                                    {{ $data['name'] }} <br>
                                    <span class="text-[10px] text-gray-500 font-medium tracking-wider">SKU: {{ $data['sku'] }}</span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 font-medium">{{ $data['category'] }}</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm">{{ number_format($data['qty']) }}</td>
                                <td class="px-6 py-4 text-right text-sm text-gray-600">${{ number_format($data['cost'], 2) }}</td>
                                <td class="px-6 py-4 text-right font-bold text-gray-900 text-sm">${{ number_format($data['valuation'], 2) }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 font-bold border-t border-gray-200">
                            <td colspan="4" class="px-6 py-4 text-right text-gray-900 uppercase">Grand Total</td>
                            <td class="px-6 py-4 text-right text-[#28A375] text-lg">${{ number_format($totalValuation, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </main>
    </div>
@endsection
