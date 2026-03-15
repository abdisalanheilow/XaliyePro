@extends('admin.admin_master')

@section('title', 'View Item - XaliyePro')

@section('admin')
    <div>
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('items.index') }}"
                    class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $item->name }}</h1>
                    <p class="text-gray-500 mt-0.5 text-sm">Item SKU — <span
                            class="font-medium text-gray-700">{{ $item->sku }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="px-3 py-1.5 {{ $item->status == 'active' ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-gray-100 text-gray-700' }} text-xs font-bold rounded-lg uppercase tracking-wider">
                    {{ str_replace('_', ' ', $item->status) }}
                </span>
                <span
                    class="px-3 py-1.5 {{ $item->type == 'service' ? 'bg-purple-100 text-purple-700' : 'bg-blue-100 text-blue-700' }} text-xs font-bold rounded-lg uppercase tracking-wider">
                    {{ ucfirst($item->type) }}
                </span>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all cursor-not-allowed opacity-80" disabled>
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    Edit Item
                </button>
            </div>
        </div>

        <!-- Item Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Column: General & Description -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                            <i data-lucide="{{ $item->type === 'service' ? 'briefcase' : 'package' }}" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">General Information</h2>
                            <p class="text-xs text-gray-500">Core details and categorization</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Item Name</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $item->name }}</p>
                            </div>
                            @if ($item->type === 'product')
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Barcode</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $item->barcode ?: 'N/A' }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Category</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $item->category->name ?? 'N/A' }}</p>
                            </div>
                            @if ($item->type === 'product')
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Brand</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $item->brand->name ?? 'N/A' }}</p>
                            </div>
                            @endif
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Unit of Measure</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    {{ $item->unit->name ?? 'N/A' }} 
                                    @if ($item->unit) ({{ $item->unit->short_name }}) @endif
                                </p>
                            </div>
                            @if ($item->type === 'product' && $item->location)
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Rack / Bin Location</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $item->location }}</p>
                            </div>
                            @endif
                            @if ($item->type === 'product' && $item->branch)
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Assigned Branch</label>
                                <p class="mt-1 text-[11px] font-semibold text-[#28A375] bg-green-50 w-max px-2.5 py-1 rounded border border-green-100">{{ $item->branch->name }}</p>
                            </div>
                            @endif
                            @if ($item->type === 'product' && $item->store)
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Assigned Store</label>
                                <p class="mt-1 text-[11px] font-semibold text-blue-600 bg-blue-50 w-max px-2.5 py-1 rounded border border-blue-100">{{ $item->store->name }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Description Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="align-left" class="w-5 h-5 text-amber-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Description</h2>
                                <p class="text-xs text-gray-500">Item details & notes</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            @if ($item->description)
                                <p class="text-sm font-medium text-gray-700 leading-relaxed">{{ $item->description }}</p>
                            @else
                                <p class="text-sm text-gray-500 italic">No description available for this item.</p>
                            @endif
                        </div>
                    </div>
                </div>
                
                <!-- Accounting Integration Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between p-5 border-b border-gray-200">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                <i data-lucide="landmark" class="w-5 h-5 text-indigo-600"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Accounting Integration</h2>
                                <p class="text-xs text-gray-500">Linked ledger accounts</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-5">
                         <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex justify-between">
                                Sales Account
                                <span class="text-green-500">Revenue</span>
                            </label>
                            <p class="mt-1 text-sm font-medium text-gray-900 bg-gray-50 border border-gray-100 p-2 rounded w-full flex justify-between items-center">
                                <span>{{ $item->salesAccount->name ?? 'Default Sales Account' }}</span>
                                <span class="text-xs text-gray-500">{{ $item->salesAccount->code ?? '' }}</span>
                            </p>
                        </div>
                         <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex justify-between">
                                Purchase Account
                                <span class="text-red-500">Expense</span>
                            </label>
                            <p class="mt-1 text-sm font-medium text-gray-900 bg-gray-50 border border-gray-100 p-2 rounded w-full flex justify-between items-center">
                                <span>{{ $item->purchaseAccount->name ?? 'Default Purchase Account' }}</span>
                                <span class="text-xs text-gray-500">{{ $item->purchaseAccount->code ?? '' }}</span>
                            </p>
                        </div>
                        
                        @if ($item->type !== 'service')
                         <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex justify-between">
                                Inventory Asset
                                <span class="text-blue-500">Asset</span>
                            </label>
                            <p class="mt-1 text-sm font-medium text-gray-900 bg-gray-50 border border-gray-100 p-2 rounded w-full flex justify-between items-center">
                                <span>{{ $item->inventoryAssetAccount->name ?? 'Default Inventory Account' }}</span>
                                <span class="text-xs text-gray-500">{{ $item->inventoryAssetAccount->code ?? '' }}</span>
                            </p>
                        </div>
                         <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex justify-between">
                                Cost of Goods Sold
                                <span class="text-orange-500">COGS</span>
                            </label>
                            <p class="mt-1 text-sm font-medium text-gray-900 bg-gray-50 border border-gray-100 p-2 rounded w-full flex justify-between items-center">
                                <span>{{ $item->cogsAccount->name ?? 'Default COGS Account' }}</span>
                                <span class="text-xs text-gray-500">{{ $item->cogsAccount->code ?? '' }}</span>
                            </p>
                        </div>
                        @else
                        <div class="md:col-span-2 text-center py-4 bg-gray-50 border border-gray-200 border-dashed rounded-lg">
                            <p class="text-sm text-gray-500 italic">Inventory Assets and COGS tracking are disabled for service items.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column: Pricing & Inventory -->
            <div class="space-y-6">
                
                <!-- Action Stat -->
                <div class="bg-gradient-to-br from-[#28A375] to-[#1e825d] rounded-xl shadow border border-green-600 p-6 text-white text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                    </div>
                    <p class="text-sm font-medium text-white/80 uppercase tracking-wider mb-1">Selling Price</p>
                    <h3 class="text-4xl font-extrabold">${{ number_format($item->selling_price, 2) }}</h3>
                    @if ($item->tax_rate > 0)
                        <p class="text-xs text-white/70 mt-2">+ {{ $item->tax_rate }}% Tax Rate</p>
                    @endif
                </div>

                <!-- Pricing Insights -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="tag" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Pricing Data</h2>
                            <p class="text-xs text-gray-500">Costs vs Sales</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100" x-show="'{{ $item->type }}' === 'product'">
                            <span class="text-sm font-medium text-gray-500">Cost Price</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($item->cost_price, 2) }}</span>
                        </div>
                        @if ($item->type === 'product')
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Gross Profit / Unit</span>
                            @php $margin = $item->selling_price - $item->cost_price; @endphp
                            <span class="text-sm font-bold {{ $margin >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($margin, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Profit Margin</span>
                            @php 
                                $percent = $item->selling_price > 0 
                                    ? (($item->selling_price - $item->cost_price) / $item->selling_price) * 100 
                                    : 0; 
                            @endphp
                            <span class="text-sm font-bold {{ $percent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($percent, 1) }}%
                            </span>
                        </div>
                        @else
                        <div class="text-center py-2 bg-gray-50 rounded italic text-xs text-gray-500">
                            Margin analysis unavailable for services.
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Inventory Management -->
                @if ($item->type !== 'service' && $item->track_inventory)
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 relative overflow-hidden">
                    @if ($item->current_stock <= $item->reorder_level && $item->current_stock > 0)
                        <div class="absolute top-0 right-0 w-2 h-full bg-orange-400"></div>
                    @elseif ($item->current_stock <= 0)
                        <div class="absolute top-0 right-0 w-2 h-full bg-red-500"></div>
                    @else
                        <div class="absolute top-0 right-0 w-2 h-full bg-[#28A375]"></div>
                    @endif

                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-gray-900 rounded-lg flex items-center justify-center">
                            <i data-lucide="boxes" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Inventory Limits</h2>
                            <p class="text-xs text-gray-500">Stock tracking</p>
                        </div>
                    </div>
                    
                    <div class="p-5">
                        <div class="bg-gray-50 p-4 rounded-xl mb-4 border border-gray-100 flex justify-between items-center">
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">On Hand</p>
                                <p class="text-2xl font-bold {{ $item->current_stock <= $item->reorder_level ? 'text-red-600' : 'text-gray-900' }}">
                                    {{ number_format($item->current_stock) }} <span class="text-sm font-semibold text-gray-500">{{ $item->unit->short_name ?? '' }}</span>
                                </p>
                            </div>
                            <div class="text-right">
                                @if ($item->current_stock <= 0)
                                    <span class="inline-flex py-1 px-2.5 bg-red-100 text-red-700 rounded-full text-[11px] font-bold">Out of Stock</span>
                                @elseif ($item->current_stock <= $item->reorder_level)
                                    <span class="inline-flex py-1 px-2.5 bg-orange-100 text-orange-700 rounded-full text-[11px] font-bold">Low Stock</span>
                                @else
                                    <span class="inline-flex py-1 px-2.5 bg-green-100 text-green-700 rounded-full text-[11px] font-bold">In Stock</span>
                                @endif
                            </div>
                        </div>

                        <div class="space-y-3">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Reorder Level</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($item->reorder_level) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Recommended Order Qty</span>
                                <span class="text-sm font-bold text-gray-900">{{ number_format($item->reorder_quantity) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-500">Inventory Value</span>
                                <span class="text-sm font-bold text-[#28A375]">${{ number_format($item->current_stock * $item->cost_price, 2) }}</span>
                            </div>
                        </div>
                        
                        <button class="w-full mt-5 py-2.5 border-2 border-dashed border-gray-300 rounded-lg text-sm font-medium text-gray-600 hover:text-gray-900 hover:border-gray-400 hover:bg-gray-50 transition-all flex items-center justify-center gap-2">
                            <i data-lucide="edit-2" class="w-4 h-4"></i> Adjust Stock Quantity
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Timestamps -->
        <div class="flex items-center gap-6 text-xs text-gray-400">
            <div class="flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>Added: {{ $item->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <span>Last Modified: {{ $item->updated_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>
    </div>
@endsection
