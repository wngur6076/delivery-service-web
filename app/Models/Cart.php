<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(CartItem::class);
    }

    public function addItem($menu_id, $quantity, $option_ids = [])
    {
        $cartItems = CartItem::where('menu_id', $menu_id)->get();
        $cartItem = $cartItems->first(function ($cartItem) use ($option_ids) {
            return array_equal($cartItem->options->pluck('option_id')->toArray(), $option_ids);
        });

        if (isset($cartItem)) {
            $cartItem->update(['quantity' => $cartItem->quantity + $quantity]);
        } else {
            $cartItem = $this->items()->create(['menu_id' => $menu_id, 'quantity' => $quantity]);

            foreach ($option_ids as $option_id) {
                $cartItem->options()->create(['option_id' => $option_id]);
            }
        }
    }

    public function getItemsPrice()
    {
        return $this->items->map(function ($cartItem) {
            $optionsPrice = $cartItem->options->map(function ($cartOption) {
                return $cartOption->option->price;
            });
            return $cartItem->menu->price * $cartItem->quantity + $optionsPrice->sum();
        });
    }
}
