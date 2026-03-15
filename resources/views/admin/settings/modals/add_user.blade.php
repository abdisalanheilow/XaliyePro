<!-- Add User Modal -->
<div id="addUserModal"
    class="modal-hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col" x-data="{ isSaving: false }">
        <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 flex-shrink-0">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Add New User</h2>
            <button onclick="closeModal('addUserModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('settings.users.store') }}" method="POST" @submit="isSaving = true" class="p-4 md:p-6 space-y-4 overflow-y-auto">
            @csrf
            <!-- Employee Selection -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1.5">
                    Select Employee <span class="text-red-500">*</span>
                </label>
                <select name="employee_id" id="employee_select" required onchange="autoFillEmployee(this)"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white transition-all">
                    <option value="">Select Employee</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" data-name="{{ $employee->name }}"
                            data-email="{{ $employee->email }}">
                            {{ $employee->name }} ({{ $employee->employee_id }})
                        </option>
                    @endforeach
                </select>
                <p class="text-[10px] text-gray-500 mt-1">Users must be linked to an existing employee record.</p>
            </div>

            <!-- Full Name & Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Full Name
                    </label>
                    <input type="text" name="name" id="user_name" readonly placeholder="Select employee above..."
                        class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 focus:outline-none cursor-not-allowed transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email Address
                    </label>
                    <input type="email" name="email" id="user_email" readonly placeholder="Select employee above..."
                        class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-lg text-sm text-gray-500 focus:outline-none cursor-not-allowed transition-all">
                </div>
            </div>

            <!-- Role & Status -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Role <span class="text-red-500">*</span>
                    </label>
                    <select name="role_id" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white transition-all">
                        <option value="">Select Role</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}">{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Status <span class="text-red-500">*</span>
                    </label>
                    <select name="status" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white transition-all">
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <!-- Password -->
            <div>
                <label id="password_label" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password <span class="text-red-500">*</span>
                </label>
                <input type="password" name="password" required placeholder="••••••••"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
            </div>

            <!-- Branch & Store Assignment -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Branch & Store Assignment</h3>
                <div class="border border-gray-200 rounded-lg max-h-48 overflow-y-auto custom-scrollbar">
                    @foreach ($branches as $branch)
                        <div class="p-3 md:p-4 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition-colors">
                            <label class="flex items-center gap-3 mb-3 cursor-pointer group">
                                <input type="checkbox" name="branches[]" value="{{ $branch->id }}"
                                    class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375] transition-all">
                                <i data-lucide="building-2" class="w-4 h-4 text-[#28A375]"></i>
                                <span class="text-sm font-medium text-gray-900 group-hover:text-[#28A375] transition-colors">{{ $branch->name }}</span>
                            </label>
                            <div class="ml-7 space-y-2">
                                @foreach ($branch->stores as $store)
                                    <label class="flex items-center gap-3 cursor-pointer group/store">
                                        <input type="checkbox" name="stores[]" value="{{ $store->id }}"
                                            class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375] transition-all">
                                        <i data-lucide="store" class="w-4 h-4 text-gray-400 group-hover/store:text-[#28A375] transition-colors"></i>
                                        <span class="text-sm text-gray-700 group-hover/store:text-gray-900 transition-colors">{{ $store->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Allow viewing all branches -->
            <label class="flex items-start md:items-center gap-3 cursor-pointer pt-2 group">
                <input type="checkbox" name="view_all_branches"
                    class="w-4 h-4 mt-0.5 md:mt-0 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375] transition-all">
                <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Allow viewing all branches (for Accountants, Admins)</span>
            </label>

            <!-- Buttons -->
            <div class="flex flex-col md:flex-row gap-3 pt-4 border-t border-gray-50">
                <button type="submit" :disabled="isSaving"
                    class="w-full md:flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span x-show="!isSaving" class="flex items-center gap-2">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        Confirm & Create
                    </span>
                    <span x-show="isSaving" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Creating User...
                    </span>
                </button>
                <button type="button" onclick="closeModal('addUserModal')" :disabled="isSaving"
                    class="w-full md:flex-1 px-6 py-3 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all disabled:opacity-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
