<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DeletedUser;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
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
      'uniform' => [
        'kpi' => $request->boolean('dash_uniform_kpi'),
        'charts' => $request->boolean('dash_uniform_charts'),
        'recent' => $request->boolean('dash_uniform_recent'),
      ],
    ];

    $allTrue = true;
    foreach (['asset', 'uniform'] as $section) {
      foreach (['kpi', 'charts', 'recent'] as $key) {
        if (!($permissions[$section][$key] ?? false)) {
          $allTrue = false;
          break 2;
        }
      }
    }

    return $allTrue ? null : $permissions;
  }

  private function extractMenuPermissions(Request $request): ?array
  {
    if (!$request->boolean('menu_permissions_present')) {
      return null;
    }

    $roleId = (int) $request->input('role_id', 0);

    $permissions = [
      // User area
      'user_dashboard' => $request->boolean('menu_user_dashboard'),

      // Admin area
      'admin_dashboard' => $request->boolean('menu_admin_dashboard'),
      // Groups
      'assets' => $request->boolean('menu_assets'),
      'uniforms' => $request->boolean('menu_uniforms'),

      // Assets submenus
      'assets_data' => $request->boolean('menu_assets_data'),
      'assets_jababeka' => $request->boolean('menu_assets_jababeka'),
      'assets_karawang' => $request->boolean('menu_assets_karawang'),
      'assets_in' => $request->boolean('menu_assets_in'),
      'assets_transfer' => $request->boolean('menu_assets_transfer'),

      // Uniforms submenus
      'uniforms_master' => $request->boolean('menu_uniforms_master'),
      'uniforms_stock' => $request->boolean('menu_uniforms_stock'),
      'uniforms_distribution' => $request->boolean('menu_uniforms_distribution'),
      'uniforms_history' => $request->boolean('menu_uniforms_history'),

      'employees' => $request->boolean('menu_employees'),
      'master_data' => $request->boolean('menu_master_data'),
      'settings' => $request->boolean('menu_settings'),
    ];

    $defaults = match ($roleId) {
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

    return $permissions == $defaults ? null : $permissions;
  }

  public function index(Request $request)
  {
    $search = $request->input('search');

    $query = User::query()->with('role');
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

    return view('pages.admin.users', compact('users', 'search'));
  }

  public function data(Request $request)
  {
    // Server-side processing for DataTables
    $columns = ['name', 'username', 'email', 'role_id', 'id'];
    $draw = intval($request->input('draw'));
    $start = intval($request->input('start', 0));
    $length = intval($request->input('length', 10));
    $search = $request->input('search.value');

    $query = User::query()->with('role');

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

    $data = $query->skip($start)->take($length)->get(['id', 'name', 'username', 'email', 'role_id', 'created_at']);

    return response()->json([
      'draw' => $draw,
      'recordsTotal' => $recordsTotal,
      'recordsFiltered' => $recordsFiltered,
      'data' => $data,
    ]);
  }

  public function show(User $user)
  {
    return response()->json(['data' => $user]);
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'username' => 'nullable|string|max:255|unique:users,username',
      'email' => 'required|email|unique:users,email',
      'password' => 'required|min:8',
      'role_id' => 'required|integer|exists:roles,id',
    ]);

    $dashboardPermissions = $this->extractDashboardPermissions($request);
    $menuPermissions = $this->extractMenuPermissions($request);

    $user = User::create([
      'name' => $validated['name'],
      'username' => $validated['username'] ?? null,
      'email' => $validated['email'],
      'password' => Hash::make($validated['password']),
      'role_id' => (int) $validated['role_id'],
      'dashboard_permissions' => $dashboardPermissions,
      'menu_permissions' => $menuPermissions,
    ]);

    return redirect()->route('admin.users')->with('success', 'User created successfully!');
  }

  public function update(Request $request, User $user)
  {
    $validated = $request->validate([
      'name' => 'required|string|max:255',
      'username' => 'nullable|string|max:255|unique:users,username,' . $user->id,
      'email' => 'required|email|unique:users,email,' . $user->id,
      'password' => 'nullable|min:8',
      'role_id' => 'required|integer|exists:roles,id',
    ]);

    $dashboardPermissions = $this->extractDashboardPermissions($request);
    $menuPermissions = $this->extractMenuPermissions($request);

    $user->name = $validated['name'];
    $user->username = $validated['username'] ?? null;
    $user->email = $validated['email'];
    if (!empty($validated['password'])) {
      $user->password = Hash::make($validated['password']);
    }
    $user->role_id = (int) $validated['role_id'];
    $user->dashboard_permissions = $dashboardPermissions;
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
