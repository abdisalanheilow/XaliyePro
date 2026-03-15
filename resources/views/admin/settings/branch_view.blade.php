@extends('admin.admin_master')

@section('title', 'View Branch - XaliyePro')

@section('admin')
    <!-- Page Header -->
    <div class="flex items-center justify-between mb-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('settings.branches.index') }}"
                class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-gray-700 hover:border-gray-300 transition-colors">
                <i data-lucide="arrow-left" class="w-5 h-5"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-gray-900">{{ $branch->name }}</h1>
                <p class="text-gray-500 mt-0.5 text-sm">Branch Details — <span
                        class="font-medium text-gray-700">{{ $branch->code }}</span></p>
            </div>
        </div>
        <div class="flex gap-3">
            <span
                class="px-3 py-1.5 {{ $branch->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-sm font-semibold rounded-lg capitalize">
                {{ $branch->status }}
            </span>
        </div>
    </div>

    <!-- Branch Information Cards -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- General Info Card -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                <div class="w-10 h-10 bg-[#28A375] rounded-lg flex items-center justify-center">
                    <i data-lucide="building-2" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">General Information</h2>
                    <p class="text-xs text-gray-500">Core details about this branch</p>
                </div>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Branch Code</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->code }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Branch Name</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->name }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Email Address</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->email ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Phone Number</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->phone ?? 'Not Provided' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Branch Manager</label>
                        <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->manager_name ?? 'Not Assigned' }}</p>
                    </div>
                    <div>
                        <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Status</label>
                        <p class="mt-1">
                            <span
                                class="px-2.5 py-1 {{ $branch->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-xs font-semibold rounded capitalize">
                                {{ $branch->status }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Location Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="flex items-center gap-3 p-5 border-b border-gray-200">
                <div class="w-10 h-10 bg-blue-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="map-pin" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Location</h2>
                    <p class="text-xs text-gray-500">Address details</p>
                </div>
            </div>
            <div class="p-5 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Address</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->address }}</p>
                </div>
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">City</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->city }}</p>
                </div>
                @if ($branch->state)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">State</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->state }}</p>
                </div>
                @endif
                @if ($branch->zip_code)
                <div>
                    <label class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Zip Code</label>
                    <p class="mt-1 text-sm font-medium text-gray-900">{{ $branch->zip_code }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="store" class="w-6 h-6 text-purple-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $branch->stores->count() }}</p>
                    <p class="text-xs text-gray-500">Total Stores</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $branch->stores->sum('employee_count') }}</p>
                    <p class="text-xs text-gray-500">Total Employees</p>
                </div>
            </div>
        </div>
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                    <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900">{{ $branch->stores->where('status', 'active')->count() }}
                    </p>
                    <p class="text-xs text-gray-500">Active Stores</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Stores Under This Branch -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center justify-between p-5 border-b border-gray-200">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-500 rounded-lg flex items-center justify-center">
                    <i data-lucide="store" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h2 class="text-lg font-semibold text-gray-900">Stores</h2>
                    <p class="text-xs text-gray-500">All stores under this branch</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-semibold rounded-full">
                {{ $branch->stores->count() }} {{ \Illuminate\Support\Str::plural('store', $branch->stores->count()) }}
            </span>
        </div>

        @if ($branch->stores->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-200">
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Store Name</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Code</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Address</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Phone</th>
                            <th class="text-left py-3 px-6 text-xs font-semibold text-gray-500 uppercase">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($branch->stores as $store)
                            <tr class="border-b border-gray-200 hover:bg-gray-50 transition-colors">
                                <td class="py-3.5 px-6">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-9 h-9 bg-purple-600 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <i data-lucide="store" class="w-4 h-4 text-white"></i>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900">{{ $store->name }}</span>
                                    </div>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="text-sm text-gray-600 font-mono">{{ $store->code }}</span>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="text-sm text-gray-600">{{ $store->address }}</span>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="text-sm text-gray-600">{{ $store->phone ?? 'N/A' }}</span>
                                </td>
                                <td class="py-3.5 px-6">
                                    <span
                                        class="px-2.5 py-1 {{ $store->status == 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }} text-xs font-medium rounded capitalize">
                                        {{ $store->status }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-12 text-center">
                <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i data-lucide="store" class="w-8 h-8 text-gray-400"></i>
                </div>
                <h3 class="text-sm font-semibold text-gray-900 mb-1">No stores yet</h3>
                <p class="text-sm text-gray-500">This branch doesn't have any stores assigned.</p>
            </div>
        @endif
    </div>

    <!-- Timestamps -->
    <div class="mt-6 flex items-center gap-6 text-xs text-gray-400">
        <div class="flex items-center gap-1.5">
            <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
            <span>Created: {{ $branch->created_at->format('M d, Y \a\t h:i A') }}</span>
        </div>
        <div class="flex items-center gap-1.5">
            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
            <span>Last Updated: {{ $branch->updated_at->format('M d, Y \a\t h:i A') }}</span>
        </div>
    </div>
@endsection
