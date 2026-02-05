<?php

namespace App\Http\Controllers\Admin\Stamp;

use App\Http\Controllers\Controller;
use App\Support\MenuAccess;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class StampDashboardController extends Controller
{
    public function index(Request $request): RedirectResponse
    {
        $user = $request->user();
        if ($user && MenuAccess::can($user, 'admin_dashboard', 'read')) {
            return redirect()->route('admin.dashboard', ['tab' => 'stamps']);
        }

        // No separate Materai dashboard page.
        // If user cannot access Admin dashboard, send them to the most relevant landing page.
        return redirect()->route('admin.stamps.transactions.index');
    }
}
