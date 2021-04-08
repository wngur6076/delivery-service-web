<?php

namespace App\Http\Resources;

use App\Http\Resources\CartItemResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'eatery_id' => $this->eatery->id,
            'eatery_title' => $this->eatery->title,
            'menus' => CartItemResource::collection($this->items),
        ];
    }
}
