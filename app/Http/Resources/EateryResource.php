<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EateryResource extends JsonResource
{
    public function toArray($request)
    {
        $menuGroups = MenuGroupResource::collection($this->menuGroups);

        return [
            'id' => $this->id,
            'title' => $this->title,
            'poster_image' => $this->poster_image_url,
            'grade' => $this->grade,
            'review_count' => $this->review_count,
            'delivery_time' => $this->delivery_time,
            'delivery_charge' => $this->delivery_charge_in_wons,
            'minimum_order_amount' => $this->minimum_order_amount_in_wons,
            'menu_groups' => $this->signatureMenus->isEmpty() ? $menuGroups
                : $menuGroups->prepend(['name' => '추천메뉴', 'menus' => MenuResource::collection($this->signatureMenus)]),
        ];
    }
}
