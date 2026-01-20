<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\DeletedUserController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\PositionController;
use App\Http\Controllers\Admin\UniformController;
use App\Http\Controllers\Admin\UniformItemNameController;
use App\Http\Controllers\Admin\UniformCategoryController;
use App\Http\Controllers\Admin\UniformColorController;
use App\Http\Controllers\Admin\UniformUomController;
use App\Http\Controllers\Admin\UniformSizeController;
use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\AssetLocationController;
use App\Http\Controllers\Admin\AssetUomController;
use App\Http\Controllers\Admin\AssetVendorController;
use App\Http\Controllers\Admin\CareerController as AdminCareerController;
use App\Http\Controllers\Admin\CareerCandidateController as AdminCareerCandidateController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\EnsureGuest;


// Language Switcher
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'kr'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
});

// Main Pages
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about/company', [AboutController::class, 'index'])->name('aboutus');
Route::get('/about/ceo', [AboutController::class, 'ceo'])->name('ceo');
Route::get('/about/philosophy', [AboutController::class, 'philosophy'])->name('philosophy');
Route::get('/cs', [CsController::class, 'index'])->name('cs');
Route::get('/career', [CareerController::class, 'index'])->name('career');
Route::get('/career/apply/{job?}', [CareerController::class, 'applyForm'])->name('career.apply');
Route::post('/career/apply', [CareerController::class, 'submitApplication'])
    ->middleware('throttle:5,1')
    ->name('career.apply.submit');
Route::get('/contact', [ContactController::class, 'index'])->name('contact');
Route::get('/products', [ProductsController::class, 'index'])->name('products');
Route::get('/products/colorants', [ProductsController::class, 'colorants'])->name('products.colorants');
Route::get('/products/surface-coating-agents', [ProductsController::class, 'surfaceCoatingAgents'])->name('products.surface-coating-agents');
Route::get('/products/additive-coating', [ProductsController::class, 'additiveCoating'])->name('products.additive-coating');
Route::get('/products/pu-resin', [ProductsController::class, 'puResin'])->name('products.pu-resin');
Route::get('/technology', [TechnologyController::class, 'index'])->name('technology');
Route::get('/technology/certification-status', [TechnologyController::class, 'certificationStatus'])->name('technology.certification-status');
Route::get('/certificates/{certificate}/proof', [TechnologyController::class, 'certificateProof'])->name('certificates.proof');

Route::get('/privacy-policy', function () {
    return view('pages.privacy-policy');
})->name('privacy-policy');

