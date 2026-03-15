@extends('admin.admin_master')

@section('title', 'Services Management - XaliyePro')

@section('admin')
    <div x-data="servicesApp()">
        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Services</h1>
                <p class="text-gray-500 mt-1">Manage your services catalog</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="isImportModalOpen = true; selectedFile = null" type="button"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors relative h-[38px]">
                    <i data-lucide="upload" class="w-4 h-4"></i> Import
                </button>

                <a href="{{ route('services.export') }}"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors h-[38px] flex items-center">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </a>

                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all shadow-sm h-[38px]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Service
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm text-gray-500 mb-1">Total Services</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['total']) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">All time</p>
                    </div>
                    <div class="w-12 h-12 bg-[#28A375] rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="briefcase" class="w-6 h-6 text-white"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-5">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <p class="text-sm text-gray-500 mb-1">Active Services</p>
                        <h3 class="text-3xl font-bold text-gray-900">{{ number_format($stats['active']) }}</h3>
                        <p class="text-xs text-gray-500 mt-1">Currently available</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i data-lucide="check-circle" class="w-6 h-6 text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>

        @if (session('message'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                <span class="font-medium">Success!</span> {{ session('message') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                <ul class="list-disc pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search & Filter --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('services.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-2">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or SKU..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <select name="category" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Categories">All Categories</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->name }}" {{ request('category') == $cat->name ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>

                <select name="status" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Status" {{ request('status', 'All Status') == 'All Status' ? 'selected' : '' }}>All
                        Status</option>
                    <option value="Active" {{ request('status') == 'Active' ? 'selected' : '' }}>Active</option>
                    <option value="Inactive" {{ request('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </form>
        </div>

        {{-- Table --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">All Services</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Service Info</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Price</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if (count($services) > 0)
                            @foreach ($services as $service)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center gap-3">
                                            <div
                                                class="w-10 h-10 bg-purple-50 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm border border-purple-100">
                                                <i data-lucide="briefcase" class="w-5 h-5 text-purple-600"></i>
                                            </div>
                                            <div>
                                                <a href="{{ route('services.details', $service->id) }}"
                                                    class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors block">
                                                    {{ $service->name }}
                                                </a>
                                                <div class="text-xs text-gray-500">{{ $service->sku }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">{{ $service->category->name ?? 'N/A' }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right whitespace-nowrap">
                                        ${{ number_format($service->selling_price, 2) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $service->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                            {{ ucfirst($service->status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right whitespace-nowrap">
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('services.details', $service->id) }}"
                                                class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors"
                                                title="View Details">
                                                <i data-lucide="eye" class="w-4 h-4"></i>
                                            </a>
                                            <button @click="editService({{ $service->toJson() }})"
                                                class="p-1.5 text-gray-400 hover:text-[#28A375] transition-colors" title="Edit">
                                                <i data-lucide="edit" class="w-4 h-4"></i>
                                            </button>
                                            <button @click="confirmDelete('{{ route('services.destroy', $service->id) }}')"
                                                class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="briefcase" class="w-12 h-12 text-gray-300 mb-3"></i>
                                        <h4 class="text-base font-bold text-gray-900">No services found</h4>
                                        <p class="text-sm text-gray-500 mt-1">Start by adding your first service offering.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            @if ($services->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $services->links('pagination::tailwind') }}
                </div>
            @endif
        </div>

        @include('admin.services.modals.service_modals')
        @include('admin.services.modals.import_service_modal')

        <form id="delete-form" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('servicesApp', () => ({
                showAdd: false,
                showEdit: false,
                isSaving: false,
                isImportModalOpen: false,
                selectedFile: null,
                editId: null,
                activeFormStep: 1,
                selectInstances: {},
                editForm: {
                    name: '',
                    sku: '',
                    category_id: '',
                    cost_price: 0,
                    selling_price: 0,
                    tax_rate: 0,
                    unit_id: '',
                    description: '',
                    status: 'active',
                    sales_account_id: '',
                    purchase_account_id: '',
                },

                openAddModal() {
                    this.showAdd = true;
                    this.isSaving = false;
                    this.activeFormStep = 1;
                    this.editId = null;
                    this.editForm = {
                        name: '',
                        sku: '',
                        category_id: '',
                        cost_price: 0,
                        selling_price: 0,
                        tax_rate: 0,
                        unit_id: '',
                        description: '',
                        status: 'active',
                        sales_account_id: '',
                        purchase_account_id: '',
                    };
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                        this.initSelects();
                    });
                },

                initSelects() {
                    for (const key in this.selectInstances) {
                        this.selectInstances[key].destroy();
                    }
                    this.selectInstances = {};

                    const selects = document.querySelectorAll('.searchable-select');
                    selects.forEach(select => {
                        const instance = new TomSelect(select, {
                            create: false,
                            sortField: {
                                field: "text",
                                direction: "asc"
                            }
                        });

                        instance.on('change', (value) => {
                            const modelName = select.getAttribute('x-model').replace('editForm.', '');
                            this.editForm[modelName] = value;
                        });

                        this.selectInstances[select.getAttribute('name')] = instance;
                    });
                },

                validateStep(stepIndex) {
                    const stepElement = document.getElementById('service-step-' + stepIndex);
                    if (!stepElement) return true;

                    const inputs = stepElement.querySelectorAll('input[required], select[required], textarea[required]');
                    let isValid = true;

                    for (let input of inputs) {
                        if (!input.checkValidity()) {
                            input.reportValidity();
                            isValid = false;
                            break;
                        }
                    }
                    return isValid;
                },

                editService(service) {
                    this.editId = service.id;
                    this.editForm = {
                        name: service.name,
                        sku: service.sku,
                        category_id: service.category_id,
                        cost_price: service.cost_price,
                        selling_price: service.selling_price,
                        tax_rate: service.tax_rate,
                        unit_id: service.unit_id,
                        description: service.description,
                        status: service.status,
                        sales_account_id: service.sales_account_id,
                        purchase_account_id: service.purchase_account_id,
                    };
                    this.showEdit = true;
                    this.isSaving = false;
                    this.activeFormStep = 1;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                        this.initSelects();
                    });
                },

                confirmDelete(url) {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This service will be permanently deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#28A375',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            const form = document.getElementById('delete-form');
                            form.action = url;
                            form.submit();
                        }
                    });
                },

                closeModal() {
                    this.showAdd = false;
                    this.showEdit = false;
                    for (const key in this.selectInstances) {
                        this.selectInstances[key].destroy();
                    }
                    this.selectInstances = {};
                }
            }));
        });
    </script>
@endsection
