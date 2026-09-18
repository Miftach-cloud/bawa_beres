<?php

namespace Tests\Feature;

use App\Support\BusinessProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BusinessInformationConfigTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function schema_does_not_output_fake_coordinates_when_null(): void
    {
        Config::set('business.geo.latitude', null);
        Config::set('business.geo.longitude', null);

        $schema = BusinessProfile::localBusinessSchema();

        $this->assertArrayNotHasKey('geo', $schema);
    }

    #[Test]
    public function schema_outputs_valid_coordinates_when_configured(): void
    {
        Config::set('business.geo.latitude', -7.9839);
        Config::set('business.geo.longitude', 112.6214);

        $schema = BusinessProfile::localBusinessSchema();

        $this->assertArrayHasKey('geo', $schema);
        $this->assertSame(-7.9839, $schema['geo']['latitude']);
        $this->assertSame(112.6214, $schema['geo']['longitude']);
    }

    #[Test]
    public function whatsapp_helper_generates_correct_urls_with_messages(): void
    {
        Config::set('business.whatsapp', '081299998888');

        $url = BusinessProfile::whatsappUrl('Halo CS BawaBeres');
        $this->assertSame('https://wa.me/6281299998888?text=Halo%20CS%20BawaBeres', $url);

        $plainUrl = BusinessProfile::whatsappUrl();
        $this->assertSame('https://wa.me/6281299998888', $plainUrl);
    }

    #[Test]
    public function whatsapp_helper_uses_contact_page_when_no_number_is_configured(): void
    {
        Config::set('business.whatsapp', null);
        Config::set('business.phone', null);

        $this->assertSame(route('public.contact'), BusinessProfile::whatsappUrl());
    }

    #[Test]
    public function contact_page_reflects_configured_business_details(): void
    {
        Config::set('business.whatsapp', '6281987654321');
        Config::set('business.phone', '+62 819-8765-4321');
        Config::set('business.address.street', 'Jl. Ijen No. 12');
        Config::set('business.address.district', 'Klojen');
        Config::set('business.address.city', 'Kota Malang');

        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertSee('+62 819-8765-4321');
        $response->assertSee('Jl. Ijen No. 12, Klojen, Kota Malang');
        $response->assertSee('https://wa.me/6281987654321', false);
    }

    #[Test]
    public function public_layout_renders_configured_business_name_and_footer(): void
    {
        Config::set('business.name', 'Bawa Beres Malang Express');
        Config::set('business.address.street', 'Jl. Borobudur No. 10');

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Bawa Beres Malang Express');
        $response->assertSee('Jl. Borobudur No. 10');
    }
}
