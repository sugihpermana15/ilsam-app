<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureGuest
{
  /**
   * Redirect authenticated users away from guest-only pages (signin/signup/forgot).
   */
  public function handle(Request $request, Closure $next)
  {
    if (!Auth::check()) {
      return $next($request);
    }

    /** @var User|null $user */
    $user = Auth::user();
    if ($user && !$user->relationLoaded('role')) {
      $user->load('role');
    }
    $roleName = $user?->role?->role_name;

    if (in_array($roleName, ['Super Admin', 'Admin'], true)) {
      return redirect()->route('admin.dashboard');
    }

    return redirect()->route('user.dashboard');
  }
}
