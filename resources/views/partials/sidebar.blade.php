<aside class="pe-app-sidebar" id="sidebar">
    @php
        $sidebarHomeUrl = route('home');
        if (auth()->check()) {
            $isUserRole = ((auth()->user()->role?->role_name ?? null) === 'Users') || ((int) auth()->user()->role_id === 3);
            $sidebarHomeUrl = $isUserRole ? route('user.dashboard') : route('admin.dashboard');
        }
    @endphp

    <style>
        /* Fix mode sidebar icon: sembunyikan teks menu level-1 agar tidak "miss" */
        [data-sidebar="icon"] .pe-app-sidebar .pe-main-menu>.pe-slide>.pe-nav-link {
            justify-content: center;
            gap: 0;
        }

        [data-sidebar="icon"] .pe-app-sidebar .pe-main-menu>.pe-slide>.pe-nav-link .pe-nav-content,
        [data-sidebar="icon"] .pe-app-sidebar .pe-main-menu>.pe-slide>.pe-nav-link .pe-nav-arrow {
            display: none !important;
        }

        [data-sidebar="icon"] .pe-app-sidebar .pe-main-menu>.pe-slide>.pe-nav-link .pe-nav-icon {
            margin: 0 auto;
        }
    </style>
    <div class="pe-app-sidebar-logo px-6 d-flex align-items-center position-relative">
        <!--begin::Brand Image-->
        <a href="{{ $sidebarHomeUrl }}" class="fs-18 fw-semibold">
            <img height="30" class="pe-app-sidebar-logo-default d-none" alt="Logo"
                src="{{ asset('assets/img/logo.svg') }}">
            <img height="30" class="pe-app-sidebar-logo-light d-none" alt="Logo"
                src="{{ asset('assets/img/logo_wh.svg') }}">
            <img height="30" class="pe-app-sidebar-logo-minimize d-none" alt="Logo"
                src="{{ asset('assets/img/logo-min.svg') }}">
            <img height="30" class="pe-app-sidebar-logo-minimize-light d-none" alt="Logo"
                src="{{ asset('assets/img/logo-min.svg') }}">
            <!-- FabKin -->
        </a>
        <!--end::Brand Image-->
    </div>
    @php
        $tGroup = function (?string $text): string {
            $map = [
                'Main' => __('menu.group.main'),
                'Application' => __('menu.group.application'),
                'Web Pages' => __('menu.group.web_pages'),
                'Settings & UI' => __('menu.group.settings_ui'),
            ];

            return $map[$text] ?? (string) $text;
        };

        $tMenu = function (?string $text): string {
            $map = [
                'Dashboard' => __('menu.dashboard'),

                'Perlengkapan Aset' => __('menu.assets_equipment'),
                'Data Asset' => __('menu.assets.data_asset'),
                'Data Akun' => __('menu.assets.data_account'),
                'Archived Berkas' => __('menu.assets.archived_documents'),
                'Aset Jababeka' => __('menu.assets.jababeka'),
                'Aset Karawang' => __('menu.assets.karawang'),
                'Aset Masuk' => __('menu.assets.in'),
                'Aset Keluar' => __('menu.assets.out'),

                'Master Karyawan' => __('menu.employees_master'),
                'Employees' => __('menu.employees.employees'),
                'Deleted' => __('menu.employees.deleted'),
                'Audit Log' => __('menu.employees.audit_log'),

                'Master Data' => __('menu.master_data'),
                'Master HR' => __('menu.master_groups.hr'),
                'Master Assets' => __('menu.master_groups.assets'),
                'Master Accounts' => __('menu.master_groups.accounts'),
                'Master Daily Task' => __('menu.master_groups.daily_task'),
                'Departments' => __('menu.master.departments'),
                'Positions' => __('menu.master.positions'),
                'Kategori Asset' => __('menu.master.asset_categories'),
                'Kategori Akun' => __('menu.master.account_categories'),
                'Lokasi Asset' => __('menu.master.asset_locations'),
                'Plant/Site' => __('menu.master.plant_sites'),
                'Satuan Asset' => __('menu.master.asset_uoms'),
                'Vendor Asset' => __('menu.master.asset_vendors'),

                'Task Types' => __('menu.master.daily_task_types'),
                'Task Priorities' => __('menu.master.daily_task_priorities'),
                'Task Statuses' => __('menu.master.daily_task_statuses'),

                'Daily Tasks' => __('menu.daily_tasks'),

                // Settings & UI (legacy English labels)
                'Settings and Log' => 'Pengaturan & Log',
                'Users' => 'Pengguna',
                'History Delete User' => 'Riwayat Hapus Pengguna',
                'History Delete Asset' => 'Riwayat Hapus Aset',
            ];

            return $map[$text] ?? (string) $text;
        };

        $menus = [
            1 => [ // Super Admin
                [
                    'title_group' => 'Main',
                    'items' => [
                        [
                            'title' => 'Dashboard',
                            'icon' => 'fas fa-gauge-high',
                            'route' => 'admin.dashboard',
                            'active_routes' => [
                                'admin',
                                'admin.dashboard',
                                'admin.dashboard.assets',
                                'admin.dashboard.uniforms',
                                'admin.dashboard.stamps',
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Application',
                    'items' => [
                        [
                            'title' => 'Manajemen Materai',
                            'icon' => 'fas fa-stamp',
                            'active_routes' => [
                                'admin.stamps.transactions.index',
                                'admin.stamps.transactions.store_in',
                                'admin.stamps.transactions.store_out',
                                'admin.stamps.report.pdf',
                                'admin.stamps.requests.index',
                                'admin.stamps.requests.store',
                                'admin.stamps.validation.index',
                                'admin.stamps.validation.approve',
                                'admin.stamps.validation.reject',
                                'admin.stamps.validation.handover',
                                'admin.stamps.master.index',
                                'admin.stamps.master.create',
                                'admin.stamps.master.store',
                                'admin.stamps.master.edit',
                                'admin.stamps.master.update',
                                'admin.stamps.master.toggle',
                            ],
                            'children' => [
                                ['title' => 'Ledger', 'route' => 'admin.stamps.transactions.index', 'active_routes' => ['admin.stamps.transactions.datatable', 'admin.stamps.transactions.store_in', 'admin.stamps.transactions.store_out', 'admin.stamps.report.pdf'], 'default' => true, 'permission_key' => 'stamps_transactions'],
                                ['title' => 'Permintaan', 'route' => 'admin.stamps.requests.index', 'active_routes' => ['admin.stamps.requests.store'], 'permission_key' => 'stamps_requests'],
                                ['title' => 'Validasi', 'route' => 'admin.stamps.validation.index', 'active_routes' => ['admin.stamps.validation.approve', 'admin.stamps.validation.reject', 'admin.stamps.validation.handover'], 'permission_key' => 'stamps_validation'],
                                ['title' => 'Master', 'route' => 'admin.stamps.master.index', 'active_routes' => ['admin.stamps.master.datatable', 'admin.stamps.master.create', 'admin.stamps.master.store', 'admin.stamps.master.edit', 'admin.stamps.master.update', 'admin.stamps.master.toggle'], 'permission_key' => 'stamps_master'],
                            ],
                        ],
                        [
                            'title' => 'Manajemen Seragam',
                            'icon' => 'fas fa-shirt',
                            'active_routes' => [
                                'admin.uniforms.master.index',
                                'admin.uniforms.variants.index',
                                'admin.uniforms.lots.index',
                                'admin.uniforms.entitlements.index',
                                'admin.uniforms.stock.index',
                                'admin.uniforms.stock.in',
                                'admin.uniforms.distributions.index',
                                'admin.uniforms.distributions.store',
                                'admin.uniforms.reports.pivot.index',
                                'admin.uniforms.reports.lots.index',
                            ],
                            'children' => [
                                ['title' => 'Master Uniform', 'route' => 'admin.uniforms.master.index', 'active_routes' => ['admin.uniforms.master.datatable', 'admin.uniforms.master.json', 'admin.uniforms.master.store', 'admin.uniforms.master.update', 'admin.uniforms.master.toggle'], 'permission_key' => 'uniforms_master'],
                                ['title' => 'Master Varian', 'route' => 'admin.uniforms.variants.index', 'active_routes' => ['admin.uniforms.variants.datatable', 'admin.uniforms.variants.json', 'admin.uniforms.variants.store', 'admin.uniforms.variants.update', 'admin.uniforms.variants.toggle'], 'permission_key' => 'uniforms_variants'],
                                ['title' => 'Master LOT', 'route' => 'admin.uniforms.lots.index', 'active_routes' => ['admin.uniforms.lots.datatable', 'admin.uniforms.lots.json', 'admin.uniforms.lots.store', 'admin.uniforms.lots.update'], 'permission_key' => 'uniforms_lots'],
                                ['title' => 'Kuota Seragam', 'route' => 'admin.uniforms.entitlements.index', 'active_routes' => ['admin.uniforms.entitlements.datatable', 'admin.uniforms.entitlements.json', 'admin.uniforms.entitlements.store', 'admin.uniforms.entitlements.update'], 'permission_key' => 'uniforms_entitlements'],
                                ['title' => 'Stok', 'route' => 'admin.uniforms.stock.index', 'active_routes' => ['admin.uniforms.stock.datatable', 'admin.uniforms.stock.in'], 'default' => true, 'permission_key' => 'uniforms_stock'],
                                ['title' => 'Distribusi', 'route' => 'admin.uniforms.distributions.index', 'active_routes' => ['admin.uniforms.distributions.datatable', 'admin.uniforms.distributions.store'], 'permission_key' => 'uniforms_distribution'],
                                ['title' => 'Pivot Stok', 'route' => 'admin.uniforms.reports.pivot.index', 'active_routes' => ['admin.uniforms.reports.pivot.datatable'], 'permission_key' => 'uniforms_reports'],
                                ['title' => 'Stok per LOT', 'route' => 'admin.uniforms.reports.lots.index', 'active_routes' => ['admin.uniforms.reports.lots.datatable'], 'permission_key' => 'uniforms_reports'],
                            ],
                        ],
                        [
                            'title' => 'Perlengkapan Aset',
                            'icon' => 'fas fa-hard-drive',
                            'permission_key' => 'assets',
                            'active_routes' => [
                                'admin.assets.index',
                                'admin.assets.show',
                                'admin.assets.edit',
                                'admin.assets.create',
                                'admin.assets.transfer',
                                'admin.assets.in',
                                'admin.assets.jababeka',
                                'admin.assets.karawang',
                            ],
                            'children' => [
                                ['title' => 'Data Asset', 'route' => 'admin.assets.index', 'active_routes' => ['admin.assets.datatable'], 'default' => true, 'permission_key' => 'assets_data'],
                                ['title' => 'Data Akun', 'route' => 'admin.accounts.index', 'active_routes' => ['admin.accounts.show', 'admin.accounts.json', 'admin.accounts.endpoints.open', 'admin.accounts.store', 'admin.accounts.update', 'admin.accounts.destroy', 'admin.accounts.verify'], 'permission_key' => 'accounts_data'],
                                ['title' => 'Archived Berkas', 'route' => 'admin.documents.index', 'active_routes' => ['admin.documents.dashboard', 'admin.documents.show', 'admin.documents.create', 'admin.documents.store', 'admin.documents.update', 'admin.documents.destroy', 'admin.documents.restore', 'admin.documents.files.download', 'admin.documents.files.upload'], 'permission_key' => 'documents_archive'],
                                ['title' => 'Aset Jababeka', 'route' => 'admin.assets.jababeka', 'active_routes' => ['admin.assets.index'], 'params' => ['location' => 'Jababeka'], 'permission_key' => 'assets_jababeka'],
                                ['title' => 'Aset Karawang', 'route' => 'admin.assets.karawang', 'active_routes' => ['admin.assets.index'], 'params' => ['location' => 'Karawang'], 'permission_key' => 'assets_karawang'],
                                ['title' => 'Aset Masuk', 'route' => 'admin.assets.in', 'active_routes' => ['admin.assets.in.scan'], 'permission_key' => 'assets_in'],
                                ['title' => 'Aset Keluar', 'route' => 'admin.assets.transfer', 'active_routes' => ['admin.assets.transfer.list'], 'permission_key' => 'assets_transfer'],
                            ],
                        ],
                        [
                            'title' => 'Master Device',
                            'icon' => 'fas fa-desktop',
                            'route' => 'admin.devices.index',
                            'permission_key' => 'devices',
                            'active_routes' => [
                                'admin.devices.index',
                                'admin.devices.create',
                                'admin.devices.store',
                                'admin.devices.show',
                                'admin.devices.edit',
                                'admin.devices.update',
                                'admin.devices.destroy',
                            ],
                        ],
                        [
                            'title' => 'Daily Tasks',
                            'icon' => 'fas fa-clipboard-check',
                            'route' => 'admin.daily_tasks.index',
                            'permission_key' => 'daily_tasks',
                            'active_routes' => [
                                'admin.daily_tasks.index',
                                'admin.daily_tasks.datatable',
                                'admin.daily_tasks.json',
                                'admin.daily_tasks.store',
                                'admin.daily_tasks.update',
                                'admin.daily_tasks.destroy',
                                'admin.daily_tasks.attachments.upload',
                                'admin.daily_tasks.attachments.delete',
                                'admin.daily_tasks.checklists.add',
                                'admin.daily_tasks.checklists.toggle',
                                'admin.daily_tasks.checklists.delete',
                            ],
                        ],
                        [
                            'title' => 'Master Karyawan',
                            'icon' => 'fas fa-users',
                            'permission_key' => 'employees',
                            'active_routes' => [
                                'admin.employees.index',
                                'admin.employees.store',
                                'admin.employees.update',
                                'admin.employees.destroy',
                                'admin.employees.deleted',
                                'admin.employees.restore',
                                'admin.employees.audit',
                            ],
                            'children' => [
                                ['title' => 'Employees', 'route' => 'admin.employees.index', 'default' => true, 'permission_key' => 'employees_index'],
                                ['title' => 'Deleted', 'route' => 'admin.employees.deleted', 'permission_key' => 'employees_deleted'],
                                ['title' => 'Audit Log', 'route' => 'admin.employees.audit', 'permission_key' => 'employees_audit'],
                            ],
                        ],
                        [
                            'title' => 'Master HR',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_hr',
                            'active_routes' => [
                                'admin.departments.index',
                                'admin.departments.store',
                                'admin.departments.update',
                                'admin.departments.destroy',
                                'admin.positions.index',
                                'admin.positions.store',
                                'admin.positions.update',
                                'admin.positions.destroy',
                            ],
                            'children' => [
                                ['title' => 'Departments', 'route' => 'admin.departments.index', 'default' => true, 'permission_key' => 'departments'],
                                ['title' => 'Positions', 'route' => 'admin.positions.index', 'permission_key' => 'positions'],
                            ],
                        ],
                        [
                            'title' => 'Master Assets',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_assets',
                            'active_routes' => [
                                'admin.asset_categories.index',
                                'admin.asset_categories.store',
                                'admin.asset_categories.update',
                                'admin.asset_categories.toggle',
                                'admin.asset_locations.index',
                                'admin.asset_locations.store',
                                'admin.asset_locations.update',
                                'admin.asset_locations.toggle',
                                'admin.plant_sites.index',
                                'admin.plant_sites.store',
                                'admin.plant_sites.update',
                                'admin.plant_sites.toggle',
                                'admin.asset_uoms.index',
                                'admin.asset_uoms.store',
                                'admin.asset_uoms.update',
                                'admin.asset_uoms.toggle',
                                'admin.asset_vendors.index',
                                'admin.asset_vendors.store',
                                'admin.asset_vendors.update',
                                'admin.asset_vendors.toggle',
                            ],
                            'children' => [
                                ['title' => 'Kategori Asset', 'route' => 'admin.asset_categories.index', 'default' => true, 'permission_key' => 'asset_categories'],
                                ['title' => 'Lokasi Asset', 'route' => 'admin.asset_locations.index', 'permission_key' => 'asset_locations'],
                                ['title' => 'Plant/Site', 'route' => 'admin.plant_sites.index', 'permission_key' => 'plant_sites'],
                                ['title' => 'Satuan Asset', 'route' => 'admin.asset_uoms.index', 'permission_key' => 'asset_uoms'],
                                ['title' => 'Vendor Asset', 'route' => 'admin.asset_vendors.index', 'permission_key' => 'asset_vendors'],
                            ],
                        ],
                        [
                            'title' => 'Master Accounts',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_accounts',
                            'active_routes' => [
                                'admin.account_types.index',
                                'admin.account_types.store',
                                'admin.account_types.update',
                                'admin.account_types.toggle',
                            ],
                            'children' => [
                                ['title' => 'Kategori Akun', 'route' => 'admin.account_types.index', 'default' => true, 'permission_key' => 'account_types'],
                            ],
                        ],
                        [
                            'title' => 'Master Daily Task',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_daily_task',
                            'active_routes' => [
                                'admin.daily_task_types.index',
                                'admin.daily_task_types.store',
                                'admin.daily_task_types.update',
                                'admin.daily_task_types.toggle',
                                'admin.daily_task_priorities.index',
                                'admin.daily_task_priorities.store',
                                'admin.daily_task_priorities.update',
                                'admin.daily_task_priorities.toggle',
                                'admin.daily_task_statuses.index',
                                'admin.daily_task_statuses.update',
                                'admin.daily_task_statuses.toggle',
                            ],
                            'children' => [
                                ['title' => 'Task Types', 'route' => 'admin.daily_task_types.index', 'default' => true, 'permission_key' => 'daily_task_types'],
                                ['title' => 'Task Priorities', 'route' => 'admin.daily_task_priorities.index', 'permission_key' => 'daily_task_priorities'],
                                ['title' => 'Task Statuses', 'route' => 'admin.daily_task_statuses.index', 'permission_key' => 'daily_task_statuses'],
                            ],
                        ],
                    ],
                ],
                // [
                //     'title_group' => 'Applications',
                //     'items' => [
                //         [
                //             'title' => 'Calendar',
                //             'icon' => 'fas fa-calendar-week',
                //             'route' => 'apps-calendar',
                //         ],
                //         [
                //             'title' => 'E-Commerce',
                //             'icon' => 'fas fa-shopping-cart',
                //             'children' => [
                //                 ['title' => 'Products', 'route' => 'apps-ecommerce-products'],
                //                 ['title' => 'Product Details', 'route' => 'apps-ecommerce-products-details'],
                //                 ['title' => 'Product List', 'route' => 'apps-ecommerce-products-list'],
                //                 ['title' => 'Add Product', 'route' => 'apps-ecommerce-add-products'],
                //                 ['title' => 'Order Details', 'route' => 'apps-ecommerce-order-details'],
                //                 ['title' => 'Orders', 'route' => 'apps-ecommerce-order'],
                //                 ['title' => 'Cart', 'route' => 'apps-ecommerce-cart'],
                //                 ['title' => 'Checkout', 'route' => 'apps-ecommerce-checkout'],
                //                 ['title' => 'Wishlist', 'route' => 'apps-ecommerce-wishlist'],
                //             ],
                //         ],
                //     ],
                // ],
                [
                    'title_group' => 'Web Pages',
                    'items' => [
                        [
                            'title' => 'Career Management',
                            'icon' => 'fas fa-briefcase',
                            'route' => 'admin.careers.index',
                            'permission_key' => 'career',
                            'active_routes' => [
                                'admin.careers.index',
                                'admin.careers.company.update',
                                'admin.careers.store',
                                'admin.careers.update',
                                'admin.careers.destroy',
                            ],
                        ],
                        [
                            'title' => 'Certificate Management',
                            'icon' => 'fas fa-circle-check',
                            'route' => 'admin.certificates.index',
                            'permission_key' => 'certificate',
                            'active_routes' => [
                                'admin.certificates.index',
                                'admin.certificates.store',
                                'admin.certificates.update',
                                'admin.certificates.destroy',
                            ],
                        ],
                        [
                            'title' => 'Website Products',
                            'icon' => 'fas fa-box',
                            'route' => 'admin.website_products.index',
                            'permission_key' => 'website_products',
                            'active_routes' => [
                                'admin.website_products.index',
                                'admin.website_products.create',
                                'admin.website_products.store',
                                'admin.website_products.edit',
                                'admin.website_products.update',
                                'admin.website_products.destroy',
                            ],
                        ],
                        [
                            'title' => 'Website Settings',
                            'icon' => 'fas fa-sliders',
                            'route' => 'admin.website_settings.edit',
                            'permission_key' => 'website_settings',
                            'active_routes' => [
                                'admin.website_settings.edit',
                                'admin.website_settings.update',
                            ],
                        ],
                        [
                            'title' => 'Website Contact Page',
                            'icon' => 'fas fa-address-book',
                            'route' => 'admin.website_contact_page.edit',
                            'permission_key' => 'website_contact_page',
                            'active_routes' => [
                                'admin.website_contact_page.edit',
                                'admin.website_contact_page.update',
                            ],
                        ],
                        [
                            'title' => 'Website Home Sections',
                            'icon' => 'fas fa-house-chimney',
                            'route' => 'admin.website_home_sections.edit',
                            'permission_key' => 'website_home_sections',
                            'active_routes' => [
                                'admin.website_home_sections.edit',
                                'admin.website_home_sections.update',
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Settings & UI',
                    'items' => [
                        [
                            'title' => 'Settings and Log',
                            'icon' => 'fas fa-gears',
                            'permission_key' => 'settings',
                            'children' => [
                                ['title' => 'Users', 'route' => 'admin.users', 'permission_key' => 'settings_users'],
                                ['title' => 'History Delete User', 'route' => 'admin.users.history.delete', 'permission_key' => 'settings_history_user'],
                                ['title' => 'History Delete Asset', 'route' => 'admin.assets.historyDelete', 'permission_key' => 'settings_history_asset'],
                            ],
                        ],
                    ],
                ],
            ],
            2 => [ // Admin
                [
                    'title_group' => 'Main',
                    'items' => [
                        [
                            'title' => 'Dashboard',
                            'icon' => 'fas fa-gauge-high',
                            'route' => 'admin.dashboard',
                            'active_routes' => [
                                'admin',
                                'admin.dashboard',
                                'admin.dashboard.assets',
                                'admin.dashboard.uniforms',
                                'admin.dashboard.stamps',
                            ],
                        ],
                        [
                            'title' => 'Manajemen Materai',
                            'icon' => 'fas fa-stamp',
                            'active_routes' => [
                                'admin.stamps.transactions.index',
                                'admin.stamps.transactions.store_in',
                                'admin.stamps.transactions.store_out',
                                'admin.stamps.report.pdf',
                                'admin.stamps.requests.index',
                                'admin.stamps.requests.store',
                                'admin.stamps.validation.index',
                                'admin.stamps.validation.approve',
                                'admin.stamps.validation.reject',
                                'admin.stamps.validation.handover',
                                'admin.stamps.master.index',
                                'admin.stamps.master.create',
                                'admin.stamps.master.store',
                                'admin.stamps.master.edit',
                                'admin.stamps.master.update',
                                'admin.stamps.master.toggle',
                            ],
                            'children' => [
                                ['title' => 'Ledger', 'route' => 'admin.stamps.transactions.index', 'active_routes' => ['admin.stamps.transactions.datatable', 'admin.stamps.transactions.store_in', 'admin.stamps.transactions.store_out', 'admin.stamps.report.pdf'], 'default' => true, 'permission_key' => 'stamps_transactions'],
                                ['title' => 'Permintaan', 'route' => 'admin.stamps.requests.index', 'active_routes' => ['admin.stamps.requests.store'], 'permission_key' => 'stamps_requests'],
                                ['title' => 'Validasi', 'route' => 'admin.stamps.validation.index', 'active_routes' => ['admin.stamps.validation.approve', 'admin.stamps.validation.reject', 'admin.stamps.validation.handover'], 'permission_key' => 'stamps_validation'],
                                ['title' => 'Master', 'route' => 'admin.stamps.master.index', 'active_routes' => ['admin.stamps.master.datatable', 'admin.stamps.master.create', 'admin.stamps.master.store', 'admin.stamps.master.edit', 'admin.stamps.master.update', 'admin.stamps.master.toggle'], 'permission_key' => 'stamps_master'],
                            ],
                        ],
                        [
                            'title' => 'Manajemen Seragam',
                            'icon' => 'fas fa-shirt',
                            'active_routes' => [
                                'admin.uniforms.master.index',
                                'admin.uniforms.variants.index',
                                'admin.uniforms.lots.index',
                                'admin.uniforms.entitlements.index',
                                'admin.uniforms.stock.index',
                                'admin.uniforms.stock.in',
                                'admin.uniforms.distributions.index',
                                'admin.uniforms.distributions.store',
                                'admin.uniforms.reports.pivot.index',
                                'admin.uniforms.reports.lots.index',
                            ],
                            'children' => [
                                ['title' => 'Master Uniform', 'route' => 'admin.uniforms.master.index', 'active_routes' => ['admin.uniforms.master.datatable', 'admin.uniforms.master.json', 'admin.uniforms.master.store', 'admin.uniforms.master.update', 'admin.uniforms.master.toggle'], 'permission_key' => 'uniforms_master'],
                                ['title' => 'Master Varian', 'route' => 'admin.uniforms.variants.index', 'active_routes' => ['admin.uniforms.variants.datatable', 'admin.uniforms.variants.json', 'admin.uniforms.variants.store', 'admin.uniforms.variants.update', 'admin.uniforms.variants.toggle'], 'permission_key' => 'uniforms_variants'],
                                ['title' => 'Master LOT', 'route' => 'admin.uniforms.lots.index', 'active_routes' => ['admin.uniforms.lots.datatable', 'admin.uniforms.lots.json', 'admin.uniforms.lots.store', 'admin.uniforms.lots.update'], 'permission_key' => 'uniforms_lots'],
                                ['title' => 'Kuota Seragam', 'route' => 'admin.uniforms.entitlements.index', 'active_routes' => ['admin.uniforms.entitlements.datatable', 'admin.uniforms.entitlements.json', 'admin.uniforms.entitlements.store', 'admin.uniforms.entitlements.update'], 'permission_key' => 'uniforms_entitlements'],
                                ['title' => 'Stok', 'route' => 'admin.uniforms.stock.index', 'active_routes' => ['admin.uniforms.stock.datatable', 'admin.uniforms.stock.in'], 'default' => true, 'permission_key' => 'uniforms_stock'],
                                ['title' => 'Distribusi', 'route' => 'admin.uniforms.distributions.index', 'active_routes' => ['admin.uniforms.distributions.datatable', 'admin.uniforms.distributions.store'], 'permission_key' => 'uniforms_distribution'],
                                ['title' => 'Pivot Stok', 'route' => 'admin.uniforms.reports.pivot.index', 'active_routes' => ['admin.uniforms.reports.pivot.datatable'], 'permission_key' => 'uniforms_reports'],
                                ['title' => 'Stok per LOT', 'route' => 'admin.uniforms.reports.lots.index', 'active_routes' => ['admin.uniforms.reports.lots.datatable'], 'permission_key' => 'uniforms_reports'],
                            ],
                        ],
                        [
                            'title' => 'Master Karyawan',
                            'icon' => 'fas fa-users',
                            'permission_key' => 'employees',
                            'active_routes' => [
                                'admin.employees.index',
                                'admin.employees.store',
                                'admin.employees.update',
                                'admin.employees.destroy',
                                'admin.employees.deleted',
                                'admin.employees.restore',
                                'admin.employees.audit',
                            ],
                            'children' => [
                                ['title' => 'Employees', 'route' => 'admin.employees.index', 'default' => true, 'permission_key' => 'employees_index'],
                                ['title' => 'Deleted', 'route' => 'admin.employees.deleted', 'permission_key' => 'employees_deleted'],
                                ['title' => 'Audit Log', 'route' => 'admin.employees.audit', 'permission_key' => 'employees_audit'],
                            ],
                        ],
                        [
                            'title' => 'Master HR',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_hr',
                            'active_routes' => [
                                'admin.departments.index',
                                'admin.departments.store',
                                'admin.departments.update',
                                'admin.departments.destroy',
                                'admin.positions.index',
                                'admin.positions.store',
                                'admin.positions.update',
                                'admin.positions.destroy',
                            ],
                            'children' => [
                                ['title' => 'Departments', 'route' => 'admin.departments.index', 'default' => true, 'permission_key' => 'departments'],
                                ['title' => 'Positions', 'route' => 'admin.positions.index', 'permission_key' => 'positions'],
                            ],
                        ],
                        [
                            'title' => 'Master Assets',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_assets',
                            'active_routes' => [
                                'admin.asset_categories.index',
                                'admin.asset_categories.store',
                                'admin.asset_categories.update',
                                'admin.asset_categories.toggle',
                                'admin.asset_locations.index',
                                'admin.asset_locations.store',
                                'admin.asset_locations.update',
                                'admin.asset_locations.toggle',
                                'admin.plant_sites.index',
                                'admin.plant_sites.store',
                                'admin.plant_sites.update',
                                'admin.plant_sites.toggle',
                                'admin.asset_uoms.index',
                                'admin.asset_uoms.store',
                                'admin.asset_uoms.update',
                                'admin.asset_uoms.toggle',
                                'admin.asset_vendors.index',
                                'admin.asset_vendors.store',
                                'admin.asset_vendors.update',
                                'admin.asset_vendors.toggle',
                            ],
                            'children' => [
                                ['title' => 'Kategori Asset', 'route' => 'admin.asset_categories.index', 'default' => true, 'permission_key' => 'asset_categories'],
                                ['title' => 'Lokasi Asset', 'route' => 'admin.asset_locations.index', 'permission_key' => 'asset_locations'],
                                ['title' => 'Plant/Site', 'route' => 'admin.plant_sites.index', 'permission_key' => 'plant_sites'],
                                ['title' => 'Satuan Asset', 'route' => 'admin.asset_uoms.index', 'permission_key' => 'asset_uoms'],
                                ['title' => 'Vendor Asset', 'route' => 'admin.asset_vendors.index', 'permission_key' => 'asset_vendors'],
                            ],
                        ],
                        [
                            'title' => 'Master Accounts',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_accounts',
                            'active_routes' => [
                                'admin.account_types.index',
                                'admin.account_types.store',
                                'admin.account_types.update',
                                'admin.account_types.toggle',
                            ],
                            'children' => [
                                ['title' => 'Kategori Akun', 'route' => 'admin.account_types.index', 'default' => true, 'permission_key' => 'account_types'],
                            ],
                        ],
                        [
                            'title' => 'Master Daily Task',
                            'icon' => 'fas fa-database',
                            'permission_key' => 'master_daily_task',
                            'active_routes' => [
                                'admin.daily_task_types.index',
                                'admin.daily_task_types.store',
                                'admin.daily_task_types.update',
                                'admin.daily_task_types.toggle',
                                'admin.daily_task_priorities.index',
                                'admin.daily_task_priorities.store',
                                'admin.daily_task_priorities.update',
                                'admin.daily_task_priorities.toggle',
                                'admin.daily_task_statuses.index',
                                'admin.daily_task_statuses.update',
                                'admin.daily_task_statuses.toggle',
                            ],
                            'children' => [
                                ['title' => 'Task Types', 'route' => 'admin.daily_task_types.index', 'default' => true, 'permission_key' => 'daily_task_types'],
                                ['title' => 'Task Priorities', 'route' => 'admin.daily_task_priorities.index', 'permission_key' => 'daily_task_priorities'],
                                ['title' => 'Task Statuses', 'route' => 'admin.daily_task_statuses.index', 'permission_key' => 'daily_task_statuses'],
                            ],
                        ],
                        [
                            'title' => 'Daily Tasks',
                            'icon' => 'fas fa-clipboard-check',
                            'route' => 'admin.daily_tasks.index',
                            'permission_key' => 'daily_tasks',
                            'active_routes' => [
                                'admin.daily_tasks.index',
                                'admin.daily_tasks.datatable',
                                'admin.daily_tasks.json',
                                'admin.daily_tasks.store',
                                'admin.daily_tasks.update',
                                'admin.daily_tasks.destroy',
                                'admin.daily_tasks.attachments.upload',
                                'admin.daily_tasks.attachments.delete',
                                'admin.daily_tasks.checklists.add',
                                'admin.daily_tasks.checklists.toggle',
                                'admin.daily_tasks.checklists.delete',
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Web Pages',
                    'items' => [
                        [
                            'title' => 'Career Management',
                            'icon' => 'fas fa-briefcase',
                            'route' => 'admin.careers.index',
                            'permission_key' => 'career',
                            'active_routes' => [
                                'admin.careers.index',
                                'admin.careers.company.update',
                                'admin.careers.store',
                                'admin.careers.update',
                                'admin.careers.destroy',
                            ],
                        ],
                    ],
                ],
            ],
            3 => [ // Users
                [
                    'title_group' => 'Main',
                    'items' => [
                        [
                            'title' => 'Dashboard',
                            'icon' => 'fas fa-gauge-high',
                            'route' => 'user.dashboard',
                            'permission_key' => 'user_dashboard',
                            'active_routes' => [
                                'user.dashboard',
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Application',
                    'items' => [
                        [
                            'title' => 'Permintaan Materai',
                            'icon' => 'fas fa-stamp',
                            'route' => 'admin.stamps.requests.index',
                            'permission_key' => 'stamps_requests',
                            'active_routes' => [
                                'admin.stamps.requests.index',
                                'admin.stamps.requests.store',
                            ],
                        ],
                        [
                            'title' => 'Validasi Materai',
                            'icon' => 'fas fa-check-circle',
                            'route' => 'admin.stamps.validation.index',
                            'permission_key' => 'stamps_validation',
                            'active_routes' => [
                                'admin.stamps.validation.index',
                                'admin.stamps.validation.approve',
                                'admin.stamps.validation.reject',
                                'admin.stamps.validation.handover',
                            ],
                        ],
                        [
                            'title' => 'Perlengkapan Aset',
                            'icon' => 'fas fa-hard-drive',
                            'permission_key' => 'assets',
                            'active_routes' => [
                                'admin.assets.index',
                                'admin.assets.show',
                                'admin.assets.jababeka',
                                'admin.assets.karawang',
                                'admin.assets.in',
                                'admin.assets.transfer',
                            ],
                            'children' => [
                                ['title' => 'Data Asset', 'route' => 'admin.assets.index', 'active_routes' => ['admin.assets.datatable'], 'default' => true, 'permission_key' => 'assets_data'],
                                ['title' => 'Data Akun', 'route' => 'admin.accounts.index', 'active_routes' => ['admin.accounts.show', 'admin.accounts.json', 'admin.accounts.endpoints.open', 'admin.accounts.store', 'admin.accounts.update', 'admin.accounts.destroy', 'admin.accounts.verify'], 'permission_key' => 'accounts_data'],
                                ['title' => 'Archived Berkas', 'route' => 'admin.documents.index', 'active_routes' => ['admin.documents.dashboard', 'admin.documents.show', 'admin.documents.create', 'admin.documents.store', 'admin.documents.update', 'admin.documents.destroy', 'admin.documents.restore', 'admin.documents.files.download', 'admin.documents.files.upload'], 'permission_key' => 'documents_archive'],
                                ['title' => 'Aset Jababeka', 'route' => 'admin.assets.jababeka', 'active_routes' => ['admin.assets.index'], 'params' => ['location' => 'Jababeka'], 'permission_key' => 'assets_jababeka'],
                                ['title' => 'Aset Karawang', 'route' => 'admin.assets.karawang', 'active_routes' => ['admin.assets.index'], 'params' => ['location' => 'Karawang'], 'permission_key' => 'assets_karawang'],
                                ['title' => 'Aset Masuk', 'route' => 'admin.assets.in', 'active_routes' => ['admin.assets.in.scan'], 'permission_key' => 'assets_in'],
                                ['title' => 'Aset Keluar', 'route' => 'admin.assets.transfer', 'active_routes' => ['admin.assets.transfer.list'], 'permission_key' => 'assets_transfer'],
                            ],
                        ],
                        [
                            'title' => 'Master Device',
                            'icon' => 'fas fa-desktop',
                            'route' => 'admin.devices.index',
                            'permission_key' => 'devices',
                            'active_routes' => [
                                'admin.devices.index',
                                'admin.devices.show',
                            ],
                        ],
                        [
                            'title' => 'Daily Tasks',
                            'icon' => 'fas fa-clipboard-check',
                            'route' => 'admin.daily_tasks.index',
                            'permission_key' => 'daily_tasks',
                            'active_routes' => [
                                'admin.daily_tasks.index',
                                'admin.daily_tasks.datatable',
                                'admin.daily_tasks.json',
                                'admin.daily_tasks.store',
                                'admin.daily_tasks.update',
                                'admin.daily_tasks.destroy',
                                'admin.daily_tasks.attachments.upload',
                                'admin.daily_tasks.attachments.delete',
                                'admin.daily_tasks.checklists.add',
                                'admin.daily_tasks.checklists.toggle',
                                'admin.daily_tasks.checklists.delete',
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $user = auth()->user();
        $roleId = $user?->role_id;

        $normalizeMenuLevel = function ($value): string {
            if ($value === true) {
                return 'write';
            }
            if ($value === false || $value === null) {
                return 'none';
            }

            $v = strtolower(trim((string) $value));
            if ($v === 'write' || $v === 'rw') {
                return 'write';
            }
            if ($v === 'read' || $v === 'r') {
                return 'read';
            }
            if ($v === 'none' || $v === '0' || $v === '') {
                return 'none';
            }
            return 'read';
        };

        $menuDefaults = match ((int) $roleId) {
            3 => [
                'user_dashboard' => 'read',
                'admin_dashboard' => 'none',
                // Groups
                'assets' => 'none',
                'uniforms' => 'none',

                // Devices
                'devices' => 'none',

                // Assets submenus
                'assets_data' => 'none',
                'accounts_data' => 'none',
                'accounts_secrets' => 'none',
                'assets_jababeka' => 'none',
                'assets_karawang' => 'none',
                'assets_in' => 'none',
                'assets_transfer' => 'none',

                // Uniforms submenus
                'uniforms_master' => 'none',
                'uniforms_stock' => 'none',
                'uniforms_distribution' => 'none',
                'uniforms_history' => 'none',

                'employees' => 'none',
                'master_data' => 'none',
                'employees_index' => 'none',
                'employees_deleted' => 'none',
                'employees_audit' => 'none',
                'departments' => 'none',
                'positions' => 'none',
                'asset_categories' => 'none',
                'account_types' => 'none',
                'asset_locations' => 'none',
                'plant_sites' => 'none',
                'asset_uoms' => 'none',
                'asset_vendors' => 'none',
                'uniform_sizes' => 'none',
                'uniform_item_names' => 'none',
                'uniform_categories' => 'none',
                'uniform_colors' => 'none',
                'uniform_uoms' => 'none',
                'career' => 'none',
                'certificate' => 'none',
                'settings' => 'none',
                'settings_users' => 'none',
                'settings_history_user' => 'none',
                'settings_history_asset' => 'none',
            ],
            default => [
                'user_dashboard' => 'read',
                'admin_dashboard' => 'read',
                // Groups
                'assets' => 'write',
                'uniforms' => 'write',

                // Devices
                'devices' => 'write',

                // Assets submenus
                'assets_data' => 'write',
                'accounts_data' => 'write',
                'accounts_secrets' => 'write',
                'assets_jababeka' => 'write',
                'assets_karawang' => 'write',
                'assets_in' => 'write',
                'assets_transfer' => 'write',

                // Uniforms submenus
                'uniforms_master' => 'write',
                'uniforms_stock' => 'write',
                'uniforms_distribution' => 'write',
                'uniforms_history' => 'write',

                'employees' => 'write',
                'master_data' => 'write',
                'employees_index' => 'write',
                'employees_deleted' => 'write',
                'employees_audit' => 'write',
                'departments' => 'write',
                'positions' => 'write',
                'asset_categories' => 'write',
                'account_types' => 'write',
                'asset_locations' => 'write',
                'plant_sites' => 'write',
                'asset_uoms' => 'write',
                'asset_vendors' => 'write',
                'uniform_sizes' => 'write',
                'uniform_item_names' => 'write',
                'uniform_categories' => 'write',
                'uniform_colors' => 'write',
                'uniform_uoms' => 'write',
                'career' => 'write',
                'certificate' => 'write',
                'settings' => 'write',
                'settings_users' => 'write',
                'settings_history_user' => 'write',
                'settings_history_asset' => 'write',
            ],
        };

        $menuPermissions = \App\Support\MenuAccess::effectivePermissions($user);

        $isMenuAllowed = function (?string $key) use ($menuPermissions): bool {
            if ($key === null) {
                return true;
            }
            $p = $menuPermissions[$key] ?? ['read' => false];
            return (bool) ($p['read'] ?? false);
        };

        $inferPermissionKey = function (array $item): ?string {
            $routes = [];
            if (!empty($item['route'])) {
                $routes[] = $item['route'];
            }
            if (!empty($item['active_routes']) && is_array($item['active_routes'])) {
                $routes = array_merge($routes, $item['active_routes']);
            }
            if (!empty($item['children']) && is_array($item['children'])) {
                foreach ($item['children'] as $child) {
                    if (!empty($child['route'])) {
                        $routes[] = $child['route'];
                    }
                }
            }

            foreach ($routes as $routeName) {
                if (!is_string($routeName) || $routeName === '') {
                    continue;
                }

                if ($routeName === 'admin' || $routeName === 'admin.dashboard' || str_starts_with($routeName, 'admin.dashboard.')) {
                    return 'admin_dashboard';
                }
                if ($routeName === 'user.dashboard' || str_starts_with($routeName, 'user.')) {
                    return 'user_dashboard';
                }
                if (str_starts_with($routeName, 'admin.assets.')) {
                    // Assets leaf permissions
                    if (
                        in_array($routeName, [
                            'admin.assets.index',
                            'admin.assets.show',
                            'admin.assets.edit',
                            'admin.assets.update',
                            'admin.assets.destroy',
                            'admin.assets.create',
                            'admin.assets.store',
                            'admin.assets.modalList',
                            'admin.assets.json',
                            'admin.assets.barcode',
                            'admin.assets.printBarcode',
                            'admin.assets.printSelectedBarcode',
                            'admin.assets.historyDelete',
                            'admin.assets.restore',
                        ], true)
                    ) {
                        return 'assets_data';
                    }

                    if (
                        in_array($routeName, [
                            'admin.assets.jababeka',
                        ], true)
                    ) {
                        return 'assets_jababeka';
                    }

                    if (
                        in_array($routeName, [
                            'admin.assets.karawang',
                        ], true)
                    ) {
                        return 'assets_karawang';
                    }

                    if (
                        in_array($routeName, [
                            'admin.assets.in',
                            'admin.assets.in.scan',
                        ], true)
                    ) {
                        return 'assets_in';
                    }

                    if (
                        in_array($routeName, [
                            'admin.assets.transfer',
                            'admin.assets.transfer.list',
                            'admin.assets.transfer.save',
                            'admin.assets.transfer.cancel',
                        ], true)
                    ) {
                        return 'assets_transfer';
                    }

                    // Fallback: treat as assets group
                    return 'assets';
                }

                if (str_starts_with($routeName, 'admin.accounts.')) {
                    if (
                        in_array($routeName, [
                            'admin.accounts.index',
                            'admin.accounts.show',
                            'admin.accounts.json',
                            'admin.accounts.store',
                            'admin.accounts.update',
                            'admin.accounts.destroy',
                            'admin.accounts.verify',
                            'admin.accounts.endpoints.open',
                        ], true)
                    ) {
                        return 'accounts_data';
                    }

                    if (
                        in_array($routeName, [
                            'admin.accounts.secrets.reveal',
                            'admin.accounts.secrets.rotate',
                            'admin.accounts.secrets.copy_username',
                            'admin.accounts.approvals.request',
                            'admin.accounts.approvals.approve',
                        ], true)
                    ) {
                        return 'accounts_secrets';
                    }

                    return 'accounts_data';
                }

                if (str_starts_with($routeName, 'admin.documents.')) {
                    return 'documents_archive';
                }

                if (str_starts_with($routeName, 'admin.devices.')) {
                    return 'devices';
                }

                if (str_starts_with($routeName, 'admin.stamps.master.')) {
                    return 'stamps_master';
                }
                if (str_starts_with($routeName, 'admin.stamps.transactions.') || $routeName === 'admin.stamps.report.pdf') {
                    return 'stamps_transactions';
                }

                if (str_starts_with($routeName, 'admin.account_types.')) {
                    return 'account_types';
                }
                if (str_starts_with($routeName, 'admin.employees.')) {
                    if (in_array($routeName, ['admin.employees.deleted', 'admin.employees.restore'], true)) {
                        return 'employees_deleted';
                    }
                    if (in_array($routeName, ['admin.employees.audit'], true)) {
                        return 'employees_audit';
                    }
                    return 'employees_index';
                }
                if (preg_match('/^admin\.(departments)\./', $routeName) === 1) {
                    return 'departments';
                }
                if (preg_match('/^admin\.(positions)\./', $routeName) === 1) {
                    return 'positions';
                }
                if (str_starts_with($routeName, 'admin.daily_tasks.')) {
                    return 'daily_tasks';
                }
                if (preg_match('/^admin\.(daily_task_types)\./', $routeName) === 1) {
                    return 'daily_task_types';
                }
                if (preg_match('/^admin\.(daily_task_priorities)\./', $routeName) === 1) {
                    return 'daily_task_priorities';
                }
                if (preg_match('/^admin\.(daily_task_statuses)\./', $routeName) === 1) {
                    return 'daily_task_statuses';
                }
                if (preg_match('/^admin\.(asset_categories)\./', $routeName) === 1) {
                    return 'asset_categories';
                }
                if (preg_match('/^admin\.(asset_locations)\./', $routeName) === 1) {
                    return 'asset_locations';
                }
                if (preg_match('/^admin\.(plant_sites)\./', $routeName) === 1) {
                    return 'plant_sites';
                }
                if (preg_match('/^admin\.(asset_uoms)\./', $routeName) === 1) {
                    return 'asset_uoms';
                }
                if (preg_match('/^admin\.(asset_vendors)\./', $routeName) === 1) {
                    return 'asset_vendors';
                }
                if (str_starts_with($routeName, 'admin.careers.') || str_starts_with($routeName, 'admin.career_candidates.')) {
                    return 'career';
                }
                if (str_starts_with($routeName, 'admin.certificates.')) {
                    return 'certificate';
                }
                if (str_starts_with($routeName, 'admin.website_products.')) {
                    return 'website_products';
                }
                if (str_starts_with($routeName, 'admin.website_settings.')) {
                    return 'website_settings';
                }
                if (str_starts_with($routeName, 'admin.website_contact_page.')) {
                    return 'website_contact_page';
                }
                if (str_starts_with($routeName, 'admin.website_home_sections.')) {
                    return 'website_home_sections';
                }
                if (preg_match('/^admin\.users\./', $routeName) === 1 || $routeName === 'admin.users') {
                    return 'settings_users';
                }
                if ($routeName === 'admin.users.history.delete') {
                    return 'settings_history_user';
                }
                if ($routeName === 'admin.assets.historyDelete') {
                    return 'settings_history_asset';
                }
            }

            return null;
        };

        $resolvePermissionKey = function (array $item) use ($inferPermissionKey): ?string {
            return $item['permission_key'] ?? $inferPermissionKey($item);
        };
    @endphp
    <nav class="pe-app-sidebar-menu nav nav-pills" data-simplebar id="sidebar-simplebar">
        <ul class="pe-main-menu list-unstyled">
            @if($roleId && isset($menus[$roleId]))
                @foreach($menus[$roleId] as $group)
                    @php
                        $filteredItems = array_values(array_filter($group['items'], function ($item) use ($isMenuAllowed, $resolvePermissionKey) {
                            if (isset($item['children']) && is_array($item['children'])) {
                                // If a parent menu explicitly declares a permission key (group access), enforce it.
                                if (!empty($item['permission_key']) && !$isMenuAllowed($resolvePermissionKey($item))) {
                                    return false;
                                }
                                foreach ($item['children'] as $child) {
                                    if ($isMenuAllowed($resolvePermissionKey($child))) {
                                        return true;
                                    }
                                }
                                return false;
                            }
                            return $isMenuAllowed($resolvePermissionKey($item));
                        }));
                    @endphp

                    @if(empty($filteredItems))
                        @continue
                    @endif

                    <li class="pe-menu-title">{{ $tGroup($group['title_group']) }}</li>
                    @foreach($filteredItems as $itemIndex => $item)
                        @php
                            $collapseId = 'collapseMenu_' . preg_replace('/[^a-zA-Z0-9]/', '', $group['title_group']) . '_' . $itemIndex;
                        @endphp
                        @if(isset($item['children']))
                            @php
                                $currentRoute = Route::currentRouteName();

                                $childrenFiltered = array_values(array_filter($item['children'], function ($child) use ($isMenuAllowed, $resolvePermissionKey) {
                                    return $isMenuAllowed($resolvePermissionKey($child));
                                }));

                                if (empty($childrenFiltered)) {
                                    continue;
                                }

                                $childMatches = function ($child) use ($currentRoute) {
                                    $childRoute = $child['route'] ?? null;
                                    $childActiveRoutes = (array) ($child['active_routes'] ?? []);

                                    // Special-case: Assets index with location query should highlight the
                                    // dedicated location submenu (Jababeka/Karawang), not the generic "Data Asset".
                                    $childParams = $child['params'] ?? [];
                                    $location = request()->query('location');
                                    if (
                                        $childRoute === 'admin.assets.index'
                                        && empty($childParams)
                                        && in_array($location, ['Jababeka', 'Karawang'], true)
                                    ) {
                                        return false;
                                    }

                                    $routeMatched = ($currentRoute === $childRoute);
                                    if (!$routeMatched && !empty($childActiveRoutes)) {
                                        $routeMatched = in_array($currentRoute, $childActiveRoutes, true);
                                    }
                                    if (!$routeMatched) {
                                        return false;
                                    }

                                    if (empty($childParams)) {
                                        return true;
                                    }
                                    foreach ($childParams as $key => $value) {
                                        if (request()->query($key) != $value) {
                                            return false;
                                        }
                                    }
                                    return true;
                                };

                                $isAnyChildActive = false;
                                $hasExactChildMatch = false;
                                foreach ($childrenFiltered as $child) {
                                    if ($childMatches($child)) {
                                        $isAnyChildActive = true;
                                        $hasExactChildMatch = true;
                                        break;
                                    }
                                }
                                if (!$isAnyChildActive && !empty($item['active_routes']) && in_array($currentRoute, (array) $item['active_routes'], true)) {
                                    $isAnyChildActive = true;
                                }
                            @endphp
                            <li class="pe-slide pe-has-sub{{ $isAnyChildActive ? ' active' : '' }}">
                                <a href="#{{ $collapseId }}" class="pe-nav-link" data-bs-toggle="collapse"
                                    aria-expanded="{{ $isAnyChildActive ? 'true' : 'false' }}" aria-controls="{{ $collapseId }}">
                                    <i class="{{ $item['icon'] }} pe-nav-icon"></i>
                                    <span class="pe-nav-content">{{ $tMenu($item['title'] ?? '') }}</span>
                                    <i class="ri-arrow-down-s-line pe-nav-arrow"></i>
                                </a>
                                <ul class="pe-slide-menu collapse{{ $isAnyChildActive ? ' show' : '' }}" id="{{ $collapseId }}">
                                    @php
                                        $alreadyMarkedActive = false;
                                    @endphp
                                    @foreach($childrenFiltered as $child)
                                        @php
                                            $childParams = $child['params'] ?? [];
                                            $isActive = false;
                                            if (!$alreadyMarkedActive && $childMatches($child)) {
                                                $isActive = true;
                                                $alreadyMarkedActive = true;
                                            }

                                            if (!$alreadyMarkedActive && $isAnyChildActive && !$hasExactChildMatch && !empty($child['default'])) {
                                                $isActive = true;
                                                $alreadyMarkedActive = true;
                                            }
                                        @endphp
                                        <li class="pe-slide-item">
                                            <a href="{{ route($child['route'], $childParams) }}"
                                                class="pe-nav-link{{ $isActive ? ' active' : '' }}">
                                                {{ $tMenu($child['title'] ?? '') }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </li>
                        @else
                            @php
                                $currentRoute = Route::currentRouteName();
                                $isActive = false;
                                if (!empty($item['active_routes']) && in_array($currentRoute, (array) $item['active_routes'], true)) {
                                    $isActive = true;
                                }
                                if (!$isActive && !empty($item['route']) && $currentRoute === $item['route']) {
                                    $isActive = true;
                                }

                                // Optional query-param match for leaf items
                                if ($isActive && !empty($item['params']) && is_array($item['params'])) {
                                    foreach ($item['params'] as $key => $value) {
                                        if (request()->query($key) != $value) {
                                            $isActive = false;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <li class="pe-slide">
                                <a href="{{ isset($item['route']) ? route($item['route'], $item['params'] ?? []) : '#' }}"
                                    class="pe-nav-link{{ $isActive ? ' active' : '' }}">
                                    <i class="{{ $item['icon'] }} pe-nav-icon"></i>
                                    <span class="pe-nav-content">{{ $tMenu($item['title'] ?? '') }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endforeach
            @endif
        </ul>
    </nav>
</aside>