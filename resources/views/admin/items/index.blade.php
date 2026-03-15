@extends('admin.admin_master')
@section('admin')
    <div x-data="itemsApp()">
        @section('title')
            Items Management - XaliyePro
        @endsection

        {{-- Page Header --}}
        <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Items</h1>
                <p class="text-gray-500 mt-1">Manage your products and services catalog</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <button @click="isImportModalOpen = true; selectedFile = null" type="button"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors relative h-[38px]">
                    <i data-lucide="upload" class="w-4 h-4"></i> Import
                </button>

                <a href="{{ route('items.export') }}"
                    class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg text-sm font-bold hover:bg-gray-50 flex items-center gap-2 shadow-sm transition-colors h-[38px] flex items-center">
                    <i data-lucide="download" class="w-4 h-4"></i> Export
                </a>

                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#28A375] text-white rounded-lg text-sm font-medium hover:bg-[#229967] transition-all shadow-sm h-[38px]">
                    <i data-lucide="plus" class="w-4 h-4"></i>
                    Add Item
                </button>
            </div>
        </div>

        {{-- Stats Cards --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            @include('admin.partials.stats_card', [
                'title' => 'Total Items',
                'value' => number_format($stats['total']),
                'icon' => 'package',
                'subtitle' => number_format($stats['products']) . ' Products, ' . number_format($stats['services']) . ' Services'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Products',
                'value' => number_format($stats['products']),
                'icon' => 'box',
                'color' => '#3B82F6',
                'iconBg' => 'bg-blue-500',
                'iconShadow' => 'shadow-blue-100',
                'subtitle' => 'Product items'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Services',
                'value' => number_format($stats['services']),
                'icon' => 'briefcase',
                'color' => '#8B5CF6',
                'iconBg' => 'bg-purple-500',
                'iconShadow' => 'shadow-purple-100',
                'subtitle' => 'Service items'
            ])

            @include('admin.partials.stats_card', [
                'title' => 'Inventory Value',
                'value' => '$' . number_format($stats['total_value'], 0),
                'icon' => 'dollar-sign',
                'color' => '#28A375',
                'iconBg' => 'bg-[#28A375]',
                'iconShadow' => 'shadow-green-100',
                'subtitle' => 'Product valuation'
            ])
        </div>

        {{-- Search & Filter --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-6">
            <form action="{{ route('items.index') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="lg:col-span-1">
                    <div class="relative">
                        <i data-lucide="search" class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 -translate-y-1/2"></i>
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Search by name or SKU..."
                            class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    </div>
                </div>

                <select name="type" onchange="this.form.submit()"
                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-[#28A375]">
                    <option value="All Types" {{ request('type') == 'All Types' ? 'selected' : '' }}>All Types</option>
                    <option value="Product" {{ request('type') == 'Product' ? 'selected' : '' }}>Product</option>
                    <option value="Service" {{ request('type') == 'Service' ? 'selected' : '' }}>Service</option>
                </select>

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
                <h2 class="text-lg font-bold text-gray-900">All Items</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Item Info</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Category</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Type</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Price</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Stock</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @if ($items->count() > 0)
                            @foreach ($items as $item)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0 shadow-sm">
                                            <i data-lucide="{{ $item->type === 'service' ? 'briefcase' : 'package' }}"
                                                class="w-5 h-5 text-gray-500"></i>
                                        </div>
                                        <div>
                                            <a href="{{ route('items.details', $item->id) }}"
                                                class="text-sm font-semibold text-blue-600 hover:text-blue-800 hover:underline transition-colors block">
                                                {{ $item->name }}
                                            </a>
                                            <div class="text-xs text-gray-500">{{ $item->sku }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 font-medium">{{ $item->category->name ?? 'N/A' }}</div>
                                    @if ($item->brand)
                                        <div class="text-xs text-gray-500 italic">{{ $item->brand->name }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->type === 'service' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                                        {{ ucfirst($item->type) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right whitespace-nowrap">
                                    ${{ number_format($item->selling_price, 2) }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-right whitespace-nowrap">
                                    @if ($item->type === 'service')
                                        <span class="text-purple-600">N/A</span>
                                    @else
                                        <div class="flex flex-col items-end">
                                            <span
                                                class="{{ $item->current_stock <= $item->reorder_level ? 'text-red-600' : 'text-gray-900' }}">
                                                {{ number_format($item->current_stock) }} {{ $item->unit->short_name ?? '' }}
                                            </span>
                                            @if ($item->branch)
                                                <span
                                                    class="text-[10px] text-[#28A375] font-semibold bg-green-50 px-2 py-0.5 rounded-full border border-green-100 mt-1">
                                                    {{ $item->branch->name }}
                                                </span>
                                            @elseif ($item->store)
                                                <span
                                                    class="text-[10px] text-blue-600 font-semibold bg-blue-50 px-2 py-0.5 rounded-full border border-blue-100 mt-1">
                                                    {{ $item->store->name }}
                                                </span>
                                            @endif
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-700' }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right whitespace-nowrap">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('items.details', $item->id) }}"
                                            class="p-1.5 text-gray-400 hover:text-blue-600 transition-colors"
                                            title="View Details">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>
                                        <button @click="editItem({{ $item->toJson() }})"
                                            class="p-1.5 text-gray-400 hover:text-[#28A375] transition-colors" title="Edit">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </button>
                                        <button @click="confirmDelete('{{ route('items.destroy', $item->id) }}')"
                                            class="p-1.5 text-gray-400 hover:text-red-600 transition-colors" title="Delete">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <i data-lucide="package-search" class="w-12 h-12 text-gray-300 mb-3"></i>
                                        <h4 class="text-base font-bold text-gray-900">No items found</h4>
                                        <p class="text-sm text-gray-500 mt-1">Start by adding your first product or service.</p>
                                    </div>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            @if ($items->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    {{ $items->links() }}
                </div>
            @endif
        </div>

        @include('admin.items.modals.item_modals')
        @include('admin.items.modals.import_item_modal')
    </div>

    {{-- Delete confirmation form --}}
    <form id="delete-form" method="POST" class="hidden">
        @csrf @method('DELETE')
    </form>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function itemsApp() {
            return {
                showAdd: false,
                showEdit: false,
                isSaving: false,
                isImportModalOpen: false,
                selectedFile: null,
                editId: null,
                selectInstances: {},
                editForm: {
                    name: '',
                    sku: '',
                    barcode: '',
                    category_id: '',
                    brand_id: '',
                    type: 'product',
                    cost_price: '',
                    selling_price: '',
                    tax_rate: '',
                    unit_id: '',
                    reorder_level: '',
                    reorder_quantity: '',
                    track_inventory: 1,
                    location: '',
                    description: '',
                    status: 'active',
                    sales_account_id: '',
                    purchase_account_id: '',
                    inventory_asset_account_id: '',
                    cogs_account_id: '',
                    branch_id: '',
                    store_id: ''
                },
                categoriesData: @json($categories),

                openAddModal() {
                    this.showAdd = true;
                    this.isSaving = false;
                    this.editForm = {
                        name: '',
                        sku: '',
                        barcode: '',
                        category_id: '',
                        brand_id: '',
                        type: 'product',
                        cost_price: '',
                        selling_price: '',
                        tax_rate: '',
                        unit_id: '',
                        reorder_level: '',
                        reorder_quantity: '',
                        track_inventory: 1,
                        description: '',
                        status: 'active',
                        sales_account_id: '',
                        purchase_account_id: '',
                        inventory_asset_account_id: '',
                        cogs_account_id: '',
                        branch_id: '',
                        store_id: ''
                    };
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                        this.initSelects();
                    });
                },

                applyCategoryDefaults(categoryId) {
                    if (!categoryId) return;

                    const category = this.categoriesData.find(c => c.id == categoryId);
                    if (category) {
                        if (!this.editForm.sales_account_id && category.sales_account_id) {
                            this.editForm.sales_account_id = category.sales_account_id;
                            if (this.selectInstances['sales_account_id']) this.selectInstances['sales_account_id'].setValue(category.sales_account_id);
                        }
                        if (!this.editForm.purchase_account_id && category.purchase_account_id) {
                            this.editForm.purchase_account_id = category.purchase_account_id;
                            if (this.selectInstances['purchase_account_id']) this.selectInstances['purchase_account_id'].setValue(category.purchase_account_id);
                        }
                        if (!this.editForm.inventory_asset_account_id && category.inventory_asset_account_id) {
                            this.editForm.inventory_asset_account_id = category.inventory_asset_account_id;
                            if (this.selectInstances['inventory_asset_account_id']) this.selectInstances['inventory_asset_account_id'].setValue(category.inventory_asset_account_id);
                        }
                        if (!this.editForm.cogs_account_id && category.cogs_account_id) {
                            this.editForm.cogs_account_id = category.cogs_account_id;
                            if (this.selectInstances['cogs_account_id']) this.selectInstances['cogs_account_id'].setValue(category.cogs_account_id);
                        }
                    }
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
                            sortField: { field: "text", direction: "asc" }
                        });

                        instance.on('change', (value) => {
                            const modelName = select.getAttribute('x-model').replace('editForm.', '');
                            this.editForm[modelName] = value;
                            if (modelName === 'category_id') {
                                this.applyCategoryDefaults(value);
                            }
                        });

                        this.selectInstances[select.getAttribute('name')] = instance;
                    });
                },

                editItem(item) {
                    this.editId = item.id;
                    this.editForm = {
                        name: item.name,
                        sku: item.sku,
                        barcode: item.barcode,
                        category_id: item.category_id,
                        brand_id: item.brand_id,
                        type: item.type,
                        cost_price: item.cost_price,
                        selling_price: item.selling_price,
                        tax_rate: item.tax_rate,
                        unit_id: item.unit_id,
                        reorder_level: item.reorder_level,
                        reorder_quantity: item.reorder_quantity,
                        track_inventory: item.track_inventory,
                        location: item.location,
                        description: item.description,
                        status: item.status,
                        sales_account_id: item.sales_account_id,
                        purchase_account_id: item.purchase_account_id,
                        inventory_asset_account_id: item.inventory_asset_account_id,
                        cogs_account_id: item.cogs_account_id,
                        branch_id: item.branch_id,
                        store_id: item.store_id
                    };
                    this.showEdit = true;
                    this.isSaving = false;
                    this.$nextTick(() => {
                        if (typeof lucide !== 'undefined') lucide.createIcons();
                        this.initSelects();
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
            }
        }
    </script>
@endsection
