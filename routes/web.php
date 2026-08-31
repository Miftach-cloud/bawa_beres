<?php

use App\Http\Controllers\Admin\AuthController;
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
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

// Public Website
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Admin Guest Routes
Route::middleware('guest')->prefix('admin')->group(function () {
    Route::get('/login', Login::class)->name('admin.login');
});

// Admin Protected Routes
Route::middleware('auth')->prefix('admin')->group(function () {
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
});

// Generic login fallback redirecting to admin login
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
