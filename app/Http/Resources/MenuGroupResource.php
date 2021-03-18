<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class MenuGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'menus' => MenuResource::collection($this->menus),
        ];
    }
}
