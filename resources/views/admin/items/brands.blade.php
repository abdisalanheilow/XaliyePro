@extends('admin.admin_master')
@section('admin')
    <div x-data="brandsApp()">
        @section('title')
            Brands - XaliyePro
        @endsection

        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Brands</h1>
                <p class="text-gray-500 mt-1">Manage item brands and manufacturers</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="isImportModalOpen = true; selectedFile = null" type="button"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors relative h-[38px]">
                    <i data-lucide="upload" class="w-4 h-4"></i> Import
                </button>
                                
                <a href="{{ route('items.brands.export') }}" 
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors h-[38px] flex items-center">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </a>
                
                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all shadow-sm h-[38px]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Brand
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Brands',
                'value' => number_format($stats['total']),
                'icon' => 'tag',
                'subtitle' => 'Manufacturer list'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Active',
                'value' => number_format($stats['active']),
                'icon' => 'check-circle',
                'color' => '#10B981',
                'iconBg' => 'bg-green-500',
                'iconShadow' => 'shadow-green-100',
                'subtitle' => 'Available brands'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Inactive',
                'value' => number_format($stats['inactive']),
                'icon' => 'x-circle',
                'color' => '#EF4444',
                'iconBg' => 'bg-red-500',
                'iconShadow' => 'shadow-red-100',
                'subtitle' => 'Disabled brands'
            ])
        </div>

        {{-- Search Bar --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('items.brands.index') }}" method="GET" class="relative max-w-md">
                <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search brands..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
            </form>
        </div>

        {{-- Brands Content --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-900">All Brands</h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Brand Name</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($brands->count() > 0)
                            @foreach ($brands as $brand)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center flex-shrink-0 border border-indigo-200">
                                            <span class="text-indigo-700 font-bold">{{ strtoupper(substr($brand->name, 0, 1)) }}</span>
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $brand->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-sm text-gray-500 line-clamp-1 max-w-sm">{{ $brand->description ?? 'No description' }}</p>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $brand->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($brand->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <button @click="editBrand({{ $brand->toJson() }})"
                                            class="p-1.5 text-gray-400 hover:text-[#28A375] transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="confirmDelete('{{ route('items.brands.destroy', $brand->id) }}')"
                                            class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center mb-3">
                                            <i data-lucide="tag" class="w-6 h-6 text-gray-400"></i>
                                        </div>
                                        <h4 class="text-base font-bold text-gray-900">No brands found</h4>
                                        <p class="text-sm text-gray-500 mt-1">Start by adding your first brand.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($brands->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $brands->links() }}
                </div>
            @endif


        <!-- Add/Edit Brand Modal -->
        <div x-show="showAdd || showEdit" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showAdd || showEdit" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    class="fixed inset-0 bg-gray-900/40 backdrop-blur-sm transition-opacity" @click="closeModal()"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen">&#8203;</span>

                <div x-show="showAdd || showEdit" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    class="inline-block align-middle bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-md w-full border border-gray-100 hover:shadow-2xl">

                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-white relative">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center border border-indigo-100">
                                <i data-lucide="tag" class="w-5 h-5 text-indigo-600"></i>
                            </div>
                            <h3 class="text-xl font-bold text-gray-900" x-text="showAdd ? 'Add New Brand' : 'Edit Brand'">
                            </h3>
                        </div>
                        <button @click="closeModal()"
                            class="text-gray-400 hover:text-gray-600 transition-colors bg-gray-50 hover:bg-gray-100 rounded-full p-2">
                            <i data-lucide="x" class="w-5 h-5"></i>
                        </button>
                    </div>

                    <form
                        :action="showAdd ? '{{ route('items.brands.store') }}' : '{{ route('items.brands.update', ':id') }}'.replace(':id', editId)"
                        method="POST">
                        @csrf
                        <template x-if="showEdit">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="p-6 space-y-5">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5 flex items-center gap-2">
                                    Brand Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" name="name" x-model="editForm.name" required
                                    placeholder="Enter brand name"
                                    class="w-full px-4 py-2.5 flex-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all focus:bg-white text-gray-900 placeholder:text-gray-400 shadow-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Description</label>
                                <textarea name="description" x-model="editForm.description" rows="3"
                                    placeholder="Brief description about the brand..."
                                    class="w-full px-4 py-2.5 flex-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all focus:bg-white text-gray-900 placeholder:text-gray-400 shadow-sm resize-none"></textarea>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                                <select name="status" x-model="editForm.status"
                                    class="w-full px-4 py-2.5 flex-1 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-[#28A375] focus:border-transparent outline-none transition-all bg-white text-gray-900 shadow-sm appearance-none bg-no-repeat bg-[right_1rem_center] bg-[length:1em_1em]"
                                    style="background-image: url('data:image/svg+xml;charset=utf-8,%3Csvg xmlns=%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22 fill=%22none%22 viewBox=%220%200%2020%2020%22%3E%3Cpath stroke=%22%236B7280%22 stroke-linecap=%22round%22 stroke-linejoin=%22round%22 stroke-width=%221.5%22 d=%22m6%208%204%204%204-4%22%2F%3E%3C%2Fsvg%3E')">
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>

                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end gap-3 rounded-b-2xl">
                            <button type="button" @click="closeModal()"
                                class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 transition-all">
                                Cancel
                            </button>
                            <button type="submit"
                                class="px-5 py-2.5 bg-[#28A375] text-white rounded-lg text-sm font-semibold hover:bg-[#229967] focus:outline-none focus:ring-2 focus:ring-[#28A375] focus:ring-offset-2 transition-all flex items-center gap-2 shadow-sm">
                                <i data-lucide="save" class="w-4 h-4"></i>
                                <span x-text="showAdd ? 'Save Brand' : 'Update Brand'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        @include('admin.items.modals.import_brand_modal')

    </div>

    {{-- Delete confirmation form --}}
    <form id="delete-form" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function brandsApp() {
            return {
                showAdd: false,
                showEdit: false,
                isSaving: false,
                isImportModalOpen: false,
                selectedFile: null,
                editId: null,
                searchQuery: '',
                editForm: {
                    name: '',
                    description: '',
                    status: 'active'
                },

                matchesSearch(brand) {
                    if (this.searchQuery === '') return true;
                    const query = this.searchQuery.toLowerCase();
                    return brand.name.toLowerCase().includes(query) ||
                        (brand.description && brand.description.toLowerCase().includes(query));
                },

                openAddModal() {
                    this.showAdd = true;
                    this.editForm = {
                        name: '',
                        description: '',
                        status: 'active'
                    };
                    this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                },

                editBrand(brand) {
                    this.editId = brand.id;
                    this.editForm = {
                        name: brand.name,
                        description: brand.description,
                        status: brand.status
                    };
                    this.showEdit = true;
                    this.$nextTick(() => { if (typeof lucide !== 'undefined') lucide.createIcons(); });
                },

                closeModal() {
                    this.showAdd = false;
                    this.showEdit = false;
                },

                confirmDelete(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This brand will be permanently deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#28A375',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-form');
                            form.action = url;
                            form.submit();
                        }
                    });
                }
            }
        }
    </script>
@endsection
