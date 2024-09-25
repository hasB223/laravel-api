<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SemakNoInsolvensiController;

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
    return view('frontpage');
});

Route::get('/php-session', [AuthController::class, 'showSession']);

Route::middleware('generate.tokens')->group(function () {
    Route::get('/semak-insolvensi', [SemakNoInsolvensiController::class, 'showSemakInsolvensi']);
    Route::post('/semak-insolvensi', [SemakNoInsolvensiController::class, 'semakInsolvensi']);
});
