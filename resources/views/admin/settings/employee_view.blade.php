@extends('admin.admin_master')

@section('title', 'View Employee - XaliyePro')

@section('admin')
    <div x-data="{
                        showEditModal: false,
                        isSaving: false,
                        currentEmployee: {
                            id: '{{ $employee->id }}',
                            employee_id: '{{ $employee->employee_id }}',
                            name: '{{ $employee->name }}',
                            email: '{{ $employee->email }}',
                            phone: '{{ $employee->phone }}',
                            department_id: '{{ $employee->department_id }}',
                            designation: '{{ $employee->designation }}',
                            branch_id: '{{ $employee->branch_id }}',
                            join_date: '{{ $employee->join_date->format('Y-m-d') }}',
                            date_of_birth: '{{ $employee->date_of_birth ? $employee->date_of_birth->format('Y-m-d') : '' }}',
                            gender: '{{ $employee->gender }}',
                            salary: '{{ $employee->salary }}',
                            status: '{{ $employee->status }}',
                            address: '{{ $employee->address }}',
                            emergency_contact_name: '{{ $employee->emergency_contact_name }}',
                            emergency_contact_phone: '{{ $employee->emergency_contact_phone }}',
                            emergency_contact_relationship: '{{ $employee->emergency_contact_relationship }}'
                        }
                    }">
        <div class="space-y-6">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-6">
                <div class="flex items-center gap-4">
                    <a href="{{ route('settings.employees.index') }}"
                        class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                        <i data-lucide="arrow-left" class="w-5 h-5"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $employee->name }}</h1>
                        <p class="text-gray-500 mt-0.5 text-sm">Personnel Profile — <span
                                class="font-medium text-gray-700">{{ $employee->employee_id }}</span></p>
                    </div>
                </div>
                <div class="flex gap-3">
                    <button @click="showEditModal = true"
                        class="flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white rounded-lg text-sm font-medium hover:bg-black transition-all">
                        <i data-lucide="edit-2" class="w-4 h-4"></i>
                        Edit Profile
                    </button>
                </div>
            </div>

            <!-- Employee Information Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Left Column: Primary Profile -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Personnel Identity Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                            <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                                <i data-lucide="user-round" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">General Information</h2>
                                <p class="text-xs text-gray-500">Core personal details and identification</p>
                            </div>
                        </div>

                        <div class="p-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                                <div>
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Employee ID</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->employee_id }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Full Name</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->name }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email Address</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->email }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone Number</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->phone }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Date of Birth</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->date_of_birth ? $employee->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Gender</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900 capitalize">{{ $employee->gender ?? 'N/A' }}</p>
                                </div>
                                <div class="md:col-span-2">
                                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Residence Address</label>
                                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->address ?? 'N/A' }}</p>
                                </div>
                            </div>

                            <!-- Emergency Contact Section -->
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <h3 class="text-sm font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <i data-lucide="life-buoy" class="w-4 h-4 text-[#28A375]"></i>
                                    Emergency Contact Information
                                </h3>
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <div>
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Contact Name</label>
                                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->emergency_contact_name ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Relationship</label>
                                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->emergency_contact_relationship ?? 'N/A' }}</p>
                                    </div>
                                    <div>
                                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone Number</label>
                                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->emergency_contact_phone ?? 'N/A' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Access & Rights Matrix -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                        <div class="w-10 h-10 bg-amber-500 rounded-lg flex items-center justify-center">
                            <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold text-gray-900">Access Matrix</h2>
                            <p class="text-xs text-gray-500">System permissions and authorization levels</p>
                        </div>
                    </div>

                    <div class="p-5">
                            @if ($employee->user && $employee->user->role)
                                @php
                                    $permissionsByModule = $employee->user->role->permissions->groupBy('module');
                                @endphp

                                @if ($permissionsByModule->count() > 0)
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach ($permissionsByModule as $module => $module_perms)
                                            @php /** @var string $module */ /** @var \Illuminate\Database\Eloquent\Collection $module_perms */ @endphp
                                        <div class="space-y-3">
                                                <div class="flex items-center justify-between pb-2 border-b border-gray-100">
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-2 h-2 rounded-full bg-emerald-500/20 flex items-center justify-center">
                                                            <div class="w-1 h-1 rounded-full bg-emerald-500"></div>
                                                        </div>
                                                        <span class="text-xs font-semibold text-gray-900 uppercase tracking-widest">{{ $module }}</span>
                                                    </div>
                                                    <span class="text-[10px] font-bold text-gray-400 bg-gray-50 px-2 py-0.5 rounded-full">{{ count($module_perms) }} Actions</span>
                                                </div>
                                                <div class="flex flex-wrap gap-1.5">
                                                    @foreach ($module_perms as $perm)
                                                        @php
                                                            $permissionName = is_object($perm) ? $perm->name : (string)$perm;
                                                        @endphp
                                                        <span class="px-2 py-0.5 bg-gray-50 text-gray-600 text-[10px] font-medium border border-gray-200 rounded lowercase">
                                                            {{ str_replace('_', ' ', str_replace($module . '.', '', $permissionName)) }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="py-12 text-center bg-gray-50/50 rounded-lg border border-dashed border-gray-200">
                                        <p class="text-sm font-bold text-gray-400">No operational permissions defined for this role.</p>
                                    </div>
                                @endif
                            @else
                                <div class="flex flex-col items-center justify-center py-16 text-center bg-gray-50/30 rounded-lg border-2 border-dashed border-gray-100">
                                    <div class="w-16 h-16 bg-white rounded-lg flex items-center justify-center shadow-lg border border-gray-50 mb-6">
                                        <i data-lucide="shield-off" class="w-8 h-8 text-gray-200"></i>
                                    </div>
                                    <h4 class="text-base font-bold text-gray-900 tracking-tight">Restricted System Access</h4>
                                    <p class="text-xs text-gray-400 mt-2 max-w-[280px] font-medium leading-relaxed">This employee does not have an active system account. No permissions have been inherited or assigned.</p>
                                    <a href="{{ route('settings.users.index') }}" class="mt-6 px-6 py-2 bg-gray-900 text-white text-[11px] font-bold uppercase tracking-widest rounded-xl hover:bg-black transition-all">Enable Access</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-8">
                    <!-- Employment Profile Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                        <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                            <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                                <i data-lucide="briefcase" class="w-5 h-5 text-white"></i>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold text-gray-900">Employment</h2>
                                <p class="text-xs text-gray-500">Designation & Placement</p>
                            </div>
                        </div>

                        <div class="p-5 space-y-5">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Unit / Department</label>
                                <p class="mt-1">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                        {{ $employee->department?->name ?? 'N/A' }}
                                    </span>
                                </p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Professional Title</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $employee->designation }}</p>
                            </div>

                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Monthly Remuneration</label>
                                <p class="mt-1 text-lg font-bold text-[#28A375]">${{ number_format($employee->salary ?? 0, 0) }}</p>
                            </div>

                            <div class="pt-5 border-t border-gray-200 space-y-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Branch</span>
                                    <span class="text-sm font-medium text-gray-900 flex items-center gap-1.5">
                                        <i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-500"></i>
                                        {{ $employee->branch?->name ?? 'N/A' }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Join Date</span>
                                    <span class="text-sm font-medium text-gray-900 flex items-center gap-1.5">
                                        <i data-lucide="calendar" class="w-3.5 h-3.5 text-emerald-500"></i>
                                        {{ \Carbon\Carbon::parse($employee->join_date)->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Integration Card -->
                    <div class="bg-gray-900 rounded-lg shadow-lg overflow-hidden text-white relative">
                        <div class="p-5 relative z-10">
                            <div class="flex items-center gap-3 mb-6">
                                <div class="w-10 h-10 bg-white/10 rounded-lg flex items-center justify-center border border-white/10">
                                    <i data-lucide="shield" class="w-5 h-5 text-emerald-400"></i>
                                </div>
                                <div>
                                    <h2 class="text-lg font-semibold tracking-tight">User Account</h2>
                                    <p class="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Authentication State</p>
                                </div>
                            </div>

                            @if ($employee->user)
                                <div class="space-y-5">
                                    <div class="flex items-center gap-3 p-3 bg-white/5 rounded-lg border border-white/5">
                                        <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center text-white text-xs font-bold">
                                            {{ strtoupper(substr($employee->user->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold">{{ $employee->user->name }}</p>
                                            <p class="text-xs text-gray-400">{{ $employee->user->email }}</p>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="p-2.5 bg-white/5 rounded-lg border border-white/5">
                                            <p class="text-[9px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Role</p>
                                            <p class="text-xs font-bold text-emerald-400">{{ $employee->user->role?->name ?? 'N/A' }}</p>
                                        </div>
                                        <div class="p-2.5 bg-white/5 rounded-lg border border-white/5">
                                            <p class="text-[9px] font-semibold text-gray-500 uppercase tracking-wider mb-1">Status</p>
                                            <p class="text-xs font-bold text-white capitalize">{{ $employee->user->status }}</p>
                                        </div>
                                    </div>

                                    @if ($employee->user->last_login_at)
                                        <div class="flex items-center gap-2 text-[10px] text-gray-500 font-semibold uppercase tracking-wider">
                                            <i data-lucide="clock" class="w-3 h-3"></i>
                                            Last seen {{ $employee->user->last_login_at->diffForHumans() }}
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div class="flex flex-col items-center justify-center py-4 text-center">
                                    <p class="text-xs text-gray-400 mb-4">No associated system account.</p>
                                    <a href="{{ route('settings.users.index') }}" class="w-full px-4 py-2 bg-emerald-600 text-white text-xs font-bold uppercase tracking-wider rounded-lg hover:bg-emerald-500 transition-all text-center">Initialize Account</a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stats Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="calendar" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($employee->join_date)->diffInMonths() }}</p>
                        <p class="text-xs text-gray-500">Months on Duty</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="award" class="w-6 h-6 text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900">{{ \Carbon\Carbon::parse($employee->join_date)->format('Y') }}</p>
                        <p class="text-xs text-gray-500">Commission Year</p>
                    </div>
                </div>
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                        <i data-lucide="check-circle" class="w-6 h-6 text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 capitalize">{{ $employee->status }}</p>
                        <p class="text-xs text-gray-500">Operational Status</p>
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="mt-6 flex items-center gap-6 text-xs text-gray-400">
                <div class="flex items-center gap-1.5">
                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                    <span>Created: {{ $employee->created_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                    <span>Last Updated: {{ $employee->updated_at->format('M d, Y \a\t h:i A') }}</span>
                </div>
            </div>

            <!-- Edit Employee Modal -->
            <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
                role="dialog" aria-modal="true">
                <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                    <div x-show="showEditModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100" class="fixed inset-0 bg-gray-900/40 backdrop-blur-md"
                        @click="showEditModal = false"></div>

                    <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                    <div x-show="showEditModal" x-transition:enter="ease-out duration-300"
                        x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                        class="inline-block align-middle bg-white rounded-lg text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-2xl sm:w-full border border-gray-200">

                        <div
                            class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                            <h2 class="text-xl font-bold text-gray-900">Edit Employee</h2>
                            <button @click="showEditModal = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                                <i data-lucide="x" class="w-6 h-6"></i>
                            </button>
                        </div>

                        <form :action="'{{ route('settings.employees.update', ':id') }}'.replace(':id', currentEmployee.id)"
                            method="POST" @submit="isSaving = true">
                            @csrf
                            @method('PUT')
                            <div class="p-6">
                                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="employee_id" x-model="currentEmployee.employee_id" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="name" x-model="currentEmployee.name" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                        </div>
                                    </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Email Address <span
                                                        class="text-red-500">*</span></label>
                                                <input type="email" name="email" x-model="currentEmployee.email" required
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number <span
                                                        class="text-red-500">*</span></label>
                                                <input type="tel" name="phone" x-model="currentEmployee.phone" required
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                            </div>
                                        </div>

                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Date of Birth</label>
                                                <input type="date" name="date_of_birth" x-model="currentEmployee.date_of_birth"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Gender Identity</label>
                                                <select name="gender" x-model="currentEmployee.gender"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                                    <option value="">Select Gender</option>
                                                    <option value="male">Male</option>
                                                    <option value="female">Female</option>
                                                    <option value="other">Other</option>
                                                </select>
                                            </div>
                                        </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Department <span
                                                    class="text-red-500">*</span></label>
                                            <select name="department_id" x-model="currentEmployee.department_id" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                                <option value="">Select Department</option>
                                                @foreach ($departments as $dept)
                                                    <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Designation <span
                                                    class="text-red-500">*</span></label>
                                            <input type="text" name="designation" x-model="currentEmployee.designation" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Branch <span
                                                    class="text-red-500">*</span></label>
                                            <select name="branch_id" x-model="currentEmployee.branch_id" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                                <option value="">Select Branch</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Salary</label>
                                            <div class="relative">
                                                <span
                                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">$</span>
                                                <input type="number" name="salary" x-model="currentEmployee.salary" step="0.01"
                                                    class="w-full pl-7 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Join Date <span
                                                    class="text-red-500">*</span></label>
                                            <input type="date" name="join_date" x-model="currentEmployee.join_date" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                                    class="text-red-500">*</span></label>
                                            <select name="status" x-model="currentEmployee.status" required
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all outline-none">
                                                <option value="active">Active</option>
                                                <option value="inactive">Inactive</option>
                                                <option value="on_leave">On Leave</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Residence Address</label>
                                        <textarea name="address" x-model="currentEmployee.address" rows="2"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all resize-none"></textarea>
                                    </div>

                                    <div class="pt-4 border-t border-gray-50">
                                        <h4 class="text-xs font-black text-gray-400 uppercase tracking-widest mb-4">Emergency Contact Documentation</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Kin Full Name</label>
                                                <input type="text" name="emergency_contact_name" x-model="currentEmployee.emergency_contact_name" placeholder="Full legal name"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-1">Relationship</label>
                                                <input type="text" name="emergency_contact_relationship" x-model="currentEmployee.emergency_contact_relationship" placeholder="e.g. Spouse / Parent"
                                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
                                            </div>
                                        </div>
                                        <div class="mt-4">
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Line</label>
                                            <input type="tel" name="emergency_contact_phone" x-model="currentEmployee.emergency_contact_phone" placeholder="Contact number"
                                                class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent transition-all">
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
    </div>

        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
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
                        observer.observe(el, {
                            attributes: true
                        });
                    });
                });
            </script>
        @endpush
@endsection
