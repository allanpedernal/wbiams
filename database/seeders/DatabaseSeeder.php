<?php

namespace Database\Seeders;

use App\Models\IpAddress;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run role and permission seeder first
        $this->call(RoleAndPermissionSeeder::class);

        // Create super admin user
        $superAdmin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
        ]);
        $superAdmin->assignRole('super-admin');

        // Create regular users with IP addresses
        $users = User::factory(5)->create();
        foreach ($users as $user) {
            $user->assignRole('user');
            IpAddress::factory(rand(2, 5))->create(['user_id' => $user->id]);
        }

        // Create some IP addresses for the super admin
        IpAddress::factory(3)->create(['user_id' => $superAdmin->id]);
    }
}
