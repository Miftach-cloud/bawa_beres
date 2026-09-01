<?php

namespace Tests\Feature;

use App\Enums\OrderStatus;
use App\Enums\UserRole;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Dashboard;
use App\Models\Order;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\TestCase;

class AdminAuthAndDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_admin_login(): void
    {
        $response = $this->get('/admin');

        $response->assertRedirect('/admin/login');
    }

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
        $response->assertSee('Bawa Beres Internal');
    }

    public function test_user_can_login_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'admin@bawaberes.id',
            'password' => bcrypt('password123'),
            'role' => UserRole::ADMIN,
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@bawaberes.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_user_can_login_with_seeded_credentials_and_username_format(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Test Admin with username handle
        Livewire::test(Login::class)
            ->set('email', 'adminbawaberes')
            ->set('password', 'bawaberes123')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isAdmin());
    }

    public function test_owner_can_login_with_username_handle(): void
    {
        $this->seed(DatabaseSeeder::class);

        Livewire::test(Login::class)
            ->set('email', 'ownerbawaberes')
            ->set('password', 'bawaberes123')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isOwner());
    }

    public function test_operation_can_login_with_username_handle(): void
    {
        $this->seed(DatabaseSeeder::class);

        Livewire::test(Login::class)
            ->set('email', 'operationbawaberes')
            ->set('password', 'bawaberes123')
            ->call('login')
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticated();
        $this->assertTrue(auth()->user()->isOperation());
    }

    public function test_user_cannot_login_with_invalid_credentials(): void
    {
        User::factory()->create([
            'email' => 'admin@bawaberes.id',
            'password' => bcrypt('password123'),
        ]);

        Livewire::test(Login::class)
            ->set('email', 'admin@bawaberes.id')
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->admin()->create();

        $response = $this->actingAs($user)->post('/admin/logout');

        $response->assertRedirect('/admin/login');
        $this->assertGuest();
    }

    public function test_owner_has_full_access_to_all_modules(): void
    {
        $owner = User::factory()->owner()->create();

        $this->actingAs($owner)->get('/admin')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/orders')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/inventory')->assertStatus(200);
        $this->actingAs($owner)->get('/admin/settings')->assertStatus(200);
    }

    public function test_admin_has_access_to_orders_but_denied_settings_and_inventory(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/orders')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/customers')->assertStatus(200);
        $this->actingAs($admin)->get('/admin/payments')->assertStatus(200);

        $this->actingAs($admin)->get('/admin/settings')->assertStatus(403);
        $this->actingAs($admin)->get('/admin/inventory')->assertStatus(403);
    }

    public function test_operation_has_access_to_inventory_and_storage_but_denied_orders_and_settings(): void
    {
        $operation = User::factory()->operation()->create();

        $this->actingAs($operation)->get('/admin')->assertStatus(200);
        $this->actingAs($operation)->get('/admin/schedule')->assertStatus(200);
        $this->actingAs($operation)->get('/admin/inventory')->assertStatus(200);
        $this->actingAs($operation)->get('/admin/storage')->assertStatus(200);

        $this->actingAs($operation)->get('/admin/orders')->assertStatus(403);
        $this->actingAs($operation)->get('/admin/settings')->assertStatus(403);
    }

    public function test_owner_can_access_admin_dashboard(): void
    {
        $owner = User::factory()->owner()->create();

        $response = $this->actingAs($owner)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_operation_can_access_admin_dashboard(): void
    {
        $operation = User::factory()->operation()->create();

        $response = $this->actingAs($operation)->get('/admin');

        $response->assertStatus(200);
    }

    public function test_non_internal_user_cannot_access_admin_dashboard(): void
    {
        $nonInternalUser = User::factory()->create();
        $nonInternalUser->role = null;

        $response = $this->actingAs($nonInternalUser)->get('/admin');

        $response->assertStatus(403);
    }

    public function test_non_internal_user_cannot_login_to_admin(): void
    {
        User::factory()->create([
            'email' => 'staff@bawaberes.id',
            'password' => bcrypt('secret123'),
        ]);

        // Simulate a scenario where the authenticated user does not have access-admin gate
        Gate::before(fn () => null);
        Gate::define('access-admin', fn () => false);

        Livewire::test(Login::class)
            ->set('email', 'staff@bawaberes.id')
            ->set('password', 'secret123')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_dashboard_renders_metrics_correctly(): void
    {
        $admin = User::factory()->admin()->create();

        // Create sample orders for metric counts
        Order::factory()->count(2)->create(['status' => OrderStatus::PENDING_REVIEW]);
        Order::factory()->count(3)->create(['status' => OrderStatus::STORED]);
        Order::factory()->count(1)->create(['status' => OrderStatus::SCHEDULED]);

        $this->actingAs($admin);

        Livewire::test(Dashboard::class)
            ->assertViewHas('metrics', function ($metrics) {
                return $metrics['new_orders'] === 2
                    && $metrics['active_storage'] === 3
                    && $metrics['scheduled_pickups'] === 1;
            })
            ->assertSee('Pesanan Terbaru')
            ->assertSee('Operasional Kota Malang');
    }
}
