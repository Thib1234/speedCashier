<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Acompte\AcompteController;

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
    route::get('productsShow', \App\Http\Controllers\Api\Product\ShowController::class)->name('products.show');
    route::post('products', \App\Http\Controllers\Api\Product\StoreController::class)->name('products.store');
    route::put('products/{product}', \App\Http\Controllers\Api\Product\UpdateController::class)->name('products.update');
    route::put('activeProduct/{product}', \App\Http\Controllers\Api\Product\ActiveController::class)->name('products.active');

    route::get('categorie', \App\Http\Controllers\Api\Categorie\IndexController::class)->name('categories.index');

    Route::get('/acomptes', [AcompteController::class, 'index']);
    Route::post('/acomptes/store', [AcompteController::class, 'store']);
    Route::post('/sales/{sale}/apply-acompte', [AcompteController::class, 'apply']);
    Route::post('/acomptes/{acompte}/refund', [AcompteController::class, 'refund']);


    route::get('clients', \App\Http\Controllers\Api\Client\IndexController::class)->name('clients.index');
    route::post('clients', \App\Http\Controllers\Api\Client\StoreController::class)->name('clients.store');

    route::post('sales', \App\Http\Controllers\Api\Sale\SaleController::class)->name('sales.store');
    route::get('daily-stats', \App\Http\Controllers\Api\Compta\DailyStatsController::class)->name('compta.daily');
    route::get('daily-stats-toilettage', \App\Http\Controllers\Api\StatToilettage\DailyStatController::class)->name('compta.daily_toilettage');
    route::delete('sales/{saleId}', \App\Http\Controllers\Api\Sale\DeleteSaleController::class)->name('sales.delete');
    route::get('stats', \App\Http\Controllers\Api\Compta\StatsController::class)->name('compta.stats');
    route::get('stats-toilettage', \App\Http\Controllers\Api\StatToilettage\StatsController::class)->name('compta.stats-toilettage');

    route::post('pdf', \App\Http\Controllers\Api\Pdf\TicketController::class)->name('ticket.create');
});

route::post('register', \App\Http\Controllers\Api\Auth\RegisterController::class)->name('users.store');