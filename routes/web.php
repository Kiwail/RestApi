<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TenantAdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HomeController;

Route::get('/', function () {
    return view('index');
});

Route::get('index', function () {
    return view('index');
});

// Administratora maršruti
// Administratora maršruti (pilnībā aizsargāta grupa)
Route::middleware(['session.auth', 'session.admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/', [TenantAdminController::class, 'index'])
            ->name('index');

        Route::post('/tenants', [TenantAdminController::class, 'store'])
            ->name('store');

        Route::get('/tenants/{slug}', [TenantAdminController::class, 'show'])
            ->name('show');

    });

Route::get('register', function () {
    return view('register');
})->name('register');

// Reģistrācijas formas apstrāde (POST)
Route::post('register', [AuthController::class, 'register'])
    ->name('register.post');

// Pieteikšanās forma
Route::get('login', function () {
    return view('login');
})->name('login');

// Pieteikšanās apstrāde (POST)
Route::post('login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/apply', [ApplicationController::class, 'showForm'])
    ->name('apply.form');

Route::post('/apply', [ApplicationController::class, 'submit'])
    ->name('apply.submit');

Route::get('/home', [HomeController::class, 'index'])->name('home');

use App\Http\Controllers\CompanyPageController;

Route::middleware(['session.auth'])
    ->prefix('company')
    ->group(function () {
        Route::get('/{slug}', [CompanyPageController::class, 'show'])
            ->name('company.show');
    });

use App\Http\Controllers\ProfileController;

Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
Route::post('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');

Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
