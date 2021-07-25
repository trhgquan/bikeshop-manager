<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/**
 * Routing for Dashboard.
 * 
 * User should be logged in before accessing Dashboard.
 */
Route::get('/dashboard', function() {
    return view('home.dashboard');
})->middleware('auth')->name('dashboard');

/*
|--------------------------------------------------------------------------
| Authenticating route.
| 
| These routes do authentication actions.
|--------------------------------------------------------------------------
*/

/**
 * Log user in the system.
 * 
 * 
 */

use App\Http\Controllers\Auth\LoginController;

Route::middleware(['guest'])->group(function () {   
    Route::get('/login', [
        LoginController::class, 'view'
    ])->name('auth.login.view');

    Route::post('/login', [
        LoginController::class, 'handle'
    ])->name('auth.login.handle'); 
});

/**
 * Log user out of the system
 * 
 * 
 */

use App\Http\Controllers\Auth\LogoutController;

Route::post('/logout', LogoutController::class)
    ->middleware('auth')->name('auth.logout');

/**
 * Change user's password.
 * 
 * 
 */

use App\Http\Controllers\Auth\ChangePasswordController;

Route::middleware(['auth'])->group(function () {
    Route::get('/changepassword', [
        ChangePasswordController::class, 'view'
    ])->name('auth.changepassword.view');

    Route::post('/changepassword', [
        ChangePasswordController::class, 'handle'
    ])->name('auth.changepassword.handle');
});

/*
|--------------------------------------------------------------------------
| Bike Brand route.
| 
| These routes do Bike Brand actions.
|--------------------------------------------------------------------------
*/

use App\Http\Controllers\Bike\BrandController;

Route::middleware(['auth'])->group(function () {
    Route::get('/brands', [
        BrandController::class, 'view'
    ])->name('brand.view');

    Route::get('/brands/add', [
        BrandController::class, 'add'
    ])->name('brand.add.view');

    Route::get('/brands/{id}', [
        BrandController::class, 'viewId'
    ])->name('brand.view.id');

    Route::get('/brands/{id}/edit', [
        BrandController::class, 'edit'
    ])->name('brand.edit.view');

    Route::post('/brands/{id}/edit', [
        BrandController::class, 'update'
    ])->name('brand.edit');

    Route::post('/brands/{id}/delete', [
        BrandController::class, 'delete'
    ])->name('brand.delete');
});