<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\UniformIssue;

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

    $userId = Auth::id();

    $totalIssues = UniformIssue::query()
      ->where('issued_to_user_id', $userId)
      ->count();

    $activeIssued = UniformIssue::query()
      ->where('issued_to_user_id', $userId)
      ->where('status', 'ISSUED')
      ->count();

    $issues30d = UniformIssue::query()
      ->where('issued_to_user_id', $userId)
      ->where('issued_at', '>=', now()->subDays(30))
      ->count();

    $recentIssues = UniformIssue::query()
      ->with(['item'])
      ->where('issued_to_user_id', $userId)
      ->orderByDesc('issued_at')
      ->limit(10)
      ->get();

    // Reuse the same dashboard view (no extra blade files)
    $permissions = [];
    $tab = null;
    $asset = ['kpi' => null, 'charts' => null, 'recent' => collect()];
    $uniform = ['kpi' => null, 'charts' => null, 'recent' => collect()];

    return view('pages.admin.dashboard.dashboard', compact(
      'permissions',
      'tab',
      'asset',
      'uniform',
      'totalIssues',
      'activeIssued',
      'issues30d',
      'recentIssues'
    ));
  }
}
