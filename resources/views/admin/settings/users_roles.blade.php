@extends('admin.admin_master')
@section('title', 'Users & Roles Management - XaliyePro')

@push('css')
    <style>
        .tab-active {
            border-bottom: 2px solid #28A375;
            color: #28A375;
        }

        .modal-hidden {
            display: none !important;
        }
    </style>
@endpush

@section('admin')
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Users & Roles Management</h1>
            <p class="text-gray-500 mt-1">Manage system users, roles, permissions, and branch assignments</p>
        </div>
        <button id="addUserBtn" onclick="openAddUserModal()"
            class="flex items-center gap-2 px-4 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967]">
            <i data-lucide="plus" class="w-4 h-4"></i>
            Add User
        </button>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @include('admin.partials.stats_card', [
            'title' => 'Total Users',
            'value' => number_format($stats['total_users']),
            'icon' => 'users',
            'subtitle' => 'System users'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Active Users',
            'value' => number_format($stats['active_users']),
            'icon' => 'user-check',
            'color' => '#16A34A',
            'iconBg' => 'bg-green-600',
            'iconShadow' => 'shadow-green-100',
            'subtitle' => 'Currently active'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Total Roles',
            'value' => number_format($stats['total_roles']),
            'icon' => 'shield',
            'color' => '#A855F7',
            'iconBg' => 'bg-purple-500',
            'iconShadow' => 'shadow-purple-100',
            'subtitle' => 'Defined roles'
        ])

        @include('admin.partials.stats_card', [
            'title' => 'Permissions',
            'value' => number_format($stats['total_permissions']),
            'icon' => 'lock',
            'color' => '#3B82F6',
            'iconBg' => 'bg-blue-500',
            'iconShadow' => 'shadow-blue-100',
            'subtitle' => 'Available permissions'
        ])
    </div>

    <!-- Tabs -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex gap-8">
                <button id="usersTab" onclick="switchTab('users')"
                    class="{{ request()->get('tab', 'users') === 'users' ? 'tab-active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-3 px-1 text-sm font-medium transition-colors border-b-2 flex items-center gap-2">
                    <i data-lucide="users" class="w-4 h-4"></i>
                    Users
                </button>
                <button id="rolesTab" onclick="switchTab('roles')"
                    class="{{ request()->get('tab') === 'roles' ? 'tab-active' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} py-3 px-1 text-sm font-medium transition-colors border-b-2 flex items-center gap-2">
                    <i data-lucide="shield" class="w-4 h-4"></i>
                    Roles
                </button>
            </nav>
        </div>
    </div>

    <!-- Users Content -->
    <div id="usersContent" class="{{ request()->get('tab', 'users') === 'users' ? '' : 'modal-hidden' }}">
        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="relative">
                <i data-lucide="search"
                    class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" placeholder="Search users..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
            </div>
        </div>

        <!-- Users Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">User Name</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Email</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Role</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Assigned Branches
                            </th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Branch Access</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Last Login</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Status</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @if (count($users) > 0)
                            @foreach ($users as $user)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-[#28A375] rounded-full flex items-center justify-center text-white text-sm font-bold overflow-hidden">
                                                @if ($user->photo)
                                                    <img src="{{ asset('upload/admin_images/' . $user->photo) }}"
                                                        class="w-full h-full object-cover">
                                                @else
                                                    {{ substr($user->name, 0, 2) }}
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-sm text-gray-900">{{ $user->email }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-700">
                                            <i data-lucide="shield" class="w-3 h-3 mr-1"></i>
                                            {{ $user->role->name ?? 'No Role' }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="text-sm text-gray-600 space-y-1">
                                            @foreach ($user->branches as $branch)
                                                <div class="flex items-center gap-2">
                                                    <i data-lucide="building-2" class="w-3.5 h-3.5 text-gray-400"></i>
                                                    <span>{{ $branch->name }}</span>
                                                    <span
                                                        class="text-gray-400">({{ count($user->stores->where('branch_id', $branch->id)) }}
                                                        stores)</span>
                                                    @if ($branch->pivot->is_default)
                                                        <i data-lucide="check-circle" class="w-3.5 h-3.5 text-green-500"></i>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if ($user->view_all_branches)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <i data-lucide="globe" class="w-3 h-3 mr-1"></i>
                                                All Branches
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                                <i data-lucide="lock" class="w-3 h-3 mr-1"></i>
                                                Restricted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        @if ($user->last_login_at)
                                            <div class="text-sm text-gray-900">{{ $user->last_login_at->format('Y-m-d') }}</div>
                                            <div class="text-xs text-gray-500">{{ $user->last_login_at->format('h:i A') }}</div>
                                        @else
                                            <span class="text-xs text-gray-400">Never</span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium {{ $user->status === 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $user->status }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <a href="{{ route('settings.users.show', $user->id) }}"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded transition-colors"
                                                title="View User Details">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <button onclick="openEditUserModal({{ json_encode($user) }})"
                                                class="p-1.5 text-gray-400 hover:text-[#28A375] hover:bg-green-50 rounded transition-colors"
                                                title="Manage Profile & Access">
                                                <i data-lucide="settings" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('settings.users.destroy', $user->id) }}" method="POST"
                                                class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
                                                    class="p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded transition-colors"
                                                    title="Delete User">
                                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="8" class="py-8 text-center text-gray-500">No users found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Users Pagination --}}
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $users->appends(['tab' => 'users'])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Roles Content -->
    <div id="rolesContent" class="{{ request()->get('tab') === 'roles' ? '' : 'modal-hidden' }}">
        <!-- Search Bar -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <div class="relative">
                <i data-lucide="search"
                    class="w-4 h-4 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2"></i>
                <input type="text" placeholder="Search roles..."
                    class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:border-transparent">
            </div>
        </div>

        <!-- Roles Table -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Role Name</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Description</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Branch Access Type
                            </th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Users</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Permissions</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white">
                        @if (count($roles) > 0)
                            @foreach ($roles as $role)
                                <tr class="border-b border-gray-200 hover:bg-gray-50">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                                                <i data-lucide="shield" class="w-5 h-5 text-white"></i>
                                            </div>
                                            <span class="text-sm font-medium text-gray-900">{{ $role->name }}</span>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-sm text-gray-600">{{ $role->description }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        @if ($role->branch_access_type === 'all')
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700">
                                                <i data-lucide="globe" class="w-3 h-3 mr-1"></i>
                                                All Branches
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-700">
                                                <i data-lucide="lock" class="w-3 h-3 mr-1"></i>
                                                Restricted
                                            </span>
                                        @endif
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="text-sm font-semibold text-gray-900">{{ $role->users_count }}</span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                            <i data-lucide="lock-open" class="w-3 h-3 mr-1"></i>
                                            {{ $role->permissions_count }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-2">
                                            <button
                                                onclick="openEditRoleModal({{ json_encode($role) }}, {{ json_encode($role->permissions->pluck('id')) }})"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 rounded">
                                                <i data-lucide="edit-2" class="w-4 h-4"></i>
                                            </button>
                                            <form action="{{ route('settings.roles.destroy', $role->id) }}" method="POST"
                                                class="delete-role-form">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button" onclick="confirmDelete(this)"
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
                                <td colspan="6" class="py-8 text-center text-gray-500">No roles found.</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            {{-- Roles Pagination --}}
            @if($roles->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50/50">
                    {{ $roles->appends(['tab' => 'roles'])->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modals -->
    @include('admin.settings.modals.add_user')
    @include('admin.settings.modals.edit_role')

@endsection

@push('scripts')
    <script>
        function switchTab(tab) {
            const usersTab = document.getElementById('usersTab');
            const rolesTab = document.getElementById('rolesTab');
            const usersContent = document.getElementById('usersContent');
            const rolesContent = document.getElementById('rolesContent');
            const addUserBtn = document.getElementById('addUserBtn');

            // Update URL
            const url = new URL(window.location);
            url.searchParams.set('tab', tab);
            window.history.pushState({}, '', url);

            if (tab === 'users') {
                usersTab.classList.add('tab-active');
                usersTab.classList.remove('border-transparent', 'text-gray-500');
                rolesTab.classList.remove('tab-active');
                rolesTab.classList.add('border-transparent', 'text-gray-500');

                usersContent.classList.remove('modal-hidden');
                rolesContent.classList.add('modal-hidden');

                addUserBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4"></i>Add User';
                addUserBtn.onclick = () => openAddUserModal();
            } else {
                rolesTab.classList.add('tab-active');
                rolesTab.classList.remove('border-transparent', 'text-gray-500');
                usersTab.classList.remove('tab-active');
                usersTab.classList.add('border-transparent', 'text-gray-500');

                rolesContent.classList.remove('modal-hidden');
                usersContent.classList.add('modal-hidden');

                addUserBtn.innerHTML = '<i data-lucide="plus" class="w-4 h-4"></i>Create Role';
                addUserBtn.onclick = () => openAddRoleModal();
            }

            lucide.createIcons();
        }

        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('modal-hidden');
            lucide.createIcons();
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('modal-hidden');
        }

        function openAddUserModal() {
            const modal = document.getElementById('addUserModal');
            const form = modal.querySelector('form');
            form.action = "{{ route('settings.users.store') }}";
            form.reset();

            // Remove method spoofing if exists
            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

            modal.querySelector('h2').innerText = 'Add New User';
            const employeeSelect = modal.querySelector('select[name="employee_id"]');
            employeeSelect.value = '';
            employeeSelect.disabled = false;
            employeeSelect.classList.remove('bg-gray-50', 'cursor-not-allowed', 'border-gray-200');
            employeeSelect.classList.add('bg-white', 'border-gray-300');

            const nameInput = modal.querySelector('input[name="name"]');
            nameInput.value = '';
            nameInput.placeholder = 'Select employee above...';

            const emailInput = modal.querySelector('input[name="email"]');
            emailInput.value = '';
            emailInput.placeholder = 'Select employee above...';

            modal.querySelector('input[name="password"]').required = true;
            modal.querySelector('#password_label .text-red-500').classList.remove('hidden');
            modal.querySelector('input[name="password"]').placeholder = '••••••••';
            modal.querySelector('button[type="submit"]').innerText = 'Create User';

            openModal('addUserModal');
        }

        function autoFillEmployee(select) {
            const option = select.options[select.selectedIndex];
            const modal = select.closest('form');
            if (option.value) {
                modal.querySelector('input[name="name"]').value = option.getAttribute('data-name');
                modal.querySelector('input[name="email"]').value = option.getAttribute('data-email');
            } else {
                modal.querySelector('input[name="name"]').value = '';
                modal.querySelector('input[name="email"]').value = '';
            }
        }

        function openAddRoleModal() {
            const modal = document.getElementById('editRoleModal');
            const form = modal.querySelector('form');
            form.action = "{{ route('settings.roles.store') }}";
            form.reset();

            const methodInput = form.querySelector('input[name="_method"]');
            if (methodInput) methodInput.remove();

            modal.querySelector('h2').innerText = 'Create Role';
            modal.querySelector('button[type="submit"]').innerText = 'Save Role';

            openModal('editRoleModal');
        }

        function openEditRoleModal(role, assignedPermissions) {
            const modal = document.getElementById('editRoleModal');
            const form = modal.querySelector('form');
            form.action = `{{ url('settings/roles') }}/${role.id}`;

            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }

            modal.querySelector('h2').innerText = 'Edit Role: ' + role.name;
            modal.querySelector('input[name="name"]').value = role.name;
            modal.querySelector('input[name="description"]').value = role.description;
            modal.querySelector('input[name="branch_access_type"]').checked = role.branch_access_type === 'restricted';

            // Reset and set permissions
            modal.querySelectorAll('input[name="permissions[]"]').forEach(cb => {
                cb.checked = assignedPermissions.includes(parseInt(cb.value));
            });

            modal.querySelector('button[type="submit"]').innerText = 'Update Role';
            openModal('editRoleModal');
        }

        function openEditUserModal(user) {
            const modal = document.getElementById('addUserModal');
            const form = modal.querySelector('form');
            form.action = `{{ url('settings/users') }}/${user.id}`;

            if (!form.querySelector('input[name="_method"]')) {
                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'PUT';
                form.appendChild(methodInput);
            }

            modal.querySelector('h2').innerText = 'Edit User: ' + user.name;
            const employeeSelect = modal.querySelector('select[name="employee_id"]');
            employeeSelect.value = user.employee_id || '';
            employeeSelect.disabled = true;
            employeeSelect.classList.add('bg-gray-50', 'cursor-not-allowed', 'border-gray-200');
            employeeSelect.classList.remove('bg-white', 'border-gray-300');

            modal.querySelector('input[name="name"]').value = user.name;
            modal.querySelector('input[name="email"]').value = user.email;

            // Explicitly set select values with a slight delay to ensure options are loaded (if dynamic, though here they are static)
            setTimeout(() => {
                modal.querySelector('select[name="role_id"]').value = user.role_id;
                // Find and set status case-insensitively
                const statusSelect = modal.querySelector('select[name="status"]');
                const statusValue = user.status;
                Array.from(statusSelect.options).some(option => {
                    if (option.value.toLowerCase() === (statusValue || '').toLowerCase()) {
                        statusSelect.value = option.value;
                        return true;
                    }
                    return false;
                });
            }, 10);

            modal.querySelector('input[name="password"]').required = false;
            modal.querySelector('input[name="password"]').placeholder = 'Leave blank to keep current';
            modal.querySelector('#password_label .text-red-500').classList.add('hidden');

            modal.querySelector('input[name="view_all_branches"]').checked = user.view_all_branches == 1 || user.view_all_branches == true;

            // Sync branches/stores
            const assignedBranches = {!! $users->mapWithKeys(fn($u) => [$u->id => $u->branches->pluck('id')]) !!}[user.id] || [];
            const assignedStores = {!! $users->mapWithKeys(fn($u) => [$u->id => $u->stores->pluck('id')]) !!}[user.id] || [];

            modal.querySelectorAll('input[name="branches[]"]').forEach(cb => {
                cb.checked = assignedBranches.includes(parseInt(cb.value));
            });
            modal.querySelectorAll('input[name="stores[]"]').forEach(cb => {
                cb.checked = assignedStores.includes(parseInt(cb.value));
            });

            modal.querySelector('button[type="submit"]').innerText = 'Update User';
            openModal('addUserModal');
        }


        function confirmDelete(btn) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28A375',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    btn.closest('form').submit();
                }
            });
        }
    </script>
@endpush
