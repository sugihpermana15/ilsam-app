<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;
use App\Http\Controllers\AboutController;
use App\Http\Controllers\CsController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\Admin\UserController;
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
use App\Http\Controllers\Admin\AssetController;
use App\Http\Controllers\Admin\AssetCategoryController;
use App\Http\Controllers\Admin\AssetLocationController;
use App\Http\Controllers\Admin\PlantSiteController;
use App\Http\Controllers\Admin\AssetUomController;
use App\Http\Controllers\Admin\AssetVendorController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AccountTypeController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\DeviceController;
use App\Http\Controllers\Admin\DailyTaskTypeController;
use App\Http\Controllers\Admin\DailyTaskPriorityController;
use App\Http\Controllers\Admin\DailyTaskStatusController;
use App\Http\Controllers\Admin\CareerController as AdminCareerController;
use App\Http\Controllers\Admin\CareerCandidateController as AdminCareerCandidateController;
use App\Http\Controllers\Admin\CertificateController as AdminCertificateController;
use App\Http\Controllers\Admin\WebsiteProductController;
use App\Http\Controllers\Admin\WebsiteSettingsController;
use App\Http\Controllers\Admin\WebsiteContactPageController;
use App\Http\Controllers\Admin\WebsiteHomeSectionsController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\Admin\Stamp\StampDashboardController;
use App\Http\Controllers\Admin\Stamp\StampMasterController;
use App\Http\Controllers\Admin\Stamp\StampReportController;
use App\Http\Controllers\Admin\Stamp\StampTransactionController;
use App\Http\Middleware\EnsureAuthenticated;
use App\Http\Middleware\EnsureGuest;


// Language Switcher
Route::post('/language', [LanguageController::class, 'update'])->name('language.update');

// Legacy link support (deprecated): GET /lang/{locale}
Route::get('/lang/{locale}', function ($locale) {
    $request = request();
    $request->merge(['locale' => $locale]);

    return app(LanguageController::class)->update($request);
})->name('language.legacy');

// Main Pages
Route::get('/', [HomeController::class, 'index'])->name('home');

