<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureAuthenticated
{
  /**
   * Handle an incoming request.
   */
  public function handle(Request $request, Closure $next)
  {
    if (!auth()->check()) {
      return redirect()->route('auth')->with('error', 'Please sign in to access the dashboard.');
    }

    return $next($request);
  }
}
