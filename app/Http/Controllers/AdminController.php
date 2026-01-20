<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Asset;
use App\Models\Employee;
use App\Models\UniformItem;
use App\Models\UniformIssue;
use App\Models\UniformMovement;

class AdminController extends Controller
{
    public function admin()
    {
        return redirect()->route('admin.dashboard');
    }

    private function dashboardDefaults(): array
    {
        return [
            'asset' => [
                'kpi' => true,
                'charts' => true,
                'recent' => true,
            ],
            'uniform' => [
                'kpi' => true,
                'charts' => true,
                'recent' => true,
            ],
        ];
    }

    private function resolveDashboardPermissions(?array $raw): array
    {
        $defaults = $this->dashboardDefaults();
        if (empty($raw)) {
            return $defaults;
        }

        $merged = array_replace_recursive($defaults, $raw);
        foreach (['asset', 'uniform'] as $section) {
            foreach (['kpi', 'charts', 'recent'] as $key) {
                $merged[$section][$key] = filter_var($merged[$section][$key] ?? false, FILTER_VALIDATE_BOOL);
            }
        }
        return $merged;
    }

    private function monthExpr(string $column): string
    {
        $driver = DB::connection()->getDriverName();
        return match ($driver) {
            'sqlite' => "strftime('%Y-%m', $column)",
            'pgsql' => "to_char($column, 'YYYY-MM')",
            default => "DATE_FORMAT($column, '%Y-%m')",
        };
    }

    private function buildAssetDashboardData(array $permissions): array
    {
        $data = ['kpi' => null, 'charts' => null, 'recent' => collect()];

        if (!($permissions['asset']['kpi'] ?? false) && !($permissions['asset']['charts'] ?? false) && !($permissions['asset']['recent'] ?? false)) {
            return $data;
        }

        if ($permissions['asset']['kpi'] ?? false) {
            $totalAssets = Asset::query()->count();
            $activeAssets = Asset::query()->where('asset_status', 'Active')->count();
            $inactiveAssets = Asset::query()->where('asset_status', '!=', 'Active')->count();
            $totalValue = (float) Asset::query()->whereNotNull('price')->sum('price');
            $newAssets30d = Asset::query()
                ->whereNotNull('input_date')
                ->where('input_date', '>=', now()->subDays(30))
                ->count();

            $data['kpi'] = [
                'total_assets' => $totalAssets,
                'active_assets' => $activeAssets,
                'inactive_assets' => $inactiveAssets,
                'total_value' => $totalValue,
                'new_assets_30d' => $newAssets30d,
            ];
        }

        if ($permissions['asset']['charts'] ?? false) {
            $byStatus = Asset::query()
                ->select('asset_status', DB::raw('COUNT(*) as total'))
                ->groupBy('asset_status')
                ->orderByDesc('total')
                ->get();

            $byLocation = Asset::query()
                ->select('asset_location', DB::raw('COUNT(*) as total'))
                ->groupBy('asset_location')
                ->orderByDesc('total')
                ->get();

            $monthsBack = 12;
            $from = now()->startOfMonth()->subMonths($monthsBack - 1);
            $expr = $this->monthExpr('input_date');
            $monthly = Asset::query()
                ->whereNotNull('input_date')
                ->where('input_date', '>=', $from)
                ->select(DB::raw($expr . ' as ym'), DB::raw('COUNT(*) as total'))
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('total', 'ym');

            $categories = [];
            $series = [];
            for ($i = 0; $i < $monthsBack; $i++) {
                $ym = $from->copy()->addMonths($i)->format('Y-m');
                $categories[] = $ym;
                $series[] = (int) ($monthly[$ym] ?? 0);
            }

            $data['charts'] = [
                'byStatus' => [
                    'labels' => $byStatus->pluck('asset_status')->map(fn($v) => $v ?? 'Unknown')->values(),
                    'series' => $byStatus->pluck('total')->map(fn($v) => (int) $v)->values(),
                ],
                'byLocation' => [
                    'labels' => $byLocation->pluck('asset_location')->map(fn($v) => $v ?? 'Unknown')->values(),
                    'series' => $byLocation->pluck('total')->map(fn($v) => (int) $v)->values(),
                ],
                'monthlyNew' => [
                    'categories' => $categories,
                    'series' => $series,
                ],
            ];
        }

        if ($permissions['asset']['recent'] ?? false) {
            $data['recent'] = Asset::query()->orderByDesc('last_updated')->limit(10)->get();
        }

        return $data;
    }

