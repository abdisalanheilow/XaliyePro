@extends('admin.admin_master')
@section('title', 'Backup & Restore - XaliyePro')
@section('admin')
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Backup & Restore</h1>
        <p class="text-sm text-gray-500 mt-1">Protect your data with automated backups and easy restore options</p>
    </div>

    <!-- Quick Action Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Create Backup Card -->
        <div class="bg-gradient-to-br from-[#28A375] to-[#229967] rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Create New Backup</h3>
                    <p class="text-green-100 text-sm">Backup your entire system data securely</p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="database" class="w-8 h-8"></i>
                </div>
            </div>
            <button onclick="openModal('createBackupModal')"
                class="w-full bg-white text-[#28A375] px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
                Create Backup Now
            </button>
        </div>

        <!-- Restore Backup Card -->
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl shadow-lg p-8 text-white">
            <div class="flex items-start justify-between mb-6">
                <div>
                    <h3 class="text-2xl font-bold mb-2">Restore from Backup</h3>
                    <p class="text-blue-100 text-sm">Recover your data from a previous backup</p>
                </div>
                <div class="w-16 h-16 bg-white/20 rounded-xl flex items-center justify-center">
                    <i data-lucide="refresh-ccw" class="w-8 h-8"></i>
                </div>
            </div>
            <button onclick="openModal('restoreModal')"
                class="w-full bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-gray-50 transition-colors flex items-center justify-center gap-2">
                <i data-lucide="upload" class="w-5 h-5"></i>
                Restore Backup
            </button>
        </div>
    </div>

    <!-- Storage Info & Settings -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Storage Usage -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Storage Usage</h3>
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="hard-drive" class="w-5 h-5 text-purple-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <div class="flex items-baseline justify-between mb-2">
                        @php $storagePercent = min(100, round(($totalSizeMB / 10240) * 100, 2)); @endphp
                        <span
                            class="text-3xl font-bold text-gray-900">{{ $totalSizeMB > 1024 ? round($totalSizeMB / 1024, 2) . ' GB' : $totalSizeMB . ' MB' }}</span>
                        <span class="text-sm text-gray-500">of 10 GB</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div class="bg-purple-600 h-3 rounded-full" style="width: {{ $storagePercent }}%"></div>
                    </div>
                </div>
                <div class="pt-4 border-t border-gray-200">
                    <p class="text-xs text-gray-500 mb-2">Backup Files: <strong class="text-gray-900">{{ count($backups) }}
                            files</strong></p>
                    <p class="text-xs text-gray-500">Last Backup: <strong
                            class="text-gray-900">{{ count($backups) > 0 ? \Carbon\Carbon::parse($backups[0]['date'] . ' ' . $backups[0]['time'])->diffForHumans() : 'Never' }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Automatic Backup Status -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Auto Backup</h3>
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="clock" class="w-5 h-5 text-green-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Status</span>
                    @if (($schedule['status'] ?? 'inactive') == 'active')
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                        <i data-lucide="check-circle" class="w-3.5 h-3.5"></i>
                        Active
                    </span>
                    @else
                    <span
                        class="inline-flex items-center gap-1.5 px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-semibold">
                        <i data-lucide="info" class="w-3.5 h-3.5"></i>
                        Manual Only
                    </span>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Frequency</span>
                    @if (($schedule['status'] ?? 'inactive') == 'active')
                    <span class="text-sm font-semibold text-gray-900 capitalize">{{ $schedule['frequency'] ?? 'daily' }}</span>
                    @else
                    <span class="text-sm font-semibold text-gray-400">Not Configured</span>
                    @endif
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-sm text-gray-600">Retention</span>
                    @if (($schedule['status'] ?? 'inactive') == 'active')
                    <span class="text-sm font-semibold text-gray-900">{{ str_replace('keep-', '', $schedule['retention'] ?? 'keep-30') }} Days</span>
                    @else
                    <span class="text-sm font-semibold text-gray-400">N/A</span>
                    @endif
                </div>
                <button onclick="openModal('scheduleModal')"
                    class="w-full mt-2 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-200 transition-colors">
                    Configure Auto Schedule
                </button>
            </div>
        </div>

        <!-- Backup Location -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base font-bold text-gray-900">Storage Location</h3>
                <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                    <i data-lucide="folder" class="w-5 h-5 text-blue-600"></i>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="flex items-start gap-3 cursor-pointer mb-3">
                        <input type="radio" name="storage" checked disabled
                            class="mt-1 w-4 h-4 text-[#28A375] border-gray-300 focus:ring-[#28A375]">
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Local Server</p>
                            <p class="text-xs text-gray-500">storage/app/backups/</p>
                        </div>
                    </label>
                </div>
                <div class="bg-blue-50 border border-blue-100 rounded-lg p-3">
                    <div class="flex items-start gap-2">
                        <i data-lucide="info" class="w-4 h-4 text-blue-600 mt-0.5"></i>
                        <p class="text-xs text-blue-800">
                            Currently, backups are strictly stored on the local server. Cloud options will be available in
                            future modules.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup History -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">Backup History</h3>
                    <p class="text-sm text-gray-500 mt-1">View and manage all your backup files</p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="relative">
                        <i data-lucide="search"
                            class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                        <input type="text" placeholder="Search backups..."
                            class="pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                    <select
                        class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                        <option>Database Only</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Backup
                            Name</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date &
                            Time</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Size
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @if (count($backups) > 0)
                        @foreach ($backups as $backup)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="database" class="w-5 h-5 text-blue-600"></i>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $backup['name'] }}</p>
                                            <p class="text-xs text-gray-500">System backup</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium">
                                        <i data-lucide="layers" class="w-3 h-3"></i>
                                        {{ $backup['type'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-900">{{ $backup['date'] }}</p>
                                    <p class="text-xs text-gray-500">{{ $backup['time'] }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-900">{{ $backup['size'] }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-100 text-green-700 rounded-full text-xs font-semibold">
                                        <i data-lucide="check-circle" class="w-3 h-3"></i>
                                        {{ $backup['status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('settings.backup.download', $backup['name']) }}"
                                            class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                            title="Download">
                                            <i data-lucide="download" class="w-4 h-4"></i>
                                        </a>

                                        <form action="{{ route('settings.backup.destroy', $backup['name']) }}" method="POST"
                                            id="delete-backup-form-{{ $loop->index }}"
                                            class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" onclick="confirmDelete('delete-backup-form-{{ $loop->index }}')"
                                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No backups found. Create one to get started.
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
            <p class="text-sm text-gray-500">Showing <strong>{{ count($backups) }}</strong> backups</p>
        </div>
    </div>

    @include('admin.settings.modals.backup_modals')

@endsection

@push('scripts')
    <script>
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            // Re-initialize lucide icons inside modals
            lucide.createIcons();
        }

        function openViewDetailsModal(name, type, size, date, time, downloadUrl) {
            document.getElementById('vd_name').innerText = name;
            document.getElementById('vd_type').innerText = type;
            document.getElementById('vd_size').innerText = size;
            document.getElementById('vd_date').innerText = date;
            document.getElementById('vd_time').innerText = time;
            document.getElementById('vd_download').href = downloadUrl;
            openModal('viewDetailsModal');
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function startRestore() {
            closeModal('restoreConfirmModal');
            Swal.fire({
                title: 'Restoring...',
                text: 'Restore process started! This may take several minutes...',
                icon: 'info',
                timer: 3000,
                showConfirmButton: false
            });
        }

        // Close modal on outside click
        window.onclick = function (event) {
            if (event.target.classList.contains('modal-backdrop')) {
                event.target.classList.add('hidden');
            }
        }

        function confirmDelete(formId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this backup deletion!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById(formId).submit();
                }
            });
        }
    </script>
@endpush
