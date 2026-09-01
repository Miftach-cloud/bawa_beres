<?php

namespace Database\Seeders;

use App\Enums\PricingType;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $services = [
            [
                'name' => 'Jasa Pindahan Kost & Rumah',
                'slug' => 'jasa-pindahan',
                'description' => 'Layanan pindahan lengkap dengan armada pick-up/truk, tenaga angkut profesional, dan proteksi barang untuk wilayah Malang Raya.',
                'pricing_type' => PricingType::QUOTATION,
                'base_price' => 150000,
                'is_active' => true,
            ],
            [
                'name' => 'Penitipan & Storage Barang',
                'slug' => 'penitipan-storage',
                'description' => 'Penitipan barang aman bulanan/semesteran dengan sistem pelabelan QR Code, foto verifikasi kondisi, dan alokasi rak gudang.',
                'pricing_type' => PricingType::PACKAGE,
                'base_price' => 50000,
                'is_active' => true,
            ],
            [
                'name' => 'Delivery & Kurir Instan',
                'slug' => 'delivery-logistik',
                'description' => 'Pengiriman barang point-to-point cepat di area Kota Malang dengan pelacakan status realtime.',
                'pricing_type' => PricingType::FIXED,
                'base_price' => 25000,
                'is_active' => true,
            ],
        ];

        foreach ($services as $serviceData) {
            Service::updateOrCreate(
                ['slug' => $serviceData['slug']],
                $serviceData
            );
        }
    }
}
