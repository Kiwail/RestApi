<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TenantAdminController;

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
    return view('welcome');
});
// admin routes
Route::get('/admin', [TenantAdminController::class, 'index'])->name('admin.index');
Route::post('/admin/tenants', [TenantAdminController::class, 'store'])->name('admin.store');
Route::get('/admin/tenants/{slug}', [TenantAdminController::class, 'show'])->name('admin.show');
