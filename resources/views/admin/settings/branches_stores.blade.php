@extends('admin.admin_master')

@section('title', 'Branches & Stores - XaliyePro')

@section('admin')
    <div x-data="{ 
                                                            expandedBranches: {{ request('search') ? $branches->pluck('id')->toJson() : '[]' }}, 
                                                            showAddBranch: false, 
                                                            showAddStore: false,
                                                            showEditBranch: false,
                                                            showEditStore: false,
                                                            currentBranch: { code: '{{ $nextBranchCode }}' },
                                                            currentStore: { code: '{{ $nextStoreCode }}' },
                                                            toggleBranch(id) {
                                                                if (this.expandedBranches.includes(id)) {
                                                                    this.expandedBranches = this.expandedBranches.filter(i => i !== id);
                                                                } else {
                                                                    this.expandedBranches.push(id);
                                                                }
                                                            },
                                                            editBranch(branch) {
                                                                this.currentBranch = JSON.parse(JSON.stringify(branch));
                                                                this.showEditBranch = true;
                                                            },
                                                            editStore(store) {
                                                                this.currentStore = JSON.parse(JSON.stringify(store));
                                                                this.showEditStore = true;
                                                            },
                                                            isSaving: false
                                                        }">
        {{-- Page Header --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Branches & Stores Management</h1>
                <p class="text-gray-500 mt-1">Manage all your business locations and stores</p>
            </div>
            <div class="flex gap-3">
                <button @click="showAddStore = true; currentStore = {branch_id: ''}"
                    class="flex items-center gap-2 px-4 py-2.5 bg-white border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-50">
                    <i data-lucide="store" class="w-4 h-4"></i>
                    Add Store
                </button>
                <button @click="showAddBranch = true"
                    class="flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Branch
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Branches',
                'value' => number_format($branches->count()),
                'icon' => 'building-2',
                'subtitle' => $branches->where('status', 'active')->count() . ' active'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Stores',
                'value' => number_format($branches->sum(fn($b) => $b->stores->count())),
                'icon' => 'store',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'subtitle' => 'Across all branches'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Total Employees',
                'value' => number_format($branches->sum(fn($b) => $b->stores->sum('employee_count'))),
                'icon' => 'users',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
                'subtitle' => 'All locations'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active Locations',
                'value' => number_format($branches->where('status', 'active')->count() + $branches->sum(fn($b) => $b->stores->where('status', 'active')->count())),
                'icon' => 'map-pin',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'trendValue' => 'Online',
                'subtitle' => 'Branches & Stores'
            ])
        </div>

        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('settings.branches.index') }}" method="GET" class="flex items-center gap-4">
                <div class="flex-1 relative">
                    <i data-lucide="search"
                        class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search branches or stores..."
                        class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent"
                        oninput="this.form.requestSubmit()">
                </div>
                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </form>
        </div>

        {{-- Branches List --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            @if (count($branches) > 0)
                @foreach ($branches as $branch)
                    <div class="border-b border-gray-200 last:border-0">
                        <div class="flex items-center gap-3 px-4 py-3 hover:bg-gray-50">
                            <button @click="toggleBranch({{ $branch->id }})"
                                class="text-gray-400 hover:text-gray-600 flex-shrink-0 transition-transform"
                                :class="expandedBranches.includes({{ $branch->id }}) ? 'rotate-90' : ''">
                                <i data-lucide="chevron-right" class="w-4 h-4"></i>
                            </button>

                            <div class="w-12 h-12 bg-[#28A375] rounded-full flex items-center justify-center flex-shrink-0">
                                <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                            </div>

                            <div class="flex-1 min-w-0 flex items-center gap-3">
                                <h3 class="font-semibold text-gray-900 text-sm">{{ $branch->name }}</h3>
                                <span class="text-xs text-gray-500">{{ $branch->code }}</span>
                                <span
                                    class="px-2 py-0.5 {{ $branch->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs font-medium rounded capitalize">{{ $branch->status }}</span>
                                <div class="flex items-center gap-1 text-xs text-gray-600">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                    <span>{{ $branch->city }}{{ $branch->state ? ', ' . $branch->state : '' }}</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-600">
                                    <i data-lucide="store" class="w-3.5 h-3.5"></i>
                                    <span>{{ $branch->stores->count() }} stores</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-600">
                                    <i data-lucide="users" class="w-3.5 h-3.5"></i>
                                    <span>{{ $branch->employees->count() }} employees</span>
                                </div>
                            </div>

                            <div class="flex items-center gap-1 flex-shrink-0">
                                <a href="{{ route('settings.branches.show', $branch->id) }}"
                                    class="p-1.5 text-gray-400 hover:text-[#28A375] rounded" title="View Details">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <button @click='editBranch(@json($branch))' class="p-1.5 text-gray-400 hover:text-[#28A375] rounded">
                                    <i data-lucide="edit-2" class="w-4 h-4"></i>
                                </button>
                                <form action="{{ route('settings.branches.destroy', $branch->id) }}" method="POST"
                                    onsubmit="return confirmDelete(this, 'Are you sure you want to delete this branch? All associated stores will be deleted.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-gray-400 hover:text-red-600 rounded">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Stores under Branch --}}
                        <div x-show="expandedBranches.includes({{ $branch->id }})" x-transition
                            class="bg-gray-50 border-t border-gray-100">
                            <div class="flex items-center gap-2 py-2 px-6 bg-gray-100/50">
                                <i data-lucide="store" class="w-4 h-4 text-gray-600"></i>
                                <span class="text-xs font-semibold text-gray-600 uppercase">Stores under {{ $branch->name }}</span>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="bg-gray-50/50 border-b border-gray-200">
                                            <th class="text-left py-2.5 px-6 text-xs font-semibold text-gray-500 uppercase">Store
                                                Name</th>
                                            <th class="text-left py-2.5 px-6 text-xs font-semibold text-gray-500 uppercase">Code
                                            </th>
                                            <th class="text-left py-2.5 px-6 text-xs font-semibold text-gray-500 uppercase">Status
                                            </th>
                                            <th class="text-left py-2.5 px-6 text-xs font-semibold text-gray-500 uppercase">Actions
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white">
                                        @if ($branch->stores->count() > 0)
                                            @foreach ($branch->stores as $store)
                                                <tr class="border-b border-gray-100 last:border-0 hover:bg-gray-50">
                                                    <td class="py-3 px-6 text-sm">
                                                        <div class="font-medium text-gray-900">{{ $store->name }}</div>
                                                        <div class="text-xs text-gray-500">{{ $store->address }}</div>
                                                    </td>
                                                    <td class="py-3 px-6 text-sm text-gray-600">{{ $store->code }}</td>
                                                    <td class="py-3 px-6">
                                                        <span
                                                            class="px-2.5 py-1 {{ $store->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-xs font-medium rounded capitalize">{{ $store->status }}</span>
                                                    </td>
                                                    <td class="py-3 px-6">
                                                        <div class="flex items-center gap-1">
                                                            <button @click='editStore(@json($store))'
                                                                class="p-1.5 text-gray-400 hover:text-blue-600 rounded">
                                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                                            </button>
                                                            <form action="{{ route('settings.stores.destroy', $store->id) }}" method="POST"
                                                                onsubmit="return confirmDelete(this, 'Are you sure you want to delete this store?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit"
                                                                    class="p-1.5 text-gray-400 hover:text-red-600 rounded">
                                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4" class="py-4 text-center text-sm text-gray-500">No stores found for this
                                                    branch.</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Pagination --}}
                @if($branches->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                        {{ $branches->links() }}
                    </div>
                @endif
            @else
                <div class="py-12 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i data-lucide="building-2" class="w-8 h-8 text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">No Branches Yet</h3>
                    <p class="text-gray-500 mt-1">Get started by creating your first business branch.</p>
                    <button @click="showAddBranch = true"
                        class="mt-4 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967]">
                        Add First Branch
                    </button>
                </div>
            @endif
        </div>

        {{-- Add Branch Modal --}}
        <div x-show="showAddBranch" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddBranch" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" @click="showAddBranch = false">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showAddBranch" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full border border-gray-100">

                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Add New Branch</h3>
                        <button @click="showAddBranch = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form action="{{ route('settings.branches.store') }}" method="POST" @submit="isSaving = true">
                        @csrf
                        <div class="p-6">
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Branch Code <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="code" value="{{ old('code', $nextBranchCode) }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent @error('code') border-red-500 @enderror">
                                        @error('code') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Branch Name <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent @error('name') border-red-500 @enderror">
                                        @error('name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="address" value="{{ old('address') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent @error('address') border-red-500 @enderror">
                                        @error('address') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">City <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="city" value="{{ old('city') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel" name="phone" value="{{ old('phone') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" value="{{ old('email') }}"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch Manager</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                                        </div>
                                        <input list="employee-list" name="manager_name"
                                            placeholder="Search or select manager..."
                                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <datalist id="employee-list">
                                        @foreach ($employees ?? [] as $employee)
                                            <option value="{{ $employee->name }}">
                                        @endforeach
                                    </datalist>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                            class="text-red-500">*</span></label>
                                    <select name="status" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                <button type="submit" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                    <span x-show="!isSaving">Create Branch</span>
                                    <div x-show="isSaving" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Saving...</span>
                                    </div>
                                </button>
                                <button @click="showAddBranch = false" type="button" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showEditBranch" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditBranch" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity"
                    @click="showEditBranch = false"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showEditBranch" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full border border-gray-100">

                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Edit Branch</h3>
                        <button @click="showEditBranch = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form
                        :action="'{{ route('settings.branches.update', ['branch' => ':id']) }}'.replace(':id', currentBranch.id)"
                        method="POST" @submit="isSaving = true">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Branch Code <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="code" x-model="currentBranch.code" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Branch Name <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="name" x-model="currentBranch.name" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="address" x-model="currentBranch.address" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <div class="col-span-1">
                                        <label class="block text-sm font-medium text-gray-700 mb-1">City <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="city" x-model="currentBranch.city" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel" name="phone" x-model="currentBranch.phone"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                        <input type="email" name="email" x-model="currentBranch.email"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Branch Manager</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i data-lucide="search" class="h-4 w-4 text-gray-400"></i>
                                        </div>
                                        <input list="employee-list-edit" name="manager_name"
                                            x-model="currentBranch.manager_name" placeholder="Search or select manager..."
                                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <datalist id="employee-list-edit">
                                        @foreach ($employees ?? [] as $employee)
                                            <option value="{{ $employee->name }}">
                                        @endforeach
                                    </datalist>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                            class="text-red-500">*</span></label>
                                    <select name="status" x-model="currentBranch.status" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                <button type="submit" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                    <span x-show="!isSaving">Update Branch</span>
                                    <div x-show="isSaving" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Saving...</span>
                                    </div>
                                </button>
                                <button @click="showEditBranch = false" type="button" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showAddStore" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAddStore" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" @click="showAddStore = false">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showAddStore" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:max-w-xl sm:w-full border border-gray-100">

                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Add New Store</h3>
                        <button @click="showAddStore = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form action="{{ route('settings.stores.store') }}" method="POST" @submit="isSaving = true">
                        @csrf
                        <div class="p-6">
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Branch <span
                                            class="text-red-500">*</span></label>
                                    <select name="branch_id" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                        <option value="">Select Branch</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Store Code <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="code" value="{{ old('code', $nextStoreCode) }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent @error('code') border-red-500 @enderror">
                                        @error('code') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Store Name <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="name" value="{{ old('name') }}" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent @error('name') border-red-500 @enderror">
                                        @error('name') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="address" value="{{ old('address') }}" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent @error('address') border-red-500 @enderror">
                                    @error('address') <p class="text-red-500 text-[10px] mt-1">{{ $message }}</p> @enderror
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel" name="phone"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                                class="text-red-500">*</span></label>
                                        <select name="status" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                <button type="submit" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                    <span x-show="!isSaving">Create Store</span>
                                    <div x-show="isSaving" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Saving...</span>
                                    </div>
                                </button>
                                <button @click="showAddStore = false" type="button" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div x-show="showEditStore" x-cloak class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
            role="dialog" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showEditStore" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-md transition-opacity" @click="showEditStore = false">
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div x-show="showEditStore" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="inline-block align-middle bg-white rounded-[2rem] text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full border border-gray-100">

                    <div
                        class="px-8 py-4 border-b border-gray-100 flex items-center justify-between bg-white sticky top-0 z-10">
                        <h3 class="text-xl font-bold text-gray-900">Edit Store</h3>
                        <button @click="showEditStore = false" class="text-gray-400 hover:text-gray-600 transition-colors">
                            <i data-lucide="x" class="w-6 h-6"></i>
                        </button>
                    </div>

                    <form
                        :action="'{{ route('settings.stores.update', ['store' => ':id']) }}'.replace(':id', currentStore.id)"
                        method="POST" @submit="isSaving = true">
                        @csrf
                        @method('PUT')
                        <div class="p-6">
                            <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2 custom-scrollbar">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Branch <span
                                            class="text-red-500">*</span></label>
                                    <select name="branch_id" x-model="currentStore.branch_id" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Store Code <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="code" x-model="currentStore.code" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Store Name <span
                                                class="text-red-500">*</span></label>
                                        <input type="text" name="name" x-model="currentStore.name" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Address <span
                                            class="text-red-500">*</span></label>
                                    <input type="text" name="address" x-model="currentStore.address" required
                                        class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                </div>

                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                                        <input type="tel" name="phone" x-model="currentStore.phone"
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Status <span
                                                class="text-red-500">*</span></label>
                                        <select name="status" x-model="currentStore.status" required
                                            class="w-full px-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
                                            <option value="active">Active</option>
                                            <option value="inactive">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="flex gap-4 pt-4 mt-2 border-t border-gray-50">
                                <button type="submit" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 bg-[#28A375] text-white rounded-lg text-sm font-bold hover:bg-[#229967] transition-all flex items-center justify-center gap-2">
                                    <span x-show="!isSaving">Update Store</span>
                                    <div x-show="isSaving" class="flex items-center gap-2">
                                        <svg class="animate-spin h-5 w-5 text-white" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        <span>Saving...</span>
                                    </div>
                                </button>
                                <button @click="showEditStore = false" type="button" :disabled="isSaving"
                                    class="flex-1 px-6 py-3 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-all">Cancel</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            function confirmDelete(form, message) {
                Swal.fire({
                    title: 'Are you sure?',
                    text: message,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#28A375',
                    cancelButtonColor: '#ef4444',
                    confirmButtonText: 'Yes, delete it!',
                    cancelButtonText: 'Cancel',
                    reverseButtons: true,
                    customClass: {
                        confirmButton: 'px-6 py-2.5 rounded-xl font-bold text-sm',
                        cancelButton: 'px-6 py-2.5 rounded-xl font-bold text-sm mr-2'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
                return false;
            }
        </script>
    @endpush
@endsection
