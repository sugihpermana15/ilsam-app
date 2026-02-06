<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Support\MenuAccess;

class UserDashboardController extends Controller
{
  public function index(Request $request)
  {
    /** @var User|null $user */
    $user = Auth::user();
    if ($user) {
      $user->load('role');
    }
    $roleName = $user?->role?->role_name;

    if (in_array($roleName, ['Super Admin', 'Admin'], true)) {
      return redirect()->route('admin.dashboard');
    }

    if ($user && MenuAccess::can($user, 'admin_dashboard', 'read')) {
      return redirect()->route('admin.dashboard');
    }

    $userId = Auth::id();

    // Reuse the same dashboard view (no extra blade files)
    $permissions = [];
    $tab = null;
    $asset = ['kpi' => null, 'charts' => null, 'recent' => collect()];

    return view('pages.admin.dashboard.dashboard', compact(
      'permissions',
      'tab',
      'asset'
    ));
  }
}
