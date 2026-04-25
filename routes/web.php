<?php

use App\Http\Controllers\Front\FrontController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Auth\LoginController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\ProductCategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ShowroomController;
use App\Http\Controllers\Admin\UserShowroomController;
use App\Http\Controllers\Admin\CrmUserController;
use App\Http\Controllers\Admin\BusinessReportController;
use App\Http\Controllers\Admin\ProductShapeController;
use App\Http\Controllers\Admin\ProductFeatureController;
use App\Http\Controllers\Admin\AdminPaymentController;
use App\Http\Controllers\Admin\InvoicePdfSettingController;
use App\Http\Controllers\Admin\QuotationCancelReasonController;
use App\Http\Controllers\Admin\PartyReportController;
use App\Http\Controllers\Admin\LeadReportController;

use App\Http\Controllers\StoreManager\LeadController;
use App\Http\Controllers\StoreManager\LeadDesignController;
use App\Http\Controllers\StoreManager\LeadHistoryController;
use App\Http\Controllers\StoreManager\LeadPaymentController;
use App\Http\Controllers\StoreManager\InvoiceController;
use App\Http\Controllers\ComplainMasterController;
use App\Http\Controllers\AccountUser\AccountPaymentController;

use App\Http\Controllers\StoreManager\LedgerController;


Route::fallback(function () {
    return view('errors.404');
});

Route::get('/login', function () {
    return redirect()->route('login');
});

Auth::routes(['register' => false]);

Route::get('/home', [HomeController::class, 'index'])->name('home');

// Profile Routes
Route::prefix('profile')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'getProfile'])->name('detail');
    Route::get('/edit', [HomeController::class, 'EditProfile'])->name('EditProfile');
    Route::post('/update', [HomeController::class, 'updateProfile'])->name('update');
    Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change-password');
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Roles & Permissions
Route::resource('roles', App\Http\Controllers\RolesController::class);
Route::resource('permissions', App\Http\Controllers\PermissionsController::class);

// Users (legacy)
Route::middleware('auth')->prefix('users')->name('users.')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('index');
    Route::get('/create', [UserController::class, 'create'])->name('create');
    Route::post('/store', [UserController::class, 'store'])->name('store');
    Route::get('/edit/{id?}', [UserController::class, 'edit'])->name('edit');
    Route::post('/update/{user}', [UserController::class, 'update'])->name('update');
    Route::delete('/delete/{user}', [UserController::class, 'delete'])->name('destroy');
    Route::get('/update/status/{user_id}/{status}', [UserController::class, 'updateStatus'])->name('status');
    Route::post('/password-update/{Id?}', [UserController::class, 'passwordupdate'])->name('passwordupdate');
    Route::get('/import-users', [UserController::class, 'importUsers'])->name('import');
    Route::post('/upload-users', [UserController::class, 'uploadUsers'])->name('upload');
    Route::get('export/', [UserController::class, 'export'])->name('export');
});

// Admin: Customers
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::post('customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::post('customer/update/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('customer/delete/{id}', [CustomerController::class, 'destroy'])->name('customer.delete');
    Route::post('customer/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customer.bulkDelete');
});

// Admin: Product Categories
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('product-category', [ProductCategoryController::class, 'index'])->name('product-category.index');
    Route::post('product-category/store', [ProductCategoryController::class, 'store'])->name('product-category.store');
    Route::post('product-category/update/{id}', [ProductCategoryController::class, 'update'])->name('product-category.update');
    Route::delete('product-category/delete/{id}', [ProductCategoryController::class, 'destroy'])->name('product-category.delete');
    Route::post('product-category/bulk-delete', [ProductCategoryController::class, 'bulkDelete'])->name('product-category.bulkDelete');
});

// Admin: Product Shapes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('product-shape', [ProductShapeController::class, 'index'])->name('product-shape.index');
    Route::post('product-shape/store', [ProductShapeController::class, 'store'])->name('product-shape.store');
    Route::post('product-shape/update/{id}', [ProductShapeController::class, 'update'])->name('product-shape.update');
    Route::delete('product-shape/delete/{id}', [ProductShapeController::class, 'destroy'])->name('product-shape.delete');
    Route::post('product-shape/bulk-delete', [ProductShapeController::class, 'bulkDelete'])->name('product-shape.bulkDelete');
});

// Admin: Product Features
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('product-feature', [ProductFeatureController::class, 'index'])->name('product-feature.index');
    Route::post('product-feature/store', [ProductFeatureController::class, 'store'])->name('product-feature.store');
    Route::post('product-feature/update/{id}', [ProductFeatureController::class, 'update'])->name('product-feature.update');
    Route::delete('product-feature/delete/{id}', [ProductFeatureController::class, 'destroy'])->name('product-feature.delete');
    Route::post('product-feature/bulk-delete', [ProductFeatureController::class, 'bulkDelete'])->name('product-feature.bulkDelete');
});

