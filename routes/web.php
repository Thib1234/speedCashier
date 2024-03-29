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
Route::view('/compta/stats', 'compta.stats')->name('compta.stats');

Route::get('/pdf', [TicketController::class, 'viewTicket'])->name('pdf.view');
Route::get('/generate-pdf', [PDFController::class, 'generatePDF']);

Route::get('/register', [RegisterController::class, 'create']);

Route::post('/login', [LoginController::class, 'doLogin'])->name('auth.doLogin');
Route::delete('/logout', [LoginController::class, 'logout'])->name('auth.logout');

Route::view('/cashier', 'cashier')->name('cashier');