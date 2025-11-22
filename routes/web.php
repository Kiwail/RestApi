<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TenantAdminController;
use App\Http\Controllers\AuthController;
    use App\Http\Controllers\ApplicationController;
use App\Http\Controllers\HomeController;

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

Route::get('/', function () {
    return view('index');
});
Route::get('index', function () {
    return view('index');
});
// admin routes
Route::get('/admin', [TenantAdminController::class, 'index'])               ->name('admin.index');
Route::post('/admin/tenants', [TenantAdminController::class, 'store'])      ->name('admin.store');
Route::get('/admin/tenants/{slug}', [TenantAdminController::class, 'show']) ->name('admin.show');

Route::get('register', function () {
    return view('register');
})->name('register');

// Обработка формы регистрации (POST)
Route::post('register', [AuthController::class, 'register'])
    ->name('register.post');

// Форма логина
Route::get('login', function () {
    return view('login');
})->name('login');

// Обработка логина (POST)
Route::post('login', [AuthController::class, 'login'])
    ->name('login.post');

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout');

Route::get('/apply', [ApplicationController::class, 'showForm'])
    ->name('apply.form');

Route::post('/apply', [ApplicationController::class, 'submit'])
    ->name('apply.submit');
    
Route::get('/', [HomeController::class, 'index'])->name('home');


