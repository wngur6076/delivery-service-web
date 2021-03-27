<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exceptions\EaterySyncException;

class CartController extends Controller
{
    public function store($eateryId)
    {
        try {
            $cart = Auth::user()->getCart($eateryId);
            $cart->addItem(request('menu_id'), request('quantity'), request('option_ids'));
            return response()->json([], 200);
        } catch (EaterySyncException $e) {
            return response()->json([], 422);
        }
    }
}
