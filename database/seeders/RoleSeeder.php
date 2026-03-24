<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Create PCA-specific roles and assign base permissions.
     * Run AFTER shield:generate --all to set up fine-grained access.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles if they don't exist
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $supervisor = Role::firstOrCreate(['name' => 'supervisor', 'guard_name' => 'web']);

        // Super Admin gets all permissions (handled by Shield)
        // Admin gets field data + hybridization record permissions
        $adminPermissions = Permission::where('name', 'like', '%hybrid_distribution%')
            ->orWhere('name', 'like', '%monthly_harvest%')
            ->orWhere('name', 'like', '%nursery_operation%')
            ->orWhere('name', 'like', '%pollen_production%')
            ->orWhere('name', 'like', '%hybridization_record%')
            ->pluck('name');

        if ($adminPermissions->isNotEmpty()) {
            $admin->syncPermissions($adminPermissions);
        }

        // Supervisor gets view + create + update on field data only
        $supervisorPermissions = Permission::where(function ($q) {
            $q->where('name', 'like', 'view_%')
              ->orWhere('name', 'like', 'view_any_%')
              ->orWhere('name', 'like', 'create_%')
              ->orWhere('name', 'like', 'update_%');
        })->where(function ($q) {
            $q->where('name', 'like', '%hybrid_distribution%')
              ->orWhere('name', 'like', '%monthly_harvest%')
              ->orWhere('name', 'like', '%nursery_operation%')
              ->orWhere('name', 'like', '%pollen_production%')
              ->orWhere('name', 'like', '%hybridization_record%');
        })->pluck('name');

        // Also add delete for own drafts (handled in policy)
        $supervisorDeletePermissions = Permission::where(function ($q) {
            $q->where('name', 'like', 'delete_%');
        })->where(function ($q) {
            $q->where('name', 'like', '%hybrid_distribution%')
              ->orWhere('name', 'like', '%monthly_harvest%')
              ->orWhere('name', 'like', '%nursery_operation%')
              ->orWhere('name', 'like', '%pollen_production%')
              ->orWhere('name', 'like', '%hybridization_record%');
        })->pluck('name');

        $allSupervisorPerms = $supervisorPermissions->merge($supervisorDeletePermissions);

        if ($allSupervisorPerms->isNotEmpty()) {
            $supervisor->syncPermissions($allSupervisorPerms);
        }

        // Assign roles to seeded users
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            switch ($user->role) {
                case 'superadmin':
                    if (!$user->hasRole('super_admin')) {
                        $user->assignRole('super_admin');
                    }
                    break;
                case 'admin':
                    if (!$user->hasRole('admin')) {
                        $user->assignRole('admin');
                    }
                    break;
                case 'supervisor':
                    if (!$user->hasRole('supervisor')) {
                        $user->assignRole('supervisor');
                    }
                    break;
            }
        }
    }
}
