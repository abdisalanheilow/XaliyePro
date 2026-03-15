@extends('admin.admin_master')

@section('title', 'View User - XaliyePro')

@section('admin')
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('settings.users.index') }}"
                class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $user->name }}</h1>
                <p class="text-gray-500 mt-0.5 text-sm">User Profile — <span
                        class="font-medium text-gray-700">{{ $user->role->name ?? 'No Role' }}</span></p>
            </div>
        </div>
        <div class="flex gap-3">
            <span
                class="px-3 py-1.5 {{ $user->status == 'Active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-sm font-semibold rounded-lg capitalize">
                {{ $user->status }}
            </span>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Account Info Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                    <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                        <i data-lucide="user" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Personal Information</h2>
                        <p class="text-xs text-gray-500">Core identity and contact details</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="flex flex-col md:flex-row gap-6 mb-6">
                        <div
                            class="w-24 h-24 bg-gray-100 rounded-2xl flex items-center justify-center border-2 border-gray-200 overflow-hidden flex-shrink-0">
                            @if ($user->photo)
                                <img src="{{ asset('upload/admin_images/' . $user->photo) }}"
                                    class="w-full h-full object-cover">
                            @else
                                <span class="text-3xl font-bold text-gray-400">{{ substr($user->name, 0, 2) }}</span>
                            @endif
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5 flex-1">
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Full
                                    Name</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $user->name }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email
                                    Address</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">{{ $user->email }}</p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Linked
                                    Employee</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    @if ($user->employee)
                                        <a href="{{ route('settings.employees.show', $user->employee->id) }}"
                                            class="text-[#28A375] hover:underline">
                                            {{ $user->employee->name }} ({{ $user->employee->employee_id }})
                                        </a>
                                    @else
                                        <span class="text-gray-400 italic">None</span>
                                    @endif
                                </p>
                            </div>
                            <div>
                                <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Last
                                    Login</label>
                                <p class="mt-1 text-sm font-medium text-gray-900">
                                    {{ $user->last_login_at ? $user->last_login_at->format('M d, Y \a\t h:i A') : 'Never' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permission & Access Summary -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                    <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                        <i data-lucide="shield-check" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Role & Security</h2>
                        <p class="text-xs text-gray-500">System permissions and access levels</p>
                    </div>
                </div>
                <div class="p-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Role Overview</h4>
                            <div class="p-4 bg-purple-50 rounded-xl border border-purple-100 mt-2">
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                                    <span
                                        class="text-sm font-bold text-purple-900 uppercase tracking-wider">{{ $user->role->name ?? 'Basic User' }}</span>
                                </div>
                                <p class="text-xs text-purple-700">
                                    {{ $user->role->description ?? 'Standard system access' }}</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-sm font-bold text-gray-900 mb-2">Branch Visibility</h4>
                            @if ($user->view_all_branches)
                                <div
                                    class="flex items-center gap-2 text-blue-600 bg-blue-50 px-3 py-2 rounded-lg border border-blue-100 mt-2">
                                    <i data-lucide="globe" class="w-4 h-4"></i>
                                    <span class="text-sm font-medium">Full global visibility (All Branches)</span>
                                </div>
                            @else
                                <div
                                    class="flex items-center gap-2 text-orange-600 bg-orange-50 px-3 py-2 rounded-lg border border-orange-100 mt-2">
                                    <i data-lucide="lock" class="w-4 h-4"></i>
                                    <span class="text-sm font-medium">Restricted visibility (Assigned Locations)</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Activity/Stats -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-4 text-green-600">
                    <i data-lucide="check-circle-2" class="w-8 h-8"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900">Account Health</h3>
                <p class="text-sm text-gray-500 mb-4">This user account is currently fully functional.</p>
                <div class="w-full space-y-3 pt-4 border-t border-gray-100">
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Created On</span>
                        <span class="font-medium text-gray-900">{{ $user->created_at->format('M d, Y') }}</span>
                    </div>
                    <div class="flex justify-between text-xs">
                        <span class="text-gray-500">Last Updated</span>
                        <span class="font-medium text-gray-900">{{ $user->updated_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assigned Branches & Stores -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 mb-6">
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Branch & Store Assignments</h2>
                    <p class="text-xs text-gray-500">Physical locations this user can operate in</p>
                </div>
            </div>
        </div>
        <div class="p-5">
            @if ($user->branches->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($user->branches as $branch)
                        <div class="border border-gray-100 rounded-2xl p-4 bg-gray-50/50">
                            <div class="flex items-center gap-3 mb-4">
                                <div
                                    class="w-8 h-8 bg-white shadow-sm border border-gray-100 rounded-lg flex items-center justify-center text-[#28A375]">
                                    <i data-lucide="building-2" class="w-4 h-4"></i>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900">{{ $branch->name }}</h4>
                                    <p class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $branch->code }}</p>
                                </div>
                            </div>
                            <div class="space-y-2">
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-2">Authorized Stores</p>
                                @php
                                    $userBranchStores = $user->stores->where('branch_id', $branch->id);
                                @endphp
                                @if ($userBranchStores->count() > 0)
                                    @foreach ($userBranchStores as $store)
                                        <div class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg border border-gray-100">
                                            <i data-lucide="store" class="w-3.5 h-3.5 text-gray-400"></i>
                                            <span class="text-xs font-medium text-gray-700">{{ $store->name }}</span>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-xs text-gray-400 italic pl-1">No specific stores assigned</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="py-12 text-center">
                    <div
                        class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i data-lucide="map-pin-off" class="w-8 h-8 text-gray-300"></i>
                    </div>
                    <h3 class="text-sm font-semibold text-gray-900 mb-1">No Branch Assignments</h3>
                    <p class="text-sm text-gray-500">This user hasn't been assigned to any specific branches yet.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
