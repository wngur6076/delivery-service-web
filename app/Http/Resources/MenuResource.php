<?php

namespace App\Http\Resources;

use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'price' => number_format($this->price),
            'description' => $this->description,
            'image' => Storage::disk('public')->url($this->image_path),
        ];
    }
}
