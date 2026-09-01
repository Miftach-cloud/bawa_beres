<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Business Profile & Identity (Single Source of Truth)
    |--------------------------------------------------------------------------
    */
    'name' => env('BUSINESS_NAME', 'Bawa Beres'),
    'legal_name' => env('BUSINESS_LEGAL_NAME', 'Bawa Beres Indonesia'),
    'tagline' => env('BUSINESS_TAGLINE', 'Layanan Pindahan, Storage & Logistik Kota Malang'),
    'email' => env('BUSINESS_EMAIL', 'info@bawaberes.id'),
    'phone' => env('BUSINESS_PHONE', null),
    'whatsapp' => env('BUSINESS_WHATSAPP', null),

    /*
    |--------------------------------------------------------------------------
    | Physical Address & Operating Hub
    |--------------------------------------------------------------------------
    */
    'address' => [
        'street' => env('BUSINESS_STREET_ADDRESS', null),
        'district' => env('BUSINESS_DISTRICT', 'Lowokwaru'),
        'city' => env('BUSINESS_CITY', 'Kota Malang'),
        'province' => env('BUSINESS_PROVINCE', 'Jawa Timur'),
        'postal_code' => env('BUSINESS_POSTAL_CODE', null),
        'country' => 'ID',
    ],

    /*
    |--------------------------------------------------------------------------
    | Geolocation Coordinates (Nullable when not accurately verified)
    |--------------------------------------------------------------------------
    */
    'geo' => [
        'latitude' => env('BUSINESS_LATITUDE', null),
        'longitude' => env('BUSINESS_LONGITUDE', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Operating Hours
    |--------------------------------------------------------------------------
    */
    'operating_hours' => [
        'days' => [
            'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday',
        ],
        'opens' => env('BUSINESS_OPEN_TIME', '07:00'),
        'closes' => env('BUSINESS_CLOSE_TIME', '21:00'),
        'display' => env('BUSINESS_HOURS_DISPLAY', 'Senin – Minggu: 07.00 – 21.00 WIB'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Operational Service Coverage
    |--------------------------------------------------------------------------
    */
    'area_served' => [
        'Kota Malang',
        'Kota Batu',
        'Kabupaten Malang',
    ],
];
