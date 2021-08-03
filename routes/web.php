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

Route::middleware(['auth'])->group(function () {
    Route::get('/changepassword', [
        App\Http\Controllers\Auth\ChangePasswordController::class,
        'index'
    ])->name('auth.changepassword.index');

    Route::post('/changepassword', [
        App\Http\Controllers\Auth\ChangePasswordController::class,
        'handle'
    ])->name('auth.changepassword.handle');
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

Route::prefix('/report')->middleware('auth')->group(function () {
    Route::get('/out-of-stock', [
        \App\Http\Controllers\ReportController::class,
        'out_of_stock'
    ])->name('report.out_of_stock');

    Route::get('/month-quantity-stat', [
        \App\Http\Controllers\ReportController::class,
        'month_quantity_stat_index'
    ])->name('report.month_quantity_stat.index');
});

/*
|--------------------------------------------------------------------------
| APIs.
| 
| These APIs don't have to get through the auth:api middleware.
|
| Supported search and get_all.
|--------------------------------------------------------------------------
*/
Route::prefix('api')->group(function () {
    Route::get('/brands', [
        App\Http\Controllers\API\BrandAPIController::class,
        'all'
    ]);
    Route::get('/brands/search/{keyword}', [
        App\Http\Controllers\API\BrandAPIController::class, 
        'search'
    ]);
    Route::get('/brands/{brand}/bikes', [
        App\Http\Controllers\API\BikeAPIController::class,
        'brand_bikes'
    ]);
    Route::get('/brands/{brand}/bikes/search/{keyword}', [
        App\Http\Controllers\API\BikeAPIController::class,
        'brand_search'
    ]);
    Route::get('/bikes', [
        App\Http\Controllers\API\BikeAPIController::class,
        'all'
    ]);
    Route::get('/bikes/search/{keyword}', [
        App\Http\Controllers\API\BikeAPIController::class,
        'search'
    ]);
    Route::get('/month-quantity-stat/month-detail', [
        App\Http\Controllers\API\ReportAPIController::class,
        'bike_quantity_month'
    ])->name('report.month-quantity-stat.detail');
});