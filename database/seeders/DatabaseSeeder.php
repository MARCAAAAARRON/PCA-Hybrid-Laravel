<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\FieldSite;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Field Sites
        $this->call(FieldSiteSeeder::class);

        $loay = FieldSite::where('name', 'Loay Farm')->first();
        $balilihan = FieldSite::where('name', 'Balilihan Farm')->first();

        // 2. Create Admin (PCDM / Division Chief I)
        $admin = User::firstOrCreate(
            ['email' => 'admin@pca.gov.ph'],
            [
                'name' => 'Division Chief',
                'password' => Hash::make('PCA@gov.ph'),
                'role' => 'admin',
                'field_site_id' => null,
                'email_verified_at' => now(),
            ]
        );

        // 3. Create Manager (Senior Agriculturist)
        $manager = User::firstOrCreate(
            ['email' => 'manager@pca.gov.ph'],
            [
                'name' => 'Senior Agriculturist',
                'password' => Hash::make('PCA@gov.ph'),
                'role' => 'manager',
                'field_site_id' => null,
                'email_verified_at' => now(),
            ]
        );

        // 4. Create Supervisors
        $loaySupervisor = User::firstOrCreate(
            ['email' => 'loay@pca.gov.ph'],
            [
                'name' => 'Loay Supervisor',
                'password' => Hash::make('PCA@gov.ph'),
                'role' => 'supervisor',
                'field_site_id' => $loay?->id,
                'email_verified_at' => now(),
            ]
        );

        $baliSupervisor = User::firstOrCreate(
            ['email' => 'balilihan@pca.gov.ph'],
            [
                'name' => 'Balilihan Supervisor',
                'password' => Hash::make('PCA@gov.ph'),
                'role' => 'supervisor',
                'field_site_id' => $balilihan?->id,
                'email_verified_at' => now(),
            ]
        );

        // 5. Create Superadmin (System Administrator)
        $superadmin = User::firstOrCreate(
            ['email' => 'superadmin@pca.gov.ph'],
            [
                'name' => 'System Administrator',
                'password' => Hash::make('PCA@gov.ph'),
                'role' => 'superadmin',
                'field_site_id' => null,
                'email_verified_at' => now(),
            ]
        );

        // 6. Default admin user (for kaido-kit compatibility)
        User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // 7. Seed Permissions and assign Roles
        $this->call(RolePermissionSeeder::class);
    }
}
