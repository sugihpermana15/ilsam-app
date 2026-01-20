<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  mixed ...$roles
   * @return mixed
   */
  public function handle($request, Closure $next, ...$roles)
  {
    // If roles are passed as a single comma-separated string, split them
    if (count($roles) === 1 && str_contains($roles[0], ',')) {
      $roles = array_map('trim', explode(',', $roles[0]));
    }
    // Pastikan user sudah login
    if (!Auth::check()) {
      return redirect('/signin');
    }
    $user = Auth::user();
    // Eager load role jika belum
    if (!$user->relationLoaded('role')) {
      $user->load('role');
    }
    // Cek role user
    if (!in_array($user->role->role_name ?? '', $roles)) {
      abort(403, 'Anda tidak memiliki akses.');
    }
    return $next($request);
  }
}
