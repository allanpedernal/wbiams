<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        Permission::create(['name' => 'view ip-addresses']);
        Permission::create(['name' => 'create ip-addresses']);
        Permission::create(['name' => 'edit own ip-addresses']);
        Permission::create(['name' => 'edit any ip-addresses']);
        Permission::create(['name' => 'delete any ip-addresses']);
        Permission::create(['name' => 'view audit-logs']);

        // Create roles and assign permissions
        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view ip-addresses',
            'create ip-addresses',
            'edit own ip-addresses',
        ]);

        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }
}
