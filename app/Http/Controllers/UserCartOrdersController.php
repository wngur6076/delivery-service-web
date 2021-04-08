<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use App\Http\Resources\CartResource;
use Illuminate\Support\Facades\Auth;

class UserCartOrdersController extends Controller
{
    private $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function store(User $user)
    {
        if (Auth::user()->id != $user->id) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => '권환이 없습니다.',
                'error' => '401'
            ], 401);
        }

        $cart = $user->cart;
        $this->paymentGateway->charge($cart->getItemsPrice()->sum());

        $disposable_spoon = request('to_shopkeeper')['disposable_spoon'] ? '' : '(수저포크X)';

        return response()->json([
            'data' => [
                'delivery_address' => $user->address,
                'comment_to_shopkeeper' => request('to_shopkeeper')['comment'].$disposable_spoon,
                'comment_to_delivery_man' => request('to_delivery_man')['comment'],
                'cart' => new CartResource($cart),
                'payment_amount' => [
                    'order_amount' => number_format($cart->getItemsPrice()->sum()),
                    'delivery_charge' => $cart->eatery->delivery_charge_in_wons,
                ]
            ]
        ], 201);
    }
}
