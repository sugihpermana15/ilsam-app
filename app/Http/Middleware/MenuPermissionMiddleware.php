<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Support\MenuAccess;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MenuPermissionMiddleware
{
  private static function inferAction(Request $request): string
  {
    $m = strtoupper($request->method());
    return match ($m) {
      'GET', 'HEAD', 'OPTIONS' => MenuAccess::ACTION_READ,
      'DELETE' => MenuAccess::ACTION_DELETE,
      'PUT', 'PATCH' => MenuAccess::ACTION_UPDATE,
      // Default for POST: treat as create unless overridden on the route.
      default => MenuAccess::ACTION_CREATE,
    };
  }

  public function handle(Request $request, Closure $next, string $key, ?string $action = null)
  {
    if (!Auth::check()) {
      return redirect('/signin');
    }

    /** @var User|null $user */
    $user = Auth::user();
    if ($user && !$user->relationLoaded('role')) {
      $user->load('role');
    }

    $requiredAction = $action ? strtolower(trim($action)) : self::inferAction($request);
    if (!MenuAccess::can($user, $key, $requiredAction)) {
      abort(403, 'Anda tidak memiliki akses.');
    }

    return $next($request);
  }
}
