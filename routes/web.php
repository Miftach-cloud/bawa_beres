<?php

use App\Http\Controllers\Admin\AuthController;
use App\Livewire\Admin\Auth\Login;
use App\Livewire\Admin\Dashboard;
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

    // Modules with authorization gates (Phase 3.4)
    Route::get('/orders', function () {
        abort_if(Gate::denies('manage-orders'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Manajemen Order']);
    })->name('admin.orders');

    Route::get('/customers', function () {
        abort_if(Gate::denies('manage-customers'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Data Pelanggan']);
    })->name('admin.customers');

    Route::get('/services', function () {
        abort_if(Gate::denies('manage-services'), 403, 'Akses ditolak.');
        return view('admin.placeholder', ['title' => 'Katalog Layanan']);
    })->name('admin.services');

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