// Admin: Quotation Cancel Reasons
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('quotation-cancel-reason', [QuotationCancelReasonController::class, 'index'])->name('quotation-cancel-reason.index');
    Route::post('quotation-cancel-reason/store', [QuotationCancelReasonController::class, 'store'])->name('quotation-cancel-reason.store');
    Route::post('quotation-cancel-reason/update/{id}', [QuotationCancelReasonController::class, 'update'])->name('quotation-cancel-reason.update');
    Route::delete('quotation-cancel-reason/delete/{id}', [QuotationCancelReasonController::class, 'destroy'])->name('quotation-cancel-reason.delete');
    Route::post('quotation-cancel-reason/bulk-delete', [QuotationCancelReasonController::class, 'bulkDelete'])->name('quotation-cancel-reason.bulkDelete');
});

// Admin: Products
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('product', [ProductController::class, 'index'])->name('product.index');
    Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
    Route::post('product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('product/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');
    Route::post('product/bulk-delete', [ProductController::class, 'bulkDelete'])->name('product.bulkDelete');
});

// Admin: Showrooms
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('showroom', [ShowroomController::class, 'index'])->name('showroom.index');
    Route::post('showroom/store', [ShowroomController::class, 'store'])->name('showroom.store');
    Route::post('showroom/update/{id}', [ShowroomController::class, 'update'])->name('showroom.update');
    Route::delete('showroom/delete/{id}', [ShowroomController::class, 'destroy'])->name('showroom.delete');
    Route::post('showroom/bulk-delete', [ShowroomController::class, 'bulkDelete'])->name('showroom.bulkDelete');
});

// Admin: User-Showroom mapping
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('user-showroom', [UserShowroomController::class, 'index'])->name('user-showroom.index');
    Route::post('user-showroom/store', [UserShowroomController::class, 'store'])->name('user-showroom.store');
    Route::post('user-showroom/update/{id}', [UserShowroomController::class, 'update'])->name('user-showroom.update');
    Route::delete('user-showroom/delete/{id}', [UserShowroomController::class, 'destroy'])->name('user-showroom.delete');
    Route::post('user-showroom/bulk-delete', [UserShowroomController::class, 'bulkDelete'])->name('user-showroom.bulkDelete');
});

// Store Manager: Leads
Route::middleware('auth')->prefix('store-manager')->name('store.')->group(function () {

    // Lead CRUD
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('leads/store', [LeadController::class, 'store'])->name('leads.store');
    Route::get('leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit');
    Route::post('leads/{lead}/update', [LeadController::class, 'update'])->name('leads.update');
    Route::post('leads/check-customer', [LeadController::class, 'checkCustomer'])->name('leads.check-customer');

    // Quotation
    Route::get('leads/{lead}/quotation', [LeadController::class, 'quotationForm'])->name('leads.quotation');
    Route::post('leads/{lead}/save-quotation', [LeadController::class, 'saveQuotation'])->name('leads.save-quotation');
    Route::get('leads/{lead}/quotation-view', [LeadController::class, 'quotationView'])->name('leads.quotation-view');
    Route::get('leads/{lead}/quotation-pdf', [LeadController::class, 'quotationPdf'])->name('leads.quotation-pdf');
    Route::get('leads/{lead}/invoice-pdf', [LeadController::class, 'quotationPdf'])->name('leads.invoice-pdf');
    
    Route::post('leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');

    // Lead Designs
    Route::get('leads/{lead}/designs', [LeadDesignController::class, 'index'])->name('leads.designs.index');
    Route::get('leads/{lead}/designs/create', [LeadDesignController::class, 'create'])->name('leads.designs.create');
    Route::post('leads/{lead}/designs/store', [LeadDesignController::class, 'store'])->name('leads.designs.store');
    Route::get('leads/{lead}/designs/{design}/edit', [LeadDesignController::class, 'edit'])->name('leads.designs.edit');
    Route::post('leads/{lead}/designs/{design}/update', [LeadDesignController::class, 'update'])->name('leads.designs.update');
    Route::delete('leads/{lead}/designs/{design}/delete', [LeadDesignController::class, 'destroy'])->name('leads.designs.delete');
    Route::post('leads/{lead}/designs/bulk-delete', [LeadDesignController::class, 'bulkDelete'])->name('leads.designs.bulk-delete');

    // Lead Histories
    Route::get('leads/{lead}/histories', [LeadHistoryController::class, 'index'])->name('leads.histories.index');
    Route::post('leads/{lead}/histories/store', [LeadHistoryController::class, 'store'])->name('leads.histories.store');
    // Update & delete are intentionally blocked (403)
    Route::post('leads/{lead}/histories/{history}/update', [LeadHistoryController::class, 'update'])->name('leads.histories.update');
    Route::delete('leads/{lead}/histories/{history}/delete', [LeadHistoryController::class, 'destroy'])->name('leads.histories.delete');
    Route::post('leads/{lead}/histories/bulk-delete', [LeadHistoryController::class, 'bulkDelete'])->name('leads.histories.bulk-delete');
    Route::get('leads/{lead}/delivery-challan', [LeadController::class, 'deliveryChallan'])->name('leads.delivery-challan');



    Route::get('invoice',                    [InvoiceController::class, 'index'])->name('invoice.index');
    Route::get('invoice/create',             [InvoiceController::class, 'create'])->name('invoice.create');
    Route::post('invoice/store',             [InvoiceController::class, 'store'])->name('invoice.store');
    Route::get('invoice/{invoice}/pdf',      [InvoiceController::class, 'pdf'])->name('invoice.pdf');
    Route::post('invoice/{invoice}/update-payment', [InvoiceController::class, 'updatePayment'])->name('invoice.update-payment');
    Route::get('invoice/{invoice}',          [InvoiceController::class, 'show'])->name('invoice.show');
    Route::delete('invoice/{invoice}/delete', [InvoiceController::class, 'destroy'])->name('invoice.destroy');
    Route::get('invoice/products-by-category', [InvoiceController::class, 'productsByCategory'])->name('invoice.products-by-category');
    Route::get('ledger', [LedgerController::class, 'index'])->name('ledger.index');
});

