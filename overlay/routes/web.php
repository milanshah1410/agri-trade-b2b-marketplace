<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Support\HomePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => Inertia::render('Public/Home'))->name('home');

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:30,1');
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:10,1');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'destroy'])->name('logout');

    // Sends a signed-in user to the right place for their role (admin panel, customer, transporter, driver).
    Route::get('/portal', fn (Request $request) => Inertia::location(HomePath::for($request->user())))
        ->name('portal');
});

// Role-isolated portals. The Filament admin panel (/admin) is gated separately by User::canAccessPanel().
Route::middleware(['auth', 'active', 'role:customer'])->group(function () {
    Route::get('/dashboard', fn () => Inertia::render('Customer/Dashboard'))->name('customer.dashboard');
});

Route::middleware(['auth', 'active', 'role:transporter'])->prefix('transporter')->group(function () {
    Route::get('/', fn () => Inertia::render('Transporter/Dashboard'))->name('transporter.dashboard');
});

Route::middleware(['auth', 'active', 'role:driver'])->prefix('driver')->group(function () {
    Route::get('/', fn () => Inertia::render('Driver/Dashboard'))->name('driver.dashboard');
});
