<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EateryResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'poster_image' => $this->poster_image_url,
            'grade' => $this->grade,
            'review_count' => $this->review_count,
            'delivery_time' => $this->delivery_time,
            'delivery_charge' => $this->delivery_charge_in_wons,
            'minimum_order_amount' => $this->minimum_order_amount_in_wons,
            'menu_category' => MenuCategoryResource::collection($this->menuCategories)->prepend(['name' => '대표메뉴'])
        ];
    }
}
