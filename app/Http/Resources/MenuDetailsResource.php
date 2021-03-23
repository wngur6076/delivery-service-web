<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'price' => $this->price_in_wons,
            'description' => $this->description,
            'image' => $this->image_url,
            'option_groups' => OptionGroupResource::collection($this->optionGroups),
        ];
    }
}
