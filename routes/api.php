<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' =>['auth:sanctum']], function(){
    route::get('products', \App\Http\Controllers\Api\Product\IndexController::class)->name('products.index');
    route::post('products', \App\Http\Controllers\Api\Product\StoreController::class)->name('products.store');
    route::put('products/{product}', \App\Http\Controllers\Api\Product\UpdateController::class)->name('products.update');

    route::get('clients', \App\Http\Controllers\Api\Client\IndexController::class)->name('clients.index');
    route::post('clients', \App\Http\Controllers\Api\Client\StoreController::class)->name('clients.store');

    route::post('sales', \App\Http\Controllers\Api\Sale\SaleController::class)->name('sales.store');
    route::get('daily-stats', \App\Http\Controllers\Api\Compta\DailyStatsController::class)->name('compta.daily');
    route::get('stats', \App\Http\Controllers\Api\Compta\StatsController::class)->name('compta.stats');

    route::post('pdf', \App\Http\Controllers\Api\Pdf\TicketController::class)->name('ticket.create');
});

route::post('register', \App\Http\Controllers\Api\Auth\RegisterController::class)->name('users.store');