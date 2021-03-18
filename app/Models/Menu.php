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

    public function hasCategoryFor($menuName)
    {
        return $this->categories()->where('name', $menuName)->count() > 0;
    }

    public function getImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->image_path);
    }

    public function getPriceInWonsAttribute()
    {
        return number_format($this->price);
    }

    public function syncCategories($menusName, $eateryId)
    {
        $this->categories()->sync(Category::where('eatery_id', $eateryId)
            ->whereIn('name', $menusName)->pluck('id'));

        return $this;
    }
}
