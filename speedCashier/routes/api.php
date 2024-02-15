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

Route::get('users', \App\Http\Controllers\Api\User\IndexController::class)->name('index');
route::post('users', \App\Http\Controllers\Api\User\StoreController::class)->name('store');

route::get('products', \App\Http\Controllers\Api\Product\IndexController::class)->name('products.index');
