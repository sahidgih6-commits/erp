<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SuperAdmin\SuperAdminController;
use App\Http\Controllers\SuperAdmin\OwnerController as SuperAdminOwnerController;
use App\Http\Controllers\Owner\OwnerController;
use App\Http\Controllers\Owner\ManagerController as OwnerManagerController;
use App\Http\Controllers\Owner\UserController as OwnerUserController;
use App\Http\Controllers\Owner\BarcodeController as OwnerBarcodeController;
use App\Http\Controllers\Owner\ExpenseController;
use App\Http\Controllers\Manager\ManagerController;
use App\Http\Controllers\Manager\SalesmanController as ManagerSalesmanController;
use App\Http\Controllers\Manager\ProductController as ManagerProductController;
use App\Http\Controllers\Manager\StockController as ManagerStockController;
use App\Http\Controllers\Manager\DuePaymentController;
use App\Http\Controllers\Salesman\SalesmanController;
use App\Http\Controllers\Salesman\SaleController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\VoucherController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $user = auth()->user();

    if ($user) {
        return redirect()->route($user->getDashboardRoute());
    }

    return redirect()->route('login');
});

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store'])
        ->middleware('throttle:5,1'); // 5 attempts per minute
});

Route::middleware('auth')->group(function () {
    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');
    
    // Universal Voucher Routes (accessible by all authenticated users)
    Route::get('/voucher/{sale}/print', [VoucherController::class, 'print'])->name('voucher.print');
    Route::get('/payment-voucher/{profitRealization}', [VoucherController::class, 'paymentVoucher'])->name('payment-voucher.print');
});

// Super Admin Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin')->name('superadmin.')->group(function () {
    Route::get('/dashboard', [SuperAdminController::class, 'dashboard'])->name('dashboard');
    Route::resource('owners', SuperAdminOwnerController::class);
    Route::post('/owners/{owner}/toggle-due-system', [SuperAdminOwnerController::class, 'toggleDueSystem'])->name('owners.toggle-due-system');
    Route::get('/reports', [ReportController::class, 'superAdminReports'])->name('reports');
    
    // Business Management
    Route::resource('businesses', \App\Http\Controllers\SuperAdmin\BusinessController::class);
    Route::get('/businesses/{business}/edit-template', [\App\Http\Controllers\SuperAdmin\BusinessController::class, 'editTemplate'])->name('businesses.edit-template');
    Route::put('/businesses/{business}/update-template', [\App\Http\Controllers\SuperAdmin\BusinessController::class, 'updateTemplate'])->name('businesses.update-template');
    Route::get('/businesses/{business}/add-owner', [\App\Http\Controllers\SuperAdmin\BusinessController::class, 'addOwner'])->name('businesses.add-owner');
    Route::post('/businesses/{business}/store-owner', [\App\Http\Controllers\SuperAdmin\BusinessController::class, 'storeOwner'])->name('businesses.store-owner');
    
    // Old voucher templates route (will be deprecated)
    Route::resource('voucher-templates', \App\Http\Controllers\SuperAdmin\VoucherTemplateController::class);
});

