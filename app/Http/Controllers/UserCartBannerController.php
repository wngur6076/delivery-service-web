<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserCartBannerController extends Controller
{
    public function show(User $user)
    {
        if (Auth::user()->id != $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '권환이 없습니다.',
                'error' => '401'
            ], 401);
        }

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
