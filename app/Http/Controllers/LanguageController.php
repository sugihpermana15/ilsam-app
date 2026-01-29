<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class LanguageController extends Controller
{
    private const ALLOWED = ['en', 'id', 'ko'];

    public function update(Request $request): RedirectResponse
    {
        $locale = strtolower(trim((string) $request->input('locale', '')));
        if (!in_array($locale, self::ALLOWED, true)) {
            $locale = 'en';
        }

        $request->session()->put('locale', $locale);

        $user = Auth::user();
        if ($user && Schema::hasColumn('users', 'locale')) {
            $user->locale = $locale;
            $user->save();
        }

        return back();
    }
}
