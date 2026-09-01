<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicWebsiteFoundationTest extends TestCase
{
    use RefreshDatabase;

    protected Service $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = Service::create([
            'name' => 'Jasa Pindahan Kost Premium',
            'description' => 'Layanan pindahan kost lengkap dengan armada dan tenaga angkut.',
            'base_price' => 200000,
            'is_active' => true,
        ]);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function homepage_renders_all_marketing_narrative_sections(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('Pindahan');
        $response->assertSee('Titip Barang Jadi');
        $response->assertSee('Masalah Yang Sering Terjadi');
        $response->assertSee('Solusi Lengkap untuk Malang Raya');
        $response->assertSee('Cara Kerja yang Super Simpel');
        $response->assertSee('Keunggulan Standar Layanan Kami');
        $response->assertSee('Pertanyaan Umum (FAQ)');
        $response->assertSee('Pesan Jasa Pindahan');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function services_catalog_page_displays_active_services(): void
    {
        $response = $this->get('/services');

        $response->assertStatus(200);
        $response->assertSee('Katalog Layanan Resmi');
        $response->assertSee('Jasa Pindahan Kost Premium');
        $response->assertSee('200.000');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function service_detail_page_renders_service_specifics(): void
    {
        $response = $this->get("/services/{$this->service->id}");

        $response->assertStatus(200);
        $response->assertSee('Jasa Pindahan Kost Premium');
        $response->assertSee('200.000');
        $response->assertSee('Label QR Inventaris');
        $response->assertSee('Pesan Layanan Ini Sekarang');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function how_it_works_page_loads_with_4_steps(): void
    {
        $response = $this->get('/how-it-works');

        $response->assertStatus(200);
        $response->assertSee('Bagaimana BawaBeres Bekerja');
        $response->assertSee('Pesan Online Tanpa Ribet');
        $response->assertSee('Antar Tujuan / Simpan Aman');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function storage_security_page_loads_with_facility_details(): void
    {
        $response = $this->get('/storage-security');

        $response->assertStatus(200);
        $response->assertSee('Standar Keamanan Tingkat Tinggi');
        $response->assertSee('CCTV 24/7');
        $response->assertSee('Struktur Penempatan Bertingkat');
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function coverage_page_loads_with_malang_raya_zones(): void
    {
        $response = $this->get('/coverage');

        $response->assertStatus(200);
        $response->assertSee('Melayani Seluruh Wilayah Malang Raya');
        $response->assertSee('Kota Malang (Zona Utama)');
        $response->assertSee('Kota Batu');
        $response->assertSee('Kabupaten Malang');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function faq_page_loads_with_common_questions(): void
    {
        $response = $this->get('/faq');

        $response->assertStatus(200);
        $response->assertSee('Pertanyaan yang Sering Diajukan');
        $response->assertSee('Apakah saya wajib membuat akun untuk memesan?');
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function about_page_loads_with_vision_and_mission(): void
    {
        $response = $this->get('/about');

        $response->assertStatus(200);
        $response->assertSee('Mitra Terpercaya Logistik');
        $response->assertSee('Visi Kami');
        $response->assertSee('Misi Kami');
    }


    #[\PHPUnit\Framework\Attributes\Test]
    public function contact_page_loads_with_hub_information(): void
    {
        $response = $this->get('/contact');

        $response->assertStatus(200);
        $response->assertSee('Siap Membantu Kebutuhan Logistik');
        $response->assertSee('WhatsApp Resmi');
        $response->assertSee('Hub Storage Malang');
    }
}
