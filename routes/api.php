<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MenusController;
use App\Http\Controllers\EateriesController;
use App\Http\Controllers\CartItemsController;
use App\Http\Controllers\CartBannerController;

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
    Route::patch('/cart-items/{cartItem}', [CartItemsController::class, 'update']);
    Route::get('/cart-banner', [CartBannerController::class, 'show']);
});
