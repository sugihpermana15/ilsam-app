<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeletedUser;
use App\Models\Role;
use App\Models\Employee;
use App\Support\MenuAccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
  private function extractDashboardTabs(Request $request): ?array
  {
    if (!$request->boolean('dash_tabs_present')) {
      return null;
    }

    $keys = ['asset', 'stamps', 'uniforms', 'documents', 'employee'];
    $enabled = [];
    foreach ($keys as $key) {
      if ($request->boolean('dash_tab_' . $key)) {
        $enabled[] = $key;
      }
    }

    // If all are enabled, keep default behavior (no override).
    return count($enabled) === count($keys) ? null : $enabled;
  }

  private function extractDashboardPermissions(Request $request): ?array
  {
    if (!$request->boolean('dash_permissions_present')) {
      return null;
    }

    $permissions = [
      'asset' => [
        'kpi' => $request->boolean('dash_asset_kpi'),
        'charts' => $request->boolean('dash_asset_charts'),
        'recent' => $request->boolean('dash_asset_recent'),
      ],
    ];

    $allTrue = true;
    foreach (['asset'] as $section) {
      foreach (['kpi', 'charts', 'recent'] as $key) {
        if (!($permissions[$section][$key] ?? false)) {
          $allTrue = false;
          break 2;
        }
      }
    }

    return $allTrue ? null : $permissions;
  }

  private function extractMenuPermissions(Request $request, ?User $existingUser = null): ?array
  {
    if (!$request->boolean('menu_permissions_present')) {
      return null;
    }

    $roleId = (int) $request->input('role_id', 0);

    $defaults = MenuAccess::defaultsForRole(null, $roleId);
    $existingOverrides = is_array($existingUser?->menu_permissions) ? $existingUser->menu_permissions : [];

    $presentKeys = $request->input('menu_keys_present', []);
    if (!is_array($presentKeys)) {
      $presentKeys = [];
    }

    $permFor = function (string $key) use ($request, $defaults, $existingOverrides, $presentKeys): array {
      $hasAny = in_array($key, $presentKeys, true)
        || $request->has('menu_' . $key)
        || $request->has('menu_' . $key . '_create')
        || $request->has('menu_' . $key . '_update')
        || $request->has('menu_' . $key . '_delete');

      // If a key is not present in the form submission, keep existing override (if any),
      // otherwise fall back to role defaults. This avoids accidentally clearing unknown/hidden keys.
      if (!$hasAny) {
        if (array_key_exists($key, $existingOverrides)) {
          return MenuAccess::normalize($existingOverrides[$key]);
        }
        return MenuAccess::normalize($defaults[$key] ?? MenuAccess::none());
      }

      $read = $request->boolean('menu_' . $key);
      $create = $request->boolean('menu_' . $key . '_create');
      $update = $request->boolean('menu_' . $key . '_update');
      $delete = $request->boolean('menu_' . $key . '_delete');

      // Actions imply read.
      if ($create || $update || $delete) {
        $read = true;
      }

      return [
        'read' => $read,
        'create' => $create,
        'update' => $update,
        'delete' => $delete,
      ];
    };

    $permissions = [
      // User area
      'user_dashboard' => $permFor('user_dashboard'),

      // Admin area
      'admin_dashboard' => $permFor('admin_dashboard'),

      // Stamps (Materai)
      // Legacy umbrella key: kept for backward compatibility.
      'stamps' => $permFor('stamps'),
      'stamps_master' => $permFor('stamps_master'),
      'stamps_transactions' => $permFor('stamps_transactions'),
      'stamps_requests' => $permFor('stamps_requests'),
      'stamps_validation' => $permFor('stamps_validation'),

      // Daily Tasks
      'daily_tasks' => $permFor('daily_tasks'),

      // Recruitment
      'recruitment' => $permFor('recruitment'),

      // Devices
      'devices' => $permFor('devices'),

      // Groups
      'assets' => $permFor('assets'),

      // Assets submenus
      'assets_data' => $permFor('assets_data'),
      'accounts_data' => $permFor('accounts_data'),
      'accounts_secrets' => $permFor('accounts_secrets'),
      'documents_archive' => $permFor('documents_archive'),
      'documents_restricted' => $permFor('documents_restricted'),
      'assets_jababeka' => $permFor('assets_jababeka'),
      'assets_karawang' => $permFor('assets_karawang'),
      'assets_in' => $permFor('assets_in'),
      'assets_transfer' => $permFor('assets_transfer'),

      'employees' => $permFor('employees'),
      'employees_index' => $permFor('employees_index'),
      'employees_deleted' => $permFor('employees_deleted'),
      'employees_audit' => $permFor('employees_audit'),

      // Master groups (granular)
      'master_hr' => $permFor('master_hr'),
      'master_assets' => $permFor('master_assets'),
      'master_accounts' => $permFor('master_accounts'),
      'master_daily_task' => $permFor('master_daily_task'),

      'master_data' => $permFor('master_data'),
      'departments' => $permFor('departments'),
      'positions' => $permFor('positions'),
      'asset_categories' => $permFor('asset_categories'),
      'account_types' => $permFor('account_types'),
      'asset_locations' => $permFor('asset_locations'),
      'plant_sites' => $permFor('plant_sites'),
      'asset_uoms' => $permFor('asset_uoms'),
      'asset_vendors' => $permFor('asset_vendors'),

      // Daily Tasks Masters
      'daily_task_types' => $permFor('daily_task_types'),
      'daily_task_priorities' => $permFor('daily_task_priorities'),
      'daily_task_statuses' => $permFor('daily_task_statuses'),

      'career' => $permFor('career'),
      'certificate' => $permFor('certificate'),
      'website_products' => $permFor('website_products'),
      'website_settings' => $permFor('website_settings'),
      'website_contact_page' => $permFor('website_contact_page'),
      'website_home_sections' => $permFor('website_home_sections'),
      'settings' => $permFor('settings'),
      'settings_users' => $permFor('settings_users'),
      'settings_history_user' => $permFor('settings_history_user'),
      'settings_history_asset' => $permFor('settings_history_asset'),
    ];

    // Preserve any unknown/hidden keys already stored, but override with submitted keys.
    return array_merge(
      is_array($existingOverrides) ? $existingOverrides : [],
      $permissions
    );

    return $permissions == $defaults ? null : $permissions;
  }

  public function index(Request $request)
  {
    $search = $request->input('search');

    $query = User::query()->with(['role', 'employee']);
    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('username', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%")
          ->orWhereHas('role', function ($r) use ($search) {
            $r->where('role_name', 'like', "%{$search}%");
          });
      });
    }

    $users = $query->orderBy('id', 'desc')->paginate(10)->withQueryString();

    $employees = Employee::query()->orderBy('name')->get(['id', 'name']);

    $isSuperAdmin = (int) (Auth::user()?->role_id ?? 0) === 1;
    $stampValidatorCandidates = $isSuperAdmin
      ? User::query()
          ->with('employee')
          ->orderBy('name')
          ->get(['id', 'name', 'employee_id', 'role_id', 'menu_permissions'])
          ->filter(function (User $u) {
            return MenuAccess::can($u, 'stamps_validation', MenuAccess::ACTION_READ);
          })
          ->values()
      : collect();

    return view('pages.admin.users', compact('users', 'search', 'employees', 'stampValidatorCandidates'));
  }

  public function data(Request $request)
  {
    // Server-side processing for DataTables
    $columns = ['name', 'username', 'email', 'role_id', 'id'];
    $draw = intval($request->input('draw'));
    $start = intval($request->input('start', 0));
    $length = intval($request->input('length', 10));
    $search = $request->input('search.value');

    $query = User::query()->with(['role', 'employee']);

    $recordsTotal = $query->count();

    if ($search) {
      $query->where(function ($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('username', 'like', "%{$search}%")
          ->orWhere('email', 'like', "%{$search}%")
          ->orWhereHas('role', function ($r) use ($search) {
            $r->where('role_name', 'like', "%{$search}%");
          });
      });
    }

    $recordsFiltered = $query->count();

    // Ordering
    $orderColIndex = $request->input('order.0.column');
    $orderDir = $request->input('order.0.dir', 'asc');
    if (isset($orderColIndex) && isset($columns[$orderColIndex])) {
      $query->orderBy($columns[$orderColIndex], $orderDir);
    } else {
      $query->orderBy('id', 'desc');
    }

    $data = $query->skip($start)->take($length)->get(['id', 'name', 'employee_id', 'username', 'email', 'role_id', 'created_at']);

    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $data->map(function ($u) {
        return [
          'id' => $u->id,
          'name' => $u->employee?->name ?? $u->name,
          'username' => $u->username,
          'email' => $u->email,
          'role_id' => $u->role_id,
          'created_at' => $u->created_at,
        ];
      }),
    ]);
  }

  public function show(User $user)
  {
    return response()->json(['data' => $user]);
  }

  public function store(Request $request)
  {
    $isSuperAdmin = (int) (Auth::user()?->role_id ?? 0) === 1;
    $noEmployee = $isSuperAdmin && $request->boolean('no_employee');

    $validated = $request->validate(array_filter([
      'employee_id' => $noEmployee
        ? 'nullable|integer|exists:m_igi_employees,id'
        : 'required|integer|exists:m_igi_employees,id|unique:users,employee_id',
      'name' => $noEmployee ? 'required|string|max:255' : null,
      'username' => 'nullable|string|max:255|unique:users,username',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|min:8',
      'role_id' => 'required|integer|exists:roles,id',
      'stamp_validator_user_id' => $isSuperAdmin ? 'nullable|integer|exists:users,id' : null,
    ]));

    if ($isSuperAdmin && !empty($validated['stamp_validator_user_id'])) {
      $candidate = User::query()->whereKey((int) $validated['stamp_validator_user_id'])->first(['id', 'role_id', 'menu_permissions']);
      if ($candidate === null || !MenuAccess::can($candidate, 'stamps_validation', MenuAccess::ACTION_READ)) {
        return back()->withInput()->with('error', 'Validator Materai harus user yang punya akses "Validasi Permintaan".');
      }
    }

    $employee = null;
    if (!$noEmployee) {
      $employee = Employee::query()->whereKey((int) $validated['employee_id'])->first();
      if ($employee === null) {
        return back()->withInput()->with('error', 'Karyawan tidak ditemukan.');
      }
    }

    $dashboardTabs = $this->extractDashboardTabs($request);
    $dashboardPermissions = $this->extractDashboardPermissions($request);
    $menuPermissions = $this->extractMenuPermissions($request);

    // If admin dashboard tabs are explicitly restricted, ensure the user can access the admin dashboard.
    // Otherwise the tab configuration is effectively useless.
    if (is_array($dashboardTabs) && count($dashboardTabs) > 0) {
      if (!is_array($menuPermissions)) {
        $menuPermissions = [];
      }
      $menuPermissions['admin_dashboard'] = MenuAccess::normalize($menuPermissions['admin_dashboard'] ?? MenuAccess::none());
      $menuPermissions['admin_dashboard']['read'] = true;
    }

    $user = User::create([
      'name' => $noEmployee ? (string) $validated['name'] : (string) $employee->name,
      'employee_id' => $noEmployee ? null : (int) $validated['employee_id'],
      'username' => $validated['username'] ?? null,
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
      'role_id' => (int) $validated['role_id'],
      'stamp_validator_user_id' => $validated['stamp_validator_user_id'] ?? null,
      'dashboard_permissions' => $dashboardPermissions,
      'dashboard_tabs' => $dashboardTabs,
      'menu_permissions' => $menuPermissions,
    ]);

    return redirect()->route('admin.users')->with('success', 'User created successfully!');
  }

  public function update(Request $request, User $user)
  {
    $isSuperAdmin = (int) (Auth::user()?->role_id ?? 0) === 1;
    $noEmployee = $isSuperAdmin && $request->boolean('no_employee');

    $validated = $request->validate(array_filter([
      'employee_id' => $noEmployee
        ? 'nullable|integer|exists:m_igi_employees,id'
        : 'required|integer|exists:m_igi_employees,id|unique:users,employee_id,' . $user->id,
      'name' => $noEmployee ? 'required|string|max:255' : null,
      'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
      'email' => 'required|email|unique:users,email,' . $user->id,
      'password' => 'nullable|min:8',
      'role_id' => 'required|integer|exists:roles,id',
      'stamp_validator_user_id' => $isSuperAdmin ? 'nullable|integer|exists:users,id' : null,
    ]));

    if ($isSuperAdmin && !empty($validated['stamp_validator_user_id'])) {
      $candidate = User::query()->whereKey((int) $validated['stamp_validator_user_id'])->first(['id', 'role_id', 'menu_permissions']);
      if ($candidate === null || !MenuAccess::can($candidate, 'stamps_validation', MenuAccess::ACTION_READ)) {
        return back()->withInput()->with('error', 'Validator Materai harus user yang punya akses "Validasi Permintaan".');
      }
    }

    $employee = null;
    if (!$noEmployee) {
      $employee = Employee::query()->whereKey((int) $validated['employee_id'])->first();
      if ($employee === null) {
        return back()->withInput()->with('error', 'Karyawan tidak ditemukan.');
      }
    }

    $dashboardTabs = $this->extractDashboardTabs($request);
    $dashboardPermissions = $this->extractDashboardPermissions($request);
    $menuPermissions = $this->extractMenuPermissions($request, $user);

    // If admin dashboard tabs are explicitly restricted, ensure the user can access the admin dashboard.
    if (is_array($dashboardTabs) && count($dashboardTabs) > 0) {
      if (!is_array($menuPermissions)) {
        $menuPermissions = [];
      }
      $menuPermissions['admin_dashboard'] = MenuAccess::normalize($menuPermissions['admin_dashboard'] ?? MenuAccess::none());
      $menuPermissions['admin_dashboard']['read'] = true;
    }

    $user->name = $noEmployee ? (string) $validated['name'] : (string) $employee->name;
    $user->employee_id = $noEmployee ? null : (int) $validated['employee_id'];
    $user->username = $validated['username'] ?? null;
    $user->email = $validated['email'];
    if (!empty($validated['password'])) {
      $user->password = Hash::make($validated['password']);
    }
    $user->role_id = (int) $validated['role_id'];
    if ($isSuperAdmin) {
      $user->stamp_validator_user_id = $validated['stamp_validator_user_id'] ?? null;
    }
    $user->dashboard_permissions = $dashboardPermissions;
    $user->dashboard_tabs = $dashboardTabs;
    $user->menu_permissions = $menuPermissions;
    $user->save();

    return redirect()->route('admin.users')->with('success', 'User updated successfully!');
  }

  public function destroy(User $user)
  {
    // Prevent deleting the currently authenticated user
    if (Auth::check() && Auth::id() === $user->id) {
      return redirect()->route('admin.users')->with('error', 'You cannot delete your own account.');
    }

    // Log deleted user to history
    DeletedUser::create([
      'user_id'    => $user->id,
      'name'       => $user->name,
      'username'   => $user->username,
      'email'      => $user->email,
      'role'       => $user->role?->role_name,
      'deleted_at' => now(),
      'deleted_by' => Auth::id(),
    ]);

    $user->delete();
    return redirect()->route('admin.users')->with('success', 'User deleted successfully!');
  }

  public function restore($id)
  {
    $deletedUser = DeletedUser::where('user_id', $id)->first();
    if (!$deletedUser) {
      return redirect()->back()->with('error', 'User not found in deleted history.');
    }
    // Restore user to users table
    $user = new User();
    $user->id = $deletedUser->user_id;
    $user->name = $deletedUser->name;
    $user->username = $deletedUser->username;
    $user->email = $deletedUser->email;
    $roleId = null;
    if (!empty($deletedUser->role)) {
      $roleId = Role::query()->where('role_name', $deletedUser->role)->value('id');
    }
    $user->role_id = $roleId ? (int) $roleId : 3;
    $user->password = bcrypt('password123'); // Set default password, should be changed after restore
    $user->save();
    // Remove from deleted_users
    $deletedUser->delete();
    return redirect()->back()->with('success', 'User restored successfully!');
  }
}
