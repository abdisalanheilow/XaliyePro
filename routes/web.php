<?php

use App\Http\Controllers\AccountingController;
use App\Http\Controllers\BackupController;
use App\Http\Controllers\BankReconciliationController;
use App\Http\Controllers\BranchStoreController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CompanySettingController;
use App\Http\Controllers\ContextController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\CustomerPaymentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DeliveryNoteController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GoodsReceiptController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LockScreenController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PurchaseDashboardController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\PurchaseReturnController;
use App\Http\Controllers\PurchasesController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SalesInvoiceController;
use App\Http\Controllers\SalesOrderController;
use App\Http\Controllers\SalesReturnController;
use App\Http\Controllers\StockAdjustmentController;
use App\Http\Controllers\StockTransferController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserRoleController;
use App\Http\Controllers\VendorController;
use App\Http\Controllers\VendorPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/lock-screen', function () {
        return view('auth.lock-screen');
    })->name('lock-screen');

    Route::post('/lock-screen/unlock', [LockScreenController::class, 'unlock'])->name('lock-screen.unlock');

    // Company Settings
    Route::get('/settings/company', [CompanySettingController::class, 'index'])->name('settings.company');
    Route::put('/settings/company', [CompanySettingController::class, 'update'])->name('settings.company.update');

    // Branches & Stores
    Route::controller(BranchStoreController::class)->group(function () {
        Route::get('/settings/branches-stores', 'index')->name('settings.branches.index');
        Route::get('/settings/branches/{branch}/view', 'showBranch')->name('settings.branches.show');
        Route::post('/settings/branches', 'storeBranch')->name('settings.branches.store');
        Route::put('/settings/branches/{branch}', 'updateBranch')->name('settings.branches.update');
        Route::delete('/settings/branches/{branch}', 'destroyBranch')->name('settings.branches.destroy');

        Route::post('/settings/stores', 'storeStore')->name('settings.stores.store');
        Route::put('/settings/stores/{store}', 'updateStore')->name('settings.stores.update');
        Route::delete('/settings/stores/{store}', 'destroyStore')->name('settings.stores.destroy');
    });

    // Employee Management
    Route::resource('settings/employees', EmployeeController::class)->names([
        'index' => 'settings.employees.index',
        'show' => 'settings.employees.show',
        'store' => 'settings.employees.store',
        'update' => 'settings.employees.update',
        'destroy' => 'settings.employees.destroy',
    ]);

    // Departments
    Route::controller(DepartmentController::class)->group(function () {
        Route::get('/settings/departments', 'index')->name('settings.departments.index');
        Route::get('/settings/departments/{department}', 'show')->name('settings.departments.show');
        Route::post('/settings/departments', 'store')->name('settings.departments.store');
        Route::put('/settings/departments/{department}', 'update')->name('settings.departments.update');
        Route::delete('/settings/departments/{department}', 'destroy')->name('settings.departments.destroy');
    });

    // Users & Roles
    Route::controller(UserRoleController::class)->group(function () {
        Route::get('/settings/users-roles', 'index')->name('settings.users.index');
        Route::get('/settings/users/{user}/view', 'showUser')->name('settings.users.show');
        Route::post('/settings/users', 'storeUser')->name('settings.users.store');
        Route::put('/settings/users/{user}', 'updateUser')->name('settings.users.update');
        Route::delete('/settings/users/{user}', 'destroyUser')->name('settings.users.destroy');

        Route::post('/settings/roles', 'storeRole')->name('settings.roles.store');
        Route::put('/settings/roles/{role}', 'updateRole')->name('settings.roles.update');
        Route::delete('/settings/roles/{role}', 'destroyRole')->name('settings.roles.destroy');
    });

    // Backup & Restore
    Route::controller(BackupController::class)->group(function () {
        Route::get('/settings/backup-restore', 'index')->name('settings.backup.index');
        Route::post('/settings/backup/create', 'create')->name('settings.backup.create');
        Route::post('/settings/backup/restore', 'restore')->name('settings.backup.restore');
        Route::post('/settings/backup/schedule', 'saveSchedule')->name('settings.backup.schedule');
        Route::get('/settings/backup/{filename}/download', 'download')->name('settings.backup.download');
        Route::delete('/settings/backup/{filename}', 'destroy')->name('settings.backup.destroy');
    });

    // Accounting
    Route::controller(AccountingController::class)->group(function () {
        Route::get('/accounting/chart-of-accounts', 'chartOfAccounts')->name('accounting.accounts.index');
        Route::post('/accounting/chart-of-accounts', 'storeAccount')->name('accounting.accounts.store');
        Route::put('/accounting/chart-of-accounts/{id}', 'updateAccount')->name('accounting.accounts.update');
        Route::delete('/accounting/chart-of-accounts/{id}', 'deleteAccount')->name('accounting.accounts.destroy');
        Route::get('/accounting/general-ledger', 'generalLedger')->name('accounting.ledger.index');
        Route::get('/accounting/journal-entries', 'journalEntries')->name('accounting.journal.index');
        Route::post('/accounting/journal-entries', 'storeJournalEntry')->name('accounting.journal.store');
        Route::get('/accounting/journal-entries/{id}', 'showJournalEntry')->name('accounting.journal.show');
        Route::put('/accounting/journal-entries/{id}', 'updateJournalEntry')->name('accounting.journal.update');
        Route::delete('/accounting/journal-entries/{id}', 'deleteJournalEntry')->name('accounting.journal.destroy');
    });

    // Bank Reconciliation
    Route::controller(BankReconciliationController::class)->group(function () {
        Route::get('/accounting/reconciliation', 'index')->name('accounting.reconciliation.index');
        Route::get('/accounting/reconciliation/create', 'create')->name('accounting.reconciliation.create');
        Route::post('/accounting/reconciliation', 'store')->name('accounting.reconciliation.store');
        Route::get('/accounting/reconciliation/{id}', 'show')->name('accounting.reconciliation.show');
        Route::post('/accounting/reconciliation/{id}/reconcile', 'reconcile')->name('accounting.reconciliation.reconcile');
    });

    // Reports
    Route::controller(ReportController::class)->group(function () {
        Route::get('/reports/dashboard', 'dashboard')->name('reports.dashboard');
        Route::get('/reports/balance-sheet', 'balanceSheet')->name('reports.balance-sheet');
        Route::get('/reports/profit-loss', 'profitLoss')->name('reports.profit-loss');
        Route::get('/reports/trial-balance', 'trialBalance')->name('reports.trial-balance');
        Route::get('/reports/cash-flow', 'cashFlow')->name('reports.cash-flow');
        Route::get('/reports/sales-summary', 'salesSummary')->name('reports.sales-summary');
        Route::get('/reports/sales-by-customer', 'salesByCustomer')->name('reports.sales-by-customer');
        Route::get('/reports/sales-by-product', 'salesByItem')->name('reports.sales-by-item');
        Route::get('/reports/revenue-trends', 'revenueTrends')->name('reports.revenue-trends');
        Route::get('/reports/stock-on-hand', 'stockOnHand')->name('reports.stock-on-hand');
        Route::get('/reports/inventory-valuation', 'inventoryValuation')->name('reports.inventory-valuation');
        Route::get('/reports/stock-movement', 'stockMovement')->name('reports.stock-movement');
        Route::get('/reports/low-stock', 'lowStockReport')->name('reports.low-stock');
        Route::get('/reports/purchase-summary', 'purchaseSummary')->name('reports.purchase-summary');
        Route::get('/reports/purchases-by-vendor', 'purchasesByVendor')->name('reports.purchases-by-vendor');
        Route::get('/reports/customer-statement', 'customerStatement')->name('reports.customer-statement');
        Route::get('/reports/vendor-statement', 'vendorStatement')->name('reports.vendor-statement');
    });

    // Contacts - Customers
    Route::controller(CustomerController::class)->group(function () {
        Route::get('/contacts/customers/export', 'export')->name('contacts.customers.export');
        Route::get('/contacts/customers/template', 'template')->name('contacts.customers.template');
        Route::post('/contacts/customers/import', 'import')->name('contacts.customers.import');
        Route::get('/contacts/customers', 'index')->name('contacts.customers.index');
        Route::post('/contacts/customers', 'store')->name('contacts.customers.store');
        Route::get('/contacts/customers/{id}', 'show')->name('contacts.customers.show');
        Route::get('/contacts/customers/{id}/details', 'details')->name('contacts.customers.details');
        Route::put('/contacts/customers/{id}', 'update')->name('contacts.customers.update');
        Route::delete('/contacts/customers/{id}', 'destroy')->name('contacts.customers.destroy');
    });

    // Contacts - Vendors
    Route::controller(VendorController::class)->group(function () {
        Route::get('/contacts/vendors/export', 'export')->name('contacts.vendors.export');
        Route::get('/contacts/vendors/template', 'template')->name('contacts.vendors.template');
        Route::post('/contacts/vendors/import', 'import')->name('contacts.vendors.import');
        Route::get('/contacts/vendors', 'index')->name('contacts.vendors.index');
        Route::post('/contacts/vendors', 'store')->name('contacts.vendors.store');
        Route::get('/contacts/vendors/{id}', 'show')->name('contacts.vendors.show');
        Route::get('/contacts/vendors/{id}/details', 'details')->name('contacts.vendors.details');
        Route::put('/contacts/vendors/{id}', 'update')->name('contacts.vendors.update');
        Route::delete('/contacts/vendors/{id}', 'destroy')->name('contacts.vendors.destroy');
    });

    Route::controller(PurchasesController::class)->group(function () {
        Route::get('/purchases/bills', 'bills')->name('purchases.bills.index');
        Route::get('/purchases/bills/create', 'create')->name('purchases.bills.create');
        Route::post('/purchases/bills', 'store')->name('purchases.bills.store');
        Route::get('/purchases/bills/{id}', 'show')->name('purchases.bills.show');
        Route::get('/purchases/bills/{id}/edit', 'edit')->name('purchases.bills.edit');
        Route::put('/purchases/bills/{id}', 'update')->name('purchases.bills.update');
        Route::delete('/purchases/bills/{id}', 'destroy')->name('purchases.bills.destroy');
    });

    Route::controller(PurchaseOrderController::class)->group(function () {
        Route::get('/purchases/orders', 'index')->name('purchases.orders.index');
        Route::get('/purchases/orders/create', 'create')->name('purchases.orders.create');
        Route::post('/purchases/orders', 'store')->name('purchases.orders.store');
        Route::get('/purchases/orders/{id}', 'show')->name('purchases.orders.show');
        Route::get('/purchases/orders/{id}/edit', 'edit')->name('purchases.orders.edit');
        Route::put('/purchases/orders/{id}', 'update')->name('purchases.orders.update');
        Route::delete('/purchases/orders/{id}', 'destroy')->name('purchases.orders.destroy');
    });

    Route::controller(PurchaseReturnController::class)->group(function () {
        Route::get('/purchases/returns', 'index')->name('purchases.returns.index');
        Route::get('/purchases/returns/create', 'create')->name('purchases.returns.create');
        Route::post('/purchases/returns', 'store')->name('purchases.returns.store');
        Route::get('/purchases/returns/{id}', 'show')->name('purchases.returns.show');
        Route::get('/purchases/returns/{id}/edit', 'edit')->name('purchases.returns.edit');
        Route::put('/purchases/returns/{id}', 'update')->name('purchases.returns.update');
        Route::delete('/purchases/returns/{id}', 'destroy')->name('purchases.returns.destroy');
    });

    Route::controller(PurchaseDashboardController::class)->group(function () {
        Route::get('/purchases/dashboard', 'index')->name('purchases.dashboard');
    });

    Route::resource('purchases/receipts', GoodsReceiptController::class)->names('purchases.receipts');
    Route::get('/purchases/receipts/create-from-order/{order_id}', [GoodsReceiptController::class, 'createFromOrder'])->name('purchases.receipts.create_from_order');

    Route::controller(VendorPaymentController::class)->group(function () {
        Route::get('/purchases/payments', 'index')->name('purchases.payments.index');
        Route::get('/purchases/payments/create', 'create')->name('purchases.payments.create');
        Route::post('/purchases/payments', 'store')->name('purchases.payments.store');
        Route::get('/purchases/payments/{id}', 'show')->name('purchases.payments.show');
        Route::delete('/purchases/payments/{id}', 'destroy')->name('purchases.payments.destroy');
    });

    // Sales Module
    Route::controller(SalesOrderController::class)->group(function () {
        Route::get('/sales/orders', 'index')->name('sales.orders.index');
        Route::get('/sales/orders/create', 'create')->name('sales.orders.create');
        Route::post('/sales/orders', 'store')->name('sales.orders.store');
        Route::get('/sales/orders/{id}', 'show')->name('sales.orders.show');
        Route::get('/sales/orders/{id}/edit', 'edit')->name('sales.orders.edit');
        Route::put('/sales/orders/{id}', 'update')->name('sales.orders.update');
        Route::delete('/sales/orders/{id}', 'destroy')->name('sales.orders.destroy');
    });

    Route::controller(DeliveryNoteController::class)->group(function () {
        Route::get('/sales/delivery-notes', 'index')->name('sales.receipts.index');
        Route::get('/sales/delivery-notes/create', 'create')->name('sales.receipts.create');
        Route::post('/sales/delivery-notes', 'store')->name('sales.receipts.store');
        Route::get('/sales/delivery-notes/{id}', 'show')->name('sales.receipts.show');
        Route::get('/sales/delivery-notes/{id}/edit', 'edit')->name('sales.receipts.edit');
        Route::put('/sales/delivery-notes/{id}', 'update')->name('sales.receipts.update');
        Route::delete('/sales/delivery-notes/{id}', 'destroy')->name('sales.receipts.destroy');
    });

    Route::controller(SalesInvoiceController::class)->group(function () {
        Route::get('/sales/invoices', 'index')->name('sales.invoices.index');
        Route::get('/sales/invoices/create', 'create')->name('sales.invoices.create');
        Route::post('/sales/invoices', 'store')->name('sales.invoices.store');
        Route::get('/sales/invoices/{id}', 'show')->name('sales.invoices.show');
        Route::get('/sales/invoices/{id}/edit', 'edit')->name('sales.invoices.edit');
        Route::put('/sales/invoices/{id}', 'update')->name('sales.invoices.update');
        Route::delete('/sales/invoices/{id}', 'destroy')->name('sales.invoices.destroy');
    });

    Route::controller(CustomerPaymentController::class)->group(function () {
        Route::get('/sales/payments', 'index')->name('sales.payments.index');
        Route::get('/sales/payments/create', 'create')->name('sales.payments.create');
        Route::post('/sales/payments', 'store')->name('sales.payments.store');
        Route::get('/sales/payments/{id}', 'show')->name('sales.payments.show');
        Route::delete('/sales/payments/{id}', 'destroy')->name('sales.payments.destroy');
    });

    Route::controller(SalesReturnController::class)->group(function () {
        Route::get('/sales/returns', 'index')->name('sales.returns.index');
        Route::get('/sales/returns/create', 'create')->name('sales.returns.create');
        Route::post('/sales/returns', 'store')->name('sales.returns.store');
        Route::get('/sales/returns/{id}', 'show')->name('sales.returns.show');
        Route::get('/sales/returns/{id}/edit', 'edit')->name('sales.returns.edit');
        Route::put('/sales/returns/{id}', 'update')->name('sales.returns.update');
        Route::delete('/sales/returns/{id}', 'destroy')->name('sales.returns.destroy');
    });

    // Inventory Module
    Route::controller(InventoryController::class)->group(function () {
        Route::get('/inventory/on-hand', 'onHand')->name('inventory.on_hand');
        Route::get('/inventory/movements', 'movements')->name('inventory.movements');
        Route::get('/inventory/low-stock', 'lowStock')->name('inventory.low_stock');
    });

    Route::patch('inventory/adjustments/{id}/finalize', [StockAdjustmentController::class, 'finalize'])->name('inventory.adjustments.finalize');
    Route::resource('inventory/adjustments', StockAdjustmentController::class)->names('inventory.adjustments');
    Route::controller(StockAdjustmentController::class)->group(function () {
        Route::get('/inventory/adjustments/{id}/edit', 'edit')->name('inventory.adjustments.edit');
        Route::put('/inventory/adjustments/{id}', 'update')->name('inventory.adjustments.update');
        Route::delete('/inventory/adjustments/{id}', 'destroy')->name('inventory.adjustments.destroy');
        Route::post('/inventory/adjustments/{id}/finalize', 'finalize')->name('inventory.adjustments.finalize');
    });

    Route::patch('inventory/transfers/{id}/finalize', [StockTransferController::class, 'finalize'])->name('inventory.transfers.finalize');
    Route::resource('inventory/transfers', StockTransferController::class)->names('inventory.transfers');
    Route::controller(StockTransferController::class)->group(function () {
        Route::get('/inventory/transfers/{id}/edit', 'edit')->name('inventory.transfers.edit');
        Route::put('/inventory/transfers/{id}', 'update')->name('inventory.transfers.update');
        Route::delete('/inventory/transfers/{id}', 'destroy')->name('inventory.transfers.destroy');
        Route::post('/inventory/transfers/{id}/finalize', 'finalize')->name('inventory.transfers.finalize');
    });

    // Items - Categories
    Route::controller(CategoryController::class)->group(function () {
        Route::get('/items/categories/export', 'export')->name('items.categories.export');
        Route::get('/items/categories/template', 'template')->name('items.categories.template');
        Route::post('/items/categories/import', 'import')->name('items.categories.import');
        Route::get('/items/categories', 'index')->name('items.categories.index');
        Route::post('/items/categories', 'store')->name('items.categories.store');
        Route::put('/items/categories/{id}', 'update')->name('items.categories.update');
        Route::delete('/items/categories/{id}', 'destroy')->name('items.categories.destroy');
    });

    // Items - Index
    Route::controller(ItemController::class)->group(function () {
        Route::get('/items/export', 'export')->name('items.export');
        Route::get('/items/template', 'template')->name('items.template');
        Route::post('/items/import', 'import')->name('items.import');
        Route::get('/items/search', 'search')->name('items.search');
        Route::get('/items', 'index')->name('items.index');
        Route::post('/items', 'store')->name('items.store');
        Route::get('/items/{id}/details', 'details')->name('items.details');
        Route::put('/items/{id}', 'update')->name('items.update');
        Route::delete('/items/{id}', 'destroy')->name('items.destroy');
    });

    // Items - Brands
    Route::controller(BrandController::class)->group(function () {
        Route::get('/items/brands/export', 'export')->name('items.brands.export');
        Route::get('/items/brands/template', 'template')->name('items.brands.template');
        Route::post('/items/brands/import', 'import')->name('items.brands.import');
        Route::get('/items/brands', 'index')->name('items.brands.index');
        Route::post('/items/brands', 'store')->name('items.brands.store');
        Route::put('/items/brands/{id}', 'update')->name('items.brands.update');
        Route::delete('/items/brands/{id}', 'destroy')->name('items.brands.destroy');
    });

    // Items - Units
    Route::controller(UnitController::class)->group(function () {
        Route::get('/items/units/export', 'export')->name('items.units.export');
        Route::get('/items/units/template', 'template')->name('items.units.template');
        Route::post('/items/units/import', 'import')->name('items.units.import');
        Route::get('/items/units', 'index')->name('items.units.index');
        Route::post('/items/units', 'store')->name('items.units.store');
        Route::put('/items/units/{id}', 'update')->name('items.units.update');
        Route::delete('/items/units/{id}', 'destroy')->name('items.units.destroy');
    });

    // Global Context
    Route::post('/session/context', [ContextController::class, 'update'])->name('session.context.update');
});

require __DIR__.'/auth.php';
