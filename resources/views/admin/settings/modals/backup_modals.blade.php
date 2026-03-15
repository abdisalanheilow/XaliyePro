<!-- Create Backup Modal -->
<div id="createBackupModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto" x-data="{ isSaving: false }">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl z-10">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                        <i data-lucide="database" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Create New Backup</h2>
                        <p class="text-sm text-gray-500">Configure and create a backup of your data</p>
                    </div>
                </div>
                <button onclick="closeModal('createBackupModal')" class="p-1 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <form action="{{ route('settings.backup.create') }}" method="POST" @submit="isSaving = true">
                @csrf
                <!-- Backup Name -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-2">
                        Backup Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" value="XaliyePro_DB_Backup_{{ date('Y-m-d') }}"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                    <p class="mt-1 text-xs text-gray-500">A descriptive name to identify this backup</p>
                </div>

                <!-- Backup Type -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">
                        Backup Type <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="backupType" value="database" checked class="sr-only peer">
                            <div
                                class="border-2 border-gray-200 rounded-lg p-4 peer-checked:border-[#28A375] peer-checked:bg-green-50 transition-all">
                                <div
                                    class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center mx-auto mb-2">
                                    <i data-lucide="database" class="w-5 h-5 text-purple-600"></i>
                                </div>
                                <p class="text-sm font-semibold text-gray-900 text-center">Database Only</p>
                                <p class="text-xs text-gray-500 text-center mt-1">Database backup</p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Storage Location -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">
                        Storage Location <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        <label
                            class="flex items-start gap-3 p-4 border-2 border-gray-200 rounded-lg cursor-pointer hover:border-gray-300 transition-colors">
                            <input type="radio" name="storageLocation" value="local" checked
                                class="mt-1 w-4 h-4 text-[#28A375] border-gray-300 focus:ring-[#28A375]">
                            <div class="flex-1">
                                <div class="flex items-center gap-2 mb-1">
                                    <i data-lucide="hard-drive" class="w-4 h-4 text-gray-600"></i>
                                    <p class="text-sm font-semibold text-gray-900">Local Server Storage</p>
                                </div>
                                <p class="text-xs text-gray-500">/storage/app/backups/</p>
                                <p class="text-xs text-gray-500 mt-1">Available: <strong>45.8 GB</strong></p>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Compression -->
                <div class="mb-6">
                    <label
                        class="flex items-start gap-3 p-4 bg-blue-50 border border-blue-200 rounded-lg cursor-pointer">
                        <input type="checkbox" name="compression" checked
                            class="mt-1 w-5 h-5 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375]">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Enable Compression</p>
                            <p class="text-xs text-gray-600 mt-1">Reduce backup file size by compressing data
                                (recommended)
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('createBackupModal')" :disabled="isSaving"
                        class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors disabled:opacity-50">
                        Cancel
                    </button>
                    <button type="submit" :disabled="isSaving"
                        class="w-full sm:w-auto px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSaving" class="flex items-center gap-2">
                            <i data-lucide="play" class="w-4 h-4"></i>
                            Start Backup
                        </span>
                        <span x-show="isSaving" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Backing up...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Modal -->