// Sitemap (basic). Add/adjust URLs as needed.
Route::get('/sitemap.xml', function () {
    $urls = [
        route('home'),
        route('aboutus'),
        route('ceo'),
        route('philosophy'),
        route('technology'),
        route('technology.certification-status'),
        route('products'),
        route('products.colorants'),
        route('products.surface-coating-agents'),
        route('products.additive-coating'),
        route('products.pu-resin'),
        route('career'),
        route('contact'),
        route('privacy-policy'),
    ];

    $lastmod = now()->toAtomString();

    $xml = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
    $xml .= "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";
    foreach ($urls as $url) {
        $loc = htmlspecialchars($url, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $xml .= "  <url>\n";
        $xml .= "    <loc>{$loc}</loc>\n";
        $xml .= "    <lastmod>{$lastmod}</lastmod>\n";
        $xml .= "    <changefreq>weekly</changefreq>\n";
        $xml .= "    <priority>0.7</priority>\n";
        $xml .= "  </url>\n";
    }
    $xml .= "</urlset>\n";

    return response($xml)
        ->header('Content-Type', 'application/xml; charset=UTF-8');
})->name('sitemap');

// NOTE: Manajemen Materai sekarang berada di dalam /admin (tidak ada lagi folder/route ERP).

// Optimized image variants (resize/compress + cache)
Route::get('/img/{path}', [ImageController::class, 'show'])
    ->where('path', '.*')
    ->name('img');

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
Route::post('/contact/send', [ContactController::class, 'send'])
    ->middleware('throttle:10,1')
    ->name('contact.send');
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
    ])
    ->name('admin');

Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
])->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->middleware('menu:admin_dashboard')->name('admin.dashboard');
    Route::get('/dashboard/assets', [AdminController::class, 'dashboardAssets'])->middleware('menu:admin_dashboard')->name('admin.dashboard.assets');
    Route::get('/dashboard/uniforms', [AdminController::class, 'dashboardUniforms'])->middleware('menu:admin_dashboard')->name('admin.dashboard.uniforms');
    Route::get('/dashboard/stamps', [AdminController::class, 'dashboardStamps'])->middleware('menu:admin_dashboard')->name('admin.dashboard.stamps');

    // Manajemen Materai
    Route::prefix('stamps')->name('admin.stamps.')->group(function () {
        // Entry point for Manajemen Materai now lands on Ledger.
        Route::get('/', [StampDashboardController::class, 'index'])->middleware('menu:stamps_transactions')->name('dashboard');

        Route::get('master', [StampMasterController::class, 'index'])->middleware('menu:stamps_master')->name('master.index');
        Route::get('master/datatable', [StampMasterController::class, 'datatable'])->middleware('menu:stamps_master')->name('master.datatable');
        Route::get('master/{stamp}/json', [StampMasterController::class, 'json'])->middleware('menu:stamps_master')->name('master.json');
        Route::get('master/create', [StampMasterController::class, 'create'])->middleware('menu:stamps_master')->name('master.create');
        Route::post('master', [StampMasterController::class, 'store'])->middleware('menu:stamps_master')->name('master.store');
        Route::get('master/{stamp}/edit', [StampMasterController::class, 'edit'])->middleware('menu:stamps_master')->name('master.edit');
        Route::put('master/{stamp}', [StampMasterController::class, 'update'])->middleware('menu:stamps_master')->name('master.update');
        Route::patch('master/{stamp}/toggle', [StampMasterController::class, 'toggle'])->middleware('menu:stamps_master')->name('master.toggle');

        Route::get('transactions', [StampTransactionController::class, 'index'])->middleware('menu:stamps_transactions')->name('transactions.index');
        Route::get('transactions/datatable', [StampTransactionController::class, 'datatable'])->middleware('menu:stamps_transactions')->name('transactions.datatable');
        Route::post('transactions/in', [StampTransactionController::class, 'storeIn'])->middleware('menu:stamps_transactions')->name('transactions.store_in');
        Route::post('transactions/out', [StampTransactionController::class, 'storeOut'])->middleware('menu:stamps_transactions')->name('transactions.store_out');

        Route::get('report.pdf', [StampReportController::class, 'pdf'])->middleware('menu:stamps_transactions')->name('report.pdf');
    });

    // Everything else in /admin stays restricted to Super Admin/Admin.
    Route::middleware('role:Super Admin,Admin')->group(function () {

    // Device (Computer) Management
    Route::get('/devices', [DeviceController::class, 'index'])->middleware('menu:devices')->name('admin.devices.index');
    Route::get('/devices/create', [DeviceController::class, 'create'])->middleware('menu:devices,create')->name('admin.devices.create');
    Route::post('/devices', [DeviceController::class, 'store'])->middleware('menu:devices,create')->name('admin.devices.store');
    Route::get('/devices/{device}', [DeviceController::class, 'show'])->middleware('menu:devices')->name('admin.devices.show');
    Route::get('/devices/{device}/edit', [DeviceController::class, 'edit'])->middleware('menu:devices,update')->name('admin.devices.edit');
    Route::put('/devices/{device}', [DeviceController::class, 'update'])->middleware('menu:devices,update')->name('admin.devices.update');
    Route::delete('/devices/{device}', [DeviceController::class, 'destroy'])->middleware('menu:devices,delete')->name('admin.devices.destroy');

    // Asset lookup for auto-fill (no page refresh)
    Route::get('/devices/assets/{asset}', [DeviceController::class, 'lookupAsset'])->middleware('menu:devices')->name('admin.devices.assets.lookup');
    Route::get('/devices/assets/code/{asset_code}', [DeviceController::class, 'lookupAssetByCode'])->middleware('menu:devices')->name('admin.devices.assets.lookup_by_code');

    // Maintenance history
    Route::post('/devices/{device}/maintenances', [DeviceController::class, 'storeMaintenance'])->middleware('menu:devices,update')->name('admin.devices.maintenances.store');
    Route::get('/devices/maintenances/{maintenance}/download', [DeviceController::class, 'downloadMaintenanceAttachment'])->middleware('menu:devices')->name('admin.devices.maintenances.download');
    Route::delete('/devices/maintenances/{maintenance}', [DeviceController::class, 'destroyMaintenance'])->middleware('menu:devices,update')->name('admin.devices.maintenances.destroy');

    // Vault (sensitive): requires update permission + re-auth on each reveal
    Route::post('/devices/{device}/vault/reveal', [DeviceController::class, 'revealVault'])
        ->middleware(['menu:devices,update', 'throttle:vault-reveal'])
        ->name('admin.devices.vault.reveal');

    // Employee Master
    Route::get('/employees', [EmployeeController::class, 'index'])->middleware('menu:employees_index')->name('admin.employees.index');
    Route::get('/employees/deleted', [EmployeeController::class, 'deleted'])->middleware('menu:employees_deleted')->name('admin.employees.deleted');
    Route::post('/employees/{id}/restore', [EmployeeController::class, 'restore'])->middleware('menu:employees_deleted,update')->name('admin.employees.restore');
    Route::get('/employees/audit', [EmployeeController::class, 'audit'])->middleware('menu:employees_audit')->name('admin.employees.audit');
    Route::post('/employees', [EmployeeController::class, 'store'])->middleware('menu:employees_index')->name('admin.employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->middleware('menu:employees_index')->name('admin.employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->middleware('menu:employees_index')->name('admin.employees.destroy');

    // Master Department
    Route::get('/departments', [DepartmentController::class, 'index'])->middleware('menu:departments')->name('admin.departments.index');
    Route::post('/departments', [DepartmentController::class, 'store'])->middleware('menu:departments')->name('admin.departments.store');
    Route::put('/departments/{department}', [DepartmentController::class, 'update'])->middleware('menu:departments')->name('admin.departments.update');
    Route::delete('/departments/{department}', [DepartmentController::class, 'destroy'])->middleware('menu:departments')->name('admin.departments.destroy');

    // Master Position
    Route::get('/positions', [PositionController::class, 'index'])->middleware('menu:positions')->name('admin.positions.index');
    Route::post('/positions', [PositionController::class, 'store'])->middleware('menu:positions')->name('admin.positions.store');
    Route::put('/positions/{position}', [PositionController::class, 'update'])->middleware('menu:positions')->name('admin.positions.update');
    Route::delete('/positions/{position}', [PositionController::class, 'destroy'])->middleware('menu:positions')->name('admin.positions.destroy');

    // Master Data: Uniform Sizes
    Route::get('/uniform-sizes', [UniformSizeController::class, 'index'])->middleware('menu:uniform_sizes')->name('admin.uniform_sizes.index');
    Route::post('/uniform-sizes', [UniformSizeController::class, 'store'])->middleware('menu:uniform_sizes')->name('admin.uniform_sizes.store');
    Route::put('/uniform-sizes/{size}', [UniformSizeController::class, 'update'])->middleware('menu:uniform_sizes')->name('admin.uniform_sizes.update');
    Route::post('/uniform-sizes/{size}/toggle', [UniformSizeController::class, 'toggle'])->middleware('menu:uniform_sizes,update')->name('admin.uniform_sizes.toggle');

    // Master Data: Uniform Item Names
    Route::get('/uniform-item-names', [UniformItemNameController::class, 'index'])->middleware('menu:uniform_item_names')->name('admin.uniform_item_names.index');
    Route::post('/uniform-item-names', [UniformItemNameController::class, 'store'])->middleware('menu:uniform_item_names')->name('admin.uniform_item_names.store');
    Route::put('/uniform-item-names/{itemName}', [UniformItemNameController::class, 'update'])->middleware('menu:uniform_item_names')->name('admin.uniform_item_names.update');
    Route::post('/uniform-item-names/{itemName}/toggle', [UniformItemNameController::class, 'toggle'])->middleware('menu:uniform_item_names,update')->name('admin.uniform_item_names.toggle');

    // Master Data: Uniform Categories
    Route::get('/uniform-categories', [UniformCategoryController::class, 'index'])->middleware('menu:uniform_categories')->name('admin.uniform_categories.index');
    Route::post('/uniform-categories', [UniformCategoryController::class, 'store'])->middleware('menu:uniform_categories')->name('admin.uniform_categories.store');
    Route::put('/uniform-categories/{category}', [UniformCategoryController::class, 'update'])->middleware('menu:uniform_categories')->name('admin.uniform_categories.update');
    Route::post('/uniform-categories/{category}/toggle', [UniformCategoryController::class, 'toggle'])->middleware('menu:uniform_categories,update')->name('admin.uniform_categories.toggle');

    // Master Data: Uniform Colors
    Route::get('/uniform-colors', [UniformColorController::class, 'index'])->middleware('menu:uniform_colors')->name('admin.uniform_colors.index');
    Route::post('/uniform-colors', [UniformColorController::class, 'store'])->middleware('menu:uniform_colors')->name('admin.uniform_colors.store');
    Route::put('/uniform-colors/{color}', [UniformColorController::class, 'update'])->middleware('menu:uniform_colors')->name('admin.uniform_colors.update');
    Route::post('/uniform-colors/{color}/toggle', [UniformColorController::class, 'toggle'])->middleware('menu:uniform_colors,update')->name('admin.uniform_colors.toggle');

    // Master Data: Uniform UOM
    Route::get('/uniform-uoms', [UniformUomController::class, 'index'])->middleware('menu:uniform_uoms')->name('admin.uniform_uoms.index');
    Route::post('/uniform-uoms', [UniformUomController::class, 'store'])->middleware('menu:uniform_uoms')->name('admin.uniform_uoms.store');
    Route::put('/uniform-uoms/{uom}', [UniformUomController::class, 'update'])->middleware('menu:uniform_uoms')->name('admin.uniform_uoms.update');
    Route::post('/uniform-uoms/{uom}/toggle', [UniformUomController::class, 'toggle'])->middleware('menu:uniform_uoms,update')->name('admin.uniform_uoms.toggle');

    // Master Data: Daily Task Types
    Route::get('/daily-task-types', [DailyTaskTypeController::class, 'index'])->middleware('menu:daily_task_types')->name('admin.daily_task_types.index');
    Route::post('/daily-task-types', [DailyTaskTypeController::class, 'store'])->middleware('menu:daily_task_types')->name('admin.daily_task_types.store');
    Route::put('/daily-task-types/{type}', [DailyTaskTypeController::class, 'update'])->middleware('menu:daily_task_types')->name('admin.daily_task_types.update');
    Route::post('/daily-task-types/{type}/toggle', [DailyTaskTypeController::class, 'toggle'])->middleware('menu:daily_task_types,update')->name('admin.daily_task_types.toggle');

    // Master Data: Daily Task Priorities
    Route::get('/daily-task-priorities', [DailyTaskPriorityController::class, 'index'])->middleware('menu:daily_task_priorities')->name('admin.daily_task_priorities.index');
    Route::post('/daily-task-priorities', [DailyTaskPriorityController::class, 'store'])->middleware('menu:daily_task_priorities')->name('admin.daily_task_priorities.store');
    Route::put('/daily-task-priorities/{priority}', [DailyTaskPriorityController::class, 'update'])->middleware('menu:daily_task_priorities')->name('admin.daily_task_priorities.update');
    Route::post('/daily-task-priorities/{priority}/toggle', [DailyTaskPriorityController::class, 'toggle'])->middleware('menu:daily_task_priorities,update')->name('admin.daily_task_priorities.toggle');

    // Master Data: Daily Task Statuses
    Route::get('/daily-task-statuses', [DailyTaskStatusController::class, 'index'])->middleware('menu:daily_task_statuses')->name('admin.daily_task_statuses.index');
    Route::put('/daily-task-statuses/{status}', [DailyTaskStatusController::class, 'update'])->middleware('menu:daily_task_statuses')->name('admin.daily_task_statuses.update');
    Route::post('/daily-task-statuses/{status}/toggle', [DailyTaskStatusController::class, 'toggle'])->middleware('menu:daily_task_statuses,update')->name('admin.daily_task_statuses.toggle');

    // Career Management
    Route::get('/careers', [AdminCareerController::class, 'index'])->middleware('menu:career')->name('admin.careers.index');
    Route::put('/careers/company', [AdminCareerController::class, 'updateCompany'])->middleware('menu:career')->name('admin.careers.company.update');

    // Website: Products content
    Route::get('/website/products', [WebsiteProductController::class, 'index'])->middleware('menu:website_products')->name('admin.website_products.index');
    Route::get('/website/products/create', [WebsiteProductController::class, 'create'])->middleware('menu:website_products,create')->name('admin.website_products.create');
    Route::post('/website/products', [WebsiteProductController::class, 'store'])->middleware('menu:website_products,create')->name('admin.website_products.store');
    Route::get('/website/products/{id}/edit', [WebsiteProductController::class, 'edit'])->middleware('menu:website_products,update')->name('admin.website_products.edit');
    Route::put('/website/products/{id}', [WebsiteProductController::class, 'update'])->middleware('menu:website_products,update')->name('admin.website_products.update');
    Route::delete('/website/products/{id}', [WebsiteProductController::class, 'destroy'])->middleware('menu:website_products,delete')->name('admin.website_products.destroy');

    // Website: Global settings (navbar/footer/home SEO + hero slides)
    Route::get('/website/settings', [WebsiteSettingsController::class, 'edit'])->middleware('menu:website_settings')->name('admin.website_settings.edit');
    Route::put('/website/settings', [WebsiteSettingsController::class, 'update'])->middleware('menu:website_settings,update')->name('admin.website_settings.update');

    // Website: Contact page
    Route::get('/website/contact-page', [WebsiteContactPageController::class, 'edit'])->middleware('menu:website_contact_page')->name('admin.website_contact_page.edit');
    Route::put('/website/contact-page', [WebsiteContactPageController::class, 'update'])->middleware('menu:website_contact_page,update')->name('admin.website_contact_page.update');

    // Website: Home sections (text slider, etc)
    Route::get('/website/home-sections', [WebsiteHomeSectionsController::class, 'edit'])->middleware('menu:website_home_sections')->name('admin.website_home_sections.edit');
    Route::put('/website/home-sections', [WebsiteHomeSectionsController::class, 'update'])->middleware('menu:website_home_sections,update')->name('admin.website_home_sections.update');
    Route::post('/careers', [AdminCareerController::class, 'store'])->middleware('menu:career')->name('admin.careers.store');
    Route::put('/careers/{id}', [AdminCareerController::class, 'update'])->middleware('menu:career')->name('admin.careers.update');
    Route::delete('/careers/{id}', [AdminCareerController::class, 'destroy'])->middleware('menu:career')->name('admin.careers.destroy');

    // Career Candidates
    Route::get('/career-candidates', [AdminCareerCandidateController::class, 'index'])->middleware('menu:career')->name('admin.career_candidates.index');
    Route::get('/career-candidates/{candidate}/cv', [AdminCareerCandidateController::class, 'downloadCv'])->middleware('menu:career')->name('admin.career_candidates.cv');

    // Certificate Management
    Route::get('/certificates', [AdminCertificateController::class, 'index'])->middleware('menu:certificate')->name('admin.certificates.index');
    Route::post('/certificates', [AdminCertificateController::class, 'store'])->middleware('menu:certificate')->name('admin.certificates.store');
    Route::put('/certificates/{certificate}', [AdminCertificateController::class, 'update'])->middleware('menu:certificate')->name('admin.certificates.update');
    Route::delete('/certificates/{certificate}', [AdminCertificateController::class, 'destroy'])->middleware('menu:certificate')->name('admin.certificates.destroy');
});

});

