<!-- Add Unit Modal -->
<div x-show="showAdd" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showAdd" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
            @click="showAdd = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="showAdd" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full border border-gray-100">

            <div
                class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900">Add New Unit</h3>
                <button @click="showAdd = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form action="{{ route('items.units.store') }}" method="POST" @submit="isSaving = true">
                @csrf
                <div class="p-6">
                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Unit Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" required placeholder="e.g., Kilogram"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                            </div>

                            <!-- Short Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Short Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="short_name" required placeholder="e.g., kg"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                            </div>
                        </div>

                        <!-- Conversion Section -->
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <i data-lucide="refresh-cw" class="w-4 h-4 text-[#28A375]"></i>
                                    Unit Conversion
                                </h4>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="addForm.hasConversion" class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28A375]">
                                    </div>
                                </label>
                            </div>

                            <div x-show="addForm.hasConversion" x-transition.opacity class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Base
                                            Unit</label>
                                        <select name="base_unit_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                            <option value="">Select Base Unit</option>
                                            @foreach ($baseUnits as $bu)
                                                <option value="{{ $bu->id }}">{{ $bu->name }} ({{ $bu->short_name }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Operator</label>
                                        <select name="operator"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                            <option value="*">Multiply (*)</option>
                                            <option value="/">Divide (/)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Value</label>
                                        <input type="number" step="any" name="operation_value" placeholder="e.g., 1000"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                        <button type="submit" :disabled="isSaving"
                            class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                            <template x-if="!isSaving">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4 text-white"></i>
                                    Save Unit
                                </span>
                            </template>
                            <template x-if="isSaving">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Processing...</span>
                                </div>
                            </template>
                        </button>
                        <button type="button" @click="showAdd = false" :disabled="isSaving"
                            class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Unit Modal -->
<div x-show="showEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="showEdit" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
            @click="showEdit = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="showEdit" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full border border-gray-100">

            <div
                class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900">Edit Unit</h3>
                <button @click="showEdit = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form :action="'{{ route('items.units.update', ':id') }}'.replace(':id', editId)" method="POST"
                @submit="isSaving = true">
                @csrf @method('PUT')
                <div class="p-6">
                    <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <div class="grid grid-cols-2 gap-4">
                            <!-- Unit Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="name" :value="editForm.name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                            </div>

                            <!-- Short Name -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Short Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="short_name" :value="editForm.short_name" required
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                            </div>
                        </div>

                        <!-- Conversion Section -->
                        <div class="bg-gray-50 rounded-2xl p-5 border border-gray-100">
                            <div class="flex items-center justify-between mb-4">
                                <h4 class="text-sm font-bold text-gray-900 flex items-center gap-2">
                                    <i data-lucide="refresh-cw" class="w-4 h-4 text-[#28A375]"></i>
                                    Unit Conversion
                                </h4>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" x-model="editForm.hasConversion" class="sr-only peer">
                                    <div
                                        class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-[#28A375]">
                                    </div>
                                </label>
                            </div>

                            <div x-show="editForm.hasConversion" x-transition.opacity class="space-y-4">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Base
                                            Unit</label>
                                        <select name="base_unit_id"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                            <option value="">Select Base Unit</option>
                                            @foreach ($baseUnits as $bu)
                                                <option value="{{ $bu->id }}"
                                                    :selected="editForm.base_unit_id == {{ $bu->id }}">{{ $bu->name }}
                                                    ({{ $bu->short_name }})</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Operator</label>
                                        <select name="operator"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                            <option value="*" :selected="editForm.operator === '*'">Multiply (*)
                                            </option>
                                            <option value="/" :selected="editForm.operator === '/'">Divide (/)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-xs font-semibold text-gray-500 mb-1.5">Value</label>
                                        <input type="number" step="any" name="operation_value"
                                            :value="editForm.operation_value" placeholder="e.g., 1000"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select name="status"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                <option value="active" :selected="editForm.status === 'active'">Active</option>
                                <option value="inactive" :selected="editForm.status === 'inactive'">Inactive</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                        <button type="submit" :disabled="isSaving"
                            class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                            <template x-if="!isSaving">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="save" class="w-4 h-4 text-white"></i>
                                    Update Unit
                                </span>
                            </template>
                            <template x-if="isSaving">
                                <div class="flex items-center gap-2">
                                    <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    <span>Processing...</span>
                                </div>
                            </template>
                        </button>
                        <button type="button" @click="showEdit = false" :disabled="isSaving"
                            class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
