<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

use function PHPSTORM_META\map;

class OptionGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'name' => $this->name,
            'required' => $this->required,
            'min' => $this->min,
            'max' => $this->max,
            'option_count' => $this->options()->count(),
            'options' => OptionResource::collection($this->options),
        ];
    }
}
