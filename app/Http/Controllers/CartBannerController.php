<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartBannerController extends Controller
{
    public function show()
    {
        $cart = Auth::user()->cart;

        return response()->json([
            'data' => [
                'cart_banner' => [
                    'cart_item_count' => $cart->items->count(),
                    'cart_item_total' => number_format($cart->getItemsPrice()->sum()),
                ]
            ]
        ], 200);
    }
}
