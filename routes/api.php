<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenusController;
use App\Http\Controllers\EateriesController;
use App\Http\Controllers\CartItemsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CartBannerController;
use App\Http\Controllers\Auth\RefreshController;
use App\Http\Controllers\Auth\RegisterController;

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

Route::get('/eateries/{eatery}', [EateriesController::class, 'show']);

Route::get('/menugroups/{menugroupId}/menus/{menuId}', [MenusController::class, 'show']);

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('/cart-items', [CartItemsController::class, 'store']);
    Route::patch('/cart-items/{id}', [CartItemsController::class, 'update']);
    Route::delete('/cart-items/{id}', [CartItemsController::class, 'destroy']);
    Route::get('/cart-banner', [CartBannerController::class, 'show']);
});

Route::prefix('auth')->group(function () {
    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });
    Route::group(['middleware' => 'auth:api'], function () {
        Route::delete('/login', [LoginController::class, 'destroy'])->name('login.destroy');
        Route::get('/user', [LoginController::class, 'show'])->name('login.show');
        Route::get('/refresh', [RefreshController::class, 'store'])->name('refresh.store');
    });
});
