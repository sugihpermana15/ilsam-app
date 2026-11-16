<?php

namespace Tests\Unit;

use App\Http\Controllers\Admin\UserController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use ReflectionMethod;
use Tests\TestCase;

class UserControllerMenuPermissionsTest extends TestCase
{
    public function test_role_defaults_are_not_stored_as_overrides(): void
    {
        $permissions = $this->extractMenuPermissions([
            'menu_permissions_present' => '1',
            'role_id' => 2,
        ]);

        $this->assertNull($permissions);
    }

    public function test_uniform_permission_can_be_enabled_for_a_user_role(): void
    {
        $permissions = $this->extractMenuPermissions([
            'menu_permissions_present' => '1',
            'role_id' => 3,
            'menu_keys_present' => ['uniforms_stock'],
            'menu_uniforms_stock' => '1',
        ]);

        $this->assertSame([
            'read' => true,
            'create' => false,
            'update' => false,
            'delete' => false,
        ], $permissions['uniforms_stock']);
    }

    public function test_uniform_permission_can_be_revoked_from_an_admin_role(): void
    {
        $permissions = $this->extractMenuPermissions([
            'menu_permissions_present' => '1',
            'role_id' => 2,
            'menu_keys_present' => ['uniforms_stock'],
        ]);

        $this->assertSame([
            'read' => false,
            'create' => false,
            'update' => false,
            'delete' => false,
        ], $permissions['uniforms_stock']);
    }

    public function test_stock_permission_can_be_enabled_for_a_user_role(): void
    {
        $permissions = $this->extractMenuPermissions([
            'menu_permissions_present' => '1',
            'role_id' => 3,
            'menu_keys_present' => ['stock'],
            'menu_stock' => '1',
            'menu_stock_create' => '1',
        ]);

        $this->assertSame([
            'read' => true,
            'create' => true,
            'update' => false,
            'delete' => false,
        ], $permissions['stock']);
    }

    public function test_stock_permission_is_available_to_the_sidebar_for_a_user_role(): void
    {
        $user = new User([
            'role_id' => 3,
            'menu_permissions' => [
                'stock' => ['read' => true, 'create' => false, 'update' => false, 'delete' => false],
            ],
        ]);
        $user->setRelation('role', new Role(['role_name' => 'Users']));

        Auth::setUser($user);

        try {
            $sidebar = view('partials.sidebar')->render();

            $this->assertStringContainsString(__('menu.section.inventory'), $sidebar);
            $this->assertStringContainsString('pe-menu-level-2', $sidebar);
        } finally {
            Auth::logout();
        }
    }

    public function test_admin_sidebar_uses_three_level_sections_and_shows_stock_once(): void
    {
        foreach ([1 => 'Super Admin', 2 => 'Admin'] as $roleId => $roleName) {
            $user = new User(['role_id' => $roleId]);
            $user->setRelation('role', new Role(['role_name' => $roleName]));

            Auth::setUser($user);

            try {
                $sidebar = view('partials.sidebar')->render();
                $inventoryPosition = strpos($sidebar, __('menu.section.inventory'));
                $assetsPosition = strpos($sidebar, e(__('menu.section.assets_it')));
                $hrPosition = strpos($sidebar, e(__('menu.section.hr')));
                $masterHrPosition = strpos($sidebar, __('menu.master_groups.hr'));

                // Level 1 = area kerja, level 2 = modul, level 3 = halaman.
                $this->assertStringContainsString('pe-menu-level-1', $sidebar);
                $this->assertStringContainsString('pe-menu-level-2', $sidebar);
                $this->assertStringContainsString('pe-menu-level-3', $sidebar);
                $this->assertStringNotContainsString('pe-menu-level-4', $sidebar);

                // Stok muncul sekali sebagai area, dengan tiga kategori sebagai modul.
                $this->assertSame(1, substr_count($sidebar, __('menu.section.inventory')));
                $this->assertStringNotContainsString('Manajemen Stok', $sidebar);
                $this->assertSame(3, preg_match_all('/category=|stock\/(atk|material|apd)\/master"/', $sidebar));

                $this->assertNotFalse($inventoryPosition);
                $this->assertNotFalse($assetsPosition);
                $this->assertNotFalse($hrPosition);
                $this->assertNotFalse($masterHrPosition);
                $this->assertTrue($inventoryPosition < $assetsPosition);
                $this->assertTrue($assetsPosition < $hrPosition);
                $this->assertTrue($hrPosition < $masterHrPosition);
            } finally {
                Auth::logout();
            }
        }
    }

    public function test_profile_menu_is_rendered_at_sidebar_bottom_and_removed_from_header(): void
    {
        $user = new User([
            'name' => 'Root',
            'username' => 'root',
            'email' => 'root@example.com',
            'role_id' => 1,
        ]);
        $user->setRelation('role', new Role(['role_name' => 'Super Admin']));
        Auth::setUser($user);

        try {
            $sidebar = view('partials.sidebar')->render();
            $header = view('partials.header')->render();

            $this->assertStringContainsString('pe-sidebar-profile-toggle', $sidebar);
            $this->assertStringContainsString('Root', $sidebar);
            $this->assertStringContainsString(route('logout'), $sidebar);
            $this->assertStringNotContainsString('header-profile-btn', $header);
            $this->assertStringContainsString('fa-language', $header);
        } finally {
            Auth::logout();
        }
    }

    public function test_stock_can_be_selected_as_the_only_dashboard_tab(): void
    {
        $method = new ReflectionMethod(UserController::class, 'extractDashboardTabs');

        $tabs = $method->invoke(new UserController(), Request::create('/', 'POST', [
            'dash_tabs_present' => '1',
            'dash_tab_stock' => '1',
        ]));

        $this->assertSame(['stock'], $tabs);
    }

    private function extractMenuPermissions(array $input): ?array
    {
        $method = new ReflectionMethod(UserController::class, 'extractMenuPermissions');

        return $method->invoke(new UserController(), Request::create('/', 'POST', $input), new User());
    }
}