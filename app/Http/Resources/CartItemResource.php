<?php

namespace App\Http\Resources;

use App\Http\Resources\CartItemOptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'cart_item_id' => $this->id,
            'quantity' => $this->quantity,
            'name' => $this->menu->name,
            'price' => $this->menu->price_in_wons,
            'options' => CartItemOptionResource::collection($this->options),
        ];
    }
}
