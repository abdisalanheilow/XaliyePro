<aside class="w-64 bg-white shadow-lg flex flex-col border-r border-gray-200">
    <!-- Logo -->
    @php
        $companyName = $companySetting->company_name ?? 'XaliyePro';
        $initials = collect(explode(' ', $companyName))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->implode('');
        $logoPath = ($companySetting->logo ?? null) && file_exists(public_path($companySetting->logo))
            ? asset($companySetting->logo) : null;
    @endphp
    <div
        class="h-16 flex items-center justify-between px-4 border-b border-gray-200 bg-gradient-to-r from-[#28A375] to-[#229967]">
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
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto py-4 px-2">
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
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->is('sales*') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="shopping-bag" class="w-5 h-5 {{ request()->is('sales*') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span class="text-gray-700 font-{{ request()->is('sales*') ? 'bold' : 'normal' }}">Sales</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->is('sales*') ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="submenu ml-6 mt-1 space-y-1 {{ request()->is('sales*') ? '' : 'hidden' }}">
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
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->is('purchases*') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="shopping-cart"
                        class="w-5 h-5 {{ request()->is('purchases*') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span
                        class="text-gray-700 font-{{ request()->is('purchases*') ? 'bold' : 'normal' }}">Purchases</span>
                </div>
                <i data-lucide="chevron-down"
                    class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->is('purchases*') ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="submenu ml-6 mt-1 space-y-1 {{ request()->is('purchases*') ? '' : 'hidden' }}">
                <a href="{{ route('purchases.orders.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.orders.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Purchase Orders</a>
                <a href="{{ route('purchases.receipts.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.receipts.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Goods Receipts</a>
                <a href="{{ route('purchases.bills.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.bills.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Vendor Bills</a>
                <a href="{{ route('purchases.payments.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.payments.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Vendor Payments</a>
                <a href="{{ route('purchases.returns.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('purchases.returns.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Purchase Returns</a>
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
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->is('items*') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="package"
                        class="w-5 h-5 {{ request()->is('items*') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span
                        class="text-gray-700 font-{{ request()->is('items*') ? 'bold' : 'normal' }}">Items</span>
                </div>
                <i data-lucide="chevron-down"
                    class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->is('items*') ? 'rotate-180' : '' }}"></i>
            </button>
            <div
                class="submenu ml-6 mt-1 space-y-1 {{ request()->is('items*') ? '' : 'hidden' }}">
                <a href="{{ route('items.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.index') || request()->routeIs('items.details') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">All Items</a>
                <a href="{{ route('items.categories.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.categories.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Categories</a>
                <a href="{{ route('items.brands.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.brands.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Brands</a>
                <a href="{{ route('items.units.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('items.units.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Units</a>
            </div>
        </div>

        <!-- Inventory -->
        <div class="mb-1">
            <button
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->is('inventory*') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="boxes" class="w-5 h-5 {{ request()->is('inventory*') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span class="text-gray-700 font-{{ request()->is('inventory*') ? 'bold' : 'normal' }}">Inventory</span>
                </div>
                <i data-lucide="chevron-down" class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->is('inventory*') ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="submenu ml-6 mt-1 space-y-1 {{ request()->is('inventory*') ? '' : 'hidden' }}">
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
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->is('contacts*') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="users"
                        class="w-5 h-5 {{ request()->is('contacts*') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span
                        class="text-gray-700 font-{{ request()->is('contacts*') ? 'bold' : 'normal' }}">Contacts</span>
                </div>
                <i data-lucide="chevron-down"
                    class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->is('contacts*') ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="submenu ml-6 mt-1 space-y-1 {{ request()->is('contacts*') ? '' : 'hidden' }}">
                <a href="{{ route('contacts.customers.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('contacts.customers.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Customers</a>
                <a href="{{ route('contacts.vendors.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('contacts.vendors.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Vendors</a>
            </div>
        </div>

        <!-- Accounting -->
        <div class="mb-1">
            <button
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->is('accounting*') || request()->routeIs('reports.trial-balance') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="book-open"
                        class="w-5 h-5 {{ request()->is('accounting*') || request()->routeIs('reports.trial-balance') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span
                        class="text-gray-700 font-{{ request()->is('accounting*') || request()->routeIs('reports.trial-balance') ? 'bold' : 'normal' }}">Accounting</span>
                </div>
                <i data-lucide="chevron-down"
                    class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->is('accounting*') || request()->routeIs('reports.trial-balance') ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="submenu ml-6 mt-1 space-y-1 {{ request()->is('accounting*') || request()->routeIs('reports.trial-balance') ? '' : 'hidden' }}">
                <a href="{{ route('reports.dashboard') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.dashboard') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Accounting
                    Dashboard</a>
                <a href="{{ route('accounting.accounts.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.accounts.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Chart
                    of Accounts</a>
                <a href="{{ route('accounting.journal.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.journal.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Journal
                    Entries</a>
                <a href="{{ route('accounting.ledger.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.ledger.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">General
                    Ledger</a>
                <a href="{{ route('reports.trial-balance') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.trial-balance') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Trial Balance</a>
                <a href="{{ route('accounting.reconciliation.index') }}" class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('accounting.reconciliation.*') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Bank
                    Reconciliation</a>
            </div>
        </div>

        <!-- Reports -->
        <div class="mb-1">
            <button
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('reports.*') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="bar-chart-3"
                        class="w-5 h-5 {{ request()->routeIs('reports.*') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span
                        class="text-gray-700 font-{{ request()->routeIs('reports.*') ? 'bold' : 'normal' }}">Reports</span>
                </div>
                <i data-lucide="chevron-down"
                    class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->routeIs('reports.*') ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="submenu ml-6 mt-1 space-y-1 {{ request()->routeIs('reports.*') ? '' : 'hidden' }}">
                <a href="{{ route('reports.dashboard') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.dashboard') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Reports
                    Dashboard</a>
                <a href="{{ route('reports.profit-loss') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.profit-loss') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Profit
                    & Loss</a>
                <a href="{{ route('reports.balance-sheet') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.balance-sheet') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Balance
                    Sheet</a>
                <a href="{{ route('reports.cash-flow') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.cash-flow') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Cash
                    Flow</a>
                <a href="{{ route('reports.trial-balance') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.trial-balance') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Trial
                    Balance</a>
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sales Reports
                </div>
                <a href="{{ route('reports.sales-summary') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.sales-summary') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales
                    Summary</a>
                <a href="{{ route('reports.sales-by-customer') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.sales-by-customer') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales
                    by Customer</a>
                <a href="{{ route('reports.sales-by-item') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.sales-by-item') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Sales
                    by Item</a>
                <a href="{{ route('reports.revenue-trends') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.revenue-trends') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Revenue
                    Trends</a>
                
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Inventory Reports</div>
                <a href="{{ route('reports.stock-on-hand') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.stock-on-hand') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Stock On Hand</a>
                <a href="{{ route('reports.inventory-valuation') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.inventory-valuation') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Valuation</a>
                <a href="{{ route('reports.stock-movement') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.stock-movement') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Movement</a>
                
                <div class="pt-2 pb-1 px-3 text-[10px] font-bold text-gray-400 uppercase tracking-wider">Financial Detail</div>
                <a href="{{ route('reports.customer-statement') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.customer-statement') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Customer Statement</a>
                <a href="{{ route('reports.vendor-statement') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('reports.vendor-statement') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">Vendor Statement</a>
            </div>
        </div>

        <!-- Settings -->
        <div class="mb-1">
            <button
                class="menu-toggle w-full flex items-center justify-between px-3 py-2.5 text-sm rounded-lg {{ request()->routeIs('settings.*') ? 'bg-gray-100' : '' }} hover:bg-gray-100"
                onclick="toggleSubmenu(this)">
                <div class="flex items-center gap-3">
                    <i data-lucide="settings"
                        class="w-5 h-5 {{ request()->routeIs('settings.*') ? 'text-[#28A375]' : 'text-gray-600' }}"></i>
                    <span
                        class="text-gray-700 font-{{ request()->routeIs('settings.*') ? 'bold' : 'normal' }}">Settings</span>
                </div>
                <i data-lucide="chevron-down"
                    class="chevron-icon w-4 h-4 text-gray-400 transition-transform {{ request()->routeIs('settings.*') ? 'rotate-180' : '' }}"></i>
            </button>
            <div class="submenu ml-6 mt-1 space-y-1 {{ request()->routeIs('settings.*') ? '' : 'hidden' }}">
                <a href="{{ route('settings.company') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.company') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Company Settings
                </a>
                <a href="{{ route('settings.employees.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.employees.index') || request()->routeIs('settings.employees.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Employees
                </a>
                <a href="{{ route('settings.departments.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.departments.index') || request()->routeIs('settings.departments.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Departments
                </a>
                <a href="{{ route('settings.users.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.users.index') || request()->routeIs('settings.users.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Users & Roles
                </a>
                <a href="{{ route('settings.branches.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.branches.index') || request()->routeIs('settings.branches.show') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Branches &amp; Stores
                </a>
                <a href="{{ route('settings.backup.index') }}"
                    class="block px-3 py-2 text-sm rounded-lg {{ request()->routeIs('settings.backup.index') ? 'bg-[#28A375] text-white' : 'text-gray-600 hover:bg-gray-100' }}">
                    Backup & Restore
                </a>
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

<script>
    function toggleSubmenu(button) {
        const submenu = button.nextElementSibling;
        const icon = button.querySelector('.chevron-icon');

        submenu.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }
</script>
