<?php

namespace Tests\Feature;

use App\Livewire\Public\SystemStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
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

    public function test_livewire_system_status_component_mounts_and_increments(): void
    {
        Livewire::test(SystemStatus::class)
            ->assertSet('counter', 0)
            ->call('increment')
            ->assertSet('counter', 1);
    }
}
