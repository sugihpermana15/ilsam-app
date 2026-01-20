<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function signin()
    {
        return view('pages.auth.auth-signin');
    }

    public function register()
    {
        return view('pages.auth.auth-signup');
    }

    public function store(Request $request)
    {
        $request->validate([
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->firstname . ' ' . $request->lastname,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Do not log the user in automatically; redirect to sign-in so they can authenticate.
        return redirect()->route('auth')->with('success', 'Account created successfully.');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $remember = $request->filled('rememberMe');

        if (Auth::attempt(['username' => $credentials['username'], 'password' => $credentials['password']], $remember)) {
            $request->session()->regenerate();

            /** @var User|null $user */
            $user = Auth::user();
            $user?->load('role');
            $roleName = $user?->role?->role_name;

            if (in_array($roleName, ['Super Admin', 'Admin'], true)) {
                return redirect()->intended(route('admin'));
            }

            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors(['username' => 'The provided credentials do not match our records.']);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('auth');
    }

    public function forgotPassword()
    {
        return view('pages.auth.auth-reset-password');
    }
}