// Admin pages that can be granted to Users (read-only via GET).
Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
    'role:Super Admin,Admin,Users',
])->group(function () {
    // Assets (read-only)
    Route::get('/assets', [AssetController::class, 'index'])->middleware('menu:assets_data')->name('admin.assets.index');
    Route::get('/assets/datatables', [AssetController::class, 'datatable'])->middleware('menu:assets_data')->name('admin.assets.datatable');
    Route::get('/assets/export/pdf', [AssetController::class, 'exportPdf'])->middleware('menu:assets_data')->name('admin.assets.export.pdf');
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

    // Daily Tasks (read-only)
    Route::get('/daily-tasks', [\App\Http\Controllers\Admin\DailyTaskController::class, 'index'])->middleware('menu:daily_tasks')->name('admin.daily_tasks.index');
    Route::get('/daily-tasks/datatables', [\App\Http\Controllers\Admin\DailyTaskController::class, 'datatable'])->middleware('menu:daily_tasks')->name('admin.daily_tasks.datatable');
    Route::get('/daily-tasks/{task}/json', [\App\Http\Controllers\Admin\DailyTaskController::class, 'json'])->middleware('menu:daily_tasks')->name('admin.daily_tasks.json');
    Route::get('/daily-tasks/export/pdf', [\App\Http\Controllers\Admin\DailyTaskController::class, 'exportPdf'])->middleware('menu:daily_tasks')->name('admin.daily_tasks.export.pdf');

    // Accounts (read-only)
    Route::get('/accounts', [AccountController::class, 'index'])->middleware('menu:accounts_data')->name('admin.accounts.index');
    Route::get('/accounts/{id}', [AccountController::class, 'show'])->middleware('menu:accounts_data')->name('admin.accounts.show');
    Route::get('/accounts/{id}/json', [AccountController::class, 'json'])->middleware('menu:accounts_data')->name('admin.accounts.json');

    // Accounts endpoints (logged open)
    Route::get('/account-endpoints/{endpointId}/open', [AccountController::class, 'openEndpoint'])->middleware('menu:accounts_data')->name('admin.accounts.endpoints.open');

    // Archived Berkas (read-only)
    Route::get('/documents/dashboard', [DocumentController::class, 'dashboard'])->middleware('menu:documents_archive')->name('admin.documents.dashboard');
    Route::get('/documents', [DocumentController::class, 'index'])->middleware('menu:documents_archive')->name('admin.documents.index');
    Route::get('/documents/{id}', [DocumentController::class, 'show'])
        ->whereUuid('id')
        ->middleware('menu:documents_archive')
        ->name('admin.documents.show');

    // Download via signed URL (still requires auth)
    Route::get('/documents/{document}/files/{file}/download', [DocumentController::class, 'downloadFile'])
        ->whereUuid('document')
        ->whereUuid('file')
        ->middleware(['menu:documents_archive', 'signed'])
        ->name('admin.documents.files.download');

    // Secrets (sensitive, requires explicit permission key)
    Route::post('/accounts/secrets/{secretId}/reveal', [AccountController::class, 'revealSecret'])->middleware('menu:accounts_secrets,read')->name('admin.accounts.secrets.reveal');
    Route::post('/accounts/secrets/{secretId}/copy-username', [AccountController::class, 'copyUsername'])->middleware('menu:accounts_secrets,read')->name('admin.accounts.secrets.copy_username');

    // Approvals (requester)
    Route::post('/accounts/secrets/{secretId}/approval', [AccountController::class, 'requestRevealApproval'])->middleware('menu:accounts_secrets,read')->name('admin.accounts.approvals.request');
});