// Auth pages are guest-only (prevent accessing via URL when already logged in)
Route::middleware([EnsureGuest::class])->group(function () {
    Route::get('/signin', [AuthController::class, 'signin'])->name('auth');
    Route::post('/signin', [AuthController::class, 'login'])->name('login');

    Route::get('/signup', [AuthController::class, 'register'])->name('register');
    Route::post('/signup', [AuthController::class, 'store'])->name('register.store');
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware([EnsureAuthenticated::class])->name('logout');

// Dashboard karyawan (role: Users)
Route::get('/dashboard', [UserDashboardController::class, 'index'])
    ->middleware([
        EnsureAuthenticated::class,
        'menu:user_dashboard',
    ])
    ->name('user.dashboard');

// Admin dashboard (hanya untuk Super Admin & Admin)
Route::get('/admin', [AdminController::class, 'admin'])
    ->middleware([
        EnsureAuthenticated::class,
        'role:Super Admin,Admin',
    ])
    ->name('admin');

Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
    'role:Super Admin,Admin',
])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('menu:admin_dashboard')->name('admin.dashboard');
    Route::get('/dashboard/assets', [AdminController::class, 'dashboardAssets'])->middleware('menu:admin_dashboard')->name('admin.dashboard.assets');
    Route::get('/dashboard/uniforms', [AdminController::class, 'dashboardUniforms'])->middleware('menu:admin_dashboard')->name('admin.dashboard.uniforms');

    // Employee Master
    Route::get('/employees', [EmployeeController::class, 'index'])->middleware('menu:employees')->name('admin.employees.index');
    Route::get('/employees/deleted', [EmployeeController::class, 'deleted'])->middleware('menu:employees')->name('admin.employees.deleted');
    Route::post('/employees/{id}/restore', [EmployeeController::class, 'restore'])->middleware('menu:employees')->name('admin.employees.restore');
    Route::get('/employees/audit', [EmployeeController::class, 'audit'])->middleware('menu:employees')->name('admin.employees.audit');
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('menu:employees')->name('admin.employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->middleware('menu:employees')->name('admin.employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->middleware('menu:employees')->name('admin.employees.destroy');

    // Master Department
    Route::get('/departments', [DepartmentController::class, 'index'])->middleware('menu:master_data')->name('admin.departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->middleware('menu:master_data')->name('admin.departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->middleware('menu:master_data')->name('admin.departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->middleware('menu:master_data')->name('admin.departments.destroy');

    // Master Position
    Route::get('/positions', [PositionController::class, 'index'])->middleware('menu:master_data')->name('admin.positions.index');
    Route::post('/positions', [PositionController::class, 'store'])->middleware('menu:master_data')->name('admin.positions.store');
    Route::put('/positions/{position}', [PositionController::class, 'update'])->middleware('menu:master_data')->name('admin.positions.update');
    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->middleware('menu:master_data')->name('admin.positions.destroy');

    // Master Data: Uniform Sizes
    Route::get('/uniform-sizes', [UniformSizeController::class, 'index'])->middleware('menu:master_data')->name('admin.uniform_sizes.index');
    Route::post('/uniform-sizes', [UniformSizeController::class, 'store'])->middleware('menu:master_data')->name('admin.uniform_sizes.store');
    Route::put('/uniform-sizes/{size}', [UniformSizeController::class, 'update'])->middleware('menu:master_data')->name('admin.uniform_sizes.update');
    Route::post('/uniform-sizes/{size}/toggle', [UniformSizeController::class, 'toggle'])->middleware('menu:master_data')->name('admin.uniform_sizes.toggle');

    // Master Data: Uniform Item Names
    Route::get('/uniform-item-names', [UniformItemNameController::class, 'index'])->middleware('menu:master_data')->name('admin.uniform_item_names.index');
    Route::post('/uniform-item-names', [UniformItemNameController::class, 'store'])->middleware('menu:master_data')->name('admin.uniform_item_names.store');
    Route::put('/uniform-item-names/{itemName}', [UniformItemNameController::class, 'update'])->middleware('menu:master_data')->name('admin.uniform_item_names.update');
    Route::post('/uniform-item-names/{itemName}/toggle', [UniformItemNameController::class, 'toggle'])->middleware('menu:master_data')->name('admin.uniform_item_names.toggle');

    // Master Data: Uniform Categories
    Route::get('/uniform-categories', [UniformCategoryController::class, 'index'])->middleware('menu:master_data')->name('admin.uniform_categories.index');
    Route::post('/uniform-categories', [UniformCategoryController::class, 'store'])->middleware('menu:master_data')->name('admin.uniform_categories.store');
    Route::put('/uniform-categories/{category}', [UniformCategoryController::class, 'update'])->middleware('menu:master_data')->name('admin.uniform_categories.update');
    Route::post('/uniform-categories/{category}/toggle', [UniformCategoryController::class, 'toggle'])->middleware('menu:master_data')->name('admin.uniform_categories.toggle');

    // Master Data: Uniform Colors
    Route::get('/uniform-colors', [UniformColorController::class, 'index'])->middleware('menu:master_data')->name('admin.uniform_colors.index');
    Route::post('/uniform-colors', [UniformColorController::class, 'store'])->middleware('menu:master_data')->name('admin.uniform_colors.store');
    Route::put('/uniform-colors/{color}', [UniformColorController::class, 'update'])->middleware('menu:master_data')->name('admin.uniform_colors.update');
    Route::post('/uniform-colors/{color}/toggle', [UniformColorController::class, 'toggle'])->middleware('menu:master_data')->name('admin.uniform_colors.toggle');

    // Master Data: Uniform UOM
    Route::get('/uniform-uoms', [UniformUomController::class, 'index'])->middleware('menu:master_data')->name('admin.uniform_uoms.index');
    Route::post('/uniform-uoms', [UniformUomController::class, 'store'])->middleware('menu:master_data')->name('admin.uniform_uoms.store');
    Route::put('/uniform-uoms/{uom}', [UniformUomController::class, 'update'])->middleware('menu:master_data')->name('admin.uniform_uoms.update');
    Route::post('/uniform-uoms/{uom}/toggle', [UniformUomController::class, 'toggle'])->middleware('menu:master_data')->name('admin.uniform_uoms.toggle');

    // Career Management
    Route::get('/careers', [AdminCareerController::class, 'index'])->middleware('menu:settings')->name('admin.careers.index');
    Route::put('/careers/company', [AdminCareerController::class, 'updateCompany'])->middleware('menu:settings')->name('admin.careers.company.update');
    Route::post('/careers', [AdminCareerController::class, 'store'])->middleware('menu:settings')->name('admin.careers.store');
    Route::put('/careers/{id}', [AdminCareerController::class, 'update'])->middleware('menu:settings')->name('admin.careers.update');
    Route::delete('/careers/{id}', [AdminCareerController::class, 'destroy'])->middleware('menu:settings')->name('admin.careers.destroy');

    // Career Candidates
    Route::get('/career-candidates', [AdminCareerCandidateController::class, 'index'])->middleware('menu:settings')->name('admin.career_candidates.index');
    Route::get('/career-candidates/{candidate}/cv', [AdminCareerCandidateController::class, 'downloadCv'])->middleware('menu:settings')->name('admin.career_candidates.cv');

    // Certificate Management
    Route::get('/certificates', [AdminCertificateController::class, 'index'])->middleware('menu:settings')->name('admin.certificates.index');
    Route::post('/certificates', [AdminCertificateController::class, 'store'])->middleware('menu:settings')->name('admin.certificates.store');
    Route::put('/certificates/{certificate}', [AdminCertificateController::class, 'update'])->middleware('menu:settings')->name('admin.certificates.update');
    Route::delete('/certificates/{certificate}', [AdminCertificateController::class, 'destroy'])->middleware('menu:settings')->name('admin.certificates.destroy');
});

// Admin pages that can be granted to Users (read-only via GET).
Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
    'role:Super Admin,Admin,Users',
])->group(function () {
    // Assets (read-only)
    Route::get('/assets', [AssetController::class, 'index'])->middleware('menu:assets_data')->name('admin.assets.index');
    Route::get('/assets/jababeka', [AssetController::class, 'jababeka'])->middleware('menu:assets_jababeka')->name('admin.assets.jababeka');
    Route::get('/assets/karawang', [AssetController::class, 'karawang'])->middleware('menu:assets_karawang')->name('admin.assets.karawang');
    Route::get('/assets/transfer', [AssetController::class, 'transfer'])->middleware('menu:assets_transfer')->name('admin.assets.transfer');
    Route::get('/assets/in', [AssetController::class, 'in'])->middleware('menu:assets_in')->name('admin.assets.in');
    Route::get('/assets/transfer/list', [AssetController::class, 'transferList'])->middleware('menu:assets_transfer')->name('admin.assets.transfer.list');
    Route::get('/assets/modal-list', [AssetController::class, 'modalList'])->middleware('menu:assets_data')->name('admin.assets.modalList');
    Route::get('/assets/{id}/json', [AssetController::class, 'json'])->middleware('menu:assets_data')->name('admin.assets.json');
    Route::get('/assets/{id}', [AssetController::class, 'show'])->middleware('menu:assets_data')->name('admin.assets.show');
    Route::get('/assets/barcode/{code}', [AssetController::class, 'barcodeImage'])->middleware('menu:assets_data')->name('admin.assets.barcode');
    Route::get('/assets/{id}/print-barcode', [AssetController::class, 'printBarcode'])->middleware('menu:assets_data')->name('admin.assets.printBarcode');

    // Uniforms (read-only)
    Route::get('/uniforms/master', [UniformController::class, 'master'])->middleware('menu:uniforms_master')->name('admin.uniforms.master');
    Route::get('/uniforms/stock', [UniformController::class, 'stock'])->middleware('menu:uniforms_stock')->name('admin.uniforms.stock');
    Route::get('/uniforms/distribution', [UniformController::class, 'distribution'])->middleware('menu:uniforms_distribution')->name('admin.uniforms.distribution');
    Route::get('/uniforms/history', [UniformController::class, 'history'])->middleware('menu:uniforms_history')->name('admin.uniforms.history');
});

