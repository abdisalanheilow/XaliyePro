@section('title', 'Manage Departments - XaliyePro')

@extends('admin.admin_master')
@section('admin')
    <div x-data="{
                                        showAddModal: false,
                                        showEditModal: false,
                                        isSaving: false,
                                        currentDepartment: {
                                            id: '',
                                            name: '',
                                            status: 'active'
                                        },
                                        openEditModal(department) {
                                            this.currentDepartment = { ...department };
                                            this.showEditModal = true;
                                        },
                                        confirmDelete(form) {
                                            Swal.fire({
                                                title: 'Are you sure?',
                                                text: 'You will not be able to revert this!',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#28A375',
                                                cancelButtonColor: '#d33',
                                                confirmButtonText: 'Yes, delete it!'
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    form.submit();
                                                }
                                            });
                                        }
                                    }">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Department Management</h1>
                <p class="text-gray-500 mt-1">Configure and organize company departments</p>
            </div>
            <button @click="showAddModal = true"
                class="flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all">
                <i data-lucide="plus" class="w-4 h-4 text-white"></i>
                Add Department
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Departments',
                'value' => number_format($stats['total']),
                'icon' => 'briefcase',
                'subtitle' => $stats['active'] . ' active'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Employees',
                'value' => number_format($stats['total_employees']),
                'icon' => 'users',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'subtitle' => 'Across all units'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active Units',
                'value' => number_format($stats['active']),
                'icon' => 'check-circle',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'trendValue' => 'Online',
                'subtitle' => 'Operational units'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Inactive Units',
                'value' => number_format($stats['inactive']),
                'icon' => 'alert-circle',
                'color' => '#F59E0B',
                'iconBg' => 'bg-amber-500',
                'iconShadow' => 'shadow-amber-100',
                'subtitle' => 'Currently paused'
            ])
        </div>

        <!-- Search & Filter Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('settings.departments.index') }}" method="GET" class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <i data-lucide="search"
                        class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search departments by name..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                </div>

                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] bg-white outline-none">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </form>
        </div>

        <!-- Departments List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100/50 border-b border-gray-200">
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">DEPARTMENT
                                NAME</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                EMPLOYEES</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                STATUS</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">CREATED AT
                            </th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @if (count($departments) > 0)
                            @foreach ($departments as $dept)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-lg bg-emerald-50 flex items-center justify-center">
                                                <i data-lucide="briefcase" class="w-5 h-5 text-emerald-600"></i>
                                            </div>
                                            <span class="text-sm font-semibold text-gray-900">{{ $dept->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                                            {{ $dept->employees_count }} Members
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $dept->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($dept->status) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-500">
                                        {{ $dept->created_at->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center gap-2">
                                            <a href="{{ route('settings.departments.show', $dept->id) }}"
                                                class="p-1.5 text-gray-400 hover:text-emerald-600 rounded transition-colors"
                                                title="View Details">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <button @click="openEditModal({{ json_encode($dept) }})"
                                                class="p-1.5 text-gray-400 hover:text-[#28A375] rounded transition-colors"
                                                title="Edit Department">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('settings.departments.destroy', $dept->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="confirmDelete($el.closest('form'))"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 rounded transition-colors"
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
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i data-lucide="briefcase" class="w-8 h-8 text-gray-300"></i>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">No departments found</h3>
                                        <p class="text-xs text-gray-500 mt-1">Get started by adding your first department.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($departments->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $departments->links() }}
                </div>
            @endif
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
                    @click="showAddModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-200">

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Add New Department</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <form action="{{ route('settings.departments.store') }}" method="POST" @submit="isSaving = true">
                        @csrf
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Department Name <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="briefcase"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input type="text" name="name" required placeholder="e.g. Sales, Marketing, IT"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="activity"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <select name="status" required
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex gap-3">
                            <button type="submit" :disabled="isSaving"
                                class="flex-1 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="!isSaving">Save Department</span>
                                <div x-show="isSaving" class="flex items-center gap-2">
                                    <div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                                    <span>Saving...</span>
                                </div>
                            </button>
                            <button @click="showAddModal = false" type="button" :disabled="isSaving"
                                class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-white transition-all">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
                    @click="showEditModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-md sm:w-full border border-gray-200">

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                        <h3 class="text-lg font-bold text-gray-900">Edit Department</h3>
                        <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <form :action="'{{ route('settings.departments.update', ':id') }}'.replace(':id', currentDepartment.id)"
                        method="POST" @submit="isSaving = true">
                        @csrf
                        @method('PUT')
                        <div class="p-6 space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Department Name <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="briefcase"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <input type="text" name="name" x-model="currentDepartment.name" required
                                        placeholder="e.g. Sales"
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span
                                        class="text-red-500">*</span></label>
                                <div class="relative">
                                    <i data-lucide="activity"
                                        class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                    <select name="status" x-model="currentDepartment.status" required
                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 flex gap-3">
                            <button type="submit" :disabled="isSaving"
                                class="flex-1 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                                <span x-show="!isSaving">Update Department</span>
                                <div x-show="isSaving" class="flex items-center gap-2">
                                    <div class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></div>
                                    <span>Updating...</span>
                                </div>
                            </button>
                            <button @click="showEditModal = false" type="button" :disabled="isSaving"
                                class="flex-1 px-4 py-2 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-white transition-all">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
