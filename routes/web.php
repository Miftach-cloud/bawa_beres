<?php

use App\Http\Controllers\Admin\AuthController;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Customers\Index as CustomerIndex;
use App\Livewire\Admin\Customers\Show as CustomerShow;
use App\Livewire\Admin\Dashboard;
use App\Livewire\Admin\Services\Index as ServiceIndex;
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

    // Phase 4: Service Management
    Route::get('/services', ServiceIndex::class)->name('admin.services');

    // Phase 4: Customer Management
    Route::get('/customers', CustomerIndex::class)->name('admin.customers');
    Route::get('/customers/{customer}', CustomerShow::class)->name('admin.customers.show');

    // Phase 5+: Other Modules
    Route::get('/orders', function () {
        abort_if(Gate::denies('manage-orders'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Manajemen Order']);
    })->name('admin.orders');

    Route::get('/schedule', function () {
        abort_if(Gate::denies('manage-schedule'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Jadwal & Armada']);
    })->name('admin.schedule');

    Route::get('/inventory', function () {
        abort_if(Gate::denies('manage-inventory'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Item & QR Label']);
    })->name('admin.inventory');

    Route::get('/storage', function () {
        abort_if(Gate::denies('manage-storage'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Gudang Storage']);
    })->name('admin.storage');

    Route::get('/payments', function () {
        abort_if(Gate::denies('manage-payments'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Pembayaran']);
    })->name('admin.payments');

    Route::get('/settings', function () {
        abort_if(Gate::denies('manage-settings'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Pengaturan Sistem']);
    })->name('admin.settings');
});

// Generic login fallback redirecting to admin login
Route::get('/login', function () {
    return redirect()->route('admin.login');
})->name('login');
