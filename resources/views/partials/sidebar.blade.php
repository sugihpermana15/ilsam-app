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
        $menus = [
            1 => [ // Super Admin
                [
                    'title_group' => 'Main',
                    'items' => [
                        [
                            'title' => 'Dashboard',
                            'icon' => 'bi bi-speedometer2',
                            'route' => 'admin.dashboard',
                            'active_routes' => [
                                'admin',
                                'admin.dashboard',
                                'admin.dashboard.assets',
                                'admin.dashboard.uniforms',
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Application',
                    'items' => [
                        [
                            'title' => 'Perlengkapan Aset',
                            'icon' => 'bi bi-hdd-stack',
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
                                ['title' => 'Data Asset', 'route' => 'admin.assets.index', 'default' => true, 'permission_key' => 'assets_data'],
                                ['title' => 'Aset Jababeka', 'route' => 'admin.assets.jababeka', 'permission_key' => 'assets_jababeka'],
                                ['title' => 'Aset Karawang', 'route' => 'admin.assets.karawang', 'permission_key' => 'assets_karawang'],
                                ['title' => 'Aset Masuk', 'route' => 'admin.assets.in', 'permission_key' => 'assets_in'],
                                ['title' => 'Aset Keluar', 'route' => 'admin.assets.transfer', 'permission_key' => 'assets_transfer'],
                            ],
                        ],
                        [
                            'title' => 'Stok Seragam',
                            'icon' => 'bi bi-box-seam',
                            'permission_key' => 'uniforms',
                            'active_routes' => [
                                'admin.uniforms.master',
                                'admin.uniforms.items.store',
                                'admin.uniforms.items.toggle',
                                'admin.uniforms.stock',
                                'admin.uniforms.stock.in',
                                'admin.uniforms.distribution',
                                'admin.uniforms.distribution.issue',
                                'admin.uniforms.history',
                            ],
                            'children' => [
                                ['title' => 'Master Seragam', 'route' => 'admin.uniforms.master', 'default' => true, 'permission_key' => 'uniforms_master'],
                                ['title' => 'Stok Masuk', 'route' => 'admin.uniforms.stock', 'permission_key' => 'uniforms_stock'],
                                ['title' => 'Distribusi', 'route' => 'admin.uniforms.distribution', 'permission_key' => 'uniforms_distribution'],
                                ['title' => 'Riwayat', 'route' => 'admin.uniforms.history', 'permission_key' => 'uniforms_history'],
                            ],
                        ],
                        [
                            'title' => 'Master Karyawan',
                            'icon' => 'bi bi-people',
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
                                ['title' => 'Employees', 'route' => 'admin.employees.index', 'default' => true],
                                ['title' => 'Deleted', 'route' => 'admin.employees.deleted'],
                                ['title' => 'Audit Log', 'route' => 'admin.employees.audit'],
                            ],
                        ],
                        [
                            'title' => 'Master Data',
                            'icon' => 'bi bi-database',
                            'active_routes' => [
                                'admin.departments.index',
                                'admin.departments.store',
                                'admin.departments.update',
                                'admin.departments.destroy',
                                'admin.positions.index',
                                'admin.positions.store',
                                'admin.positions.update',
                                'admin.positions.destroy',
                                'admin.uniform_item_names.index',
                                'admin.uniform_item_names.store',
                                'admin.uniform_item_names.update',
                                'admin.uniform_item_names.toggle',
                                'admin.uniform_categories.index',
                                'admin.uniform_categories.store',
                                'admin.uniform_categories.update',
                                'admin.uniform_categories.toggle',
                                'admin.uniform_colors.index',
                                'admin.uniform_colors.store',
                                'admin.uniform_colors.update',
                                'admin.uniform_colors.toggle',
                                'admin.uniform_uoms.index',
                                'admin.uniform_uoms.store',
                                'admin.uniform_uoms.update',
                                'admin.uniform_uoms.toggle',
                                'admin.uniform_sizes.index',
                                'admin.uniform_sizes.store',
                                'admin.uniform_sizes.update',
                                'admin.uniform_sizes.toggle',
                                'admin.asset_categories.index',
                                'admin.asset_categories.store',
                                'admin.asset_categories.update',
                                'admin.asset_categories.toggle',
                                'admin.asset_locations.index',
                                'admin.asset_locations.store',
                                'admin.asset_locations.update',
                                'admin.asset_locations.toggle',
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
                                ['title' => 'Departments', 'route' => 'admin.departments.index', 'default' => true],
                                ['title' => 'Positions', 'route' => 'admin.positions.index'],
                                ['title' => 'Kategori Asset', 'route' => 'admin.asset_categories.index'],
                                ['title' => 'Lokasi Asset', 'route' => 'admin.asset_locations.index'],
                                ['title' => 'Satuan Asset', 'route' => 'admin.asset_uoms.index'],
                                ['title' => 'Vendor Asset', 'route' => 'admin.asset_vendors.index'],
                                ['title' => 'Ukuran Seragam', 'route' => 'admin.uniform_sizes.index'],
                                ['title' => 'Nama Item Seragam', 'route' => 'admin.uniform_item_names.index'],
                                ['title' => 'Kategori Seragam', 'route' => 'admin.uniform_categories.index'],
                                ['title' => 'Warna Seragam', 'route' => 'admin.uniform_colors.index'],
                                ['title' => 'UOM Seragam', 'route' => 'admin.uniform_uoms.index'],
                            ],
                        ],
                    ],
                ],
                // [
                //     'title_group' => 'Applications',
                //     'items' => [
                //         [
                //             'title' => 'Calendar',
                //             'icon' => 'bi bi-calendar-week',
                //             'route' => 'apps-calendar',
                //         ],
                //         [
                //             'title' => 'E-Commerce',
                //             'icon' => 'bi bi-cart4',
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
                            'icon' => 'bi bi-briefcase',
                            'route' => 'admin.careers.index',
                            'permission_key' => 'settings',
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
                            'icon' => 'bi bi-patch-check',
                            'route' => 'admin.certificates.index',
                            'permission_key' => 'settings',
                            'active_routes' => [
                                'admin.certificates.index',
                                'admin.certificates.store',
                                'admin.certificates.update',
                                'admin.certificates.destroy',
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Settings & UI',
                    'items' => [
                        [
                            'title' => 'Settings and Log',
                            'icon' => 'bi bi-gear-wide-connected',
                            'children' => [
                                ['title' => 'Users', 'route' => 'admin.users'],
                                ['title' => 'History Delete User', 'route' => 'admin.users.history.delete'],
                                ['title' => 'History Delete Asset', 'route' => 'admin.assets.historyDelete'],
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
                            'icon' => 'bi bi-speedometer2',
                            'route' => 'admin.dashboard',
                            'active_routes' => [
                                'admin',
                                'admin.dashboard',
                                'admin.dashboard.assets',
                                'admin.dashboard.uniforms',
                            ],
                        ],
                        [
                            'title' => 'Master Karyawan',
                            'icon' => 'bi bi-people',
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
                                ['title' => 'Employees', 'route' => 'admin.employees.index', 'default' => true],
                                ['title' => 'Deleted', 'route' => 'admin.employees.deleted'],
                                ['title' => 'Audit Log', 'route' => 'admin.employees.audit'],
                            ],
                        ],
                        [
                            'title' => 'Master Data',
                            'icon' => 'bi bi-database',
                            'active_routes' => [
                                'admin.departments.index',
                                'admin.departments.store',
                                'admin.departments.update',
                                'admin.departments.destroy',
                                'admin.positions.index',
                                'admin.positions.store',
                                'admin.positions.update',
                                'admin.positions.destroy',
                                'admin.uniform_item_names.index',
                                'admin.uniform_item_names.store',
                                'admin.uniform_item_names.update',
                                'admin.uniform_item_names.toggle',
                                'admin.uniform_categories.index',
                                'admin.uniform_categories.store',
                                'admin.uniform_categories.update',
                                'admin.uniform_categories.toggle',
                                'admin.uniform_colors.index',
                                'admin.uniform_colors.store',
                                'admin.uniform_colors.update',
                                'admin.uniform_colors.toggle',
                                'admin.uniform_uoms.index',
                                'admin.uniform_uoms.store',
                                'admin.uniform_uoms.update',
                                'admin.uniform_uoms.toggle',
                                'admin.uniform_sizes.index',
                                'admin.uniform_sizes.store',
                                'admin.uniform_sizes.update',
                                'admin.uniform_sizes.toggle',
                            ],
                            'children' => [
                                ['title' => 'Departments', 'route' => 'admin.departments.index', 'default' => true],
                                ['title' => 'Positions', 'route' => 'admin.positions.index'],
                                ['title' => 'Ukuran Seragam', 'route' => 'admin.uniform_sizes.index'],
                                ['title' => 'Nama Item Seragam', 'route' => 'admin.uniform_item_names.index'],
                                ['title' => 'Kategori Seragam', 'route' => 'admin.uniform_categories.index'],
                                ['title' => 'Warna Seragam', 'route' => 'admin.uniform_colors.index'],
                                ['title' => 'UOM Seragam', 'route' => 'admin.uniform_uoms.index'],
                            ],
                        ],
                    ],
                ],
                [
                    'title_group' => 'Web Pages',
                    'items' => [
                        [
                            'title' => 'Career Management',
                            'icon' => 'bi bi-briefcase',
                            'route' => 'admin.careers.index',
                            'permission_key' => 'settings',
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
                [
                    'title_group' => 'Applications',
                    'items' => [
                        [
                            'title' => 'Calendar',
                            'icon' => 'bi bi-calendar-week',
                            'route' => 'apps-calendar',
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
                            'icon' => 'bi bi-speedometer2',
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
                            'title' => 'Perlengkapan Aset',
                            'icon' => 'bi bi-hdd-stack',
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
                                ['title' => 'Data Asset', 'route' => 'admin.assets.index', 'default' => true, 'permission_key' => 'assets_data'],
                                ['title' => 'Aset Jababeka', 'route' => 'admin.assets.jababeka', 'permission_key' => 'assets_jababeka'],
                                ['title' => 'Aset Karawang', 'route' => 'admin.assets.karawang', 'permission_key' => 'assets_karawang'],
                                ['title' => 'Aset Masuk', 'route' => 'admin.assets.in', 'permission_key' => 'assets_in'],
                                ['title' => 'Aset Keluar', 'route' => 'admin.assets.transfer', 'permission_key' => 'assets_transfer'],
                            ],
                        ],
                        [
                            'title' => 'Stok Seragam',
                            'icon' => 'bi bi-box-seam',
                            'permission_key' => 'uniforms',
                            'active_routes' => [
                                'admin.uniforms.master',
                                'admin.uniforms.stock',
                                'admin.uniforms.distribution',
                                'admin.uniforms.history',
                            ],
                            'children' => [
                                ['title' => 'Master Seragam', 'route' => 'admin.uniforms.master', 'default' => true, 'permission_key' => 'uniforms_master'],
                                ['title' => 'Stok Masuk', 'route' => 'admin.uniforms.stock', 'permission_key' => 'uniforms_stock'],
                                ['title' => 'Distribusi', 'route' => 'admin.uniforms.distribution', 'permission_key' => 'uniforms_distribution'],
                                ['title' => 'Riwayat', 'route' => 'admin.uniforms.history', 'permission_key' => 'uniforms_history'],
                            ],
                        ],
                    ],
                ],
            ],
        ];
        $user = auth()->user();
        $roleId = $user?->role_id;

        $menuDefaults = match ((int) $roleId) {
            3 => [
                'user_dashboard' => true,
                'admin_dashboard' => false,
                // Groups
                'assets' => false,
                'uniforms' => false,

                // Assets submenus
                'assets_data' => false,
                'assets_jababeka' => false,
                'assets_karawang' => false,
                'assets_in' => false,
                'assets_transfer' => false,

                // Uniforms submenus
                'uniforms_master' => false,
                'uniforms_stock' => false,
                'uniforms_distribution' => false,
                'uniforms_history' => false,

                'employees' => false,
                'master_data' => false,
                'settings' => false,
            ],
            default => [
                'user_dashboard' => true,
                'admin_dashboard' => true,
                // Groups
                'assets' => true,
                'uniforms' => true,

                // Assets submenus
                'assets_data' => true,
                'assets_jababeka' => true,
                'assets_karawang' => true,
                'assets_in' => true,
                'assets_transfer' => true,

                // Uniforms submenus
                'uniforms_master' => true,
                'uniforms_stock' => true,
                'uniforms_distribution' => true,
                'uniforms_history' => true,

                'employees' => true,
                'master_data' => true,
                'settings' => true,
            ],
        };

        $menuOverrides = is_array($user?->menu_permissions) ? $user->menu_permissions : [];
        $menuPermissions = array_merge($menuDefaults, $menuOverrides);

        $isMenuAllowed = function (?string $key) use ($menuPermissions): bool {
            if ($key === null) {
                return true;
            }
            return (bool) ($menuPermissions[$key] ?? false);
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
                if (str_starts_with($routeName, 'admin.uniforms.')) {
                    // Uniforms leaf permissions
                    if (
                        in_array($routeName, [
                            'admin.uniforms.master',
                            'admin.uniforms.items.store',
                            'admin.uniforms.items.update',
                            'admin.uniforms.items.toggle',
                        ], true)
                    ) {
                        return 'uniforms_master';
                    }

                    if (
                        in_array($routeName, [
                            'admin.uniforms.stock',
                            'admin.uniforms.stock.in',
                        ], true)
                    ) {
                        return 'uniforms_stock';
                    }

                    if (
                        in_array($routeName, [
                            'admin.uniforms.distribution',
                            'admin.uniforms.distribution.issue',
                            'admin.uniforms.issues.return',
                        ], true)
                    ) {
                        return 'uniforms_distribution';
                    }

                    if (
                        in_array($routeName, [
                            'admin.uniforms.history',
                        ], true)
                    ) {
                        return 'uniforms_history';
                    }

                    // Fallback: treat as uniforms group
                    return 'uniforms';
                }
                if (str_starts_with($routeName, 'admin.employees.')) {
                    return 'employees';
                }
                if (preg_match('/^admin\.(departments|positions|uniform_item_names|uniform_categories|uniform_colors|uniform_uoms|uniform_sizes|asset_categories|asset_locations|asset_uoms|asset_vendors)\./', $routeName) === 1) {
                    return 'master_data';
                }
                if (preg_match('/^admin\.(users(\.|$)|assets\.historyDelete$)/', $routeName) === 1) {
                    return 'settings';
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

                    <li class="pe-menu-title">{{ $group['title_group'] }}</li>
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
                                    if ($currentRoute !== ($child['route'] ?? null)) {
                                        return false;
                                    }
                                    $childParams = $child['params'] ?? [];
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
                                    <span class="pe-nav-content">{{ $item['title'] }}</span>
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
                                                {{ $child['title'] }}
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
                            @endphp
                            <li class="pe-slide">
                                <a href="{{ isset($item['route']) ? route($item['route']) : '#' }}"
                                    class="pe-nav-link{{ $isActive ? ' active' : '' }}">
                                    <i class="{{ $item['icon'] }} pe-nav-icon"></i>
                                    <span class="pe-nav-content">{{ $item['title'] }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                @endforeach
            @endif
        </ul>
    </nav>
</aside>