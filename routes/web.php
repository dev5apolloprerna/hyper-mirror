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


use App\Http\Controllers\StoreManager\LeadController;
use App\Http\Controllers\StoreManager\LeadDesignController;
use App\Http\Controllers\StoreManager\LeadHistoryController;
use App\Http\Controllers\StoreManager\LeadPaymentController;


Route::fallback(function () {
     return view('errors.404');
});

Route::get('/login', function () {
    return redirect()->route('login');
});


Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Profile Routes
Route::prefix('profile')->name('profile.')->middleware('auth')->group(function () {
    Route::get('/', [HomeController::class, 'getProfile'])->name('detail');
    Route::get('/edit', [HomeController::class, 'EditProfile'])->name('EditProfile');
    Route::post('/update', [HomeController::class, 'updateProfile'])->name('update');
    Route::post('/change-password', [HomeController::class, 'changePassword'])->name('change-password');
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

// Roles
Route::resource('roles', App\Http\Controllers\RolesController::class);

// Permissions
Route::resource('permissions', App\Http\Controllers\PermissionsController::class);

// Users
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


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('customer', [CustomerController::class, 'index'])->name('customer.index');
    Route::get('customer/create', [CustomerController::class, 'create'])->name('customer.create');
    Route::post('customer/store', [CustomerController::class, 'store'])->name('customer.store');
    Route::get('customer/edit/{id}', [CustomerController::class, 'edit'])->name('customer.edit');
    Route::post('customer/update/{id}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('customer/delete/{id}', [CustomerController::class, 'destroy'])->name('customer.delete');
    Route::post('customer/bulk-delete', [CustomerController::class, 'bulkDelete'])->name('customer.bulkDelete');
});



Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('product-category', [ProductCategoryController::class, 'index'])->name('product-category.index');
    Route::get('product-category/create', [ProductCategoryController::class, 'create'])->name('product-category.create');
    Route::post('product-category/store', [ProductCategoryController::class, 'store'])->name('product-category.store');
    Route::get('product-category/edit/{id}', [ProductCategoryController::class, 'edit'])->name('product-category.edit');
    Route::post('product-category/update/{id}', [ProductCategoryController::class, 'update'])->name('product-category.update');
    Route::delete('product-category/delete/{id}', [ProductCategoryController::class, 'destroy'])->name('product-category.delete');
    Route::post('product-category/bulk-delete', [ProductCategoryController::class, 'bulkDelete'])->name('product-category.bulkDelete');
});


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('product', [ProductController::class, 'index'])->name('product.index');
    Route::get('product/create', [ProductController::class, 'create'])->name('product.create');
    Route::post('product/store', [ProductController::class, 'store'])->name('product.store');
    Route::get('product/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
    Route::post('product/update/{id}', [ProductController::class, 'update'])->name('product.update');
    Route::delete('product/delete/{id}', [ProductController::class, 'destroy'])->name('product.delete');
    Route::post('product/bulk-delete', [ProductController::class, 'bulkDelete'])->name('product.bulkDelete');
});


Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('showroom', [ShowroomController::class, 'index'])->name('showroom.index');
    Route::get('showroom/create', [ShowroomController::class, 'create'])->name('showroom.create');
    Route::post('showroom/store', [ShowroomController::class, 'store'])->name('showroom.store');
    Route::get('showroom/edit/{id}', [ShowroomController::class, 'edit'])->name('showroom.edit');
    Route::post('showroom/update/{id}', [ShowroomController::class, 'update'])->name('showroom.update');
    Route::delete('showroom/delete/{id}', [ShowroomController::class, 'destroy'])->name('showroom.delete');
    Route::post('showroom/bulk-delete', [ShowroomController::class, 'bulkDelete'])->name('showroom.bulkDelete');
});

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('user-showroom', [UserShowroomController::class, 'index'])->name('user-showroom.index');
    Route::get('user-showroom/create', [UserShowroomController::class, 'create'])->name('user-showroom.create');
    Route::post('user-showroom/store', [UserShowroomController::class, 'store'])->name('user-showroom.store');
    Route::get('user-showroom/edit/{id}', [UserShowroomController::class, 'edit'])->name('user-showroom.edit');
    Route::post('user-showroom/update/{id}', [UserShowroomController::class, 'update'])->name('user-showroom.update');
    Route::delete('user-showroom/delete/{id}', [UserShowroomController::class, 'destroy'])->name('user-showroom.delete');
    Route::post('user-showroom/bulk-delete', [UserShowroomController::class, 'bulkDelete'])->name('user-showroom.bulkDelete');
});


Route::prefix('store-manager')->name('store.')->group(function () {
    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::get('leads/create', [LeadController::class, 'create'])->name('leads.create');
    Route::post('leads/store', [LeadController::class, 'store'])->name('leads.store');
    Route::post('leads/check-customer', [LeadController::class, 'checkCustomer'])->name('leads.check-customer');

    Route::get('leads/{lead}/quotation', [LeadController::class, 'quotationForm'])->name('leads.quotation');
    Route::post('leads/{lead}/save-quotation', [LeadController::class, 'saveQuotation'])->name('leads.save-quotation');
    Route::get('leads/{lead}/quotation-view', [LeadController::class, 'quotationView'])->name('leads.quotation-view');
    Route::post('leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status');


    Route::get('leads/{lead}/designs', [LeadDesignController::class, 'index'])->name('leads.designs.index');
    Route::get('leads/{lead}/designs/create', [LeadDesignController::class, 'create'])->name('leads.designs.create');
    Route::post('leads/{lead}/designs/store', [LeadDesignController::class, 'store'])->name('leads.designs.store');
    Route::get('leads/{lead}/designs/{design}/edit', [LeadDesignController::class, 'edit'])->name('leads.designs.edit');
    Route::post('leads/{lead}/designs/{design}/update', [LeadDesignController::class, 'update'])->name('leads.designs.update');
    Route::delete('leads/{lead}/designs/{design}/delete', [LeadDesignController::class, 'destroy'])->name('leads.designs.delete');
    Route::post('leads/{lead}/designs/bulk-delete', [LeadDesignController::class, 'bulkDelete'])->name('leads.designs.bulk-delete');


   Route::get('leads/{lead}/histories', [LeadHistoryController::class, 'index'])->name('leads.histories.index');
    Route::post('leads/{lead}/histories/store', [LeadHistoryController::class, 'store'])->name('leads.histories.store');
    Route::post('leads/{lead}/histories/{history}/update', [LeadHistoryController::class, 'update'])->name('leads.histories.update');
    Route::delete('leads/{lead}/histories/{history}/delete', [LeadHistoryController::class, 'destroy'])->name('leads.histories.delete');
    Route::post('leads/{lead}/histories/bulk-delete', [LeadHistoryController::class, 'bulkDelete'])->name('leads.histories.bulk-delete');

});


Route::prefix('store-manager')->name('store.')->group(function () {
    Route::get('leads/{lead}/payments', [LeadPaymentController::class, 'index'])->name('leads.payments.index');
    Route::post('leads/{lead}/payments/store', [LeadPaymentController::class, 'store'])->name('leads.payments.store');
    Route::post('leads/{lead}/payments/{payment}/update', [LeadPaymentController::class, 'update'])->name('leads.payments.update');
    Route::delete('leads/{lead}/payments/{payment}/delete', [LeadPaymentController::class, 'destroy'])->name('leads.payments.delete');
    Route::post('leads/{lead}/payments/bulk-delete', [LeadPaymentController::class, 'bulkDelete'])->name('leads.payments.bulk-delete');
});

