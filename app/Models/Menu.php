<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Menu extends Model
{
    use HasFactory;

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'menu_category');
    }

    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image_path);
    }

    public function getPriceInWonsAttribute()
    {
        return number_format($this->price);
    }
}
