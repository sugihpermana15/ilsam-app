<?php

namespace App\Support;

use App\Models\User;

final class MenuAccess
{
    public const ACTION_READ = 'read';
    public const ACTION_CREATE = 'create';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';

    /**
     * Normalized shape:
     *  - read: bool
     *  - create: bool
     *  - update: bool
     *  - delete: bool
     */
    public static function normalize(mixed $value): array
    {
        // Legacy booleans
        if ($value === true) {
            return self::all();
        }
        if ($value === false || $value === null) {
            return self::none();
        }

        // New shape
        if (is_array($value)) {
            $read = (bool) ($value['read'] ?? false);
            $create = (bool) ($value['create'] ?? false);
            $update = (bool) ($value['update'] ?? false);
            $delete = (bool) ($value['delete'] ?? false);

            // Actions imply read.
            if ($create || $update || $delete) {
                $read = true;
            }

            return [
                'read' => $read,
                'create' => $create,
                'update' => $update,
                'delete' => $delete,
            ];
        }

        // Legacy strings: none/read/write
        $v = strtolower(trim((string) $value));
        if ($v === 'write' || $v === 'rw') {
            return self::all();
        }
        if ($v === 'read' || $v === 'r') {
            return self::readOnly();
        }
        if ($v === 'none' || $v === '0' || $v === '') {
            return self::none();
        }

        // Unknown but present: treat as read-only.
        return self::readOnly();
    }

    public static function none(): array
    {
        return ['read' => false, 'create' => false, 'update' => false, 'delete' => false];
    }

    public static function readOnly(): array
    {
        return ['read' => true, 'create' => false, 'update' => false, 'delete' => false];
    }

    public static function all(): array
    {
        return ['read' => true, 'create' => true, 'update' => true, 'delete' => true];
    }

    public static function readCreateUpdate(): array
    {
        return ['read' => true, 'create' => true, 'update' => true, 'delete' => false];
    }

