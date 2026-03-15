@extends('admin.admin_master')

@section('title', 'New Sales Return - XaliyePro')

@section('admin')
<div x-data="{ 
    isSaving: false,
    selectedCustomer: '{{ $selectedInvoice ? $selectedInvoice->customer_id : '' }}',
    selectedInvoice: '{{ $selectedInvoice ? $selectedInvoice->id : '' }}',
    items: [
        @foreach ($items as $item)
        { id: '{{ $item->id }}', name: '{{ $item->name }}', price: '{{ $item->selling_price }}' },
        @endforeach
    ]
}">
<form action="{{ route('sales.returns.store') }}" method="POST" id="salesReturnForm" @submit="isSaving = true">
    @csrf
    <div class="space-y-6">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('sales.returns.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">New Sales Return</h1>
                </div>
                <p class="text-sm text-gray-500">Process a customer return and issue a credit note</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" :disabled="isSaving" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95 disabled:opacity-50">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Post Return
                        </div>
                    </template>
                    <template x-if="isSaving">
                        <div class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Posting...
                        </div>
                    </template>
                </button>
            </div>
        </div>

        <!-- Primary Return Details -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm mb-6">
            <div class="flex items-center gap-2 mb-8">
                <div class="p-2 bg-green-50 rounded-lg">
                    <i data-lucide="rotate-ccw" class="w-5 h-5 text-[#28A375]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Return Information</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Customer <span class="text-red-500">*</span></label>
                    <select name="customer_id" x-model="selectedCustomer" required class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                        <option value="">Select Customer</option>
                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Return Number</label>
                    <input type="text" name="return_no" value="{{ $returnNo }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600 focus:outline-none" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Against Invoice</label>
                    <select name="sales_invoice_id" x-model="selectedInvoice" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                        <option value="">Standalone Return</option>
                        @foreach ($invoices as $invoice)
                            <option value="{{ $invoice->id }}">{{ $invoice->invoice_no }} ({{ $invoice->customer->name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Return Date <span class="text-red-500">*</span></label>
                    <input type="date" name="return_date" value="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Branch</label>
                    <select name="branch_id" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                        <option value="">General / None</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}" {{ $selectedInvoice && $selectedInvoice->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Reason for Return</label>
                    <select name="reason" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                        <option value="Damaged">Damaged</option>
                        <option value="Incorrect Item">Incorrect Item</option>
                        <option value="Customer Satisfied">Not Satisfied</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i data-lucide="package-search" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Returned Items</h2>
                </div>
                <button type="button" onclick="addLineItem()" class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Item
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Item Name</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Quantity</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Price</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Tax</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-40 text-right">Amount</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-16 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="lineItemsBody">
                        @if ($selectedInvoice)
                            @foreach ($selectedInvoice->items as $index => $invItem)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <select name="items[{{ $index }}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                                        <option value="">Select Item</option>
                                        <template x-for="it in items" :key="it.id">
                                            <option :value="it.id" :selected="it.id == {{ $invItem->item_id }}" x-text="it.name" :data-price="it.price"></option>
                                        </template>
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ $invItem->quantity }}" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $invItem->unit_price }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                </td>
                                <td class="px-4 py-3">
                                    <select name="items[{{ $index }}][tax_rate]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                        <option value="0">No Tax</option>
                                        <option value="10" {{ $invItem->tax_rate == 10 ? 'selected' : '' }}>VAT 10%</option>
                                        <option value="15" {{ $invItem->tax_rate == 15 ? 'selected' : '' }}>VAT 15%</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-sm font-bold text-gray-900 line-amount tracking-tight">${{ number_format($invItem->amount, 2) }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg transition-all group">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-gray-400 group-hover:text-red-500"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        @else
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3 text-sm text-gray-600">1</td>
                                <td class="px-4 py-3">
                                    <select name="items[0][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                                        <option value="">Select Item</option>
                                        <template x-for="it in items" :key="it.id">
                                            <option :value="it.id" x-text="it.name" :data-price="it.price"></option>
                                        </template>
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
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-sm font-bold text-gray-900 line-amount tracking-tight">$0.00</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg transition-all group">
                                        <i data-lucide="trash-2" class="w-4 h-4 text-gray-400 group-hover:text-red-500"></i>
                                    </button>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary & Totals -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-xl border border-gray-200 p-6 shadow-sm">
                <div class="flex items-center gap-2 mb-4">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i data-lucide="file-text" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Notes & Internal Remarks</h2>
                </div>
                <textarea name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] outline-none transition-all placeholder:text-gray-400" placeholder="Describe the reason for return or any special instructions..."></textarea>
            </div>

            <div class="bg-white rounded-xl border border-gray-200 p-8 space-y-5 shadow-sm">
                <div class="flex items-center justify-between text-gray-600">
                    <span class="text-sm font-semibold uppercase tracking-wider">Subtotal</span>
                    <span id="subtotal" class="text-lg font-black text-gray-900">$0.00</span>
                </div>
                <div class="flex items-center justify-between text-gray-600 pb-5 border-b border-gray-100">
                    <span class="text-sm font-semibold uppercase tracking-wider">Tax Amount</span>
                    <span id="tax" class="text-lg font-black text-gray-900">$0.00</span>
                </div>
                
                <div class="flex items-center justify-between py-6">
                    <span class="text-lg font-black text-gray-900 uppercase tracking-tighter text-red-600">Total Credit</span>
                    <div class="flex flex-col items-end">
                        <span id="total" class="text-4xl font-black text-red-600 tracking-tighter">$0.00</span>
                    </div>
                </div>
            </div>
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

    let lineItemCounter = {{ $selectedInvoice ? $selectedInvoice->items->count() : 1 }};

    window.onload = function() {
        if({{ $selectedInvoice ? 'true' : 'false' }}) {
            calculateTotals();
        }
    };

    function addLineItem() {
        lineItemCounter++;
        const tbody = document.getElementById('lineItemsBody');
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-100';
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-600">${lineItemCounter}</td>
            <td class="px-4 py-3">
                <select name="items[${lineItemCounter-1}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                    <option value="">Select Item</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}">
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
                </select>
            </td>
            <td class="px-4 py-3 text-right">
                <span class="text-sm font-bold text-gray-900 line-amount tracking-tight">$0.00</span>
            </td>
            <td class="px-4 py-3 text-center">
                <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg transition-all group">
                    <i data-lucide="trash-2" class="w-4 h-4 text-gray-400 group-hover:text-red-500"></i>
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
            calculateTotals();
        }
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
</script>
@endpush