// Daily Tasks (write actions)
Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
    'role:Super Admin,Admin,Users',
])->group(function () {
    Route::post('/daily-tasks', [\App\Http\Controllers\Admin\DailyTaskController::class, 'store'])->middleware('menu:daily_tasks,create')->name('admin.daily_tasks.store');
    Route::put('/daily-tasks/{task}', [\App\Http\Controllers\Admin\DailyTaskController::class, 'update'])->middleware('menu:daily_tasks,update')->name('admin.daily_tasks.update');
    Route::delete('/daily-tasks/{task}', [\App\Http\Controllers\Admin\DailyTaskController::class, 'destroy'])->middleware('menu:daily_tasks,delete')->name('admin.daily_tasks.destroy');

    Route::post('/daily-tasks/{task}/attachments', [\App\Http\Controllers\Admin\DailyTaskController::class, 'uploadAttachment'])->middleware('menu:daily_tasks')->name('admin.daily_tasks.attachments.upload');
    Route::delete('/daily-tasks/attachments/{attachment}', [\App\Http\Controllers\Admin\DailyTaskController::class, 'deleteAttachment'])->middleware('menu:daily_tasks')->name('admin.daily_tasks.attachments.delete');

    Route::post('/daily-tasks/{task}/checklists', [\App\Http\Controllers\Admin\DailyTaskController::class, 'addChecklist'])->middleware('menu:daily_tasks,update')->name('admin.daily_tasks.checklists.add');
    // Allow assignees/creators to tick checklist even if their menu access is read-only.
    Route::patch('/daily-tasks/checklists/{item}', [\App\Http\Controllers\Admin\DailyTaskController::class, 'toggleChecklist'])->middleware('menu:daily_tasks')->name('admin.daily_tasks.checklists.toggle');
    Route::delete('/daily-tasks/checklists/{item}', [\App\Http\Controllers\Admin\DailyTaskController::class, 'deleteChecklist'])->middleware('menu:daily_tasks,update')->name('admin.daily_tasks.checklists.delete');
});

