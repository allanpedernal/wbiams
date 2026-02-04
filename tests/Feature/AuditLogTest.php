<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Create roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create(['name' => 'view ip-addresses']);
        Permission::create(['name' => 'create ip-addresses']);
        Permission::create(['name' => 'edit own ip-addresses']);
        Permission::create(['name' => 'edit any ip-addresses']);
        Permission::create(['name' => 'delete any ip-addresses']);
        Permission::create(['name' => 'view audit-logs']);

        $userRole = Role::create(['name' => 'user']);
        $userRole->givePermissionTo([
            'view ip-addresses',
            'create ip-addresses',
            'edit own ip-addresses',
        ]);

        $superAdminRole = Role::create(['name' => 'super-admin']);
        $superAdminRole->givePermissionTo(Permission::all());
    }

    public function test_regular_users_cannot_access_audit_logs(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/audit-logs');

        $response->assertStatus(403);
    }

    public function test_super_admin_can_access_audit_logs(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $response = $this->actingAs($admin)->get('/audit-logs');

        $response->assertStatus(200);
    }

    public function test_super_admin_can_view_single_audit_log(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $activity = Activity::create([
            'log_name' => 'default',
            'description' => 'Test activity',
            'causer_type' => User::class,
            'causer_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get("/audit-logs/{$activity->id}");

        $response->assertStatus(200);
    }

    public function test_login_events_are_logged(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);
        $user->assignRole('user');

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'authentication',
            'description' => 'User logged in',
            'causer_id' => $user->id,
        ]);
    }

    public function test_logout_events_are_logged(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)->post('/logout');

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'authentication',
            'description' => 'User logged out',
            'causer_id' => $user->id,
        ]);
    }

    public function test_audit_logs_cannot_be_deleted_via_api(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $activity = Activity::create([
            'log_name' => 'default',
            'description' => 'Test activity',
        ]);

        // There's no delete route for audit logs
        $response = $this->actingAs($admin)->delete("/audit-logs/{$activity->id}");

        // Should return 405 Method Not Allowed or 404
        $this->assertTrue(in_array($response->status(), [404, 405]));
        $this->assertDatabaseHas('activity_log', ['id' => $activity->id]);
    }
}
