<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenusController;
use App\Http\Controllers\EateriesController;
use App\Http\Controllers\UserCartsController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RefreshController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\UserCartBannerController;
use App\Http\Controllers\UserCartCartItemsController;

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
    Route::delete('/user-carts/{user}', [UserCartsController::class, 'destroy']);

    Route::get('/user-cart/{user}/banner', [UserCartBannerController::class, 'show']);
    Route::post('/user-cart/{user}/cart-items', [UserCartCartItemsController::class, 'store']);
    Route::patch('/user-cart/{user}/cart-items/{id}', [UserCartCartItemsController::class, 'update']);
    Route::delete('/user-cart/{user}/cart-items/{id}', [UserCartCartItemsController::class, 'destroy']);
});

Route::prefix('auth')->group(function () {
    Route::group(['middleware' => 'guest:api'], function () {
        Route::post('/register', [RegisterController::class, 'store'])->name('register.store');
        Route::post('/login', [LoginController::class, 'store'])->name('login.store');
    });
    Route::group(['middleware' => 'auth:api'], function () {
        Route::delete('/logout', [LoginController::class, 'destroy'])->name('login.destroy');
        Route::get('/user', [LoginController::class, 'show'])->name('login.show');
        Route::post('/refresh', [RefreshController::class, 'store'])->name('refresh.store');
    });
});
