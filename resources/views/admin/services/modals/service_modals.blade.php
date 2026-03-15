@php
    $categories = $categories ?? collect();
    $units = $units ?? collect();
    $accounts = $accounts ?? collect();
@endphp

<!-- Service Modal (Add/Edit) -->
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
                    <h3 class="text-xl font-bold text-gray-900" x-text="showAdd ? 'Add New Service' : 'Edit Service'">
                    </h3>
                </div>
                <button @click="closeModal()" type="button" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <!-- Stepper Progress -->
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/30 relative">
                <div class="flex items-center justify-between relative max-w-2xl mx-auto">
                    <!-- Progress Line -->
                    <div class="absolute top-1/2 left-0 w-full h-0.5 bg-gray-200 -translate-y-1/2 z-0"></div>
                    <div class="absolute top-1/2 left-0 h-0.5 bg-[#28A375] transition-all duration-500 -translate-y-1/2 z-0"
                        :style="'width: ' + ((activeFormStep - 1) * 50) + '%'"></div>

                    <!-- Step 1 -->
                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300 shadow-sm"
                            :class="activeFormStep >= 1 ? 'bg-[#28A375] text-white border-4 border-white' : 'bg-white text-gray-400 border-2 border-gray-200'">
                            <span x-show="activeFormStep <= 1">1</span>
                            <i data-lucide="check" x-show="activeFormStep > 1" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider"
                            :class="activeFormStep >= 1 ? 'text-[#28A375]' : 'text-gray-400'">General</span>
                    </div>

                    <!-- Step 2 -->
                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300 shadow-sm"
                            :class="activeFormStep >= 2 ? 'bg-[#28A375] text-white border-4 border-white' : 'bg-white text-gray-400 border-2 border-gray-200'">
                            <span x-show="activeFormStep <= 2">2</span>
                            <i data-lucide="check" x-show="activeFormStep > 2" class="w-5 h-5"></i>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider"
                            :class="activeFormStep >= 2 ? 'text-[#28A375]' : 'text-gray-400'">Pricing</span>
                    </div>

                    <!-- Step 3 -->
                    <div class="relative z-10 flex flex-col items-center gap-2">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all duration-300 shadow-sm"
                            :class="activeFormStep >= 3 ? 'bg-[#28A375] text-white border-4 border-white' : 'bg-white text-gray-400 border-2 border-gray-200'">
                            <span>3</span>
                        </div>
                        <span class="text-[11px] font-bold uppercase tracking-wider"
                            :class="activeFormStep >= 3 ? 'text-[#28A375]' : 'text-gray-400'">Accounting</span>
                    </div>
                </div>
            </div>

            <form
                :action="showAdd ? '{{ route('services.store') }}' : '{{ route('services.update', ':id') }}'.replace(':id', editId)"
                method="POST" @submit="isSaving = true">
                @csrf
                <template x-if="showEdit">
                    <input type="hidden" name="_method" value="PUT">
                </template>

                <div class="p-6 max-h-[55vh] overflow-y-auto custom-scrollbar">

                    <!-- Step 1: General Info -->
                    <div id="service-step-1" x-show="activeFormStep === 1" x-transition.opacity.duration.300ms
                        class="space-y-4">
                        <div class="bg-green-50/50 p-3 rounded-xl border border-green-100 mb-4 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-[#28A375] text-white flex items-center justify-center shrink-0">
                                <i data-lucide="info" class="w-4 h-4"></i>
                            </div>
                            <p class="text-xs text-gray-700 font-medium">Step 1: Provide basic service details like
                                Name,
                                SKU, and Category.</p>
                        </div>
                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div class="md:col-span-1">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Service Name <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="name" x-model="editForm.name" required
                                        placeholder="Enter service name"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">SKU / Item
                                        Code</label>
                                    <input type="text" name="sku" x-model="editForm.sku"
                                        placeholder="Leave blank to auto-generate"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Category <span
                                            class="text-red-500">*</span></label>
                                    <select name="category_id" x-model="editForm.category_id" required
                                        class="searchable-select w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                                        <option value="">Select Category</option>
                                        @foreach ($categories as $cat)
                                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Unit of Measure
                                        (Optional)</label>
                                    <select name="unit_id" x-model="editForm.unit_id"
                                        class="searchable-select w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                                        <option value="">Select Unit</option>
                                        @foreach ($units as $unit)
                                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->short_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Description</label>
                                    <textarea name="description" x-model="editForm.description" rows="3"
                                        placeholder="Detailed description of the service..."
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Status</label>
                                    <select name="status" x-model="editForm.status"
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]"
                                        style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 fill=%22none%22 viewBox=%220%200%2020%2020%22%3E%3Cpath stroke=%22%236B7280%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Pricing Info -->
                    <div id="service-step-2" x-show="activeFormStep === 2" x-transition.opacity.duration.300ms
                        style="display: none;" class="space-y-4">
                        <div class="bg-green-50/50 p-3 rounded-xl border border-green-100 mb-4 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-[#28A375] text-white flex items-center justify-center shrink-0">
                                <i data-lucide="tag" class="w-4 h-4"></i>
                            </div>
                            <p class="text-xs text-gray-700 font-medium">Step 2: Set the service cost and selling price
                                for profitability tracking.</p>
                        </div>
                        <div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5 text-red-600">Cost
                                        Price (Estimated)</label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 text-red-600 left-0 pl-4 flex items-center pointer-events-none">
                                            $
                                        </div>
                                        <input type="number" step="0.01" name="cost_price" x-model="editForm.cost_price"
                                            placeholder="0.00"
                                            class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all text-red-600 font-medium">
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1.5">Estimated cost to perform this service</p>
                                </div>
                                <div
                                    class="bg-gradient-to-br from-[#28A375]/10 to-[#1e825d]/10 p-4 rounded-xl border border-[#28A375]/20 md:row-span-2 flex flex-col justify-center shadow-[inset_0_2px_4px_rgba(0,0,0,0.02)] relative overflow-hidden">
                                    <div class="absolute -right-4 -bottom-4 opacity-5">
                                        <i data-lucide="trending-up" class="w-32 h-32"></i>
                                    </div>
                                    <label
                                        class="block text-sm font-bold text-[#28A375] mb-2 uppercase tracking-wide">Selling
                                        Price <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <div
                                            class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="text-[#28A375] font-bold sm:text-lg">$</span>
                                        </div>
                                        <input type="number" step="0.01" name="selling_price" required
                                            x-model="editForm.selling_price" placeholder="0.00"
                                            class="w-full pl-10 pr-4 py-4 border-2 border-[#28A375]/30 rounded-lg text-lg sm:text-2xl font-bold focus:ring-4 focus:ring-[#28A375]/20 focus:border-[#28A375] outline-none transition-all text-[#1e825d] bg-white shadow-sm">
                                    </div>
                                    <p class="text-xs text-[#28A375]/80 mt-2 font-medium flex items-center gap-1">
                                        <i data-lucide="calculator" class="w-3.5 h-3.5"></i>
                                        Base price billed to customer
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tax Rate (%)</label>
                                    <div class="relative">
                                        <input type="number" step="0.01" name="tax_rate" x-model="editForm.tax_rate"
                                            placeholder="0.00"
                                            class="w-full pr-8 pl-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                                        <div
                                            class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-gray-400">
                                            %
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-400 mt-1.5">Applicable tax percentage</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Accounting Info -->
                    <div id="service-step-3" x-show="activeFormStep === 3" x-transition.opacity.duration.300ms
                        style="display: none;" class="space-y-4">
                        <div class="bg-green-50/50 p-3 rounded-xl border border-green-100 mb-4 flex items-center gap-3">
                            <div
                                class="w-8 h-8 rounded-full bg-[#28A375] text-white flex items-center justify-center shrink-0">
                                <i data-lucide="landmark" class="w-4 h-4"></i>
                            </div>
                            <p class="text-xs text-gray-700 font-medium">Final Step: Select the accounting ledgers for
                                automated financial entries.</p>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Sales -->
                            <div class="space-y-4">
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-1.5">
                                        <i data-lucide="trending-up" class="w-4 h-4 text-[#28A375]"></i>
                                        Sales (Revenue)
                                    </label>
                                    <select name="sales_account_id" x-model="editForm.sales_account_id"
                                        class="searchable-select w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                                        <option value="">Default Sales Account</option>
                                        @foreach ($accounts->where('type', 'revenue') as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- Purchases -->
                            <div class="space-y-4">
                                <div>
                                    <label class="flex items-center gap-2 text-sm font-semibold text-gray-900 mb-1.5">
                                        <i data-lucide="shopping-cart" class="w-4 h-4 text-orange-500"></i>
                                        Purchase (Expense)
                                    </label>
                                    <select name="purchase_account_id" x-model="editForm.purchase_account_id"
                                        class="searchable-select w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all">
                                        <option value="">Default Expense Account</option>
                                        @foreach ($accounts->where('type', 'expense') as $acc)
                                            <option value="{{ $acc->id }}">{{ $acc->name }} ({{ $acc->code }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </div>
                        <div
                            class="mt-4 bg-blue-50/50 p-4 rounded-xl border border-blue-100 flex gap-3 text-sm text-blue-800">
                            <i data-lucide="info" class="w-5 h-5 text-blue-500 shrink-0"></i>
                            <p>Services do not require Inventory Asset or Cost of Goods Sold (COGS) accounts because
                                there is no physical stock to track.</p>
                        </div>
                    </div>

                </div>

                <!-- Footer Navigation -->
                <div
                    class="px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-[2rem] flex items-center justify-between">
                    <div>
                        <button type="button" x-show="activeFormStep > 1" @click="activeFormStep--"
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 transition-all flex items-center gap-2 shadow-sm">
                            <i data-lucide="chevron-left" class="w-4 h-4"></i>
                            Previous Step
                        </button>
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="button" @click="closeModal()"
                            class="px-5 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 transition-all shadow-sm">
                            Cancel
                        </button>

                        <!-- Next Button (Shows on Step 1, 2) -->
                        <button type="button" x-show="activeFormStep < 3"
                            @click="if(validateStep(activeFormStep)) activeFormStep++"
                            class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#1a855c] transition-all flex items-center gap-2 shadow-md hover:shadow-lg">
                            Next Step
                            <i data-lucide="chevron-right" class="w-4 h-4"></i>
                        </button>

                        <!-- Submit Button (Shows on Step 3) -->
                        <button type="submit" x-show="activeFormStep === 3"
                            x-text="showAdd ? 'Save Service' : 'Update Service'"
                            class="px-6 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#1a855c] transition-all shadow-md hover:shadow-lg flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
