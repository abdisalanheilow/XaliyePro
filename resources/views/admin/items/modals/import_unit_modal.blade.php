<!-- Import Units Modal -->
<div x-show="isImportModalOpen" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <div x-show="isImportModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
            @click="isImportModalOpen = false"></div>

        <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

        <div x-show="isImportModalOpen" x-transition:enter="ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full border border-gray-100">

            <div
                class="px-8 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white z-10">
                <h3 class="text-xl font-bold text-gray-900">Import Units</h3>
                <button type="button" @click="isImportModalOpen = false"
                    class="text-gray-400 hover:text-gray-600 transition-colors">
                    <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>

            <form action="{{ route('items.units.import') }}" method="POST" enctype="multipart/form-data"
                @submit="isSaving = true">
                @csrf
                <div class="p-6">
                    <div class="space-y-6 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                        <!-- File Upload Area -->
                        <div
                            class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-50 transition-colors relative">
                            <input type="file" name="file" id="import_file" accept=".csv" required
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10"
                                @change="selectedFile = $event.target.files[0]">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div
                                    class="w-14 h-14 bg-blue-50 text-blue-600 rounded-full flex items-center justify-center mb-2">
                                    <i data-lucide="upload" class="w-8 h-8"></i>
                                </div>
                                <h4 class="text-sm font-semibold text-gray-700"
                                    x-text="selectedFile ? selectedFile.name : 'Upload CSV file'"></h4>
                                <p class="text-xs text-gray-500" x-show="!selectedFile">Maximum file size: 5MB</p>
                                <div x-show="!selectedFile"
                                    class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium">Choose
                                    File</div>
                            </div>
                        </div>

                        <!-- Template Link -->
                        <div class="bg-blue-50 rounded-xl p-5 border border-blue-100">
                            <div class="flex items-start gap-4">
                                <i data-lucide="file-text" class="w-6 h-6 text-blue-600 mt-1"></i>
                                <div>
                                    <h4 class="text-sm font-semibold text-blue-900 mb-1">Need a template?</h4>
                                    <p class="text-xs text-blue-700 mb-2">Download the CSV template to see the required
                                        format</p>
                                    <a href="{{ route('items.units.template') }}"
                                        class="text-sm font-bold text-blue-600 hover:text-blue-800 underline">Download
                                        Template</a>
                                </div>
                            </div>
                        </div>

                        <!-- Format Requirements -->
                        <div class="bg-gray-50 rounded-xl p-5">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">CSV Format Requirements:</h4>
                            <ul class="text-xs text-gray-600 space-y-2 list-disc pl-4">
                                <li>First row must contain column headers</li>
                                <li>Use comma (,) as separator</li>
                                <li><strong>Operator</strong> and <strong>Operator Value</strong> are only used if Base
                                    Unit is provided</li>
                            </ul>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                        <button type="submit" :disabled="isSaving"
                            class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                            <template x-if="!isSaving">
                                <span class="flex items-center gap-2">
                                    <i data-lucide="upload" class="w-4 h-4 text-white"></i>
                                    Upload & Import
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
                                    Processing...
                                </div>
                            </template>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
