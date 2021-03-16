<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Eatery extends Model
{
    use HasFactory;

    public function menuCategories()
    {
        return $this->hasMany(Category::class);
    }
}
