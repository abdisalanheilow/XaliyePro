@extends('admin.admin_master')

@section('title', 'New Purchase Return - XaliyePro')

@section('admin')
<div x-data="{ showAdd: false, addForm: { type: 'individual' }, isSaving: false }">
<form action="{{ route('purchases.returns.store') }}" method="POST" id="purchaseReturnForm" enctype="multipart/form-data" @submit="isSaving = true">
    @csrf
    <div class="space-y-6">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('purchases.returns.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">New Purchase Return</h1>
                </div>
                <p class="text-sm text-gray-500">Record a return of physical products to a vendor</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.returns.index') }}" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                    Cancel
                </a>
                <button type="submit" :disabled="isSaving" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2 transition-all active:scale-95 shadow-sm disabled:opacity-50">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Save & Record Return
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
                    <i data-lucide="rotate-ccw" class="w-5 h-5 text-[#28A375]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Return Details</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                <!-- Row 1 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Vendor <span class="text-red-500">*</span>
                    </label>
                    <select name="vendor_id" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Select Vendor</option>
                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Return Number <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="return_no" value="{{ $returnNo }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600 focus:outline-none" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Link to Purchase Bill (Optional)</label>
                    <select name="purchase_bill_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option value="">Independent Return</option>
                        @foreach ($bills as $bill)
                            <option value="{{ $bill->id }}">{{ $bill->bill_no }} ({{ $bill->vendor->name }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">
                        Return Date <span class="text-red-500">*</span>
                    </label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reference #</label>
                    <div class="relative">
                        <i data-lucide="hash" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="text" name="reference_no" placeholder="Enter reference number" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Branch</label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <select name="branch_id" x-model="selectedBranch" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                            <option value="">Select Branch</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Row 3 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Store / Warehouse</label>
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
            </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white rounded-lg border border-gray-200 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Items to Return</h2>
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
                        <!-- Line Item Row 1 -->
                        <tr class="border-b border-gray-200">
                            <td class="px-4 py-3 text-sm text-gray-600">1</td>
                            <td class="px-4 py-3">
                                <select name="items[0][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                                    <option value="">Select Product</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}">
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[0][quantity]" value="1" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
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
                    <div class="pt-4 border-t-2 border-dashed border-gray-100 flex items-center justify-between">
                        <span class="text-base font-bold text-gray-900 uppercase tracking-tight">Grand Total:</span>
                        <div class="flex flex-col items-end">
                            <span class="text-3xl font-black text-[#28A375] tracking-tighter" id="total">$0.00</span>
                            <div class="h-1 w-full bg-[#28A375]/10 mt-1 rounded-full"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm mb-6">
            <div class="flex items-center gap-2 mb-4">
                <div class="p-2 bg-green-50 rounded-lg">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#28A375]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Notes / Reason for Return</h2>
            </div>
            <textarea name="notes" rows="4" placeholder="Enter reason for return or any special instructions..." class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] resize-none transition-all placeholder:text-gray-400"></textarea>
        </div>

        <!-- Form Actions -->
        <div class="flex items-center justify-end gap-3">
            <a href="{{ route('purchases.returns.index') }}" class="px-6 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] inline-flex items-center gap-2">
                <i data-lucide="check" class="w-4 h-4"></i>
                Save Return
            </button>
        </div>
    </div>
</form>
</div>
@endsection

@push('scripts')
<script>
    lucide.createIcons();

    function updateProductPrice(select) {
        const row = select.closest('tr');
        const option = select.options[select.selectedIndex];
        const price = option.getAttribute('data-price') || '0.00';
        const priceInput = row.querySelectorAll('input[type="number"]')[1];
        if (priceInput) {
            priceInput.value = parseFloat(price).toFixed(2);
        }
        calculateLineTotal(select);
    }

    let lineItemCounter = 1;

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
                        <option value="{{ $item->id }}" data-price="{{ $item->cost_price }}">
                            {{ $item->name }}
                        </option>
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
        row.querySelector('.line-amount').textContent = '$' + total.toFixed(2);
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
        const total = subtotal + totalTax;
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('tax').textContent = '$' + totalTax.toFixed(2);
        document.getElementById('total').textContent = '$' + total.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateTotals();
    });
</script>
@endpush