// Owner Routes
Route::middleware(['auth', 'role:owner'])->prefix('owner')->name('owner.')->group(function () {
    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');
    Route::get('/due-customers', [OwnerController::class, 'dueCustomers'])->name('due-customers');
    Route::get('/all-sales', [OwnerController::class, 'allSales'])->name('all-sales');
    Route::get('/voucher/{sale}/print', [VoucherController::class, 'print'])->name('voucher.print');
    Route::get('/payment/{sale}/record', [OwnerController::class, 'recordPayment'])->name('payment.record');
    Route::post('/payment/{sale}/store', [OwnerController::class, 'storePayment'])->name('payment.store');
    Route::get('/payment-voucher/{profitRealization}', [OwnerController::class, 'paymentVoucher'])->name('payment.voucher');
    
    // User Management (Managers, Salesmen, Cashiers)
    Route::resource('users', OwnerUserController::class);
    
    // Barcode Printing (POS Feature)
    Route::get('/barcode', [OwnerBarcodeController::class, 'index'])->name('barcode.index');
    Route::post('/barcode/generate', [OwnerBarcodeController::class, 'generate'])->name('barcode.generate');
    Route::get('/barcode/quick-print/{product}', [OwnerBarcodeController::class, 'quickPrint'])->name('barcode.quick-print');
    
    // Categories & Customers (same as manager)
    Route::get('/customers/search', [\App\Http\Controllers\Manager\CustomerController::class, 'search'])->name('customers.search');
    Route::resource('categories', \App\Http\Controllers\Manager\CategoryController::class);
    Route::resource('customers', \App\Http\Controllers\Manager\CustomerController::class);
    
    // Legacy manager routes (redirects to users)
    Route::resource('managers', OwnerManagerController::class);
    
    Route::resource('expenses', ExpenseController::class);
    Route::resource('products', ManagerProductController::class);
    Route::get('/stock', [ManagerStockController::class, 'index'])->name('stock.index');
    Route::post('/stock', [ManagerStockController::class, 'store'])->name('stock.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::delete('/sales/{sale}', [SaleController::class, 'destroy'])->name('sales.destroy');
    Route::get('/reports', [ReportController::class, 'ownerReports'])->name('reports');
});

// Manager Routes
Route::middleware(['auth', 'role:manager'])->prefix('manager')->name('manager.')->group(function () {
    Route::get('/dashboard', [ManagerController::class, 'dashboard'])->name('dashboard');
    Route::get('/due-customers', [ManagerController::class, 'dueCustomers'])->name('due-customers');
    Route::get('/payment/{sale}/record', [ManagerController::class, 'recordPayment'])->name('payment.record');
    Route::post('/payment/{sale}/store', [ManagerController::class, 'storePayment'])->name('payment.store');
    Route::get('/voucher/{sale}/print', [VoucherController::class, 'print'])->name('voucher.print');
    Route::get('/payment-voucher/{profitRealization}', [ManagerController::class, 'paymentVoucher'])->name('payment.voucher');
    Route::resource('salesmen', ManagerSalesmanController::class);
    Route::resource('products', ManagerProductController::class);
    Route::get('/customers/search', [\App\Http\Controllers\Manager\CustomerController::class, 'search'])->name('customers.search');
    Route::resource('categories', \App\Http\Controllers\Manager\CategoryController::class);
    Route::resource('customers', \App\Http\Controllers\Manager\CustomerController::class);
    Route::get('/stock', [ManagerStockController::class, 'index'])->name('stock.index');
    Route::post('/stock', [ManagerStockController::class, 'store'])->name('stock.store');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
    Route::get('/due-payments', [DuePaymentController::class, 'index'])->name('due-payments.index');
    Route::post('/due-payments/{sale}', [DuePaymentController::class, 'update'])->name('due-payments.update');
    
    // Reports
    Route::get('/reports', [\App\Http\Controllers\Manager\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [\App\Http\Controllers\Manager\ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/stock', [\App\Http\Controllers\Manager\ReportController::class, 'stock'])->name('reports.stock');
    Route::get('/reports/profit', [\App\Http\Controllers\Manager\ReportController::class, 'profit'])->name('reports.profit');
    Route::get('/reports/customers', [\App\Http\Controllers\Manager\ReportController::class, 'customers'])->name('reports.customers');
    Route::get('/reports/export', [\App\Http\Controllers\Manager\ReportController::class, 'export'])->name('reports.export');
});

// Salesman Routes
Route::middleware(['auth', 'role:salesman'])->prefix('salesman')->name('salesman.')->group(function () {
    Route::get('/dashboard', [SalesmanController::class, 'dashboard'])->name('dashboard');
    Route::get('/sales', [SaleController::class, 'index'])->name('sales.index');
    Route::get('/sales/create', [SaleController::class, 'create'])->name('sales.create');
    Route::post('/sales', [SaleController::class, 'store'])->name('sales.store');
});

// Language/Locale Route
Route::get('/locale/{lang}', function ($lang) {
    if (in_array($lang, ['en', 'bn'])) {
        session(['locale' => $lang]);
        app()->setLocale($lang);
    }
    return redirect()->back();
})->name('locale');

// POS Routes
Route::middleware(['auth', 'role:owner|manager|salesman|cashier'])->prefix('pos')->name('pos.')->group(function () {
    // Original POS routes
    Route::get('/dashboard', [\App\Http\Controllers\POS\POSDashboardController::class, 'index'])->name('dashboard');
    Route::get('/billing', [\App\Http\Controllers\POS\POSDashboardController::class, 'billing'])->name('billing');
    Route::post('/transaction', [\App\Http\Controllers\POS\POSDashboardController::class, 'createTransaction'])->name('transaction.store');
    Route::post('/print-receipt/{transaction}', [\App\Http\Controllers\POS\POSDashboardController::class, 'printReceipt'])->name('receipt.print');
    Route::post('/open-drawer', [\App\Http\Controllers\POS\POSDashboardController::class, 'openDrawer'])->name('drawer.open');
    Route::get('/search-product', [\App\Http\Controllers\POS\POSDashboardController::class, 'searchProduct'])->name('product.search');
    Route::get('/summary', [\App\Http\Controllers\POS\POSDashboardController::class, 'getSummary'])->name('summary');
    Route::get('/history', [\App\Http\Controllers\POS\POSDashboardController::class, 'history'])->name('history');
    
    // Enhanced POS routes
    Route::get('/enhanced-billing', [\App\Http\Controllers\POS\EnhancedPOSController::class, 'billing'])->name('enhanced-billing');
    Route::get('/search-products', [\App\Http\Controllers\POS\EnhancedPOSController::class, 'searchProducts'])->name('products.search');
    Route::post('/enhanced-billing', [\App\Http\Controllers\POS\EnhancedPOSController::class, 'checkout'])->name('enhanced-billing.store');
    Route::post('/checkout', [\App\Http\Controllers\POS\EnhancedPOSController::class, 'checkout'])->name('checkout');
    Route::post('/hold', [\App\Http\Controllers\POS\EnhancedPOSController::class, 'hold'])->name('hold');
    Route::get('/recall/{id}', [\App\Http\Controllers\POS\EnhancedPOSController::class, 'recall'])->name('recall');
    Route::delete('/hold/{id}', [\App\Http\Controllers\POS\EnhancedPOSController::class, 'cancelHold'])->name('hold.cancel');
    
    // Cash Drawer Management
    Route::get('/cash-drawer', [\App\Http\Controllers\POS\CashDrawerController::class, 'index'])->name('cash-drawer.index');
    Route::get('/cash-drawer/create', [\App\Http\Controllers\POS\CashDrawerController::class, 'create'])->name('cash-drawer.create');
    Route::post('/cash-drawer', [\App\Http\Controllers\POS\CashDrawerController::class, 'store'])->name('cash-drawer.store');
    Route::get('/cash-drawer/{id}', [\App\Http\Controllers\POS\CashDrawerController::class, 'show'])->name('cash-drawer.show');
    Route::get('/cash-drawer/{id}/close', [\App\Http\Controllers\POS\CashDrawerController::class, 'close'])->name('cash-drawer.close');
    Route::put('/cash-drawer/{id}', [\App\Http\Controllers\POS\CashDrawerController::class, 'update'])->name('cash-drawer.update');
});

// Super Admin Hardware Management Routes
Route::middleware(['auth', 'role:superadmin'])->prefix('superadmin/hardware')->name('superadmin.hardware.')->group(function () {
    Route::get('/', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'index'])->name('index');
    Route::get('/business/{business}', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'show'])->name('show');
    Route::get('/business/{business}/configure-version', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'configureVersion'])->name('configure-version');
    Route::post('/business/{business}/update-version', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'updateVersion'])->name('update-version');
    Route::get('/business/{business}/device/create', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'createDevice'])->name('create-device');
    Route::post('/business/{business}/device', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'storeDevice'])->name('store-device');
    Route::get('/business/{business}/device/{device}/edit', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'editDevice'])->name('edit-device');
    Route::put('/business/{business}/device/{device}', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'updateDevice'])->name('update-device');
    Route::get('/business/{business}/device/{device}/toggle', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'toggleDevice'])->name('toggle-device');
    Route::delete('/business/{business}/device/{device}', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'deleteDevice'])->name('delete-device');
    Route::get('/business/{business}/audit-logs', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'auditLogs'])->name('audit-logs');
    Route::post('/business/{business}/device/{device}/test', [\App\Http\Controllers\SuperAdmin\HardwareManagementController::class, 'testDevice'])->name('test-device');
});
