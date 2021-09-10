<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
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
Route::group(['as' => 'api.', 'middleware' => 'auth:api'], function () {
    Route::get('/month-quantity-stat', [
        App\Http\Controllers\API\APIController::class,
        'bike_quantity_month',
    ])->name('report.month-quantity-stat');

    Route::get('/month-revenue-stat', [
        App\Http\Controllers\API\APIController::class,
        'order_revenue_month',
    ])->name('report.month-revenue-stat');

    Route::get('/month-orders', [
        App\Http\Controllers\API\APIController::class,
        'order_month',
    ])->name('orders.month');
});
