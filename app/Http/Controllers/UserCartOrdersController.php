<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Billing\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\CartItemResource;
use App\Http\Resources\OrderResource;

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

        // $disposable_spoon = request('to_shopkeeper')['disposable_spoon'] ? '' : '(수저포크X)';

        $order = $user->orders()->create([
            'eatery_title' => $cart->eatery->title,
            'delivery_address' => $user->address,
            'comment_to_shopkeeper' => '리뷰할게요.',
            'comment_to_delivery_man' => '안전하게 와주세요.',
            'order_amount' => $cart->getItemsPrice()->sum(),
            'delivery_charge' => $cart->eatery->delivery_charge,
            'menus' => json_encode(CartItemResource::collection($cart->items)),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => '주문 성공했어요.',
            'data' => new OrderResource($order),
        ], 201);
    }
}
