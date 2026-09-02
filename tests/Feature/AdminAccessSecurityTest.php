<?php

namespace Tests\Feature;

use App\Livewire\Admin\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AdminAccessSecurityTest extends TestCase
{
    use RefreshDatabase;

    protected User $owner;

    protected User $admin;

    protected User $operation;

    protected User $customerUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->owner = User::factory()->owner()->create([
            'email' => 'owner@bawaberes.id',
            'password' => bcrypt('password123'),
        ]);

        $this->admin = User::factory()->admin()->create([
            'email' => 'admin@bawaberes.id',
            'password' => bcrypt('password123'),
        ]);

        $this->operation = User::factory()->operation()->create([
            'email' => 'operation@bawaberes.id',
            'password' => bcrypt('password123'),
        ]);

        $this->customerUser = User::factory()->create([
            'email' => 'customer@example.com',
            'password' => bcrypt('password123'),
            'role' => null,
        ]);
    }

    #[Test]
    public function guest_accessing_admin_dashboard_is_redirected_to_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    #[Test]
    public function guest_accessing_admin_subroutes_is_redirected_to_login(): void
    {
        $this->get('/admin/orders')->assertRedirect('/admin/login');
        $this->get('/admin/inventory')->assertRedirect('/admin/login');
        $this->get('/admin/payments')->assertRedirect('/admin/login');
        $this->get('/admin/schedule')->assertRedirect('/admin/login');
        $this->get('/admin/storage')->assertRedirect('/admin/login');
        $this->get('/admin/settings')->assertRedirect('/admin/login');
    }

    #[Test]
    public function owner_role_is_authorized_to_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->owner)->get('/admin');

        $response->assertStatus(200);
    }

    #[Test]
    public function admin_role_is_authorized_to_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin');

        $response->assertStatus(200);
    }

    #[Test]
    public function operation_role_is_authorized_to_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->operation)->get('/admin');

        $response->assertStatus(200);
    }

    #[Test]
    public function user_without_valid_internal_role_is_forbidden_from_admin_dashboard(): void
    {
        $response = $this->actingAs($this->customerUser)->get('/admin');

        $response->assertStatus(403);
    }

    #[Test]
    public function user_without_valid_internal_role_is_forbidden_from_all_admin_subroutes(): void
    {
        $this->actingAs($this->customerUser);

        $this->get('/admin/orders')->assertStatus(403);
        $this->get('/admin/payments')->assertStatus(403);
        $this->get('/admin/schedule')->assertStatus(403);
        $this->get('/admin/inventory')->assertStatus(403);
        $this->get('/admin/storage')->assertStatus(403);
        $this->get('/admin/services')->assertStatus(403);
        $this->get('/admin/customers')->assertStatus(403);
        $this->get('/admin/settings')->assertStatus(403);
    }

    #[Test]
    public function login_component_rejects_user_without_valid_internal_role(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'customer@example.com')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    #[Test]
    public function login_component_allows_internal_roles(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'admin@bawaberes.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($this->admin);
    }

    #[Test]
    public function module_level_authorization_remains_enforced(): void
    {
        // Admin cannot access inventory or settings
        $this->actingAs($this->admin)->get('/admin/inventory')->assertStatus(403);
        $this->actingAs($this->admin)->get('/admin/settings')->assertStatus(403);

        // Operation cannot access orders or commercial payments
        $this->actingAs($this->operation)->get('/admin/orders')->assertStatus(403);
        $this->actingAs($this->operation)->get('/admin/payments')->assertStatus(403);
    }
}
