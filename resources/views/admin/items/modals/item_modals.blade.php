@php
    $categories = $categories ?? collect();
    $brands = $brands ?? collect();
    $units = $units ?? collect();
    $accounts = $accounts ?? collect();
    $branches = $branches ?? collect();
    $stores = $stores ?? collect();
@endphp

<!-- Item Modal (Add/Edit) -->
<div x-show="showAdd || showEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAdd || showEdit" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            class="fixed inset-0 bg-gray-900/40 backdrop-blur-md" @click="closeModal()"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="showAdd || showEdit" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-4xl sm:w-full border border-gray-100">

            <div
                class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                <div class="flex items-center gap-3">
                    <h3 class="text-xl font-bold text-gray-900" x-text="showAdd ? 'Add New Item' : 'Edit Item'">
                    </h3>
                </div>
                <button @click="closeModal()" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form
                :action="showAdd ? '{{ route('items.store') }}' : '{{ route('items.update', ':id') }}'.replace(':id', editId)"
                method="POST" @submit="isSaving = true">
                @csrf
                <template x-if="showEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-8 max-h-[70vh] overflow-y-auto custom-scrollbar space-y-8">

                    <!-- Section 1: Item Type Selection -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                            <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                <i data-lucide="layers" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">Item Type</h4>
                                <p class="text-xs text-gray-500">Classify as a physical product or a service</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Item Type <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors" :class="editForm.type === 'product' ? 'border-[#28A375] bg-green-50/30' : 'border-gray-200'">
                                        <input type="radio" name="type" value="product" x-model="editForm.type" class="w-4 h-4 text-[#28A375] focus:ring-[#28A375] border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-900">Product</span>
                                            <span class="block text-xs text-gray-500">Physical item with stock tracking</span>
                                        </div>
                                    </label>
                                    <label class="relative flex items-center p-4 border rounded-xl cursor-pointer hover:bg-gray-50 transition-colors" :class="editForm.type === 'service' ? 'border-[#28A375] bg-green-50/30' : 'border-gray-200'">
                                        <input type="radio" name="type" value="service" x-model="editForm.type" class="w-4 h-4 text-[#28A375] focus:ring-[#28A375] border-gray-300">
                                        <div class="ml-3">
                                            <span class="block text-sm font-bold text-gray-900">Service</span>
                                            <span class="block text-xs text-gray-500">Non-physical service or labor</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: General Information -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                                <i data-lucide="package" class="w-5 h-5" x-show="editForm.type === 'product'"></i>
                                <i data-lucide="briefcase" class="w-5 h-5" x-show="editForm.type === 'service'"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">General Information</h4>
                                <p class="text-xs text-gray-500">Basic details about your <span x-text="editForm.type"></span></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5" x-text="editForm.type === 'product' ? 'Product Name' : 'Service Name'"></label>
                                <input type="text" name="name" x-model="editForm.name" required
                                    :placeholder="editForm.type === 'product' ? 'Enter product name' : 'Enter service name'"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">SKU / Item Code</label>
                                <input type="text" name="sku" x-model="editForm.sku"
                                    placeholder="Leave blank to auto-generate"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Category <span
                                        class="text-red-500">*</span></label>
                                <select name="category_id" x-model="editForm.category_id" required
                                    class="searchable-select w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] outline-none transition-all">
                                    <option value="">Select Category</option>
                                    @foreach ($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unit of Measure</label>
                                <select name="unit_id" x-model="editForm.unit_id"
                                    class="searchable-select w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] outline-none transition-all">
                                    <option value="">Select Unit</option>
                                    @foreach ($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_name }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                                <textarea name="description" x-model="editForm.description" rows="3"
                                    :placeholder="editForm.type === 'product' ? 'Detailed description of the product...' : 'Detailed description of the service...'"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] outline-none transition-all resize-none shadow-sm"></textarea>
                            </div>
                            <div x-show="editForm.type === 'product'">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Brand</label>
                                <select name="brand_id" x-model="editForm.brand_id"
                                    class="searchable-select w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] outline-none transition-all">
                                    <option value="">Select Brand</option>
                                    @foreach ($brands as $brand)
                                        <option value="{{ $brand->id }}">{{ $brand->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div x-show="editForm.type === 'product'">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Barcode</label>
                                <input type="text" name="barcode" x-model="editForm.barcode" placeholder="Enter barcode"
                                    class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] outline-none transition-all shadow-sm">
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Pricing & Inventory -->
                    <div class="space-y-6">
                        <div class="flex items-center gap-3 border-b border-gray-100 pb-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-green-50 text-[#28A375] flex items-center justify-center">
                                <i data-lucide="dollar-sign" class="w-5 h-5"></i>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">Pricing <span x-show="editForm.type === 'product'">& Inventory</span></h4>
                                <p class="text-xs text-gray-500">Set costs, prices, <span x-show="editForm.type === 'product'">and stock tracking</span></p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Sale Price <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" step="any" name="selling_price"
                                        x-model="editForm.selling_price" required placeholder="0.00"
                                        class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] outline-none transition-all shadow-sm">
                                </div>
                            </div>
                            <div x-show="editForm.type === 'product'">
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Purchase Cost <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <span
                                        class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                    <input type="number" step="any" name="cost_price" x-model="editForm.cost_price"
                                        :required="editForm.type === 'product'" placeholder="0.00"
                                        class="w-full pl-8 pr-4 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-[#28A375] outline-none transition-all shadow-sm">
                                </div>
                            </div>
                        </div>

                        <div class="bg-amber-50 rounded-2xl p-6 border border-amber-100 space-y-4" x-show="editForm.type === 'product'">
                            <h5 class="text-sm font-bold text-amber-800 flex items-center gap-2">
                                <i data-lucide="map-pin" class="w-4 h-4"></i> Location & Opening Stock
                            </h5>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div>
                                    <label
                                        class="block text-xs font-bold text-amber-900/50 uppercase tracking-wider mb-1.5">Branch</label>
                                    <select name="branch_id" x-model="editForm.branch_id"
                                        @change="if(editForm.branch_id) editForm.store_id = ''"
                                        class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none transition-all appearance-none">
                                        <option value="">No Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-xs font-bold text-amber-900/50 uppercase tracking-wider mb-1.5">Store</label>
                                    <select name="store_id" x-model="editForm.store_id"
                                        @change="if(editForm.store_id) editForm.branch_id = ''"
                                        class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none transition-all appearance-none">
                                        <option value="">No Store</option>
                                        @foreach ($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div x-show="showAdd">
                                    <label
                                        class="block text-xs font-bold text-amber-900/50 uppercase tracking-wider mb-1.5">Opening
                                        Stock</label>
                                    <input type="number" name="opening_stock" placeholder="Initial Qty"
                                        class="w-full px-4 py-2.5 bg-white border border-amber-200 rounded-xl text-sm focus:ring-2 focus:ring-amber-500 outline-none transition-all">
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="flex gap-4 px-8 py-6 border-t border-gray-100 bg-gray-50/50">
                    <button type="button" @click="closeModal()"
                        class="flex-1 px-6 py-3.5 border border-gray-200 rounded-xl text-sm font-bold text-gray-600 hover:bg-white hover:border-gray-300 transition-all shadow-sm">
                        Discard Changes
                    </button>

                    <button type="submit" :disabled="isSaving"
                        class="flex-[2] px-6 py-3.5 bg-[#28A375] text-white rounded-xl text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2 shadow-lg shadow-[#28A375]/20">
                        <template x-if="!isSaving">
                            <span class="flex items-center gap-2">
                                <i data-lucide="check-circle" class="w-5 h-5 text-white"></i>
                                <span x-text="showAdd ? ('Create ' + (editForm.type === 'product' ? 'Product' : 'Service') + ' Now') : ('Save ' + (editForm.type === 'product' ? 'Product' : 'Service') + ' Updates')"></span>
                            </span>
                        </template>
                        <template x-if="isSaving">
                            <div class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span>Processing...</span>
                            </div>
                        </template>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
