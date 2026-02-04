<?php

namespace Tests\Feature;

use App\Models\IpAddress;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class IpAddressTest extends TestCase
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

    public function test_guests_cannot_access_ip_addresses(): void
    {
        $response = $this->get('/ip-addresses');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_view_ip_addresses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->get('/ip-addresses');

        $response->assertStatus(200);
    }

    public function test_users_can_create_ip_addresses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post('/ip-addresses', [
            'ip_address' => '192.168.1.1',
            'label' => 'Test Server',
            'comment' => 'A test IP address',
        ]);

        $response->assertRedirect('/ip-addresses');
        $this->assertDatabaseHas('ip_addresses', [
            'ip_address' => '192.168.1.1',
            'label' => 'Test Server',
            'user_id' => $user->id,
        ]);
    }

    public function test_users_can_create_ipv6_addresses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post('/ip-addresses', [
            'ip_address' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'label' => 'IPv6 Server',
        ]);

        $response->assertRedirect('/ip-addresses');
        $this->assertDatabaseHas('ip_addresses', [
            'ip_address' => '2001:0db8:85a3:0000:0000:8a2e:0370:7334',
            'label' => 'IPv6 Server',
        ]);
    }

    public function test_ip_address_validation_fails_for_invalid_ip(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $response = $this->actingAs($user)->post('/ip-addresses', [
            'ip_address' => 'not-a-valid-ip',
            'label' => 'Test Server',
        ]);

        $response->assertSessionHasErrors('ip_address');
    }

    public function test_users_can_update_their_own_ip_addresses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $ipAddress = IpAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->put("/ip-addresses/{$ipAddress->id}", [
            'label' => 'Updated Label',
            'comment' => 'Updated comment',
        ]);

        $response->assertRedirect('/ip-addresses');
        $this->assertDatabaseHas('ip_addresses', [
            'id' => $ipAddress->id,
            'label' => 'Updated Label',
            'comment' => 'Updated comment',
        ]);
    }

    public function test_users_cannot_update_other_users_ip_addresses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $otherUser = User::factory()->create();
        $ipAddress = IpAddress::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->put("/ip-addresses/{$ipAddress->id}", [
            'label' => 'Updated Label',
        ]);

        $response->assertStatus(403);
    }

    public function test_users_cannot_delete_ip_addresses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $ipAddress = IpAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)->delete("/ip-addresses/{$ipAddress->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('ip_addresses', ['id' => $ipAddress->id]);
    }

    public function test_super_admin_can_update_any_ip_address(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $user = User::factory()->create();
        $ipAddress = IpAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->put("/ip-addresses/{$ipAddress->id}", [
            'label' => 'Admin Updated Label',
        ]);

        $response->assertRedirect('/ip-addresses');
        $this->assertDatabaseHas('ip_addresses', [
            'id' => $ipAddress->id,
            'label' => 'Admin Updated Label',
        ]);
    }

    public function test_super_admin_can_delete_any_ip_address(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('super-admin');

        $user = User::factory()->create();
        $ipAddress = IpAddress::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($admin)->delete("/ip-addresses/{$ipAddress->id}");

        $response->assertRedirect('/ip-addresses');
        $this->assertDatabaseMissing('ip_addresses', ['id' => $ipAddress->id]);
    }

    public function test_all_users_can_view_all_ip_addresses(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $otherUser = User::factory()->create();
        $ipAddress = IpAddress::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($user)->get("/ip-addresses/{$ipAddress->id}");

        $response->assertStatus(200);
    }

    public function test_ip_address_changes_are_logged(): void
    {
        $user = User::factory()->create();
        $user->assignRole('user');

        $this->actingAs($user)->post('/ip-addresses', [
            'ip_address' => '10.0.0.1',
            'label' => 'Logged IP',
        ]);

        $this->assertDatabaseHas('activity_log', [
            'description' => 'IP address has been created',
            'causer_id' => $user->id,
        ]);
    }
}
