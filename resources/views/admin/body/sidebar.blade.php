    <!-- Sidebar Container -->
    <aside 
        class="fixed inset-y-0 left-0 w-64 bg-white shadow-xl flex flex-col border-r border-gray-200 z-50 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 lg:shadow-none h-screen"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
        x-cloak>
    <!-- Logo Section -->
    @php
        $companyName = $companySettings->company_name ?? 'XaliyePro';
        $initials = collect(explode(' ', $companyName))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
        $logoPath = ($companySettings->logo ?? null) && file_exists(public_path($companySettings->logo))
            ? asset($companySettings->logo) : null;
    @endphp
    <div
        class="h-16 flex items-center justify-between px-4 border-b border-gray-200 bg-gradient-to-r from-[#28A375] to-[#229967] shrink-0">
        <div class="flex items-center gap-2.5 min-w-0">
            <div
                class="w-9 h-9 bg-white rounded-lg flex items-center justify-center shrink-0 overflow-hidden shadow-sm">
                @if ($logoPath)
                    <img src="{{ $logoPath }}" alt="{{ $companyName }}" class="w-full h-full object-cover">
                @else
                    <span class="text-[#28A375] font-extrabold text-sm">{{ $initials }}</span>
                @endif
            </div>
            <h1 class="font-bold text-base text-white truncate">{{ $companyName }}</h1>
        </div>
        
        <!-- Mobile Close Button -->
        <button @click="sidebarOpen = false" class="lg:hidden text-white hover:bg-white/10 p-1.5 rounded-lg transition-colors">
            <i data-lucide="x" class="w-6 h-6"></i>
        </button>
    </div>

    <!-- Mobile Context Selectors (Visible ONLY on mobile/tablet) -->
    <div class="px-3 py-4 border-b border-gray-100 lg:hidden bg-gray-50/50 space-y-3">
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2">Branch</label>
            <div class="relative group">
                <select onchange="updateContext({ active_branch_id: this.value })"
                    class="w-full pl-9 pr-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-[#28A375] focus:border-[#28A375] outline-none transition-all">
                    @foreach($accessibleBranches as $branch)
                        <option value="{{ $branch->id }}" {{ $activeBranchId == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                    @endforeach
                </select>
                <i data-lucide="building-2" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                <i data-lucide="chevron-down" class="absolute right-3 top-3.5 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
            </div>
        </div>
        <div class="space-y-1">
            <label class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-2">Store</label>
            <div class="relative group">
                @php
                    $activeBranch = $accessibleBranches->where('id', $activeBranchId)->first();
                    $accessibleStores = $activeBranch ? $activeBranch->stores : collect();
                @endphp
                <select onchange="updateContext({ active_store_id: this.value })"
                    class="w-full pl-9 pr-4 py-2.5 text-sm font-semibold text-gray-700 bg-white border border-gray-200 rounded-xl appearance-none focus:ring-2 focus:ring-[#28A375] focus:border-[#28A375] outline-none transition-all">
                    @foreach($accessibleStores as $store)
                        <option value="{{ $store->id }}" {{ $activeStoreId == $store->id ? 'selected' : '' }}>
                            {{ $store->name }}
                        </option>
                    @endforeach
                </select>
                <i data-lucide="store" class="absolute left-3 top-3 w-4 h-4 text-gray-400"></i>
                <i data-lucide="chevron-down" class="absolute right-3 top-3.5 w-3.5 h-3.5 text-gray-400 pointer-events-none"></i>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-2 custom-scrollbar" x-data="{ 
        activeMenu: '{{ request()->is('sales*') ? 'sales' : (request()->is('purchases*') ? 'purchases' : (request()->is('items*') ? 'items' : (request()->is('inventory*') ? 'inventory' : (request()->is('contacts*') ? 'contacts' : (request()->is('accounting*') || request()->routeIs('reports.trial-balance') ? 'accounting' : (request()->routeIs('reports.*') ? 'reports' : (request()->routeIs('settings.*') ? 'settings' : ''))))))) }}',
        toggleMenu(menu) {
            this.activeMenu = this.activeMenu === menu ? '' : menu;
        }
    }">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}"
            class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('dashboard') ? 'bg-[#28A375] text-white' : 'text-gray-700 hover:bg-gray-100' }} mb-1">
            <i data-lucide="layout-dashboard"
                class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-white' : 'text-gray-600' }}"></i>
            <span>Dashboard</span>
        </a>

        <!-- Sales Section -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'sales' ? 'bg-gray-100' : ''"
                @click="toggleMenu('sales')">
                <div class="flex items-center gap-3">
                    <i data-lucide="shopping-bag" :class="activeMenu === 'sales' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'sales' ? 'font-bold' : 'normal'">Sales</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'sales' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'sales'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('sales.orders.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('sales.orders.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales Orders</a>
                <a href="{{ route('sales.receipts.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('sales.receipts.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Delivery Notes</a>
                <a href="{{ route('sales.invoices.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('sales.invoices.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales Invoices</a>
                <a href="{{ route('sales.payments.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('sales.payments.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Payment-In</a>
                <a href="{{ route('sales.returns.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('sales.returns.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales Returns</a>
            </div>
        </div>

        <!-- Purchases -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'purchases' ? 'bg-gray-100' : ''"
                @click="toggleMenu('purchases')">
                <div class="flex items-center gap-3">
                    <i data-lucide="shopping-cart" :class="activeMenu === 'purchases' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'purchases' ? 'font-bold' : 'normal'">Purchases</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'purchases' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'purchases'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('purchases.orders.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.orders.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Purchase Orders</a>
                <a href="{{ route('purchases.receipts.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.receipts.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Goods Receipts</a>
                <a href="{{ route('purchases.bills.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.bills.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Vendor Bills</a>
                <a href="{{ route('purchases.payments.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.payments.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Vendor Payments</a>
                <a href="{{ route('purchases.returns.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.returns.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Purchase Returns</a>
            </div>
        </div>

        <!-- POS -->
        <a href="#" class="flex items-center gap-3 px-3 py-2.5 text-sm rounded-lg text-gray-700 hover:bg-gray-100 mb-1">
            <i data-lucide="trending-up" class="w-5 h-5 text-gray-600"></i>
            <span>POS</span>
        </a>

        <!-- Items -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'items' ? 'bg-gray-100' : ''"
                @click="toggleMenu('items')">
                <div class="flex items-center gap-3">
                    <i data-lucide="package" :class="activeMenu === 'items' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'items' ? 'font-bold' : 'normal'">Items</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'items' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'items'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('items.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.index') || request()->routeIs('items.details') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">All Items</a>
                <a href="{{ route('items.categories.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.categories.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Categories</a>
                <a href="{{ route('items.brands.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.brands.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Brands</a>
                <a href="{{ route('items.units.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.units.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Units</a>
            </div>
        </div>

        <!-- Inventory -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'inventory' ? 'bg-gray-100' : ''"
                @click="toggleMenu('inventory')">
                <div class="flex items-center gap-3">
                    <i data-lucide="boxes" :class="activeMenu === 'inventory' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'inventory' ? 'font-bold' : 'normal'">Inventory</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'inventory' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'inventory'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('inventory.on_hand') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('inventory.on_hand') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Stock On Hand</a>
                <a href="{{ route('inventory.adjustments.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('inventory.adjustments.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Stock Adjustment</a>
                <a href="{{ route('inventory.transfers.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('inventory.transfers.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Stock Transfer</a>
                <a href="{{ route('inventory.movements') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('inventory.movements') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Stock Movements</a>
                <a href="{{ route('inventory.low_stock') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('inventory.low_stock') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Low Stock Alert</a>
            </div>
        </div>

        <!-- Contacts -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'contacts' ? 'bg-gray-100' : ''"
                @click="toggleMenu('contacts')">
                <div class="flex items-center gap-3">
                    <i data-lucide="users" :class="activeMenu === 'contacts' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'contacts' ? 'font-bold' : 'normal'">Contacts</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'contacts' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'contacts'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('contacts.customers.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('contacts.customers.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Customers</a>
                <a href="{{ route('contacts.vendors.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('contacts.vendors.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Vendors</a>
            </div>
        </div>

        <!-- Accounting -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'accounting' ? 'bg-gray-100' : ''"
                @click="toggleMenu('accounting')">
                <div class="flex items-center gap-3">
                    <i data-lucide="book-open" :class="activeMenu === 'accounting' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'accounting' ? 'font-bold' : 'normal'">Accounting</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'accounting' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'accounting'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('reports.dashboard') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.dashboard') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Accounting Dashboard</a>
                <a href="{{ route('accounting.accounts.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.accounts.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Chart of Accounts</a>
                <a href="{{ route('accounting.journal.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.journal.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Journal Entries</a>
                <a href="{{ route('accounting.ledger.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.ledger.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">General Ledger</a>
                <a href="{{ route('reports.trial-balance') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.trial-balance') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Trial Balance</a>
                <a href="{{ route('accounting.reconciliation.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.reconciliation.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Bank Reconciliation</a>
            </div>
        </div>

        <!-- Reports -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'reports' ? 'bg-gray-100' : ''"
                @click="toggleMenu('reports')">
                <div class="flex items-center gap-3">
                    <i data-lucide="bar-chart-3" :class="activeMenu === 'reports' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'reports' ? 'font-bold' : 'normal'">Reports</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'reports' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'reports'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('reports.dashboard') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.dashboard') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Reports Dashboard</a>
                <a href="{{ route('reports.profit-loss') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.profit-loss') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Profit & Loss</a>
                <a href="{{ route('reports.balance-sheet') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.balance-sheet') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Balance Sheet</a>
                <a href="{{ route('reports.cash-flow') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.cash-flow') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Cash Flow</a>
                <a href="{{ route('reports.trial-balance') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.trial-balance') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Trial Balance</a>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sales Reports</div>
                <a href="{{ route('reports.sales-summary') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.sales-summary') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales Summary</a>
                <a href="{{ route('reports.sales-by-customer') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.sales-by-customer') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales by Customer</a>
                <a href="{{ route('reports.sales-by-item') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.sales-by-item') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales by Item</a>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Inventory Reports</div>
                <a href="{{ route('reports.stock-on-hand') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.stock-on-hand') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Stock On Hand</a>
                <a href="{{ route('reports.inventory-valuation') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.inventory-valuation') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Valuation</a>
                <a href="{{ route('reports.stock-movement') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.stock-movement') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Movement</a>
            </div>
        </div>

        <!-- Settings -->
        <div class="mb-1">
            <button
                class="w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg hover:bg-gray-100 transition-colors"
                :class="activeMenu === 'settings' ? 'bg-gray-100' : ''"
                @click="toggleMenu('settings')">
                <div class="flex items-center gap-3">
                    <i data-lucide="settings" :class="activeMenu === 'settings' ? 'text-[#28A375]' : 'text-gray-600'" class="w-5 h-5"></i>
                    <span class="text-gray-700" :class="activeMenu === 'settings' ? 'font-bold' : 'normal'">Settings</span>
                </div>
                <i data-lucide="chevron-down" class="w-4 h-4 text-gray-400 transition-transform duration-200" :class="activeMenu === 'settings' ? 'rotate-180' : ''"></i>
            </button>
            <div x-show="activeMenu === 'settings'" x-cloak x-collapse class="ml-6 mt-1 space-y-1">
                <a href="{{ route('settings.company') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.company') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Company Settings</a>
                <a href="{{ route('settings.employees.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.employees.index') || request()->routeIs('settings.employees.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Employees</a>
                <a href="{{ route('settings.departments.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.departments.index') || request()->routeIs('settings.departments.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Departments</a>
                <a href="{{ route('settings.users.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.users.index') || request()->routeIs('settings.users.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Users & Roles</a>
                <a href="{{ route('settings.branches.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.branches.index') || request()->routeIs('settings.branches.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Branches & Stores</a>
                <a href="{{ route('settings.backup.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.backup.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Backup & Restore</a>
            </div>
        </div>
    </nav>

    <!-- Sidebar Footer -->
    <div class="p-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center gap-3">
            <div
                class="w-10 h-10 bg-gradient-to-br from-[#28A375] to-[#229967] rounded-full flex items-center justify-center text-white overflow-hidden">
                @if (auth()->user()->photo)
                    <img src="{{ asset('upload/admin_images/' . auth()->user()->photo) }}" alt="Avatar"
                        class="w-full h-full object-cover">
                @else
                    <span class="text-sm font-medium">{{ substr(auth()->user()->name ?? 'Admin', 0, 2) }}</span>
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-gray-900 truncate">{{ auth()->user()->name ?? 'Admin User' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ auth()->user()->email ?? 'admin@XaliyePro.com' }}</p>
            </div>
        </div>
    </div>
</aside>

