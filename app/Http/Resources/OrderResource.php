<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'eatery_title' => $this->eatery_title,
            'delivery_address' => $this->delivery_address,
            'comment_to_shopkeeper' => $this->comment_to_shopkeeper,
            'comment_to_delivery_man' => $this->comment_to_delivery_man,
            'order_amount' => number_format($this->order_amount),
            'delivery_charge' => number_format($this->delivery_charge),
            'menus' => json_decode($this->menus),
        ];
    }
}
