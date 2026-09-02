<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\SecureFileController;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Customers\Index as CustomerIndex;
use App\Livewire\Admin\Customers\Show as CustomerShow;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Inventory\Index as InventoryIndex;
use App\Livewire\Admin\Orders\Index as OrderIndex;
use App\Livewire\Admin\Orders\Show as OrderShow;
use App\Livewire\Admin\Payments\Index as PaymentIndex;
use App\Livewire\Admin\Schedules\Index as ScheduleIndex;
use App\Livewire\Admin\Services\Index as ServiceIndex;
use App\Livewire\Admin\Storage\Index as StorageIndex;
use App\Livewire\Public\InventoryScan;
use App\Livewire\Public\OrderTracking;
use App\Models\InventoryItem;
use App\Models\Service;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Public Website Marketing Pages & Booking
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/services', function () {
    $services = Service::where('is_active', true)->get();

    return view('public.services.index', ['services' => $services]);
})->name('public.services');

Route::get('/services/{service}', function (Service $service) {
    return view('public.services.show', ['service' => $service]);
})->name('public.services.show');

Route::get('/how-it-works', function () {
    return view('public.how-it-works');
})->name('public.how-it-works');

Route::get('/storage-security', function () {
    return view('public.storage-security');
})->name('public.storage-security');

Route::get('/coverage', function () {
    return view('public.coverage');
})->name('public.coverage');

Route::get('/faq', function () {
    return view('public.faq');
})->name('public.faq');

Route::get('/about', function () {
    return view('public.about');
})->name('public.about');

Route::get('/contact', function () {
    return view('public.contact');
})->name('public.contact');

Route::get('/booking', function () {
    return redirect('/#booking');
})->name('public.booking');

// Phase 17: SEO Sitemap XML
Route::get('/sitemap.xml', function () {
    $services = Service::where('is_active', true)->get();
    $baseUrl = config('app.url', url('/'));

    $xml = '<?xml version="1.0" encoding="UTF-8"?>';
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

    $staticRoutes = [
        ['loc' => url('/'), 'priority' => '1.0', 'changefreq' => 'weekly'],
        ['loc' => route('public.services'), 'priority' => '0.9', 'changefreq' => 'weekly'],
        ['loc' => route('public.how-it-works'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => route('public.storage-security'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => route('public.coverage'), 'priority' => '0.8', 'changefreq' => 'monthly'],
        ['loc' => route('public.faq'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => route('public.about'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => route('public.contact'), 'priority' => '0.7', 'changefreq' => 'monthly'],
        ['loc' => route('public.track'), 'priority' => '0.6', 'changefreq' => 'daily'],
    ];

    foreach ($staticRoutes as $route) {
        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars($route['loc']).'</loc>';
        $xml .= '<lastmod>'.date('Y-m-d').'</lastmod>';
        $xml .= '<changefreq>'.$route['changefreq'].'</changefreq>';
        $xml .= '<priority>'.$route['priority'].'</priority>';
        $xml .= '</url>';
    }

    foreach ($services as $service) {
        $xml .= '<url>';
        $xml .= '<loc>'.htmlspecialchars(route('public.services.show', $service)).'</loc>';
        $xml .= '<lastmod>'.($service->updated_at ? $service->updated_at->format('Y-m-d') : date('Y-m-d')).'</lastmod>';
        $xml .= '<changefreq>weekly</changefreq>';
        $xml .= '<priority>0.8</priority>';
        $xml .= '</url>';
    }

    $xml .= '</urlset>';

    return response($xml, 200)->header('Content-Type', 'text/xml');
})->name('sitemap.xml');

Route::get('/robots.txt', function () {
    $content = "User-agent: *\nAllow: /\nDisallow: /admin/\nDisallow: /admin/*\nDisallow: /livewire/\n\nSitemap: ".url('/sitemap.xml')."\n";

    return response($content, 200)->header('Content-Type', 'text/plain');
});

// Phase 15: Public Order Tracking (Rate Limited)
Route::middleware('throttle:tracking')->group(function () {
    Route::get('/track', OrderTracking::class)->name('public.track');
    Route::get('/track/{order_code}', OrderTracking::class)->name('public.track.order');
    Route::get('/i/{code}', InventoryScan::class)->name('inventory.scan');
    Route::get('/qr/{code}', InventoryScan::class)->name('inventory.qr');
});

// Admin Guest Routes (Rate Limited)
Route::middleware(['guest', 'throttle:login'])->prefix('admin')->group(function () {
    Route::get('/login', Login::class)->name('admin.login');
});

// Admin Protected Routes
Route::middleware(['auth', 'can:access-admin'])->prefix('admin')->group(function () {
    Route::get('/', Dashboard::class)->name('admin.dashboard');
    Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

    // Phase 5: Order Management
    Route::get('/orders', OrderIndex::class)->name('admin.orders');
    Route::get('/orders/{order}', OrderShow::class)->name('admin.orders.show');

    // Phase 7: Payment Management
    Route::get('/payments', PaymentIndex::class)->name('admin.payments');

    // Phase 8: Schedule Management
    Route::get('/schedule', ScheduleIndex::class)->name('admin.schedule');

    // Phase 9: Inventory Management
    Route::get('/inventory', InventoryIndex::class)->name('admin.inventory');
    Route::get('/inventory/{inventoryItem}/label', function (InventoryItem $inventoryItem) {
        Gate::authorize('manage-inventory');

        return view('admin.inventory.label', ['item' => $inventoryItem]);
    })->name('admin.inventory.label');

    // Phase 11: Storage Location Management
    Route::get('/storage', StorageIndex::class)->name('admin.storage');

    // Phase 4: Service Management
    Route::get('/services', ServiceIndex::class)->name('admin.services');

    // Phase 4: Customer Management
    Route::get('/customers', CustomerIndex::class)->name('admin.customers');
    Route::get('/customers/{customer}', CustomerShow::class)->name('admin.customers.show');

    // Phase 12+: Other Modules
    Route::get('/settings', function () {
        abort_if(Gate::denies('manage-settings'), 403, 'Akses ditolak.');

        return view('admin.placeholder', ['title' => 'Pengaturan Sistem']);
    })->name('admin.settings');

    // Secure Media Access for Operational Evidence
    Route::get('/media/payment-proof/{payment}', [SecureFileController::class, 'showPaymentProof'])->name('admin.media.payment-proof');
    Route::get('/media/inventory-photo/{inventoryPhoto}', [SecureFileController::class, 'showInventoryPhoto'])->name('admin.media.inventory-photo');
    Route::get('/media/order-attachment/{attachment}', [SecureFileController::class, 'showOrderAttachment'])->name('admin.media.order-attachment');
});

// Generic login fallback redirecting to admin login
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
