<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Rename role keys in the users table and Spatie roles table.
 *
 * Old hierarchy: supervisor, admin, superadmin, sysadmin
 * New hierarchy: supervisor, manager, admin, superadmin
 *
 * Mapping:
 *   admin     → manager
 *   superadmin → admin
 *   sysadmin  → superadmin
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Update the 'role' column on the users table (no unique constraint, CASE works)
        DB::statement("
            UPDATE users
            SET role = CASE role
                WHEN 'sysadmin'   THEN 'superadmin'
                WHEN 'superadmin' THEN 'admin'
                WHEN 'admin'      THEN 'manager'
                ELSE role
            END
            WHERE role IN ('sysadmin', 'superadmin', 'admin')
        ");

        // 2. Update the Spatie 'roles' table step-by-step using temp names
        //    to avoid unique constraint violations on (name, guard_name)
        DB::table('roles')->where('name', 'sysadmin')->update(['name' => '_temp_superadmin']);
        DB::table('roles')->where('name', 'superadmin')->update(['name' => '_temp_admin']);
        DB::table('roles')->where('name', 'admin')->update(['name' => 'manager']);
        DB::table('roles')->where('name', '_temp_admin')->update(['name' => 'admin']);
        DB::table('roles')->where('name', '_temp_superadmin')->update(['name' => 'superadmin']);

        // 3. Clear Spatie permission cache
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }

    public function down(): void
    {
        // Reverse: manager → admin, admin → superadmin, superadmin → sysadmin
        DB::statement("
            UPDATE users
            SET role = CASE role
                WHEN 'superadmin' THEN 'sysadmin'
                WHEN 'admin'      THEN 'superadmin'
                WHEN 'manager'    THEN 'admin'
                ELSE role
            END
            WHERE role IN ('superadmin', 'admin', 'manager')
        ");

        DB::table('roles')->where('name', 'superadmin')->update(['name' => '_temp_sysadmin']);
        DB::table('roles')->where('name', 'admin')->update(['name' => '_temp_superadmin']);
        DB::table('roles')->where('name', 'manager')->update(['name' => 'admin']);
        DB::table('roles')->where('name', '_temp_superadmin')->update(['name' => 'superadmin']);
        DB::table('roles')->where('name', '_temp_sysadmin')->update(['name' => 'sysadmin']);

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
    }
};
