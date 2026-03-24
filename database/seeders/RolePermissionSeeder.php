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
        $sysadminRole = Role::firstOrCreate(['name' => 'sysadmin', 'guard_name' => 'web']);
        $superadminRole = Role::firstOrCreate(['name' => 'superadmin', 'guard_name' => 'web']);
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $supervisorRole = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);

        // 2. Fetch all generated permissions
        $allPermissions = Permission::all();

        // 3. Define module groups
        $sysAdminOnlyModules = ['role']; // Strict technical configuration
        $systemManagementModules = ['user', 'audit::log']; // Operational management
        $fieldDataModules = [
            'field::site',
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
                $sysadminRole->givePermissionTo($permission);
                $superadminRole->givePermissionTo($permission);
                $adminRole->givePermissionTo($permission);
                $supervisorRole->givePermissionTo($permission);
                continue;
            }

            // A. Sysadmin Only (Roles)
            foreach ($sysAdminOnlyModules as $module) {
                if (str_ends_with($name, "_{$module}") || str_ends_with($name, "::{$module}")) {
                    $sysadminRole->givePermissionTo($permission);
                }
            }

            // B. System Management (Users, Audit Logs) - Sysadmin + Superadmin
            foreach ($systemManagementModules as $module) {
                if (str_ends_with($name, "_{$module}") || str_ends_with($name, "::{$module}")) {
                    $sysadminRole->givePermissionTo($permission);
                    $superadminRole->givePermissionTo($permission);
                }
            }

            // C. Field Data Modules
            foreach ($fieldDataModules as $module) {
                if (str_ends_with($name, "_{$module}") || str_ends_with($name, "::{$module}")) {
                    $sysadminRole->givePermissionTo($permission); // Sysadmin sees all
                    $superadminRole->givePermissionTo($permission); // Division Chief sees all
                    $adminRole->givePermissionTo($permission);
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
