<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel URL Prefix (Cloaking & Security)
    |--------------------------------------------------------------------------
    |
    | Define the secret URL path prefix for accessing the internal admin panel.
    | Changing this value conceals the admin dashboard from automated bots,
    | scanners, and unwanted public discovery.
    |
    | Default: 'kantor' (e.g. /kantor/login)
    |
    */
    'prefix' => env('ADMIN_PREFIX', 'kantor'),
];
