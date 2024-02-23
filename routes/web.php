<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

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


Route::view('/', 'app')->name('index');
Route::view('/clients/create', 'clients.create')->name('users.create');
Route::view('/clients/index', 'clients.index')->name('users.index');
Route::view('/products/index', 'products.index')->name('products.index');
Route::view('/products/create', 'products.create')->name('products.create');



Route::get('/register', [RegisterController::class, 'create']);

Route::get('/login', [LoginController::class, 'login'])->name('auth.login');
Route::post('/login', [LoginController::class, 'doLogin'])->name('auth.doLogin');
Route::delete('/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::view('/cashier', 'cashier')->name('cashier');