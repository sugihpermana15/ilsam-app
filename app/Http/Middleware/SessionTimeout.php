<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SessionTimeout
{
    private const MAX_SECONDS = 8 * 60 * 60;

    /**
     * Force logout after a maximum authenticated session duration.
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return $next($request);
        }

        $loginAt = $request->session()->get('login_at');

        if (!is_int($loginAt)) {
            $loginAt = is_numeric($loginAt) ? (int) $loginAt : now()->timestamp;
            $request->session()->put('login_at', $loginAt);

            return $next($request);
        }

        if ((now()->timestamp - $loginAt) > self::MAX_SECONDS) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            $message = __('auth.session_expired');
            if ($message === 'auth.session_expired') {
                $message = 'Sesi Anda sudah berakhir. Silakan login kembali.';
            }

            return redirect()->route('auth')->with('error', $message);
        }

        return $next($request);
    }
}