// Archived Berkas (write actions)
Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
    'role:Super Admin,Admin,Users',
])->group(function () {
    Route::get('/documents/create', [DocumentController::class, 'create'])->middleware('menu:documents_archive,create')->name('admin.documents.create');
    Route::post('/documents', [DocumentController::class, 'store'])->middleware('menu:documents_archive,create')->name('admin.documents.store');
    Route::patch('/documents/{id}', [DocumentController::class, 'update'])->whereUuid('id')->middleware('menu:documents_archive,update')->name('admin.documents.update');
    Route::delete('/documents/{id}', [DocumentController::class, 'destroy'])->whereUuid('id')->middleware('menu:documents_archive,delete')->name('admin.documents.destroy');
    Route::post('/documents/{id}/restore', [DocumentController::class, 'restore'])->whereUuid('id')->middleware('menu:documents_archive,update')->name('admin.documents.restore');
    Route::post('/documents/{id}/files', [DocumentController::class, 'uploadFile'])->whereUuid('id')->middleware('menu:documents_archive,update')->name('admin.documents.files.upload');
});

// Manajemen user (khusus Super Admin)
Route::prefix('admin')->middleware([
    EnsureAuthenticated::class,
    'role:Super Admin',
])->group(function () {
    // User Management
    Route::get('/users', [UserController::class, 'index'])->middleware('menu:settings_users')->name('admin.users');
    Route::get('/users/data', [UserController::class, 'data'])->middleware('menu:settings_users')->name('admin.users.data');
    Route::get('/users/{user}', [UserController::class, 'show'])->middleware('menu:settings_users')->name('admin.users.show');
    Route::post('/users', [UserController::class, 'store'])->middleware('menu:settings_users')->name('admin.users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->middleware('menu:settings_users')->name('admin.users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->middleware('menu:settings_users')->name('admin.users.destroy');
    Route::get('/users/history/delete', [DeletedUserController::class, 'index'])->middleware('menu:settings_history_user')->name('admin.users.history.delete');
    Route::post('/users/restore/{id}', [UserController::class, 'restore'])->middleware('menu:settings_users,update')->name('admin.users.restore');

    // Asset Management (write actions)
    Route::post('/assets/in/scan', [AssetController::class, 'scanIn'])->middleware('menu:assets_in,update')->name('admin.assets.in.scan');
    Route::post('/assets/transfer/save', [AssetController::class, 'saveTransfer'])->middleware('menu:assets_transfer')->name('admin.assets.transfer.save');
    Route::post('/assets/transfer/cancel', [AssetController::class, 'cancelTransfer'])->middleware('menu:assets_transfer,update')->name('admin.assets.transfer.cancel');
    Route::get('/assets/create', [AssetController::class, 'create'])->middleware('menu:assets_data')->name('admin.assets.create');
    Route::post('/assets', [AssetController::class, 'store'])->middleware('menu:assets_data')->name('admin.assets.store');
    Route::get('/assets/{id}/edit', [AssetController::class, 'edit'])->middleware('menu:assets_data')->name('admin.assets.edit');
    Route::put('/assets/{id}', [AssetController::class, 'update'])->middleware('menu:assets_data')->name('admin.assets.update');
    Route::delete('/assets/{id}', [AssetController::class, 'destroy'])->middleware('menu:assets_data')->name('admin.assets.destroy');
    Route::get('/assets-history/delete', [AssetController::class, 'historyDelete'])->middleware('menu:settings_history_asset')->name('admin.assets.historyDelete');
    Route::post('/assets/{id}/restore', [AssetController::class, 'restore'])->middleware('menu:assets_data,update')->name('admin.assets.restore');
    Route::post('/assets/print-selected-barcode', [AssetController::class, 'printSelectedBarcode'])->middleware('menu:assets_data,read')->name('admin.assets.printSelectedBarcode');

    // Accounts (write actions)
    Route::post('/accounts', [AccountController::class, 'store'])->middleware('menu:accounts_data')->name('admin.accounts.store');
    Route::put('/accounts/{id}', [AccountController::class, 'update'])->middleware('menu:accounts_data')->name('admin.accounts.update');
    Route::delete('/accounts/{id}', [AccountController::class, 'destroy'])->middleware('menu:accounts_data')->name('admin.accounts.destroy');
    Route::post('/accounts/{id}/verify', [AccountController::class, 'verify'])->middleware('menu:accounts_data,update')->name('admin.accounts.verify');

    // Secrets (sensitive)
    Route::post('/accounts/{accountId}/secrets/rotate', [AccountController::class, 'rotateSecret'])->middleware('menu:accounts_secrets,update')->name('admin.accounts.secrets.rotate');
    Route::post('/accounts/{accountId}/secrets/add', [AccountController::class, 'addSecrets'])->middleware('menu:accounts_secrets,update')->name('admin.accounts.secrets.add');
    Route::post('/accounts/secrets/{secretId}/deactivate', [AccountController::class, 'deactivateSecret'])->middleware('menu:accounts_secrets,update')->name('admin.accounts.secrets.deactivate');

    // Approvals (Super Admin approves)
    Route::post('/accounts/approvals/{approvalId}/approve', [AccountController::class, 'approve'])->middleware('menu:accounts_secrets,update')->name('admin.accounts.approvals.approve');

    // Master Data: Asset
    Route::get('/account-types', [AccountTypeController::class, 'index'])->middleware('menu:account_types')->name('admin.account_types.index');
    Route::post('/account-types', [AccountTypeController::class, 'store'])->middleware('menu:account_types')->name('admin.account_types.store');
    Route::put('/account-types/{type}', [AccountTypeController::class, 'update'])->middleware('menu:account_types')->name('admin.account_types.update');
    Route::post('/account-types/{type}/toggle', [AccountTypeController::class, 'toggle'])->middleware('menu:account_types,update')->name('admin.account_types.toggle');

    Route::get('/asset-categories', [AssetCategoryController::class, 'index'])->middleware('menu:asset_categories')->name('admin.asset_categories.index');
    Route::post('/asset-categories', [AssetCategoryController::class, 'store'])->middleware('menu:asset_categories')->name('admin.asset_categories.store');
    Route::put('/asset-categories/{category}', [AssetCategoryController::class, 'update'])->middleware('menu:asset_categories')->name('admin.asset_categories.update');
    Route::post('/asset-categories/{category}/toggle', [AssetCategoryController::class, 'toggle'])->middleware('menu:asset_categories,update')->name('admin.asset_categories.toggle');

    Route::get('/asset-locations', [AssetLocationController::class, 'index'])->middleware('menu:asset_locations')->name('admin.asset_locations.index');
    Route::post('/asset-locations', [AssetLocationController::class, 'store'])->middleware('menu:asset_locations')->name('admin.asset_locations.store');
    Route::put('/asset-locations/{location}', [AssetLocationController::class, 'update'])->middleware('menu:asset_locations')->name('admin.asset_locations.update');
    Route::post('/asset-locations/{location}/toggle', [AssetLocationController::class, 'toggle'])->middleware('menu:asset_locations,update')->name('admin.asset_locations.toggle');

    // Master Data: Plant/Site (Documents)
    Route::get('/plant-sites', [PlantSiteController::class, 'index'])->middleware('menu:plant_sites')->name('admin.plant_sites.index');
    Route::post('/plant-sites', [PlantSiteController::class, 'store'])->middleware('menu:plant_sites')->name('admin.plant_sites.store');
    Route::put('/plant-sites/{site}', [PlantSiteController::class, 'update'])->middleware('menu:plant_sites')->name('admin.plant_sites.update');
    Route::post('/plant-sites/{site}/toggle', [PlantSiteController::class, 'toggle'])->middleware('menu:plant_sites,update')->name('admin.plant_sites.toggle');

    Route::get('/asset-uoms', [AssetUomController::class, 'index'])->middleware('menu:asset_uoms')->name('admin.asset_uoms.index');
    Route::post('/asset-uoms', [AssetUomController::class, 'store'])->middleware('menu:asset_uoms')->name('admin.asset_uoms.store');
    Route::put('/asset-uoms/{uom}', [AssetUomController::class, 'update'])->middleware('menu:asset_uoms')->name('admin.asset_uoms.update');
    Route::post('/asset-uoms/{uom}/toggle', [AssetUomController::class, 'toggle'])->middleware('menu:asset_uoms,update')->name('admin.asset_uoms.toggle');

    Route::get('/asset-vendors', [AssetVendorController::class, 'index'])->middleware('menu:asset_vendors')->name('admin.asset_vendors.index');
    Route::post('/asset-vendors', [AssetVendorController::class, 'store'])->middleware('menu:asset_vendors')->name('admin.asset_vendors.store');
    Route::put('/asset-vendors/{vendor}', [AssetVendorController::class, 'update'])->middleware('menu:asset_vendors')->name('admin.asset_vendors.update');
    Route::post('/asset-vendors/{vendor}/toggle', [AssetVendorController::class, 'toggle'])->middleware('menu:asset_vendors,update')->name('admin.asset_vendors.toggle');

    // Uniform Stock Management (write actions)
    Route::post('/uniforms/master', [UniformController::class, 'storeItem'])->middleware('menu:uniforms_master')->name('admin.uniforms.items.store');
    Route::put('/uniforms/master/{id}', [UniformController::class, 'updateItem'])->middleware('menu:uniforms_master')->name('admin.uniforms.items.update');
    Route::post('/uniforms/master/{id}/toggle', [UniformController::class, 'toggleItemActive'])->middleware('menu:uniforms_master,update')->name('admin.uniforms.items.toggle');

    Route::post('/uniforms/stock/in', [UniformController::class, 'stockIn'])->middleware('menu:uniforms_stock')->name('admin.uniforms.stock.in');

    Route::post('/uniforms/distribution/issue', [UniformController::class, 'issue'])->middleware('menu:uniforms_distribution')->name('admin.uniforms.distribution.issue');
    Route::post('/uniforms/issues/{issue}/return', [UniformController::class, 'returnIssue'])->middleware('menu:uniforms_distribution,update')->name('admin.uniforms.issues.return');
    Route::post('/uniforms/issues/{issue}/replace', [UniformController::class, 'replaceIssue'])->middleware('menu:uniforms_distribution,update')->name('admin.uniforms.issues.replace');

    Route::get('/uniforms/adjustments', [UniformController::class, 'adjustments'])->middleware('menu:uniforms_stock')->name('admin.uniforms.adjustments');
    Route::post('/uniforms/adjustments', [UniformController::class, 'storeAdjustment'])->middleware('menu:uniforms_stock')->name('admin.uniforms.adjustments.store');
    Route::post('/uniforms/adjustments/{adjustment}/approve', [UniformController::class, 'approveAdjustment'])->middleware('menu:uniforms_stock,update')->name('admin.uniforms.adjustments.approve');
    Route::post('/uniforms/adjustments/{adjustment}/reject', [UniformController::class, 'rejectAdjustment'])->middleware('menu:uniforms_stock,update')->name('admin.uniforms.adjustments.reject');

    Route::get('/uniforms/write-offs', [UniformController::class, 'writeOffs'])->middleware('menu:uniforms_stock')->name('admin.uniforms.writeoffs');
    Route::post('/uniforms/write-offs', [UniformController::class, 'storeWriteOff'])->middleware('menu:uniforms_stock')->name('admin.uniforms.writeoffs.store');
    Route::post('/uniforms/write-offs/{writeoff}/approve', [UniformController::class, 'approveWriteOff'])->middleware('menu:uniforms_stock,update')->name('admin.uniforms.writeoffs.approve');
    Route::post('/uniforms/write-offs/{writeoff}/reject', [UniformController::class, 'rejectWriteOff'])->middleware('menu:uniforms_stock,update')->name('admin.uniforms.writeoffs.reject');

    Route::get('/uniforms/lots', [UniformController::class, 'lots'])->middleware('menu:uniforms_stock')->name('admin.uniforms.lots');

    Route::get('/uniforms/reconcile', [UniformController::class, 'reconcile'])->middleware('menu:uniforms_stock')->name('admin.uniforms.reconcile');
    Route::post('/uniforms/reconcile/create-adjustment', [UniformController::class, 'reconcileCreateAdjustment'])->middleware('menu:uniforms_stock')->name('admin.uniforms.reconcile.adjustment');

    Route::get('/uniforms/history', [UniformController::class, 'history'])->middleware('menu:uniforms_history')->name('admin.uniforms.history');

});

// Safe 404 fallback: avoid exposing internal routes/pages when URL is guessed.
Route::fallback(function () {
    return response()->view('pages.auth.auth-404', [], 404);
});
