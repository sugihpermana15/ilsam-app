<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    private const ALLOWED = ['en', 'id', 'ko'];

    public function handle(Request $request, Closure $next)
    {
        $locale = null;

        // 1) User preference
        $user = $request->user();
        if ($user && is_string($user->locale ?? null) && in_array($user->locale, self::ALLOWED, true)) {
            $locale = $user->locale;
        }

        // 2) Session
        if (!$locale) {
            if ($request->hasSession()) {
                $sessionLocale = $request->session()->get('locale');
                if (is_string($sessionLocale) && in_array($sessionLocale, self::ALLOWED, true)) {
                    $locale = $sessionLocale;
                }
            }
        }

        // 3) Accept-Language header
        if (!$locale) {
            $locale = $this->fromAcceptLanguage((string) $request->header('Accept-Language', ''));
        }

        // 4) Fallback
        $locale = $locale ?: 'en';
        App::setLocale($locale);

        return $next($request);
    }

    private function fromAcceptLanguage(string $header): ?string
    {
        if (trim($header) === '') {
            return null;
        }

        foreach (explode(',', $header) as $part) {
            $tag = strtolower(trim(explode(';', $part, 2)[0] ?? ''));
            if ($tag === '') {
                continue;
            }

            $primary = explode('-', str_replace('_', '-', $tag), 2)[0] ?? '';
            if ($primary === 'in') {
                $primary = 'id';
            }

            if (in_array($primary, self::ALLOWED, true)) {
                return $primary;
            }
        }

        return null;
    }
}
