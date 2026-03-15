@section('title', 'Manage Employees - XaliyePro')

@extends('admin.admin_master')
@section('admin')
    <div x-data="{
                                                        showAddModal: false,
                                                        showEditModal: false,
                                                        showDetailsModal: false,
                                                        isSaving: false,
                                                        imagePreview: null,
                                                        editImagePreview: null,
                                                        currentEmployee: {
                                                            id: '',
                                                            employee_id: '',
                                                            name: '',
                                                            email: '',
                                                            profile_image: '',
                                                            phone: '',
                                                            department_id: '',
                                                            designation: '',
                                                            branch_id: '',
                                                            join_date: '',
                                                            date_of_birth: '',
                                                            gender: '',
                                                            salary: '',
                                                            status: 'active',
                                                            address: '',
                                                            emergency_contact_name: '',
                                                            emergency_contact_phone: '',
                                                            emergency_contact_relationship: ''
                                                        },
                                                        openEditModal(employee) {
                                                            this.currentEmployee = { ...employee };
                                                            this.editImagePreview = null;
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
                                                        },
                                                        handleImagePreview(event, type) {
                                                            const file = event.target.files[0];
                                                            if (file) {
                                                                const reader = new FileReader();
                                                                reader.onload = (e) => {
                                                                    if (type === 'add') {
                                                                        this.imagePreview = e.target.result;
                                                                    } else {
                                                                        this.editImagePreview = e.target.result;
                                                                    }
                                                                };
                                                                reader.readAsDataURL(file);
                                                            }
                                                        }
                                                    }">
        <!-- Page Header -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Employee Management</h1>
                <p class="text-gray-500 mt-1">Manage employee records and information</p>
            </div>
            <button @click="showAddModal = true; imagePreview = null"
                class="flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all">
                <i data-lucide="plus" class="w-4 h-4 text-white"></i>
                Add Employee
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Employees',
                'value' => number_format($stats['total']),
                'icon' => 'users',
                'subtitle' => 'Onboarded Staff'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active Duty',
                'value' => number_format($stats['active']),
                'icon' => 'user-check',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'trendValue' => 'Online',
                'trendColor' => 'text-emerald-600',
                'trendIcon' => 'check-circle',
                'subtitle' => 'Currently working'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Leave/Rest',
                'value' => number_format($stats['on_leave']),
                'icon' => 'calendar-off',
                'color' => '#F59E0B',
                'iconBg' => 'bg-amber-500',
                'iconShadow' => 'shadow-amber-100',
                'trendValue' => 'Away',
                'trendColor' => 'text-amber-600',
                'trendIcon' => 'clock',
                'subtitle' => 'Temporarily away'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Units',
                'value' => number_format($stats['departments']),
                'icon' => 'briefcase',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
                'trendValue' => 'Active',
                'trendColor' => 'text-purple-600',
                'trendIcon' => 'layers',
                'subtitle' => 'Specialized teams'
            ])
        </div>

        <!-- All Employees Part -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden mt-6">
            <!-- Search and Filter Bar -->
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50/30">
                <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <form action="{{ route('settings.employees.index') }}" method="GET"
                        class="flex flex-1 flex-wrap items-center gap-4">
                        <div class="flex-1 min-w-[300px] relative">
                            <i data-lucide="search"
                                class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                            <input type="text" name="search" value="{{ request('search') }}"
                                placeholder="Search by name, ID or email..."
                                class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                        </div>

                        <select name="department_id" onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                            <option value="">All Departments</option>
                            @foreach ($departments as $dept)
                                <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->name }}
                                </option>
                            @endforeach
                        </select>

                        <select name="status" onchange="this.form.submit()"
                            class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                            <option value="on_leave" {{ request('status') == 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        </select>

                        @if (request()->anyFilled(['search', 'department_id', 'status']))
                            <a href="{{ route('settings.employees.index') }}"
                                class="px-3 py-2 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors text-sm font-medium"
                                title="Reset Filters">
                                Reset
                            </a>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Table -->
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="bg-gray-100/50 border-b border-gray-200">
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">EMPLOYEE</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">EMPLOYEE ID
                            </th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">DEPARTMENT
                            </th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">DESIGNATION
                            </th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">BRANCH</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">SALARY</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">JOIN DATE
                            </th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                STATUS</th>
                            <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">
                                ACTIONS</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @if (count($employees) > 0)
                            @foreach ($employees as $employee)
                                <tr class="hover:bg-gray-50 transition-colors group">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            @if ($employee->profile_image)
                                                <div class="w-10 h-10 rounded-lg overflow-hidden border border-gray-200">
                                                    <img src="{{ asset('storage/' . $employee->profile_image) }}" alt="{{ $employee->name }}" class="w-full h-full object-cover">
                                                </div>
                                            @else
                                                <div class="w-10 h-10 rounded-lg bg-[#28A375] flex items-center justify-center text-white text-xs font-bold">
                                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $employee->name }}</div>
                                                <div class="text-xs text-gray-500">{{ $employee->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-600 font-medium">
                                        {{ $employee->employee_id }}</td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                            {{ $employee->department->name }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-600">
                                        {{ $employee->designation }}</td>
                                    <td class="py-4 px-6 text-sm text-gray-600">
                                        {{ $employee->branch->name }}</td>
                                    <td class="py-4 px-6 text-sm font-semibold text-gray-900">
                                        ${{ number_format($employee->salary ?? 0, 0) }}
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-500">
                                        {{ \Carbon\Carbon::parse($employee->join_date)->format('M d, Y') }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                            {{ $employee->status === 'active' ? 'bg-green-100 text-green-700' : ($employee->status === 'on_leave' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ ucfirst(str_replace('_', ' ', $employee->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('settings.employees.show', $employee->id) }}"
                                                class="p-1.5 text-gray-400 hover:text-[#28A375] rounded transition-colors"
                                                title="View Profile">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <button @click="currentEmployee = {{ json_encode($employee) }}; showEditModal = true"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 rounded transition-colors"
                                                title="Edit Employee">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('settings.employees.destroy', $employee->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" @click="confirmDelete($el.closest('form'))"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 rounded transition-colors" title="Delete">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="9" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                            <i data-lucide="users" class="w-8 h-8 text-gray-300"></i>
                                        </div>
                                        <h3 class="text-sm font-bold text-gray-900">No employees found</h3>
                                        <p class="text-xs text-gray-500 mt-1">Try adjusting your filters or search terms.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($employees->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $employees->links() }}
                </div>
            @endif
        </div>

        <!-- Add Employee Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
                    @click="showAddModal = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showAddModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-200">

                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Add New Employee</h3>
                        <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form action="{{ route('settings.employees.store') }}" method="POST" @submit="isSaving = true" enctype="multipart/form-data">
                        @csrf
                        <div class="p-6">
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                    <!-- Profile Image Upload -->
                                    <div class="flex items-center gap-4 mb-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="w-16 h-16 rounded-xl bg-gray-200 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm">
                                            <template x-if="imagePreview">
                                                <img :src="imagePreview" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!imagePreview">
                                                <i data-lucide="user" class="w-8 h-8 text-gray-400"></i>
                                            </template>
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Profile Photo</label>
                                            <input type="file" name="profile_image" accept="image/*"
                                                @change="handleImagePreview($event, 'add')"
                                                x-ref="profile_image"
                                                class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#28A375]/10 file:text-[#28A375] hover:file:bg-[#28A375]/20 transition-all">
                                            <div class="flex items-center justify-between mt-1">
                                                <p class="text-[10px] text-gray-400">PNG, JPG up to 2MB</p>
                                                <button type="button" x-show="imagePreview" 
                                                    @click="imagePreview = null; $refs.profile_image.value = null" 
                                                    class="text-[10px] text-red-500 hover:underline">Remove</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Employee ID <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="hash" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="text" name="employee_id" required placeholder="EMP-001"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="text" name="name" required placeholder="John Doe"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date of Birth</label>
                                            <div class="relative">
                                                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <input type="date" name="date_of_birth"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                                            <div class="relative">
                                                <i data-lucide="users" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="gender"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="email" name="email" required placeholder="john@example.com"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="tel" name="phone" required placeholder="+1 234 567 890"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Department <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="briefcase" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="department_id" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="">Select Department</option>
                                                    @foreach ($departments as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Designation <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="award" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="text" name="designation" required placeholder="e.g. Sales Manager"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Branch <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="branch_id" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="">Select Branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Monthly Salary</label>
                                            <div class="relative">
                                                <i data-lucide="dollar-sign" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="number" name="salary" step="0.01" placeholder="0.00"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Join Date <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <input type="date" name="join_date" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="activity" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="status" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                    <option value="on_leave">On Leave</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Residence Address</label>
                                        <div class="relative">
                                            <i data-lucide="map" class="absolute left-3 top-4 w-4 h-4 text-gray-400"></i>
                                            <textarea name="address" rows="2" placeholder="Enter full address..."
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all resize-none"></textarea>
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-gray-50">
                                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Emergency Contact Documentation</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kin Full Name</label>
                                                <div class="relative">
                                                    <i data-lucide="user-plus" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                    <input type="text" name="emergency_contact_name" placeholder="Full legal name"
                                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Relationship</label>
                                                <div class="relative">
                                                    <i data-lucide="heart" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                    <input type="text" name="emergency_contact_relationship" placeholder="e.g. Spouse / Parent"
                                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Emergency Line</label>
                                            <div class="relative">
                                                <i data-lucide="phone-call" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="tel" name="emergency_contact_phone" placeholder="Contact number"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                    <button type="submit" :disabled="isSaving"
                                        class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                        <span x-show="!isSaving">Add Employee</span>
                                        <div x-show="isSaving" class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span>Processing...</span>
                                        </div>
                                    </button>
                                    <button @click="showAddModal = false" type="button" :disabled="isSaving"
                                        class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Employee Modal -->
            <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
                        @click="showEditModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-100">

                        <div
                            class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                            <h2 class="text-xl font-bold text-gray-900">Edit Employee</h2>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i data-lucide="x" class="w-6 h-6"></i>
                            </button>
                        </div>

                        <form :action="'{{ route('settings.employees.update', ':id') }}'.replace(':id', currentEmployee.id)"
                            method="POST" @submit="isSaving = true" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            <div class="p-6">
                                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                    <!-- Profile Image Edit -->
                                    <div class="flex items-center gap-4 mb-4 p-4 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="w-16 h-16 rounded-xl bg-gray-200 flex items-center justify-center overflow-hidden border-2 border-white shadow-sm">
                                            <template x-if="editImagePreview">
                                                <img :src="editImagePreview" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!editImagePreview && currentEmployee.profile_image">
                                                <img :src="'{{ asset('storage') }}/' + currentEmployee.profile_image" class="w-full h-full object-cover">
                                            </template>
                                            <template x-if="!editImagePreview && !currentEmployee.profile_image">
                                                <i data-lucide="user" class="w-8 h-8 text-gray-400"></i>
                                            </template>
                                        </div>
                                        <div class="flex-1">
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Change Profile Photo</label>
                                            <input type="file" name="profile_image" accept="image/*"
                                                @change="handleImagePreview($event, 'edit')"
                                                x-ref="edit_profile_image"
                                                class="block w-full text-xs text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-[#28A375]/10 file:text-[#28A375] hover:file:bg-[#28A375]/20 transition-all">
                                            <div class="flex items-center justify-between mt-1">
                                                <p class="text-[10px] text-gray-400">PNG, JPG up to 2MB</p>
                                                <button type="button" x-show="editImagePreview" 
                                                    @click="editImagePreview = null; $refs.edit_profile_image.value = null" 
                                                    class="text-[10px] text-red-500 hover:underline">Cancel Change</button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Employee ID <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="hash" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="text" name="employee_id" x-model="currentEmployee.employee_id" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Full Name <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="user" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="text" name="name" x-model="currentEmployee.name" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Email Address <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="mail" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="email" name="email" x-model="currentEmployee.email" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Phone Number <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="phone" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="tel" name="phone" x-model="currentEmployee.phone" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Date of Birth</label>
                                            <div class="relative">
                                                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <input type="date" name="date_of_birth" x-model="currentEmployee.date_of_birth"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Gender</label>
                                            <div class="relative">
                                                <i data-lucide="users" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="gender" x-model="currentEmployee.gender"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Department <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="briefcase" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="department_id" x-model="currentEmployee.department_id" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="">Select Department</option>
                                                    @foreach ($departments as $dept)
                                                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Designation <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="award" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="text" name="designation" x-model="currentEmployee.designation" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Branch <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="map-pin" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="branch_id" x-model="currentEmployee.branch_id" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="">Select Branch</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Monthly Salary</label>
                                            <div class="relative">
                                                <i data-lucide="dollar-sign" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="number" name="salary" x-model="currentEmployee.salary" step="0.01"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Join Date <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="calendar" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <input type="date" name="join_date" x-model="currentEmployee.join_date" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Status <span
                                                    class="text-red-500">*</span></label>
                                            <div class="relative">
                                                <i data-lucide="activity" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none"></i>
                                                <select name="status" x-model="currentEmployee.status" required
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all outline-none appearance-none">
                                                    <option value="active">Active</option>
                                                    <option value="inactive">Inactive</option>
                                                    <option value="on_leave">On Leave</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1.5">Residence Address</label>
                                        <div class="relative">
                                            <i data-lucide="map" class="absolute left-3 top-4 w-4 h-4 text-gray-400"></i>
                                            <textarea name="address" x-model="currentEmployee.address" rows="2"
                                                class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all resize-none"></textarea>
                                        </div>
                                    </div>

                                    <div class="pt-4 border-t border-gray-50">
                                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Emergency Contact Documentation</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Kin Full Name</label>
                                                <div class="relative">
                                                    <i data-lucide="user-plus" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                    <input type="text" name="emergency_contact_name" x-model="currentEmployee.emergency_contact_name" placeholder="Full legal name"
                                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                </div>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Relationship</label>
                                                <div class="relative">
                                                    <i data-lucide="heart" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                    <input type="text" name="emergency_contact_relationship" x-model="currentEmployee.emergency_contact_relationship" placeholder="e.g. Spouse / Parent"
                                                        class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1.5">Emergency Line</label>
                                            <div class="relative">
                                                <i data-lucide="phone-call" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400"></i>
                                                <input type="tel" name="emergency_contact_phone" x-model="currentEmployee.emergency_contact_phone" placeholder="Contact number"
                                                    class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]/30 focus:border-[#28A375] transition-all">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                    <button type="submit" :disabled="isSaving"
                                        class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                        <span x-show="!isSaving">Update Employee</span>
                                        <div x-show="isSaving" class="flex items-center gap-2">
                                            <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                            <span>Processing...</span>
                                        </div>
                                    </button>
                                    <button @click="showEditModal = false" type="button" :disabled="isSaving"
                                        class="flex-1 px-6 py-3 border border-gray-200 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            // No specific global init needed, handled by component x-init
        });

        function handleEditParam() {
            const urlParams = new URLSearchParams(window.location.search);
            const editId = urlParams.get('edit');
            if (editId) {
                // Find the employee data from the table or fetch it
                // Since this is a simple implementation, we can look for the button with that ID
                const editButton = document.querySelector(`[data-edit-id="${editId}"]`);
                if (editButton) {
                    editButton.click();
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            handleEditParam();
            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
                        if (typeof lucide !== 'undefined') {
                            lucide.createIcons();
                        }
                    }
                });
            });

            // Watch for modal visibility changes
            document.querySelectorAll('[x-show]').forEach(el => {
                observer.observe(el, { attributes: true });
            });
        });
    </script>
@endpush
