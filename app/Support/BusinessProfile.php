<?php

namespace App\Support;

class BusinessProfile
{
    /**
     * Get official WhatsApp URL with optional prefilled message
     */
    public static function whatsappUrl(?string $message = null): string
    {
        $number = config('business.whatsapp') ?: config('business.phone') ?: '6281234567890';
        $cleanNumber = preg_replace('/[^0-9]/', '', (string) $number);

        $url = "https://wa.me/{$cleanNumber}";
        if ($message) {
            $url .= '?text='.rawurlencode($message);
        }

        return $url;
    }

    /**
     * Get display telephone number
     */
    public static function displayPhone(): string
    {
        $phone = config('business.phone') ?: config('business.whatsapp');
        if (! $phone) {
            return 'WhatsApp Only';
        }

        return (string) $phone;
    }

    /**
     * Get display address text
     */
    public static function displayAddress(): string
    {
        $street = config('business.address.street');
        $district = config('business.address.district');
        $city = config('business.address.city', 'Kota Malang');

        if ($street) {
            return "{$street}, {$district}, {$city}";
        }

        return "Hub Operasional {$city}";
    }

    /**
     * Generate Schema.org LocalBusiness / MovingCompany JSON-LD
     */
    public static function localBusinessSchema(?string $description = null): array
    {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'MovingCompany',
            'name' => config('business.name', 'Bawa Beres'),
            'url' => url('/'),
            'description' => $description ?: 'Jasa pindahan kost & rumah terpercaya, penitipan barang aman ber-QR Code di Kota Malang.',
            'areaServed' => config('business.area_served', ['Kota Malang', 'Kota Batu', 'Kabupaten Malang']),
            'priceRange' => 'Rp',
        ];

        if ($phone = config('business.phone') ?: config('business.whatsapp')) {
            $clean = preg_replace('/[^0-9]/', '', (string) $phone);
            $schema['telephone'] = str_starts_with($phone, '+') ? $phone : "+{$clean}";
        }

        if ($email = config('business.email')) {
            $schema['email'] = $email;
        }

        $street = config('business.address.street');
        $city = config('business.address.city');
        if ($street || $city) {
            $address = [
                '@type' => 'PostalAddress',
                'addressLocality' => $city ?: 'Kota Malang',
                'addressRegion' => config('business.address.province', 'Jawa Timur'),
                'addressCountry' => config('business.address.country', 'ID'),
            ];
            if ($street) {
                $address['streetAddress'] = $street;
            }
            if ($district = config('business.address.district')) {
                $address['addressLocality'] = "{$district}, ".($city ?: 'Kota Malang');
            }
            if ($postal = config('business.address.postal_code')) {
                $address['postalCode'] = (string) $postal;
            }
            $schema['address'] = $address;
        }

        $lat = config('business.geo.latitude');
        $lng = config('business.geo.longitude');
        if ($lat !== null && $lng !== null && $lat !== '' && $lng !== '') {
            $schema['geo'] = [
                '@type' => 'GeoCoordinates',
                'latitude' => (float) $lat,
                'longitude' => (float) $lng,
            ];
        }

        if ($opens = config('business.operating_hours.opens')) {
            $schema['openingHoursSpecification'] = [
                '@type' => 'OpeningHoursSpecification',
                'dayOfWeek' => config('business.operating_hours.days'),
                'opens' => $opens,
                'closes' => config('business.operating_hours.closes', '21:00'),
            ];
        }

        return $schema;
    }
}