<div id="restoreModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">
        <div class="bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="upload" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Restore from Backup</h2>
                        <p class="text-sm text-gray-500">Upload or select a backup file to restore</p>
                    </div>
                </div>
                <button onclick="closeModal('restoreModal')" class="p-1 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <form action="{{ route('settings.backup.restore') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <!-- Upload Backup File -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">
                        Upload Backup File
                    </label>
                    <label
                        class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-500 transition-colors cursor-pointer block">
                        <input type="file" name="backup_file" class="hidden">
                        <i data-lucide="upload-cloud" class="w-12 h-12 text-gray-400 mx-auto mb-3"></i>
                        <p class="text-sm font-semibold text-gray-900 mb-1">Click to upload or drag and drop</p>
                        <p class="text-xs text-gray-500">Backup files (.zip, .sql, .tar.gz) up to 2GB</p>
                    </label>
                </div>

                <!-- Warning -->
                <div class="bg-orange-50 border-2 border-orange-200 rounded-lg p-4 mb-6">
                    <div class="flex items-start gap-3">
                        <i data-lucide="alert-triangle" class="w-5 h-5 text-orange-600 mt-0.5"></i>
                        <div>
                            <p class="text-sm font-semibold text-orange-900">Important Warning</p>
                            <p class="text-xs text-orange-800 mt-1">Restoring a backup will overwrite current data. This
                                action cannot be undone. Please ensure you have a recent backup before proceeding.</p>
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('restoreModal')"
                        class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button type="button" onclick="closeModal('restoreModal'); openModal('restoreConfirmModal')"
                        class="w-full sm:w-auto px-6 py-3 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors inline-flex items-center justify-center gap-2">
                        <i data-lucide="refresh-ccw" class="w-4 h-4"></i>
                        Continue to Restore
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Restore Confirmation Modal -->
<div id="restoreConfirmModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[110] p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-orange-100 rounded-full mx-auto mb-4">
                <i data-lucide="alert-triangle" class="w-8 h-8 text-orange-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Confirm Restore</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to restore from this backup? This
                will overwrite all current data and cannot be undone.</p>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <button onclick="closeModal('restoreConfirmModal')"
                    class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button onclick="startRestore()"
                    class="w-full sm:w-auto flex-1 px-6 py-3 bg-orange-600 text-white rounded-lg text-sm font-semibold hover:bg-orange-700 transition-colors">
                    Yes, Restore Now
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Schedule Modal -->
<div id="scheduleModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl" x-data="{ isSaving: false }">
        <div class="bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center">
                        <i data-lucide="clock" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Automatic Backup Schedule</h2>
                        <p class="text-sm text-gray-500">Configure automated backup schedule</p>
                    </div>
                </div>
                <button onclick="closeModal('scheduleModal')" class="p-1 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <form action="{{ route('settings.backup.schedule') }}" method="POST" @submit="isSaving = true">
                @csrf
                <!-- Enable Auto Backup -->
                <div class="mb-6">
                    <label
                        class="flex items-start gap-3 p-4 bg-green-50 border-2 border-green-200 rounded-lg cursor-pointer">
                        <input type="checkbox" name="enable_auto_backup" value="1" {{ ($schedule['status'] ?? 'inactive') == 'active' ? 'checked' : '' }}
                            class="mt-1 w-5 h-5 text-[#28A375] border-gray-300 rounded focus:ring-[#28A375]">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Enable Automatic Backups</p>
                            <p class="text-xs text-gray-600 mt-1">Automatically create backups based on the schedule
                                below
                            </p>
                        </div>
                    </label>
                </div>

                <!-- Frequency -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">
                        Backup Frequency
                    </label>
                    <select name="frequency"
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                        <option value="hourly" {{ ($schedule['frequency'] ?? 'daily') == 'hourly' ? 'selected' : '' }}>
                            Every Hour</option>
                        <option value="daily" {{ ($schedule['frequency'] ?? 'daily') == 'daily' ? 'selected' : '' }}>Daily
                        </option>
                        <option value="weekly" {{ ($schedule['frequency'] ?? 'daily') == 'weekly' ? 'selected' : '' }}>
                            Weekly</option>
                        <option value="monthly" {{ ($schedule['frequency'] ?? 'daily') == 'monthly' ? 'selected' : '' }}>
                            Monthly</option>
                    </select>
                </div>

                <!-- Retention Policy -->
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-3">
                        Retention Policy
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="retention" value="keep-30" {{ ($schedule['retention'] ?? 'keep-30') == 'keep-30' ? 'checked' : '' }}
                                class="w-4 h-4 text-[#28A375] border-gray-300 focus:ring-[#28A375]">
                            <span class="text-sm text-gray-700">Keep last 30 days</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="retention" value="keep-90" {{ ($schedule['retention'] ?? 'keep-30') == 'keep-90' ? 'checked' : '' }}
                                class="w-4 h-4 text-[#28A375] border-gray-300 focus:ring-[#28A375]">
                            <span class="text-sm text-gray-700">Keep last 90 days</span>
                        </label>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-6 border-t border-gray-200">
                    <button type="button" onclick="closeModal('scheduleModal')" :disabled="isSaving"
                        class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors disabled:opacity-50">
                        Cancel
                    </button>
                    <button type="submit" :disabled="isSaving"
                        class="w-full sm:w-auto px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] transition-colors inline-flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                        <span x-show="!isSaving" class="flex items-center gap-2">
                            <i data-lucide="save" class="w-4 h-4"></i>
                            Save Schedule
                        </span>
                        <span x-show="isSaving" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Saving...
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Details Modal -->
<div id="viewDetailsModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl">
        <div class="bg-white border-b border-gray-200 px-6 py-4 rounded-t-xl">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-900">Backup Details</h2>
                <button onclick="closeModal('viewDetailsModal')" class="p-1 text-gray-400 hover:text-gray-600">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>
        </div>

        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start justify-between pb-4 border-b border-gray-200">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Backup Name</p>
                        <p class="text-base font-semibold text-gray-900" id="vd_name">XaliyePro_Full_Backup</p>
                    </div>
                    <span
                        class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                        Success
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Backup Type</p>
                        <p class="text-sm font-semibold text-gray-900" id="vd_type">Full Backup</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">File Size</p>
                        <p class="text-sm font-semibold text-gray-900" id="vd_size">482 MB</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Created Date</p>
                        <p class="text-sm font-semibold text-gray-900" id="vd_date">Feb 27, 2025</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Created Time</p>
                        <p class="text-sm font-semibold text-gray-900" id="vd_time">02:00 AM</p>
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-3 pt-6 border-t border-gray-200 mt-6">
                <button onclick="closeModal('viewDetailsModal')"
                    class="flex-1 px-6 py-3 border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Close
                </button>
                <a href="#" id="vd_download"
                    class="flex-1 px-6 py-3 bg-blue-600 text-white rounded-lg text-sm font-semibold hover:bg-blue-700 transition-colors inline-flex items-center justify-center gap-2">
                    <i data-lucide="download" class="w-4 h-4"></i>
                    Download Backup
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Delete Backup Modal -->
<div id="deleteBackupModal"
    class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[100] p-4 modal-backdrop">
    <div class="bg-white rounded-xl shadow-xl w-full max-w-md">
        <div class="p-6">
            <div class="flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mx-auto mb-4">
                <i data-lucide="trash-2" class="w-8 h-8 text-red-600"></i>
            </div>
            <h3 class="text-xl font-bold text-gray-900 text-center mb-2">Delete Backup</h3>
            <p class="text-sm text-gray-600 text-center mb-6">Are you sure you want to delete this backup? This action
                cannot be undone.</p>

            <div class="flex flex-col sm:flex-row items-center gap-3">
                <button onclick="closeModal('deleteBackupModal')"
                    class="w-full sm:w-auto px-6 py-3 border-2 border-gray-300 rounded-lg text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <form action="#" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="w-full px-6 py-3 bg-red-600 text-white rounded-lg text-sm font-semibold hover:bg-red-700 transition-colors">
                        Delete Backup
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
