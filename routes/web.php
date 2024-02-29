<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PrintifyController;
use App\Http\Controllers\WooCommerceController;
use App\Http\Controllers\ProductsController;

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
})->name('/');

Route::get('/login', [AuthController::class, 'index'])->middleware('guest')->name('login');
Route::post('/login-attempt', [AuthController::class, 'login'])->middleware('guest')->name('login.attempt');

Route::get('/logout', [AuthController::class, 'Logout'])->middleware('auth')->name('logout');

Route::get('/import', [PrintifyController::class, 'importProducts']);
Route::get('/test', [WooCommerceController::class, 'testProduct']);
Route::get('/dbimport', [ProductsController::class, 'createTestProduct']);

Route::get('/newimport', [PrintifyController::class, 'newImportProducts']);