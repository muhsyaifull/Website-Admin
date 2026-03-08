<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\EducatorController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Protected Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard - redirect based on role
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile - accessible by all authenticated users
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Search - accessible by all authenticated users
    Route::get('/search', [DashboardController::class, 'search'])->name('search');

    // Cashier Routes
    Route::middleware(['role:cashier'])->prefix('kasir')->name('kasir.')->group(function () {
        Route::get('/', [KasirController::class, 'index'])->name('index');
        Route::get('/sessions', [KasirController::class, 'index'])->name('sessions'); // Same as index

        // Booking routes
        Route::prefix('booking')->name('booking.')->group(function () {
            Route::get('/create', [KasirController::class, 'createBooking'])->name('create');
            Route::post('/store', [KasirController::class, 'storeBooking'])->name('store');
            Route::get('/{booking}', [KasirController::class, 'showBooking'])->name('show');
        });

        // Ajax routes
        Route::get('/api/sessions/{type}', [KasirController::class, 'getSessionData'])->name('api.sessions');
    });

    // Educator Routes
    Route::middleware(['role:educator'])->prefix('educator')->name('educator.')->group(function () {
        Route::get('/', [EducatorController::class, 'index'])->name('index');

        // Package management
        Route::prefix('packages')->name('packages.')->group(function () {
            Route::get('/', [EducatorController::class, 'packages'])->name('index');
            Route::get('/packages', [EducatorController::class, 'packages'])->name('packages'); // Alternative route
            Route::get('/create', [EducatorController::class, 'createPackage'])->name('create');
            Route::post('/store', [EducatorController::class, 'storePackage'])->name('store');
            Route::get('/{package}/edit', [EducatorController::class, 'editPackage'])->name('edit');
            Route::put('/{package}', [EducatorController::class, 'updatePackage'])->name('update');
            Route::delete('/{package}', [EducatorController::class, 'deletePackage'])->name('destroy');
        });

        // Session management
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [EducatorController::class, 'sessions'])->name('index');
            Route::get('/sessions', [EducatorController::class, 'sessions'])->name('sessions'); // Alternative route
            Route::get('/create', [EducatorController::class, 'createSession'])->name('create');
            Route::post('/store', [EducatorController::class, 'storeSession'])->name('store');
            Route::get('/{session}/edit', [EducatorController::class, 'editSession'])->name('edit');
            Route::put('/{session}', [EducatorController::class, 'updateSession'])->name('update');
            Route::patch('/{session}/toggle', [EducatorController::class, 'toggleSession'])->name('toggle');
            Route::delete('/{session}', [EducatorController::class, 'deleteSession'])->name('destroy');
        });

        // Educator management
        Route::prefix('educators')->name('educators.')->group(function () {
            Route::get('/', [EducatorController::class, 'educators'])->name('index');
            Route::get('/educators', [EducatorController::class, 'educators'])->name('educators'); // Alternative route
            Route::get('/create', [EducatorController::class, 'createEducator'])->name('create');
            Route::post('/store', [EducatorController::class, 'storeEducator'])->name('store');
            Route::get('/{educator}/edit', [EducatorController::class, 'editEducator'])->name('edit');
            Route::put('/{educator}', [EducatorController::class, 'updateEducator'])->name('update');
        });
    });

    // Admin Routes
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('index');

        // User management
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/', [AdminController::class, 'users'])->name('index');
            Route::get('/users', [AdminController::class, 'users'])->name('users'); // Alternative route
            Route::get('/create', [AdminController::class, 'createUser'])->name('create');
            Route::post('/store', [AdminController::class, 'storeUser'])->name('store');
            Route::get('/{user}', [AdminController::class, 'showUser'])->name('show');
            Route::get('/{user}/edit', [AdminController::class, 'editUser'])->name('edit');
            Route::put('/{user}', [AdminController::class, 'updateUser'])->name('update');
            Route::delete('/{user}', [AdminController::class, 'deleteUser'])->name('destroy');
        });

        // Package management (same as educator)
        Route::prefix('packages')->name('packages.')->group(function () {
            Route::get('/', [AdminController::class, 'packages'])->name('index');
            Route::get('/packages', [AdminController::class, 'packages'])->name('packages'); // Alternative route
            Route::get('/create', [AdminController::class, 'createPackage'])->name('create');
            Route::post('/store', [AdminController::class, 'storePackage'])->name('store');
            Route::get('/{package}/edit', [AdminController::class, 'editPackage'])->name('edit');
            Route::put('/{package}', [AdminController::class, 'updatePackage'])->name('update');
            Route::delete('/{package}', [AdminController::class, 'deletePackage'])->name('destroy');
            Route::get('/{package}', [AdminController::class, 'showPackage'])->name('show');
        });

        // Session management (auto-generated from templates)
        Route::prefix('sessions')->name('sessions.')->group(function () {
            Route::get('/', [AdminController::class, 'sessions'])->name('index');
            Route::get('/sessions', [AdminController::class, 'sessions'])->name('sessions');
            Route::get('/{session}/edit', [AdminController::class, 'editSession'])->name('edit');
            Route::put('/{session}', [AdminController::class, 'updateSession'])->name('update');
            Route::patch('/{session}/toggle', [AdminController::class, 'toggleSession'])->name('toggle');
            Route::delete('/{session}', [AdminController::class, 'deleteSession'])->name('destroy');
        });

        // Session Templates
        Route::prefix('templates')->name('templates.')->group(function () {
            Route::get('/', [AdminController::class, 'templates'])->name('index');
            Route::get('/create', [AdminController::class, 'createTemplate'])->name('create');
            Route::post('/store', [AdminController::class, 'storeTemplate'])->name('store');
            Route::get('/{template}/edit', [AdminController::class, 'editTemplate'])->name('edit');
            Route::put('/{template}', [AdminController::class, 'updateTemplate'])->name('update');
            Route::patch('/{template}/toggle', [AdminController::class, 'toggleTemplate'])->name('toggle');
            Route::delete('/{template}', [AdminController::class, 'deleteTemplate'])->name('destroy');
        });

        // Educator management (same as educator with delete)
        Route::prefix('educators')->name('educators.')->group(function () {
            Route::get('/', [AdminController::class, 'educators'])->name('index');
            Route::get('/educators', [AdminController::class, 'educators'])->name('educators'); // Alternative route
            Route::get('/create', [AdminController::class, 'createEducator'])->name('create');
            Route::post('/store', [AdminController::class, 'storeEducator'])->name('store');
            Route::get('/{educator}', [AdminController::class, 'showEducator'])->name('show');
            Route::get('/{educator}/edit', [AdminController::class, 'editEducator'])->name('edit');
            Route::put('/{educator}', [AdminController::class, 'updateEducator'])->name('update');
            Route::delete('/{educator}', [AdminController::class, 'deleteEducator'])->name('destroy');
        });

        // Booking overview
        Route::prefix('bookings')->name('bookings.')->group(function () {
            Route::get('/', [AdminController::class, 'bookings'])->name('index');
            Route::get('/bookings', [AdminController::class, 'bookings'])->name('bookings'); // Alternative route
            Route::get('/{booking}', [AdminController::class, 'showBooking'])->name('show');
        });
    });


});