    private function buildUniformDashboardData(array $permissions): array
    {
        $data = ['kpi' => null, 'charts' => null, 'recent' => collect()];

        if (!($permissions['uniform']['kpi'] ?? false) && !($permissions['uniform']['charts'] ?? false) && !($permissions['uniform']['recent'] ?? false)) {
            return $data;
        }

        if ($permissions['uniform']['kpi'] ?? false) {
            $totalItems = UniformItem::query()->count();
            $totalStock = (int) UniformItem::query()->sum('current_stock');
            $lowStockItems = UniformItem::query()
                ->whereNotNull('min_stock')
                ->whereColumn('current_stock', '<=', 'min_stock')
                ->count();
            $issues30d = UniformIssue::query()->where('issued_at', '>=', now()->subDays(30))->count();

            $data['kpi'] = [
                'total_items' => $totalItems,
                'total_stock' => $totalStock,
                'low_stock_items' => $lowStockItems,
                'issues_30d' => $issues30d,
            ];
        }

        if ($permissions['uniform']['charts'] ?? false) {
            $stockByLocation = UniformItem::query()
                ->select('location', DB::raw('SUM(current_stock) as total'))
                ->groupBy('location')
                ->orderByDesc('total')
                ->get();

            $stockByCategory = UniformItem::query()
                ->select('category', DB::raw('SUM(current_stock) as total'))
                ->groupBy('category')
                ->orderByDesc('total')
                ->limit(10)
                ->get();

            $monthsBack = 12;
            $from = now()->startOfMonth()->subMonths($monthsBack - 1);
            $expr = $this->monthExpr('performed_at');

            $inMonthly = UniformMovement::query()
                ->where('performed_at', '>=', $from)
                ->where('movement_type', 'IN')
                ->select(DB::raw($expr . ' as ym'), DB::raw('SUM(qty_change) as total'))
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('total', 'ym');

            $outMonthly = UniformMovement::query()
                ->where('performed_at', '>=', $from)
                ->where('movement_type', 'OUT')
                ->select(DB::raw($expr . ' as ym'), DB::raw('SUM(ABS(qty_change)) as total'))
                ->groupBy('ym')
                ->orderBy('ym')
                ->pluck('total', 'ym');

            $categories = [];
            $inSeries = [];
            $outSeries = [];
            for ($i = 0; $i < $monthsBack; $i++) {
                $ym = $from->copy()->addMonths($i)->format('Y-m');
                $categories[] = $ym;
                $inSeries[] = (int) ($inMonthly[$ym] ?? 0);
                $outSeries[] = (int) ($outMonthly[$ym] ?? 0);
            }

            $data['charts'] = [
                'stockByLocation' => [
                    'labels' => $stockByLocation->pluck('location')->map(fn($v) => $v ?? 'Unknown')->values(),
                    'series' => $stockByLocation->pluck('total')->map(fn($v) => (int) $v)->values(),
                ],
                'stockByCategory' => [
                    'categories' => $stockByCategory->pluck('category')->values(),
                    'series' => $stockByCategory->pluck('total')->map(fn($v) => (int) $v)->values(),
                ],
                'monthlyMovements' => [
                    'categories' => $categories,
                    'inSeries' => $inSeries,
                    'outSeries' => $outSeries,
                ],
            ];
        }

        if ($permissions['uniform']['recent'] ?? false) {
            $data['recent'] = UniformIssue::query()
                ->with(['item', 'issuedToEmployee', 'issuedTo'])
                ->orderByDesc('issued_at')
                ->limit(10)
                ->get();
        }

        return $data;
    }

    private function buildEmployeeDashboardData(): array
    {
        $totalEmployees = Employee::withTrashed()->count();
        $activeEmployees = Employee::query()->count();
        $inactiveEmployees = Employee::onlyTrashed()->count();

        $activePkwt = Employee::query()->where('employment_status', 'PKWT')->count();
        $activePkwtt = Employee::query()->where('employment_status', 'PKWTT')->count();

        $joined30d = Employee::query()
            ->whereNotNull('join_date')
            ->where('join_date', '>=', now()->subDays(30))
            ->count();

        $recent = Employee::query()
            ->with(['department', 'position'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        return [
            'kpi' => [
                'total' => $totalEmployees,
                'active' => $activeEmployees,
                'inactive' => $inactiveEmployees,
                'active_pkwt' => $activePkwt,
                'active_pkwtt' => $activePkwtt,
                'joined_30d' => $joined30d,
            ],
            'recent' => $recent,
        ];
    }

    public function dashboard(Request $request)
    {
        $permissions = $this->resolveDashboardPermissions(Auth::user()?->dashboard_permissions);
        $tab = $request->query('tab');

        $asset = $this->buildAssetDashboardData($permissions);
        $uniform = $this->buildUniformDashboardData($permissions);

        // Employee summary is always available for admin dashboard view.
        $employee = $this->buildEmployeeDashboardData();

        return view('pages.admin.dashboard.dashboard', compact('permissions', 'tab', 'asset', 'uniform', 'employee'));
    }

    public function dashboardAssets()
    {
        return redirect()->route('admin.dashboard', ['tab' => 'asset']);
    }

    public function dashboardUniforms()
    {
        return redirect()->route('admin.dashboard', ['tab' => 'uniform']);
    }

    public function dataAsset()
    {
        return view('pages.admin.asset_pt');
    }
}
