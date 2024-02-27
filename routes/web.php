<?php
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
});

Route::get('/import', [PrintifyController::class, 'importProducts']);
Route::get('/test', [WooCommerceController::class, 'testProduct']);
Route::get('/dbimport', [ProductsController::class, 'createTestProduct']);
