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

            $this->assertStringContainsString('Manajemen Stok', $sidebar);
        } finally {
            Auth::logout();
        }
    }

    public function test_admin_sidebar_groups_master_data_and_shows_stock_menu_once(): void
    {
        foreach ([1 => 'Super Admin', 2 => 'Admin'] as $roleId => $roleName) {
            $user = new User(['role_id' => $roleId]);
            $user->setRelation('role', new Role(['role_name' => $roleName]));

            Auth::setUser($user);

            try {
                $sidebar = view('partials.sidebar')->render();
                $operationsPosition = strpos($sidebar, __('menu.group.operations'));
                $masterDataPosition = strpos($sidebar, __('menu.master_data'));
                $masterHrPosition = strpos($sidebar, __('menu.master_groups.hr'));

                $this->assertSame(1, substr_count($sidebar, 'Manajemen Stok'));
                $this->assertNotFalse($operationsPosition);
                $this->assertNotFalse($masterDataPosition);
                $this->assertNotFalse($masterHrPosition);
                $this->assertTrue($operationsPosition < $masterDataPosition);
                $this->assertTrue($masterDataPosition < $masterHrPosition);
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