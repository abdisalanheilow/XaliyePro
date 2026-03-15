@extends('admin.admin_master')

@section('title', 'Edit Purchase Order - XaliyePro')

@section('admin')
<div x-data="{ 
    showAdd: false, 
    addForm: { type: 'individual' }, 
    isSaving: false,
    discount: {{ $order->discount_amount ?? 0 }},
    discountType: 'fixed'
}">
<form action="{{ route('purchases.orders.update', $order->id) }}" method="POST" id="purchaseOrderForm">
    @csrf
    @method('PUT')
    <div class="space-y-6">
        <!-- Page Title -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('purchases.orders.index') }}" class="text-gray-500 hover:text-gray-700">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Order #{{ $order->order_no }}</h1>
                </div>
                <p class="text-sm text-gray-500">Update procurement details</p>
            </div>
            <div class="flex items-center gap-3">
                <button type="submit" name="action" value="draft" class="px-4 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all active:scale-95 shadow-sm">
                    Keep as Draft
                </button>
                <button type="submit" name="action" value="save" class="px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2 shadow-sm active:scale-95">
                    <i data-lucide="check" class="w-4 h-4"></i>
                    Update Order
                </button>
            </div>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-8 shadow-sm mb-6" x-data="{ 
            selectedBranch: '{{ $order->branch_id }}',
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
                <h2 class="text-lg font-bold text-gray-900">Order Information</h2>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                <!-- Row 1: Primary Identifiers -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Vendor <span class="text-red-500">*</span></label>
                    <div class="flex gap-2">
                        <select name="vendor_id" required class="flex-1 px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                            <option value="">Select Vendor</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}" {{ $order->vendor_id == $vendor->id ? 'selected' : '' }}>{{ $vendor->name }}</option>
                            @endforeach
                        </select>
                        <button type="button" @click="showAdd = true" class="p-2.5 bg-gray-50 border border-gray-300 rounded-lg hover:bg-white hover:text-[#28A375] hover:border-[#28A375] transition-all group shadow-sm">
                            <i data-lucide="plus" class="w-4 h-4 text-gray-400 group-hover:text-[#28A375]"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Order Number <span class="text-red-500">*</span></label>
                    <input type="text" name="order_no" value="{{ $order->order_no }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-300 rounded-lg text-sm font-bold text-gray-600 focus:outline-none cursor-not-allowed" readonly>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Internal Reference</label>
                    <div class="relative group">
                        <i data-lucide="hash" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 transition-colors"></i>
                        <input type="text" name="reference_no" value="{{ $order->reference_no }}" placeholder="Enter tracking code" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                    </div>
                </div>

                <!-- Row 2: Dates & Terms -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Order Date <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <i data-lucide="calendar" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="order_date" required value="{{ $order->order_date->format('Y-m-d') }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Expected Delivery</label>
                    <div class="relative">
                        <i data-lucide="truck" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <input type="date" name="expected_date" value="{{ $order->expected_date ? $order->expected_date->format('Y-m-d') : '' }}" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Terms</label>
                    <select name="payment_terms" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                        <option value="net_15" {{ $order->payment_terms == 'net_15' ? 'selected' : '' }}>Net 15</option>
                        <option value="net_30" {{ $order->payment_terms == 'net_30' ? 'selected' : '' }}>Net 30</option>
                        <option value="net_45" {{ $order->payment_terms == 'net_45' ? 'selected' : '' }}>Net 45</option>
                        <option value="due_on_receipt" {{ $order->payment_terms == 'due_on_receipt' ? 'selected' : '' }}>On Receipt</option>
                    </select>
                </div>

                <!-- Row 3: Destination -->
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Branch (Optional)</label>
                    <div class="relative">
                        <i data-lucide="map-pin" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <select name="branch_id" x-model="selectedBranch" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                            <option value="">General / None</option>
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}" {{ $order->branch_id == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Store / Warehouse (Optional)</label>
                    <div class="relative">
                        <i data-lucide="archive" class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                        <select name="store_id" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white">
                            <option value="">Main Store / Default</option>
                            <template x-for="store in filteredStores()" :key="store.id">
                                <option :value="store.id" x-text="store.name" :selected="store.id == {{ $order->store_id ?? 'null' }}"></option>
                            </template>
                        </select>
                    </div>
                </div>

                <div class="flex items-end pb-1">
                    <p class="text-xs text-gray-400 italic">Review changes before updating the order.</p>
                </div>
            </div>
        </div>

        <!-- Line Items -->
        <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden mb-6">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Requested Items</h2>
                <button type="button" onclick="addLineItem()" class="px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-all flex items-center gap-2">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Item
                </button>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-12">#</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Product Name</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-32">Quantity</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Rate</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-40">Tax</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-40 text-right">Amount</th>
                            <th class="px-4 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider w-16 text-center"></th>
                        </tr>
                    </thead>
                    <tbody id="lineItemsBody">
                        @foreach ($order->items as $index => $item)
                        <tr class="border-b border-gray-100">
                            <td class="px-4 py-3 text-sm text-gray-600">{{ $index + 1 }}</td>
                            <td class="px-4 py-3">
                                <select name="items[{{ $index }}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                                    <option value="">Select Product</option>
                                    @foreach ($items as $item_opt)
                                        <option value="{{ $item_opt->id }}" data-price="{{ $item_opt->cost_price }}" {{ $item->item_id == $item_opt->id ? 'selected' : '' }}>
                                            {{ $item_opt->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $index }}][quantity]" value="{{ $item->quantity }}" min="0.01" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                            </td>
                            <td class="px-4 py-3">
                                <input type="number" name="items[{{ $index }}][unit_price]" value="{{ $item->unit_price }}" min="0" step="0.01" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                            </td>
                            <td class="px-4 py-3">
                                <select name="items[{{ $index }}][tax_rate]" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="calculateLineTotal(this)">
                                    <option value="0" {{ $item->tax_amount == 0 ? 'selected' : '' }}>No Tax</option>
                                    <option value="10" {{ abs(($item->tax_amount / ($item->amount - $item->tax_amount)) * 100 - 10) < 0.1 ? 'selected' : '' }}>VAT 10%</option>
                                    <option value="15" {{ abs(($item->tax_amount / ($item->amount - $item->tax_amount)) * 100 - 15) < 0.1 ? 'selected' : '' }}>VAT 15%</option>
                                    <option value="18" {{ abs(($item->tax_amount / ($item->amount - $item->tax_amount)) * 100 - 18) < 0.1 ? 'selected' : '' }}>VAT 18%</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-bold text-gray-900 line-amount tracking-tight">${{ number_format($item->amount, 2) }}</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button type="button" onclick="removeLineItem(this)" class="p-1.5 hover:bg-red-50 rounded-lg transition-all group">
                                    <i data-lucide="trash-2" class="w-4 h-4 text-gray-400 group-hover:text-red-500"></i>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Summary & Totals -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-lg border border-gray-200 p-6 h-min">
                <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Terms & Special Instructions</h2>
                <textarea name="notes" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] outline-none" placeholder="Enter any specific requirements for this order...">{{ $order->notes }}</textarea>
            </div>

            <div class="bg-white rounded-lg border border-gray-200 p-8 space-y-5 shadow-sm">
                <div class="flex items-center justify-between text-gray-600">
                    <span class="text-sm font-semibold uppercase tracking-wider">Subtotal</span>
                    <span id="subtotal" class="text-sm font-extrabold text-gray-900">${{ number_format($order->total_amount, 2) }}</span>
                </div>
                <div class="flex items-center justify-between text-gray-600 pb-5 border-b border-gray-100">
                    <span class="text-sm font-semibold uppercase tracking-wider">Tax Total</span>
                    <span id="tax" class="text-sm font-extrabold text-gray-900">${{ number_format($order->tax_amount, 2) }}</span>
                </div>
                
                <div class="flex items-center gap-6 py-2">
                    <div class="flex-1">
                        <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Global Discount</label>
                        <div class="flex gap-2">
                            <input type="number" id="discount" name="discount_val" x-model="discount" class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" placeholder="0.00" oninput="calculateTotals()">
                            <select id="discountType" name="discount_type" x-model="discountType" class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] font-bold" onchange="calculateTotals()">
                                <option value="fixed" {{ $order->discount_type == 'fixed' ? 'selected' : '' }}>$</option>
                                <option value="percent" {{ $order->discount_type == 'percent' ? 'selected' : '' }}>%</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-between py-6 border-t-2 border-dashed border-gray-100">
                    <span class="text-lg font-black text-gray-900 uppercase tracking-tighter">Grand Total</span>
                    <span id="total" class="text-3xl font-black text-[#28A375] border-b-4 border-[#28A375]/10">${{ number_format($order->grand_total, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- ADD VENDOR MODAL --}}
<div x-show="showAdd" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="showAdd = false"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>
        <div class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-3xl sm:w-full border border-gray-100">
            <div class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900">Add New Vendor</h3>
                <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600"><i data-lucide="x" class="w-6 h-6"></i></button>
            </div>
            <form action="{{ route('contacts.vendors.store') }}" method="POST">
                @csrf
                <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto custom-scrollbar text-sm">
                    {{-- Vendor Type --}}
                    <div>
                        <label class="block font-bold text-gray-700 mb-3">Vendor Type</label>
                        <div class="grid grid-cols-2 gap-4">
                            <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                :class="addForm.type === 'individual' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="type" value="individual" x-model="addForm.type"
                                        class="w-4 h-4 text-[#28A375] focus:ring-[#28A375]">
                                    <div>
                                        <p class="font-bold text-gray-900">Individual</p>
                                        <p class="text-xs text-gray-500">Sole proprietor</p>
                                    </div>
                                </div>
                            </label>
                            <label class="block p-4 border-2 rounded-xl cursor-pointer transition-all"
                                :class="addForm.type === 'company' ? 'border-[#28A375] bg-green-50' : 'border-gray-200 hover:border-gray-300'">
                                <div class="flex items-center gap-3">
                                    <input type="radio" name="type" value="company" x-model="addForm.type"
                                        class="w-4 h-4 text-[#28A375] focus:ring-[#28A375]">
                                    <div>
                                        <p class="font-bold text-gray-900">Company</p>
                                        <p class="text-xs text-gray-500">Business/Corp</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block font-medium text-gray-700 mb-1">Company/Vendor Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Email</label>
                            <input type="email" name="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none">
                        </div>
                        <div>
                            <label class="block font-medium text-gray-700 mb-1">Phone</label>
                            <input type="tel" name="phone" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#28A375] outline-none">
                        </div>
                    </div>
                </div>
                <div class="px-6 pb-6 flex gap-4 pt-4 border-t border-gray-50">
                    <button type="submit" class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg font-bold hover:bg-[#229967] transition-all">Save Vendor</button>
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

    let lineItemCounter = {{ count($order->items) }};

    function addLineItem() {
        lineItemCounter++;
        const tbody = document.getElementById('lineItemsBody');
        const row = document.createElement('tr');
        row.className = 'border-b border-gray-100';
        row.innerHTML = `
            <td class="px-4 py-3 text-sm text-gray-600">${lineItemCounter}</td>
            <td class="px-4 py-3">
                <select name="items[${lineItemCounter-1}][item_id]" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]" onchange="updateProductPrice(this)">
                    <option value="">Select Product</option>
                    @foreach ($items as $item_opt)
                        <option value="{{ $item_opt->id }}" data-price="{{ $item_opt->cost_price }}">
                            {{ $item_opt->name }}
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
        const total = (qty * rate) * (1 + taxRate/100);
        row.querySelector('.line-amount').textContent = '$' + total.toFixed(2);
        calculateTotals();
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalTax = 0;
        document.querySelectorAll('#lineItemsBody tr').forEach(row => {
            const qtyInput = row.querySelector('input[name$="[quantity]"]');
            const rateInput = row.querySelector('input[name$="[unit_price]"]');
            const taxSelect = row.querySelector('select[name$="[tax_rate]"]');
            
            if (qtyInput && rateInput && taxSelect) {
                const qty = parseFloat(qtyInput.value) || 0;
                const rate = parseFloat(rateInput.value) || 0;
                const tax = parseFloat(taxSelect.value) || 0;
                const lineSub = qty * rate;
                subtotal += lineSub;
                totalTax += lineSub * (tax/100);
            }
        });
        const discountInput = document.getElementById('discount');
        const discountTypeSelect = document.getElementById('discountType');
        
        const disc = parseFloat(discountInput.value) || 0;
        const discType = discountTypeSelect.value;
        const discAmt = discType === 'percent' ? (subtotal + totalTax) * (disc/100) : disc;
        const total = subtotal + totalTax - discAmt;
        document.getElementById('subtotal').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('tax').textContent = '$' + totalTax.toFixed(2);
        document.getElementById('total').textContent = '$' + Math.max(0, total).toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', () => {
        calculateTotals();
    });
</script>
@endpush
