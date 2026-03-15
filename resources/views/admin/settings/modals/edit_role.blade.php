<!-- Edit Role Modal -->
<div id="editRoleModal"
    class="modal-hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col" x-data="{ isSaving: false }">
        <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 flex-shrink-0">
            <h2 class="text-lg md:text-xl font-bold text-gray-900">Create/Edit Role</h2>
            <button onclick="closeModal('editRoleModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="{{ route('settings.roles.store') }}" method="POST" @submit="isSaving = true" class="p-4 md:p-6 space-y-5 overflow-y-auto">
            @csrf
            <!-- Role Name & Description -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Role Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" required placeholder="Administrator"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1.5">
                        Description <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="description" required
                        placeholder="Full system access across all branches and stores"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] transition-all">
                </div>
            </div>

            <!-- Branch Access Type -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-2">Branch Access Type</h3>
                <label class="flex items-center gap-3 cursor-pointer group">
                    <input type="checkbox" name="branch_access_type" value="restricted"
                        class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375] transition-all">
                    <span class="text-sm text-gray-700 group-hover:text-gray-900 transition-colors">Branch Restricted (User can only access assigned
                        branches)</span>
                </label>
            </div>

            <!-- Permissions -->
            <div>
                <h3 class="text-sm font-semibold text-gray-900 mb-3">Permissions</h3>
                <div class="border border-gray-200 rounded-lg max-h-80 overflow-y-auto custom-scrollbar">
                    @php /** @var \Illuminate\Support\Collection<string, \App\Models\Permission[]> $permissions */ @endphp
                    @foreach ($permissions as $module => $module_permissions)
                        <!-- Module -->
                        <div class="p-3 md:p-4 border-b border-gray-200 last:border-0 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center justify-between mb-3">
                                <div class="flex items-center gap-2">
                                    <i data-lucide="lock-open" class="w-4 h-4 text-[#28A375]"></i>
                                    <span class="text-sm font-semibold text-gray-900">{{ $module }}</span>
                                </div>
                                <button type="button" onclick="selectAllInModule(this)"
                                    class="text-xs font-medium text-[#28A375] hover:text-[#229967] transition-colors">
                                    <i data-lucide="check-square" class="w-3 h-3 inline mr-1"></i>
                                    Select All
                                </button>
                            </div>
                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($module_permissions as $perm)
                                    @php /** @var \App\Models\Permission $perm */ @endphp
                                    <label class="flex items-center gap-3 cursor-pointer group/perm">
                                        <input type="checkbox" name="permissions[]" value="{{ optional($perm)->id }}"
                                            class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375] transition-all">
                                        <span class="text-sm text-gray-700 group-hover/perm:text-gray-900 transition-colors">{{ optional($perm)->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Buttons -->
            <div class="flex flex-col md:flex-row gap-3 pt-4 border-t border-gray-50">
                <button type="submit" :disabled="isSaving"
                    class="w-full md:flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                    <span x-show="!isSaving" class="flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        Save Role
                    </span>
                    <span x-show="isSaving" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Processing...
                    </span>
                </button>
                <button type="button" onclick="closeModal('editRoleModal')" :disabled="isSaving"
                    class="w-full md:flex-1 px-6 py-3 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all disabled:opacity-50">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function selectAllInModule(btn) {
        const moduleDiv = btn.closest('.p-3');
        const checkboxes = moduleDiv.querySelectorAll('input[type="checkbox"]');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
    }
</script>
