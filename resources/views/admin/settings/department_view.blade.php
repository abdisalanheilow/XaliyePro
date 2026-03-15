@section('title', 'View Department - XaliyePro')

@extends('admin.admin_master')
@section('admin')
    <div class="space-y-6">
        <!-- Page Header -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('settings.departments.index') }}"
                    class="w-10 h-10 bg-white border border-gray-200 rounded-lg flex items-center justify-center text-gray-500 hover:text-[#28A375] hover:border-[#28A375] transition-all shadow-sm">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ $department->name }}</h1>
                    <p class="text-gray-500 mt-1 text-sm flex items-center gap-2">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 font-medium text-xs border border-emerald-100 uppercase tracking-wider">
                            Department
                        </span>
                        <span>Created on {{ $department->created_at->format('M d, Y') }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-3 py-1.5 {{ $department->status === 'active' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }} text-sm font-bold rounded-lg capitalize border {{ $department->status === 'active' ? 'border-green-200' : 'border-gray-200' }}">
                    {{ $department->status }}
                </span>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Total Employees -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Total Members</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['total_employees'] }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Personnel assigned</p>
                    </div>
                    <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="users" class="w-6 h-6 text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Active Employees -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Active Duty</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['active'] }}</h3>
                        <p class="text-xs text-emerald-600 mt-1">Currently working</p>
                    </div>
                    <div class="w-12 h-12 bg-emerald-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="user-check" class="w-6 h-6 text-emerald-600"></i>
                    </div>
                </div>
            </div>

            <!-- On Leave -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">On Leave</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['on_leave'] }}</h3>
                        <p class="text-xs text-amber-600 mt-1">Temporarily away</p>
                    </div>
                    <div class="w-12 h-12 bg-amber-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="user-minus" class="w-6 h-6 text-amber-600"></i>
                    </div>
                </div>
            </div>

            <!-- Inactive -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">Inactive</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ $stats['inactive'] }}</h3>
                        <p class="text-xs text-red-600 mt-1">Off-boarded</p>
                    </div>
                    <div class="w-12 h-12 bg-red-50 rounded-xl flex items-center justify-center">
                        <i data-lucide="user-x" class="w-6 h-6 text-red-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Linked Employees List -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between bg-gray-50/30">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-[#28A375] rounded-xl flex items-center justify-center shadow-sm">
                        <i data-lucide="users" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-gray-900">Linked Employees</h2>
                        <p class="text-xs text-gray-500">Personnel assigned to this department</p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-bold rounded-full">
                        {{ count($department->employees) }} {{ \Illuminate\Support\Str::plural('Member', count($department->employees)) }}
                    </span>
                </div>
            </div>

            @if (count($department->employees) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead>
                            <tr class="bg-gray-100/50 border-b border-gray-200">
                                <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">EMPLOYEE</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">DESIGNATION</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">BRANCH</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider">JOIN DATE</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">STATUS</th>
                                <th class="py-4 px-6 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">ACTION</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($department->employees as $employee)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center overflow-hidden border-2 border-white shadow-sm">
                                                @if ($employee->profile_image)
                                                    <img src="{{ asset($employee->profile_image) }}" alt="" class="w-full h-full object-cover">
                                                @else
                                                    <span class="text-emerald-700 font-bold text-sm">{{ substr($employee->name, 0, 1) }}</span>
                                                @endif
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-gray-900">{{ $employee->name }}</p>
                                                <p class="text-xs text-gray-500 font-mono">{{ $employee->employee_id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm font-medium text-gray-700">
                                        {{ $employee->designation }}
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center gap-1.5 text-sm text-gray-600">
                                            <i data-lucide="map-pin" class="w-3.5 h-3.5 text-blue-500"></i>
                                            {{ $employee->branch->name }}
                                        </div>
                                    </td>
                                    <td class="py-4 px-6 text-sm text-gray-500 font-medium">
                                        {{ \Carbon\Carbon::parse($employee->join_date)->format('M d, Y') }}
                                    </td>
                                    <td class="py-4 px-6 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold 
                                            {{ $employee->status === 'active' ? 'bg-green-100 text-green-700' : ($employee->status === 'on_leave' ? 'bg-amber-100 text-amber-700' : 'bg-gray-100 text-gray-700') }}">
                                            {{ ucfirst(str_replace('_', ' ', $employee->status)) }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-center">
                                            <a href="{{ route('settings.employees.show', $employee->id) }}" 
                                               class="p-2 text-gray-400 hover:text-[#28A375] rounded-lg hover:bg-emerald-50 transition-all shadow-sm"
                                               title="View Profile">
                                                <i data-lucide="external-link" class="w-4 h-4"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="p-16 text-center">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-dashed border-gray-200">
                        <i data-lucide="users" class="w-10 h-10 text-gray-300"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900">No employees assigned</h3>
                    <p class="text-sm text-gray-500 mt-1 max-w-sm mx-auto">There are currently no personnel linked to this department. You can assign staff from the Employee Management module.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