    public static function defaultsForRole(?string $roleName, ?int $roleId): array
    {
        $resolved = $roleName;
        if (!$resolved && $roleId) {
            $resolved = match ((int) $roleId) {
                1 => 'Super Admin',
                2 => 'Admin',
                3 => 'Users',
                default => null,
            };
        }

        $viewOnly = [
            'user_dashboard',
            'admin_dashboard',
            'settings_history_user',
            'settings_history_asset',
        ];

        if ($resolved === 'Users') {
            // Users: only user dashboard by default.
            return [
                'user_dashboard' => self::readOnly(),
                'admin_dashboard' => self::none(),

                // Daily Tasks
                'daily_tasks' => self::readCreateUpdate(),

                // Daily Tasks Masters
                'daily_task_types' => self::none(),
                'daily_task_priorities' => self::none(),
                'daily_task_statuses' => self::none(),

                // Groups
                'assets' => self::none(),
                'uniforms' => self::none(),

                // Devices
                'devices' => self::none(),

                // Assets submenus
                'assets_data' => self::none(),
                'assets_jababeka' => self::none(),
                'assets_karawang' => self::none(),
                'assets_in' => self::none(),
                'assets_transfer' => self::none(),

                // Accounts
                'accounts_data' => self::none(),
                'accounts_secrets' => self::none(),

                // Archived Berkas
                'documents_archive' => self::none(),
                'documents_restricted' => self::none(),

                // Uniforms submenus
                'uniforms_master' => self::none(),
                'uniforms_stock' => self::none(),
                'uniforms_distribution' => self::none(),
                'uniforms_history' => self::none(),

                'employees' => self::none(),
                'employees_index' => self::none(),
                'employees_deleted' => self::none(),
                'employees_audit' => self::none(),

                // Master groups (granular)
                'master_hr' => self::none(),
                'master_assets' => self::none(),
                'master_accounts' => self::none(),
                'master_uniform' => self::none(),
                'master_daily_task' => self::none(),

                'master_data' => self::none(),
                'departments' => self::none(),
                'positions' => self::none(),
                'asset_categories' => self::none(),
                'account_types' => self::none(),
                'asset_locations' => self::none(),
                'plant_sites' => self::none(),
                'asset_uoms' => self::none(),
                'asset_vendors' => self::none(),
                'uniform_sizes' => self::none(),
                'uniform_item_names' => self::none(),
                'uniform_categories' => self::none(),
                'uniform_colors' => self::none(),
                'uniform_uoms' => self::none(),
                'career' => self::none(),
                'certificate' => self::none(),
                'settings' => self::none(),
                'settings_users' => self::none(),
                'settings_history_user' => self::none(),
                'settings_history_asset' => self::none(),
            ];
        }

        // Admin/Super Admin: default allow everything.
        $all = [
            'user_dashboard' => self::readOnly(),
            'admin_dashboard' => self::readOnly(),

            // Daily Tasks
            'daily_tasks' => self::all(),

            // Daily Tasks Masters
            'daily_task_types' => self::all(),
            'daily_task_priorities' => self::all(),
            'daily_task_statuses' => self::all(),

            // Groups
            'assets' => self::all(),
            'uniforms' => self::all(),

            // Devices
            'devices' => self::all(),

            // Assets submenus
            'assets_data' => self::all(),
            'assets_jababeka' => self::all(),
            'assets_karawang' => self::all(),
            'assets_in' => self::all(),
            'assets_transfer' => self::all(),

            // Accounts
            'accounts_data' => self::all(),
            'accounts_secrets' => self::all(),

            // Archived Berkas
            'documents_archive' => self::all(),
            'documents_restricted' => self::readOnly(),

            // Uniforms submenus
            'uniforms_master' => self::all(),
            'uniforms_stock' => self::all(),
            'uniforms_distribution' => self::all(),
            'uniforms_history' => self::all(),

            'employees' => self::all(),
            'employees_index' => self::all(),
            'employees_deleted' => self::all(),
            'employees_audit' => self::all(),

            // Master groups (granular)
            'master_hr' => self::all(),
            'master_assets' => self::all(),
            'master_accounts' => self::all(),
            'master_uniform' => self::all(),
            'master_daily_task' => self::all(),

            'master_data' => self::all(),
            'departments' => self::all(),
            'positions' => self::all(),
            'asset_categories' => self::all(),
            'account_types' => self::all(),
            'asset_locations' => self::all(),
            'plant_sites' => self::all(),
            'asset_uoms' => self::all(),
            'asset_vendors' => self::all(),
            'uniform_sizes' => self::all(),
            'uniform_item_names' => self::all(),
            'uniform_categories' => self::all(),
            'uniform_colors' => self::all(),
            'uniform_uoms' => self::all(),
            'career' => self::all(),
            'certificate' => self::all(),
            'settings' => self::all(),
            'settings_users' => self::all(),
            'settings_history_user' => self::readOnly(),
            'settings_history_asset' => self::readOnly(),
        ];

        // Enforce view-only keys as read-only by default (even if configured otherwise).
        foreach ($viewOnly as $k) {
            if (array_key_exists($k, $all)) {
                $all[$k] = self::readOnly();
            }
        }

        return $all;
    }

    public static function effectivePermissions(?User $user): array
    {
        $defaults = self::defaultsForRole($user?->role?->role_name, $user?->role_id);
        $stored = $user?->menu_permissions;

        $effective = $defaults;
        if (is_array($stored)) {
            foreach ($stored as $k => $v) {
                $effective[$k] = self::normalize($v);
            }
        }

        // Backward compatibility: legacy "master_data" used to gate all master dropdowns.
        // If a user has overrides for master_data but not the new granular master_* keys,
        // mirror the legacy permission into the new group keys.
        if (is_array($stored) && array_key_exists('master_data', $stored)) {
            $legacy = self::normalize($stored['master_data']);
            foreach (['master_hr', 'master_assets', 'master_accounts', 'master_uniform', 'master_daily_task'] as $k) {
                if (!array_key_exists($k, $stored)) {
                    $effective[$k] = $legacy;
                }
            }
        }

        // Ensure all values are normalized.
        foreach ($effective as $k => $v) {
            $effective[$k] = self::normalize($v);
        }

        return $effective;
    }

    public static function can(?User $user, string $key, string $action = self::ACTION_READ): bool
    {
        $effective = self::effectivePermissions($user);
        $p = $effective[$key] ?? self::none();
        $p = self::normalize($p);

        return match ($action) {
            self::ACTION_READ => (bool) ($p['read'] ?? false),
            self::ACTION_CREATE => (bool) ($p['create'] ?? false),
            self::ACTION_UPDATE => (bool) ($p['update'] ?? false),
            self::ACTION_DELETE => (bool) ($p['delete'] ?? false),
            default => (bool) ($p['read'] ?? false),
        };
    }
}
