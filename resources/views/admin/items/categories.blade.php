@extends('admin.admin_master')
@section('admin')
    <div x-data="categoriesApp()">
        @section('title')
            Product & Service Categories - XaliyePro
        @endsection

        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Categories</h1>
                <p class="text-gray-500 mt-1">Organize products and services into categories</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="isImportModalOpen = true; selectedFile = null" type="button"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors relative h-[38px]">
                    <i data-lucide="upload" class="w-4 h-4"></i> Import
                </button>

                <a href="{{ route('items.categories.export') }}"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors h-[38px] flex items-center">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </a>

                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all shadow-sm h-[38px]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Category
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Categories',
                'value' => number_format($stats['total']),
                'icon' => 'folder_closed',
                'subtitle' => number_format($stats['product_count']) . ' Product, ' . number_format($stats['service_count']) . ' Service'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active',
                'value' => number_format($stats['active']),
                'icon' => 'check-circle',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'iconShadow' => 'shadow-green-100',
                'subtitle' => 'Available categories'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Product Categories',
                'value' => number_format($stats['product_count']),
                'icon' => 'box',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'subtitle' => 'Product organization'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Service Categories',
                'value' => number_format($stats['service_count']),
                'icon' => 'briefcase',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
                'subtitle' => 'Service organization'
            ])
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('items.categories.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or description..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <select name="type" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Types" {{ request('type') == 'All Types' ? 'selected' : '' }}>All Types</option>
                    <option value="Product" {{ request('type') == 'Product' ? 'selected' : '' }}>Product</option>
                    <option value="Service" {{ request('type') == 'Service' ? 'selected' : '' }}>Service</option>
                </select>

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
                <h2 class="text-lg font-bold text-gray-900">All Categories</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Items Count</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($categories->count() > 0)
                            @foreach ($categories as $category)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <i data-lucide="{{ $category->type === 'service' ? 'briefcase' : 'folder' }}" class="w-5 h-5 text-gray-500"></i>
                                        </div>
                                        <div class="text-sm font-semibold text-gray-900">{{ $category->name }}</div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->type === 'service' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($category->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-500 line-clamp-1 max-w-xs">{{ $category->description ?? 'No description' }}</p>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <span class="text-sm font-semibold text-gray-900">{{ $category->items->count() }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $category->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($category->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="editCategory({{ $category->id }}, {{ json_encode($category) }})"
                                            class="p-1.5 text-gray-400 hover:text-[#28A375] transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <form action="{{ route('items.categories.destroy', $category->id) }}" method="POST" class="inline">
                                            @csrf @method('DELETE')
                                            <button type="button" @click="confirmDelete($el.form, '{{ $category->name }}')"
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
                                        <i data-lucide="folder-search" class="w-12 h-12 text-gray-300 mb-3"></i>
                                        <h4 class="text-base font-bold text-gray-900">No categories found</h4>
                                        <p class="text-sm text-gray-500 mt-1">Start by adding your first category.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($categories->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $categories->links() }}
                </div>
            @endif
        </div>


        <!-- Modals Section -->
        @include('admin.items.modals.category_modals')
        @include('admin.items.modals.import_category_modal')

        <script>
            function categoriesApp() {
                return {
                    activeTab: 'product',
                    showAdd: false,
                    showEdit: false,
                    isSaving: false,
                    isImportModalOpen: false,
                    selectedFile: null,
                    editId: null,
                    editForm: {},
                    addForm: { type: 'product' },

                    openAddModal() {
                        this.showAdd = true;
                        this.isSaving = false;
                        this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                    },

                    editCategory(id, data) {
                        this.editId = id;
                        this.editForm = { ...data };
                        this.showEdit = true;
                        this.isSaving = false;
                        this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                    },

                    confirmDelete(form, name) {
                        Swal.fire({
                            title: 'Delete Category?',
                            text: `Are you sure you want to delete "${name}"? Products in this category will become uncategorized.`,
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#EF4444',
                            cancelButtonColor: '#6B7280',
                            confirmButtonText: 'Yes, delete',
                            cancelButtonText: 'Cancel',
                            customClass: {
                                popup: 'rounded-[1.5rem]',
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
