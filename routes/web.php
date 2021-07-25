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
 * User should NOT be logged in before acccesing this route.
 */

use App\Http\Controllers\Auth\LoginController;

Route::middleware(['guest'])->group(function () {   
    Route::get('/login', [
        LoginController::class, 'index'
    ])->name('auth.login.index');

    Route::post('/login', [
        LoginController::class, 'handle'
    ])->name('auth.login.handle'); 
});

/**
 * Log user out of the system
 * 
 * User should logged in before access this route.
 */

use App\Http\Controllers\Auth\LogoutController;

Route::post('/logout', [
    LogoutController::class, 'handle'
])->middleware('auth')->name('auth.logout');

Route::get('/logout', [
    LogoutController::class, 'index'
]);

/**
 * Change user's password.
 * 
 * User should logged in before access this route.
 */

use App\Http\Controllers\Auth\ChangePasswordController;

Route::middleware(['auth'])->group(function () {
    Route::get('/changepassword', [
        ChangePasswordController::class, 'index'
    ])->name('auth.changepassword.index');

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

Route::resource('brands', BrandController::class)
    ->middleware('auth');