@extends('admin.admin_master')

@section('title', 'View Service - XaliyePro')

@section('admin')
    <div>
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
                <a href="{{ route('services.index') }}"
                    class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $product->name }}</h1>
                    <p class="text-gray-500 mt-0.5 text-sm">Service SKU — <span
                            class="font-medium text-gray-700">{{ $product->sku }}</span></p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span
                    class="px-3 py-1.5 {{ $product->status == 'active' ? 'bg-[#DCFCE7] text-[#16A34A]' : 'bg-gray-100 text-gray-700' }} text-xs font-bold rounded-lg uppercase tracking-wider">
                    {{ str_replace('_', ' ', $product->status) }}
                </span>
                <span
                    class="px-3 py-1.5 bg-purple-100 text-purple-700 text-xs font-bold rounded-lg uppercase tracking-wider">
                    {{ ucfirst($product->type) }}
                </span>
                <button
                    class="flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all cursor-not-allowed opacity-80" disabled>
                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                    Edit Service
                </button>
            </div>
        </div>

        <!-- Service Information Cards -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
            <!-- Main Column: General & Description -->
            <div class="lg:col-span-2 space-y-6">
                <!-- General Info Card -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                            <i data-lucide="briefcase" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">General Information</h2>
                            <p class="text-xs text-gray-500">Core details and categorization</p>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Service Name</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $product->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Category</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $product->category->name ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">SKU / Item Code</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $product->sku }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Billing Unit</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    {{ $product->unit->name ?? 'N/A' }} 
                                    @if ($product->unit) ({{ $product->unit->short_name }}) @endif
                                </p>
                            </div>
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
                                <p class="text-xs text-gray-500">Service terms & inclusions</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-5">
                        <div class="bg-gray-50 rounded-xl border border-gray-200 p-4">
                            @if ($product->description)
                                <p class="text-sm font-medium text-gray-700 leading-relaxed">{{ $product->description }}</p>
                            @else
                                <p class="text-sm text-gray-500 italic">No description available for this service.</p>
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
                                <span>{{ $product->salesAccount->name ?? 'Default Sales Account' }}</span>
                                <span class="text-xs text-gray-500">{{ $product->salesAccount->code ?? '' }}</span>
                            </p>
                        </div>
                         <div>
                            <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider flex justify-between">
                                Purchase Account
                                <span class="text-red-500">Expense</span>
                            </label>
                            <p class="mt-1 text-sm font-medium text-gray-900 bg-gray-50 border border-gray-100 p-2 rounded w-full flex justify-between items-center">
                                <span>{{ $product->purchaseAccount->name ?? 'Default Expense Account' }}</span>
                                <span class="text-xs text-gray-500">{{ $product->purchaseAccount->code ?? '' }}</span>
                            </p>
                        </div>
                        <div class="md:col-span-2 mt-2 bg-blue-50/50 p-3 rounded-lg border border-blue-100 flex gap-3 text-xs text-blue-800">
                             <i data-lucide="info" class="w-4 h-4 text-blue-500 shrink-0"></i>
                             <p>Inventory Asset and COGS ledgers are natively disabled for service offerings.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Pricing & Insights -->
            <div class="space-y-6">
                
                <!-- Action Stat -->
                <div class="bg-gradient-to-br from-[#28A375] to-[#1e825d] rounded-xl shadow border border-green-600 p-6 text-white text-center">
                    <div class="w-12 h-12 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-3 backdrop-blur-sm">
                        <i data-lucide="dollar-sign" class="w-6 h-6 text-white"></i>
                    </div>
                    <p class="text-sm font-medium text-white/80 uppercase tracking-wider mb-1">Selling Rate</p>
                    <h3 class="text-4xl font-extrabold">${{ number_format($product->selling_price, 2) }}</h3>
                    <p class="text-sm text-white/90 mt-1">per {{ $product->unit->short_name ?? 'Unit' }}</p>
                    @if ($product->tax_rate > 0)
                        <p class="text-xs text-white/70 mt-2">+ {{ $product->tax_rate }}% Tax Rate</p>
                    @endif
                </div>

                <!-- Pricing Insights -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                            <i data-lucide="tag" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Profitability</h2>
                            <p class="text-xs text-gray-500">Costs vs Revenue</p>
                        </div>
                    </div>
                    <div class="p-5 space-y-4">
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Estimated Cost</span>
                            <span class="text-sm font-bold text-gray-900">${{ number_format($product->cost_price, 2) }}</span>
                        </div>
                        <div class="flex justify-between items-center pb-3 border-b border-gray-100">
                            <span class="text-sm font-medium text-gray-500">Gross Margin</span>
                            @php $margin = $product->selling_price - $product->cost_price; @endphp
                            <span class="text-sm font-bold {{ $margin >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                ${{ number_format($margin, 2) }}
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-sm font-medium text-gray-500">Profit Rate</span>
                            @php 
                                $percent = $product->selling_price > 0 
                                    ? (($product->selling_price - $product->cost_price) / $product->selling_price) * 100 
                                    : 0; 
                            @endphp
                            <span class="text-sm font-bold {{ $percent >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ number_format($percent, 1) }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timestamps -->
        <div class="flex items-center gap-6 text-xs text-gray-400">
            <div class="flex items-center gap-1.5">
                <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                <span>Added: {{ $product->created_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
            <div class="flex items-center gap-1.5">
                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                <span>Last Modified: {{ $product->updated_at->format('M d, Y \a\t h:i A') }}</span>
            </div>
        </div>
    </div>
@endsection
