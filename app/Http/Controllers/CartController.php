<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItemOption;
use App\Models\Menu;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function store()
    {
        $cart = Auth::user()->cart()->create();

        $cartItem = $cart->items()->create(['menu_id' => request('menu_id'), 'quantity' => request('quantity')]);

        foreach (request('option_ids') as $option_id) {
            $cartItem->options()->create(['option_id' => $option_id]);
        }
        // dd($cartItem->options()->find(2)->option->toArray());
        // dd($cart->items()->first()->menu()->first());

        return response()->json([], 200);
    }
}
