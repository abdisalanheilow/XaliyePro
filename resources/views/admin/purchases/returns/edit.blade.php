@extends('admin.admin_master')

@section('title', 'Edit Purchase Return - XaliyePro')

@section('admin')
<div x-data="{ isSaving: false }">
<form action="{{ route('purchases.returns.update', $return->id) }}" method="POST" id="purchaseReturnForm" enctype="multipart/form-data" @submit="isSaving = true">
    @csrf
    @method('PUT')
    <div class="space-y-6">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('purchases.returns.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Return: {{ $return->return_no }}</h1>
                </div>
                <p class="text-sm text-gray-500">Modify the details of this purchase return</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.returns.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" :disabled="isSaving" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm disabled:opacity-50">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Update & Save
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

        <!-- Return Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm overflow-hidden" x-data="{ 
            selectedBranch: '{{ $return->branch_id }}',
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
                    <i data-lucide="rotate-ccw" class="w-5 h-5 text-[#28A375]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Return Details</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Vendor <span class="text-red-500">*</span></label>
                    <select name="vendor_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Select Vendor</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}" {{ $return->vendor_id == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Return Number <span class="text-red-500">*</span></label>
                    <input type="text" value="{{ $return->return_no }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600 focus:outline-none" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link to Bill (Optional)</label>
                    <select name="purchase_bill_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Independent Return</option>
                        @foreach ($bills as $bill)
                            <option value="{{ $bill->id }}" {{ $return->purchase_bill_id == $bill->id ? 'selected' : '' }}>{{ $bill->bill_no }} ({{ $bill->vendor->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Return Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="return_date" value="{{ $return->return_date->format('Y-m-d') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reference #</label>
                    <input type="text" name="reference_no" value="{{ $return->reference_no }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Branch</label>
                    <select name="branch_id" x-model="selectedBranch" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Select Branch</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Store / Warehouse</label>
                    <select name="store_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Select Store</option>
                        <template x-for="store in filteredStores()" :key="store.id">
                            <option :value="store.id" x-text="store.name" :selected="store.id == '{{ $return->store_id }}'"></option>
                        </template>
                    </select>
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
                    Add Product
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
                        @foreach ($return->items as $index => $rItem)
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <select name="items[{{ $index }}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                                    <option value="">Select Product</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}" {{ $rItem->item_id == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $rItem->quantity }}" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $rItem->unit_price }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                            </td>
                            <td class="px-4 py-3">
                                @php
                                    $taxRate = ($rItem->tax_amount > 0 && $rItem->amount > 0) ? round(($rItem->tax_amount / ($rItem->amount - $rItem->tax_amount)) * 100) : 0;
                                @endphp
                                <select name="items[{{ $index }}][tax_rate]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                    <option value="0" {{ $taxRate == 0 ? 'selected' : '' }}>No Tax</option>
                                    <option value="10" {{ $taxRate == 10 ? 'selected' : '' }}>VAT 10%</option>
                                    <option value="15" {{ $taxRate == 15 ? 'selected' : '' }}>VAT 15%</option>
                                    <option value="18" {{ $taxRate == 18 ? 'selected' : '' }}>VAT 18%</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-gray-900 line-amount">${{ number_format($rItem->amount, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-red-600"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-6 flex justify-end">
                <div class="w-full md:w-96 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Subtotal:</span>
                        <span class="text-sm font-semibold text-gray-900" id="subtotal">${{ number_format($return->total_amount, 2) }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">Tax Amount:</span>
                        <span class="text-sm font-semibold text-gray-900" id="tax">${{ number_format($return->tax_amount, 2) }}</span>
                    </div>
                    <div class="pt-4 border-t-2 border-dashed border-gray-100 flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900 uppercase tracking-tight">Grand Total:</span>
                        <span class="text-3xl font-black text-[#28A375] tracking-tighter" id="total">${{ number_format($return->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Notes -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-6">
            <label class="block text-sm font-bold text-gray-900 mb-2">Notes / Reason for Return</label>
            <textarea name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] resize-none transition-all">{{ $return->notes }}</textarea>
        </div>

        <div class="flex justify-end gap-3">
            <a href="{{ route('purchases.returns.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">Cancel</a>
            <button type="submit" class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                Update Return
            </button>
        </div>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();
    // Re-use logic from create.blade.php
    function updateProductPrice(select) {
        const row = select.closest('tr');
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price') || '0.00';
        const priceInput = row.querySelectorAll('input[type="number"]')[1];
        if (priceInput) priceInput.value = parseFloat(price).toFixed(2);
        calculateLineTotal(select);
    }
    let lineItemCounter = {{ count($return->items) }};
    function addLineItem() {
        lineItemCounter++;
        const tbody = document.getElementById('lineItemsBody');
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-200';
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-600">${lineItemCounter}</td>
            <td class="px-4 py-3">
                <select name="items[${lineItemCounter-1}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                    <option value="">Select Product</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}">{{ $item->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-4 py-3">
                <input type="number" name="items[${lineItemCounter-1}][quantity]" value="1" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
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
        const total = (qty * rate) + ((qty * rate) * (taxRate / 100));
        row.querySelector('.line-amount').textContent = '$' + total.toFixed(2);
        calculateTotals();
    }
    function calculateTotals() {
        let subtotal = 0, totalTax = 0;
        const rows = document.querySelectorAll('#lineItemsBody tr');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('input[name$="[quantity]"]').value) || 0;
            const rate = parseFloat(row.querySelector('input[name$="[unit_price]"]').value) || 0;
            const taxRate = parseFloat(row.querySelector('select[name$="[tax_rate]"]').value) || 0;
            subtotal += (qty * rate);
            totalTax += ((qty * rate) * (taxRate / 100));
        });
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('tax').textContent = '$' + totalTax.toFixed(2);
        document.getElementById('total').textContent = '$' + (subtotal + totalTax).toFixed(2);
    }
</script>
@endpush
