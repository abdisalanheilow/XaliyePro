<!-- Manage Branch Access Modal -->
<div id="manageBranchAccessModal"
    class="modal-hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-lg shadow-xl w-full max-w-2xl max-h-[90vh] flex flex-col">
        <div class="flex items-center justify-between p-4 md:p-6 border-b border-gray-200 flex-shrink-0">
            <div>
                <h2 class="text-lg md:text-xl font-bold text-gray-900">Manage Branch Access</h2>
                <p class="text-xs md:text-sm text-gray-500 mt-1">Configure branches and stores for User</p>
            </div>
            <button onclick="closeModal('manageBranchAccessModal')" class="text-gray-400 hover:text-gray-600">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>

        <form action="#" method="POST" class="p-4 md:p-6 space-y-4 overflow-y-auto">
            @csrf
            <!-- Current Role Info -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 md:p-4 flex-shrink-0">
                <p class="text-sm text-blue-900">
                    <span class="font-semibold">Role Access Type:</span> (Derived from Role)
                </p>
            </div>

            <!-- Branches List -->
            <div class="space-y-3 max-h-96 overflow-y-auto">
                @foreach ($branches as $branch)
                    <div class="border-2 border-gray-200 rounded-lg p-3 md:p-4">
                        <div class="flex items-center justify-between mb-3">
                            <label class="flex items-center gap-3 cursor-pointer flex-1">
                                <input type="checkbox" name="branches[]" value="{{ $branch->id }}"
                                    class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375]">
                                <i data-lucide="building-2" class="w-5 h-5 text-gray-400"></i>
                                <span class="text-sm font-semibold text-gray-900">{{ $branch->name }}</span>
                            </label>
                        </div>
                        <div class="border-l-2 border-gray-300 ml-5 pl-4">
                            <p class="text-xs font-medium text-gray-600 mb-2">Assigned Stores:</p>
                            <div class="space-y-2">
                                @foreach ($branch->stores as $store)
                                    <label class="flex items-center gap-3 cursor-pointer">
                                        <input type="checkbox" name="stores[]" value="{{ $store->id }}"
                                            class="w-4 h-4 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375]">
                                        <i data-lucide="store" class="w-4 h-4 text-gray-400"></i>
                                        <span class="text-sm text-gray-700">{{ $store->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Allow viewing all branches -->
            <label class="flex items-start md:items-center gap-3 cursor-pointer pt-2">
                <input type="checkbox" name="view_all_branches" value="1"
                    class="w-4 h-4 mt-0.5 md:mt-0 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375]">
                <i data-lucide="eye" class="w-4 h-4 mt-0.5 md:mt-0 text-gray-500"></i>
                <span class="text-sm font-medium text-gray-900">Allow viewing data from all branches (recommended for
                    Accountants)</span>
            </label>

            <!-- Buttons -->
            <div class="flex flex-col md:flex-row gap-3 pt-4">
                <button type="submit"
                    class="w-full md:flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all">
                    Save Changes
                </button>
                <button type="button" onclick="closeModal('manageBranchAccessModal')"
                    class="w-full md:flex-1 px-6 py-3 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">
                    Cancel
                </button>
            </div>
        </form>
    </div>
</div>
