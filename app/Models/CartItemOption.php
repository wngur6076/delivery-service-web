<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItemOption extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function option()
    {
        return $this->hasOne(Option::class, 'id', 'option_id');
    }
}
