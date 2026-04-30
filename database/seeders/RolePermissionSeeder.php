<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Ensure Roles Exist
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);

        // Revoke all existing permissions to start fresh and avoid carryover
        $superadminRole->syncPermissions([]);
        $adminRole->syncPermissions([]);
        $managerRole->syncPermissions([]);
        $supervisorRole->syncPermissions([]);

        // 2. Fetch all generated permissions
        $allPermissions = Permission::all();

        // 3. Define module groups
        $superAdminOnlyModules = ['role', 'user', 'field::site']; // Superadmin: roles, users, field sites
        $adminOnlyModules = ['audit::log']; // Admin: audit logs only
        $fieldDataModules = [
            'hybrid::distribution',
            'hybridization::record',
            'monthly::harvest',
            'nursery::operation',
            'pollen::production',
            'terminal',
        ];

        // Also include pages/widgets that all roles should access
        $sharedPermissions = [
            'page_MyProfilePage',
            'widget_StatsOverviewWidget',
            'widget_MonthlyProductionChart',
        ];

        // 4. Assign permissions to roles
        foreach ($allPermissions as $permission) {
            $name = $permission->name;

            // Check if this is a shared permission (pages/widgets)
            if (in_array($name, $sharedPermissions)) {
                $superadminRole->givePermissionTo($permission);
                $adminRole->givePermissionTo($permission);
                $managerRole->givePermissionTo($permission);
                $supervisorRole->givePermissionTo($permission);
                continue;
            }

            // A. Superadmin Only (Roles, Users, Field Sites)
            foreach ($superAdminOnlyModules as $module) {
                if (str_ends_with($name, "_{$module}") || str_ends_with($name, "::{$module}")) {
                    $superadminRole->givePermissionTo($permission);
                }
            }

            // B. Admin Only (Audit Logs)
            foreach ($adminOnlyModules as $module) {
                if (str_ends_with($name, "_{$module}") || str_ends_with($name, "::{$module}")) {
                    $adminRole->givePermissionTo($permission);
                }
            }

            // C. Field Data Modules - Only operational roles (Admin, Manager, Supervisor)
            foreach ($fieldDataModules as $module) {
                if (str_ends_with($name, "_{$module}") || str_ends_with($name, "::{$module}")) {
                    $adminRole->givePermissionTo($permission); // Division Chief sees all
                    $managerRole->givePermissionTo($permission);
                    $supervisorRole->givePermissionTo($permission);
                }
            }
        }

        // 5. Assign roles to users based on their 'role' column
        $users = User::all();
        foreach ($users as $user) {
            if ($user->role) {
                $user->assignRole($user->role);
            }
        }
    }
}
