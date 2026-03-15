@extends('admin.admin_master')

@section('title', 'New Purchase Bill - XaliyePro')

@section('admin')
<div x-data="{ showAdd: false, addForm: { type: 'individual' }, isSaving: false }">
<form action="{{ route('purchases.bills.store') }}" method="POST" id="purchaseBillForm" enctype="multipart/form-data" @submit="isSaving = true">
    @csrf
    @if (isset($selectedReceipt))
        <input type="hidden" name="goods_receipt_id" value="{{ $selectedReceipt->id }}">
        @if ($selectedReceipt->purchase_order_id)
            <input type="hidden" name="purchase_order_id" value="{{ $selectedReceipt->purchase_order_id }}">
        @endif
    @endif
    <div class="space-y-6">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('purchases.bills.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">New Purchase Bill</h1>
                </div>
                <p class="text-sm text-gray-500">Create a new vendor bill and record purchase</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.bills.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" name="action" value="draft" :disabled="isSaving" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all active:scale-95 shadow-sm disabled:opacity-50">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="save" :disabled="isSaving" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm disabled:opacity-50">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Save & Record
                        </div>
                    </template>
                    <template x-if="isSaving">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </div>
                    </template>
                </button>
            </div>
        </div>

        <!-- Purchase Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm overflow-hidden" x-data="{ 
            selectedBranch: '',
            stores: [
                @foreach ($stores as $store)
                { id: '{{ $store->id }}', name: '{{ $store->name }}', branch_id: '{{ $store->branch_id }}' },
                @endforeach
            ],
            filteredStores() {
                if (!this.selectedBranch) return this.stores;
                return this.stores.filter(s => s.branch_id == this.selectedBranch);
            }
        }">
            <div class="flex items-center gap-2 mb-8">
                <div class="p-2 bg-green-50 rounded-lg">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#28A375]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Bill Information</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                <!-- Row 1: Primary Identifiers -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Vendor <span class="text-red-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <select name="vendor_id" required class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                            <option value="">Select Vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ (isset($selectedReceipt) && $selectedReceipt->vendor_id == $vendor->id) ? 'selected' : '' }}>
                                    {{ $vendor->name }}
                                </option>
                            @endforeach
                        </select>
                        <button type="button" @click="showAdd = true" class="px-3 py-2.5 bg-gray-50 border border-gray-300 rounded-lg hover:bg-white hover:text-[#28A375] hover:border-[#28A375] transition-all group shadow-sm">
                            <i data-lucide="plus" class="w-4 h-4 text-gray-500 group-hover:text-[#28A375]"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Bill Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="bill_no" value="{{ $billNo }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600 focus:outline-none" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reference (Invoice #)</label>
                    <div class="relative">
                        <i data-lucide="hash" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" name="reference_no" placeholder="Enter invoice number" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <!-- Row 2: Dates & Terms -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Bill Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="bill_date" value="{{ date('Y-m-d') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Due Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Terms</label>
                    <select name="payment_terms" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="net_15">Net 15</option>
                        <option value="net_30" selected>Net 30</option>
                        <option value="net_45">Net 45</option>
                        <option value="due_on_receipt">On Receipt</option>
                    </select>
                </div>

                <!-- Row 3: Inventory Destination -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Branch (Optional)</label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <select name="branch_id" x-model="selectedBranch" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white" {{ isset($selectedReceipt) && $selectedReceipt->order ? 'x-init="selectedBranch = \''.$selectedReceipt->order->branch_id.'\'"' : '' }}>
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>

                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Store / Warehouse (Optional)</label>
                    <div class="relative">
                        <i data-lucide="archive" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <select name="store_id" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                            <option value="">Select Store</option>
                            <template x-for="store in filteredStores()" :key="store.id">
                                <option :value="store.id" x-text="store.name"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="flex items-end pb-1">
                    <p class="text-xs text-gray-400 italic">Verify all information before recording.</p>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Line Items</h2>
                </div>
                <button type="button" onclick="addLineItem()" class="px-3 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Item
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full" id="lineItemsTable">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-8">#</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Product Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-32">Quantity</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-32">Rate</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase w-32">Tax</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase w-40">Amount</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-gray-600 uppercase w-16"></th>
                        </tr>
                    </thead>
                    <tbody id="lineItemsBody">
                        @if (isset($selectedReceipt) && $selectedReceipt->items->count() > 0)
                            @foreach ($selectedReceipt->items as $index => $recItem)
                                <tr class="border-b border-gray-200">
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                    <td class="px-4 py-3">
                                        <select name="items[{{ $index }}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                                            <option value="">Select Product</option>
                                            @foreach ($items as $item)
                                                <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}" {{ $recItem->item_id == $item->id ? 'selected' : '' }}>
                                                    {{ $item->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-3">
                                        <!-- Pre-fill with the received (accepted) quantity from GRN, not ordered quantity -->
                                        <input type="number" name="items[{{ $index }}][quantity]" value="{{ $recItem->received_qty }}" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                    </td>
                                    <td class="px-4 py-3">
                                        <!-- Attempt to grab price from original PO item, fallback to current cost -->
                                        @php
                                            $orderPrice = $recItem->item->cost_price;
                                            if ($selectedReceipt->order) {
                                                $poItem = $selectedReceipt->order->items->where('item_id', $recItem->item_id)->first();
                                                if ($poItem) {
                                                    $orderPrice = $poItem->unit_price;
                                                }
                                            }
                                        @endphp
                                        <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $orderPrice }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                    </td>
                                    <td class="px-4 py-3">
                                        <select name="items[{{ $index }}][tax_rate]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                            <option value="0">No Tax</option>
                                            <option value="10">VAT 10%</option>
                                            <option value="15">VAT 15%</option>
                                            <option value="18">VAT 18%</option>
                                        </select>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <span class="text-sm font-semibold text-gray-900 line-amount">${{ number_format($recItem->received_qty * $orderPrice, 2) }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg">
                                            <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr class="border-b border-gray-200">
                                <td class="px-4 py-3 text-sm text-gray-600">1</td>
                                <td class="px-4 py-3">
                                    <select name="items[0][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateItemPrice(this)">
                                        <option value="">Select Item</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}">
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][quantity]" value="1" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[0][unit_price]" value="0.00" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                </td>
                                <td class="px-4 py-3">
                                    <select name="items[0][tax_rate]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                        <option value="0">No Tax</option>
                                        <option value="10">VAT 10%</option>
                                        <option value="15">VAT 15%</option>
                                        <option value="18">VAT 18%</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-sm font-semibold text-gray-900 line-amount">$0.00</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- Totals Section -->
            <div class="mt-6 flex justify-end">
                <div class="w-full md:w-96 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Subtotal:</span>
                        <span class="text-sm font-semibold text-gray-900" id="subtotal">$0.00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Tax Amount:</span>
                        <span class="text-sm font-semibold text-gray-900" id="tax">$0.00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-sm text-gray-600">Discount:</label>
                        <div class="flex items-center gap-2">
                            <input type="number" name="discount_val" value="0" min="0" step="0.01" class="w-24 px-3 py-1.5 border border-gray-300 rounded-lg text-sm text-right focus:outline-none focus:ring-2 focus:ring-[#28A375]" id="discount" onchange="calculateTotals()">
                            <select name="discount_type" class="px-2 py-1.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" id="discountType" onchange="calculateTotals()">
                                <option value="amount">$</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                    </div>
                    <div class="pt-4 border-t-2 border-dashed border-gray-100 flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900 uppercase tracking-tight">Total Amount:</span>
                        <div class="flex flex-col items-end">
                            <span class="text-3xl font-black text-[#28A375] tracking-tighter" id="total">$0.00</span>
                            <div class="h-1 w-full bg-[#28A375]/10 mt-1 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Notes -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Notes</h2>
                </div>
                <textarea name="notes" rows="4" placeholder="Add any additional notes or special instructions..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] resize-none transition-all placeholder:text-gray-400"></textarea>
            </div>

            <!-- Attachments -->
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i data-lucide="paperclip" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Attachments</h2>
                </div>
                <div class="border-2 border-dashed border-gray-200 rounded-xl p-8 text-center hover:border-[#28A375] hover:bg-green-50/30 transition-all cursor-pointer group">
                    <div class="w-12 h-12 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3 group-hover:bg-white transition-all">
                        <i data-lucide="upload-cloud" class="w-6 h-6 text-gray-400 group-hover:text-[#28A375]"></i>
                    </div>
                    <p class="text-sm font-semibold text-gray-700 mb-1">Upload vendor invoice</p>
                    <p class="text-xs text-gray-500">PDF, JPG or PNG (Max 10MB)</p>
                    <input type="file" name="attachments[]" class="hidden" multiple id="fileInput">
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('purchases.bills.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" name="action" value="draft" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Save as Draft
            </button>
            <button type="submit" name="action" value="save" class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                Save & Record Bill
            </button>
        </div>
    </div>
</form>

{{-- ADD VENDOR MODAL --}}
<div x-show="showAdd" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showAdd = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div
            class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full border border-gray-100">
            <div
                class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900">Add New Vendor</h3>
                <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x"
                        class="w-6 h-6"></i></button>
            </div>
            <form action="{{ route('contacts.vendors.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar">
                    {{-- Vendor Type --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Vendor Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                :class="addForm.type === 'individual' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="type" value="individual" x-model="addForm.type"
                                        class="w-4 h-4 text-[#28A375] focus:ring-[#28A375]">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Individual</p>
                                        <p class="text-xs text-gray-500">Sole proprietor/Freelancer</p>
                                    </div>
                                </div>
                            </label>
                            <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                :class="addForm.type === 'company' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="type" value="company" x-model="addForm.type"
                                        class="w-4 h-4 text-[#28A375] focus:ring-[#28A375]">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">Company</p>
                                        <p class="text-xs text-gray-500">Business/Corporation</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    {{-- Basic Info --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Company/Vendor Name <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" required placeholder="Enter vendor name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                            <input type="email" name="email" placeholder="vendor@example.com"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                            <input type="tel" name="phone" placeholder="+1 (555) 000-0000"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                        </div>
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-4 pt-4 border-t border-gray-50">
                    <button type="submit" :disabled="isSaving" @click="isSaving = true"
                        class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                        <span x-show="!isSaving" class="flex items-center gap-2"><i data-lucide="save"
                                class="w-4 h-4"></i> Save Vendor</span>
                        <span x-show="isSaving" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                    <button type="button" @click="showAdd = false"
                        class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function updateItemPrice(select) {
        const row = select.closest('tr');
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price') || '0.00';
        const priceInput = row.querySelectorAll('input[type="number"]')[1];
        if (priceInput) {
            priceInput.value = parseFloat(price).toFixed(2);
        }
        calculateLineTotal(select);
    }

    function toggleSubmenu(id) {
        const submenu = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        
        if (submenu) {
            if (submenu.classList.contains('hidden')) {
                submenu.classList.remove('hidden');
                icon.setAttribute('data-lucide', 'chevron-up');
            } else {
                submenu.classList.add('hidden');
                icon.setAttribute('data-lucide', 'chevron-down');
            }
            lucide.createIcons();
        }
    }

    let lineItemCounter = {{ isset($selectedReceipt) ? $selectedReceipt->items->count() : 1 }};
    
    window.onload = function() {
        if({{ isset($selectedReceipt) ? 'true' : 'false' }}) {
            calculateTotals();
        }
    };

    function addLineItem() {
        lineItemCounter++;
        const tbody = document.getElementById('lineItemsBody');
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-200';
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-600">${lineItemCounter}</td>
            <td class="px-4 py-3">
                <select name="items[${lineItemCounter-1}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateItemPrice(this)">
                    <option value="">Select Item</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}">
                            {{ $item->name }}
                        </option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${lineItemCounter-1}][quantity]" value="1" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${lineItemCounter-1}][unit_price]" value="0.00" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
            </td>
            <td class="px-4 py-3">
                <select name="items[${lineItemCounter-1}][tax_rate]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                    <option value="0">No Tax</option>
                    <option value="10">VAT 10%</option>
                    <option value="15">VAT 15%</option>
                    <option value="18">VAT 18%</option>
                </select>
            </td>
            <td class="px-4 py-3 text-right">
                <span class="text-sm font-semibold text-gray-900 line-amount">$0.00</span>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg">
                    <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
        lucide.createIcons();
        calculateTotals();
    }

    function removeLineItem(button) {
        const row = button.closest('tr');
        if (document.querySelectorAll('#lineItemsBody tr').length > 1) {
            row.remove();
            updateLineNumbersAndNames();
            calculateTotals();
        }
    }

    function updateLineNumbersAndNames() {
        const rows = document.querySelectorAll('#lineItemsBody tr');
        rows.forEach((row, index) => {
            row.querySelector('td:first-child').textContent = index + 1;
            
            // Update names for backend indexing
            const productIdSelect = row.querySelector('select[name^="items"]');
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const priceInput = row.querySelector('input[name$="[unit_price]"]');
            const taxSelect = row.querySelector('select[name$="[tax_rate]"]');
            
            if (productIdSelect) productIdSelect.name = `items[${index}][item_id]`;
            if (qtyInput) qtyInput.name = `items[${index}][quantity]`;
            if (priceInput) priceInput.name = `items[${index}][unit_price]`;
            if (taxSelect) taxSelect.name = `items[${index}][tax_rate]`;
        });
        lineItemCounter = rows.length;
    }

    function calculateLineTotal(input) {
        const row = input.closest('tr');
        const qty = parseFloat(row.querySelector('input[name$="[quantity]"]').value) || 0;
        const rate = parseFloat(row.querySelector('input[name$="[unit_price]"]').value) || 0;
        const taxRate = parseFloat(row.querySelector('select[name$="[tax_rate]"]').value) || 0;
        
        const lineSubtotal = qty * rate;
        const lineTax = lineSubtotal * (taxRate / 100);
        const total = lineSubtotal + lineTax;
        
        row.querySelector('.line-amount').textContent = window.ERP_CONFIG.currency_symbol + total.toFixed(window.ERP_CONFIG.decimal_precision);
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        
        const rows = document.querySelectorAll('#lineItemsBody tr');
        rows.forEach(row => {
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const rateInput = row.querySelector('input[name$="[unit_price]"]');
            const taxSelect = row.querySelector('select[name$="[tax_rate]"]');
            
            if (qtyInput && rateInput && taxSelect) {
                const qty = parseFloat(qtyInput.value) || 0;
                const rate = parseFloat(rateInput.value) || 0;
                const taxRate = parseFloat(taxSelect.value) || 0;
                
                const lineSubtotal = qty * rate;
                const lineTax = lineSubtotal * (taxRate / 100);
                
                subtotal += lineSubtotal;
                totalTax += lineTax;
            }
        });
        
        const discountInput = document.getElementById('discount');
        const discountTypeSelect = document.getElementById('discountType');
        
        const discount = parseFloat(discountInput.value) || 0;
        const discountType = discountTypeSelect.value;
        
        let discountAmount = 0;
        if (discountType === 'percent') {
            discountAmount = (subtotal + totalTax) * (discount / 100);
        } else {
            discountAmount = discount;
        }
        
        const total = subtotal + totalTax - discountAmount;
        
        document.getElementById('subtotal').textContent = window.ERP_CONFIG.currency_symbol + subtotal.toFixed(window.ERP_CONFIG.decimal_precision);
        document.getElementById('tax').textContent = window.ERP_CONFIG.currency_symbol + totalTax.toFixed(window.ERP_CONFIG.decimal_precision);
        document.getElementById('total').textContent = window.ERP_CONFIG.currency_symbol + Math.max(0, total).toFixed(window.ERP_CONFIG.decimal_precision);
    }

    // Initialize calculations on page load
    document.addEventListener('DOMContentLoaded', function() {
        calculateTotals();
    });
</script>
@endpush

