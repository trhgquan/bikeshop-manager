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

Route::get('/login', function() {
    return view('auth.login');
})->name('auth.login');

Route::post('/login', [LoginController::class, 'authenticate'])
       ->name('auth.login.authenticate');

/**
 * Log user out of the system
 * 
 * 
 */

use App\Http\Controllers\Auth\LogoutController;

Route::post('/logout', LogoutController::class)
       ->name('auth.logout');