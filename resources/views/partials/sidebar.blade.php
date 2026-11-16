<aside class="pe-app-sidebar{{ auth()->check() ? ' has-profile' : '' }}" id="sidebar">
    @php
        $sidebarHomeUrl = route('home');
        $sidebarLocale = app()->getLocale();
        $sidebarLanguageOptions = [
            'en' => __('settings.languages.en'),
            'id' => __('settings.languages.id'),
            'ko' => __('settings.languages.ko'),
        ];
        if (auth()->check()) {
            $isUserRole = (auth()->user()->role?->role_name ?? null) === 'Users' || (int) auth()->user()->role_id === 3;
            $sidebarHomeUrl = $isUserRole ? route('user.dashboard') : route('admin.dashboard');
        }
    @endphp

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
                'Menu' => __('menu.group.menu'),
                'Operasional' => __('menu.group.operations'),
                'Master Data' => __('menu.master_data'),
                'Website' => __('menu.group.website'),
                'Settings' => __('menu.group.settings'),
                'Web Pages' => __('menu.group.web_pages'),
                'Settings & UI' => __('menu.group.settings_ui'),
            ];

            return $map[$text] ?? (string) $text;
        };

        $tMenu = function (?string $text): string {
            $map = [
                'Dashboard' => __('menu.dashboard'),
                'Operasional' => __('menu.group.operations'),
                'Master Data' => __('menu.master_data'),
                'Website' => __('menu.group.website'),
                'Settings' => __('menu.group.settings'),

                'Perlengkapan Aset' => __('menu.assets_equipment'),
                'Data Asset' => __('menu.assets.data_asset'),
                'Data Akun' => __('menu.assets.data_account'),
                'Archived Berkas' => __('menu.assets.archived_documents'),
                'Aset Jababeka' => __('menu.assets.jababeka'),
                'Aset Karawang' => __('menu.assets.karawang'),
                'Aset Masuk' => __('menu.assets.in'),
                'Aset Keluar' => __('menu.assets.out'),

                'Master Karyawan' => __('menu.employees_master'),
                'Manajemen Karyawan' => __('menu.employees_master'),
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
            1 => [
                // Super Admin
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
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stamps.transactions.index',
                                    'active_routes' => [
                                        'admin.stamps.transactions.datatable',
                                        'admin.stamps.transactions.store_in',
                                        'admin.stamps.transactions.store_out',
                                        'admin.stamps.report.pdf',
                                    ],
                                    'default' => true,
                                    'permission_key' => 'stamps_transactions',
                                ],
                                [
                                    'title' => 'Permintaan',
                                    'route' => 'admin.stamps.requests.index',
                                    'active_routes' => ['admin.stamps.requests.store'],
                                    'permission_key' => 'stamps_requests',
                                ],
                                [
                                    'title' => 'Validasi',
                                    'route' => 'admin.stamps.validation.index',
                                    'active_routes' => [
                                        'admin.stamps.validation.approve',
                                        'admin.stamps.validation.reject',
                                        'admin.stamps.validation.handover',
                                    ],
                                    'permission_key' => 'stamps_validation',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stamps.master.index',
                                    'active_routes' => [
                                        'admin.stamps.master.datatable',
                                        'admin.stamps.master.create',
                                        'admin.stamps.master.store',
                                        'admin.stamps.master.edit',
                                        'admin.stamps.master.update',
                                        'admin.stamps.master.toggle',
                                    ],
                                    'permission_key' => 'stamps_master',
                                ],
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
                                'admin.uniforms.stock.lots.index',
                            ],
                            'children' => [
                                [
                                    'title' => 'Master Uniform',
                                    'route' => 'admin.uniforms.master.index',
                                    'active_routes' => [
                                        'admin.uniforms.master.datatable',
                                        'admin.uniforms.master.json',
                                        'admin.uniforms.master.store',
                                        'admin.uniforms.master.update',
                                        'admin.uniforms.master.toggle',
                                    ],
                                    'permission_key' => 'uniforms_master',
                                ],
                                [
                                    'title' => 'Master Varian',
                                    'route' => 'admin.uniforms.variants.index',
                                    'active_routes' => [
                                        'admin.uniforms.variants.datatable',
                                        'admin.uniforms.variants.json',
                                        'admin.uniforms.variants.store',
                                        'admin.uniforms.variants.update',
                                        'admin.uniforms.variants.toggle',
                                    ],
                                    'permission_key' => 'uniforms_variants',
                                ],
                                [
                                    'title' => 'Master LOT',
                                    'route' => 'admin.uniforms.lots.index',
                                    'active_routes' => [
                                        'admin.uniforms.lots.datatable',
                                        'admin.uniforms.lots.json',
                                        'admin.uniforms.lots.store',
                                        'admin.uniforms.lots.update',
                                    ],
                                    'permission_key' => 'uniforms_lots',
                                ],
                                [
                                    'title' => 'Kuota Seragam',
                                    'route' => 'admin.uniforms.entitlements.index',
                                    'active_routes' => [
                                        'admin.uniforms.entitlements.datatable',
                                        'admin.uniforms.entitlements.json',
                                        'admin.uniforms.entitlements.store',
                                        'admin.uniforms.entitlements.update',
                                    ],
                                    'permission_key' => 'uniforms_entitlements',
                                ],
                                [
                                    'title' => 'Stok',
                                    'route' => 'admin.uniforms.stock.index',
                                    'active_routes' => ['admin.uniforms.stock.datatable', 'admin.uniforms.stock.in'],
                                    'default' => true,
                                    'permission_key' => 'uniforms_stock',
                                ],
                                [
                                    'title' => 'Distribusi',
                                    'route' => 'admin.uniforms.distributions.index',
                                    'active_routes' => [
                                        'admin.uniforms.distributions.datatable',
                                        'admin.uniforms.distributions.store',
                                    ],
                                    'permission_key' => 'uniforms_distribution',
                                ],
                                [
                                    'title' => 'Pivot Stok',
                                    'route' => 'admin.uniforms.reports.pivot.index',
                                    'active_routes' => ['admin.uniforms.reports.pivot.datatable'],
                                    'permission_key' => 'uniforms_reports',
                                ],
                                [
                                    'title' => 'Stok per LOT',
                                    'route' => 'admin.uniforms.stock.lots.index',
                                    'active_routes' => ['admin.uniforms.stock.lots.datatable'],
                                    'permission_key' => 'uniforms_stock',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Manajemen Stok',
                            'icon' => 'fas fa-boxes-stacked',
                            'permission_key' => 'stock',
                            'active_routes' => [
                                'admin.stock.master.index',
                                'admin.stock.master.datatable',
                                'admin.stock.master.json',
                                'admin.stock.master.store',
                                'admin.stock.master.update',
                                'admin.stock.master.toggle',
                                'admin.stock.master.destroy',
                                'admin.stock.restock.index',
                                'admin.stock.restock.store',
                                'admin.stock.request.index',
                                'admin.stock.request.store',
                                'admin.stock.ledger.index',
                                'admin.stock.ledger.datatable',
                            ],
                            'children' => [
                                [
                                    'title' => 'ATK',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => [
                                        'admin.stock.master.datatable',
                                        'admin.stock.master.json',
                                        'admin.stock.master.store',
                                        'admin.stock.master.update',
                                        'admin.stock.master.toggle',
                                        'admin.stock.master.destroy',
                                    ],
                                    'default' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => ['admin.stock.restock.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => ['admin.stock.request.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => ['admin.stock.ledger.datatable'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => [
                                        'admin.stock.transfer.store',
                                        'admin.stock.transfer.prepare',
                                        'admin.stock.transfer.ship',
                                        'admin.stock.transfer.receive',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Material',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => [
                                        'admin.stock.master.datatable',
                                        'admin.stock.master.json',
                                        'admin.stock.master.store',
                                        'admin.stock.master.update',
                                        'admin.stock.master.toggle',
                                        'admin.stock.master.destroy',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => ['admin.stock.restock.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => ['admin.stock.request.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => ['admin.stock.ledger.datatable'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => [
                                        'admin.stock.transfer.store',
                                        'admin.stock.transfer.prepare',
                                        'admin.stock.transfer.ship',
                                        'admin.stock.transfer.receive',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'APD',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => [
                                        'admin.stock.master.datatable',
                                        'admin.stock.master.json',
                                        'admin.stock.master.store',
                                        'admin.stock.master.update',
                                        'admin.stock.master.toggle',
                                        'admin.stock.master.destroy',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => ['admin.stock.restock.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => ['admin.stock.request.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => ['admin.stock.ledger.datatable'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => [
                                        'admin.stock.transfer.store',
                                        'admin.stock.transfer.prepare',
                                        'admin.stock.transfer.ship',
                                        'admin.stock.transfer.receive',
                                    ],
                                    'permission_key' => 'stock',
                                ],
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
                                [
                                    'title' => 'Data Asset',
                                    'route' => 'admin.assets.index',
                                    'active_routes' => ['admin.assets.datatable'],
                                    'default' => true,
                                    'permission_key' => 'assets_data',
                                ],
                                [
                                    'title' => 'Data Akun',
                                    'route' => 'admin.accounts.index',
                                    'active_routes' => [
                                        'admin.accounts.show',
                                        'admin.accounts.json',
                                        'admin.accounts.endpoints.open',
                                        'admin.accounts.store',
                                        'admin.accounts.update',
                                        'admin.accounts.destroy',
                                        'admin.accounts.verify',
                                    ],
                                    'permission_key' => 'accounts_data',
                                ],
                                [
                                    'title' => 'Archived Berkas',
                                    'route' => 'admin.documents.index',
                                    'active_routes' => [
                                        'admin.documents.dashboard',
                                        'admin.documents.show',
                                        'admin.documents.create',
                                        'admin.documents.store',
                                        'admin.documents.update',
                                        'admin.documents.destroy',
                                        'admin.documents.restore',
                                        'admin.documents.files.download',
                                        'admin.documents.files.upload',
                                    ],
                                    'permission_key' => 'documents_archive',
                                ],
                                [
                                    'title' => 'Aset Jababeka',
                                    'route' => 'admin.assets.jababeka',
                                    'active_routes' => ['admin.assets.index'],
                                    'params' => ['location' => 'Jababeka'],
                                    'permission_key' => 'assets_jababeka',
                                ],
                                [
                                    'title' => 'Aset Karawang',
                                    'route' => 'admin.assets.karawang',
                                    'active_routes' => ['admin.assets.index'],
                                    'params' => ['location' => 'Karawang'],
                                    'permission_key' => 'assets_karawang',
                                ],
                                [
                                    'title' => 'Aset Masuk',
                                    'route' => 'admin.assets.in',
                                    'active_routes' => ['admin.assets.in.scan'],
                                    'permission_key' => 'assets_in',
                                ],
                                [
                                    'title' => 'Aset Keluar',
                                    'route' => 'admin.assets.transfer',
                                    'active_routes' => ['admin.assets.transfer.list'],
                                    'permission_key' => 'assets_transfer',
                                ],
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
                            'title' => 'Notes',
                            'icon' => 'fas fa-sticky-note',
                            'route' => 'admin.notes.index',
                            'permission_key' => 'notes',
                            'active_routes' => [
                                'admin.notes.index',
                                'admin.notes.create',
                                'admin.notes.store',
                                'admin.notes.show',
                                'admin.notes.edit',
                                'admin.notes.update',
                                'admin.notes.destroy',
                            ],
                        ],
                        [
                            'title' => 'Rekrutmen',
                            'icon' => 'fas fa-clipboard-list',
                            'route' => 'admin.recruitment.forms.index',
                            'permission_key' => 'recruitment',
                            'active_routes' => [
                                'admin.recruitment.forms.index',
                                'admin.recruitment.forms.datatable',
                                'admin.recruitment.forms.store',
                                'admin.recruitment.forms.show',
                                'admin.recruitment.forms.update',
                                'admin.recruitment.forms.questions.datatable',
                                'admin.recruitment.questions.store',
                                'admin.recruitment.questions.update',
                                'admin.recruitment.questions.destroy',
                                'admin.recruitment.candidates.index',
                                'admin.recruitment.candidates.datatable',
                                'admin.recruitment.candidates.show',
                                'admin.recruitment.candidates.scores.update',
                                'admin.recruitment.candidates.export.pdf',
                                'admin.recruitment.candidates.export.excel',
                                'admin.recruitment.candidates.files.download',
                            ],
                        ],
                        [
                            'title' => 'Manajemen Stok',
                            'icon' => 'fas fa-boxes-stacked',
                            'permission_key' => 'stock',
                            'active_routes' => [
                                'admin.stock.master.index',
                                'admin.stock.master.datatable',
                                'admin.stock.master.json',
                                'admin.stock.master.store',
                                'admin.stock.master.update',
                                'admin.stock.master.toggle',
                                'admin.stock.master.destroy',
                                'admin.stock.restock.index',
                                'admin.stock.restock.store',
                                'admin.stock.request.index',
                                'admin.stock.request.store',
                                'admin.stock.ledger.index',
                                'admin.stock.ledger.datatable',
                            ],
                            'children' => [
                                [
                                    'title' => 'ATK',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => [
                                        'admin.stock.master.datatable',
                                        'admin.stock.master.json',
                                        'admin.stock.master.store',
                                        'admin.stock.master.update',
                                        'admin.stock.master.toggle',
                                        'admin.stock.master.destroy',
                                    ],
                                    'default' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => ['admin.stock.restock.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => ['admin.stock.request.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => ['admin.stock.ledger.datatable'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'atk'],
                                    'active_routes' => [
                                        'admin.stock.transfer.store',
                                        'admin.stock.transfer.prepare',
                                        'admin.stock.transfer.ship',
                                        'admin.stock.transfer.receive',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Material',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => [
                                        'admin.stock.master.datatable',
                                        'admin.stock.master.json',
                                        'admin.stock.master.store',
                                        'admin.stock.master.update',
                                        'admin.stock.master.toggle',
                                        'admin.stock.master.destroy',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => ['admin.stock.restock.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => ['admin.stock.request.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => ['admin.stock.ledger.datatable'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'material'],
                                    'active_routes' => [
                                        'admin.stock.transfer.store',
                                        'admin.stock.transfer.prepare',
                                        'admin.stock.transfer.ship',
                                        'admin.stock.transfer.receive',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'APD',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => [
                                        'admin.stock.master.datatable',
                                        'admin.stock.master.json',
                                        'admin.stock.master.store',
                                        'admin.stock.master.update',
                                        'admin.stock.master.toggle',
                                        'admin.stock.master.destroy',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => ['admin.stock.restock.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => ['admin.stock.request.store'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => ['admin.stock.ledger.datatable'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'apd'],
                                    'active_routes' => [
                                        'admin.stock.transfer.store',
                                        'admin.stock.transfer.prepare',
                                        'admin.stock.transfer.ship',
                                        'admin.stock.transfer.receive',
                                    ],
                                    'permission_key' => 'stock',
                                ],
                            ],
                        ],
                        [
                            'title' => __('menu.employees_master'),
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
                                [
                                    'title' => 'Employees',
                                    'route' => 'admin.employees.index',
                                    'default' => true,
                                    'permission_key' => 'employees_index',
                                ],
                                [
                                    'title' => 'Deleted',
                                    'route' => 'admin.employees.deleted',
                                    'permission_key' => 'employees_deleted',
                                ],
                                [
                                    'title' => 'Audit Log',
                                    'route' => 'admin.employees.audit',
                                    'permission_key' => 'employees_audit',
                                ],
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
                                [
                                    'title' => 'Departments',
                                    'route' => 'admin.departments.index',
                                    'default' => true,
                                    'permission_key' => 'departments',
                                ],
                                [
                                    'title' => 'Positions',
                                    'route' => 'admin.positions.index',
                                    'permission_key' => 'positions',
                                ],
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
                                [
                                    'title' => 'Kategori Asset',
                                    'route' => 'admin.asset_categories.index',
                                    'default' => true,
                                    'permission_key' => 'asset_categories',
                                ],
                                [
                                    'title' => 'Lokasi Asset',
                                    'route' => 'admin.asset_locations.index',
                                    'permission_key' => 'asset_locations',
                                ],
                                [
                                    'title' => 'Plant/Site',
                                    'route' => 'admin.plant_sites.index',
                                    'permission_key' => 'plant_sites',
                                ],
                                [
                                    'title' => 'Satuan Asset',
                                    'route' => 'admin.asset_uoms.index',
                                    'permission_key' => 'asset_uoms',
                                ],
                                [
                                    'title' => 'Vendor Asset',
                                    'route' => 'admin.asset_vendors.index',
                                    'permission_key' => 'asset_vendors',
                                ],
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
                                [
                                    'title' => 'Kategori Akun',
                                    'route' => 'admin.account_types.index',
                                    'default' => true,
                                    'permission_key' => 'account_types',
                                ],
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
                                [
                                    'title' => 'Task Types',
                                    'route' => 'admin.daily_task_types.index',
                                    'default' => true,
                                    'permission_key' => 'daily_task_types',
                                ],
                                [
                                    'title' => 'Task Priorities',
                                    'route' => 'admin.daily_task_priorities.index',
                                    'permission_key' => 'daily_task_priorities',
                                ],
                                [
                                    'title' => 'Task Statuses',
                                    'route' => 'admin.daily_task_statuses.index',
                                    'permission_key' => 'daily_task_statuses',
                                ],
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
                            'title' => 'Career',
                            'icon' => 'fas fa-briefcase',
                            'permission_key' => 'career',
                            'active_routes' => [
                                'admin.careers.index',
                                'admin.careers.company.update',
                                'admin.careers.store',
                                'admin.careers.update',
                                'admin.careers.destroy',
                                'admin.career_candidates.index',
                                'admin.career_candidates.screening_cv',
                                'admin.career_candidates.psychology_test',
                                'admin.career_candidates.hrd_online_interview',
                                'admin.career_candidates.user_site_interview',
                                'admin.career_candidates.offering_letter',
                                'admin.career_candidates.datatable',
                                'admin.career_candidates.talent_pool',
                                'admin.career_candidates.talent_pool.datatable',
                                'admin.career_candidates.failed',
                                'admin.career_candidates.failed.datatable',
                                'admin.career_candidates.failed.export',
                                'admin.career_candidates.advance',
                                'admin.career_candidates.talent_pool.store',
                                'admin.career_candidates.cv.view',
                                'admin.career_candidates.cv',
                            ],
                            'children' => [
                                [
                                    'title' => 'Job Openings',
                                    'route' => 'admin.careers.index',
                                    'default' => true,
                                ],
                                [
                                    'title' => 'Screening CV',
                                    'route' => 'admin.career_candidates.index',
                                    'active_routes' => ['admin.career_candidates.screening_cv'],
                                ],
                                [
                                    'title' => 'Test Psikotes',
                                    'route' => 'admin.career_candidates.psychology_test',
                                ],
                                [
                                    'title' => 'Interview HRD Online',
                                    'route' => 'admin.career_candidates.hrd_online_interview',
                                ],
                                [
                                    'title' => 'Interview User',
                                    'route' => 'admin.career_candidates.user_site_interview',
                                ],
                                [
                                    'title' => 'Offering Letter',
                                    'route' => 'admin.career_candidates.offering_letter',
                                ],
                                [
                                    'title' => 'Talent Pool',
                                    'route' => 'admin.career_candidates.talent_pool',
                                ],
                                [
                                    'title' => 'Kandidat Gagal',
                                    'route' => 'admin.career_candidates.failed',
                                ],
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
                            'title' => 'Colorants Catalog',
                            'icon' => 'fas fa-flask',
                            'route' => 'admin.colorants.index',
                            'permission_key' => 'website_products',
                            'active_routes' => [
                                'admin.colorants.index',
                                'admin.colorants.create',
                                'admin.colorants.store',
                                'admin.colorants.edit',
                                'admin.colorants.update',
                                'admin.colorants.destroy',
                            ],
                        ],
                        [
                            'title' => 'Website Settings',
                            'icon' => 'fas fa-sliders',
                            'route' => 'admin.website_settings.edit',
                            'permission_key' => 'website_settings',
                            'active_routes' => ['admin.website_settings.edit', 'admin.website_settings.update'],
                        ],
                        [
                            'title' => 'Website Contact Page',
                            'icon' => 'fas fa-address-book',
                            'route' => 'admin.website_contact_page.edit',
                            'permission_key' => 'website_contact_page',
                            'active_routes' => ['admin.website_contact_page.edit', 'admin.website_contact_page.update'],
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
                                [
                                    'title' => 'History Delete User',
                                    'route' => 'admin.users.history.delete',
                                    'permission_key' => 'settings_history_user',
                                ],
                                [
                                    'title' => 'History Delete Asset',
                                    'route' => 'admin.assets.historyDelete',
                                    'permission_key' => 'settings_history_asset',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            2 => [
                // Admin
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
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stamps.transactions.index',
                                    'active_routes' => [
                                        'admin.stamps.transactions.datatable',
                                        'admin.stamps.transactions.store_in',
                                        'admin.stamps.transactions.store_out',
                                        'admin.stamps.report.pdf',
                                    ],
                                    'default' => true,
                                    'permission_key' => 'stamps_transactions',
                                ],
                                [
                                    'title' => 'Permintaan',
                                    'route' => 'admin.stamps.requests.index',
                                    'active_routes' => ['admin.stamps.requests.store'],
                                    'permission_key' => 'stamps_requests',
                                ],
                                [
                                    'title' => 'Validasi',
                                    'route' => 'admin.stamps.validation.index',
                                    'active_routes' => [
                                        'admin.stamps.validation.approve',
                                        'admin.stamps.validation.reject',
                                        'admin.stamps.validation.handover',
                                    ],
                                    'permission_key' => 'stamps_validation',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stamps.master.index',
                                    'active_routes' => [
                                        'admin.stamps.master.datatable',
                                        'admin.stamps.master.create',
                                        'admin.stamps.master.store',
                                        'admin.stamps.master.edit',
                                        'admin.stamps.master.update',
                                        'admin.stamps.master.toggle',
                                    ],
                                    'permission_key' => 'stamps_master',
                                ],
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
                                'admin.uniforms.stock.lots.index',
                            ],
                            'children' => [
                                [
                                    'title' => 'Master Uniform',
                                    'route' => 'admin.uniforms.master.index',
                                    'active_routes' => [
                                        'admin.uniforms.master.datatable',
                                        'admin.uniforms.master.json',
                                        'admin.uniforms.master.store',
                                        'admin.uniforms.master.update',
                                        'admin.uniforms.master.toggle',
                                    ],
                                    'permission_key' => 'uniforms_master',
                                ],
                                [
                                    'title' => 'Master Varian',
                                    'route' => 'admin.uniforms.variants.index',
                                    'active_routes' => [
                                        'admin.uniforms.variants.datatable',
                                        'admin.uniforms.variants.json',
                                        'admin.uniforms.variants.store',
                                        'admin.uniforms.variants.update',
                                        'admin.uniforms.variants.toggle',
                                    ],
                                    'permission_key' => 'uniforms_variants',
                                ],
                                [
                                    'title' => 'Master LOT',
                                    'route' => 'admin.uniforms.lots.index',
                                    'active_routes' => [
                                        'admin.uniforms.lots.datatable',
                                        'admin.uniforms.lots.json',
                                        'admin.uniforms.lots.store',
                                        'admin.uniforms.lots.update',
                                    ],
                                    'permission_key' => 'uniforms_lots',
                                ],
                                [
                                    'title' => 'Kuota Seragam',
                                    'route' => 'admin.uniforms.entitlements.index',
                                    'active_routes' => [
                                        'admin.uniforms.entitlements.datatable',
                                        'admin.uniforms.entitlements.json',
                                        'admin.uniforms.entitlements.store',
                                        'admin.uniforms.entitlements.update',
                                    ],
                                    'permission_key' => 'uniforms_entitlements',
                                ],
                                [
                                    'title' => 'Stok',
                                    'route' => 'admin.uniforms.stock.index',
                                    'active_routes' => ['admin.uniforms.stock.datatable', 'admin.uniforms.stock.in'],
                                    'default' => true,
                                    'permission_key' => 'uniforms_stock',
                                ],
                                [
                                    'title' => 'Distribusi',
                                    'route' => 'admin.uniforms.distributions.index',
                                    'active_routes' => [
                                        'admin.uniforms.distributions.datatable',
                                        'admin.uniforms.distributions.store',
                                    ],
                                    'permission_key' => 'uniforms_distribution',
                                ],
                                [
                                    'title' => 'Pivot Stok',
                                    'route' => 'admin.uniforms.reports.pivot.index',
                                    'active_routes' => ['admin.uniforms.reports.pivot.datatable'],
                                    'permission_key' => 'uniforms_reports',
                                ],
                                [
                                    'title' => 'Stok per LOT',
                                    'route' => 'admin.uniforms.stock.lots.index',
                                    'active_routes' => ['admin.uniforms.stock.lots.datatable'],
                                    'permission_key' => 'uniforms_stock',
                                ],
                            ],
                        ],
                        [
                            'title' => __('menu.employees_master'),
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
                                [
                                    'title' => 'Employees',
                                    'route' => 'admin.employees.index',
                                    'default' => true,
                                    'permission_key' => 'employees_index',
                                ],
                                [
                                    'title' => 'Deleted',
                                    'route' => 'admin.employees.deleted',
                                    'permission_key' => 'employees_deleted',
                                ],
                                [
                                    'title' => 'Audit Log',
                                    'route' => 'admin.employees.audit',
                                    'permission_key' => 'employees_audit',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Notes',
                            'icon' => 'fas fa-sticky-note',
                            'route' => 'admin.notes.index',
                            'permission_key' => 'notes',
                            'active_routes' => [
                                'admin.notes.index',
                                'admin.notes.create',
                                'admin.notes.store',
                                'admin.notes.show',
                                'admin.notes.edit',
                                'admin.notes.update',
                                'admin.notes.destroy',
                            ],
                        ],
                        [
                            'title' => 'Rekrutmen',
                            'icon' => 'fas fa-clipboard-list',
                            'route' => 'admin.recruitment.forms.index',
                            'permission_key' => 'recruitment',
                            'active_routes' => [
                                'admin.recruitment.forms.index',
                                'admin.recruitment.forms.datatable',
                                'admin.recruitment.forms.store',
                                'admin.recruitment.forms.show',
                                'admin.recruitment.forms.update',
                                'admin.recruitment.forms.questions.datatable',
                                'admin.recruitment.questions.store',
                                'admin.recruitment.questions.update',
                                'admin.recruitment.questions.destroy',
                                'admin.recruitment.candidates.index',
                                'admin.recruitment.candidates.datatable',
                                'admin.recruitment.candidates.show',
                                'admin.recruitment.candidates.scores.update',
                                'admin.recruitment.candidates.export.pdf',
                                'admin.recruitment.candidates.export.excel',
                                'admin.recruitment.candidates.files.download',
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
                                [
                                    'title' => 'Departments',
                                    'route' => 'admin.departments.index',
                                    'default' => true,
                                    'permission_key' => 'departments',
                                ],
                                [
                                    'title' => 'Positions',
                                    'route' => 'admin.positions.index',
                                    'permission_key' => 'positions',
                                ],
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
                                [
                                    'title' => 'Kategori Asset',
                                    'route' => 'admin.asset_categories.index',
                                    'default' => true,
                                    'permission_key' => 'asset_categories',
                                ],
                                [
                                    'title' => 'Lokasi Asset',
                                    'route' => 'admin.asset_locations.index',
                                    'permission_key' => 'asset_locations',
                                ],
                                [
                                    'title' => 'Plant/Site',
                                    'route' => 'admin.plant_sites.index',
                                    'permission_key' => 'plant_sites',
                                ],
                                [
                                    'title' => 'Satuan Asset',
                                    'route' => 'admin.asset_uoms.index',
                                    'permission_key' => 'asset_uoms',
                                ],
                                [
                                    'title' => 'Vendor Asset',
                                    'route' => 'admin.asset_vendors.index',
                                    'permission_key' => 'asset_vendors',
                                ],
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
                                [
                                    'title' => 'Kategori Akun',
                                    'route' => 'admin.account_types.index',
                                    'default' => true,
                                    'permission_key' => 'account_types',
                                ],
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
                                [
                                    'title' => 'Task Types',
                                    'route' => 'admin.daily_task_types.index',
                                    'default' => true,
                                    'permission_key' => 'daily_task_types',
                                ],
                                [
                                    'title' => 'Task Priorities',
                                    'route' => 'admin.daily_task_priorities.index',
                                    'permission_key' => 'daily_task_priorities',
                                ],
                                [
                                    'title' => 'Task Statuses',
                                    'route' => 'admin.daily_task_statuses.index',
                                    'permission_key' => 'daily_task_statuses',
                                ],
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
                            'title' => 'Career',
                            'icon' => 'fas fa-briefcase',
                            'permission_key' => 'career',
                            'active_routes' => [
                                'admin.careers.index',
                                'admin.careers.company.update',
                                'admin.careers.store',
                                'admin.careers.update',
                                'admin.careers.destroy',
                                'admin.career_candidates.index',
                                'admin.career_candidates.screening_cv',
                                'admin.career_candidates.psychology_test',
                                'admin.career_candidates.hrd_online_interview',
                                'admin.career_candidates.user_site_interview',
                                'admin.career_candidates.offering_letter',
                                'admin.career_candidates.datatable',
                                'admin.career_candidates.talent_pool',
                                'admin.career_candidates.talent_pool.datatable',
                                'admin.career_candidates.failed',
                                'admin.career_candidates.failed.datatable',
                                'admin.career_candidates.failed.export',
                                'admin.career_candidates.advance',
                                'admin.career_candidates.talent_pool.store',
                                'admin.career_candidates.cv.view',
                                'admin.career_candidates.cv',
                            ],
                            'children' => [
                                [
                                    'title' => 'Job Openings',
                                    'route' => 'admin.careers.index',
                                    'default' => true,
                                ],
                                [
                                    'title' => 'Screening CV',
                                    'route' => 'admin.career_candidates.index',
                                    'active_routes' => ['admin.career_candidates.screening_cv'],
                                ],
                                [
                                    'title' => 'Test Psikotes',
                                    'route' => 'admin.career_candidates.psychology_test',
                                ],
                                [
                                    'title' => 'Interview HRD Online',
                                    'route' => 'admin.career_candidates.hrd_online_interview',
                                ],
                                [
                                    'title' => 'Interview User',
                                    'route' => 'admin.career_candidates.user_site_interview',
                                ],
                                [
                                    'title' => 'Offering Letter',
                                    'route' => 'admin.career_candidates.offering_letter',
                                ],
                                [
                                    'title' => 'Talent Pool',
                                    'route' => 'admin.career_candidates.talent_pool',
                                ],
                                [
                                    'title' => 'Kandidat Gagal',
                                    'route' => 'admin.career_candidates.failed',
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Application',
                    'items' => [
                        [
                            'title' => 'Manajemen Stok',
                            'icon' => 'fas fa-boxes-stacked',
                            'permission_key' => 'stock',
                            'active_routes' => [
                                'admin.stock.master.index',
                                'admin.stock.restock.index',
                                'admin.stock.request.index',
                                'admin.stock.ledger.index',
                                'admin.stock.transfer.index',
                            ],
                            'children' => [
                                [
                                    'title' => 'ATK',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'atk'],
                                    'default' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'atk'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'atk'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'atk'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'atk'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Material',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'material'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'material'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'material'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'material'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'material'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'APD',
                                    'is_heading' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Master',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'apd'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Stok Masuk',
                                    'route' => 'admin.stock.restock.index',
                                    'params' => ['category' => 'apd'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Pengambilan',
                                    'route' => 'admin.stock.request.index',
                                    'params' => ['category' => 'apd'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Ledger',
                                    'route' => 'admin.stock.ledger.index',
                                    'params' => ['category' => 'apd'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Transfer Site',
                                    'route' => 'admin.stock.transfer.index',
                                    'params' => ['category' => 'apd'],
                                    'permission_key' => 'stock',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            3 => [
                // Users
                [
                    'title_group' => 'Main',
                    'items' => [
                        [
                            'title' => 'Dashboard',
                            'icon' => 'fas fa-gauge-high',
                            'route' => 'user.dashboard',
                            'permission_key' => 'user_dashboard',
                            'active_routes' => ['user.dashboard'],
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
                            'active_routes' => ['admin.stamps.requests.index', 'admin.stamps.requests.store'],
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
                            'title' => 'Rekrutmen',
                            'icon' => 'fas fa-clipboard-list',
                            'route' => 'admin.recruitment.forms.index',
                            'permission_key' => 'recruitment',
                            'active_routes' => [
                                'admin.recruitment.forms.index',
                                'admin.recruitment.forms.datatable',
                                'admin.recruitment.forms.store',
                                'admin.recruitment.forms.show',
                                'admin.recruitment.forms.update',
                                'admin.recruitment.forms.questions.datatable',
                                'admin.recruitment.questions.store',
                                'admin.recruitment.questions.update',
                                'admin.recruitment.questions.destroy',
                                'admin.recruitment.candidates.index',
                                'admin.recruitment.candidates.datatable',
                                'admin.recruitment.candidates.show',
                                'admin.recruitment.candidates.scores.update',
                                'admin.recruitment.candidates.export.pdf',
                                'admin.recruitment.candidates.export.excel',
                                'admin.recruitment.candidates.files.download',
                            ],
                        ],
                        [
                            'title' => 'Manajemen Stok',
                            'icon' => 'fas fa-boxes-stacked',
                            'permission_key' => 'stock',
                            'active_routes' => [
                                'admin.stock.master.index',
                                'admin.stock.restock.index',
                                'admin.stock.request.index',
                                'admin.stock.ledger.index',
                                'admin.stock.transfer.index',
                            ],
                            'children' => [
                                [
                                    'title' => 'ATK',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'atk'],
                                    'default' => true,
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'Material',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'material'],
                                    'permission_key' => 'stock',
                                ],
                                [
                                    'title' => 'APD',
                                    'route' => 'admin.stock.master.index',
                                    'params' => ['category' => 'apd'],
                                    'permission_key' => 'stock',
                                ],
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
                                [
                                    'title' => 'Data Asset',
                                    'route' => 'admin.assets.index',
                                    'active_routes' => ['admin.assets.datatable'],
                                    'default' => true,
                                    'permission_key' => 'assets_data',
                                ],
                                [
                                    'title' => 'Data Akun',
                                    'route' => 'admin.accounts.index',
                                    'active_routes' => [
                                        'admin.accounts.show',
                                        'admin.accounts.json',
                                        'admin.accounts.endpoints.open',
                                        'admin.accounts.store',
                                        'admin.accounts.update',
                                        'admin.accounts.destroy',
                                        'admin.accounts.verify',
                                    ],
                                    'permission_key' => 'accounts_data',
                                ],
                                [
                                    'title' => 'Archived Berkas',
                                    'route' => 'admin.documents.index',
                                    'active_routes' => [
                                        'admin.documents.dashboard',
                                        'admin.documents.show',
                                        'admin.documents.create',
                                        'admin.documents.store',
                                        'admin.documents.update',
                                        'admin.documents.destroy',
                                        'admin.documents.restore',
                                        'admin.documents.files.download',
                                        'admin.documents.files.upload',
                                    ],
                                    'permission_key' => 'documents_archive',
                                ],
                                [
                                    'title' => 'Aset Jababeka',
                                    'route' => 'admin.assets.jababeka',
                                    'active_routes' => ['admin.assets.index'],
                                    'params' => ['location' => 'Jababeka'],
                                    'permission_key' => 'assets_jababeka',
                                ],
                                [
                                    'title' => 'Aset Karawang',
                                    'route' => 'admin.assets.karawang',
                                    'active_routes' => ['admin.assets.index'],
                                    'params' => ['location' => 'Karawang'],
                                    'permission_key' => 'assets_karawang',
                                ],
                                [
                                    'title' => 'Aset Masuk',
                                    'route' => 'admin.assets.in',
                                    'active_routes' => ['admin.assets.in.scan'],
                                    'permission_key' => 'assets_in',
                                ],
                                [
                                    'title' => 'Aset Keluar',
                                    'route' => 'admin.assets.transfer',
                                    'active_routes' => ['admin.assets.transfer.list'],
                                    'permission_key' => 'assets_transfer',
                                ],
                            ],
                        ],
                        [
                            'title' => 'Master Device',
                            'icon' => 'fas fa-desktop',
                            'route' => 'admin.devices.index',
                            'permission_key' => 'devices',
                            'active_routes' => ['admin.devices.index', 'admin.devices.show'],
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

        /*
        |----------------------------------------------------------------------
        | Struktur menu 3 tingkat
        |----------------------------------------------------------------------
        | Tingkat 1: Area kerja (General Affairs, Persediaan, Aset & IT, ...)
        | Tingkat 2: Modul (Materai, Seragam, ATK, Karyawan, ...)
        | Tingkat 3: Halaman (Ledger, Permintaan, Master, ...)
        |
        | Definisi per-role di atas tetap menjadi sumber item & permission key;
        | blok ini hanya menyusun ulang item tersebut ke dalam area kerja.
        */
        $menuSections = [
            'ga' => ['title' => __('menu.section.general_affairs'), 'icon' => 'fas fa-building'],
            'inventory' => ['title' => __('menu.section.inventory'), 'icon' => 'fas fa-boxes-stacked'],
            'assets' => ['title' => __('menu.section.assets_it'), 'icon' => 'fas fa-laptop'],
            'hr' => ['title' => __('menu.section.hr'), 'icon' => 'fas fa-users'],
            'work' => ['title' => __('menu.section.productivity'), 'icon' => 'fas fa-list-check'],
            'website' => ['title' => __('menu.section.website'), 'icon' => 'fas fa-globe'],
            'settings' => ['title' => __('menu.section.settings'), 'icon' => 'fas fa-gear', 'permission_key' => 'settings'],
            'other' => ['title' => __('menu.section.other'), 'icon' => 'fas fa-layer-group'],
        ];
        $sectionByPermissionKey = [
            'stock' => 'inventory',
            'assets' => 'assets',
            'devices' => 'assets',
            'master_assets' => 'assets',
            'master_accounts' => 'assets',
            'employees' => 'hr',
            'master_hr' => 'hr',
            'recruitment' => 'hr',
            'career' => 'hr',
            'daily_tasks' => 'work',
            'notes' => 'work',
            'master_daily_task' => 'work',
            'certificate' => 'website',
            'website_products' => 'website',
            'website_settings' => 'website',
            'website_contact_page' => 'website',
            'website_home_sections' => 'website',
            'stamps_requests' => 'ga',
            'stamps_validation' => 'ga',
            'settings' => 'settings',
        ];
        $sectionByTitle = [
            'Manajemen Materai' => 'ga',
            'Manajemen Seragam' => 'ga',
        ];
        // Urutan modul di dalam area (yang tidak terdaftar mengikuti urutan definisi).
        $moduleOrder = [
            'employees', 'master_hr', 'recruitment', 'career',
            'assets', 'devices', 'master_assets', 'master_accounts',
            'daily_tasks', 'notes', 'master_daily_task',
        ];

        $collectMenuRoutes = function (array $item) use (&$collectMenuRoutes): array {
            $routes = (array) ($item['active_routes'] ?? []);
            if (!empty($item['route'])) {
                $routes[] = $item['route'];
            }

            foreach ((array) ($item['children'] ?? []) as $child) {
                $routes = array_merge($routes, $collectMenuRoutes($child));
            }

            return array_values(array_unique(array_filter($routes, 'is_string')));
        };

        // Daftar datar ber-heading (ATK / Material / APD) -> modul bersarang.
        $nestByHeadings = function (array $children): array {
            $hasHeading = false;
            foreach ($children as $child) {
                if (!empty($child['is_heading'])) {
                    $hasHeading = true;
                    break;
                }
            }
            if (!$hasHeading) {
                return $children;
            }

            $nested = [];
            $current = null;
            foreach ($children as $child) {
                if (!empty($child['is_heading'])) {
                    if ($current !== null && !empty($current['children'])) {
                        $nested[] = $current;
                    }
                    $current = [
                        'title' => $child['title'] ?? '',
                        'permission_key' => $child['permission_key'] ?? null,
                        'children' => [],
                    ];
                    continue;
                }
                if ($current === null) {
                    $nested[] = $child;
                    continue;
                }
                $current['children'][] = $child;
            }
            if ($current !== null && !empty($current['children'])) {
                $nested[] = $current;
            }

            return $nested;
        };

        foreach ($menus as $role => $groups) {
            // 1) Gabungkan item duplikat antar grup lama.
            $flatItems = [];
            foreach ($groups as $group) {
                foreach ($group['items'] as $item) {
                    $itemIdentity = implode('|', [
                        $item['title'] ?? '',
                        $item['permission_key'] ?? '',
                        $item['route'] ?? '',
                    ]);

                    if (!isset($flatItems[$itemIdentity])) {
                        $flatItems[$itemIdentity] = $item;
                        continue;
                    }

                    $existingItem = &$flatItems[$itemIdentity];
                    $existingItem['active_routes'] = array_values(array_unique(array_merge(
                        (array) ($existingItem['active_routes'] ?? []),
                        (array) ($item['active_routes'] ?? []),
                    )));

                    $childIdentity = function (array $child): string {
                        return serialize([
                            $child['title'] ?? null,
                            $child['route'] ?? null,
                            $child['params'] ?? [],
                            $child['is_heading'] ?? false,
                            $child['permission_key'] ?? null,
                        ]);
                    };
                    foreach ((array) ($item['children'] ?? []) as $child) {
                        $matchingChildIndex = null;
                        foreach ((array) ($existingItem['children'] ?? []) as $childIndex => $existingChild) {
                            if ($childIdentity($existingChild) === $childIdentity($child)) {
                                $matchingChildIndex = $childIndex;
                                break;
                            }
                        }

                        if ($matchingChildIndex === null) {
                            $existingItem['children'][] = $child;
                            continue;
                        }

                        $existingItem['children'][$matchingChildIndex]['active_routes'] = array_values(array_unique(array_merge(
                            (array) ($existingItem['children'][$matchingChildIndex]['active_routes'] ?? []),
                            (array) ($child['active_routes'] ?? []),
                        )));
                    }
                    unset($existingItem);
                }
            }

            // 2) Tempatkan setiap item ke area kerja.
            $rootItems = [];
            $sectionModules = [];
            foreach ($flatItems as $item) {
                $permissionKey = $item['permission_key'] ?? null;
                $isDashboard = in_array($item['route'] ?? null, ['admin.dashboard', 'user.dashboard'], true);
                if ($isDashboard) {
                    $rootItems[] = $item;
                    continue;
                }

                $sectionKey = $sectionByTitle[$item['title'] ?? ''] ?? ($sectionByPermissionKey[$permissionKey] ?? 'other');

                if ($sectionKey === 'inventory' || $sectionKey === 'settings') {
                    // Modul tunggal: anaknya langsung menjadi tingkat 2.
                    $children = $nestByHeadings((array) ($item['children'] ?? [$item]));
                    foreach ($children as $child) {
                        $sectionModules[$sectionKey][] = $child;
                    }
                    continue;
                }

                $sectionModules[$sectionKey][] = $item;
            }

            // 3) Bentuk pohon akhir: Dashboard + area kerja.
            $topLevelItems = $rootItems;
            foreach ($menuSections as $sectionKey => $definition) {
                $modules = $sectionModules[$sectionKey] ?? [];
                if (empty($modules)) {
                    continue;
                }

                $orderOf = function (array $module) use ($moduleOrder): int {
                    $position = array_search($module['permission_key'] ?? null, $moduleOrder, true);
                    return $position === false ? -1 : $position;
                };
                $indexed = [];
                foreach ($modules as $index => $module) {
                    $indexed[] = [$orderOf($module), $index, $module];
                }
                usort($indexed, fn(array $a, array $b): int => [$a[0], $a[1]] <=> [$b[0], $b[1]]);
                $modules = array_column($indexed, 2);

                $activeRoutes = [];
                foreach ($modules as $module) {
                    $activeRoutes = array_merge($activeRoutes, $collectMenuRoutes($module));
                }

                $section = [
                    'title' => $definition['title'],
                    'icon' => $definition['icon'],
                    'active_routes' => array_values(array_unique($activeRoutes)),
                    'children' => $modules,
                ];
                if (!empty($definition['permission_key'])) {
                    $section['permission_key'] = $definition['permission_key'];
                }
                $topLevelItems[] = $section;
            }

            $menus[$role] = [[
                'title_group' => 'Menu',
                'items' => $topLevelItems,
            ]];
        }

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

                // Stock management
                'stock' => 'none',

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

                // Stock management
                'stock' => 'write',

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

                if (
                    $routeName === 'admin' ||
                    $routeName === 'admin.dashboard' ||
                    str_starts_with($routeName, 'admin.dashboard.')
                ) {
                    return 'admin_dashboard';
                }
                if ($routeName === 'user.dashboard' || str_starts_with($routeName, 'user.')) {
                    return 'user_dashboard';
                }
                if (str_starts_with($routeName, 'admin.assets.')) {
                    // Assets leaf permissions
                    if (
                        in_array(
                            $routeName,
                            [
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
                            ],
                            true,
                        )
                    ) {
                        return 'assets_data';
                    }

                    if (in_array($routeName, ['admin.assets.jababeka'], true)) {
                        return 'assets_jababeka';
                    }

                    if (in_array($routeName, ['admin.assets.karawang'], true)) {
                        return 'assets_karawang';
                    }

                    if (in_array($routeName, ['admin.assets.in', 'admin.assets.in.scan'], true)) {
                        return 'assets_in';
                    }

                    if (
                        in_array(
                            $routeName,
                            [
                                'admin.assets.transfer',
                                'admin.assets.transfer.list',
                                'admin.assets.transfer.save',
                                'admin.assets.transfer.cancel',
                            ],
                            true,
                        )
                    ) {
                        return 'assets_transfer';
                    }

                    // Fallback: treat as assets group
                    return 'assets';
                }

                if (str_starts_with($routeName, 'admin.accounts.')) {
                    if (
                        in_array(
                            $routeName,
                            [
                                'admin.accounts.index',
                                'admin.accounts.show',
                                'admin.accounts.json',
                                'admin.accounts.store',
                                'admin.accounts.update',
                                'admin.accounts.destroy',
                                'admin.accounts.verify',
                                'admin.accounts.endpoints.open',
                            ],
                            true,
                        )
                    ) {
                        return 'accounts_data';
                    }

                    if (
                        in_array(
                            $routeName,
                            [
                                'admin.accounts.secrets.reveal',
                                'admin.accounts.secrets.rotate',
                                'admin.accounts.secrets.copy_username',
                                'admin.accounts.approvals.request',
                                'admin.accounts.approvals.approve',
                            ],
                            true,
                        )
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
                if (
                    str_starts_with($routeName, 'admin.stamps.transactions.') ||
                    $routeName === 'admin.stamps.report.pdf'
                ) {
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
                if (
                    str_starts_with($routeName, 'admin.careers.') ||
                    str_starts_with($routeName, 'admin.career_candidates.')
                ) {
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
    @php
        $currentRoute = Route::currentRouteName();

        $leafMatches = function (array $node) use ($currentRoute): bool {
            $nodeRoute = $node['route'] ?? null;
            $nodeActiveRoutes = (array) ($node['active_routes'] ?? []);
            $nodeParams = (array) ($node['params'] ?? []);

            // Assets index dengan query lokasi harus menyorot submenu lokasi
            // (Jababeka/Karawang), bukan "Data Asset" yang generik.
            if (
                $nodeRoute === 'admin.assets.index' &&
                empty($nodeParams) &&
                in_array(request()->query('location'), ['Jababeka', 'Karawang'], true)
            ) {
                return false;
            }

            if ($currentRoute !== $nodeRoute && !in_array($currentRoute, $nodeActiveRoutes, true)) {
                return false;
            }

            foreach ($nodeParams as $key => $value) {
                if (request()->query($key, request()->route($key)) != $value) {
                    return false;
                }
            }

            return true;
        };

        // Susun pohon menu yang sudah difilter hak akses + status aktif (maks. 3 tingkat).
        $buildMenuNode = function (array $item, int $level, string $id) use (
            &$buildMenuNode,
            $isMenuAllowed,
            $resolvePermissionKey,
            $leafMatches,
            $currentRoute,
            $tMenu,
        ): ?array {
            if (!empty($item['is_heading'])) {
                return null;
            }

            $node = [
                'id' => $id,
                'level' => $level,
                'title' => $tMenu($item['title'] ?? ''),
                'icon' => $item['icon'] ?? null,
                'url' => null,
                'active' => false,
                'is_default' => !empty($item['default']),
                'children' => [],
            ];

            if (!empty($item['children']) && is_array($item['children']) && $level < 3) {
                if (!empty($item['permission_key']) && !$isMenuAllowed($item['permission_key'])) {
                    return null;
                }

                foreach (array_values($item['children']) as $index => $child) {
                    $childNode = $buildMenuNode($child, $level + 1, $id . '_' . $index);
                    if ($childNode !== null) {
                        $node['children'][] = $childNode;
                    }
                }
                if (empty($node['children'])) {
                    return null;
                }

                $activeIndex = null;
                foreach ($node['children'] as $index => $childNode) {
                    if (!$childNode['active']) {
                        continue;
                    }
                    if ($activeIndex === null) {
                        $activeIndex = $index;
                    } else {
                        $node['children'][$index]['active'] = false;
                    }
                }

                $node['active'] = $activeIndex !== null
                    || in_array($currentRoute, (array) ($item['active_routes'] ?? []), true);

                if ($node['active'] && $activeIndex === null) {
                    foreach ($node['children'] as $index => $childNode) {
                        if ($childNode['is_default'] && empty($childNode['children'])) {
                            $node['children'][$index]['active'] = true;
                            break;
                        }
                    }
                }

                return $node;
            }

            if (empty($item['route']) || !$isMenuAllowed($resolvePermissionKey($item))) {
                return null;
            }

            $node['url'] = route($item['route'], $item['params'] ?? []);
            $node['active'] = $leafMatches($item);

            return $node;
        };

        $menuTree = [];
        if ($roleId && isset($menus[$roleId])) {
            foreach ($menus[$roleId] as $group) {
                foreach (array_values($group['items']) as $index => $item) {
                    $menuNode = $buildMenuNode($item, 1, 'sidebarMenu_' . $index);
                    if ($menuNode !== null) {
                        $menuTree[] = $menuNode;
                    }
                }
            }
        }
    @endphp
    <div class="pe-sidebar-search">
        <i class="fas fa-magnifying-glass" aria-hidden="true"></i>
        <input type="search" id="sidebarMenuSearch" class="form-control" autocomplete="off"
            placeholder="{{ __('menu.search_placeholder') }}" aria-label="{{ __('menu.search_placeholder') }}">
    </div>
    <nav class="pe-app-sidebar-menu nav nav-pills" data-simplebar id="sidebar-simplebar">
        <ul class="pe-main-menu list-unstyled" id="sidebarMainMenu">
            @if (!empty($menuTree))
                <li class="pe-menu-title">{{ $tGroup('Menu') }}</li>
                @foreach ($menuTree as $menuNode)
                    @include('partials.admin.sidebar-node', ['node' => $menuNode, 'parentId' => 'sidebarMainMenu'])
                @endforeach
                <li class="pe-sidebar-search-empty d-none">{{ __('menu.search_empty') }}</li>
            @endif
        </ul>
    </nav>
    <script>
        // Filter menu sidebar. Memakai event delegation karena skrip tema dapat
        // membangun ulang elemen sidebar setelah halaman dimuat.
        document.addEventListener('input', function (event) {
            const input = event.target;
            if (!(input instanceof HTMLInputElement) || input.id !== 'sidebarMenuSearch') return;

            const menu = document.getElementById('sidebarMainMenu');
            if (!menu) return;

            const empty = menu.querySelector('.pe-sidebar-search-empty');
            const nodes = Array.from(menu.querySelectorAll('li[data-menu-node]'));
            const text = (li) => (li.querySelector(':scope > .pe-nav-link')?.textContent || '').trim().toLowerCase();
            const setOpen = (li, open) => {
                const list = li.querySelector(':scope > .pe-slide-menu');
                const link = li.querySelector(':scope > .pe-nav-link');
                if (!list || !link) return;
                list.classList.toggle('show', open);
                link.setAttribute('aria-expanded', open ? 'true' : 'false');
                link.classList.toggle('collapsed', !open);
            };

            const query = input.value.trim().toLowerCase();
            menu.classList.toggle('is-searching', query !== '');

            if (query === '') {
                nodes.forEach((li) => {
                    li.classList.remove('d-none');
                    setOpen(li, li.classList.contains('active'));
                });
                empty?.classList.add('d-none');
                return;
            }

            // Node tampil bila dirinya, leluhurnya, atau salah satu turunannya cocok.
            const selfMatch = new Map(nodes.map((li) => [li, text(li).includes(query)]));
            let visibleCount = 0;
            nodes.forEach((li) => {
                const descendantMatch = Array.from(li.querySelectorAll('li[data-menu-node]'))
                    .some((child) => selfMatch.get(child));
                let ancestorMatch = false;
                for (let parent = li.parentElement?.closest('li[data-menu-node]'); parent; parent = parent.parentElement?.closest('li[data-menu-node]')) {
                    if (selfMatch.get(parent)) {
                        ancestorMatch = true;
                        break;
                    }
                }
                const visible = selfMatch.get(li) || descendantMatch || ancestorMatch;
                li.classList.toggle('d-none', !visible);
                setOpen(li, descendantMatch);
                if (visible) visibleCount++;
            });
            empty?.classList.toggle('d-none', visibleCount > 0);
        });
    </script>
    @if (auth()->check())
        <div class="dropdown dropup pe-sidebar-profile">
            <button class="pe-sidebar-profile-toggle btn w-100 d-flex align-items-center gap-2" type="button"
                data-bs-toggle="dropdown" aria-expanded="false" aria-label="User profile menu">
                <img src="{{ Avatar::create(auth()->user()->name ?? (auth()->user()->username ?? 'User'))->toBase64() }}"
                    alt="Avatar Image" class="pe-sidebar-profile-avatar rounded-circle">
                <span class="pe-sidebar-profile-copy">
                    <span class="fw-semibold">{{ auth()->user()->name ?? auth()->user()->username }}</span>
                    <small class="text-muted">{{ auth()->user()->role?->role_name ?? 'User' }}</small>
                </span>
                <i class="ri-arrow-up-s-line pe-sidebar-profile-arrow"></i>
            </button>
            <div class="dropdown-menu header-dropdown-menu p-2">
                <div class="d-flex align-items-center gap-2 p-2 border-bottom mb-2">
                    <img src="{{ Avatar::create(auth()->user()->name ?? (auth()->user()->username ?? 'User'))->toBase64() }}"
                        alt="Avatar Image" class="pe-sidebar-profile-avatar rounded-circle">
                    <div class="pe-sidebar-profile-copy">
                        <span class="fw-semibold">{{ auth()->user()->name ?? auth()->user()->username }}</span>
                        <small class="text-muted">{{ auth()->user()->email }}</small>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="fas fa-sign-out-alt me-1"></i> {{ __('auth.logout') }}
                    </button>
                </form>
                <div class="border-top mt-2 pt-2">
                    <div class="small text-muted px-2 pb-1">{{ __('settings.choose_language') }}</div>
                    @foreach ($sidebarLanguageOptions as $code => $label)
                        <form action="{{ route('language.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="locale" value="{{ $code }}">
                            <button type="submit"
                                class="dropdown-item d-flex align-items-center justify-content-between">
                                <span>{{ $label }}</span>
                                @if ($sidebarLocale === $code)
                                    <i class="fas fa-check"></i>
                                @endif
                            </button>
                        </form>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</aside>
