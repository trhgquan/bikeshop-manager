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
    return redirect()->route('dashboard');
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

Route::middleware(['guest'])->group(function () {   
    Route::get('/login', [
        App\Http\Controllers\Auth\LoginController::class,
        'index'
    ])->name('auth.login.index');

    Route::post('/login', [
        App\Http\Controllers\Auth\LoginController::class,
        'handle'
    ])->name('auth.login.handle'); 
});

/**
 * Log user out of the system
 * 
 * User should logged in before access this route.
 */

Route::post('/logout', [
    App\Http\Controllers\Auth\LogoutController::class,
    'handle'
])->middleware('auth')->name('auth.logout');

Route::get('/logout', [
    App\Http\Controllers\Auth\LogoutController::class,
    'index'
]);

/**
 * Change user's password.
 * 
 * User should logged in before access this route.
 */

Route::group([
  'as' => 'auth.changepassword.',
  'middleware' => 'auth'
], function() {
    Route::get('/changepassword', [
        App\Http\Controllers\Auth\ChangePasswordController::class,
        'index'
    ])->name('index');

    Route::post('/changepassword', [
        App\Http\Controllers\Auth\ChangePasswordController::class,
        'handle'
    ])->name('handle');
  
});

/*
|--------------------------------------------------------------------------
| Bike Brand route.
| 
| These routes do Bike Brand actions.
|--------------------------------------------------------------------------
*/

Route::resource(
    'brands', 
    App\Http\Controllers\Bike\BrandController::class
)->middleware('auth');

/*
|--------------------------------------------------------------------------
| Bikes route.
| 
| These routes do Bikes actions.
|--------------------------------------------------------------------------
*/

Route::resource(
    'bikes',
    App\Http\Controllers\Bike\BikeController::class
)->middleware('auth');

/*
|--------------------------------------------------------------------------
| Order route.
| 
| These routes do Orders actions.
|--------------------------------------------------------------------------
*/

Route::resource(
    'orders',
    App\Http\Controllers\OrderController::class
)->middleware('auth');

/*
|--------------------------------------------------------------------------
| Report route.
| 
| These routes do Reports actions.
|--------------------------------------------------------------------------
*/

Route::group([
    'as' => 'report.',
    'prefix' => 'report',
    'middleware' => 'auth'
], function () {
    Route::get('/out-of-stock', [
        \App\Http\Controllers\ReportController::class,
        'out_of_stock_index'
    ])->name('out_of_stock');

    Route::get('/month-quantity-stat', [
        \App\Http\Controllers\ReportController::class,
        'month_quantity_stat_index'
    ])->name('month_quantity_stat.index');

    Route::get('/month-revenue-stat', [
        \App\Http\Controllers\ReportController::class,
        'month_revenue_stat_index'
    ])->name('month_revenue_stat.index');
});

/*
|--------------------------------------------------------------------------
| User Management route.
| 
| These routes do User Management actions.
|--------------------------------------------------------------------------
*/

Route::resource(
    'users',
    \App\Http\Controllers\UserManagementController::class
)->except(['show', 'update'])->middleware('auth');

Route::group([
  'as' => 'users.update.',
  'prefix' => 'users/{user}/update',
  'middleware' => 'auth'
], function() {
  Route::put('/password', [
    \App\Http\Controllers\UserManagementController::class,
    'update_password'
  ])->name('password');

  Route::put('/role', [
    \App\Http\Controllers\UserManagementController::class,
    'update_role'
  ])->name('role');
});
