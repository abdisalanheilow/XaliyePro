@extends('admin.admin_master')

@section('title', 'New Sales Invoice - XaliyePro')

@section('admin')
<div x-data="{ 
    showAdd: false, 
    addForm: { type: 'individual' }, 
    isSaving: false,
    discount: 0,
    discountType: 'fixed'
}">
<form action="{{ route('sales.invoices.store') }}" method="POST" id="salesInvoiceForm" @submit="isSaving = true">
    @csrf
    <div class="space-y-6">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('sales.invoices.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">New Sales Invoice</h1>
                </div>
                <p class="text-sm text-gray-500">Record a sale and generate a bill for a customer</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" name="action" value="draft" :disabled="isSaving" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all active:scale-95 shadow-sm disabled:opacity-50">
                    Save as Draft
                </button>
                <button type="submit" name="action" value="save" :disabled="isSaving" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95 disabled:opacity-50">
                    <template x-if="!isSaving">
                        <div class="flex items-center gap-2">
                            <i data-lucide="check" class="w-4 h-4"></i>
                            Create Invoice
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

        <!-- Primary Invoice Details -->
        <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm mb-6" x-data="{
            selectedOrder: {{ $selectedOrder ? $selectedOrder->id : 'null' }},
            selectedCustomer: '{{ $selectedOrder ? $selectedOrder->customer_id : '' }}',
            selectedBranch: '{{ $selectedOrder ? $selectedOrder->branch_id : '' }}'
        }">
            <div class="flex items-center gap-2 mb-8">
                <div class="p-2 bg-green-50 rounded-lg">
                    <i data-lucide="file-text" class="w-5 h-5 text-[#28A375]"></i>
                </div>
                <h2 class="text-lg font-bold text-gray-900">Invoice Information</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                <!-- Row 1 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Customer <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="customer_id" x-model="selectedCustomer" required class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                            <option value="">Select Customer</option>
                            @foreach ($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="showAdd = true" class="p-2.5 bg-gray-50 border border-gray-300 rounded-lg hover:bg-white hover:text-[#28A375] hover:border-[#28A375] transition-all group shadow-sm">
                            <i data-lucide="plus" class="w-4 h-4 text-gray-400 group-hover:text-[#28A375]"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Invoice Number <span class="text-red-500">*</span></label>
                    <input type="text" name="invoice_no" value="{{ $invoiceNo }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600 focus:outline-none" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Related Order</label>
                    <select name="sales_order_id" x-model="selectedOrder" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                        <option value="">Standalone Invoice</option>
                        @foreach ($orders as $order)
                            <option value="{{ $order->id }}">{{ $order->order_no }} ({{ $order->customer->name }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Row 2 -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Invoice Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="invoice_date" value="{{ date('Y-m-d') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Due Date</label>
                    <div class="relative">
                        <i data-lucide="clock" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="due_date" value="{{ date('Y-m-d', strtotime('+30 days')) }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Branch</label>
                    <select name="branch_id" x-model="selectedBranch" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                        <option value="">General / None</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-2">
                    <div class="p-2 bg-green-50 rounded-lg">
                        <i data-lucide="shopping-cart" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Billed Items</h2>
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
                        @if ($selectedOrder)
                            @foreach ($selectedOrder->items as $index => $orderItem)
                            <tr class="border-b border-gray-100">
                                <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <select name="items[{{ $index }}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                                        <option value="">Select Item</option>
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}" {{ $orderItem->item_id == $item->id ? 'selected' : '' }}>
                                                {{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $index }}][quantity]" value="{{ $orderItem->quantity }}" min="1" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                </td>
                                <td class="px-4 py-3">
                                    <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $orderItem->unit_price }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                </td>
                                <td class="px-4 py-3">
                                    <select name="items[{{ $index }}][tax_rate]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                        <option value="0">No Tax</option>
                                        <option value="10" {{ $orderItem->tax_rate == 10 ? 'selected' : '' }}>VAT 10%</option>
                                        <option value="15" {{ $orderItem->tax_rate == 15 ? 'selected' : '' }}>VAT 15%</option>
                                    </select>
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <span class="text-sm font-bold text-gray-900 line-amount tracking-tight">${{ number_format($orderItem->amount, 2) }}</span>
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
                                        @foreach ($items as $item)
                                            <option value="{{ $item->id }}" data-price="{{ $item->selling_price }}">
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
                        <i data-lucide="file-check" class="w-5 h-5 text-[#28A375]"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Notes & Payment Terms</h2>
                </div>
                <textarea name="terms" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] outline-none transition-all placeholder:text-gray-400" placeholder="Enter payment instructions, bank details, or terms..."></textarea>
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
                
                <div class="flex items-center gap-6 py-2">
                    <div class="flex-1">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Invoice Discount</label>
                        <div class="flex gap-2">
                            <input type="number" id="discount" name="discount_amount" x-model="discount" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" placeholder="0.00" oninput="calculateTotals()">
                            <select id="discountType" name="discount_type" x-model="discountType" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] font-bold" onchange="calculateTotals()">
                                <option value="fixed">$</option>
                                <option value="percent">%</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between py-6 border-t-2 border-dashed border-gray-100">
                    <span class="text-lg font-black text-gray-900 uppercase tracking-tighter">Grand Total</span>
                    <div class="flex flex-col items-end">
                        <span id="total" class="text-4xl font-black text-[#28A375] tracking-tighter">$0.00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ADD CUSTOMER MODAL --}}
<div x-show="showAdd" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showAdd = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full border border-gray-100">
            <div class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900">Add New Customer</h3>
                <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            <form action="{{ route('contacts.customers.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar text-sm">
                    <div>
                        <label class="block font-medium text-gray-700 mb-1">Customer Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none">
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-4 pt-4 border-t border-gray-50">
                    <button type="submit" class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg font-bold hover:bg-[#229967] transition-all">Save Customer</button>
                    <button type="button" @click="showAdd = false" class="flex-1 px-6 py-3 border border-gray-200 rounded-lg font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
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

    let lineItemCounter = {{ $selectedOrder ? $selectedOrder->items->count() : 1 }};

    window.onload = function() {
        if({{ $selectedOrder ? 'true' : 'false' }}) {
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
        
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('tax').textContent = '$' + totalTax.toFixed(2);
        document.getElementById('total').textContent = '$' + Math.max(0, total).toFixed(2);
    }
</script>
@endpush
