<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class FoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_loads_successfully_with_public_layout(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bawa');
        $response->assertSee('Beres');
        $response->assertSee('Kota Malang');
    }

    public function test_homepage_does_not_expose_database_name_or_system_foundation_check(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertDontSee('System Foundation Check');
        $response->assertDontSee('Uji Reaktivitas Livewire');
        $response->assertDontSee('Livewire Reactivity & Database Connectivity');
        $response->assertDontSee('MySQL Active');
        $response->assertDontSee('Database Issue:');

        // Ensure database name is never printed into public homepage
        $dbName = DB::connection()->getDatabaseName();
        if (! empty($dbName) && $dbName !== ':memory:') {
            $response->assertDontSee($dbName);
        }
    }

    public function test_public_pages_render_normally_without_development_cards(): void
    {
        $routes = [
            '/',
            '/services',
            '/how-it-works',
            '/storage-security',
            '/coverage',
            '/faq',
            '/about',
            '/contact',
            '/track',
        ];

        foreach ($routes as $route) {
            $response = $this->get($route);
            $response->assertStatus(200);
            $response->assertDontSee('System Foundation Check');
            $response->assertDontSee('Uji Reaktivitas Livewire');
        }
    }
}
