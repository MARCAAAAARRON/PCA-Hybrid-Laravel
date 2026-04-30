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
        $admin = User::factory()->create([
            'name' => 'Division Chief',
            'email' => 'admin@pca.gov.ph',
            'password' => Hash::make('PCA@gov.ph'),
            'role' => 'admin',
            'field_site_id' => null,
            'email_verified_at' => now(),
        ]);

        // 3. Create Manager (Senior Agriculturist)
        $manager = User::factory()->create([
            'name' => 'Senior Agriculturist',
            'email' => 'manager@pca.gov.ph',
            'password' => Hash::make('PCA@gov.ph'),
            'role' => 'manager',
            'field_site_id' => null,
            'email_verified_at' => now(),
        ]);

        // 4. Create Supervisors
        $loaySupervisor = User::factory()->create([
            'name' => 'Loay Supervisor',
            'email' => 'loay@pca.gov.ph',
            'password' => Hash::make('PCA@gov.ph'),
            'role' => 'supervisor',
            'field_site_id' => $loay?->id,
            'email_verified_at' => now(),
        ]);

        $baliSupervisor = User::factory()->create([
            'name' => 'Balilihan Supervisor',
            'email' => 'balilihan@pca.gov.ph',
            'password' => Hash::make('PCA@gov.ph'),
            'role' => 'supervisor',
            'field_site_id' => $balilihan?->id,
            'email_verified_at' => now(),
        ]);

        // 5. Create Superadmin (System Administrator)
        $superadmin = User::factory()->create([
            'name' => 'System Administrator',
            'email' => 'superadmin@pca.gov.ph',
            'password' => Hash::make('PCA@gov.ph'),
            'role' => 'superadmin',
            'field_site_id' => null,
            'email_verified_at' => now(),
        ]);

        // 6. Default admin user (for kaido-kit compatibility)
        User::factory()->create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // 7. Seed Permissions and assign Roles
        $this->call(RolePermissionSeeder::class);
    }
}