// Store Manager: Payments (separate group for clarity)
Route::middleware('auth')->prefix('store-manager')->name('store.')->group(function () {
    Route::get('leads/{lead}/payments', [LeadPaymentController::class, 'index'])->name('leads.payments.index');
    Route::post('leads/{lead}/payments/store', [LeadPaymentController::class, 'store'])->name('leads.payments.store');
    Route::post('leads/{lead}/payments/{payment}/update', [LeadPaymentController::class, 'update'])->name('leads.payments.update');
    Route::delete('leads/{lead}/payments/{payment}/delete', [LeadPaymentController::class, 'destroy'])->name('leads.payments.delete');
    Route::post('leads/{lead}/payments/bulk-delete', [LeadPaymentController::class, 'bulkDelete'])->name('leads.payments.bulk-delete');
});

// Admin: CRM Users
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('users', [CrmUserController::class, 'index'])->name('users.index');
    Route::get('users/create', [CrmUserController::class, 'create'])->name('users.create');
    Route::post('users/store', [CrmUserController::class, 'store'])->name('users.store');
    Route::get('users/{user}/edit', [CrmUserController::class, 'edit'])->name('users.edit');
    Route::post('users/update/{user}', [CrmUserController::class, 'update'])->name('users.update');
    Route::post('users/password-update/{user}', [CrmUserController::class, 'updatePassword'])->name('users.password.update');
    Route::delete('users/delete/{user}', [CrmUserController::class, 'destroy'])->name('users.destroy');
    Route::get('reports/business', [BusinessReportController::class, 'index'])->name('reports.business');
    Route::get('reports/party', [PartyReportController::class, 'index'])->name('reports.party');
    Route::get('invoice-settings', [InvoicePdfSettingController::class, 'edit'])->name('invoice-settings.edit');
    Route::post('invoice-settings', [InvoicePdfSettingController::class, 'update'])->name('invoice-settings.update');

    Route::get('reports/leads', [LeadReportController::class, 'index'])->name('reports.leads');
    Route::get('reports/leads/{lead}', [LeadReportController::class, 'show'])->name('reports.leads.show');
    Route::get('reports/leads/{lead}/histories', [LeadReportController::class, 'histories'])->name('reports.leads.histories');
    Route::get('reports/leads/{lead}/quotations', [LeadReportController::class, 'quotations'])->name('reports.leads.quotations');
    Route::get('reports/leads/{lead}/payments', [LeadReportController::class, 'payments'])->name('reports.leads.payments');

});

Route::middleware('auth')->prefix('complaints')->name('complaints.')->group(function () {
    Route::get('/', [ComplainMasterController::class, 'index'])->name('index');
    Route::post('/store', [ComplainMasterController::class, 'store'])->name('store');
    Route::post('/{complaint}/resolve', [ComplainMasterController::class, 'resolve'])
        ->middleware('crm.role:fitting')
        ->name('resolve');
});

# Account route
Route::middleware('auth')->prefix('Accountuser')->name('Accountuser.')->group(function () {
    Route::get('Accountpayments', [AccountPaymentController::class, 'index'])->name('Accountpayments');
    Route::get('Create-payments', [AccountPaymentController::class, 'Create'])->name('Create');
    Route::get('available-amount/{userId}', [AccountPaymentController::class, 'getUserAvailableAmount'])->name('availableAmount');
    Route::post('Store-payments', [AccountPaymentController::class, 'Store'])->name('Store');
    Route::get('payment-delete/{id?}/{emp_id?}', [AccountPaymentController::class, 'delete'])
        ->name('deletePayment');
});

Route::prefix('admin')->name('Paymentcollection.')->middleware('auth')->group(function () {
    Route::get('Payment-index', [AdminPaymentController::class, 'index'])->name('index');
    Route::get('Create-payments', [AdminPaymentController::class, 'Create'])->name('Create');
    Route::post('Store-payments', [AdminPaymentController::class, 'Store'])->name('Store');
    Route::get('payment-delete/{id?}/{emp_id?}', [AdminPaymentController::class, 'delete'])
        ->name('deletePayment');
});
