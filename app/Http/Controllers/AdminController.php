<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Asset;
use App\Models\ContractTerms;
use App\Models\Document;
use App\Models\Employee;
use App\Models\Stamp;
use App\Models\StampTransaction;
use App\Models\Uniform;
use App\Models\UniformAllocation;
use App\Models\UniformAllocationItem;
use App\Models\UniformEntitlement;
use App\Models\UniformLot;
use App\Models\UniformLotStock;
use App\Models\UniformVariant;
use App\Support\MenuAccess;

class AdminController extends Controller
{
    public function admin()
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && !$user->relationLoaded('role')) {
            $user->load('role');
        }

        if (MenuAccess::can($user, 'admin_dashboard', 'read')) {
            return redirect()->route('admin.dashboard');
        }

        if (
            MenuAccess::can($user, 'stamps_transactions', 'read') ||
            MenuAccess::can($user, 'stamps_master', 'read')
        ) {
            return redirect()->route('admin.stamps.dashboard');
        }

        if (MenuAccess::can($user, 'user_dashboard', 'read')) {
            return redirect()->route('user.dashboard');
        }

        abort(403, 'Anda tidak memiliki akses.');
    }

    private function dashboardDefaults(): array
    {
        return [
            'asset' => [
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
        foreach (['asset'] as $section) {
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

    private function buildDocumentsDashboardData(bool $canSeeRestricted): array
    {
        $today = now()->toDateString();
        $plus90 = now()->addDays(90)->toDateString();

        $expiring = Document::query()
            ->visibleTo(Auth::user(), $canSeeRestricted)
            ->with(['vendor', 'contractTerms'])
            ->whereHas('contractTerms', function ($q) use ($today, $plus90) {
                $q->whereNotNull('end_date')
                    ->whereBetween('end_date', [$today, $plus90]);
            })
            ->whereIn('status', ['Active', 'Draft'])
            ->orderBy('status')
            ->limit(20)
            ->get();

        $latest = Document::query()
            ->visibleTo(Auth::user(), $canSeeRestricted)
            ->with(['vendor'])
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        $activeByMonth = ContractTerms::query()
            ->join('m_igi_documents as d', 'd.document_id', '=', 'm_igi_contract_terms.document_id')
            ->whereNull('d.deleted_at')
            ->when(!$canSeeRestricted, fn($q) => $q->where('d.confidentiality_level', '!=', 'Restricted'))
            ->whereIn('d.document_type', ['Subscription', 'Contract'])
            ->where('d.status', 'Active')
            ->whereNotNull('m_igi_contract_terms.end_date')
            ->selectRaw("DATE_FORMAT(m_igi_contract_terms.end_date, '%Y-%m') as ym, COUNT(*) as total")
            ->groupBy('ym')
            ->orderBy('ym')
            ->limit(12)
            ->get();

        return compact('expiring', 'latest', 'activeByMonth');
    }

    private function buildStampDashboardData(): array
    {
        $kpi = DB::table('stamps')
            ->leftJoin('stamp_balances', 'stamps.id', '=', 'stamp_balances.stamp_id')
            ->selectRaw('COALESCE(SUM(COALESCE(stamp_balances.on_hand_qty, 0)), 0) as total_qty')
            ->selectRaw('COALESCE(SUM(COALESCE(stamp_balances.on_hand_qty, 0) * stamps.face_value), 0) as total_value')
            ->first();

        $totalOutQty = (int) DB::table('stamp_transactions')
            ->where('trx_type', 'OUT')
            ->sum('qty');

        $recentTransactions = StampTransaction::query()
            ->with(['stamp', 'pic', 'creator'])
            ->orderByDesc('trx_date')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        $topStamps = Stamp::query()
            ->with('balance')
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->limit(8)
            ->get();

        return [
            'kpi' => [
                'total_qty' => (int) ($kpi->total_qty ?? 0),
                'total_value' => (int) ($kpi->total_value ?? 0),
                'total_out_qty' => $totalOutQty,
            ],
            'recentTransactions' => $recentTransactions,
            'topStamps' => $topStamps,
        ];
    }

    private function buildUniformsDashboardData(): array
    {
        $from = now()->subDays(30);

        $totalOnHand = (int) UniformLotStock::query()->sum('stock_on_hand');

        $allocations30d = (int) UniformAllocation::query()
            ->where('allocated_at', '>=', $from)
            ->count();

        $allocatedQty30d = (int) UniformAllocationItem::query()
            ->join('m_igi_uniform_allocations as ua', 'ua.id', '=', 'm_igi_uniform_allocation_items.uniform_allocation_id')
            ->where('ua.allocated_at', '>=', $from)
            ->sum('m_igi_uniform_allocation_items.qty');

        $dailyRows = UniformAllocationItem::query()
            ->join('m_igi_uniform_allocations as ua', 'ua.id', '=', 'm_igi_uniform_allocation_items.uniform_allocation_id')
            ->where('ua.allocated_at', '>=', $from->copy()->startOfDay())
            ->selectRaw('DATE(ua.allocated_at) as day, SUM(m_igi_uniform_allocation_items.qty) as qty')
            ->groupByRaw('DATE(ua.allocated_at)')
            ->orderBy('day')
            ->get();

        $qtyByDay = [];
        foreach ($dailyRows as $r) {
            $day = (string) ($r->day ?? '');
            if ($day !== '') {
                $qtyByDay[$day] = (int) ($r->qty ?? 0);
            }
        }

        $dailyCategories = [];
        $dailySeries = [];
        for ($i = 29; $i >= 0; $i--) {
            $d = now()->subDays($i)->startOfDay();
            $key = $d->toDateString();
            $dailyCategories[] = $d->format('d-m');
            $dailySeries[] = (int) ($qtyByDay[$key] ?? 0);
        }

        $recentAllocations = UniformAllocation::query()
            ->with(['employee:id,no_id,name'])
            ->orderByDesc('allocated_at')
            ->orderByDesc('id')
            ->limit(10)
            ->get(['id', 'allocation_no', 'employee_id', 'allocated_at']);

        $recentItems = collect();
        $recentIds = $recentAllocations->pluck('id')->filter()->values();
        if ($recentIds->isNotEmpty()) {
            $recentItems = UniformAllocationItem::query()
                ->leftJoin('m_igi_uniform_variants as uv', 'uv.id', '=', 'm_igi_uniform_allocation_items.uniform_variant_id')
                ->leftJoin('m_igi_uniforms as u_from_variant', 'u_from_variant.id', '=', 'uv.uniform_id')
                ->leftJoin('m_igi_uniforms as u_direct', 'u_direct.id', '=', 'm_igi_uniform_allocation_items.uniform_id')
                ->whereIn('m_igi_uniform_allocation_items.uniform_allocation_id', $recentIds)
                ->get([
                    'm_igi_uniform_allocation_items.uniform_allocation_id',
                    'm_igi_uniform_allocation_items.qty',
                    'uv.size as size',
                    DB::raw('COALESCE(u_direct.name, u_from_variant.name) as uniform_name'),
                    DB::raw('COALESCE(u_direct.code, u_from_variant.code) as uniform_code'),
                ]);
        }

        $itemsByAllocation = $recentItems->groupBy('uniform_allocation_id');
        $recent = $recentAllocations->map(function ($allocation) use ($itemsByAllocation) {
            $items = $itemsByAllocation->get($allocation->id, collect());

            $lines = $items
                ->map(function ($row) {
                    $name = (string) ($row->uniform_name ?? '-');
                    $code = (string) ($row->uniform_code ?? '');
                    $size = (string) ($row->size ?? '');
                    $qty = (int) ($row->qty ?? 0);

                    $label = trim($name);
                    if ($code !== '') {
                        $label .= ' (' . $code . ')';
                    }

                    if (trim($size) === '') {
                        return trim($label . ' x' . $qty);
                    }

                    return trim($label . ' - ' . $size . ' x' . $qty);
                })
                ->filter()
                ->values();

            return [
                'allocation_no' => $allocation->allocation_no,
                'allocated_at' => $allocation->allocated_at,
                'employee_no_id' => $allocation->employee?->no_id,
                'employee_name' => $allocation->employee?->name,
                'items_lines' => $lines,
                'total_qty' => (int) $items->sum('qty'),
            ];
        });

        return [
            'kpi' => [
                'total_uniforms' => (int) Uniform::query()->count(),
                'total_variants' => (int) UniformVariant::query()->count(),
                'total_lots' => (int) UniformLot::query()->count(),
                'total_entitlements' => (int) UniformEntitlement::query()->count(),
                'total_on_hand' => $totalOnHand,
                'allocations_30d' => $allocations30d,
                'allocated_qty_30d' => $allocatedQty30d,
            ],
            'charts' => [
                'allocatedDaily30d' => [
                    'categories' => $dailyCategories,
                    'series' => $dailySeries,
                ],
            ],
            'recentAllocations' => $recent,
        ];
    }

    public function dashboard(Request $request)
    {
        $permissions = $this->resolveDashboardPermissions(Auth::user()?->dashboard_permissions);
        $tab = (string) $request->query('tab', '');

        $asset = $this->buildAssetDashboardData($permissions);

        // Employee summary is always available for admin dashboard view.
        $employee = $this->buildEmployeeDashboardData();

        $user = Auth::user();

        $stamps = null;
        if ($user && (
            MenuAccess::can($user, 'stamps_transactions', 'read') ||
            MenuAccess::can($user, 'stamps_master', 'read')
        )) {
            $stamps = $this->buildStampDashboardData();
        }

        $showDocuments = $user ? MenuAccess::can($user, 'documents_archive', 'read') : false;
        $canSeeRestricted = $user && (($user->role?->role_name ?? null) === 'Super Admin' || MenuAccess::can($user, 'documents_restricted', 'read'));
        $documents = $showDocuments ? $this->buildDocumentsDashboardData($canSeeRestricted) : null;

        $uniforms = null;
        if ($user && (
            MenuAccess::can($user, 'uniforms_stock', 'read') ||
            MenuAccess::can($user, 'uniforms_distribution', 'read') ||
            MenuAccess::can($user, 'uniforms_reports', 'read') ||
            MenuAccess::can($user, 'uniforms_master', 'read') ||
            MenuAccess::can($user, 'uniforms_variants', 'read') ||
            MenuAccess::can($user, 'uniforms_lots', 'read') ||
            MenuAccess::can($user, 'uniforms_entitlements', 'read')
        )) {
            $uniforms = $this->buildUniformsDashboardData();
        }

        // Apply per-user tab access overrides (if configured).
        $tabOverrides = $user && is_array($user->dashboard_tabs) ? array_values($user->dashboard_tabs) : null;
        if (is_array($tabOverrides)) {
            $allowedKeys = ['asset', 'stamps', 'uniforms', 'documents', 'employee'];
            $tabOverrides = array_values(array_intersect($tabOverrides, $allowedKeys));
        }

        $showAsset = !empty($permissions['asset']) && (
            !empty($permissions['asset']['kpi']) ||
            !empty($permissions['asset']['charts']) ||
            !empty($permissions['asset']['recent'])
        );
        $showStamps = (bool) $stamps;
        $showUniforms = (bool) $uniforms;
        $showEmployee = !empty($employee) && !empty($employee['kpi']);
        $showDocs = (bool) $showDocuments;

        $tabOrder = ['asset', 'stamps', 'uniforms', 'documents', 'employee'];
        $tabs = [];
        foreach ($tabOrder as $key) {
            $available = match ($key) {
                'asset' => $showAsset,
                'stamps' => $showStamps,
                'uniforms' => $showUniforms,
                'documents' => $showDocs,
                'employee' => $showEmployee,
                default => false,
            };

            $allowedByOverride = $tabOverrides === null || in_array($key, $tabOverrides, true);
            if ($available && $allowedByOverride) {
                $tabs[] = $key;
            }
        }

        $activeTab = in_array($tab, $tabs, true) ? $tab : ($tabs[0] ?? '');
        if ($activeTab !== '' && $activeTab !== $tab) {
            return redirect()->route('admin.dashboard', ['tab' => $activeTab]);
        }

        $tab = $activeTab;

        return view('pages.admin.dashboard.dashboard', compact('permissions', 'tab', 'asset', 'employee', 'showDocuments', 'documents', 'stamps', 'uniforms'));
    }

    public function dashboardAssets()
    {
        return redirect()->route('admin.dashboard', ['tab' => 'asset']);
    }

    public function dashboardStamps()
    {
        return redirect()->route('admin.dashboard', ['tab' => 'stamps']);
    }

    public function dataAsset()
    {
        return view('pages.admin.asset_pt');
    }
}
