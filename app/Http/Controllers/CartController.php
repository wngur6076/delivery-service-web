<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function store()
    {
        $cart = Auth::user()->cart()->create();
        // dd($cart->user);

        return response()->json([], 200);
    }
}
