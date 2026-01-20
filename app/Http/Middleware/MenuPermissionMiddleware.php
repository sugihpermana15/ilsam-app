<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuPermissionMiddleware
{
  private function defaultsFor(?string $roleName, ?int $roleId): array
  {
    $resolved = $roleName;
    if (!$resolved && $roleId) {
      $resolved = match ($roleId) {
        1 => 'Super Admin',
        2 => 'Admin',
        3 => 'Users',
        default => null,
      };
    }

    if ($resolved === 'Users') {
      return [
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
      ];
    }

    // Super Admin / Admin default: allow all menus (route-level role middleware still applies)
    return [
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
    ];
  }

  public function handle(Request $request, Closure $next, string $key)
  {
    if (!Auth::check()) {
      return redirect('/signin');
    }

    /** @var User|null $user */
    $user = Auth::user();
    if ($user && !$user->relationLoaded('role')) {
      $user->load('role');
    }

    $defaults = $this->defaultsFor($user?->role?->role_name, (int) ($user?->role_id ?? 0));
    $stored = $user?->menu_permissions;

    $merged = $defaults;
    if (is_array($stored)) {
      $merged = array_replace($defaults, $stored);
    }

    if (!((bool) ($merged[$key] ?? false))) {
      abort(403, 'Anda tidak memiliki akses.');
    }

    return $next($request);
  }
}
