@extends('admin.admin_master')
@section('admin')
    <div x-data="unitsApp()">
        @section('title')
            Units of Measure - XaliyePro
        @endsection

        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Units of Measure</h1>
                <p class="text-gray-500 mt-1">Manage physical measurement units and conversions</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="isImportModalOpen = true; selectedFile = null;" type="button"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors relative h-[38px]">
                    <i data-lucide="upload" class="w-4 h-4"></i> Import
                </button>

                <a href="{{ route('items.units.export') }}"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors h-[38px] flex items-center">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </a>

                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all shadow-sm h-[38px]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Unit
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Units',
                'value' => number_format($stats['total']),
                'icon' => 'scale',
                'subtitle' => 'Unit definitions'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active Units',
                'value' => number_format($stats['active']),
                'icon' => 'check-circle',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'iconShadow' => 'shadow-green-100',
                'subtitle' => 'Available for use'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Items count',
                'value' => number_format($stats['total_items']),
                'icon' => 'package',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
                'subtitle' => 'Products & Services'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Avg per Unit',
                'value' => number_format($stats['avg_per_unit'], 1),
                'icon' => 'bar-chart',
                'color' => '#F59E0B',
                'iconBg' => 'bg-orange-500',
                'iconShadow' => 'shadow-orange-100',
                'subtitle' => 'Utilization'
            ])
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('items.units.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or short name..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Status" {{ request('status', 'All Status') == 'All Status' ? 'selected' : '' }}>All Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">All Units</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Unit Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Short Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Conversion</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Items Count</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($units->count() > 0)
                            @foreach ($units as $unit)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900">{{ $unit->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <span class="px-2.5 py-1 bg-gray-100 rounded text-gray-900 font-bold uppercase">{{ $unit->short_name }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if ($unit->base_unit_id)
                                        <div class="text-xs font-medium text-gray-600 flex items-center gap-1.5 bg-blue-50 px-2 py-1 rounded inline-flex border border-blue-100">
                                            <i data-lucide="refresh-cw" class="w-3.5 h-3.5 text-blue-500"></i>
                                            1 {{ $unit->short_name }} = {{ (float) $unit->operation_value }} {{ $unit->operator }} {{ $unit->baseUnit->short_name }}
                                        </div>
                                    @else
                                        <span class="text-[10px] font-bold text-green-600 uppercase tracking-wider bg-green-50 px-2 py-1 rounded border border-green-100">Base Unit</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $unit->items->count() }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $unit->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($unit->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="editUnit({{ $unit->id }}, {{ json_encode($unit) }})"
                                            class="p-1.5 text-gray-400 hover:text-[#28A375] transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <form action="{{ route('items.units.destroy', $unit->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" @click="confirmDelete($el.form, '{{ $unit->name }}')"
                                                class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="6" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="scale-3d" class="w-12 h-12 text-gray-300 mb-3"></i>
                                        <h4 class="text-base font-bold text-gray-900">No units found</h4>
                                        <p class="text-sm text-gray-500 mt-1">Start by adding your first unit of measure.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($units->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $units->links() }}
                </div>
            @endif
        </div>


        <!-- Modals Section -->
        @include('admin.items.modals.unit_modals')
        @include('admin.items.modals.import_unit_modal')

        <script>
            function unitsApp() {
                return {
                    showAdd: false,
                    showEdit: false,
                    isSaving: false,
                    isImportModalOpen: false,
                    selectedFile: null,
                    editId: null,
                    editForm: {},
                    addForm: {
                        status: 'active',
                        hasConversion: false
                    },

                    openAddModal() {
                        this.showAdd = true;
                        this.isSaving = false;
                        this.addForm.hasConversion = false;
                        this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                    },

                    editUnit(id, data) {
                        this.editId = id;
                        this.isSaving = false;
                        this.editForm = {
                            ...data,
                            hasConversion: data.base_unit_id ? true : false
                        };
                        this.showEdit = true;
                        this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                    },

                    confirmDelete(form, name) {
                        Swal.fire({
                            title: 'Delete Unit?',
                            text: `Are you sure you want to delete "${name}"? This might affect products using this unit.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                popup: 'rounded-2xl',
                                confirmButton: 'rounded-lg p-3',
                                cancelButton: 'rounded-lg p-3'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) form.submit();
                        });
                    }
                }
            }
        </script>
    </div>
@endsection
