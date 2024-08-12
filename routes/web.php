<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Pdf\TicketController;
use App\Http\Controllers\PDFController;

Route::view('/', 'app')->name('index');

Route::view('/clients/create', 'clients.create')->name('users.create');
Route::view('/clients/index', 'clients.index')->name('users.index');

Route::view('/products/index', 'products.index')->name('products.index');
Route::view('/products/create', 'products.create')->name('products.create');

Route::view('/compta/daily', 'compta.daily')->name('compta.daily');
Route::view('/compta/daily-toilettage', 'compta.daily_toilettage')->name('compta.daily_toilettage');
Route::view('/compta/stats', 'compta.stats')->name('compta.stats');
Route::view('/compta/stats-toilettage', 'compta.stats_toilettage')->name('compta.stats_toilettage');
Route::view('/compta/stats-products-toilettage', 'compta.stats_products')->name('compta.stats_products');
Route::view('/compta/stats-nourriture', 'compta.stats_nourriture')->name('compta.stats_nourriture');
Route::view('/compta/stats-accessoires', 'compta.stats_accessoires')->name('compta.stats_accessoires');

Route::get('/pdf', [TicketController::class, 'viewTicket'])->name('pdf.view');
Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);

Route::view('/account', 'acompte.show')->name('acompte.show');
Route::view('/account/create', 'acompte.create')->name('acompte.create');

// Route::get('/register', [RegisterController::class, 'create']);

Route::post('/login', [LoginController::class, 'doLogin'])->name('auth.doLogin');
Route::delete('/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::view('/cashier', 'cashier')->name('cashier');
Route::view('/test', 'cashier_test')->name('test');

Route::get('/stats/print', [\App\Http\Controllers\Api\Compta\ShowStatsController::class, 'show'])->name('stats.show');
Route::get('/stats-toilettage/print', [\App\Http\Controllers\Api\StatToilettage\ShowStatsController::class, 'show'])->name('stats-toilettage.show');


Route::view('/factures/create', 'factures.create')->name('factures.create');
Route::view('/factures/list', 'factures.list')->name('factures.list');

Route::view('/stock', 'stock.index')->name('stock.index');
