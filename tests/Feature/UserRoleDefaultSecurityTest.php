<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserRoleDefaultSecurityTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function creating_user_without_explicit_role_defaults_to_null_and_no_admin_privileges(): void
    {
        // 1. Factory default should be null
        $factoryUser = User::factory()->create();
        $this->assertNull($factoryUser->role);
        $this->assertFalse($factoryUser->isOwner());
        $this->assertFalse($factoryUser->isAdmin());
        $this->assertFalse($factoryUser->isOperation());
        $this->assertFalse($factoryUser->hasRole(UserRole::ADMIN));

        // Direct DB insert without role (testing DB level default)
        $rawUser = User::create([
            'name' => 'Raw User',
            'email' => 'raw@example.com',
            'password' => bcrypt('secret123'),
        ]);
        $this->assertNull($rawUser->fresh()->role);
        $this->assertFalse($rawUser->fresh()->isAdmin());
    }

    #[Test]
    public function internal_staff_can_be_created_with_explicit_roles(): void
    {
        $owner = User::factory()->owner()->create();
        $admin = User::factory()->admin()->create();
        $operation = User::factory()->operation()->create();

        $this->assertEquals(UserRole::OWNER, $owner->role);
        $this->assertTrue($owner->isOwner());

        $this->assertEquals(UserRole::ADMIN, $admin->role);
        $this->assertTrue($admin->isAdmin());

        $this->assertEquals(UserRole::OPERATION, $operation->role);
        $this->assertTrue($operation->isOperation());
    }

    #[Test]
    public function default_unprivileged_user_cannot_access_any_admin_endpoint(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/admin')->assertStatus(403);
        $this->get('/admin/orders')->assertStatus(403);
        $this->get('/admin/inventory')->assertStatus(403);
        $this->get('/admin/storage')->assertStatus(403);
        $this->get('/admin/settings')->assertStatus(403);
    }

    #[Test]
    public function database_seeder_explicitly_assigns_correct_internal_roles(): void
    {
        $this->seed(DatabaseSeeder::class);

        $owner = User::where('email', 'owner@bawaberes.id')->firstOrFail();
        $admin = User::where('email', 'admin@bawaberes.id')->firstOrFail();
        $operation = User::where('email', 'operation@bawaberes.id')->firstOrFail();

        $this->assertEquals(UserRole::OWNER, $owner->role);
        $this->assertEquals(UserRole::ADMIN, $admin->role);
        $this->assertEquals(UserRole::OPERATION, $operation->role);
    }
}
