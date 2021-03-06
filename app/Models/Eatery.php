<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Eatery extends Model
{
    use HasFactory;

    protected $guarded = [];

    public static function findByTitle($title)
    {
        return self::where('title', $title)->firstOrFail();
    }

    public function menuGroups()
    {
        return $this->hasMany(MenuGroup::class);
    }

    public function signatureMenus()
    {
        return $this->belongsToMany(Menu::class, 'signature_menus')->withTimestamps();
    }

    public function getDeliveryChargeInWonsAttribute()
    {
        return number_format($this->delivery_charge);
    }

    public function getMinimumOrderAmountInWonsAttribute()
    {
        return number_format($this->minimum_order_amount);
    }

    public function getPosterImageUrlAttribute()
    {
        return Storage::disk('public')->url($this->poster_image_path);
    }

    public function gradeAverage()
    {
        return number_format(Review::where('eatery_title', $this->title)->pluck('grade')->average(), 1);
    }

}
