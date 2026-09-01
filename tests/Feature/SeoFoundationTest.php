<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = Service::create([
            'name' => 'Jasa Pindahan Kost Mahasiswa',
            'description' => 'Layanan pindahan kost mahasiswa hemat di Malang.',
            'base_price' => 150000,
            'is_active' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function robots_txt_returns_proper_rules_and_sitemap_link(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('User-agent: *');
        $response->assertSee('Disallow: /admin/');
        $response->assertSee('Sitemap:');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function sitemap_xml_returns_valid_xml_with_all_public_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $response->assertSee('<?xml version="1.0" encoding="UTF-8"?>', false);
        $response->assertSee('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', false);
        $response->assertSee(url('/'));
        $response->assertSee(route('public.services'));
        $response->assertSee(route('public.how-it-works'));
        $response->assertSee(route('public.storage-security'));
        $response->assertSee(route('public.coverage'));
        $response->assertSee(route('public.faq'));
        $response->assertSee(route('public.about'));
        $response->assertSee(route('public.contact'));
        $response->assertSee(route('public.track'));
        $response->assertSee(route('public.services.show', $this->service));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function homepage_contains_meta_tags_opengraph_and_local_business_schema(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('<meta name="description"', false);
        $response->assertSee('<link rel="canonical"', false);
        $response->assertSee('<meta property="og:site_name" content="Bawa Beres">', false);
        $response->assertSee('<meta property="og:type" content="website">', false);
        $response->assertSee('<meta name="twitter:card" content="summary_large_image">', false);

        // Schema.org LocalBusiness / MovingCompany
        $response->assertSee('"@type": "MovingCompany"', false);
        $response->assertSee('"addressLocality": "Kota Malang"', false);

        // Schema.org FAQPage on Homepage
        $response->assertSee('"@type": "FAQPage"', false);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function service_detail_page_contains_service_and_breadcrumb_schema(): void
    {
        $response = $this->get("/services/{$this->service->id}");

        $response->assertStatus(200);
        $response->assertSee('"@type": "Service"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
        $response->assertSee($this->service->name);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function faq_page_contains_faq_schema(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('"@type": "FAQPage"', false);
        $response->assertSee('"@type": "BreadcrumbList"', false);
    }
}
