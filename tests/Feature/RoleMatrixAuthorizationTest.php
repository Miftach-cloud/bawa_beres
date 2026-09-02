<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class RoleMatrixAuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $admin;

    protected User $operation;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->create([
            'name' => 'Owner User',
            'email' => 'owner@bawaberes.id',
            'role' => UserRole::OWNER,
        ]);

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@bawaberes.id',
            'role' => UserRole::ADMIN,
        ]);

        $this->operation = User::factory()->create([
            'name' => 'Operation User',
            'email' => 'operation@bawaberes.id',
            'role' => UserRole::OPERATION,
        ]);
    }

    #[Test]
    public function owner_has_complete_unrestricted_access_to_all_admin_modules(): void
    {
        $this->actingAs($this->owner);

        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/orders')->assertStatus(200);
        $this->get('/admin/payments')->assertStatus(200);
        $this->get('/admin/schedule')->assertStatus(200);
        $this->get('/admin/inventory')->assertStatus(200);
        $this->get('/admin/storage')->assertStatus(200);
        $this->get('/admin/services')->assertStatus(200);
        $this->get('/admin/customers')->assertStatus(200);
        $this->get('/admin/settings')->assertStatus(200);
    }

    #[Test]
    public function admin_role_can_access_business_modules_but_not_inventory_storage_or_settings(): void
    {
        $this->actingAs($this->admin);

        // Allowed modules
        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/orders')->assertStatus(200);
        $this->get('/admin/payments')->assertStatus(200);
        $this->get('/admin/schedule')->assertStatus(200);
        $this->get('/admin/services')->assertStatus(200);
        $this->get('/admin/customers')->assertStatus(200);

        // Forbidden modules (403)
        $this->get('/admin/inventory')->assertStatus(403);
        $this->get('/admin/storage')->assertStatus(403);
        $this->get('/admin/settings')->assertStatus(403);
    }

    #[Test]
    public function operation_role_can_access_operational_modules_but_not_commercial_modules(): void
    {
        $this->actingAs($this->operation);

        // Allowed modules
        $this->get('/admin')->assertStatus(200);
        $this->get('/admin/schedule')->assertStatus(200);
        $this->get('/admin/inventory')->assertStatus(200);
        $this->get('/admin/storage')->assertStatus(200);

        // Forbidden commercial & settings modules (403)
        $this->get('/admin/orders')->assertStatus(403);
        $this->get('/admin/payments')->assertStatus(403);
        $this->get('/admin/services')->assertStatus(403);
        $this->get('/admin/customers')->assertStatus(403);
        $this->get('/admin/settings')->assertStatus(403);
    }

    #[Test]
    public function public_guest_can_access_marketing_and_tracking_but_is_redirected_from_admin(): void
    {
        // Public pages (200 OK)
        $this->get('/')->assertStatus(200);
        $this->get('/services')->assertStatus(200);
        $this->get('/how-it-works')->assertStatus(200);
        $this->get('/storage-security')->assertStatus(200);
        $this->get('/coverage')->assertStatus(200);
        $this->get('/faq')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
        $this->get('/track')->assertStatus(200);

        // Protected Admin routes redirect to login (302)
        $this->get('/admin')->assertRedirect('/admin/login');
        $this->get('/admin/orders')->assertRedirect('/admin/login');
        $this->get('/admin/inventory')->assertRedirect('/admin/login');
    }

    #[Test]
    public function non_internal_user_is_forbidden_from_all_admin_routes(): void
    {
        $nonInternal = User::factory()->create([
            'name' => 'Non Internal User',
            'email' => 'user@example.com',
        ]);
        $nonInternal->role = null;

        $this->actingAs($nonInternal);

        $this->get('/admin')->assertStatus(403);
        $this->get('/admin/orders')->assertStatus(403);
        $this->get('/admin/inventory')->assertStatus(403);
        $this->get('/admin/storage')->assertStatus(403);
        $this->get('/admin/payments')->assertStatus(403);
        $this->get('/admin/schedule')->assertStatus(403);
        $this->get('/admin/customers')->assertStatus(403);
        $this->get('/admin/services')->assertStatus(403);
        $this->get('/admin/settings')->assertStatus(403);
    }
}
