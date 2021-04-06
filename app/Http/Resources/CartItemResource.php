<?php

namespace App\Http\Resources;

use App\Http\Resources\CartItemOptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'quantity' => $this->quantity,
            'menu_name' => $this->menu->name,
            'menu_price' => $this->menu->price_in_wons,
            'options' => CartItemOptionResource::collection($this->options),
        ];
    }
}
