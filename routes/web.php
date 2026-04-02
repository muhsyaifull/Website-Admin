<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PackageController;
use App\Http\Controllers\TourController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\EducatorController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\CashierBookingController;
use App\Http\Controllers\BookingExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Login Routes
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('throttle:10,1');

// Protected Routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    Route::get('/search', [DashboardController::class, 'search'])->name('search')->middleware('throttle:20,1');

    // Cashier Routes 
    Route::middleware(['role:cashier'])->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [CashierBookingController::class, 'index'])->name('index');
        Route::get('/sessions', [CashierBookingController::class, 'index'])->name('sessions');

        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/create', [CashierBookingController::class, 'create'])->name('create');
            Route::post('/', [CashierBookingController::class, 'store'])->name('store');
            Route::get('/{booking}', [CashierBookingController::class, 'show'])->name('show');
            Route::get('/{booking}/reschedule', [CashierBookingController::class, 'rescheduleForm'])->name('reschedule');
            Route::post('/{booking}/reschedule', [CashierBookingController::class, 'rescheduleStore'])->name('reschedule.store');
        });

        Route::get('/api/sessions/{tourId}', [CashierBookingController::class, 'getSessionData'])->name('api.sessions')->middleware('throttle:30,1');
    });

    // Admin & Educator Routes 
    Route::middleware(['role:admin,educator'])->prefix('panel')->name('panel.')->group(function () {

        // Package management
        Route::prefix('packages')->name('packages.')->group(function () {
            Route::get('/', [PackageController::class, 'index'])->name('index');
            Route::get('/create', [PackageController::class, 'create'])->name('create');
            Route::post('/', [PackageController::class, 'store'])->name('store');
            Route::get('/{package}', [PackageController::class, 'show'])->name('show');
            Route::get('/{package}/edit', [PackageController::class, 'edit'])->name('edit');
            Route::put('/{package}', [PackageController::class, 'update'])->name('update');
            Route::delete('/{package}', [PackageController::class, 'destroy'])->name('destroy');
        });

        // Tour management
        Route::prefix('tours')->name('tours.')->group(function () {
            Route::get('/', [TourController::class, 'index'])->name('index');
            Route::get('/create', [TourController::class, 'create'])->name('create');
            Route::post('/', [TourController::class, 'store'])->name('store');
            Route::get('/{tour}/edit', [TourController::class, 'edit'])->name('edit');
            Route::put('/{tour}', [TourController::class, 'update'])->name('update');
            Route::patch('/{tour}/toggle', [TourController::class, 'toggle'])->name('toggle');
            Route::delete('/{tour}', [TourController::class, 'destroy'])->name('destroy');
        });

        // Session management
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [SessionController::class, 'index'])->name('index');
            Route::get('/create', [SessionController::class, 'create'])->name('create');
            Route::post('/', [SessionController::class, 'store'])->name('store');
            Route::get('/{session}/edit', [SessionController::class, 'edit'])->name('edit');
            Route::put('/{session}', [SessionController::class, 'update'])->name('update');
            Route::patch('/{session}/toggle', [SessionController::class, 'toggle'])->name('toggle');
            Route::delete('/{session}', [SessionController::class, 'destroy'])->name('destroy');
        });

        // Session Templates
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [SessionController::class, 'templates'])->name('index');
            Route::get('/create', [SessionController::class, 'createTemplate'])->name('create');
            Route::post('/', [SessionController::class, 'storeTemplate'])->name('store');
            Route::get('/{template}/edit', [SessionController::class, 'editTemplate'])->name('edit');
            Route::put('/{template}', [SessionController::class, 'updateTemplate'])->name('update');
            Route::patch('/{template}/toggle', [SessionController::class, 'toggleTemplate'])->name('toggle');
            Route::delete('/{template}', [SessionController::class, 'deleteTemplate'])->name('destroy');
        });

        // Educator management
        Route::prefix('educators')->name('educators.')->group(function () {
            Route::get('/', [EducatorController::class, 'index'])->name('index');
            Route::get('/create', [EducatorController::class, 'create'])->name('create');
            Route::post('/', [EducatorController::class, 'store'])->name('store');
            Route::get('/{educator}', [EducatorController::class, 'show'])->name('show');
            Route::get('/{educator}/edit', [EducatorController::class, 'edit'])->name('edit');
            Route::put('/{educator}', [EducatorController::class, 'update'])->name('update');
            Route::delete('/{educator}', [EducatorController::class, 'destroy'])->name('destroy');
        });

        // Booking overview
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/export', [BookingExportController::class, 'export'])->name('export');
            Route::get('/', [BookingController::class, 'index'])->name('index');
            Route::get('/{booking}', [BookingController::class, 'show'])->name('show');
        });
    });

    // Admin Only Routes 
    Route::middleware(['role:admin'])->prefix('panel')->name('panel.')->group(function () {

        // User management 
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [UserController::class, 'index'])->name('index');
            Route::get('/create', [UserController::class, 'create'])->name('create');
            Route::post('/', [UserController::class, 'store'])->name('store');
            Route::get('/{user}', [UserController::class, 'show'])->name('show');
            Route::get('/{user}/edit', [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}', [UserController::class, 'update'])->name('update');
            Route::delete('/{user}', [UserController::class, 'destroy'])->name('destroy');
        });
    });
});