// Manajemen user (khusus Super Admin)
Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
    'role:Super Admin',
])->group(function () {
    // User Management
    Route::get('/users', [UserController::class, 'index'])->middleware('menu:settings')->name('admin.users');
    Route::get('/users/data', [UserController::class, 'data'])->middleware('menu:settings')->name('admin.users.data');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('menu:settings')->name('admin.users.show');
    Route::post('/users', [UserController::class, 'store'])->middleware('menu:settings')->name('admin.users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('menu:settings')->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('menu:settings')->name('admin.users.destroy');
    Route::get('/users/history/delete', [DeletedUserController::class, 'index'])->middleware('menu:settings')->name('admin.users.history.delete');
    Route::post('/users/restore/{id}', [UserController::class, 'restore'])->middleware('menu:settings')->name('admin.users.restore');

    // Asset Management (write actions)
    Route::post('/assets/in/scan', [AssetController::class, 'scanIn'])->middleware('menu:assets_in')->name('admin.assets.in.scan');
    Route::post('/assets/transfer/save', [AssetController::class, 'saveTransfer'])->middleware('menu:assets_transfer')->name('admin.assets.transfer.save');
    Route::post('/assets/transfer/cancel', [AssetController::class, 'cancelTransfer'])->middleware('menu:assets_transfer')->name('admin.assets.transfer.cancel');
    Route::get('/assets/create', [AssetController::class, 'create'])->middleware('menu:assets_data')->name('admin.assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->middleware('menu:assets_data')->name('admin.assets.store');
    Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->middleware('menu:assets_data')->name('admin.assets.edit');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->middleware('menu:assets_data')->name('admin.assets.update');
    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->middleware('menu:assets_data')->name('admin.assets.destroy');
    Route::get('/assets-history/delete', [AssetController::class, 'historyDelete'])->middleware('menu:assets_data')->name('admin.assets.historyDelete');
    Route::post('/assets/{id}/restore', [AssetController::class, 'restore'])->middleware('menu:assets_data')->name('admin.assets.restore');
    Route::post('/assets/print-selected-barcode', [AssetController::class, 'printSelectedBarcode'])->middleware('menu:assets_data')->name('admin.assets.printSelectedBarcode');

    // Master Data: Asset
    Route::get('/asset-categories', [AssetCategoryController::class, 'index'])->middleware('menu:master_data')->name('admin.asset_categories.index');
    Route::post('/asset-categories', [AssetCategoryController::class, 'store'])->middleware('menu:master_data')->name('admin.asset_categories.store');
    Route::put('/asset-categories/{category}', [AssetCategoryController::class, 'update'])->middleware('menu:master_data')->name('admin.asset_categories.update');
    Route::post('/asset-categories/{category}/toggle', [AssetCategoryController::class, 'toggle'])->middleware('menu:master_data')->name('admin.asset_categories.toggle');

    Route::get('/asset-locations', [AssetLocationController::class, 'index'])->middleware('menu:master_data')->name('admin.asset_locations.index');
    Route::post('/asset-locations', [AssetLocationController::class, 'store'])->middleware('menu:master_data')->name('admin.asset_locations.store');
    Route::put('/asset-locations/{location}', [AssetLocationController::class, 'update'])->middleware('menu:master_data')->name('admin.asset_locations.update');
    Route::post('/asset-locations/{location}/toggle', [AssetLocationController::class, 'toggle'])->middleware('menu:master_data')->name('admin.asset_locations.toggle');

    Route::get('/asset-uoms', [AssetUomController::class, 'index'])->middleware('menu:master_data')->name('admin.asset_uoms.index');
    Route::post('/asset-uoms', [AssetUomController::class, 'store'])->middleware('menu:master_data')->name('admin.asset_uoms.store');
    Route::put('/asset-uoms/{uom}', [AssetUomController::class, 'update'])->middleware('menu:master_data')->name('admin.asset_uoms.update');
    Route::post('/asset-uoms/{uom}/toggle', [AssetUomController::class, 'toggle'])->middleware('menu:master_data')->name('admin.asset_uoms.toggle');

    Route::get('/asset-vendors', [AssetVendorController::class, 'index'])->middleware('menu:master_data')->name('admin.asset_vendors.index');
    Route::post('/asset-vendors', [AssetVendorController::class, 'store'])->middleware('menu:master_data')->name('admin.asset_vendors.store');
    Route::put('/asset-vendors/{vendor}', [AssetVendorController::class, 'update'])->middleware('menu:master_data')->name('admin.asset_vendors.update');
    Route::post('/asset-vendors/{vendor}/toggle', [AssetVendorController::class, 'toggle'])->middleware('menu:master_data')->name('admin.asset_vendors.toggle');

    // Uniform Stock Management (write actions)
    Route::post('/uniforms/master', [UniformController::class, 'storeItem'])->middleware('menu:uniforms_master')->name('admin.uniforms.items.store');
    Route::put('/uniforms/master/{id}', [UniformController::class, 'updateItem'])->middleware('menu:uniforms_master')->name('admin.uniforms.items.update');
    Route::post('/uniforms/master/{id}/toggle', [UniformController::class, 'toggleItemActive'])->middleware('menu:uniforms_master')->name('admin.uniforms.items.toggle');

    Route::post('/uniforms/stock/in', [UniformController::class, 'stockIn'])->middleware('menu:uniforms_stock')->name('admin.uniforms.stock.in');

    Route::post('/uniforms/distribution/issue', [UniformController::class, 'issue'])->middleware('menu:uniforms_distribution')->name('admin.uniforms.distribution.issue');
    Route::post('/uniforms/issues/{issue}/return', [UniformController::class, 'returnIssue'])->middleware('menu:uniforms_distribution')->name('admin.uniforms.issues.return');
    Route::post('/uniforms/issues/{issue}/replace', [UniformController::class, 'replaceIssue'])->middleware('menu:uniforms_distribution')->name('admin.uniforms.issues.replace');

    Route::get('/uniforms/adjustments', [UniformController::class, 'adjustments'])->middleware('menu:uniforms_stock')->name('admin.uniforms.adjustments');
    Route::post('/uniforms/adjustments', [UniformController::class, 'storeAdjustment'])->middleware('menu:uniforms_stock')->name('admin.uniforms.adjustments.store');
    Route::post('/uniforms/adjustments/{adjustment}/approve', [UniformController::class, 'approveAdjustment'])->middleware('menu:uniforms_stock')->name('admin.uniforms.adjustments.approve');
    Route::post('/uniforms/adjustments/{adjustment}/reject', [UniformController::class, 'rejectAdjustment'])->middleware('menu:uniforms_stock')->name('admin.uniforms.adjustments.reject');

    Route::get('/uniforms/write-offs', [UniformController::class, 'writeOffs'])->middleware('menu:uniforms_stock')->name('admin.uniforms.writeoffs');
    Route::post('/uniforms/write-offs', [UniformController::class, 'storeWriteOff'])->middleware('menu:uniforms_stock')->name('admin.uniforms.writeoffs.store');
    Route::post('/uniforms/write-offs/{writeoff}/approve', [UniformController::class, 'approveWriteOff'])->middleware('menu:uniforms_stock')->name('admin.uniforms.writeoffs.approve');
    Route::post('/uniforms/write-offs/{writeoff}/reject', [UniformController::class, 'rejectWriteOff'])->middleware('menu:uniforms_stock')->name('admin.uniforms.writeoffs.reject');

    Route::get('/uniforms/lots', [UniformController::class, 'lots'])->middleware('menu:uniforms_stock')->name('admin.uniforms.lots');

    Route::get('/uniforms/reconcile', [UniformController::class, 'reconcile'])->middleware('menu:uniforms_stock')->name('admin.uniforms.reconcile');
    Route::post('/uniforms/reconcile/create-adjustment', [UniformController::class, 'reconcileCreateAdjustment'])->middleware('menu:uniforms_stock')->name('admin.uniforms.reconcile.adjustment');

    Route::get('/uniforms/history', [UniformController::class, 'history'])->middleware('menu:uniforms_history')->name('admin.uniforms.history');
});

// Safe 404 fallback: avoid exposing internal routes/pages when URL is guessed.
Route::fallback(function () {
    return response()->view('pages.auth.auth-404', [], 404);
});
